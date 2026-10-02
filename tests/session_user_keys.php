<?php
/**
 * The keys the signed-in user is read by, against the keys the session holds.
 *
 * The session user is the users row the Form authenticator returned, plus the
 * role_names and roles that login() adds to it. The column is full_name. Six
 * places read 'fullname', which has never been a key there:
 *
 *   Layout/elegant.ctp and Layout/default.ctp - the header on every page of the
 *       application, which fell through to the login name, so nobody has ever
 *       been greeted by their own name.
 *   LpkRegistration twice - the verification mail prints "Registered By:" and
 *       then the value, so every one of those mails has gone out with that line
 *       blank.
 *   ApprenticeOrders - the name recorded against a shared order.
 *
 * Five of the six had a fallback, which is why it looked like nothing. The one
 * without is the one that went out by email.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_session_user.sqlite';
$conn = sqliteConnections(['cms_authentication_authorization', 'default'], $db);
$conn->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(100),
    email VARCHAR(255), password VARCHAR(255), full_name VARCHAR(100),
    is_active INTEGER, institution_id INTEGER, institution_type VARCHAR(50),
    created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE roles (id INTEGER PRIMARY KEY, name VARCHAR(100))');
$conn->execute('CREATE TABLE user_roles (id INTEGER PRIMARY KEY, user_id INTEGER, role_id INTEGER)');
$conn->insert('users', ['id' => 5, 'username' => 'budi.admin',
    'email' => 'budi@example.test', 'password' => 'x',
    'full_name' => 'Budi Santoso', 'is_active' => 1]);
$conn->insert('roles', ['id' => 1, 'name' => 'administrator']);
$conn->insert('user_roles', ['id' => 1, 'user_id' => 5, 'role_id' => 1]);

echo "  what the session actually holds\n";
// Built the way login() builds it: the row the authenticator returns, plus the
// two keys it adds.
$users = TableRegistry::getTableLocator()->get('Users');
$row = $users->get(5, ['contain' => ['Roles']]);
$session = $row->toArray();
unset($session['roles']);
$session['role_names'] = ['administrator'];

check('the name is under full_name', $session['full_name'], 'Budi Santoso');
check('there is no fullname key', isset($session['fullname']), false);
checkTrue('the role names login() adds are there', $session['role_names'] === ['administrator']);
check('and no photo, which nothing stores for a user', isset($session['photo']), false);

echo "  the header on every page\n";
// The expression the layouts use, applied to that session.
$header = function (array $user) {
    return !empty($user['full_name']) ? $user['full_name']
        : (isset($user['username']) ? $user['username'] : 'User');
};
check('greets the person by name', $header($session), 'Budi Santoso');
check('falls back to the login name when there is none',
    $header(['username' => 'budi.admin']), 'budi.admin');
check('and to something when there is neither', $header([]), 'User');

foreach (['elegant', 'default'] as $name) {
    $layout = file_get_contents(TMM_ROOT . '/src/Template/Layout/' . $name . '.ctp');
    $code = preg_replace('#^\s*//.*$#m', '', $layout);
    check($name . '.ctp reads the key the session has',
        substr_count($code, "\$user['full_name']") > 0, true);
    check('and not the one it does not', strpos($code, "\$user['fullname']"), false);
}

echo "  the line that went out by email\n";
$mail = renderClean('the verification mail renders', 'lpk_verification', [
    'institutionName' => 'LPK Wonogiri',
    'directorName' => 'Siti Rahayu',
    'email' => 'lpk@example.test',
    'username' => 'lpk.wonogiri',
    'registrationNumber' => 'LPK-2026-0012',
    'registeredByAdmin' => 'Budi Santoso',
    'registrationDate' => '01 October 2026, 09:00',
    'verificationUrl' => 'https://example.test/verify/abc',
], ['controller' => 'Email', 'templatePath' => 'Email' . DIRECTORY_SEPARATOR . 'html',
    'url' => '/', 'params' => ['controller' => 'Email', 'action' => 'index']]);
checkTrue('and names who registered the institution',
    strpos($mail, 'Budi Santoso') !== false);
checkTrue('beside the label that was left hanging',
    strpos($mail, 'Registered By:') !== false || strpos($mail, 'Didaftarkan Oleh:') !== false);

echo "  and that the mails are handed everything they read\n";
// bin/check-view-vars.php skips src/Template/Email: those are not rendered from
// a controller action, so it cannot tell who fills them. A mail that reads a
// variable nobody passes is the same fault as a page that does, and harder to
// notice - nobody sees the mail but whoever received it.
//
// A template counts as sent only where it is named in a sendEmail() call, not
// merely where its name appears somewhere in a controller.
$calls = [];
foreach (array_merge(
    glob(TMM_ROOT . '/src/Controller/*.php'),
    glob(TMM_ROOT . '/src/Controller/*/*.php'),
    glob(TMM_ROOT . '/src/Controller/Component/*.php')
) as $file) {
    $body = file_get_contents($file);
    $at = 0;
    while (($at = strpos($body, 'sendEmail(', $at)) !== false) {
        $window = substr($body, $at, 1600);
        if (preg_match("/sendEmail\\([^;]*?'([a-z_]+)'/s", $window, $named)) {
            $calls[$named[1]][] = $window;
        }
        $at += 10;
    }
}

$missing = [];
foreach ($calls as $name => $windows) {
    $template = TMM_ROOT . '/src/Template/Email/html/' . $name . '.ctp';
    if (!is_file($template)) {
        $missing[] = $name . ': no such template';
        continue;
    }
    preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)/', file_get_contents($template), $reads);
    foreach (array_unique($reads[1]) as $variable) {
        if ($variable === 'this') {
            continue;
        }
        $passed = false;
        foreach ($windows as $window) {
            if (strpos($window, "'" . $variable . "'") !== false) {
                $passed = true;
                break;
            }
        }
        if (!$passed) {
            $missing[] = $name . ': $' . $variable;
        }
    }
}
checkTrue('there are mails to check (' . count($calls) . ')', count($calls) > 0);
check('every variable a mail reads is passed where it is sent', $missing, []);

// Five of the seven templates are sent by nothing: admin_lpk_notification,
// db_template, default, special_skill_verification, verification_confirmation.
// Recorded, not demanded - whether they are for something planned is not a
// question the source can answer.
$unsent = [];
foreach (glob(TMM_ROOT . '/src/Template/Email/html/*.ctp') as $template) {
    if (!isset($calls[basename($template, '.ctp')])) {
        $unsent[] = basename($template, '.ctp');
    }
}
checkTrue(count($unsent) . ' mail template(s) nothing sends, and no more than that',
    count($unsent) <= 5);

echo "  every key the signed-in user is read by\n";
// Auth->user('x') is unambiguous: it can only be reading the session.
$known = array_merge(array_keys($session), ['roles']);
$wrong = [];
foreach (array_merge(
    glob(TMM_ROOT . '/src/Controller/*.php'),
    glob(TMM_ROOT . '/src/Controller/*/*.php'),
    glob(TMM_ROOT . '/src/Template/*/*.ctp'),
    glob(TMM_ROOT . '/src/Template/*/*/*.ctp')
) as $file) {
    preg_match_all("/Auth->user\('([a-z_]+)'\)/", file_get_contents($file), $found);
    foreach (array_unique($found[1]) as $key) {
        if (in_array($key, $known, true)) {
            continue;
        }
        $wrong[] = basename($file) . ": " . $key;
    }
}
check('each one is a key the session holds', $wrong, []);

finish($db);
