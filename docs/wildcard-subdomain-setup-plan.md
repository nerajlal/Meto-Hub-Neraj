# Permanent SaaS subdomain setup plan

Status: activated on production on 1 November 2026. Verified valid HTTPS and
the Mithram storefront at `mithram.metohub.cloud`, unknown-store HTTP 404, and
successful responses from production, test and both Glov domains. Renewal timer
is active; its service completed successfully (certificate not due yet).
Initial certificate expires 30 January 2027 and covers `metohub.cloud` and
`*.metohub.cloud`. Activation backup:
`/srv/apps/metohub-next/wildcard-backup-20261101T043459Z`.

## Operations quick reference

Production store subdomains use host Nginx → `127.0.0.1:8892` → Laravel tenant
lookup. The exact `test.metohub.cloud` route stays on port `8893`. Glov routes
remain separate. Ordinary `deploy.sh` releases do not replace host Nginx or TLS.

Server tooling lives in `/srv/apps/metohub-next/wildcard-tools`. Initial setup:

```sh
bash /srv/apps/metohub-next/wildcard-tools/setup-certificate.sh
bash /srv/apps/metohub-next/wildcard-tools/enable-routing.sh
```

The activation script is for first setup only: it refuses to overwrite an existing
wildcard site and creates a private backup before enabling routing. Deploy the
application subdomain support first. Do not rerun activation for code releases.

Certificates are installed in `/etc/nginx/metohub-wildcard/`. The
`metohub-wildcard-renew.timer` checks renewal every six hours with a randomized
delay. Successful renewal installs the certificate and validates/reloads Nginx.

```sh
systemctl status metohub-wildcard-renew.timer
journalctl -u metohub-wildcard-renew.service --since '7 days ago'
openssl x509 -in /etc/nginx/metohub-wildcard/fullchain.pem -noout -dates -ext subjectAltName
```

Generate the Hostinger token at <https://hpanel.hostinger.com/profile/api> using
the account managing this DNS zone. Store only the token in
`/root/.config/metohub/hostinger-token`, owned by root with mode `600`. Never put
it in Git, application `.env`, or chat. Replace it before its chosen expiry;
an expired token prevents future renewals. After replacement, check with
`systemctl start metohub-wildcard-renew.service` (a certificate not yet due will
be skipped; this alone does not prove DNS API credentials work).

## Original implementation plan

## 1. Verify wildcard DNS

Check that the record is literally `*.metohub.cloud` pointing to `200.234.39.67`. A record named `star.metohub.cloud` only covers that single hostname.

## 2. Set up wildcard HTTPS with automatic renewal

Obtain a certificate covering `*.metohub.cloud`. Wildcard certificates require DNS TXT verification. For automatic renewal, we’ll need a supported DNS-provider integration and a narrowly scoped API credential, stored only on the server.

## 3. Configure domain routing

Route store subdomains to production Laravel. Keep explicit routes for:

- `metohub.cloud` → platform homepage
- `test.metohub.cloud` → isolated test environment
- Glov domains → existing Glov apps

## 4. Harden store identification

Match each hostname to its saved store domain. Unknown subdomains will show **“Store not found”**. Reserve names such as `test`, `www`, and `admin` so merchants cannot claim them.

## 5. Clarify the admin panel

Saving an available subdomain will connect it through the shared wildcard infrastructure. Adjust the status message so “Connected” doesn’t misleadingly imply that arbitrary custom-domain DNS and HTTPS were provisioned.

## 6. Test before applying production changes

Verify store matching, unknown/reserved names, HTTPS, and separation from the test environment. Back up Nginx configuration before switching routing.

The corrected database entry will be preserved. Wildcard support covers platform subdomains; merchant-owned domains still need separate verification and certificates.

## DNS provider

DNS is managed through Hostinger. Public nameservers checked on 27 October 2026:

- `solar.dns-parking.com`
- `lunar.dns-parking.com`

Automated wildcard certificate renewal uses the Hostinger DNS API through the
pinned acme.sh integration in `deploy/wildcard/setup-certificate.sh`. Credentials
remain private on the server. Do not store API tokens in this repository or paste
them into chat.
