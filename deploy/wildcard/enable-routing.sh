#!/usr/bin/env bash
# Run after deploying the application changes and completing setup-certificate.sh.
set -Eeuo pipefail
[[ $EUID == 0 ]] || exit 1
HERE=$(cd "$(dirname "$0")" && pwd)
BASE=/srv/apps/metohub-next
SITE=/etc/nginx/sites-available/metohub-wildcard
LINK=/etc/nginx/sites-enabled/metohub-wildcard
[[ ! -e "$SITE" && ! -e "$LINK" && ! -L "$LINK" ]] || { echo 'Wildcard route already exists; inspect before updating.'; exit 1; }
install -d -m 700 /srv/apps/metohub-next-deploy
exec 9>/srv/apps/metohub-next-deploy/lock
flock -n 9 || { echo 'A deployment is running.'; exit 1; }
openssl x509 -in /etc/nginx/metohub-wildcard/fullchain.pem -noout -checkhost 'wildcard-check.metohub.cloud'
openssl x509 -in /etc/nginx/metohub-wildcard/fullchain.pem -noout -checkend 86400
cd "$BASE/runtime"
docker compose exec -T app php -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); if (!class_exists(App\Http\Middleware\ResolveStoreSubdomain::class) || config("session.domain") || app(App\Services\StoreDomains::class)->base() !== "metohub.cloud") {fwrite(STDERR,"Deploy subdomain support and use host-only cookies before enabling routing.\n");exit(1);}'
# Ensure the deployed middleware really rejects unknown hosts, not just that its class exists.
code=$(curl -sS -o /dev/null -w '%{http_code}' -H 'Host: wildcard-check-unassigned.metohub.cloud' http://127.0.0.1:8892/)
[[ "$code" == 404 ]] || { echo 'Unknown store host must return 404 before activation.'; exit 1; }
BACKUP=$BASE/wildcard-backup-$(date -u +%Y%m%dT%H%M%SZ)
install -d -m 700 "$BACKUP"
cp -p "$BASE/shared/app.env" "$BACKUP/app.env"
nginx -T > "$BACKUP/nginx.conf" 2>&1
chmod 600 "$BACKUP/"*
recover() {
    result=$?; trap - ERR
    rm -f "$LINK"
    cp -p "$BACKUP/app.env" "$BASE/shared/app.env"
    nginx -t && systemctl reload nginx || true
    docker compose up -d --no-deps --no-build --pull never --force-recreate app worker scheduler || true
    docker compose up -d --no-deps --no-build --pull never --force-recreate web || true
    echo "Activation failed; restored environment and removed wildcard route. Backup: $BACKUP"
    exit "$result"
}
trap recover ERR
install -m 644 "$HERE/nginx.conf" "$SITE"
ln -s "$SITE" "$LINK"
nginx -t
systemctl reload nginx
python3 - "$BASE/shared/app.env" <<'PY'
from pathlib import Path
import sys
p=Path(sys.argv[1]); lines=[l for l in p.read_text().splitlines() if not l.startswith('STOREFRONT_WILDCARD_READY=')]
p.write_text('\n'.join(lines+['STOREFRONT_WILDCARD_READY=true'])+'\n')
PY
docker compose up -d --no-deps --no-build --pull never --force-recreate app worker scheduler
docker compose up -d --no-deps --no-build --pull never --force-recreate --wait --wait-timeout 180 web
for domain in metohub.cloud test.metohub.cloud glovstore.metora.in test-glovstore.metora.in; do
    curl -fsS --retry 5 --retry-all-errors --retry-delay 1 --max-time 20 --resolve "$domain:443:127.0.0.1" "https://$domain/" >/dev/null
done
code=$(curl -sS --resolve wildcard-check-unassigned.metohub.cloud:443:127.0.0.1 -o /dev/null -w '%{http_code}' https://wildcard-check-unassigned.metohub.cloud/)
[[ "$code" == 404 ]]
trap - ERR
echo "Wildcard routing enabled; other domain routes preserved. Backup: $BACKUP"
