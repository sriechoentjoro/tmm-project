<?php
/**
 * The registration link an institution follows out of its invitation email.
 *
 * An administrator adds a vocational training institution, the application
 * generates a token and mails /institution-registration/complete/<token>, and
 * the institution opens it and chooses a password. That is the whole of how an
 * invited LPK was supposed to get a login.
 *
 * Nobody ever did. Three separate faults sat in createUserAccount(), and the
 * first hid the other two:
 *
 *   1. institution_type was written as 'vocational' or 'special_skill'. The
 *      column is ENUM('vocational_training', 'special_skill_support'). MySQL
 *      refuses a value an ENUM does not list, the catch turned the exception
 *      into false, and the screen said "failed to create user account, please
 *      contact administrator" - for both kinds of institution, every time.
 *   2. The password was handed over already hashed. The User entity hashes
 *      what it is given, so the stored value was a hash of a hash and the
 *      password just chosen could not log in.
 *   3. The role was the literal 6, which is lpk-penyangga in this database and
 *      lpk-so in config/sql/seed_roles.sql.
 *
 * By the time any of that happened the institution was already marked
 * registered, so the link would not open a second time. There was no way out
 * of it from any screen.
 *
 * The fixture carries the real ENUM as a CHECK, read from the database dump
 * rather than copied, for the reason the last one taught: sqlite has no ENUM
 * and accepts whatever it is given, so a fixture that spells the column
 * VARCHAR tests the application's assumption instead of the database's.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

$db = sys_get_temp_dir() . '/tmm_institution_registration.sqlite';
$conn = sqliteConnections(
    ['cms_authentication_authorization', 'cms_tmm_stakeholders', 'cms_masters',
        'default'],
    $db
);

// The values users.institution_type can hold, taken from the dump of the real
// database rather than written out here.
$dump = file_get_contents(TMM_ROOT . '/database_exports/20251124_035402/system_authentication_authorization.sql');
preg_match('/`institution_type`\s+enum\s*\((.*?)\)/is', $dump, $m);
preg_match_all("/'([^']+)'/", $m[1], $found);
$types = $found[1];
check('the dump still defines the two institution types', implode(',', $types),
    'vocational_training,special_skill_support');

$conn->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(100),
    email VARCHAR(255), password VARCHAR(255), full_name VARCHAR(100),
    is_active INTEGER, status VARCHAR(50), email_verified_at DATETIME,
    institution_id INTEGER,
    institution_type VARCHAR(50) CHECK (institution_type IS NULL
        OR institution_type IN (\'' . implode("', '", $types) . '\')),
    created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE roles (id INTEGER PRIMARY KEY, name VARCHAR(100))');
$conn->execute('CREATE TABLE user_roles (id INTEGER PRIMARY KEY, user_id INTEGER,
    role_id INTEGER)');
$conn->execute('CREATE TABLE vocational_training_institutions (id INTEGER PRIMARY KEY,
    name VARCHAR(256), email VARCHAR(100), username VARCHAR(50), password VARCHAR(255),
    director VARCHAR(256), master_propinsi_id INTEGER, master_kabupaten_id INTEGER,
    master_kecamatan_id INTEGER, master_kelurahan_id INTEGER, address VARCHAR(256),
    registration_token VARCHAR(255), token_expires_at DATETIME,
    is_registered BOOLEAN DEFAULT 0, registered_at DATETIME,
    created DATETIME, modified DATETIME)');
// The institution belongsTo four region tables, and saving it checks they
// exist. They take no part in registration; they are here so the save can run.
foreach (['master_propinsis', 'master_kabupatens', 'master_kecamatans',
    'master_kelurahans'] as $region) {
    $conn->execute('CREATE TABLE ' . $region . ' (id INTEGER PRIMARY KEY, name VARCHAR(100))');
    $conn->insert($region, ['id' => 1, 'name' => 'Region']);
}
$conn->execute('CREATE TABLE vocational_training_institution_stories (id INTEGER PRIMARY KEY,
    vocational_training_institution_id INTEGER)');

$conn->execute('CREATE TABLE special_skill_support_institutions (id INTEGER PRIMARY KEY,
    name VARCHAR(256), email VARCHAR(100), username VARCHAR(50),
    password VARCHAR(255), registration_token VARCHAR(255), token_expires_at DATETIME,
    is_registered BOOLEAN DEFAULT 0, registered_at DATETIME,
    created DATETIME, modified DATETIME)');

// The role ids are deliberately not 1..n: nothing may assume lpk-penyangga is 6.
$conn->insert('roles', ['id' => 11, 'name' => 'administrator']);
$conn->insert('roles', ['id' => 17, 'name' => 'lpk-penyangga']);

// The fixtures above are claims about the schema; these hold them to the dump.
checkFixtureColumns('special_skill_support_institutions',
    ['id', 'name', 'email', 'username', 'password', 'registration_token',
        'token_expires_at', 'is_registered', 'registered_at']);
checkFixtureColumns('vocational_training_institutions',
    ['id', 'name', 'email', 'username', 'director', 'address']);
checkFixtureColumns('users', ['id', 'username', 'email', 'password', 'full_name',
    'is_active', 'institution_id', 'institution_type']);

$users = TableRegistry::getTableLocator()->get('Users');
$userRoles = TableRegistry::getTableLocator()->get('UserRoles');

/**
 * Put an invited institution back, unregistered, with a live token.
 *
 * @param string $kind vocational or special.
 * @return string The token in its link.
 */
function invited($kind = 'vocational')
{
    global $conn;
    $token = str_repeat($kind === 'vocational' ? 'a' : 'b', 32);
    $expires = date('Y-m-d H:i:s', strtotime('+48 hours'));

    $conn->delete('users', ['1 = 1']);
    $conn->delete('user_roles', ['1 = 1']);

    if ($kind === 'vocational') {
        $conn->delete('vocational_training_institutions', ['1 = 1']);
        $conn->insert('vocational_training_institutions', ['id' => 3,
            'name' => 'LPK Sejahtera', 'email' => 'lpk@example.test',
            'username' => 'lpk_sejahtera', 'director' => 'Siti Rahayu',
            'master_propinsi_id' => 1, 'master_kabupaten_id' => 1,
            'master_kecamatan_id' => 1, 'master_kelurahan_id' => 1,
            'address' => 'Jalan Satu',
            'registration_token' => $token, 'token_expires_at' => $expires,
            'is_registered' => 0]);
    } else {
        $conn->delete('special_skill_support_institutions', ['1 = 1']);
        $conn->insert('special_skill_support_institutions', ['id' => 4,
            'name' => 'PT Penyangga Mandiri', 'email' => 'ssw@example.test',
            'username' => 'ssw_mandiri', 'registration_token' => $token,
            'token_expires_at' => $expires, 'is_registered' => 0]);
    }

    return $token;
}

/**
 * Open the registration link and set a password, as the form does.
 *
 * @param string $token The token in the link.
 * @param array $post What the form sent, or [] to just open the page.
 * @return string Where it redirected to, or '' when it rendered.
 */
function complete($token, array $post = [])
{
    $request = new ServerRequest([
        'url' => '/institution-registration/complete/' . $token,
        'post' => $post,
        'environment' => ['REQUEST_METHOD' => $post ? 'POST' : 'GET'],
        'session' => new Session(),
        'params' => ['controller' => 'InstitutionRegistration',
            'action' => 'complete', 'plugin' => null, 'pass' => [$token]],
    ]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    $controller = new \App\Controller\InstitutionRegistrationController($request, new Response());
    $controller->startupProcess();
    $answer = $controller->complete($token);

    return $answer ? $answer->getHeaderLine('Location') : '';
}

$hasher = new DefaultPasswordHasher();

echo "  a vocational institution follows its link\n";
$token = invited('vocational');
$where = complete($token, ['password' => 'Str0ng!pass', 'password_confirm' => 'Str0ng!pass']);
$account = $users->find()->where(['email' => 'lpk@example.test'])->first();
checkTrue('the account exists at all', (bool)$account);
check('under the login name the administrator chose', $account->username, 'lpk_sejahtera');

echo "  the three faults, one at a time\n";
check('institution_type is a value the column can hold',
    $account->institution_type, 'vocational_training');
checkTrue('the password it just chose logs in',
    $hasher->check('Str0ng!pass', $account->password));
checkTrue('and the hash is not a hash of a hash',
    !$hasher->check($hasher->hash('Str0ng!pass'), $account->password));
$role = $userRoles->find()->where(['user_id' => $account->id])->first();
checkTrue('it has a role', (bool)$role);
check('looked up by name, not the literal 6', (int)$role->role_id, 17);

echo "  and what else the account needs to be usable\n";
check('it is enabled', (int)$account->is_active, 1);
check('and not left waiting for a verification nobody sends',
    $account->status, 'active');
checkTrue('the address the token proved is recorded as verified',
    !empty($account->email_verified_at));
checkTrue('then it sends them into the application',
    strpos($where, '/dashboard') !== false);

echo "  a special skill support institution follows its own link\n";
$token = invited('special');
complete($token, ['password' => 'Str0ng!pass', 'password_confirm' => 'Str0ng!pass']);
$account = $users->find()->where(['email' => 'ssw@example.test'])->first();
checkTrue('that account exists too', (bool)$account);
check('with the other spelling the column holds',
    $account->institution_type, 'special_skill_support');
check('and the name off the column that table actually has',
    $account->full_name, 'PT Penyangga Mandiri');

echo "  the rules the password has to meet\n";
// The page has always listed five requirements and checked them in JavaScript.
// The action checked the length and nothing else, so the weakest password in
// the application was the one an institution chose for itself.
$token = invited('vocational');
complete($token, ['password' => 'abcdefghij', 'password_confirm' => 'abcdefghij']);
check('a long lowercase password is refused',
    $users->find()->where(['email' => 'lpk@example.test'])->count(), 0);
$institutions = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');
check('and the institution is not marked registered on the way out',
    (int)$institutions->get(3)->is_registered, 0);

echo "  an account that already holds this address\n";
$token = invited('vocational');
$conn->insert('users', ['id' => 70, 'username' => 'lpk_sejahtera',
    'email' => 'lpk@example.test', 'password' => $hasher->hash('OldPass1!'),
    'full_name' => 'Siti Rahayu', 'is_active' => 0, 'status' => 'pending_verification']);
complete($token, ['password' => 'Str0ng!pass', 'password_confirm' => 'Str0ng!pass']);
$account = $users->get(70);
check('is updated rather than duplicated',
    $users->find()->where(['email' => 'lpk@example.test'])->count(), 1);
checkTrue('and takes the password just chosen',
    $hasher->check('Str0ng!pass', $account->password));

echo "  a login name that belongs to somebody else\n";
// The token proves the email address and nothing else. Overwriting the
// password of whoever holds the name would be a takeover, not a registration.
$token = invited('vocational');
$conn->insert('users', ['id' => 71, 'username' => 'lpk_sejahtera',
    'email' => 'someone.else@example.test', 'password' => $hasher->hash('Their1!pass'),
    'full_name' => 'Someone Else', 'is_active' => 1, 'status' => 'active']);
complete($token, ['password' => 'Str0ng!pass', 'password_confirm' => 'Str0ng!pass']);
$other = $users->get(71);
checkTrue('keeps its own password', $hasher->check('Their1!pass', $other->password));
check('and no second account is made for the institution',
    $users->find()->where(['email' => 'lpk@example.test'])->count(), 0);

echo "  the link after it has been used\n";
$token = invited('vocational');
complete($token, ['password' => 'Str0ng!pass', 'password_confirm' => 'Str0ng!pass']);
$where = complete($token);
checkTrue('sends them to the login page rather than opening again',
    strpos($where, 'login') !== false);

echo "  what the public may reach without logging in\n";
$source = file_get_contents(TMM_ROOT . '/src/Controller/AppController.php');
check('not every action this controller has',
    strpos($source, '$this->Auth->allow();'), false);
checkTrue('only the two that are meant to be public',
    strpos($source, "\$this->Auth->allow(['complete', 'processFlow']);") !== false);

echo "  the URL an administrator is given when mail is not working\n";
$add = file_get_contents(TMM_ROOT . '/src/Controller/VocationalTrainingInstitutionsController.php');
check('is no longer a hard-coded localhost address',
    strpos($add, 'http://localhost/tmm/institution-registration'), false);

finish($db);
