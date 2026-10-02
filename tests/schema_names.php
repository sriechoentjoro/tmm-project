<?php
/**
 * Column names written into templates, against the names the model uses.
 *
 * There is no migration set and no schema dump in this repository, so the only
 * machine-readable description of the schema is the @property list on each
 * entity - and three checks already lean on it: the related-record tabs, the
 * form fields, and the sort and filter columns on index screens.
 *
 * ApprenticeOrders is what happens when it drifts. The entity documented
 * job_category_id. The table's own belongsTo names master_job_category_id as
 * its foreign key, the validation rule checks that name, _accessible lists it,
 * both forms post it, and index() contains MasterJobCategories - which could
 * not load at all if the column were not there. The index page sorted and
 * filtered on the documented name instead, so the column header looked
 * sortable and did nothing, and the filter named a column the table does not
 * have.
 *
 * Reading one source cannot settle that; two of them disagreeing is the signal.
 * This holds the templates to both.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Utility\Inflector;

/**
 * The scalar columns an entity documents.
 *
 * @param string $entity Entity class name.
 * @return array
 */
function documents($entity)
{
    $file = TMM_ROOT . '/src/Model/Entity/' . $entity . '.php';
    if (!is_file($file)) {
        return [];
    }
    preg_match_all('/@property\s+(\S+)\s+\$([a-z_0-9]+)/',
        file_get_contents($file), $found, PREG_SET_ORDER);
    $columns = [];
    foreach ($found as $hit) {
        if (strpos($hit[1], 'App\\Model\\Entity') === false) {
            $columns[] = $hit[2];
        }
    }

    return $columns;
}

/**
 * The foreign keys a table class declares.
 *
 * @param string $alias Table alias.
 * @return array
 */
function declaredKeys($alias)
{
    $file = TMM_ROOT . '/src/Model/Table/' . $alias . 'Table.php';
    if (!is_file($file)) {
        return [];
    }
    preg_match_all("/'foreignKey'\s*=>\s*'([a-z_0-9]+)'/", file_get_contents($file), $found);

    return array_unique($found[1]);
}

echo "  what index screens sort and filter on\n";
$orphans = [];
$seen = 0;
foreach (glob(TMM_ROOT . '/src/Template/*/index.ctp') as $file) {
    $folder = basename(dirname($file));
    $entity = Inflector::singularize($folder);
    $columns = documents($entity);
    $keys = declaredKeys($folder);
    if (!$columns && !$keys) {
        continue;
    }
    $src = file_get_contents($file);
    preg_match_all("/Paginator->sort\('([a-z_0-9]+)'/", $src, $sorted);
    preg_match_all('/data-column="([a-z_0-9]+)"/', $src, $filtered);
    foreach (array_unique(array_merge($sorted[1], $filtered[1])) as $name) {
        if (substr($name, -3) !== '_id') {
            continue;   // only keys, where two sources can be compared
        }
        $seen++;
        if (in_array($name, $columns, true) || in_array($name, $keys, true)) {
            continue;
        }
        $orphans[] = $folder . '/index.ctp: ' . $name;
    }
}
checkTrue('there are some to check (' . $seen . ')', $seen > 100);
check('every key they name is one the model uses somewhere', $orphans, []);

echo "  the one that had drifted\n";
$index = file_get_contents(TMM_ROOT . '/src/Template/ApprenticeOrders/index.ctp');
checkTrue('the apprentice orders index sorts on the real column',
    strpos($index, "Paginator->sort('master_job_category_id')") !== false);
checkTrue('and filters on it', strpos($index, 'data-column="master_job_category_id"') !== false);
check('the name the entity had documented is gone from the template',
    preg_match('/(?<![_a-z])job_category_id/', $index), 0);
checkTrue('and it is the key the table declares',
    in_array('master_job_category_id', declaredKeys('ApprenticeOrders'), true));
checkTrue('which the entity now documents too',
    in_array('master_job_category_id', documents('ApprenticeOrder'), true));

echo "  the checker that settles the rest\n";
// It needs a database. What matters here is that it says so rather than
// reporting a clean run it never made: a check that exits 0 having checked
// nothing is worse than no check.
$script = escapeshellarg(TMM_ROOT . '/bin/check-entity-columns.php');
$lines = [];
$status = 0;
exec(escapeshellarg(PHP_BINARY) . ' ' . $script . ' 2>&1', $lines, $status);
$out = implode("\n", $lines);
checkTrue('it runs', $out !== '');
checkTrue('it names the entities that document no columns at all',
    strpos($out, 'document no columns at all') !== false);
checkTrue('and it does not pass when it could reach nothing',
    strpos($out, 'could not be reached') === false || $status !== 0);

echo "  entities with nothing to compare against\n";
$blank = [];
foreach (glob(TMM_ROOT . '/src/Model/Entity/*.php') as $file) {
    if (!documents(basename($file, '.php'))) {
        $blank[] = basename($file, '.php');
    }
}
// Recorded rather than demanded: filling these in needs the database, which is
// what bin/check-entity-columns.php is for. The count is here so that it is
// noticed when it grows.
checkTrue('there are ' . count($blank) . ' of them, and no more than there were',
    count($blank) <= 15);

finish();
