# Credentials: rotation and configuration

**This repository is public.** Anything committed here is readable by anyone on
the internet, and stays readable in the git history even after it is deleted.

## What was exposed

Credentials were committed in plaintext and are in the public history:

| Secret | Where it was | Status |
|---|---|---|
| MySQL password (user `tmm`, all 15 databases) | `config/app_datasources.php` + 20 other files | **Must be rotated** |
| MySQL password (user `root`, older commits) | same files, earlier history | **Must be rotated** |
| Gmail App Password | `config/app.php` (`EmailTransport`) | **Must be revoked** |
| `Security.salt` | `config/app.php` — still the placeholder `__SALT__` | **Must be set** |

Removing them from the current files does **not** undo the exposure. They are
in the history of a public repository and must be assumed compromised.

## Step 1 — Rotate (do this first)

### MySQL

```sql
-- as a MySQL admin on the server
ALTER USER 'tmm'@'localhost' IDENTIFIED BY 'a-new-strong-password';
FLUSH PRIVILEGES;
```

Check whether MySQL is reachable from outside the host. If it is, restrict it:

```bash
ss -lntp | grep 3306      # 127.0.0.1:3306 = local only (good)
                          # 0.0.0.0:3306   = exposed to the network (bad)
```

### Gmail App Password

Revoke the old one at <https://myaccount.google.com/apppasswords> and generate
a new one. The old value is public, so anyone can send mail as this account
until it is revoked.

### Security salt

`config/app.php` uses `env('SECURITY_SALT', '__SALT__')`. If the environment
variable is not set in production, the app is hashing cookies and CSRF tokens
with the literal string `__SALT__`, which is in this public repository.

```bash
php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'
```

Changing it invalidates existing sessions and CSRF tokens — users are logged
out once, which is expected.

## Step 2 — Configure the new values

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

## Step 3 — Consider the history

The values remain in the public git history. Options, in order of practicality:

1. **Rotate and move on** (recommended). Once rotated, the old values are
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
