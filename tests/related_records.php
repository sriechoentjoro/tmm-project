<?php
/**
 * The rows behind a related-records tab, and the query string that used to be
 * able to ask for all of them.
 *
 * Every copy of getRelated() built its filter straight from the query string:
 *
 *     $query->where([$filterField => $filterValue]);
 *
 * CakePHP puts an array key into the SQL as written, so filter_field set to
 * "1 = 1 OR id IS NOT" produced WHERE 1 = 1 or id is not :c0 and matched every
 * row in the table, not the rows belonging to the record on screen. The column
 * filters read from the same place went the same way. There were nine copies,
 * each about a hundred lines, differing only in the model.
 *
 * There is one now, in RelatedRecordsTrait, used by AppController so that every
 * controller answers it - seventeen tabs named controllers that had no copy at
 * all, and could only ever show an empty table. Every column name it uses has
 * to be a column of that table first.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Http\Response;
use Cake\Http\ServerRequest;

$db = sys_get_temp_dir() . '/tmm_related_records.sqlite';
$conn = sqliteConnections(['cms_tmm_trainees', 'default'], $db);
$conn->execute('CREATE TABLE trainee_certifications (id INTEGER PRIMARY KEY,
    trainee_id INTEGER, title VARCHAR(255), institution_name VARCHAR(255),
    certification_date DATE, detail TEXT)');
foreach ([[1, 7, 'JLPT N4', 'LPK Wonogiri'], [2, 7, 'Forklift', 'LPK Wonogiri'],
          [3, 9, 'JLPT N5', 'LPK Bekasi'], [4, 9, 'Welding', 'LPK Bekasi'],
          [5, 9, 'Crane', 'LPK Bekasi']] as $row) {
    $conn->insert('trainee_certifications', ['id' => $row[0], 'trainee_id' => $row[1],
        'title' => $row[2], 'institution_name' => $row[3]]);
}

/**
 * Ask the endpoint, as the tab's script does.
 *
 * @param array $query The query string.
 * @return array The decoded answer.
 */
function ask(array $query)
{
    $request = new ServerRequest([
        'url' => '/trainee-certifications/get-related',
        'query' => $query,
        'params' => ['controller' => 'TraineeCertifications', 'action' => 'getRelated',
            'plugin' => null, 'pass' => []],
    ]);
    $controller = new \App\Controller\TraineeCertificationsController($request, new Response());

    return json_decode((string)$controller->getRelated()->getBody(), true);
}

echo "  a controller that never had a copy of its own\n";
$answer = ask(['filter_field' => 'trainee_id', 'filter_value' => 7]);
checkTrue('answers at all', $answer['success']);
check('with the rows belonging to that record', count($answer['records']), 2);
check('and not the ones belonging to another', array_column($answer['records'], 'trainee_id'),
    [7, 7]);
check('the count is the whole set, not the page', $answer['pagination']['total'], 2);

echo "  the query string that used to return the whole table\n";
$answer = ask(['filter_field' => '1 = 1 OR id IS NOT', 'filter_value' => null]);
check('is refused', $answer['success'], false);
checkTrue('and says which column it does not have',
    strpos($answer['error'], '1 = 1 OR id IS NOT') !== false);
checkTrue('no rows come back with it', !isset($answer['records']));

echo "  and a column filter shaped the same way\n";
$answer = ask(['filter_field' => 'trainee_id', 'filter_value' => 9,
    'filters' => json_encode(['title) OR (1' => ['value' => 'x', 'operator' => 'contains']])]);
checkTrue('the request still answers', $answer['success']);
check('the column is refused by name', $answer['refused'], ['title) OR (1']);
check('and the scoping it tried to escape still holds', count($answer['records']), 3);

echo "  a filter on a column that is really there\n";
$answer = ask(['filter_field' => 'trainee_id', 'filter_value' => 9,
    'filters' => json_encode(['title' => ['value' => 'ld', 'operator' => 'contains']])]);
check('narrows to what matches', array_column($answer['records'], 'title'), ['Welding']);
check('with nothing refused', $answer['refused'], []);

$answer = ask(['filter_field' => 'trainee_id', 'filter_value' => 9,
    'filters' => json_encode(['title' => ['value' => 'Crane', 'operator' => 'equals']])]);
check('equals means equals', array_column($answer['records'], 'title'), ['Crane']);

echo "  a page of a longer list\n";
$answer = ask(['filter_field' => 'trainee_id', 'filter_value' => 9, 'page' => 2, 'limit' => 2]);
check('holds what is left', count($answer['records']), 1);
check('the count is still of the whole set', $answer['pagination']['total'], 3);
check('and it says how many pages that is', $answer['pagination']['pages'], 2);

echo "  a limit somebody made up\n";
$answer = ask(['filter_field' => 'trainee_id', 'filter_value' => 9, 'limit' => 100000]);
check('is capped', $answer['pagination']['limit'], 100);
$answer = ask(['filter_field' => 'trainee_id', 'filter_value' => 9, 'limit' => 0, 'page' => -3]);
check('and a limit of nothing is not nothing', $answer['pagination']['limit'], 1);
check('nor is a page before the first', $answer['pagination']['page'], 1);

echo "  no filter column at all\n";
$answer = ask([]);
check('is refused rather than answered with everything', $answer['success'], false);

echo "  what the tree holds now\n";
$copies = [];
foreach (glob(TMM_ROOT . '/src/Controller/*Controller.php') as $file) {
    if (strpos(file_get_contents($file), 'public function getRelated()') !== false) {
        $copies[] = basename($file);
    }
}
check('no controller keeps a copy of its own', $copies, []);
checkTrue('the one copy is the trait',
    strpos(file_get_contents(TMM_ROOT . '/src/Controller/RelatedRecordsTrait.php'),
        'public function getRelated()') !== false);
checkTrue('and AppController uses it',
    strpos(file_get_contents(TMM_ROOT . '/src/Controller/AppController.php'),
        'use RelatedRecordsTrait;') !== false);
checkTrue('granted where view is granted, and not on its own',
    strpos(file_get_contents(TMM_ROOT . '/src/Controller/AppController.php'),
        "\$action === 'getRelated' && \$this->hasPermission(\$controller, 'view')") !== false);

echo "  the element the tabs are drawn with\n";
$element = file_get_contents(TMM_ROOT
    . '/src/Template/Element/related_records_table_static.ctp');
checkTrue('reads the names the views pass', strpos($element, 'isset($ajaxUrl)') !== false
    && strpos($element, 'isset($filterField)') !== false
    && strpos($element, 'isset($filterValue)') !== false);
checkTrue('sends the column to filter on', strpos($element, 'filter_field: filterField') !== false);
checkTrue('reads the key the endpoint writes', strpos($element, 'data.records') !== false);
// Comments are stripped first: the block explains what it used to read, and
// the explanation should not read as the fault still being there.
$code = preg_replace('#^\s*//.*$#m', '', $element);
check('and no longer reads the one it does not',
    strpos($code, 'data.data'), false);
checkTrue('draws its rows from the columns the tab was given',
    strpos($element, 'columns.forEach(function (column)') !== false);
check('rather than from hardcoded trainee fields',
    strpos($element, 'record.identity_number'), false);
checkTrue('fetches the first page, since the pages pass no rows',
    strpos($element, 'if (tbody.querySelectorAll(\'tr\').length === 0)') !== false);
checkTrue('and says so when the lookup is refused',
    strpos($element, 'not allowed to read these records') !== false);

echo "  how every tab on every page is configured\n";
// A tab names the column that ties its rows to the record on screen, and the
// columns to show. Every one of the twenty-one named a column that is not a
// column: filterField was the lowercased plural of the parent -
// trainees_id, apprentices_id, mastercandidateinterviewtypes_id - where the
// real key is trainee_id, apprentice_id,
// master_candidate_interview_type_id; and nearly all listed name and created,
// which those tables do not have either. The endpoint refuses a column it does
// not have, so the tabs would have said so instead of filling.
$problems = [];
foreach (glob(TMM_ROOT . '/src/Template/*/*.ctp') as $template) {
    $body = file_get_contents($template);
    if (strpos($body, 'related_records_table_static') === false) {
        continue;
    }
    preg_match_all("/element\('related_records_table_static',\s*\[(.*?)\n\s*\]\)/s",
        $body, $calls);
    foreach ($calls[1] as $call) {
        if (!preg_match("/'ajaxUrl'.*?'controller'\s*=>\s*'([A-Za-z0-9_]+)'/s", $call, $named)) {
            continue;
        }
        $where = basename(dirname($template)) . '/' . basename($template)
            . ' -> ' . $named[1];
        $entity = TMM_ROOT . '/src/Model/Entity/'
            . Cake\Utility\Inflector::singularize($named[1]) . '.php';
        if (!is_file($entity)) {
            $problems[] = $where . ': no entity';
            continue;
        }
        preg_match_all('/@property\s+(\S+)\s+\$([a-z_]+)/',
            file_get_contents($entity), $properties, PREG_SET_ORDER);
        $columns = [];
        foreach ($properties as $property) {
            if (strpos($property[1], 'App\\Model\\Entity') === false) {
                $columns[] = $property[2];
            }
        }
        if (preg_match("/'(?:filterField|foreignKey)'\s*=>\s*'([a-z_0-9]+)'/", $call, $key)
            && !in_array($key[1], $columns, true)) {
            $problems[] = $where . ': no column ' . $key[1];
        }
        preg_match_all("/'name'\s*=>\s*'([a-z_0-9]+)'/", $call, $listed);
        foreach (array_diff($listed[1], $columns) as $missing) {
            $problems[] = $where . ': no column ' . $missing;
        }
    }
}
check('every tab names columns its table really has', $problems, []);

// Two tabs with one tabId cannot both work: the element finds its container by
// that id, so the second binds to the first one's table.
$twice = [];
foreach (glob(TMM_ROOT . '/src/Template/*/*.ctp') as $template) {
    preg_match_all("/'tabId'\s*=>\s*'([a-z_0-9]+)'/", file_get_contents($template), $ids);
    foreach (array_count_values($ids[1]) as $id => $times) {
        if ($times > 1) {
            $twice[] = basename(dirname($template)) . '/' . basename($template) . ': ' . $id;
        }
    }
}
check('and no page carries the same tab twice', $twice, []);

// Three endpoints answered these tabs, each its own copy of the same hundred
// lines and the same unchecked column name.
$custom = [];
foreach (['getRelatedStories', 'searchApprentices'] as $name) {
    foreach (glob(TMM_ROOT . '/src/Controller/*.php') as $controller) {
        if (strpos(file_get_contents($controller), 'function ' . $name . '(') !== false) {
            $custom[] = basename($controller) . '::' . $name;
        }
    }
}
check('and one endpoint answers all of them', $custom, []);

finish($db);
