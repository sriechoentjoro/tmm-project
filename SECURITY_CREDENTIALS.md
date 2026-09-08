# Credentials: rotation and configuration

**This repository is public.** Anything committed here is readable by anyone on
the internet, and stays readable in the git history even after it is deleted.

## What was exposed

Credentials were committed in plaintext and are in the public history. All of
them have now been rotated or revoked on the production server:

| Secret | Where it was | Status |
|---|---|---|
| MySQL password (user `tmm`, all 15 databases) | `config/app_datasources.php` + 20 other files | Rotated |
| MySQL password (user `root`, older commits) | same files, earlier history | Rotated (2026-08-29) |
| Gmail App Password | `config/app.php` (`EmailTransport`) | Revoked and replaced |
| `Security.salt` | `config/app.php` — was the placeholder `__SALT__` | Set to a random value |

Removing them from the current files does **not** undo the exposure. The old
values are in the history of a public repository and must be assumed
compromised — which is why they were rotated rather than merely deleted.

Rotating `Security.salt` invalidated existing sessions and CSRF tokens, so
users were logged out once. That is expected.

## Deployed

Production runs this configuration as of 2026-09-08. Every credential lives in
`config/app_local.php`, which is git-ignored; no tracked file holds a value.
Verified on the server: all 15 connections resolve to the current password, a
direct `PDO` connection to `cms_masters` succeeds, and the site returns 200.

`Security.salt` was regenerated at the same time — the first value set by hand
was 14 characters, well short of what `bin2hex(random_bytes(32))` produces.
`App.fullBaseUrl` is `https://`, matching the scheme nginx redirects to; the
`http://` it held before would have put a 301 in front of every link sent by
email.

## Still to do

- **Review the remaining `webroot/` debug scripts** (listed further down).
  None of them hold credentials any more, but they are development scripts in
  a publicly served directory.
- **Delete the old config backups** under `/root/` once there is no doubt the
  deployment is stable. They contain pre-rotation passwords.

### Deploying to another server

`bin/extract-local-config.php` does the delicate part: it reads whatever
`config/app.php` currently serves and writes `config/app_local.php` from it, so
no value is retyped. Run it **before** reverting the tracked files, because the
revert is what discards them.

```bash
cd /path/to/app
php bin/extract-local-config.php --base-url=https://example.com
chown www-data:www-data config/app_local.php

git checkout -- config/app.php config/app_datasources.php
git pull
```

Check `git branch --show-current` first: a working copy sitting on some other
branch will report "Already up to date" and quietly deploy nothing.

Do **not** run `git add -A` in that directory before `config/app_local.php`
exists and the tracked files are reverted — it would commit the new passwords
back into the public repository.

## How rotation was done (for reference)

### MySQL

```sql
-- as a MySQL admin on the server
ALTER USER 'tmm'@'localhost' IDENTIFIED BY 'a-new-strong-password';
FLUSH PRIVILEGES;
```

`ALTER USER ... IDENTIFIED BY` keeps the account's existing authentication
plugin; MySQL 8 removed `SET PASSWORD ... = PASSWORD()`.

Check whether MySQL is reachable from outside the host. If it is, restrict it:

```bash
ss -lntp | grep 3306      # 127.0.0.1:3306 = local only (good)
                          # 0.0.0.0:3306   = exposed to the network (bad)
```

### Gmail App Password

Revoked at <https://myaccount.google.com/apppasswords> and regenerated. The old
value is public, so anyone could have sent mail as this account until it was
revoked.

### Security salt

`config/app.php` uses `env('SECURITY_SALT', '__SALT__')`. With the environment
variable unset, the app was hashing cookies and CSRF tokens with the literal
string `__SALT__`, which is in this public repository.

```bash
php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'
```

Note: user passwords were not affected. `DefaultPasswordHasher` uses bcrypt via
`password_hash()`, which does not read `Security.salt`.

## Configuring the new values

Credentials are no longer stored in tracked files. `config/app_datasources.php`
reads them from the environment, falling back to `config/app_local.php`, which
is git-ignored. Environment variables win when both are present.

### Option A — `config/app_local.php` (simplest)

```bash
cd /var/www/html/tmm
cp config/app_local.example.php config/app_local.php
# edit the CHANGE_ME values
chown www-data:www-data config/app_local.php
chmod 640 config/app_local.php
```

### Option B — environment variables

For php-fpm these must be set in the **pool config**, not via `putenv()` —
a plain `putenv()` during a web request does not reach the config files:

```ini
; /etc/php/7.4/fpm/pool.d/<pool>.conf
env[TMM_DB_HOST] = localhost
env[TMM_DB_USERNAME] = tmm
env[TMM_DB_PASSWORD] = the-new-password
env[TMM_SMTP_USERNAME] = you@example.com
env[TMM_SMTP_PASSWORD] = the-new-app-password
env[SECURITY_SALT] = the-generated-salt
```

Then `systemctl restart php7.4-fpm`.

Note: if you use this option, do **not** put `phpinfo()` back in the web root —
it prints the environment, which would expose everything set here.

### Verify

The app fails loudly at boot if the database credentials are missing, rather
than surfacing a confusing error on the first query:

```
Database credentials are not configured. Set TMM_DB_USERNAME and
TMM_DB_PASSWORD in the environment, or create config/app_local.php ...
```

So a successful page load confirms the configuration is being read.

## The history

The old values remain in the public git history. Options, in order of
practicality:

1. **Rotate and move on** (what was done). Once rotated, the old values are
   worthless. Rewriting history on a public repository that others may have
   cloned buys little.
2. **Make the repository private.** Limits future exposure but does not
   retract what has already been fetched or indexed.
3. **Rewrite history** with `git filter-repo`. Invalidates every existing
   clone and requires a force-push. Only worth it combined with rotation, and
   it still cannot un-publish what has already been read.

## The web root

`webroot/` is served directly, so anything there is reachable over HTTP.

Removed in #4, because they exposed credentials or configuration:

- `info.php` — was `phpinfo()`, printing the full environment
- `simple_debug.php`, `test_login.php`, `test_password.php`, `update_passwords.php`

Removed after the deployment — 15 development scripts, none referenced by any
application code:

```
check_all_location_tables.php   debug_session.php    fix_token.php
check_candidate_educations_schema.php                generate_hash.php
check_candidates_schema.php     direct_require_test.php
check_db_data.php               final_test.php       parse_test.php
check_master_kabupatens_schema.php                   debug-apache.php
debug_institutions.php          fix_file.php
migrate_candidate_education_location_data.php
```

Two of them were worse than mere clutter. `generate_hash.php` printed a bcrypt
hash for `password123` together with the SQL to apply it:

```sql
UPDATE users SET password = '$hash' WHERE username = 'admin';
```

Anyone who loaded that page got a working credential and the exact statement to
install it. And five of the scripts opened MySQL connections as `root` with an
empty password — harmless on this server, where `root` has one, but they
document the intent plainly enough.

### Also removed

Six more of the same kind, missed by the original inventory because they
arrived with the production snapshot after it was written:

```
webroot/test_ajax_element.php      webroot/test_propinsi.php
webroot/test_apprentices.php       webroot/update_apprentices_data.php
webroot/test_opcache_cleared.php   webroot/verify_db_associations.php
```

`update_apprentices_data.php` was the one that mattered: it called
`updateAll()` on the apprentices table, so loading the URL rewrote rows. No
authentication, no confirmation. The rest booted the framework and printed
schema, association or OPcache detail.

`webroot/` now holds `index.php` and static assets only — CakePHP serves every
request through `index.php`, so nothing else belongs there.

## Keeping it clean

`.gitignore` now covers `config/app_local*.php` (except the example) and
config backups (`*.backup`, `*.bak`, `*.before_*`, `*.disabled`). Before
committing anything under `config/`, confirm it holds no real values.

Backups of files that held old credentials belong outside the repository
directory (for example `/root/`), never inside it.
