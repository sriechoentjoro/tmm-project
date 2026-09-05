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

## Still to do

- **Deploy this branch to production.** Until then `config/app.php` and
  `config/app_datasources.php` on the server still hold the **new** passwords
  in files that git tracks. Deployment order matters — see below.
- **Review the remaining `webroot/` debug scripts** (listed further down).
  None of them hold credentials any more, but they are development scripts in
  a publicly served directory.

### Deploying this change to the server

The server's working copy has local edits carrying the new secrets. Write them
into the git-ignored file first, then discard the tracked edits, then pull:

```bash
cd /var/www/html/tmm
cp config/app_local.example.php config/app_local.php
# fill in the real values (DB password, SMTP user/password, salt)
chown www-data:www-data config/app_local.php
chmod 640 config/app_local.php

git checkout -- config/app.php config/app_datasources.php
git pull
```

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

## Still in the web root

`webroot/` is served directly, so anything there is reachable over HTTP. These
were removed because they exposed credentials or configuration:

- `info.php` — was `phpinfo()`, printing the full environment
- `simple_debug.php`, `test_login.php`, `test_password.php`, `update_passwords.php`

These remain and are worth reviewing — they are development scripts sitting in
a public directory:

```
webroot/check_all_location_tables.php
webroot/check_candidate_educations_schema.php
webroot/check_candidates_schema.php
webroot/check_db_data.php
webroot/check_master_kabupatens_schema.php
webroot/debug-apache.php
webroot/debug_institutions.php
webroot/debug_session.php
webroot/direct_require_test.php
webroot/final_test.php
webroot/fix_file.php
webroot/fix_token.php
webroot/generate_hash.php
webroot/migrate_candidate_education_location_data.php
webroot/parse_test.php
```

None of them are needed for the application to run — CakePHP serves everything
through `webroot/index.php`.

## Keeping it clean

`.gitignore` now covers `config/app_local*.php` (except the example) and
config backups (`*.backup`, `*.bak`, `*.before_*`, `*.disabled`). Before
committing anything under `config/`, confirm it holds no real values.

Backups of files that held old credentials belong outside the repository
directory (for example `/root/`), never inside it.
