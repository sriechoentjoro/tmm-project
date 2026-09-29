# Tests

Small, readable harnesses over the parts of this application where a fault is
silent — where the wrong answer looks exactly like the right one, so nothing
complains and nobody finds out until the figure is on a report.

They exist because they were written repeatedly and lost repeatedly: they
started as scratch files outside the repository, and a rebuilt machine took them
with it. Anything worth running twice belongs in here.

## Running them

```
composer install          # they need vendor/
php tests/run.php         # all of them, each in its own process
php tests/run.php region  # only the ones whose name matches
php tests/run.php --list  # name them without running them
```

Each file also runs on its own:

```
php tests/region_lists.php
```

Exit code 0 means nothing failed. `run.php` prints a count at the end and exits 1
if any harness failed.

## What each harness needs

Most of them only read the application's own source, and run anywhere PHP does.
Eleven stand the ORM up on a temporary SQLite file instead of touching a real
database, and need **pdo_sqlite**:

```
candidate_record_buttons  export_columns  index_filters  region_lists
render_add_payment  render_address_forms  render_currency_notes
related_records  render_edit_identity  shell_backfill_lpk
shell_installment_chain
```

The demo server's PHP does not have it, so those eleven report

```
  skipped: needs the pdo_sqlite extension, which this PHP does not have.
           On Debian or Ubuntu: apt install php7.4-sqlite3
```

and `run.php` counts them apart from both passes and failures — `27 harness(es),
16 ran` rather than a number that claims more was checked than was. Installing
the package is the whole of the fix; nothing in the application needs it, only
these tests.

A skip is not a pass, so the summary line says `nothing failed` rather than
`all good` whenever anything was skipped.

## What is here

| harness | what it holds to account |
| --- | --- |
| `region_lists.php` | Address dropdowns on forms and on index filter rows: every province, and below one only what belongs to it. The cap that used to offer 200 of 84,305 villages, and wiped a saved address on the next save. |
| `index_filters.php` | The filter row applied to the query rather than to the rows already on the page. Operators, ranges, a column the table does not have, and a value it cannot hold. |
| `export_columns.php` | Exports carrying the table's real columns instead of bake's four placeholders, foreign keys resolved to names, dates a spreadsheet can read, and a table wider than the alphabet. |
| `upload_guard.php` | What may be written into the web root. The extension allow list, the two base64 croppers, the second lock on disk, and the wizard no longer being open to anyone. |
| `datasource_guard.php` | The credential check at boot, including the empty password that used to boot the application and then fail on the first query. |
| `check_view_vars.php` | `bin/check-view-vars.php` over a tree whose answers are known: what counts as set, the five shapes that are assignments rather than missing variables, and the templates nothing renders. |
| `check_config.php` | What `bin/check-config.php` reports in each state the server has actually been in — and that it never prints a secret. |
| `browser/table_filter.js` | The filter row driven in Chromium: what it asks the server for, what it puts back in the boxes, and the fallback for a screen that does not paginate. |
| `render_guides.php` | Every page guide rendered, and one read closely so the renderer is known to put the data on the page rather than merely not fall over. |
| `render_filter_notice.php` | The banner that says a list has been narrowed, in each of its four states, including the one where it must print nothing. |
| `render_export_print.php` | The print view of an export: dates written the way the CSV writes them, a wide table, an empty list, and the template working no value out for itself. |
| `render_stakeholder_dashboard.php` | The dashboard's waiting panels: the expired link that needs sending again, an earned all-clear, and the two states it cannot establish. |
| `render_add_payment.php` | The add-payment screen and the warning that stops a payment being recorded twice — that it names the payment already on file, links to it, and still offers to record anyway. |
| `render_currency_notes.php` | Where a total says which currency it is written in and how many rows are not, and where a source record is held back rather than posted into books that cannot mean it. |
| `render_financial_report.php` | The income statement and the balance sheet: the period in force, dates the wrong way round, the one date a balance sheet takes, and what was left out of the figures. |
| `candidate_record_buttons.php` | The three buttons on a candidate's page: where they point now that the actions they named never existed, the candidate they carry, and a dropdown that contains the candidate it is set to without offering one the role may not see. |
| `shell_installment_chain.php` | The running figures rebuilt from the payments: a deleted payment, an overpayment, a trainee with no payments, running it twice, and never touching what was paid. |
| `shell_set_aside.php` | What stands between a duplicate table and a rename — what counts as "something still names this table", where three faults were found while it was written. |
| `shell_compare_duplicate.php` | The comparison that decides which copy is live, and the partial view that once made a real promotion read as an empty row. |
| `shell_backfill_trail.php` | Copying an old promotion into the decision trail — and every case it refuses rather than guessing a date, a sequence or a subject. |
| `shell_mcu_fitness.php` | Reading a medical result as pass or fail: the negative winning over a word it contains, a title it cannot read staying unread, and a person's own correction surviving. |
| `shell_column_adders.php` | The five column-adding shells, held to what none of them may do: never drop, never modify, nothing without `--apply`, and say so when there is nothing to do. |
| `shell_moves_and_creates.php` | The share table's creation skipping the canonical database rather than the connection name, and the template move refusing to overwrite a live template or to write anywhere but the copy the application reads. |
| `render_address_forms.php` | The address controls on the forms bake stamped them onto: one card, one control per region column, the options the controller really sets, and an id the cascade script binds to. |
| `related_records.php` | The rows behind a related-records tab: that every controller answers for its own table, that a column name out of the query string has to be a column before it reaches a query, and that the element sends and reads what the endpoint sends and reads. |
| `render_edit_identity.php` | The hidden id an edit form carries: written for a saved record, absent for a blank one, never able to move a primary key, and present on every form that replaces an uploaded file. |
| `shell_backfill_lpk.php` | Marking the LPKs that finished registering but were never recorded as having: where the date comes from, the earliest activation winning, and every value a date column here turns out to hold. |

## The check scripts beside them

`bin/` holds scripts that answer one question over the whole application at once,
where a harness would have to name every case. They print what they found and
exit non-zero when it is something:

| script | what it answers |
| --- | --- |
| `check-view-vars.php` | Which view variables does a template read that nothing sets, and which templates does nothing render? The quietest fault here: the page returns 200 and shows a blank or an empty dropdown. It found the address card on fourteen forms and the seventeen mis-named edit guards. Ask the second question first - a template behind a redirect-only action reports every variable it reads, and none of it matters. |
| `check-route-targets.php` | Does every link naming a controller point at a class that exists, and every link naming an action point at a method that exists? The second half is new: three buttons on every candidate's page named actions that had never been written. |
| `check-icons.php` | Does every icon name exist in the bundled Font Awesome? |
| `check-role-names.php` | Do the role names the code expects match the roles table? |
| `check-create-validation.php` | Does every table class reach its database, and do its rules match the columns? |
| `check-duplicate-tables.php` | Does a table name appear in more than one database? |
| `check-config.php` | What is configured on this machine — credentials, salt, mail, ImageResize — without printing a secret. |

## What `TestCase/` and `Fixture/` are

Not these. They came with the application skeleton: 351 files under
`tests/TestCase/`, of which 320 call `markTestIncomplete()` and assert nothing,
and a fixture per table. PHPUnit is not in `composer.json`, so none of it has
ever run. It is scaffolding, not coverage, and `run.php` leaves it alone.

## Writing another

- `require __DIR__ . '/lib/harness.php';` first. It gives `check()`,
  `checkTrue()`, `sqliteConnections()`, `reachInto()`, `giveRequest()` and
  `finish()`, and configures the application enough to be asked questions.
- Point the ORM at SQLite with `sqliteConnections()` and create only the tables
  the case needs. Do not touch a real database.
- Reach protected methods with `reachInto()`. Building a controller pulls in
  components, a request and a database; most rules here need none of them.
- Write the label as the claim a reader can check — "a saved kelurahan outside
  the chosen kecamatan is kept", not "test 14". A failure should read as a
  sentence about the application, not about the test.
- Say in the docblock what went wrong once. A harness whose reason is written
  down survives a rewrite of the code it guards; one that only asserts does not.
- Keep to PHP 7.4 syntax, and watch for PHP 7 vs 8 *behaviour*, not only syntax.
  Two harnesses passed on 8.4 and failed on the server: `iterator_to_array()`
  refuses an array on 7.4 and accepts one on 8, so a fixture that stood in for a
  query was fine on one and a TypeError on the other. `run.php` prints the
  version it is running on and says so when that is not a version the
  application supports.
- Make a fixture the shape the controller really passes. A query is Traversable;
  an array is not, and the difference only shows on the older PHP.
- Where the framework warns about a fixture - text in a DATETIME column, which
  the hand-written schemas here really do contain - wrap the call in
  `withoutVendorWarnings()`. It hides warnings from `vendor/` and hands back any
  from `src/` or `config/`, which are never hidden.

## The browser harness

`tests/browser/` holds JavaScript, driven by Playwright. `run.php` names it as
skipped where node or playwright is missing rather than leaving it out of the
count:

```
npm install -g playwright
npx playwright install chromium
```

## The render harnesses

The four `render_*` files stand a `Cake\View\View` up on its own and render a
template or an element with an error handler attached. The point is that handler:
a template that reads a key which is not there renders anyway — PHP emits a
notice, the page comes out with a gap in it, and with debug off on the server
nobody sees either. So a render that warns from a file under `src/` or `config/`
is a failure, and the warning is quoted.

Warnings from `vendor/` are ignored on purpose. This application is CakePHP 3.9
on whatever PHP the machine has, and a newer PHP deprecates things inside the
framework that are none of a template's business.

`renderClean()` in the library does the whole of it: render, fail on a warning,
return the html to make claims about.

## The shell harnesses

A shell that changes data is the worst place for a silent fault: it runs once,
over everything, with nobody watching a screen. The three here cover the ones
where a mistake is either irreversible or invisible.

They test the judgement, not the console: `rebuildChain()` against rows written
straight into SQLite, and the set-aside's "does anything still name this table"
against a small tree of files the harness writes. Where the dangerous half is a
single statement — the rename — the harness reads the source and holds it to
what it must never do, rather than renaming something to find out.

## What a shell harness can and cannot reach

Where the judgement is a method, it is called: `rebuildChain()` against rows in
SQLite, `readTitle()` against the words people actually type, `lineFor()` against
an old promotion record, the set-aside's file scan against a tree the harness
writes.

Where the dangerous half is one statement — a `RENAME`, a `CREATE`, an `ALTER` —
the harness reads the source and holds it to what it must never do, rather than
renaming something to find out. That is weaker, and it is said plainly here
rather than dressed up: it catches a shell that grows a `DROP`, or loses its
`--apply` guard, or stops naming what it will change. It would not catch a
`RENAME` with the wrong table in it.

Every shell that writes data now has one. What is still uncovered is the shells
that only read and print: `ListTablesShell`, `ShowAssociationsShell`,
`CheckDataShell`, `MenuPermissionsShell`, `GenerateMasterGuideShell`. A fault in
one of those misleads a reader rather than changing a row, which is a real cost -
the duplicate-table comparer misled one - but a smaller one than the rest.
