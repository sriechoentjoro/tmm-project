<?php
/**
 * Sending an account's verification email again, and the link it carries.
 *
 * Adding a user on /users/add sets no status, so the row takes the column's
 * default and lands on pending_verification - and nothing in this application
 * has ever moved a login account off it. The only verification flow there was
 * belongs to the LPK registration and verifies an institution, keyed on the
 * institution, not the account. So the user list filled with rows reading
 * "Menunggu" and there was nothing an administrator could press.
 *
 * The token is of its own kind. An institution's verification and an account's
 * are both keyed on an email address and nothing else, and for an LPK the two
 * are the same address: resending one must not spend the other's live token.
 * That is the first thing checked here, because it is the one that would show
 * up as somebody else's link mysteriously dying.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

$db = sys_get_temp_dir() . '/tmm_user_verification.sqlite';
$conn = sqliteConnections(['cms_authentication_authorization', 'default'], $db);
$conn->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(100),
    email VARCHAR(255), password VARCHAR(255), full_name VARCHAR(100),
    is_active INTEGER, status VARCHAR(50), institution_id INTEGER,
    institution_type VARCHAR(50), created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE roles (id INTEGER PRIMARY KEY, name VARCHAR(100))');
$conn->execute('CREATE TABLE user_roles (id INTEGER PRIMARY KEY, user_id INTEGER, role_id INTEGER)');
$conn->execute('CREATE TABLE email_verification_tokens (id INTEGER PRIMARY KEY,
    user_email VARCHAR(255), token VARCHAR(255), token_type VARCHAR(50),
    is_used INTEGER DEFAULT 0, used_at DATETIME, expires_at DATETIME,
    created DATETIME)');

$users = TableRegistry::getTableLocator()->get('Users');
$tokens = TableRegistry::getTableLocator()->get('EmailVerificationTokens');

/**
 * Put the account back as a newly added one: pending, never verified.
 *
 * @return void
 */
function pending()
{
    global $conn;
    $conn->delete('users', ['id' => 9]);
    $conn->insert('users', ['id' => 9, 'username' => 'budi.staff',
        'email' => 'budi@example.test', 'password' => 'x',
        'full_name' => 'Budi Santoso', 'is_active' => 0,
        'status' => 'pending_verification']);
}

/**
 * Drive one of the two actions.
 *
 * @param string $action resendVerification or verifyEmail.
 * @param mixed $argument The account id, or the token.
 * @param array $roles The signed-in roles.
 * @return string Where it redirected to.
 */
function run($action, $argument, array $roles = ['administrator'])
{
    $session = new Session();
    $session->write('Auth.User', ['id' => 1, 'username' => 'admin', 'role_names' => $roles]);
    $request = new ServerRequest([
        'url' => '/users/' . $action,
        'environment' => ['REQUEST_METHOD' => 'POST'],
        'session' => $session,
        'params' => ['controller' => 'Users', 'action' => $action,
            'plugin' => null, 'pass' => [(string)$argument]],
    ]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    $controller = new \App\Controller\UsersController($request, new Response());
    $answer = $controller->{$action}($argument);

    return $answer ? $answer->getHeaderLine('Location') : '';
}

pending();

echo "  resending does not spend the institution's token\n";
// The case this is really about: an LPK's account and its institution share one
// email address, and both kinds of token are keyed on that address alone.
$institutionToken = $tokens->generateToken('budi@example.test', 'email_verification');
checkTrue('the institution has a live token', (bool)$institutionToken);
run('resendVerification', 9);
$still = $tokens->find()->where(['token' => $institutionToken])->first();
check('which is still live after the account resend', (int)$still->is_used, 0);

echo "  and it does invalidate the account's own previous one\n";
$first = $tokens->find()->where(['user_email' => 'budi@example.test',
    'token_type' => 'user_verification'])->first()->token;
run('resendVerification', 9);
$spent = $tokens->find()->where(['token' => $first])->first();
check('the earlier link stops working', (int)$spent->is_used, 1);
$live = $tokens->find()->where(['user_email' => 'budi@example.test',
    'token_type' => 'user_verification', 'is_used' => 0])->count();
check('and exactly one is live', $live, 1);

echo "  the link activates the account\n";
$token = $tokens->find()->where(['user_email' => 'budi@example.test',
    'token_type' => 'user_verification', 'is_used' => 0])->first()->token;
check('the account is pending before it is opened', $users->get(9)->status,
    'pending_verification');
$where = run('verifyEmail', $token);
$after = $users->get(9);
check('and active after', $after->status, 'active');
check('and enabled', (int)$after->is_active, 1);
checkTrue('then it sends them to the login page', strpos($where, '/users') !== false);

echo "  opening the same link twice\n";
// The ordinary case, not an attack: the first click spent the token.
$where = run('verifyEmail', $token);
check('leaves the account active', $users->get(9)->status, 'active');
checkTrue('and still goes to the login page', strpos($where, '/users') !== false);

echo "  a link that is not one\n";
pending();
run('verifyEmail', 'nonsense');
check('changes nothing', $users->get(9)->status, 'pending_verification');
run('verifyEmail', str_repeat('a', 64));
check('nor does a well-formed token nobody issued',
    $users->get(9)->status, 'pending_verification');

echo "  an account there is nothing to send to\n";
$conn->update('users', ['status' => 'active'], ['id' => 9]);
run('resendVerification', 9);
check('an active account is not sent one',
    $tokens->find()->where(['user_email' => 'budi@example.test',
        'token_type' => 'user_verification', 'is_used' => 0])->count(), 0);

pending();
$conn->update('users', ['email' => ''], ['id' => 9]);
run('resendVerification', 9);
check('nor one with no address to send to',
    $tokens->find()->where(['token_type' => 'user_verification', 'is_used' => 0])->count(), 0);

echo "  who may press it\n";
$request = new ServerRequest(['url' => '/users/resend-verification/9',
    'session' => new Session(),
    'params' => ['controller' => 'Users', 'action' => 'resendVerification',
        'plugin' => null, 'pass' => ['9']]]);
$controller = new \App\Controller\UsersController($request, new Response());
checkTrue('an administrator may',
    $controller->isAuthorized(['id' => 1, 'role_names' => ['administrator']]));
check('a training role may not',
    $controller->isAuthorized(['id' => 2, 'role_names' => ['tmm-training']]), false);

echo "  how it is reached, and what the mail carries\n";
$list = file_get_contents(TMM_ROOT . '/src/Template/Users/index.ctp');
checkTrue('the list offers it on a row that is not active',
    strpos($list, "'action' => 'resendVerification'") !== false
    && strpos($list, "\$user->status !== 'active'") !== false);
checkTrue('as a post, because it sends mail',
    strpos($list, 'postLink') !== false);

$source = file_get_contents(TMM_ROOT . '/src/Controller/UsersController.php');
checkTrue('the mail says whether it actually left',
    strpos($source, 'could not be sent') !== false);
checkTrue('and the attempt is recorded either way',
    strpos($source, "recordDecision('user.resendVerification'") !== false);

finish($db);
