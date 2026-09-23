#!/usr/bin/env php
<?php
/**
 * Find table names that exist in more than one database, and say which copy
 * the application actually reads.
 *
 * This system spreads its data over fourteen databases, and nothing stops the
 * same table name existing in two of them. When that happens one copy is live
 * and the other is a leftover - but both answer a query, so the only symptom
 * is a screen quietly showing the wrong rows, or an association that finds
 * nothing because its two tables ended up on opposite sides of a connection
 * boundary. CakePHP cannot join across connections, so that boundary is not a
 * detail: it decides whether a relationship works at all.
 *
 * apprentice_orders is in both cms_tmm_apprentices and cms_tmm_trainees, while
 * apprentice_order_shares - which points at it - is in cms_tmm_apprentices
 * alone. That is what this was written to surface, and to keep surfacing.
 *
 * Nothing is written. Every query here counts rows.
 *
 * Usage:
 *     php bin/check-duplicate-tables.php          report duplicates, exit 1 if any
 *     php bin/check-duplicate-tables.php --all    also list every table counted
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', array_slice($argv, 1), true);

// config/app_datasources.php stops the script with E_USER_ERROR when no
// credentials are configured, and E_USER_ERROR cannot be caught.
$hasLocal = is_file($root . '/config/app_local.php');
$hasEnv = getenv('TMM_DB_USERNAME') !== false && getenv('TMM_DB_PASSWORD') !== false;
if (!$hasLocal && !$hasEnv) {
    echo "No database credentials configured, so the databases cannot be read.\n";
    echo "This check has nothing to say without them.\n";
    exit(0);
}

require $root . '/vendor/autoload.php';
require $root . '/config/paths.php';

\Cake\Core\Configure::write('App', ['namespace' => 'App', 'encoding' => 'UTF-8', 'defaultLocale' => 'en_US']);
\Cake\Core\Configure::config('default', new \Cake\Core\Configure\Engine\PhpConfig());
\Cake\Core\Configure::load('app', 'default', false);
foreach ((array)\Cake\Core\Configure::consume('Datasources') as $name => $config) {
    \Cake\Datasource\ConnectionManager::setConfig($name, $config);
}
foreach (['_cake_model_', '_cake_core_'] as $cache) {
    if (!in_array($cache, \Cake\Cache\Cache::configured(), true)) {
        \Cake\Cache\Cache::setConfig($cache, ['className' => 'Array']);
    }
}

// 'default' and 'cms_masters' are deliberately the same database, so counting
// both would report every masters table as a duplicate of itself.
$seenDatabases = [];
$connections = [];
foreach (\Cake\Datasource\ConnectionManager::configured() as $name) {
    try {
        $connection = \Cake\Datasource\ConnectionManager::get($name);
        $config = $connection->config();
    } catch (\Throwable $e) {
        continue;
    }
    $database = isset($config['database']) ? $config['database'] : $name;
    if (isset($seenDatabases[$database])) {
        continue;
    }
    $seenDatabases[$database] = $name;
    $connections[$name] = $connection;
}
printf("%d distinct database(s) reachable\n", count($connections));

$where = [];   // table name => [connection => row count]
foreach ($connections as $name => $connection) {
    try {
        $tables = $connection->getSchemaCollection()->listTables();
    } catch (\Throwable $e) {
        printf("  could not list %s: %s\n", $name, substr($e->getMessage(), 0, 60));
        continue;
    }
    foreach ($tables as $table) {
        try {
            $rows = (int)$connection->execute('SELECT COUNT(*) FROM `' . $table . '`')->fetch()[0];
        } catch (\Throwable $e) {
            $rows = -1; // a view, or something that will not be counted
        }
        $where[$table][$name] = $rows;
    }
}

$duplicates = array_filter($where, function ($copies) {
    return count($copies) > 1;
});
ksort($duplicates);

printf("%d table name(s) seen, %d in more than one database\n",
    count($where), count($duplicates));

if ($showAll) {
    echo "\nEvery table counted:\n\n";
    foreach ($where as $table => $copies) {
        foreach ($copies as $name => $rows) {
            printf("  %-40s %-34s %s\n", $table, $name, $rows < 0 ? '(not counted)' : $rows);
        }
    }
}

if (!$duplicates) {
    echo "\nno table name appears in two databases\n";
    exit(0);
}

/**
 * Which connection the application's Table class for this table reads.
 *
 * Returns null when there is no Table class. That is not the same as nothing
 * reading the table: journal_details has no class and is queried by raw SQL
 * from two controllers, holding live accounting rows. An earlier version of
 * this script reported those twelve rows as read by nobody, which is exactly
 * the kind of confident wrong answer that gets data deleted.
 *
 * @return string|null
 */
function readsFrom($table)
{
    $alias = \Cake\Utility\Inflector::camelize($table);
    if (!is_file(APP . 'Model' . DS . 'Table' . DS . $alias . 'Table.php')) {
        return null;
    }
    try {
        return \Cake\ORM\TableRegistry::getTableLocator()->get($alias)->getConnection()->configName();
    } catch (\Throwable $e) {
        return '(could not be loaded)';
    }
}

/**
 * How many source files mention this table name at all.
 *
 * A crude grep, and deliberately so: it is the only way to notice a table
 * reached by raw SQL, which the Table classes know nothing about. Zero means
 * nothing in the application names it, which is worth knowing on its own.
 */
function mentionedIn($table)
{
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(APP, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        if (!in_array(strtolower($file->getExtension()), ['php', 'ctp'], true)) {
            continue;
        }
        if (strpos((string)file_get_contents($file->getPathname()), $table) !== false) {
            $count++;
        }
    }

    return $count;
}

echo "\nThe same table name in more than one database:\n\n";
$serious = 0;
$unknown = 0;
foreach ($duplicates as $table => $copies) {
    $reads = readsFrom($table);
    printf("  %s\n", $table);
    printf("      %-38s %-10s %s\n", 'database', 'rows', 'the Table class reads');
    foreach ($copies as $name => $rows) {
        printf("      %-38s %-10s %s\n", $name,
            $rows < 0 ? '(not counted)' : $rows,
            $reads !== null && $name === $reads ? 'yes' : '');
    }

    if ($reads === null) {
        // No class means no opinion. Saying "nothing reads this" here would be
        // a guess dressed as a finding.
        $mentions = mentionedIn($table);
        if ($mentions > 0) {
            printf("      ?  no Table class - but %d source file(s) name this table,\n", $mentions);
            printf("         so it is reached by raw SQL and this check cannot say which copy\n");
        } else {
            printf("      ?  no Table class and no source file names it - nothing in the\n");
            printf("         application appears to use either copy\n");
        }
        $unknown++;
        echo "\n";
        continue;
    }

    // The copy the class does not read, holding rows, is the dangerous shape:
    // somebody wrote them expecting them to be seen.
    foreach ($copies as $name => $rows) {
        if ($name !== $reads && $rows > 0) {
            printf("      <- %s holds %d row(s) the Table class does not read\n", $name, $rows);
            $serious++;
        }
    }
    echo "\n";
}

echo "One of each pair is a leftover, but both answer a query, so the only\n";
echo "symptom is a screen showing the wrong rows - or an association that finds\n";
echo "nothing, because CakePHP cannot join across connections and the two\n";
echo "tables ended up on opposite sides of that boundary.\n";
if ($serious) {
    echo "\nRows in a copy the Table class does not read are the ones to look at\n";
    echo "first: somebody entered them expecting them to be seen. Compare the two\n";
    echo "copies before moving or deleting anything - equal row counts do not mean\n";
    echo "equal rows.\n";
}
if ($unknown) {
    printf("\n%d of these have no Table class, so this check cannot say which copy\n", $unknown);
    echo "is live. Where source files name the table it is queried by raw SQL, and\n";
    echo "only reading that SQL will say which database it opens.\n";
}
exit(1);
