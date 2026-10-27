# Permanent SaaS subdomain setup plan

Status: proposed implementation plan; these changes have not yet been implemented.

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

The remaining prerequisite for automated wildcard certificate renewal is a suitable Hostinger DNS API integration and server-side credentials. Do not store API tokens in this repository or paste them into chat.
