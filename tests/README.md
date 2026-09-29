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

Exit code 0 means every check passed. `run.php` prints a count at the end and
exits 1 if any harness failed.

## What is here

| harness | what it holds to account |
| --- | --- |
| `region_lists.php` | Address dropdowns on forms and on index filter rows: every province, and below one only what belongs to it. The cap that used to offer 200 of 84,305 villages, and wiped a saved address on the next save. |
| `index_filters.php` | The filter row applied to the query rather than to the rows already on the page. Operators, ranges, a column the table does not have, and a value it cannot hold. |
| `export_columns.php` | Exports carrying the table's real columns instead of bake's four placeholders, foreign keys resolved to names, dates a spreadsheet can read, and a table wider than the alphabet. |
| `upload_guard.php` | What may be written into the web root. The extension allow list, the two base64 croppers, the second lock on disk, and the wizard no longer being open to anyone. |
| `datasource_guard.php` | The credential check at boot, including the empty password that used to boot the application and then fail on the first query. |
| `check_config.php` | What `bin/check-config.php` reports in each state the server has actually been in — and that it never prints a secret. |
| `browser/table_filter.js` | The filter row driven in Chromium: what it asks the server for, what it puts back in the boxes, and the fallback for a screen that does not paginate. |
| `render_guides.php` | Every page guide rendered, and one read closely so the renderer is known to put the data on the page rather than merely not fall over. |
| `render_filter_notice.php` | The banner that says a list has been narrowed, in each of its four states, including the one where it must print nothing. |
| `render_export_print.php` | The print view of an export: dates written the way the CSV writes them, a wide table, an empty list, and the template working no value out for itself. |
| `render_stakeholder_dashboard.php` | The dashboard's waiting panels: the expired link that needs sending again, an earned all-clear, and the two states it cannot establish. |
| `render_add_payment.php` | The add-payment screen and the warning that stops a payment being recorded twice — that it names the payment already on file, links to it, and still offers to record anyway. |
| `render_currency_notes.php` | Where a total says which currency it is written in and how many rows are not, and where a source record is held back rather than posted into books that cannot mean it. |
| `render_financial_report.php` | The income statement and the balance sheet: the period in force, dates the wrong way round, the one date a balance sheet takes, and what was left out of the figures. |
| `shell_installment_chain.php` | The running figures rebuilt from the payments: a deleted payment, an overpayment, a trainee with no payments, running it twice, and never touching what was paid. |
| `shell_set_aside.php` | What stands between a duplicate table and a rename — what counts as "something still names this table", where three faults were found while it was written. |
| `shell_compare_duplicate.php` | The comparison that decides which copy is live, and the partial view that once made a real promotion read as an empty row. |
| `shell_backfill_trail.php` | Copying an old promotion into the decision trail — and every case it refuses rather than guessing a date, a sequence or a subject. |
| `shell_mcu_fitness.php` | Reading a medical result as pass or fail: the negative winning over a word it contains, a title it cannot read staying unread, and a person's own correction surviving. |
| `shell_column_adders.php` | The five column-adding shells, held to what none of them may do: never drop, never modify, nothing without `--apply`, and say so when there is nothing to do. |
| `shell_moves_and_creates.php` | The share table's creation skipping the canonical database rather than the connection name, and the template move refusing to overwrite a live template or to write anywhere but the copy the application reads. |

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
- Keep to PHP 7.4 syntax. That is what the server runs, and these should run
  there too.

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

`BackfillLpkRegistrationShell` is the one shell still uncovered. It reads
activation times out of several places and writes `is_registered` and
`registered_at`; the reading is worth a harness and does not have one.
