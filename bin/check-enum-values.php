#!/usr/bin/env php
<?php
/**
 * Find values a table's validator allows that its ENUM column cannot hold.
 *
 * The resend-verification button failed in exactly this way. Its table said
 *
 *     ->inList('token_type', ['email_verification', 'user_verification', ...])
 *
 * while the column was ENUM('email_verification', 'password_reset'). The
 * validator passed, the save reached MySQL, MySQL refused the value, the
 * exception was caught and turned into false, and the screen said "please try
 * again" - which was never going to work. Nothing in the application pointed at
 * the column: the only place the two lists could be compared was here.
 *
 * The test harnesses could not catch it either. They build their fixtures in
 * sqlite, which has no ENUM and stores whatever it is given, so a fixture
 * written as token_type VARCHAR(50) accepts a value the real column rejects -
 * the fixture asserts the application's assumption instead of the database's.
 *
 * This reads both sides out of the repository: the inList rules from
 * src/Model/Table, and the ENUM definitions from the .sql files. It needs no
 * database, so it runs anywhere, including before a migration is applied.
 *
 * What it reports:
 *   - a value the validator allows and the column does not. This is the fault
 *     above: saving it fails at the database. Exit code 1.
 *   - a value the column allows and the validator refuses. Usually deliberate -
 *     an old value being retired - so it is listed and does not fail.
 *   - a validator whose column this repository never defines as an ENUM. It
 *     cannot be checked from here; say so rather than imply it is clean.
 *
 * The .sql files are the deployment's history, not the live schema, so a clean
 * run means the repository agrees with itself. bin/check-entity-columns.php
 * reads the real database.
 *
 * Usage:
 *     php bin/check-enum-values.php          report problems, exit 1 if any
 *     php bin/check-enum-values.php --all    also list every pair resolved
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', $argv, true);

/**
 * Split the inside of an ENUM(...) or a PHP array into its quoted strings.
 *
 * @param string $text What stood between the brackets.
 * @return array The values, in order, unquoted.
 */
function quotedValues($text)
{
    if (!preg_match_all('/([\'"])((?:\\\\.|(?!\1).)*)\1/s', $text, $found, PREG_SET_ORDER)) {
        return [];
    }
    $values = [];
    foreach ($found as $one) {
        $values[] = stripcslashes($one[2]);
    }

    return $values;
}

/**
 * Every ENUM column this repository defines, as table.column => values.
 *
 * Both shapes count: a column inside CREATE TABLE, and one named by
 * ALTER TABLE ... MODIFY/CHANGE/ADD COLUMN. A later definition wins, because
 * that is what applying them in order does.
 *
 * @param string $root The repository root.
 * @return array [table.column => ['values' => [...], 'where' => 'file:line']]
 */
function enumColumns($root)
{
    $found = [];
    $files = [];
    $walk = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($walk as $file) {
        $path = $file->getPathname();
        if (substr($path, -4) !== '.sql'
            || strpos($path, '/vendor/') !== false
            || strpos($path, '/tmp/') !== false
            || strpos($path, '/.git/') !== false) {
            continue;
        }
        $files[] = $path;
    }
    sort($files);

    foreach ($files as $path) {
        $rel = ltrim(str_replace($root, '', $path), '/');
        $table = null;
        foreach (file($path) as $n => $line) {
            $bare = trim($line);
            if ($bare === '' || substr($bare, 0, 2) === '--') {
                continue;
            }
            if (preg_match('/\b(?:CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?|ALTER\s+TABLE)\s+`?(\w+)`?/i',
                $bare, $m)) {
                $table = strtolower($m[1]);
            }
            if ($table === null) {
                continue;
            }
            // A column line: optional MODIFY/CHANGE/ADD, then the name, then
            // ENUM(...). CHANGE names the old column first and the new one
            // second; the second is the one that ends up in the schema.
            if (!preg_match('/^(?:(MODIFY|CHANGE|ADD)\s+(?:COLUMN\s+)?)?`?(\w+)`?\s+(?:`?(\w+)`?\s+)?ENUM\s*\((.*?)\)/i',
                $bare, $m)) {
                continue;
            }
            $column = strtolower(strcasecmp((string)$m[1], 'CHANGE') === 0 && $m[3] !== ''
                ? $m[3] : $m[2]);
            $values = quotedValues($m[4]);
            if (!$values) {
                continue;
            }
            $found[$table . '.' . $column] = [
                'values' => $values,
                'where' => $rel . ':' . ($n + 1),
            ];
        }
    }

    return $found;
}

/**
 * Every inList rule in the table classes, with the table it belongs to.
 *
 * The rule is often written across several lines, so the file is read whole
 * and the line number worked out from the offset.
 *
 * @param string $root The repository root.
 * @return array A list of [table, column, values, where].
 */
function inListRules($root)
{
    $rules = [];
    foreach (glob($root . '/src/Model/Table/*Table.php') as $path) {
        $rel = ltrim(str_replace($root, '', $path), '/');
        $php = file_get_contents($path);

        // setTable() is explicit in these classes; fall back to the
        // conventional name when it is not.
        if (preg_match('/setTable\(\s*[\'"](\w+)[\'"]/', $php, $m)) {
            $table = strtolower($m[1]);
        } else {
            $table = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2',
                basename($path, 'Table.php')));
        }

        if (!preg_match_all('/->inList\(\s*[\'"](\w+)[\'"]\s*,\s*\[(.*?)\]/s',
            $php, $found, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            continue;
        }
        foreach ($found as $one) {
            $values = quotedValues($one[2][0]);
            if (!$values) {
                continue;
            }
            $line = substr_count(substr($php, 0, $one[0][1]), "\n") + 1;
            $rules[] = [
                'table' => $table,
                'column' => strtolower($one[1][0]),
                'values' => $values,
                'where' => $rel . ':' . $line,
            ];
        }
    }

    return $rules;
}

$columns = enumColumns($root);
$rules = inListRules($root);

$refused = [];
$extra = [];
$unknown = [];

foreach ($rules as $rule) {
    $key = $rule['table'] . '.' . $rule['column'];
    if (!isset($columns[$key])) {
        $unknown[] = $rule;
        continue;
    }
    $column = $columns[$key];
    $tooMany = array_values(array_diff($rule['values'], $column['values']));
    $tooFew = array_values(array_diff($column['values'], $rule['values']));
    if ($tooMany) {
        $refused[] = [$rule, $column, $tooMany];
    }
    if ($tooFew) {
        $extra[] = [$rule, $column, $tooFew];
    }
    if ($showAll && !$tooMany && !$tooFew) {
        printf("  %s\n    %s\n    both allow: %s\n\n", $key, $rule['where'],
            implode(', ', $rule['values']));
    }
}

printf(
    "%d inList rule(s) in %d table class(es), %d ENUM column(s) defined in SQL\n",
    count($rules),
    count(glob($root . '/src/Model/Table/*Table.php')),
    count($columns)
);

if ($unknown) {
    printf("\n%d rule(s) on a column this repository never defines as an ENUM,\n"
        . "so nothing here can check them:\n\n", count($unknown));
    foreach ($unknown as $rule) {
        printf("  %s.%s\n    %s\n", $rule['table'], $rule['column'], $rule['where']);
    }
    echo "\n";
}

if ($extra) {
    printf("%d column(s) that hold a value the validator will not accept.\n"
        . "Usually deliberate - a value being retired - so this is a note, not a fault:\n\n",
        count($extra));
    foreach ($extra as list($rule, $column, $tooFew)) {
        printf("  %s.%s\n    column %s allows: %s\n    validator %s does not\n\n",
            $rule['table'], $rule['column'], $column['where'],
            implode(', ', $tooFew), $rule['where']);
    }
}

if (!$refused) {
    echo "every value a validator allows, its ENUM column can hold\n";
    exit(0);
}

printf("\n%d value(s) a validator allows that the column cannot hold:\n\n", count($refused));
foreach ($refused as list($rule, $column, $tooMany)) {
    printf("  %s.%s\n    validator %s allows: %s\n    column %s does not\n\n",
        $rule['table'], $rule['column'], $rule['where'],
        implode(', ', $tooMany), $column['where']);
}
echo "Saving one of these passes validation and then fails at the database.\n";
echo "Either widen the column with a migration, or stop allowing the value.\n";
exit(1);
