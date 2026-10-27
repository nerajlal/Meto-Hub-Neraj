#!/usr/bin/env bash
# VPS root only. Does not enable wildcard routing or change existing certificates.
set -Eeuo pipefail
[[ $EUID == 0 ]] || exit 1
HERE=$(cd "$(dirname "$0")" && pwd)
TOKEN=/root/.config/metohub/hostinger-token
[[ -s "$TOKEN" && $(stat -c '%u:%a' "$TOKEN") == 0:600 ]] || { echo "Save the Hostinger token privately at $TOKEN (root:root, mode 600)."; exit 1; }
umask 077
install -d -m 700 /opt/metohub-acme /opt/metohub-acme/state /etc/nginx/metohub-wildcard
exec 9>/opt/metohub-acme/setup.lock
flock -n 9 || { echo 'Certificate setup is already running.'; exit 1; }
REVISION=3661fd86b6304115e42f43910e6dd452ab9866d6
if [[ ! -d /opt/metohub-acme/source ]]; then
    git clone --branch 3.1.4 --depth 1 https://github.com/acmesh-official/acme.sh.git /opt/metohub-acme/source
fi
[[ $(git -C /opt/metohub-acme/source rev-parse HEAD) == "$REVISION" && -z $(git -C /opt/metohub-acme/source status --porcelain) ]] || { echo 'Unexpected ACME source revision or local modifications.'; exit 1; }
export HOSTINGER_Token
HOSTINGER_Token=$(cat "$TOKEN")
ACME=(/opt/metohub-acme/source/acme.sh --home /opt/metohub-acme/source --config-home /opt/metohub-acme/state --cert-home /opt/metohub-acme/state/certs --server letsencrypt)
# DNS API uses temporary TXT challenge records; it does not change A/MX records.
result=0
"${ACME[@]}" --issue --dns dns_hostinger -d metohub.cloud -d '*.metohub.cloud' --keylength ec-256 || result=$?
# acme.sh returns 2 if an existing valid certificate is not due for renewal.
[[ "$result" == 0 || "$result" == 2 ]] || exit "$result"
"${ACME[@]}" --install-cert -d metohub.cloud --ecc --key-file /etc/nginx/metohub-wildcard/privkey.pem --fullchain-file /etc/nginx/metohub-wildcard/fullchain.pem --reloadcmd 'nginx -t && systemctl reload nginx'
openssl x509 -in /etc/nginx/metohub-wildcard/fullchain.pem -noout -checkhost 'wildcard-check.metohub.cloud'
install -m 700 "$HERE/renew.sh" /opt/metohub-acme/renew.sh
install -m 644 "$HERE/metohub-wildcard-renew.service" /etc/systemd/system/metohub-wildcard-renew.service
install -m 644 "$HERE/metohub-wildcard-renew.timer" /etc/systemd/system/metohub-wildcard-renew.timer
systemctl daemon-reload
systemctl enable --now metohub-wildcard-renew.timer
echo 'Wildcard certificate installed and renewal scheduled. Routing has not been enabled yet.'
