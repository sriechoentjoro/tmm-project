<?php
/**
 * The administrator's password reset, and the rules it holds a password to.
 *
 * An administrator could already change somebody's password, but only through
 * the whole edit form: no confirmation field, none of the rules the account
 * holder had to meet when they set it themselves, and no record that it
 * happened. The rules were written out as six elseif branches inside
 * LpkRegistration::setPassword, so a second screen meant either repeating them
 * or sharing them - and this application has already paid for repeating things
 * nine times over.
 *
 * So both screens ask PasswordPolicyTrait, and the form prints the rules from
 * the same place they are enforced. What is checked here is that they agree,
 * that the reset changes the password and nothing else, and that it is refused
 * to anybody but an administrator.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

$db = sys_get_temp_dir() . '/tmm_reset_password.sqlite';
$conn = sqliteConnections(['cms_authentication_authorization', 'default'], $db);
$conn->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(100),
    email VARCHAR(255), password VARCHAR(255), full_name VARCHAR(100),
    is_active INTEGER, institution_id INTEGER, institution_type VARCHAR(50),
    created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE roles (id INTEGER PRIMARY KEY, name VARCHAR(100))');
$conn->execute('CREATE TABLE user_roles (id INTEGER PRIMARY KEY, user_id INTEGER,
    role_id INTEGER)');

$hasher = new DefaultPasswordHasher();
$conn->insert('users', ['id' => 5, 'username' => 'lpk.wonogiri',
    'email' => 'wonogiri@example.test', 'password' => $hasher->hash('OldPass1!'),
    'full_name' => 'Budi Santoso', 'is_active' => 1, 'institution_id' => 12,
    'institution_type' => 'vocational_training']);
$users = TableRegistry::getTableLocator()->get('Users');

list($policy, $problem) = reachInto('App\Controller\UsersController', 'passwordProblem');
list($policy2, $printed) = reachInto('App\Controller\UsersController', 'passwordRules');

/**
 * What the policy says about a password.
 *
 * @param string|null $password What was typed.
 * @param string|null $confirmation What was typed again.
 * @return string|null
 */
function says($password, $confirmation = null)
{
    global $policy, $problem;

    return $problem->invoke($policy, $password, $confirmation);
}

/**
 * Post a reset to the action, as the screen does.
 *
 * @param int $id The account.
 * @param array $post What the form sent.
 * @return array [redirect location or '', the account as it is now]
 */
function reset_through_action($id, array $post)
{
    global $users;

    $request = new ServerRequest([
        'url' => '/users/reset-password/' . $id,
        'post' => $post,
        'environment' => ['REQUEST_METHOD' => 'POST'],
        'session' => new Session(),
        'params' => ['controller' => 'Users', 'action' => 'resetPassword',
            'plugin' => null, 'pass' => [(string)$id]],
    ]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    $controller = new \App\Controller\UsersController($request, new Response());
    $answer = $controller->resetPassword($id);

    return [$answer ? $answer->getHeaderLine('Location') : '', $users->get($id)];
}

echo "  what the policy refuses\n";
checkTrue('nothing at all', says('') !== null);
checkTrue('a password that does not match its confirmation',
    says('Str0ng!pass', 'Str0ng!pasz') !== null);
checkTrue('seven characters', says('Ab1!cde') !== null);
checkTrue('no uppercase', says('ab1!cdefg') !== null);
checkTrue('no lowercase', says('AB1!CDEFG') !== null);
checkTrue('no number', says('Abc!defgh') !== null);
checkTrue('no symbol', says('Abc1defgh') !== null);

echo "  and what it accepts\n";
check('a password meeting all five rules', says('Abc1!defg'), null);
check('with a matching confirmation', says('Abc1!defg', 'Abc1!defg'), null);
checkTrue('the confirmation is only checked when there is one',
    says('Abc1!defg', null) === null);

echo "  the rules the form prints\n";
$rules = $printed->invoke($policy2);
check('there are five of them', count($rules), 5);
foreach ($rules as $rule) {
    checkTrue('each one says something: ' . $rule, trim($rule) !== '');
}

echo "  resetting an account\n";
list($where, $account) = reset_through_action(5,
    ['password' => 'NewPass1!', 'confirm_password' => 'NewPass1!']);
checkTrue('the new password works', $hasher->check('NewPass1!', $account->password));
check('the old one does not', $hasher->check('OldPass1!', $account->password), false);
checkTrue('and it is stored hashed, not as typed',
    strpos($account->password, 'NewPass1!') === false);
checkTrue('the screen goes back to the list', strpos($where, '/users') !== false);

echo "  what a reset may not change\n";
list($where, $account) = reset_through_action(5, [
    'password' => 'Second1!pass', 'confirm_password' => 'Second1!pass',
    // A form post can carry anything. This screen changes one thing.
    'username' => 'taken.over', 'email' => 'attacker@example.test',
    'is_active' => 0, 'institution_id' => 999,
]);
checkTrue('the password did change', $hasher->check('Second1!pass', $account->password));
check('the username did not', $account->username, 'lpk.wonogiri');
check('nor the email', $account->email, 'wonogiri@example.test');
check('nor whether the account is enabled', (int)$account->is_active, 1);
check('nor which institution it belongs to', (int)$account->institution_id, 12);

echo "  a reset the policy refuses\n";
list($where, $account) = reset_through_action(5,
    ['password' => 'weak', 'confirm_password' => 'weak']);
checkTrue('leaves the password as it was',
    $hasher->check('Second1!pass', $account->password));
check('and does not redirect away from the form', $where, '');

list($where, $account) = reset_through_action(5,
    ['password' => 'Mismatch1!', 'confirm_password' => 'Mismatch2!']);
checkTrue('and so does one that does not match its confirmation',
    $hasher->check('Second1!pass', $account->password));

echo "  who is allowed to do it\n";
$request = new ServerRequest(['url' => '/users/reset-password/5',
    'session' => new Session(),
    'params' => ['controller' => 'Users', 'action' => 'resetPassword',
        'plugin' => null, 'pass' => ['5']]]);
$controller = new \App\Controller\UsersController($request, new Response());
checkTrue('an administrator may',
    $controller->isAuthorized(['id' => 1, 'role_names' => ['administrator']]));
check('a training role may not',
    $controller->isAuthorized(['id' => 2, 'role_names' => ['tmm-training']]), false);
check('nor an LPK',
    $controller->isAuthorized(['id' => 3, 'role_names' => ['lpk-penyangga']]), false);
check('nor somebody with no role at all',
    $controller->isAuthorized(['id' => 4]), false);

echo "  where it is reachable from\n";
$list = file_get_contents(TMM_ROOT . '/src/Template/Users/index.ctp');
checkTrue('the user list offers it', strpos($list, "'action' => 'resetPassword'") !== false);
checkTrue('only to an administrator', strpos($list, 'if ($isAdministrator)') !== false);

$lpk = file_get_contents(TMM_ROOT . '/src/Template/Admin/LpkRegistration/index.ctp');
checkTrue('the LPK list offers it on the account, not the institution',
    strpos($lpk, "'controller' => 'Users'") !== false
    && strpos($lpk, "'action' => 'resetPassword', \$account->id") !== false);
checkTrue('and says so when an LPK has no account yet',
    strpos($lpk, 'No login account yet') !== false);

echo "  one statement of the rules, not two\n";
$setPassword = file_get_contents(TMM_ROOT . '/src/Controller/Admin/LpkRegistrationController.php');
checkTrue('setPassword asks the shared policy',
    strpos($setPassword, '$this->passwordProblem($password, $confirmPassword)') !== false);
check('and keeps no copy of the rules',
    preg_match('/preg_match\(.\/\[A-Z\]\//', $setPassword), 0);
$spelt = 0;
foreach (glob(TMM_ROOT . '/src/Controller/*.php') as $file) {
    if (basename($file) === 'PasswordPolicyTrait.php') {
        continue;
    }
    $spelt += preg_match("#'/\[A-Z\]/'#", file_get_contents($file));
}
check('nor does any other controller', $spelt, 0);

echo "  and the password is never handed back\n";
$action = file_get_contents(TMM_ROOT . '/src/Controller/UsersController.php');
$reset = substr($action, strpos($action, 'public function resetPassword'));
$reset = substr($reset, 0, strpos($reset, "\n    /**"));
check('no flash message carries it',
    preg_match('/Flash->\w+\([^;]*\$password/', $reset), 0);
check('and no email is sent with it',
    preg_match('/EmailService|sendEmail/', $reset), 0);
checkTrue('what is recorded is that it happened',
    strpos($reset, "recordDecision('user.resetPassword'") !== false);
check('and the record does not hold the password',
    preg_match("/recordDecision\('user\.resetPassword'.*?\]\);/s", $reset, $m)
        ? (int)(strpos($m[0], '$password') !== false) : 1, 0);

finish($db);
