# Working on this repository

CakePHP 3.9, targeting PHP 7.4, MySQL 8. The data lives in fourteen databases
behind fifteen connection aliases — `config/app_datasources.php` maps alias to
database, and `default` and `cms_masters` deliberately point at the same one.
A table class names its own database with `defaultConnectionName()`.

**This repository is public.** No credential, no password, no API key, no
private hostname belongs in any file here — including in this one. Real values
go in `config/app_local.php`, which `.gitignore` keeps out.

## How work is done here

One real finding at a time:

1. Find a defect that actually exists, and prove it exists before fixing it.
2. Fix it.
3. **Fence it in** with a harness under `tests/`, a checker under `bin/`, or
   both — whichever can see the whole class of the mistake rather than the one
   instance.
4. **Prove the fence is not vacuous**: break the fix on purpose, watch the new
   check fail, put it back. A check that passes before the fix proves nothing.
5. Run the whole suite and the checkers. Commit. Push.

Reply to the user **in Indonesian**. Any command they must run themselves gets
the label `# === JALANKAN DI TERMINAL VPS ===` and says plainly whether it only
reads or changes data.

The working copy on the server is also the directory the web server serves, so
a saved file is live at once. Run `php -l` on any PHP file right after touching
it, and the full suite (about 19 seconds) *before* a commit, not after.

## Tools

```
php tests/run.php              # 42 harnesses, ~1150 checks, ~19s
php tests/run.php <name>       # only harnesses whose name matches
php tests/run.php --list       # name them without running them
php bin/check-<name>.php       # a static checker; --all on most of them
php bin/query.php <db> "SQL"   # read-only: SELECT/SHOW/DESCRIBE/EXPLAIN only
php bin/apply-migration.php <file.sql> [--go]
```

The thirteen checkers: `accessible-fields`, `config`, `create-validation`,
`duplicate-tables`, `entity-columns`, `enum-values`, `export-scope`, `icons`,
`input-types`, `role-names`, `route-targets`, `schema-names`, `view-vars`.

Four of them (`create-validation`, `duplicate-tables`, `entity-columns`,
`role-names`) ask a real database and mean nothing without one. `check-config`
exits 1 while the mail credentials are unset — that is an open finding, not a
broken checker. Everything else should exit 0.

`tests/README.md` says what each harness covers and which need `pdo_sqlite`.

## Traps this codebase has already paid for

- **CakePHP drops silently what it cannot place.** `save()` drops a non-column,
  `newEntity()` drops a field missing from `$_accessible`, `PaginatorComponent`
  drops a sort on an unknown field, `BoolType::marshal('')` becomes `false`.
  Nothing errors; the screen just quietly does the wrong thing.
- **`granted_actions = '*'` expands to `[the menu's own action, index, view]`** —
  nothing else. Every non-CRUD action needs its own exception in
  `isAuthorized()`. An exception that reads *another* action's permission must
  also accept its own: a menu pointing at `/x/wizard` grants `wizard`, not
  `add`, so a rule reading only `add` refuses the wizard to the very role whose
  menu is the wizard.
- **`database_exports/*.sql` is the schema as the database describes it**, not
  as the application believes it to be. Every check that reads only the
  application's own account agrees with the application, including where it is
  wrong. Two sources disagreeing is the signal.
- **A table name is not unique.** Seven names exist in more than one database
  with different columns. Resolve a table through the connection its model
  declares, never by name alone.
- **A fixture must carry the real constraint.** SQLite accepts anything; an
  ENUM or NOT NULL left out of a fixture has hidden a defect three times now.
- **Institution scoping lives in `LpkDataFilterTrait`.** `scopeToInstitution()`
  narrows a listing, `refuseOtherInstitution()` guards one record,
  `scopeCandidates()` narrows a pick-list. `canAccessRecord()` is the older,
  divergent one — do not reach for it.
- **A trait method beats an inherited one**, which is why an `AppController`
  method can sit there never running.
- An export, a print view or an AJAX endpoint is a separate action with its own
  query. Whatever the index screen narrows by, it narrows too — `bin/check-export-scope.php`
  exists because that was missed in 39 places at once.

## Where things stand

Open, and none of it secret:

1. `database/migrations/add_candidate_wizard_menu.sql` has not been applied
   with `--go`. Until it is, the menu entry for the CV-preview registration
   wizard does not appear; the page itself works at `/candidates/wizard`.
2. `chart_of_accounts` — the model opens a database no SQL in this repository
   describes, and two other databases define that table name differently.
   `SHOW COLUMNS` settles which spelling the code should use.
3. `candidate_submission_documents` — the dump gives it no `id` and no primary
   key, while the table class and the index screen both assume one. Recorded in
   `EXPECTED` in `bin/check-schema-names.php` with the query that settles it.
4. Mail credentials are unset, so institution-registration verification links
   are never delivered. `bin/check-config.php` reports it every run.
5. `vendor/ImageResize/ImageResize.php` is missing, so uploaded photos are
   neither shrunk nor watermarked.
6. Eighteen table classes whose tables no SQL in this repository creates, and a
   handful of index screens that cannot be checked for the same reason — both
   listed by `bin/check-schema-names.php`.

Development continues on `claude/http-500-error-avtyuc`. It is a long way ahead
of `main` and has deliberately not been merged yet.

## Background, and where it is out of date

`.github/copilot-instructions.md` is a long guide to the bake templates and the
form conventions. Claude Code does not read it automatically, and parts of it
have been overtaken:

- it says **PHP 5.6 only**; the target is 7.4,
- it points at **`canAccessRecord()`** for data scoping; see the trait above,
- several of its generated patterns are exactly what produced the defects fixed
  since — date pickers on `*_id` columns, pass/fail flags rendered as password
  boxes, placeholder export columns.

Read it for the form and template conventions. Check anything it says about
schema or permissions against the code.
