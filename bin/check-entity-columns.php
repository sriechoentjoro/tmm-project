#!/usr/bin/env php
<?php
/**
 * Does each entity's @property list match the table it stands for?
 *
 * Those docblocks are the only machine-readable description of this schema -
 * there is no migration set and no schema dump - so three checks already lean
 * on them: the related-record tabs, the form fields, and the sort and filter
 * columns on index screens. A docblock that has drifted does not merely fail to
 * help; it sends the reader, and those checks, the wrong way.
 *
 * It has happened:
 *
 *   ApprenticeOrder documented job_category_id. The table's own belongsTo names
 *       master_job_category_id as its foreign key, the validation rule checks
 *       that name, _accessible lists it, and both forms post it - and the index
 *       page sorted and filtered on the documented name, which is not a column,
 *       so the header did nothing and the filter matched nothing.
 *   User documents no status at all, though UsersController::index() counts
 *       rows by it in raw SQL and the LPK registration writes it.
 *   CandidateSubmissionDocument documents no id.
 *
 * Reading the source cannot settle any of these: it can only say that two
 * places disagree. The database can, so this asks it.
 *
 * Nothing is written. It reads the schema and compares names.
 *
 * Usage:
 *     php bin/check-entity-columns.php          report drift, exit 1 if any
 *     php bin/check-entity-columns.php --all    also list every entity checked
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', array_slice($argv, 1), true);

/**
 * The scalar properties an entity documents.
 *
 * A property typed as another entity is an association, not a column, and is
 * left out; so is anything the class holds that is not a @property at all.
 *
 * @param string $file Entity source file.
 * @return array
 */
function documentedColumns($file)
{
    preg_match_all('/@property\s+(\S+)\s+\$([a-z_0-9]+)/',
        file_get_contents($file), $found, PREG_SET_ORDER);
    $columns = [];
    foreach ($found as $hit) {
        if (strpos($hit[1], 'App\\Model\\Entity') !== false) {
            continue;
        }
        $columns[] = $hit[2];
    }

    return array_values(array_unique($columns));
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

$drift = [];
$unreachable = [];
$undocumented = [];
$fine = 0;

foreach (glob($root . '/src/Model/Entity/*.php') as $file) {
    $entity = basename($file, '.php');
    $alias = \Cake\Utility\Inflector::pluralize($entity);
    $documented = documentedColumns($file);

    if (!$documented) {
        // Nothing to compare against. Worth naming: a check that reads these
        // docblocks learns nothing from this entity and will say so by
        // accident, as a finding, rather than on purpose.
        $undocumented[] = $entity;
        continue;
    }

    try {
        $table = $locator->get($alias);
        $real = $table->getSchema()->columns();
    } catch (\Throwable $e) {
        $unreachable[$entity] = $e->getMessage();
        continue;
    }

    $missing = array_values(array_diff($real, $documented));
    $phantom = array_values(array_diff($documented, $real));
    if (!$missing && !$phantom) {
        $fine++;
        if ($showAll) {
            printf("  ok   %-44s %d column(s)\n", $entity, count($real));
        }
        continue;
    }
    $drift[$entity] = ['table' => $table->getTable(),
        'missing' => $missing, 'phantom' => $phantom];
}

foreach ($drift as $entity => $what) {
    printf("\n%s (%s)\n", $entity, $what['table']);
    if ($what['phantom']) {
        printf("    documented, but not a column: %s\n", implode(', ', $what['phantom']));
    }
    if ($what['missing']) {
        printf("    a column, but not documented: %s\n", implode(', ', $what['missing']));
    }
}

printf("\n%d entit(y|ies) match their table\n", $fine);
if ($undocumented) {
    printf("%d document no columns at all, so nothing could be compared: %s\n",
        count($undocumented), implode(', ', $undocumented));
}
if ($unreachable) {
    printf("%d could not be reached: %s\n", count($unreachable),
        implode(', ', array_slice(array_keys($unreachable), 0, 8))
        . (count($unreachable) > 8 ? ', ...' : ''));
    printf("    first reason: %s\n", reset($unreachable));
}
if (!$drift) {
    echo "no entity documents a column its table does not have\n";
    exit($unreachable || $undocumented ? 1 : 0);
}
printf("%d entit(y|ies) have drifted from their table\n", count($drift));
exit(1);
