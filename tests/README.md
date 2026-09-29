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

## What is not here yet

The render harnesses — standalone `Cake\View\View` renders of the page guides,
the filter notice and the print export, each with an error handler that fails on
a warning from application code. They were lost with the scratch files and are
worth rebuilding.
