<?php
/**
 * Changing your own password, and what the settings screen may touch.
 *
 * The profile page used to say "to change your password, please contact the
 * system administrator", and it was telling the truth: there was no screen for
 * it. Every forgotten or shared password was an administrator's errand, and a
 * password somebody else chose for you is one you do not change afterwards.
 *
 * Beside it, Users::settings() called patchEntity() on the whole request and
 * tried to protect itself with unset($user->vocational_training_institution_id)
 * - a column that does not exist, since the column is institution_id. So
 * institution_id, is_active and password were all patchable from that screen by
 * whoever was logged in. It is their own account, so nothing stopped them; and
 * because LpkDataFilterTrait scopes what an LPK user may read by their
 * institution_id, posting one there was a way into another institution's
 * candidates.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

$db = sys_get_temp_dir() . '/tmm_change_password.sqlite';
$conn = sqliteConnections(['cms_authentication_authorization', 'default'], $db);
$conn->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(100),
    email VARCHAR(255), password VARCHAR(255), full_name VARCHAR(100),
    is_active INTEGER, institution_id INTEGER, institution_type VARCHAR(50),
    created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE roles (id INTEGER PRIMARY KEY, name VARCHAR(100))');
$conn->execute('CREATE TABLE user_roles (id INTEGER PRIMARY KEY, user_id INTEGER,
    role_id INTEGER)');

$hasher = new DefaultPasswordHasher();
$users = TableRegistry::getTableLocator()->get('Users');

/**
 * Put the account back as it started, so each case begins from the same place.
 *
 * @return void
 */
function restore()
{
    global $conn, $hasher;
    $conn->delete('users', ['id' => 7]);
    $conn->insert('users', ['id' => 7, 'username' => 'lpk.wonogiri',
        'email' => 'wonogiri@example.test', 'password' => $hasher->hash('OldPass1!'),
        'full_name' => 'Budi Santoso', 'is_active' => 1, 'institution_id' => 12,
        'institution_type' => 'vocational_training']);
}

/**
 * Drive one of the two screens as the signed-in account.
 *
 * @param string $action changePassword or settings.
 * @param array $post What the form sent.
 * @return array [redirect location or '', the account as it is now]
 */
function asSignedIn($action, array $post)
{
    global $users;

    $session = new Session();
    $session->write('Auth.User', ['id' => 7, 'username' => 'lpk.wonogiri',
        'role_names' => ['lpk-penyangga']]);
    $request = new ServerRequest([
        'url' => '/users/' . $action,
        'post' => $post,
        'environment' => ['REQUEST_METHOD' => 'POST'],
        'session' => $session,
        'params' => ['controller' => 'Users', 'action' => $action,
            'plugin' => null, 'pass' => []],
    ]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    $controller = new \App\Controller\UsersController($request, new Response());
    $controller->Auth->setUser(['id' => 7, 'username' => 'lpk.wonogiri',
        'role_names' => ['lpk-penyangga']]);
    $answer = $controller->{$action}();

    return [$answer ? $answer->getHeaderLine('Location') : '', $users->get(7)];
}

restore();

echo "  changing your own password\n";
list($where, $account) = asSignedIn('changePassword', [
    'current_password' => 'OldPass1!',
    'password' => 'Brand3w!pass',
    'confirm_password' => 'Brand3w!pass',
]);
checkTrue('the new one works', $hasher->check('Brand3w!pass', $account->password));
check('the old one does not', $hasher->check('OldPass1!', $account->password), false);
checkTrue('and it goes back to the profile', strpos($where, '/users') !== false);

echo "  without the current password\n";
restore();
list($where, $account) = asSignedIn('changePassword', [
    'current_password' => 'NotTheOne1!',
    'password' => 'Brand3w!pass',
    'confirm_password' => 'Brand3w!pass',
]);
checkTrue('nothing changes', $hasher->check('OldPass1!', $account->password));
check('and the form is not left', $where, '');

echo "  with the current password left blank\n";
restore();
list($where, $account) = asSignedIn('changePassword',
    ['password' => 'Brand3w!pass', 'confirm_password' => 'Brand3w!pass']);
checkTrue('nothing changes either', $hasher->check('OldPass1!', $account->password));

echo "  a new password that is the old one\n";
restore();
list($where, $account) = asSignedIn('changePassword', [
    'current_password' => 'OldPass1!',
    'password' => 'OldPass1!',
    'confirm_password' => 'OldPass1!',
]);
check('is refused rather than reported as a change', $where, '');

echo "  a new password the rules refuse\n";
restore();
foreach (['weak' => 'weak', 'no symbol' => 'Abc1defgh', 'no number' => 'Abc!defgh'] as $why => $attempt) {
    list($where, $account) = asSignedIn('changePassword', [
        'current_password' => 'OldPass1!',
        'password' => $attempt,
        'confirm_password' => $attempt,
    ]);
    checkTrue($why . ' leaves the password alone',
        $hasher->check('OldPass1!', $account->password));
}

echo "  a confirmation that does not match\n";
restore();
list($where, $account) = asSignedIn('changePassword', [
    'current_password' => 'OldPass1!',
    'password' => 'Brand3w!pass',
    'confirm_password' => 'Brand3w!pasz',
]);
checkTrue('leaves the password alone', $hasher->check('OldPass1!', $account->password));

echo "  what the settings screen may change\n";
restore();
list($where, $account) = asSignedIn('settings', [
    'full_name' => 'Budi Santoso Baru',
    'email' => 'baru@example.test',
    'username' => 'lpk.wonogiri.baru',
]);
check('the full name, which never saved before', $account->full_name, 'Budi Santoso Baru');
check('the email', $account->email, 'baru@example.test');
check('and the username', $account->username, 'lpk.wonogiri.baru');

echo "  and what it may not\n";
restore();
list($where, $account) = asSignedIn('settings', [
    'full_name' => 'Budi Santoso',
    // The three that were patchable from here, and the one that matters most:
    // institution_id is what LpkDataFilterTrait scopes an LPK user's data by.
    'institution_id' => 99,
    'institution_type' => 'special_skill_support',
    'is_active' => 0,
    'password' => 'Hijacked1!',
]);
check('not the institution it belongs to', (int)$account->institution_id, 12);
check('nor the kind of institution', $account->institution_type, 'vocational_training');
check('nor whether the account is enabled', (int)$account->is_active, 1);
checkTrue('and not the password, which has a screen of its own',
    $hasher->check('OldPass1!', $account->password));

echo "  how the screens are reached\n";
$profile = file_get_contents(TMM_ROOT . '/src/Template/Users/profile.ctp');
check('the profile page no longer sends people to the administrator',
    strpos($profile, 'contact the system administrator'), false);
checkTrue('it offers the screen instead',
    strpos($profile, "'action' => 'changePassword'") !== false);
checkTrue('and so does the settings page',
    strpos(file_get_contents(TMM_ROOT . '/src/Template/Users/settings.ctp'),
        "'action' => 'changePassword'") !== false);
checkTrue('every signed-in account may reach it',
    strpos(file_get_contents(TMM_ROOT . '/src/Controller/AppController.php'),
        "'logout', 'changePassword'") !== false);

$settings = file_get_contents(TMM_ROOT . '/src/Template/Users/settings.ctp');
checkTrue('the full name field posts the column the table has',
    strpos($settings, "control('full_name'") !== false);
check('and not the one it does not', strpos($settings, "control('fullname'"), false);

echo "  one statement of the rules, still\n";
$controller = file_get_contents(TMM_ROOT . '/src/Controller/UsersController.php');
check('changePassword keeps no copy of them',
    preg_match("#'/\[A-Z\]/'#", $controller), 0);
checkTrue('it asks the shared policy',
    strpos($controller, '$this->passwordProblem($password,') !== false);
checkTrue('and the change is recorded',
    strpos($controller, "recordDecision('user.changePassword'") !== false);

finish($db);
