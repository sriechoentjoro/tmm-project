#!/usr/bin/env php
<?php
/**
 * Ask one of this application's databases a read-only question.
 *
 * There was no way to look at the data without the mysql client, and the
 * mysql client needs an account and a password: /root/tmm-db-password.txt
 * turned out not to be the MySQL root password, so every invitation to "just
 * check" came back Access denied. The application already holds working
 * credentials and reaches these databases on every page load.
 *
 * bin/apply-migration.php runs a file of statements and can change things.
 * This one cannot: anything but SELECT, SHOW, DESCRIBE or EXPLAIN is refused
 * before a connection is opened, so it is safe to hand to somebody mid-
 * conversation without a second thought about what it might do.
 *
 * The connection name doubles as the database name in this application; a
 * name it does not know is refused with the list of the ones it does.
 *
 * Usage:
 *     php bin/query.php cms_tmm_trainees "SELECT COUNT(*) FROM trainees"
 *     php bin/query.php cms_tmm_trainees < question.sql
 *     php bin/query.php --list
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$arguments = array_slice($argv, 1);
$list = in_array('--list', $arguments, true);
$arguments = array_values(array_filter($arguments, function ($one) {
    return $one !== '--list';
}));

$connectionName = isset($arguments[0]) ? $arguments[0] : null;
$sql = isset($arguments[1]) ? $arguments[1] : null;

// The application's own bootstrap, for its credentials and connection names.
// It announces every deprecation in the framework on a newer PHP than this
// application runs on, which would bury the answer, so it is buffered; a
// bootstrap that dies takes the whole buffer to the screen with it.
$bootstrapped = false;
register_shutdown_function(function () use (&$bootstrapped) {
    if (!$bootstrapped && ob_get_level() > 0) {
        echo ob_get_clean();
    }
});
ob_start();
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';
$bootstrapped = true;
$said = ob_get_clean();
foreach (explode("\n", $said) as $line) {
    if (trim($line) !== '' && !preg_match('/^(PHP )?Deprecated|^Deprecated Error:|vendor\//', $line)) {
        echo $line . "\n";
    }
}

use Cake\Datasource\ConnectionManager;

$known = ConnectionManager::configured();
sort($known);

if ($list || $connectionName === null) {
    echo "Databases this application is configured for:\n\n";
    foreach ($known as $name) {
        $config = ConnectionManager::getConfig($name);
        printf("  %-40s %s\n", $name,
            isset($config['database']) ? $config['database'] : '(no database)');
    }
    echo "\nUsage: php bin/query.php <name> \"SELECT ...\"\n";
    exit($list ? 0 : 1);
}

if (!in_array($connectionName, $known, true)) {
    printf("No connection named %s. Known: %s\n", $connectionName, implode(', ', $known));
    exit(1);
}

if ($sql === null) {
    $sql = stream_get_contents(STDIN);
}
$sql = trim((string)$sql);
// A trailing semicolon is how anybody writing SQL ends a statement.
$sql = rtrim($sql, ";\n\r\t ");

if ($sql === '') {
    echo "Nothing to ask.\n";
    exit(1);
}

// Read-only, and decided before anything is opened. Statements are not split:
// one question at a time, so a second statement cannot ride in behind the
// first.
$bare = preg_replace('/^\s*(?:(?:--|#)[^\n]*\n|\/\*.*?\*\/)*\s*/s', '', $sql);
if (!preg_match('/^(SELECT|SHOW|DESCRIBE|DESC|EXPLAIN)\b/i', $bare)) {
    echo "Refused: this only asks questions.\n";
    echo "It runs SELECT, SHOW, DESCRIBE and EXPLAIN. For anything that\n";
    echo "changes the database, write a file and use bin/apply-migration.php,\n";
    echo "which prints what it will do before it does it.\n";
    exit(1);
}
if (strpos($bare, ';') !== false) {
    echo "Refused: one statement at a time.\n";
    exit(1);
}

$connection = ConnectionManager::get($connectionName);
$config = $connection->config();
printf("%s -> %s\n\n", $connectionName,
    isset($config['database']) ? $config['database'] : '(no database)');

try {
    $rows = $connection->execute($bare)->fetchAll('assoc');
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}

if (!$rows) {
    echo "(no rows)\n";
    exit(0);
}

// Widths from the data, so the columns line up without a library.
$columns = array_keys($rows[0]);
$width = [];
foreach ($columns as $column) {
    $width[$column] = strlen($column);
}
foreach ($rows as $row) {
    foreach ($columns as $column) {
        $width[$column] = max($width[$column], strlen((string)$row[$column]));
    }
}

$line = [];
foreach ($columns as $column) {
    $line[] = str_pad($column, $width[$column]);
}
echo implode('  ', $line) . "\n";
$line = [];
foreach ($columns as $column) {
    $line[] = str_repeat('-', $width[$column]);
}
echo implode('  ', $line) . "\n";
foreach ($rows as $row) {
    $line = [];
    foreach ($columns as $column) {
        $line[] = str_pad((string)$row[$column], $width[$column]);
    }
    echo implode('  ', $line) . "\n";
}
printf("\n%d row(s)\n", count($rows));
exit(0);
