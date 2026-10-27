#!/usr/bin/env bash
set -Eeuo pipefail
umask 077
TOKEN=/root/.config/metohub/hostinger-token
[[ -s "$TOKEN" && $(stat -c '%u:%a' "$TOKEN") == 0:600 ]] || { echo 'Hostinger token must be a nonempty root-owned mode-600 file.'; exit 1; }
export HOSTINGER_Token
HOSTINGER_Token=$(cat "$TOKEN")
exec /opt/metohub-acme/source/acme.sh --cron --home /opt/metohub-acme/source --config-home /opt/metohub-acme/state --cert-home /opt/metohub-acme/state/certs --server letsencrypt
