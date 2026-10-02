<?php
/**
 * Which institution a login account belongs to, by name.
 *
 * users.institution_id and users.institution_type say which institution an
 * account belongs to, and no screen ever turned that into a name. The user list
 * showed "LPK #12", the detail page showed "Vocational Training" and "#12", and
 * the account holder's own profile had a row for it guarded on
 * $user->has('vocational_training_institution') - an association UsersTable does
 * not declare and cannot, because institution_id points at one of two tables
 * depending on institution_type. That row has never rendered for anybody.
 *
 * The password reset screen had the only lookup, written inline, and it was
 * wrong twice over: it compared institution_type with 'special_skill_support',
 * a spelling nothing in this application writes, and then read ->name off
 * whichever table it got - special_skill_support_institutions names its
 * institutions company_name.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

$db = sys_get_temp_dir() . '/tmm_institution_names.sqlite';
$conn = sqliteConnections(['cms_authentication_authorization',
    'cms_tmm_stakeholders', 'default'], $db);
$conn->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(100),
    email VARCHAR(255), password VARCHAR(255), full_name VARCHAR(100),
    is_active INTEGER, institution_id INTEGER, institution_type VARCHAR(50),
    created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE roles (id INTEGER PRIMARY KEY, name VARCHAR(100))');
$conn->execute('CREATE TABLE user_roles (id INTEGER PRIMARY KEY, user_id INTEGER, role_id INTEGER)');
$conn->execute('CREATE TABLE vocational_training_institutions (id INTEGER PRIMARY KEY,
    name VARCHAR(255))');
$conn->execute('CREATE TABLE special_skill_support_institutions (id INTEGER PRIMARY KEY,
    company_name VARCHAR(255))');
$conn->insert('vocational_training_institutions', ['id' => 12, 'name' => 'LPK Wonogiri']);
$conn->insert('vocational_training_institutions', ['id' => 13, 'name' => 'LPK Bekasi']);
$conn->insert('special_skill_support_institutions', ['id' => 4, 'company_name' => 'SO Asahi']);

/**
 * A controller that can be asked, with the session of somebody signed in.
 *
 * @return \App\Controller\UsersController
 */
function controller()
{
    $session = new Session();
    $session->write('Auth.User', ['id' => 1, 'username' => 'admin',
        'role_names' => ['administrator']]);
    $request = new ServerRequest(['url' => '/users', 'session' => $session,
        'params' => ['controller' => 'Users', 'action' => 'index',
            'plugin' => null, 'pass' => []]]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    return new \App\Controller\UsersController($request, new Response());
}

$users = TableRegistry::getTableLocator()->get('Users');
$reader = controller();
$one = reachInto('App\Controller\UsersController', 'institutionFor');
$many = reachInto('App\Controller\UsersController', 'institutionNamesFor');

/**
 * Ask for one account's institution.
 *
 * @param array $account institution_id and institution_type.
 * @return array|null
 */
function resolve(array $account)
{
    global $reader, $one;

    return $one[1]->invoke($reader, $account);
}

echo "  an LPK account\n";
$found = resolve(['institution_id' => 12, 'institution_type' => 'vocational_training']);
check('is named', $found['name'], 'LPK Wonogiri');
check('and links to the institution it belongs to', $found['controller'],
    'VocationalTrainingInstitutions');
check('by its own id', $found['id'], 12);

echo "  a special-skill account\n";
// The point of the trait: this table has no name column at all.
$found = resolve(['institution_id' => 4, 'institution_type' => 'special_skill']);
check('is named from company_name, not name', $found['name'], 'SO Asahi');
check('and links to its own module', $found['controller'],
    'SpecialSkillSupportInstitutions');
check('the spelling the reset screen used to test for works too',
    resolve(['institution_id' => 4, 'institution_type' => 'special_skill_support'])['name'],
    'SO Asahi');

echo "  an account with nothing to resolve\n";
check('no institution at all', resolve(['institution_id' => null,
    'institution_type' => null]), null);
check('an institution that is not on file any more',
    resolve(['institution_id' => 999, 'institution_type' => 'vocational_training']), null);

echo "  a page full of accounts\n";
$rows = [
    ['institution_id' => 12, 'institution_type' => 'vocational_training'],
    ['institution_id' => 13, 'institution_type' => 'vocational_training'],
    ['institution_id' => 4, 'institution_type' => 'special_skill'],
    ['institution_id' => null, 'institution_type' => null],
    ['institution_id' => 999, 'institution_type' => 'vocational_training'],
];
$found = $many[1]->invoke($reader, $rows);
check('every one that exists is resolved', count($found), 3);
check('keyed so a row can find its own', $found['vocational_training:13']['name'],
    'LPK Bekasi');
check('across both kinds at once', $found['special_skill:4']['name'], 'SO Asahi');

echo "  what the screens show now\n";
$conn->insert('users', ['id' => 7, 'username' => 'lpk.wonogiri',
    'email' => 'a@b.test', 'password' => 'x', 'full_name' => 'Budi Santoso',
    'is_active' => 1, 'institution_id' => 12,
    'institution_type' => 'vocational_training']);
$account = $users->get(7, ['contain' => ['Roles']]);
$institution = resolve(['institution_id' => 12, 'institution_type' => 'vocational_training']);

$profile = renderClean('the profile renders', 'profile',
    ['user' => $account, 'institution' => $institution],
    ['controller' => 'Users', 'templatePath' => 'Users', 'url' => '/users/profile',
     'params' => ['controller' => 'Users', 'action' => 'profile']]);
checkTrue('and names the institution, which it never used to show at all',
    strpos($profile, 'LPK Wonogiri') !== false);

$detail = renderClean('the detail page renders', 'view',
    ['user' => $account, 'institution' => $institution, 'isAdministrator' => true],
    ['controller' => 'Users', 'templatePath' => 'Users', 'url' => '/users/view/7',
     'params' => ['controller' => 'Users', 'action' => 'view', 'pass' => ['7']]]);
checkTrue('and names it rather than printing #12',
    strpos($detail, 'LPK Wonogiri') !== false);
check('the bare id is gone from it', preg_match('/#12\b/', $detail), 0);

$list = renderClean('the list renders', 'index', [
    'users' => $users->find()->where(['Users.id' => 7]),
    'roleList' => [1 => 'administrator'],
    'summary' => ['total' => 1, 'active' => 1, 'pending' => 0, 'inactive' => 0],
    'byRole' => [1 => 1], 'filterRole' => null, 'filterStatus' => null,
    'search' => null, 'isAdministrator' => true,
    'institutionNames' => $many[1]->invoke($reader, [$account]),
], ['controller' => 'Users', 'templatePath' => 'Users', 'url' => '/users',
    'params' => ['controller' => 'Users', 'action' => 'index']]);
checkTrue('with the name in the chip', strpos($list, 'LPK Wonogiri') !== false);
check('and not "LPK #12"', preg_match('/LPK #12/', $list), 0);

echo "  one lookup, not four\n";
$source = file_get_contents(TMM_ROOT . '/src/Controller/UsersController.php');
// Comments are stripped first: the file explains the spelling it used to test
// for, and the explanation should not read as the fault still being there.
$code = preg_replace('#^\s*//.*$#m', '', $source);
check('the reset screen keeps no copy of it',
    preg_match("/'special_skill_support'/", $code), 0);
checkTrue('index, view, profile and the reset all ask the same place',
    substr_count($source, 'institutionFor($user)') === 3
    && strpos($source, 'institutionNamesFor($users)') !== false);
$trait = file_get_contents(TMM_ROOT . '/src/Controller/InstitutionNameTrait.php');
checkTrue('and the trait knows the two tables name their institutions differently',
    strpos($trait, 'company_name') !== false && strpos($trait, "'name'") !== false);

finish($db);
