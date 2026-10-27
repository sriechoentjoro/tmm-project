<?php
/**
 * Column names written into templates, against the names the model uses.
 *
 * This leans on the @property list on each entity, as three checks do: the
 * related-record tabs, the form fields, and the sort and filter columns on
 * index screens.
 *
 * That was once the only machine-readable description of the schema here, and
 * this file used to say so. It is not: database_exports holds dumps written by
 * the databases themselves. The difference matters, because every check that
 * reads only the application's own account of the schema agrees with the
 * application - including where it is wrong. The whole application called
 * special_skill_support_institutions.name "company_name" and nothing could
 * see it, because every source anybody read said company_name.
 * bin/check-schema-names.php reads the dumps instead.
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

echo "  a column header that looks sortable and is not\n";
// Thirteen index screens sorted on Candidates.fullname / Trainees.fullname.
// Neither table has a fullname column - it is name, and the cell underneath
// the header rendered $x->candidate->name all along. CakePHP drops a sort on
// a field it cannot place without saying anything, so the header was
// clickable and did nothing. The same ghost as the six session reads of
// 'fullname' fixed earlier; it came back by copy-paste, so this is the guard
// against the next copy.
$screens = glob(TMM_ROOT . '/src/Template/*/index.ctp');
checkTrue('there are index screens to read', count($screens) > 50);

$ghosts = [];
$named = [];
foreach ($screens as $path) {
    $body = file_get_contents($path);
    if (preg_match_all('/(?:Paginator->sort\(|data-column=")\s*.([\w.]*fullname)/', $body, $hit)) {
        foreach ($hit[1] as $one) {
            $ghosts[] = basename(dirname($path)) . ' -> ' . $one;
        }
    }
    if (strpos($body, "Paginator->sort('Candidates.name', 'Candidate')") !== false
        || strpos($body, "Paginator->sort('Trainees.name', 'Trainee')") !== false) {
        $named[] = basename(dirname($path));
    }
}
check('no index screen sorts or filters on fullname: ' . implode(', ', $ghosts),
    count($ghosts), 0);
checkTrue('the thirteen that did now name the real column, and all 13 are there: '
    . count($named), count($named) === 13);

// And the real column is real, read from the dumps the databases wrote - not
// from the application's own account of itself, which is how fullname lived
// this long.
foreach (['candidates', 'trainees'] as $table) {
    $columns = schemaColumns($table);
    checkTrue($table . ' has a name column', in_array('name', $columns, true));
    checkTrue('and no fullname column for the header to have meant',
        !in_array('fullname', $columns, true));
}

echo "  one table name, several databases\n";
// There are fourteen databases here and seven table names that appear in more
// than one of them with different columns. chart_of_accounts is code/title in
// asahi_accountings and account_code/account_name/account_type in
// asahi_inventories, and the model opens a third database that no SQL here
// describes. Merged into one list, that is the union of two different tables
// that happen to share a name: it accused the screen of sorting on a column
// it may well have, and it passed ChartOfAccounts.code because the other
// database has a code. A checker that cannot tell must say so.
$lines = [];
$status = 0;
exec(escapeshellarg(PHP_BINARY) . ' '
    . escapeshellarg(TMM_ROOT . '/bin/check-schema-names.php') . ' 2>&1', $lines, $status);
$out = implode("\n", $lines);

checkTrue('the schema checker runs', $out !== '');
check('and it passes, with its exceptions written down', $status, 0);
checkTrue('it says it cannot judge chart_of_accounts rather than judging it',
    strpos($out, 'chart_of_accounts is declared differently in 2 databases') !== false);
checkTrue('and it names the database the model actually opens',
    strpos($out, 'none of them cms_tmm_trainee_accountings') !== false);
check('so that screen is not reported as wrong',
    (int)(strpos($out, 'ChartOfAccounts/index.ctp
    sort:name') !== false), 0);
checkTrue('the one it still cannot settle is recorded with its reason',
    strpos($out, 'candidate_submission_documents.id') !== false);

finish();
