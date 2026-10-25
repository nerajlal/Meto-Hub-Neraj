# Metohub deployment guide

## TL;DR — next developer

Work in this repository. Test your changes, then commit them. The script deploys
**committed, pushed code only**, never your uncommitted working files.

```sh
# 1. Review and commit your intended changes on main.
git status
git add -A
git diff --cached --stat
git commit -m "Describe the change"

# 2. Push main and deploy it to the isolated test environment.
./deploy.sh test --push

# 3. Check https://test.metohub.cloud in your browser.
./deploy.sh status test

# 4. Promote that same commit/image to the main domain.
./deploy.sh prod
./deploy.sh status prod
```

If you already ran `git push origin main`, use `./deploy.sh test` without
`--push`. Nothing is committed automatically. `--push` only works while checked
out on the configured deployment branch (default `main`), and never force-pushes.
Production asks you to type `DEPLOY`. SSH asks for a password/key passphrase if
no reusable connection is available; enter it in your terminal, never in source.

**Current limitation:** email, Razorpay and Web Push are not configured on the
main site. `prod` deliberately refuses to update it until its existing
`app:preflight` passes. Fill the real provider settings privately on the server
and recreate the app to load them before promoting releases. Test deployments
allow those providers to remain unconfigured. Neither command enables indexing
or changes the provider settings automatically.

Preview a target without pushing, connecting or changing anything:

```sh
./deploy.sh test --dry-run
./deploy.sh prod --dry-run
```

## Where each command deploys

- `test`: `https://test.metohub.cloud`, `/srv/apps/metohub-next-staging`,
  Compose project `metohub-next-staging`, private HTTP port `8893`.
- `prod`: `https://metohub.cloud`, `/srv/apps/metohub-next`,
  Compose project `metohub-next-test`, private HTTP port `8892`.
  The main site's Compose name is historical: **do not rename it**, because
  its database/upload volume names depend on it.

Each environment has independent MySQL data, APP_KEY, credentials, uploads,
private storage, sessions, worker and scheduler. The Python Glov apps at
`glovstore.metora.in` and `test-glovstore.metora.in` are outside this workflow.
Deployment does not modify Nginx, TLS, DNS, or Glov containers/files/databases.

## Prerequisites and authentication

On the laptop: Git, Bash, SSH/SCP, and access to this repository's `origin`.
On the existing VPS: Docker Compose, Python 3, curl, flock, tar and a root SSH
connection. Host PHP, Node and MySQL are not required. The two environments,
server-side configuration files, domain routes and TLS must already exist.
This update script does not initialize a server or overwrite its configuration.

By default the server is `root@200.234.39.67`, and the deployment branch is
`main`. Overrides are `METOHUB_VPS`, `METOHUB_BRANCH`, and
`METOHUB_SSH_SOCKET`. Both commands accept a committed ancestor of the fetched
branch; they deploy local HEAD, not whatever commit happens to be newest remotely.
For normal work, pull/rebase and resolve conflicts before committing/pushing.
A custom branch override should be agreed with the team; it changes which branch
is accepted for both test and production.

The script reuses `~/.ssh/metohub-deploy.sock` if it is active. Otherwise it opens
its own temporary multiplexed connection, prompts interactively, and closes only
that connection when finished. It never deletes someone else's SSH socket.

## What happens during an update

1. Reject dirty/untracked changes. Optionally push the current branch, fetch the
   deployment branch and verify HEAD has been pushed. Reject tracked runtime
   `.env`, `app.env` or `db.env` secrets.
2. Upload a Git archive of that exact commit. Never copy laptop environment files
   or ignored local uploads, caches or dependencies. Commit SHA identifies the
   release. Builds use the Docker ignore rules and dependency lockfiles.
3. Take a VPS-wide Metohub deployment lock. Verify the existing project, private
   port and persistent volume names before touching services.
4. On test, build PHP/web images if that completed image pair does not exist.
   On production, require that exact SHA to have passed the most recent test
   deployment and reuse its image pair without rebuilding. A passing deploy is
   a technical gate; the developer still checks the browser before promotion.
5. Before disrupting production, run the current app's production preflight.
   Enter maintenance, stop the worker/scheduler and web container, then back up
   MySQL, uploads, private files, environment files and runtime configuration.
6. Switch the image tag; run migrations and configuration/database/storage checks.
   Production also runs the new image's full `app:preflight`. Recreate the app and
   web, check `/up` and `/` privately and through HTTPS, then recreate background
   services. Record the current/previous releases and successful test SHA.

**There is downtime** while web writes are stopped for backup/migration/restart;
Nginx may return 502/503 during this window. This is not a zero-downtime deploy.
Use a suitable window and keep migrations backward-compatible where possible.
No `db:seed`, key generation, dependency updates, database recreation, or volume
removal occurs during an update.

## Configuration and infrastructure changes

Each server directory contains:

- `shared/app.env`: Laravel configuration and secrets.
- `shared/db.env`: existing MySQL container credentials.
- `runtime/.env`: selected image release only.
- `runtime/compose.yml`: installed infrastructure definition.
- `releases/<commit>`: archived source for a release.
- `deployment/`: release records, deployment backups and recovery state.

Server `.env` files are preserved. Edit integration credentials directly on the
server without pasting them into chat or Git. Recreate affected services to load
environment changes; a plain container restart does not load changed Compose
`env_file` values. Keep APP_KEY stable. Never change only a MySQL environment
password and assume it changes an already-initialized MySQL account.

Changes to `deploy/Dockerfile` and application code are included in a release.
Changes to Compose files are **not** automatically installed over the running
server definition. Ports, volumes, databases, routing and other infrastructure
changes need a separate reviewed migration. Initial setup and domain-switch
helpers are not routine update commands.

## Backups and failure recovery

Before every release/rollback, backups go to
`<environment>/deployment/backups/<UTC timestamp>-<process id>/`:

- `database.sql`: MySQL dump, including routines/triggers/events.
- `files.tar.gz`: uploads and private files.
- `app.env`, `db.env`, `release.env`, `compose.yml`: protected configuration.

These contain secrets/customer data. Their directory is root-readable, but the
backup is not encrypted at rest by this script. Arrange encrypted off-server
copies, retention, disk alerts and restore tests. Backups, source archives and
Docker images are retained; prune only after checking what rollback needs.

If deployment fails before maintenance, the live site is left running. If it
fails after maintenance starts, the script stops web/worker/scheduler, restores
the prior selected image tag and records `recovery-release`. It does **not** undo
migrations or silently serve old code against a potentially incompatible schema.
Read the error and backup path, then fix the problem and redeploy. Do not manually
start services until you have checked database compatibility.

For a deliberate code rollback:

```sh
./deploy.sh rollback test
./deploy.sh rollback prod
```

Type `ROLLBACK` only after checking the old image supports the current schema.
Rollback makes another backup and selects the previous/recovery image. It never
reverses migrations or restores database data automatically. Production rollback
must still pass full preflight. A test rollback clears the production-promotion
marker; deploy the intended test commit again before promotion. The first update
records the currently installed snapshot image as its previous release.

Inspect logs on the VPS (choose the correct directory):

```sh
cd /srv/apps/metohub-next-staging/runtime
docker compose ps
docker compose logs --tail=100 app web worker scheduler
```

## Verify the deployment tooling locally

```sh
bash -n deploy.sh deploy/release.sh
php -l deploy/check-runtime.php
python3 -m unittest discover -s deploy/tests -v
./deploy.sh test --dry-run
```

The orchestration tests use mock commands and temporary directories. They check
backup ordering, failure recovery, promotion gates and code-only rollback without
SSH or a Docker daemon. They do not replace an actual test deployment/browser
check. The script changes should be committed before their first real release.
