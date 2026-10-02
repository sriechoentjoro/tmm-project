#!/usr/bin/env php
<?php
/**
 * Apply one .sql migration through the application's own database connection.
 *
 * The migrations in this repository are applied by hand with the mysql client,
 * which needs a password on the command line and an account that has one. That
 * went wrong in the obvious way: /root/tmm-db-password.txt is not the MySQL
 * root password, so
 *
 *     mysql -u root -p"$(cat /root/tmm-db-password.txt)" < migration.sql
 *
 * came back "Access denied", changed nothing, and the screen that needed the
 * migration went on asking for it. The application already holds working
 * credentials in config/app_local.php, and it reaches the database every time
 * anybody opens a page, so there is no reason to type a password at all.
 *
 * It prints the statements and stops. Nothing runs without --go.
 *
 * A USE statement is read, not executed: the connection decides which database
 * is written to, and if the file names a different one that is a mistake worth
 * stopping for, not a line to skip quietly.
 *
 * This is for the hand-written files in database/migrations and the repository
 * root. config/Migrations is Phinx's and is not touched here.
 *
 * Usage:
 *     php bin/apply-migration.php <file.sql>        show what it would run
 *     php bin/apply-migration.php <file.sql> --go   run it
 *     ... --connection=name                         override the connection
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$arguments = array_slice($argv, 1);
$go = false;
$connectionName = null;
$path = null;
foreach ($arguments as $argument) {
    if ($argument === '--go') {
        $go = true;
    } elseif (strpos($argument, '--connection=') === 0) {
        $connectionName = substr($argument, 13);
    } elseif ($argument[0] !== '-') {
        $path = $argument;
    }
}

if ($path === null) {
    exit("Usage: php bin/apply-migration.php <file.sql> [--go] [--connection=name]\n");
}
if (!is_file($path)) {
    exit("No such file: $path\n");
}

$sql = file_get_contents($path);

// Strip comments. Line comments only - these files have no /* */ blocks, and
// guessing at one would risk cutting a statement in half.
$body = preg_replace('/^\s*(--|#).*$/m', '', $sql);

// The database the file says it is for.
$named = null;
if (preg_match('/\bUSE\s+`?(\w+)`?\s*;/i', $body, $m)) {
    $named = $m[1];
}
$body = preg_replace('/\bUSE\s+`?\w+`?\s*;/i', '', $body);

$statements = [];
foreach (explode(';', $body) as $one) {
    $one = trim($one);
    if ($one !== '') {
        $statements[] = $one;
    }
}

if (!$statements) {
    exit("Nothing to run in $path\n");
}

// The application's own bootstrap, so the credentials, the connection names
// and the database names are the ones it really uses - not a second copy of
// them here that can drift.
//
// Loading it prints nothing on the PHP this application runs on. On a newer
// one, the framework it ships announces every deprecation in its own code
// before anything of ours runs, which would bury the statements this script
// exists to show. So it is buffered: lines that are only that noise are
// dropped, anything else is printed, and a bootstrap that dies takes the whole
// buffer to the screen with it.
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
$kept = [];
foreach (explode("\n", $said) as $line) {
    if (trim($line) !== '' && !preg_match('/^(PHP )?Deprecated|^Deprecated Error:|vendor\//', $line)) {
        $kept[] = $line;
    }
}
if ($kept) {
    echo implode("\n", $kept) . "\n";
}

use Cake\Datasource\ConnectionManager;

if ($connectionName === null) {
    $connectionName = $named ?: 'default';
}

try {
    $connection = ConnectionManager::get($connectionName);
} catch (\Exception $e) {
    exit("No connection named $connectionName: " . $e->getMessage() . "\n");
}

$database = $connection->config()['database'];
printf("%s\n  connection %s -> database %s\n", $path, $connectionName, $database);

if ($named !== null && $named !== $database) {
    printf("\nThe file says USE %s, and this connection writes to %s.\n", $named, $database);
    echo "Refusing: one of the two is wrong, and guessing which would apply a\n";
    echo "migration to a database it was not written for.\n";
    exit(1);
}

printf("\n%d statement(s):\n\n", count($statements));
foreach ($statements as $n => $one) {
    printf("  %d. %s\n\n", $n + 1, preg_replace('/\s+/', ' ', $one));
}

if (!$go) {
    echo "Nothing was run. Add --go to apply it.\n";
    exit(0);
}

foreach ($statements as $n => $one) {
    printf("running %d ... ", $n + 1);
    try {
        $result = $connection->execute($one);
    } catch (\Exception $e) {
        echo "failed\n\n" . $e->getMessage() . "\n\n";
        echo "Nothing after this statement was run.\n";
        // ALTER cannot be rolled back in MySQL, so whatever ran before this
        // has taken effect. Say that rather than imply the file was undone.
        if ($n > 0) {
            printf("The %d statement(s) before it did run.\n", $n);
        }
        exit(1);
    }
    // A SELECT at the end of these files is there to show the result.
    $rows = preg_match('/^\s*(SELECT|SHOW|DESCRIBE)\b/i', $one) ? $result->fetchAll('assoc') : [];
    if ($rows) {
        echo "ok\n";
        foreach ($rows as $row) {
            echo '    ' . implode(' | ', array_map('strval', $row)) . "\n";
        }
    } else {
        echo "ok\n";
    }
}

echo "\napplied\n";
exit(0);
