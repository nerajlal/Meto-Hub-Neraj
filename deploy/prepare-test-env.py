#!/usr/bin/env python3
"""Create isolated test secrets on the VPS; never print them."""
import argparse
import base64
import os
from pathlib import Path
import secrets
import sys

parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('base')
parser.add_argument('template')
parser.add_argument('--domain', default='metohub.cloud')
parser.add_argument('--database', default='metohub_next_test')
args = parser.parse_args()
import re
if not re.fullmatch(r'[a-zA-Z0-9][a-zA-Z0-9.-]+', args.domain):
    parser.error('Invalid domain')
if not re.fullmatch(r'[a-zA-Z0-9_]+', args.database):
    parser.error('Invalid database name')
base = Path(args.base)
shared = base / 'shared'
shared.mkdir(mode=0o700, parents=True, exist_ok=True)
if (shared / 'app.env').exists() or (shared / 'db.env').exists():
    raise SystemExit('Environment already exists; preserve it and review manually.')
password = secrets.token_hex(32)
values = {}
for line in Path(args.template).read_text().splitlines():
    if line and not line.startswith('#') and '=' in line:
        key, value = line.split('=', 1)
        values[key] = value
values.update(APP_KEY='base64:' + base64.b64encode(secrets.token_bytes(32)).decode(),
    APP_URL=f'https://{args.domain}', DB_HOST='db', DB_DATABASE=args.database,
    DB_USERNAME='metohub_next', DB_PASSWORD=password, MAIL_MAILER='log',
    SEARCH_INDEXABLE='false', MAIL_EHLO_DOMAIN=args.domain,
    GOOGLE_REDIRECT_URL=f'https://{args.domain}/auth/google/callback')
os.umask(0o077)
(shared / 'app.env').write_text(''.join(f'{k}={v}\n' for k, v in values.items()))
(shared / 'db.env').write_text(f'MYSQL_DATABASE={args.database}\nMYSQL_USER=metohub_next\nMYSQL_PASSWORD={password}\nMYSQL_ROOT_PASSWORD={secrets.token_hex(32)}\n')
print('Created isolated test environment. Integrations disabled; credentials remain on VPS.')
