<?php
/**
 * The three buttons at the top of every candidate's page, and the list they
 * land in.
 *
 * "Schedule Interview", "Add MCU" and "Upload Doc" pointed at addInterview,
 * addMcu and uploadDocument on CandidatesController. None of those methods has
 * ever existed, so every click was a MissingActionException - a 500 page - and
 * four routes in config/routes.php pointed at the same three places and a
 * fourth, changeStatus, that nothing referred to at all.
 *
 * Templates for them existed, which is what made it look like a feature rather
 * than a sketch. They were written against a schema that is not there:
 * interview_type_id where the column is master_candidate_interview_type_id,
 * planned_date and planned_time where there is one date_interview, and
 * apprenticeship_order_id, male_count, female_count and reference_file, which
 * are not columns of anything. Each record already has a module that writes it
 * correctly, so the buttons go there instead.
 *
 * Landing there is only useful if the form knows which candidate it is for.
 * Those forms offer find('list', ['limit' => 200]) - the first two hundred
 * candidates by id - so a form set to a candidate outside them would show
 * "-- Select --" and save against whatever was picked instead. That is the
 * trap that used to wipe a saved address, and listIncluding() is what keeps it
 * from repeating, without handing anybody a candidate their role cannot see.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_candidate_buttons.sqlite';
$conn = sqliteConnections(['cms_lpk_candidates', 'default'], $db);
$conn->execute('CREATE TABLE candidates (id INTEGER PRIMARY KEY, name VARCHAR(255),
    vocational_training_institution_id INTEGER)');
$candidates = TableRegistry::getTableLocator()->get('Candidates');
foreach ([[1, 'Budi Santoso', 7], [2, 'Nur Aini', 7], [3, 'Siti Rahayu', 9],
          [4, 'Agus Salim', 9]] as $row) {
    $conn->insert('candidates', ['id' => $row[0], 'name' => $row[1],
        'vocational_training_institution_id' => $row[2]]);
}

list($controller, $listIncluding) = reachInto('App\Controller\AppController', 'listIncluding');

/**
 * The list a form would be offered.
 *
 * @param int $limit How many rows the query offers.
 * @param int|null $chosen The candidate the form is set to.
 * @param array $where Extra conditions, as a role's scoping would add.
 * @return array
 */
function offered($limit, $chosen, array $where = [])
{
    global $listIncluding, $controller, $candidates;
    $query = $candidates->find('list', ['limit' => $limit]);
    if ($where) {
        $query->where($where);
    }

    return $listIncluding->invoke($controller, $query, $chosen);
}

echo "  the candidate is already in the list\n";
check('it is offered once', offered(200, 2), [1 => 'Budi Santoso', 2 => 'Nur Aini',
    3 => 'Siti Rahayu', 4 => 'Agus Salim']);

echo "  the candidate is past where the list stops\n";
$list = offered(2, 4);
checkTrue('the two the query returned are there',
    isset($list[1]) && isset($list[2]));
check('and so is the one the form is set to', $list[4], 'Agus Salim');
check('nothing else is added', count($list), 3);

echo "  no candidate to add\n";
check('an empty list stays the length the query gave it',
    count(offered(2, null)), 2);
check('and so does one asked for id 0', count(offered(2, 0)), 2);

echo "  a candidate the role may not see\n";
// What CandidateDocuments::add does for an lpk-penyangga user: the query is
// narrowed to their own institution before the chosen candidate is looked up.
$list = offered(200, 3, ['Candidates.vocational_training_institution_id' => 7]);
checkTrue('their own institution is offered',
    isset($list[1]) && isset($list[2]));
check('and the candidate from another one is not, even though the form named it',
    isset($list[3]), false);

echo "  an institution with nobody in it\n";
check('offers nobody, rather than the candidate that was asked for',
    offered(200, 3, ['1 = 0']), []);

echo "  the way that condition used to be written\n";
// where(['1' => 0]) was in five places, each commented "Empty result". CakePHP
// drops a numeric array key, so the query goes out with no WHERE clause at all
// and returns everything - including, in LpkDataFilterTrait, every candidate in
// the system to an LPK user whose account carries no institution.
check('a numeric key is no condition, so it returns the whole table',
    count(offered(200, null, ['1' => 0])), 4);
check('the string form returns nothing, which is what was meant',
    count(offered(200, null, ['1 = 0'])), 0);

$stillBroken = [];
foreach (glob(TMM_ROOT . '/src/Controller/*.php') as $file) {
    foreach (file($file) as $number => $line) {
        if (strpos($line, '//') !== false) {
            continue;
        }
        if (preg_match("/where\\(\\[\\s*'1'\\s*=>/", $line)) {
            $stillBroken[] = basename($file) . ':' . ($number + 1);
        }
    }
}
check('and no controller writes the numeric form any more', $stillBroken, []);

echo "  where the three buttons point now\n";
$view = file_get_contents(TMM_ROOT . '/src/Template/Candidates/view.ctp');
foreach ([['CandidateRecordInterviews', 'Schedule Interview'],
          ['CandidateRecordMedicalCheckUps', 'Add MCU'],
          ['CandidateDocuments', 'Upload Doc']] as $pair) {
    list($target, $label) = $pair;
    checkTrue($label . ' goes to ' . $target,
        strpos($view, "'controller' => '" . $target . "', 'action' => 'add', \$candidate->id") !== false);
}

echo "  and that nothing names the three that never existed\n";
$named = [];
foreach (['addInterview', 'addMcu', 'uploadDocument', 'changeStatus'] as $action) {
    foreach (array_merge(
        glob(TMM_ROOT . '/src/Template/*/*.ctp'),
        [TMM_ROOT . '/config/routes.php']
    ) as $file) {
        $body = file_get_contents($file);
        if (preg_match("/'action'\s*=>\s*'" . $action . "'/", $body)) {
            $named[] = basename(dirname($file)) . '/' . basename($file) . ' -> ' . $action;
        }
    }
}
check('no link or route points at one of them', $named, []);

echo "  each add() takes the candidate and gives it back\n";
foreach (['CandidateRecordInterviews' => 'applicant_id',
          'CandidateRecordMedicalCheckUps' => 'applicant_id',
          'CandidateDocuments' => 'candidate_id'] as $name => $field) {
    $php = file_get_contents(TMM_ROOT . '/src/Controller/' . $name . 'Controller.php');
    checkTrue($name . ' add() accepts a candidate',
        strpos($php, 'public function add($candidateId = null)') !== false);
    checkTrue($name . ' sets ' . $field . ' from it',
        preg_match('/->' . $field . ' = \(int\)\$candidateId;/', $php) === 1);
    checkTrue($name . ' returns to the candidate it came from',
        strpos($php, "['controller' => 'Candidates', 'action' => 'view', \$candidateId]") !== false);
}

finish($db);
