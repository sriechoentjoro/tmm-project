<?php
/**
 * One trainee's scores over time, and who may open them.
 *
 * The list screen holds every score of every trainee as one row each. A
 * trainee with thirty tests is thirty rows among hundreds, and the grade
 * column answers "was this one test good" and nothing about the run of them.
 * Whether somebody is getting better was not a question any screen could
 * answer.
 *
 * What is checked here: that the action groups by competency in the order the
 * tests were sat, that the summary states the first-to-latest difference
 * rather than leaving it to be eyeballed, that a trainee with no scores and a
 * database with no scores at all are both ordinary cases rather than errors,
 * and that a role allowed to read this module is allowed to open the three
 * links printed at the top of it.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\Routing\Router;

$db = sys_get_temp_dir() . '/tmm_trainee_progress.sqlite';
$conn = sqliteConnections(
    ['cms_tmm_trainee_training_scorings', 'cms_tmm_trainees', 'default'],
    $db
);
$conn->execute('CREATE TABLE trainee_training_test_scores (id INTEGER PRIMARY KEY,
    trainee_id INTEGER, master_training_competency_id INTEGER,
    test_date DATE, score INTEGER, master_training_test_score_grade_id INTEGER)');
$conn->execute('CREATE TABLE master_training_competencies (id INTEGER PRIMARY KEY,
    title VARCHAR(255))');
$conn->execute('CREATE TABLE master_training_test_score_grades (id INTEGER PRIMARY KEY,
    title CHAR(2), min_score INTEGER, max_score INTEGER, description TEXT)');
$conn->execute('CREATE TABLE trainees (id INTEGER PRIMARY KEY, name VARCHAR(255),
    tmm_code VARCHAR(50))');

// These queries join across databases - cms_tmm_trainees.trainees - because
// that is how this application is laid out: a schema per module. sqlite reads
// the same syntax as schema.table, so the one file is attached again under
// that name and the query runs here exactly as written.
$conn->execute("ATTACH DATABASE '" . $db . "' AS cms_tmm_trainees");

$conn->insert('master_training_competencies', ['id' => 1, 'title' => 'Test bab 1 ~ 8']);
$conn->insert('master_training_competencies', ['id' => 2, 'title' => 'Test N5 ke 1']);
$conn->insert('master_training_test_score_grades',
    ['id' => 1, 'title' => 'A', 'min_score' => 81, 'max_score' => 100,
        'description' => 'Sangat Baik']);
$conn->insert('master_training_test_score_grades',
    ['id' => 2, 'title' => 'B', 'min_score' => 61, 'max_score' => 80,
        'description' => 'Baik']);
$conn->insert('trainees', ['id' => 1, 'name' => 'MUHAMAD QOWIYADI', 'tmm_code' => 'TMM-001']);
$conn->insert('trainees', ['id' => 2, 'name' => 'MUTIA DARMAYANTI', 'tmm_code' => 'TMM-003']);

// Out of date order on purpose: the action has to put them back in the order
// they were sat, or "progress" means nothing.
$scores = [
    [3, 1, 1, '2026-02-12', 80, 2],
    [1, 1, 1, '2026-01-15', 85, 1],
    [2, 1, 1, '2026-01-22', 78, 2],
    [4, 1, 2, '2026-02-15', 90, 1],
    [5, 1, 2, '2026-03-01', 96, 1],
];
foreach ($scores as $row) {
    $conn->insert('trainee_training_test_scores', [
        'id' => $row[0], 'trainee_id' => $row[1],
        'master_training_competency_id' => $row[2], 'test_date' => $row[3],
        'score' => $row[4], 'master_training_test_score_grade_id' => $row[5],
    ]);
}

/**
 * Run the action and hand back what it set.
 *
 * @param mixed $id The trainee in the path, or null.
 * @param array $query The query string.
 * @return array The view variables.
 */
function progress($id = null, array $query = [])
{
    $session = new Session();
    $session->write('Auth.User', ['id' => 1, 'username' => 'sensei',
        'role_names' => ['tmm-training']]);
    $request = new ServerRequest([
        'url' => '/trainee-training-test-scores/progress',
        'query' => $query,
        'session' => $session,
        'params' => ['controller' => 'TraineeTrainingTestScores',
            'action' => 'progress', 'plugin' => null,
            'pass' => $id === null ? [] : [(string)$id]],
    ]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    $controller = new \App\Controller\TraineeTrainingTestScoresController($request, new Response());
    $controller->progress($id);

    return $controller->viewVars;
}

echo "  the trainee picker\n";
$vars = progress();
check('holds only people who have sat something', count($vars['trainees']), 1);
check('and names them', $vars['trainees'][0]['name'], 'MUHAMAD QOWIYADI');
check('with how many tests that is', (int)$vars['trainees'][0]['tests'], 5);
check('opening it with nobody chosen shows the first of them',
    (int)$vars['traineeId'], 1);

echo "  one trainee's scores\n";
$vars = progress(1);
check('all of them', count($vars['rows']), 5);
check('grouped by competency', count($vars['series']), 2);
checkTrue('under the competency names the list screen shows',
    isset($vars['series']['Test bab 1 ~ 8']) && isset($vars['series']['Test N5 ke 1']));

echo "  in the order they were sat\n";
// The fixture inserted them shuffled; this is the check that would fail if the
// query ever lost its ORDER BY, and a progress chart drawn out of order is
// worse than none - it reads as improvement or decline that never happened.
$dates = array_map(function ($point) {
    return $point['date'];
}, $vars['series']['Test bab 1 ~ 8']);
check('oldest first', implode(',', $dates), '2026-01-15,2026-01-22,2026-02-12');
$firstScores = array_map(function ($point) {
    return $point['score'];
}, $vars['series']['Test bab 1 ~ 8']);
check('with the scores that go with those dates',
    implode(',', $firstScores), '85,78,80');
check('and the grade each one earned',
    $vars['series']['Test bab 1 ~ 8'][0]['grade'], 'A');

echo "  what the summary says\n";
check('how many tests', $vars['summary']['tests'], 5);
check('across how many competencies', $vars['summary']['competencies'], 2);
check('the average', $vars['summary']['average'], 85.8);
check('the lowest', $vars['summary']['lowest'], 78);
check('the highest', $vars['summary']['highest'], 96);
check('the first score', $vars['summary']['first'], 85);
check('the latest', $vars['summary']['latest'], 96);
check('and the difference between them, stated',
    $vars['summary']['change'], 11);
check('over a period it names', $vars['summary']['from'] . ' - ' . $vars['summary']['to'],
    '2026-01-15 - 2026-03-01');

echo "  the grade bands the chart reads against\n";
check('both of them', count($vars['bands']), 2);
check('lowest band first', $vars['bands'][0]['title'], 'B');

echo "  a trainee with nothing on record\n";
$vars = progress(2);
check('is not an error', count($vars['rows']), 0);
check('and has no summary to show', $vars['summary'], null);

echo "  a trainee id nobody has\n";
$vars = progress(99);
check('shows no rows', count($vars['rows']), 0);
checkTrue('and names nobody', $vars['trainee'] === null);

echo "  who may open it\n";
// daily, report and progress read exactly what index reads. Without this,
// granted_actions = '*' expands to [menu action, index, view] and the three
// links printed across the top of the list refuse the role that opened it.
$request = new ServerRequest([
    'url' => '/trainee-training-test-scores/progress',
    'session' => new Session(),
    'params' => ['controller' => 'TraineeTrainingTestScores',
        'action' => 'progress', 'plugin' => null, 'pass' => []],
]);
$controller = new \App\Controller\TraineeTrainingTestScoresController($request, new Response());
$source = file_get_contents(TMM_ROOT . '/src/Controller/TraineeTrainingTestScoresController.php');
checkTrue('the three read-only views are granted together',
    strpos($source, "['daily', 'report', 'progress']") !== false);
checkTrue('where index is granted, and only there',
    strpos($source, "hasPermission(\$this->request->getParam('controller'), 'index')") !== false);

echo "  how it is reached\n";
$index = file_get_contents(TMM_ROOT . '/src/Template/TraineeTrainingTestScores/index.ctp');
checkTrue('from the list screen', strpos($index, "'action' => 'progress'") !== false);
$cert = file_get_contents(TMM_ROOT . '/src/Template/TraineeCertificates/view.ctp');
checkTrue("and from one trainee's own score history, already on them",
    strpos($cert, "'action' => 'progress', \$row['trainee_id']") !== false);

finish($db);
