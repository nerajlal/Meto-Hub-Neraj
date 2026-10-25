#!/usr/bin/env bash
# Run ONLY after explicit cutover approval. Changes routing, never containers/data.
set -Eeuo pipefail
[[ $EUID == 0 ]] || exit 1
BASE=/srv/apps/metohub-next
CANDIDATES=$(cd "$(dirname "$0")/routing" && pwd)
exec 9>"$BASE/domain-switch.lock"
flock -n 9 || exit 1
for domain in metohub.cloud glovstore.metora.in test-glovstore.metora.in test.metohub.cloud; do
    [[ -f /etc/letsencrypt/live/$domain/fullchain.pem ]] || { echo "TLS missing: $domain"; exit 1; }
done
for name in metohub-next-test glovstore-prod glovstore-test; do
    [[ ! -e /etc/nginx/sites-enabled/$name && ! -L /etc/nginx/sites-enabled/$name ]] || { echo "Site already enabled: $name"; exit 1; }
    if [[ -e /etc/nginx/sites-available/$name ]]; then
        cmp -s "$CANDIDATES/$name.conf" /etc/nginx/sites-available/$name || { echo "Different site config already exists: $name"; exit 1; }
    fi
done
for name in metohub-prod metohub-test; do
    [[ -L /etc/nginx/sites-enabled/$name && $(readlink -f /etc/nginx/sites-enabled/$name) == /etc/nginx/sites-available/$name ]] || { echo "Unexpected old routing link: $name"; exit 1; }
done
curl -fsS -H 'Host: metohub.cloud' -H 'X-Forwarded-Proto: https' http://127.0.0.1:8892/up >/dev/null
read -r -p 'Move metohub.cloud to new Laravel, and old apps to Glov domains? Type SWITCH: ' answer
[[ "$answer" == SWITCH ]] || exit 1
BACKUP=$BASE/routing-backup-$(date -u +%Y%m%dT%H%M%SZ)
mkdir -m 700 "$BACKUP"
for name in metohub-prod metohub-test; do
    cp -a /etc/nginx/sites-available/$name "$BACKUP/$name.conf"
    readlink /etc/nginx/sites-enabled/$name > "$BACKUP/$name.link"
done
recover() {
    result=$?
    trap - ERR
    for name in metohub-next-test glovstore-prod glovstore-test; do rm -f /etc/nginx/sites-enabled/$name; done
    for name in metohub-prod metohub-test; do ln -sfn "$(cat "$BACKUP/$name.link")" /etc/nginx/sites-enabled/$name; done
    nginx -t && systemctl reload nginx || true
    echo "Cutover failed; restored original routing links. Backup: $BACKUP"
    exit "$result"
}
trap recover ERR
for name in metohub-next-test glovstore-prod glovstore-test; do
    install -m 644 "$CANDIDATES/$name.conf" /etc/nginx/sites-available/$name
    ln -s /etc/nginx/sites-available/$name /etc/nginx/sites-enabled/$name
done
# Unlink routing only. Keep the original config files, containers and all data.
rm /etc/nginx/sites-enabled/metohub-prod /etc/nginx/sites-enabled/metohub-test
nginx -t
systemctl reload nginx
for domain in metohub.cloud glovstore.metora.in test-glovstore.metora.in; do
    endpoint=/
    [[ "$domain" != metohub.cloud ]] || endpoint=/up
    echo "Checking https://$domain$endpoint"
    curl -fsS --retry 5 --retry-all-errors --retry-delay 1 --max-time 30 --resolve "$domain:443:127.0.0.1" "https://$domain$endpoint" >/dev/null
done
trap - ERR
echo "Domain routes switched. Original config files and apps preserved; routing backup: $BACKUP"
