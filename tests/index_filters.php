<?php
/**
 * The filter row, applied where it can reach the whole list.
 *
 * Filtering used to be narrowed in the browser only, over the rows already on
 * the page, so on a paginated list a filter could hide what was in front of you
 * and nothing else: type a name, see "no rows", and the record is on page four.
 * AppController::paginate() applies it to the query now.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_index_filters.sqlite';
$conn = sqliteConnections(['default'], $db);
$conn->execute('CREATE TABLE widgets (id INTEGER PRIMARY KEY, name VARCHAR(255),
    score INTEGER, note VARCHAR(255), issued DATE)');
$conn->execute("INSERT INTO widgets (id, name, score, note, issued) VALUES
    (1,  'Budi',    5,  'first',  '2026-01-10'),
    (2,  'Budiman', 12, 'second', '2026-05-10'),
    (3,  'Nur',     30, 'third',  '2026-05-20'),
    (4,  'Sri',     5,  'fourth', '2025-12-01')");

$widgets = TableRegistry::getTableLocator()->get('Widgets');
$reflection = new ReflectionClass('App\Controller\AppController');
$apply = $reflection->getMethod('applyIndexFilters');
$apply->setAccessible(true);

$lastController = null;

/**
 * Run a query string through the filter and say which rows come back.
 *
 * @param string $queryString What the filter row posted.
 * @param mixed $object What paginate() would have been handed; null for a
 *  fresh query over the widgets.
 * @return mixed The ids that survive, or whatever was handed back unchanged.
 */
function filtered($queryString, $object = null)
{
    global $reflection, $apply, $widgets, $lastController;

    $lastController = $reflection->newInstanceWithoutConstructor();
    giveRequest($lastController, $queryString,
        ['controller' => 'Widgets', 'action' => 'index']);

    $out = $apply->invoke($lastController, $object === null ? $widgets->find() : $object);
    if (!$out instanceof Query) {
        return $out;
    }

    return array_values($out->extract('id')->toList());
}

/**
 * What the screen was told about the filters.
 *
 * @return array|null
 */
function filterNotice()
{
    global $lastController;
    $vars = $lastController->viewVars;

    return isset($vars['indexFilters']) ? $vars['indexFilters'] : null;
}

echo "  nothing to apply\n";
// With no filters the argument must come back as it went in - the same object,
// not an equivalent one - so a screen that passes none is left exactly as it
// was.
$same = $widgets->find();
$untouched = $reflection->newInstanceWithoutConstructor();
giveRequest($untouched, '', ['controller' => 'Widgets', 'action' => 'index']);
check('the query is handed straight on',
    $apply->invoke($untouched, $same) === $same, true);
check('and the screen is told nothing',
    isset($untouched->viewVars['indexFilters']), false);
check('a value that is only spaces is not a filter', filtered('filter_name=%20%20'), [1, 2, 3, 4]);
check('an operator with no value is not a filter either',
    filtered('filter_name_operator=starts_with'), [1, 2, 3, 4]);

echo "  text columns\n";
check('mean contains by default', filtered('filter_name=bud'), [1, 2]);
check('and ignore case', filtered('filter_name=BUD'), [1, 2]);
check('starts with', filtered('filter_name=bud&filter_name_operator=starts_with'), [1, 2]);
check('ends with', filtered('filter_name=man&filter_name_operator=ends_with'), [2]);
check('not like', filtered('filter_name=bud&filter_name_operator=not_like'), [3, 4]);
check('exact', filtered('filter_name=Budi&filter_name_operator=='), [1]);
check('not equal', filtered('filter_name=Budi&filter_name_operator=!='), [2, 3, 4]);

echo "  number columns\n";
check('mean equals by default', filtered('filter_score=5'), [1, 4]);
check('greater than compares as a number, not as text',
    filtered('filter_score=5&filter_score_operator=>'), [2, 3]);
check('less than or equal keeps the boundary',
    filtered('filter_score=12&filter_score_operator=<='), [1, 2, 4]);
check('between takes both ends',
    filtered('filter_score=5&filter_score_operator=between&filter_score_to=12'), [1, 2, 4]);
check('and with no far end is a floor',
    filtered('filter_score=12&filter_score_operator=between'), [2, 3]);

echo "  a value the column cannot hold\n";
// Showing every row would say "no filter". The true answer is that nothing can
// match, so nothing does.
check('letters over a number match nothing, not everything',
    filtered('filter_score=abc'), []);
check('and that is not reported as a fault', filterNotice()['ignored'], []);

echo "  other columns\n";
check('a date can be narrowed by the part you know',
    filtered('filter_issued=2026-05'), [2, 3]);
check('two filters both apply', filtered('filter_name=bud&filter_score=12'), [2]);

echo "  a column the table does not have\n";
// The implementation this replaced qualified whatever it was given as
// Alias.field and let the database object, so a typo in a template was a 500.
check('is dropped, not pasted into the SQL',
    filtered('filter_nosuch=x&filter_name=bud'), [1, 2]);
check('and the screen is told which', filterNotice()['ignored'], ['nosuch']);
check('while the ones that worked are named', array_keys(filterNotice()['applied']), ['name']);

echo "  what paginate() might be handed\n";
check('a table is turned into a query rather than refused',
    filtered('filter_name=nur', $widgets), [3]);
check('and something it cannot narrow is handed back untouched',
    filtered('filter_name=nur', ['not', 'a', 'query']), ['not', 'a', 'query']);

echo "  and the exports go through the same filter\n";
$swept = 0;
foreach (array_merge(glob(TMM_ROOT . '/src/Controller/*.php'),
        glob(TMM_ROOT . '/src/Controller/*/*.php')) as $path) {
    $swept += substr_count(file_get_contents($path),
        '$query = $this->applyIndexFilters($query);');
}
check('every export action narrows its query too', $swept, 245);
$placeholders = 0;
foreach (array_merge(glob(TMM_ROOT . '/src/Controller/*.php'),
        glob(TMM_ROOT . '/src/Controller/*/*.php')) as $path) {
    $placeholders += substr_count(file_get_contents($path),
        "\$fields = ['id', 'name', 'created', 'modified'];");
}
check('and none still asks for the placeholder four columns', $placeholders, 0);

finish($db);
