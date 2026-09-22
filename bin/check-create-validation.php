#!/usr/bin/env php
<?php
/**
 * Find tables whose validation rejects every create, whatever the form sends.
 *
 * A validator that requires a field the database supplies itself can never be
 * satisfied: no form sends an auto-increment primary key, so requiring one on
 * create means save() returns false for every add - and it returns false the
 * way save() always does, silently, with the reason tucked inside the entity
 * where the controller's generic "could not be saved" never shows it. The edit
 * screen for the same table keeps working, because an existing entity is
 * validated in the update context instead, which is why nobody suspects the
 * validator.
 *
 * TraineeInstallments had exactly this. The rule came from bake, and the same
 * line appears on other tables, so this asks the question of all of them at
 * once, by running the real validator rather than by reading the source: it
 * builds an empty entity through each table and reports the ones that come
 * back complaining about a column the database fills in.
 *
 * Nothing is written. newEntity() validates and never touches the database.
 *
 * Usage:
 *     php bin/check-create-validation.php          report problems, exit 1 if any
 *     php bin/check-create-validation.php --all    also list every table checked
 *
 * Without database credentials it falls back to reading the source, which
 * finds the same rule but cannot confirm the column is auto-increment.
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', array_slice($argv, 1), true);

$files = glob($root . '/src/Model/Table/*Table.php');
sort($files);
printf("%d table class(es) found\n", count($files));

// ---- what the source says, which works without a database ---------------
$suspectSource = [];
foreach ($files as $file) {
    $name = basename($file, 'Table.php');
    $body = (string)file_get_contents($file);
    if (preg_match("/requirePresence\(\s*'id'\s*,\s*'create'\s*\)/", $body)) {
        $suspectSource[$name] = true;
    }
}

// config/app_datasources.php stops the whole script with E_USER_ERROR when no
// credentials are configured, and E_USER_ERROR cannot be caught, so they are
// checked here rather than loading the config and hoping.
$hasLocal = is_file($root . '/config/app_local.php');
$hasEnv = getenv('TMM_DB_USERNAME') !== false && getenv('TMM_DB_PASSWORD') !== false;
if (!$hasLocal && !$hasEnv) {
    echo "\nNo database credentials configured, so the validators cannot be run.\n";
    if (!$suspectSource) {
        echo "No table requires 'id' on create in its source either.\n";
        exit(0);
    }
    printf("\n%d table(s) require 'id' on create, read from the source:\n\n", count($suspectSource));
    foreach (array_keys($suspectSource) as $name) {
        printf("  %s\n", $name);
    }
    echo "\nRun this where the database is reachable to confirm the column is\n";
    echo "auto-increment and that the validator really rejects the create.\n";
    exit(1);
}

require $root . '/vendor/autoload.php';
// config/app.php reads CORE_PATH and CONFIG, which only the application's own
// path definitions provide.
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

$locator = \Cake\ORM\TableRegistry::getTableLocator();

$broken = [];      // the create can never succeed
$awkward = [];     // required on create, but the form could at least send it
$unreachable = []; // no table behind the class, so nothing to say
$fine = [];

foreach ($files as $file) {
    $alias = basename($file, 'Table.php');

    // Which database a class reads is worth recording even when it works,
    // because "could not be reached" on its own says nothing about whether
    // the table is missing or the class is looking in the wrong place.
    // Cleared every pass: left over from the previous table, these would
    // report the last class that worked instead of this one that did not.
    $connection = '?';
    $physical = '?';
    $table = null;
    try {
        $table = $locator->get($alias);
        $connection = $table->getConnection()->configName();
        $physical = $table->getTable();
        $schema = $table->getSchema();
        $primary = (array)$table->getPrimaryKey();
    } catch (\Throwable $e) {
        $unreachable[$alias] = [
            'connection' => $connection,
            'table' => $physical,
            'why' => $e->getMessage(),
        ];
        continue;
    }

    // Columns the database fills in by itself. A validator must never require
    // one of these on create: no form has them to send.
    $supplied = [];
    foreach ($primary as $column) {
        $definition = $schema->getColumn($column);
        if ($definition && !empty($definition['autoIncrement'])) {
            $supplied[$column] = 'auto-increment primary key';
        }
    }

    // Run the real validator. newEntity() validates and writes nothing.
    try {
        $entity = $table->newEntity([]);
        $errors = $entity->getErrors();
    } catch (\Throwable $e) {
        $unreachable[$alias] = $e->getMessage();
        continue;
    }

    $required = [];
    foreach ($errors as $field => $messages) {
        if (is_array($messages) && array_key_exists('_required', $messages)) {
            $required[] = $field;
        }
    }

    $impossible = array_values(array_intersect($required, array_keys($supplied)));
    if ($impossible) {
        $broken[$alias] = ['fields' => $impossible, 'why' => $supplied, 'required' => $required];
        continue;
    }
    if ($required) {
        $awkward[$alias] = $required;
        continue;
    }
    $fine[] = $alias;
}

$checked = count($broken) + count($awkward) + count($fine);
printf("%d checked against the database, %d could not be reached\n",
    $checked, count($unreachable));

// A check that examined nothing must not report nothing wrong. Without this
// an unreachable database reads exactly like a clean bill of health, which is
// the failure this whole tool exists to catch.
if ($checked === 0) {
    echo "\nNothing could be checked - no table class reached its database.\n";
    if ($unreachable) {
        $first = reset($unreachable);
        printf("First reason given: %s\n", substr(str_replace("\n", ' ', $first['why']), 0, 160));
        if ($showAll) {
            echo "\n";
            printf("  %-32s %-34s %s\n", 'class', 'looked in', 'for table');
            foreach ($unreachable as $alias => $info) {
                printf("  %-32s %-34s %s\n", $alias, $info['connection'], $info['table']);
            }
        } else {
            echo "Run with --all to see which class looked in which database.\n";
        }
    }
    if ($suspectSource) {
        printf("\nFrom the source alone, %d table(s) require 'id' on create:\n\n",
            count($suspectSource));
        foreach (array_keys($suspectSource) as $name) {
            printf("  %s\n", $name);
        }
    }
    exit(1);
}

if ($showAll) {
    echo "\nRequired on create, but a form can supply them:\n\n";
    foreach ($awkward as $alias => $fields) {
        printf("  %-38s %s\n", $alias, implode(', ', $fields));
    }
    if ($unreachable) {
        echo "\nNo table behind the class:\n\n";
        printf("  %-32s %-34s %s\n", 'class', 'looked in', 'for table');
        foreach ($unreachable as $alias => $info) {
            printf("  %-32s %-34s %s\n", $alias, $info['connection'], $info['table']);
        }
        echo "\nA class with no defaultConnectionName() reads the 'default' connection.\n";
        echo "Where the only code using it passes a connectionName to loadModel(),\n";
        echo "the class works there and nowhere else - anything reaching for it\n";
        echo "through the table locator gets the wrong database and no warning.\n";
    }
}

if (!$broken) {
    printf("\nno table requires a column the database supplies itself (%d checked)\n", $checked);
    if ($unreachable) {
        printf("%d class(es) were skipped, so this is not a statement about them.\n",
            count($unreachable));
    }
    exit(0);
}

printf("\n%d table(s) cannot be created through any form:\n\n", count($broken));
printf("  %-38s %-22s %s\n", 'table', 'required but impossible', 'also required on create');
foreach ($broken as $alias => $info) {
    printf("  %-38s %-22s %s\n",
        $alias,
        implode(', ', $info['fields']),
        implode(', ', array_diff($info['required'], $info['fields'])) ?: '-');
}

echo "\nEach of these has a validator requiring a column the database fills in\n";
echo "itself, so save() refuses every create and the screen reports only that\n";
echo "the record could not be saved. The edit screen is unaffected: an existing\n";
echo "entity is validated in the update context, where the rule does not apply.\n";
echo "\nThe fix is one line per table - allowEmptyString in place of\n";
echo "requirePresence - but confirm the add screen is actually wanted first:\n";
echo "a table nobody adds to through a form loses nothing by staying as it is.\n";
exit(1);
