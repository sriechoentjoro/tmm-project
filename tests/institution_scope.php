<?php
/**
 * What an LPK user can reach that belongs to another LPK.
 *
 * The permission system in this application answers "may this role open this
 * screen". It does not answer "may this person see this record", and nothing
 * else did either, except on Candidates: LpkDataFilterTrait existed and only
 * CandidatesController and CandidateDocumentsController called it. Seven
 * tables carry vocational_training_institution_id and six of them were
 * unscoped; six more hang off a candidate and none of them were.
 *
 * So an id in the address bar was enough. A trainee, an apprentice, a course,
 * an institution's story, a candidate's education, experience, family,
 * certificate, interview or medical check-up could be opened, edited and
 * deleted by anybody whose role had the screen - including the LPK down the
 * road. CandidateRecordInterviewsController even imported the trait and never
 * called anything in it.
 *
 * The pick-lists were the other half: a screen can refuse to open somebody
 * else's candidate and still offer that candidate in a dropdown, and
 * attaching a record to them writes into another institution's file.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

$db = sys_get_temp_dir() . '/tmm_institution_scope.sqlite';
$conn = sqliteConnections(['cms_tmm_trainees', 'cms_lpk_candidates', 'default'], $db);
$conn->execute("ATTACH DATABASE '" . $db . "' AS cms_lpk_candidates");
$conn->execute('CREATE TABLE trainees (id INTEGER PRIMARY KEY, name VARCHAR(255),
    vocational_training_institution_id INTEGER, created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE candidates (id INTEGER PRIMARY KEY, name VARCHAR(255),
    vocational_training_institution_id INTEGER, created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE candidate_educations (id INTEGER PRIMARY KEY,
    candidate_id INTEGER, school_name VARCHAR(255), created DATETIME, modified DATETIME)');

// Two institutions. 7 is ours; 9 is the one down the road.
$conn->insert('trainees', ['id' => 1, 'name' => 'Ours',
    'vocational_training_institution_id' => 7]);
$conn->insert('trainees', ['id' => 2, 'name' => 'Theirs',
    'vocational_training_institution_id' => 9]);
$conn->insert('candidates', ['id' => 11, 'name' => 'Our candidate',
    'vocational_training_institution_id' => 7]);
$conn->insert('candidates', ['id' => 12, 'name' => 'Their candidate',
    'vocational_training_institution_id' => 9]);
$conn->insert('candidate_educations', ['id' => 21, 'candidate_id' => 11,
    'school_name' => 'Ours']);
$conn->insert('candidate_educations', ['id' => 22, 'candidate_id' => 12,
    'school_name' => 'Theirs']);

/**
 * A controller, with somebody signed in to it.
 *
 * @param string $class The controller class.
 * @param array $user The identity, as isAuthorized() leaves it.
 * @param string $action The action being attempted.
 * @return \App\Controller\AppController
 */
function signedInTo($class, array $user, $action = 'view')
{
    $session = new Session();
    $session->write('Auth.User', $user);
    $request = new ServerRequest([
        'url' => '/x/' . $action,
        'session' => $session,
        'params' => ['controller' => 'X', 'action' => $action,
            'plugin' => null, 'pass' => []],
    ]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    $controller = new $class($request, new Response());
    // isAuthorized() is what fills this in during a real request; it runs
    // before the action, so by the time a record is loaded it is set.
    $controller->currentUser = $user;

    return $controller;
}

$lpk = ['id' => 5, 'username' => 'lpk.ours', 'role_names' => ['lpk-penyangga'],
    'institution_id' => 7];
$other = ['id' => 6, 'username' => 'lpk.theirs', 'role_names' => ['lpk-penyangga'],
    'institution_id' => 9];
$admin = ['id' => 1, 'username' => 'admin', 'role_names' => ['administrator']];
$stray = ['id' => 7, 'username' => 'lpk.nowhere', 'role_names' => ['lpk-penyangga']];

/**
 * Call one of the trait's protected methods on a live controller.
 *
 * @param object $controller The controller.
 * @param string $method Which one.
 * @param mixed $argument Its argument.
 * @return mixed
 */
function call($controller, $method, $argument)
{
    $handle = new ReflectionMethod(get_class($controller), $method);
    $handle->setAccessible(true);

    return $handle->invoke($controller, $argument);
}

$trainees = TableRegistry::getTableLocator()->get('Trainees');
$educations = TableRegistry::getTableLocator()->get('CandidateEducations');
$candidates = TableRegistry::getTableLocator()->get('Candidates');

echo "  a table that carries the institution itself\n";
$controller = signedInTo('App\Controller\TraineesController', $lpk);
$rows = call($controller, 'scopeToInstitution', $trainees->find())->toArray();
check('the listing shows only this institution', count($rows), 1);
check('and it is the right one', $rows[0]->name, 'Ours');
check('opening their own is allowed',
    call($controller, 'refuseOtherInstitution', $trainees->get(1)), null);
checkTrue("opening the other institution's is refused",
    call($controller, 'refuseOtherInstitution', $trainees->get(2)) instanceof Response);

echo "  the same table, seen by the institution down the road\n";
$controller = signedInTo('App\Controller\TraineesController', $other);
$rows = call($controller, 'scopeToInstitution', $trainees->find())->toArray();
check('shows only theirs', count($rows), 1);
check('and it is theirs', $rows[0]->name, 'Theirs');

echo "  an administrator\n";
$controller = signedInTo('App\Controller\TraineesController', $admin);
$rows = call($controller, 'scopeToInstitution', $trainees->find())->toArray();
check('sees both, because this is scoping and not a second permission system',
    count($rows), 2);
check('and is refused nothing',
    call($controller, 'refuseOtherInstitution', $trainees->get(2)), null);

echo "  an LPK account with no institution on it\n";
// where(['1' => 0]) once read as no condition at all and showed everything.
$controller = signedInTo('App\Controller\TraineesController', $stray);
check('sees nothing rather than everything',
    count(call($controller, 'scopeToInstitution', $trainees->find())->toArray()), 0);
checkTrue('and may open nothing',
    call($controller, 'refuseOtherInstitution', $trainees->get(1)) instanceof Response);

echo "  a record that hangs off a candidate instead\n";
$controller = signedInTo('App\Controller\CandidateEducationsController', $lpk);
$rows = call($controller, 'scopeToInstitution', $educations->find())->toArray();
check('the listing follows the candidate to its institution', count($rows), 1);
check('and shows ours', $rows[0]->school_name, 'Ours');
check('ours may be opened',
    call($controller, 'refuseOtherInstitution', $educations->get(21)), null);
checkTrue('theirs may not',
    call($controller, 'refuseOtherInstitution', $educations->get(22)) instanceof Response);

echo "  the guard does not depend on being remembered\n";
// The record is fetched without its candidate contained, as delete() does.
$bare = $educations->get(22, ['contain' => []]);
checkTrue('an uncontained record is still traced to its candidate',
    call($controller, 'refuseOtherInstitution', $bare) instanceof Response);

echo "  the pick-list the form offers\n";
$list = call($controller, 'scopeCandidates', $candidates->find('list'))->toArray();
check('offers only this institution\'s candidates', count($list), 1);
checkTrue('and it is ours', isset($list[11]));

echo "  every screen that manages institution data\n";
$direct = ['Trainees', 'Apprentices', 'CandidateCourses', 'TraineeCourses',
    'ApprenticeCourses', 'VocationalTrainingInstitutionStories'];
$throughCandidate = ['CandidateEducations', 'CandidateExperiences',
    'CandidateFamilies', 'CandidateCertifications', 'CandidateRecordInterviews',
    'CandidateRecordMedicalCheckUps'];
foreach (array_merge($direct, $throughCandidate) as $name) {
    $source = file_get_contents(TMM_ROOT . '/src/Controller/' . $name . 'Controller.php');
    $guards = substr_count($source, 'refuseOtherInstitution(');
    checkTrue($name . ' guards the record it was asked for', $guards >= 3);
    checkTrue($name . ' narrows its listing',
        strpos($source, 'scopeToInstitution(') !== false);
}
foreach ($throughCandidate as $name) {
    $source = file_get_contents(TMM_ROOT . '/src/Controller/' . $name . 'Controller.php');
    checkTrue($name . ' says it belongs through its candidate',
        strpos($source, "'through' => 'Candidates'") !== false);
}

echo "  and nothing offers a candidate it should not\n";
$unscoped = [];
foreach (glob(TMM_ROOT . '/src/Controller/*Controller.php') as $path) {
    $source = file_get_contents($path);
    if (preg_match_all("/(?<!scopeCandidates\()\\\$this->\w+->Candidates->find\('list'/", $source, $m)) {
        $unscoped[] = basename($path) . ' x' . count($m[0]);
    }
}
check('no candidate pick-list is left unscoped', implode(', ', $unscoped), '');

echo "  Export and Print, which no role but administrator could press\n";
// granted_actions = '*' expands to [menu action, index, view], so the four
// export actions were in it only for a menu pointing straight at one - and no
// menu does. 292 buttons across 73 modules answered "your role does not have
// access". grant_accounting_role_full_access.sql is somebody having typed all
// four into granted_actions for one role rather than fixing the gate.
class ExportGate extends \App\Controller\AppController
{
    public $permissions = [];

    protected function getMenuRolePermissions()
    {
        return $this->permissions;
    }

    // The real one writes a flash and a log line; neither is the question here.
    protected function handleUnauthorizedAccess($action = 'access', $reason = null)
    {
        return null;
    }
}

/**
 * Ask the gate about one action, for a role holding the given permissions.
 *
 * @param array $user The identity.
 * @param string $action The action attempted.
 * @param array $permissions controller => granted actions.
 * @return bool
 */
function gate(array $user, $action, array $permissions)
{
    $session = new Session();
    $session->write('Auth.User', $user);
    $request = new ServerRequest([
        'url' => '/trainees/' . $action,
        'session' => $session,
        'params' => ['controller' => 'Trainees', 'action' => $action,
            'plugin' => null, 'pass' => []],
    ]);
    $controller = new ExportGate($request, new Response());
    $controller->permissions = $permissions;

    return (bool)$controller->isAuthorized($user);
}

$mayList = ['Trainees' => ['index', 'view']];
foreach (['exportCsv', 'exportExcel', 'exportPdf', 'printReport'] as $action) {
    checkTrue('a role that may read the list may ' . $action,
        gate($lpk, $action, $mayList));
}
checkTrue('a role with no permission on the controller still may not export',
    !gate($lpk, 'exportCsv', []));
checkTrue('nor one granted view alone, without the list',
    !gate($lpk, 'exportCsv', ['Trainees' => ['view']]));
checkTrue('and this grants nothing else: promoteToApprentice is still refused',
    !gate($lpk, 'promoteToApprentice', $mayList));
checkTrue('an administrator was never the one being stopped',
    gate($admin, 'exportCsv', []));

echo "  and what those buttons now answer with\n";
// Granting them is only safe because the export queries narrow the way
// index() does. They did not: the screen showed one institution's rows and
// the download held every institution's.
$lines = [];
$status = 0;
exec(escapeshellarg(PHP_BINARY) . ' '
    . escapeshellarg(TMM_ROOT . '/bin/check-export-scope.php') . ' 2>&1', $lines, $status);
$out = implode("\n", $lines);
checkTrue('the export scope checker runs', $out !== '');
check('and every export narrows to its own screen\'s rows', $status, 0);
checkTrue('it looked at the thirteen that narrow their listing',
    strpos($out, '39 export action(s) checked') !== false);

// Read in the controllers themselves, so the checker being wrong about what
// it reads cannot make this pass.
foreach (array_merge($direct, $throughCandidate) as $name) {
    $source = file_get_contents(TMM_ROOT . '/src/Controller/' . $name . 'Controller.php');
    $bodies = preg_split('/\n    public function /', $source);
    $unscoped = [];
    foreach ($bodies as $body) {
        if (!preg_match('/^(exportCsv|exportExcel|printReport)\(/', $body)) {
            continue;
        }
        if (strpos($body, 'scopeToInstitution($query)') === false) {
            $unscoped[] = strtok($body, '(');
        }
    }
    check($name . ' exports what its screen shows', implode(', ', $unscoped), '');
}

finish($db);
