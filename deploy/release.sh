#!/usr/bin/env bash
# VPS controller. Updates existing Metohub applications only; no DNS/Nginx changes.
set -Eeuo pipefail
ACTION=${1:-}; ENVIRONMENT=${2:-}; SHA=${3:-}; UPLOAD=${4:--}
[[ $(id -u) == 0 ]] || { echo 'Use the root SSH connection.'; exit 1; }
case "$ENVIRONMENT" in
    test) BASE=/srv/apps/metohub-next-staging; PROJECT=metohub-next-staging; DOMAIN=test.metohub.cloud; PORT=8893;;
    prod) BASE=/srv/apps/metohub-next; PROJECT=metohub-next-test; DOMAIN=metohub.cloud; PORT=8892;;
    *) exit 2;;
esac
case "$ACTION" in test|prod) [[ "$ACTION" == "$ENVIRONMENT" ]] || exit 2;; rollback) ;; *) exit 2;; esac
[[ "$SHA" =~ ^[a-f0-9]{40}$ ]] || exit 2
STATE=$BASE/deployment
RUNTIME=$BASE/runtime
[[ -f "$RUNTIME/compose.yml" && -f "$RUNTIME/.env" && -f "$BASE/shared/app.env" && -f "$BASE/shared/db.env" ]] || { echo 'Existing deployment is missing. This script does not initialize or overwrite server configuration.'; exit 1; }
install -d -m 700 "$STATE" /srv/apps/metohub-next-deploy
exec 9>/srv/apps/metohub-next-deploy/lock
flock -n 9 || { echo 'Another Metohub release is running; retry when it finishes.'; exit 1; }
umask 077
cd "$RUNTIME"
dc() { docker compose --project-name "$PROJECT" "$@"; }
# Never source .env as executable shell code.
OLD=$(sed -n 's/^RELEASE=//p' .env)
[[ "$OLD" =~ ^[A-Za-z0-9_-]+$ ]] || { echo 'Unexpected current RELEASE.'; exit 1; }
# Keep existing infrastructure; reject accidental project/port/volume changes.
docker compose config --format json | python3 -c '
import json,sys
c=json.load(sys.stdin); project=sys.argv[1]; port=sys.argv[2]
assert c["name"] == project, "Unexpected Compose project"
p=c["services"]["web"]["ports"]
assert len(p)==1 and p[0].get("host_ip")=="127.0.0.1" and str(p[0]["published"])==port
for v in ["db","uploads","private","logs"]:
    assert c["volumes"][v]["name"]==project+"_"+v, "Unexpected persistent volume"
' "$PROJECT" "$PORT"
if [[ "$ACTION" == rollback ]]; then
    RECORD=$STATE/previous
    [[ ! -s "$STATE/recovery-release" ]] || RECORD=$STATE/recovery-release
    [[ -s "$RECORD" ]] || { echo 'No previous release recorded.'; exit 1; }
    RELEASE=$(cat "$RECORD")
    [[ "$RELEASE" =~ ^[A-Za-z0-9_-]+$ ]] || exit 1
else
    [[ "$UPLOAD" == "$BASE/incoming/upload."* && -f "$UPLOAD/source.tar.gz" ]] || exit 2
    SOURCE=$BASE/releases/$SHA
    if [[ ! -d "$SOURCE" ]]; then
        mkdir -p "$BASE/releases"
        EXTRACT=$(mktemp -d "$BASE/releases/.extract.XXXXXXXX")
        tar -xzf "$UPLOAD/source.tar.gz" -C "$EXTRACT"
        mv "$EXTRACT" "$SOURCE"
    fi
    [[ -f "$SOURCE/deploy/Dockerfile" && -f "$SOURCE/deploy/check-runtime.php" ]] || { echo 'Commit the deployment files before deploying.'; exit 1; }
    RELEASE=$SHA
    if [[ "$ENVIRONMENT" == prod ]]; then
        [[ -s /srv/apps/metohub-next-staging/deployment/tested && $(cat /srv/apps/metohub-next-staging/deployment/tested) == "$SHA" ]] || { echo 'Deploy this exact commit to test first.'; exit 1; }
    elif ! docker image inspect "metohub-next-app:$SHA" "metohub-next-web:$SHA" >/dev/null 2>&1; then
        # Never rebuild a completed tag: production promotes the tested image pair.
        docker build --target app -f "$SOURCE/deploy/Dockerfile" -t "metohub-next-app:$SHA" "$SOURCE"
        docker build --target web -f "$SOURCE/deploy/Dockerfile" -t "metohub-next-web:$SHA" "$SOURCE"
    fi
fi
docker image inspect "metohub-next-app:$RELEASE" "metohub-next-web:$RELEASE" >/dev/null
# Require the existing application/DB to be available before entering maintenance.
dc exec -T app php artisan --version >/dev/null
dc exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mysql -u "$MYSQL_USER" "$MYSQL_DATABASE" -e "SELECT 1"' >/dev/null
# Catch existing production configuration gaps before disrupting traffic.
if [[ "$ENVIRONMENT" == prod && "$ACTION" != rollback ]]; then
    dc exec -T -u www-data app php artisan app:preflight
fi
BACKUP=$STATE/backups/$(date -u +%Y%m%dT%H%M%SZ)-$$
mkdir -p "$BACKUP"
cp .env "$BACKUP/release.env"
cp compose.yml "$BACKUP/compose.yml"
cp "$BASE/shared/app.env" "$BASE/shared/db.env" "$BACKUP/"
MAINTENANCE=0
recover() {
    result=$?
    trap - ERR
    if [[ "$MAINTENANCE" == 1 ]]; then
        dc stop web worker scheduler || true
        cp "$BACKUP/release.env" "$RUNTIME/.env"
        printf '%s\n' "$OLD" > "$STATE/recovery-release"
        echo "Deployment failed. Public app/worker/scheduler remain stopped. Database migrations were NOT reversed."
        echo "Backup/recovery files: $BACKUP. Fix and redeploy, or use rollback after checking schema compatibility."
    fi
    exit "$result"
}
trap recover ERR
MAINTENANCE=1
dc exec -T -u www-data app php artisan down --retry=60
dc stop worker scheduler web
# No app writes during backup/migration. Passwords remain in the DB container env.
dc exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqldump -u root --single-transaction --no-tablespaces --routines --triggers --events "$MYSQL_DATABASE"' > "$BACKUP/database.sql"
[[ -s "$BACKUP/database.sql" ]]
dc exec -T app tar -czf - -C /var/www/html public/storage storage/app/private > "$BACKUP/files.tar.gz"
printf 'RELEASE=%s\n' "$RELEASE" > .env.next
mv .env.next .env
if [[ "$ACTION" != rollback ]]; then
    dc run --rm --no-deps app php artisan migrate --force
    dc run --rm --no-deps -e "DEPLOY_EXPECTED_URL=https://$DOMAIN" app php deploy/check-runtime.php
fi
if [[ "$ENVIRONMENT" == prod ]]; then
    dc run --rm --no-deps app php artisan app:preflight
fi
# Do not replace the DB or change Compose files, credentials, volume names or routing.
dc up -d --no-deps --no-build --pull never --force-recreate app
# New app container has fresh caches; explicitly clear maintenance for reruns as well.
dc exec -T -u www-data app php artisan up
dc up -d --no-deps --no-build --pull never --force-recreate --wait --wait-timeout 180 web
for path in /up /; do
    curl -fsS --retry 5 --retry-all-errors --retry-delay 1 --max-time 20 -H "Host: $DOMAIN" -H 'X-Forwarded-Proto: https' "http://127.0.0.1:$PORT$path" >/dev/null
    curl -fsS --retry 5 --retry-all-errors --retry-delay 1 --max-time 20 --resolve "$DOMAIN:443:127.0.0.1" "https://$DOMAIN$path" >/dev/null
done
dc up -d --no-deps --no-build --pull never --force-recreate worker scheduler
for service in worker scheduler; do
    container=$(dc ps -q "$service")
    [[ -n "$container" && $(docker inspect -f '{{.State.Running}}' "$container") == true ]]
done
if [[ "$OLD" != "$RELEASE" ]]; then printf '%s\n' "$OLD" > "$STATE/previous"; fi
printf '%s\n' "$RELEASE" > "$STATE/current"
if [[ "$ENVIRONMENT" == test ]]; then
    if [[ "$ACTION" != rollback ]]; then printf '%s\n' "$SHA" > "$STATE/tested"; else rm -f "$STATE/tested"; fi
fi
rm -f "$STATE/recovery-release"
trap - ERR
echo "Deployed $RELEASE to https://$DOMAIN. Backup: $BACKUP"
dc ps
