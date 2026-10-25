#!/usr/bin/env bash
# Developer entry point. Run from a checkout on your laptop.
set -Eeuo pipefail
cd "$(dirname "$0")"
usage() {
    cat <<'HELP'
Usage:
  ./deploy.sh test [--push] [--dry-run]
  ./deploy.sh prod [--push] [--dry-run]
  ./deploy.sh rollback test|prod [--dry-run]
  ./deploy.sh status [test|prod]

--push     Push the current deployment branch to origin before deploying.
--dry-run  Show the target and checks without pushing, SSH, or deployment.
Default branch: main. Override with METOHUB_BRANCH.
HELP
}
ACTION=${1:---help}; shift "$(( $# > 0 ? 1 : 0 ))"
PUSH=0; DRY=0; ENVIRONMENT=$ACTION
case "$ACTION" in
    test|prod) ;;
    rollback) ENVIRONMENT=${1:-}; [[ $# == 0 ]] || shift;;
    status) ENVIRONMENT=${1:-test}; [[ $# == 0 ]] || shift;;
    --help|-h) usage; exit 0;;
    *) usage; exit 2;;
esac
[[ "$ENVIRONMENT" == test || "$ENVIRONMENT" == prod ]] || { usage; exit 2; }
for arg in "$@"; do
    case "$arg" in
        --push) PUSH=1;;
        --dry-run) DRY=1;;
        *) usage; exit 2;;
    esac
done
[[ "$ACTION" != status && "$ACTION" != rollback || "$PUSH" == 0 ]] || { echo '--push is only valid for test/prod.'; exit 2; }
VPS=${METOHUB_VPS:-root@200.234.39.67}
BRANCH=${METOHUB_BRANCH:-main}
git check-ref-format "refs/heads/$BRANCH" >/dev/null
DOMAIN=test.metohub.cloud; BASE=/srv/apps/metohub-next-staging
[[ "$ENVIRONMENT" != prod ]] || { DOMAIN=metohub.cloud; BASE=/srv/apps/metohub-next; }
SHA=$(git rev-parse HEAD)
if [[ "$DRY" == 1 ]]; then
    echo "Action: $ACTION; environment: $ENVIRONMENT; domain: https://$DOMAIN"
    echo "Server: $VPS; directory: $BASE; commit: $SHA; branch: $BRANCH; push: $PUSH"
    echo 'No commands will run remotely. Deployment requires clean, pushed code; prod requires this commit to pass test and production preflight.'
    if [[ -n $(git status --porcelain) ]]; then echo 'Working tree is dirty: commit intended changes before deploying.'; fi
    exit 0
fi
if [[ "$ACTION" == test || "$ACTION" == prod ]]; then
    [[ -z $(git status --porcelain) ]] || { echo 'Commit all intended changes first. Deployment requires a clean working tree (including untracked files).'; exit 1; }
    if [[ "$PUSH" == 1 ]]; then
        [[ $(git branch --show-current) == "$BRANCH" ]] || { echo "--push requires the current branch to be $BRANCH."; exit 1; }
        git push origin "HEAD:refs/heads/$BRANCH"
    fi
    git fetch origin "$BRANCH"
    git merge-base --is-ancestor "$SHA" FETCH_HEAD || { echo "Commit is not pushed to origin/$BRANCH. Push it first or use --push."; exit 1; }
    if git ls-tree -r --name-only "$SHA" | grep -Eq '(^|/)(\.env|app\.env|db\.env)$'; then
        echo 'A runtime secrets file is tracked. Remove it from Git and rotate exposed secrets before deployment.'; exit 1
    fi
fi
if [[ "$ACTION" == prod ]]; then
    read -r -p "Deploy $SHA to https://$DOMAIN after testing it? Type DEPLOY: " answer
    [[ "$answer" == DEPLOY ]] || exit 1
elif [[ "$ACTION" == rollback ]]; then
    read -r -p "Roll back $DOMAIN code only? Confirm old code supports the CURRENT database schema. Type ROLLBACK: " answer
    [[ "$answer" == ROLLBACK ]] || exit 1
fi
TEMP=$(mktemp -d /tmp/metohub-deploy.XXXXXX)
OWN_SOCKET=0
SOCKET=${METOHUB_SSH_SOCKET:-$HOME/.ssh/metohub-deploy.sock}
cleanup() {
    if [[ "$OWN_SOCKET" == 1 ]]; then ssh -S "$SOCKET" -O exit "$VPS" >/dev/null 2>&1 || true; fi
    rm -rf "$TEMP"
}
trap cleanup EXIT
if ! ssh -S "$SOCKET" -O check "$VPS" >/dev/null 2>&1; then
    SOCKET=$TEMP/socket
    ssh -M -S "$SOCKET" -o ControlPersist=600 -fN "$VPS"
    OWN_SOCKET=1
fi
SSH_ARGS=(-S "$SOCKET" -o BatchMode=yes)
if [[ "$ACTION" == status ]]; then
    ssh "${SSH_ARGS[@]}" "$VPS" "cd '$BASE/runtime' && docker compose ps"
    exit
fi
if [[ "$ACTION" != rollback ]]; then
    git archive --format=tar.gz "$SHA" > "$TEMP/source.tar.gz"
    ssh "${SSH_ARGS[@]}" "$VPS" "install -d -m 700 '$BASE/incoming'"
    # Unique upload directory avoids two developers overwriting each other's archive.
    REMOTE=$(ssh "${SSH_ARGS[@]}" "$VPS" "mktemp -d '$BASE/incoming/upload.XXXXXXXX'")
    [[ "$REMOTE" == "$BASE/incoming/upload."* && "$REMOTE" =~ ^[A-Za-z0-9/._-]+$ ]] || exit 1
    scp -o "ControlPath=$SOCKET" -o BatchMode=yes "$TEMP/source.tar.gz" "$VPS:$REMOTE/source.tar.gz"
else
    REMOTE=-
fi
# The controller is sent separately, allowing rollback even if the failed release cannot boot.
CONTROL=$(ssh "${SSH_ARGS[@]}" "$VPS" 'mktemp -d /tmp/metohub-release.XXXXXXXX')
[[ "$CONTROL" =~ ^/tmp/metohub-release\.[A-Za-z0-9]+$ ]] || exit 1
scp -o "ControlPath=$SOCKET" -o BatchMode=yes deploy/release.sh "$VPS:$CONTROL/release.sh"
result=0
ssh "${SSH_ARGS[@]}" "$VPS" "bash '$CONTROL/release.sh' '$ACTION' '$ENVIRONMENT' '$SHA' '$REMOTE'" || result=$?
ssh "${SSH_ARGS[@]}" "$VPS" "rm -f '$CONTROL/release.sh'; rmdir '$CONTROL'" || true
exit "$result"
