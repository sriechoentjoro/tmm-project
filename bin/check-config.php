#!/usr/bin/env php
<?php
/**
 * Say what the local configuration is missing, before a page has to.
 *
 * config/app_local.php holds everything that cannot be committed: the database
 * credentials, the outgoing-mail key, the security salt. It is git-ignored, so
 * it is not restored by a deployment and it is removed by anything that cleans
 * ignored files - git clean -fdx among them. When it went missing on the demo
 * server the symptom was a five hundred on every page, and the answer took an
 * afternoon of reading logs to reach.
 *
 * Worse than absent is incomplete. An empty password used to pass the check at
 * boot, so the application started and then failed on the first query with
 * "Access denied for user", which sends whoever reads it looking at MySQL
 * grants rather than at the file. And a missing mail key fails nowhere at all
 * until somebody registers an institution and the verification link never
 * arrives.
 *
 * This reads the file the way the application does and says what is there,
 * what is missing, and what to do about each. It prints no secret: a value
 * that is set is reported as set, with its length, never its content.
 *
 * Usage:
 *     php bin/check-config.php           report, exit 1 if anything is missing
 *     php bin/check-config.php --quiet   print nothing when all is well
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$quiet = in_array('--quiet', $argv, true);
$localFile = $root . '/config/app_local.php';

$problems = [];
$notes = [];
$lines = [];

/** Report a setting without ever printing it. */
function present($value)
{
    if ($value === null) {
        return 'not set';
    }
    if (!is_scalar($value)) {
        return 'set (' . gettype($value) . ')';
    }
    $text = (string)$value;
    if (trim($text) === '') {
        return 'EMPTY';
    }

    return 'set (' . strlen($text) . ' characters)';
}

/**
 * An environment variable, or null when it is absent or blank.
 *
 * A variable exported empty gives an empty string rather than false, and that
 * must not shadow a good app_local.php - config/app_datasources.php reads them
 * the same way.
 */
function fromEnv($key)
{
    $value = getenv($key);

    return ($value === false || trim((string)$value) === '') ? null : $value;
}

/** Dig a value out of nested arrays without warnings. */
function at(array $config, array $path)
{
    $value = $config;
    foreach ($path as $key) {
        if (!is_array($value) || !array_key_exists($key, $value)) {
            return null;
        }
        $value = $value[$key];
    }

    return $value;
}

// ------------------------------------------------------------- the file
$lines[] = 'config/app_local.php';
$localMissing = false;
if (!file_exists($localFile)) {
    // Not a fault on its own: the environment is a supported alternative, and
    // the settings below decide whether anything is actually missing. Saying
    // "create app_local.php" to a server that runs from env[] lines in its
    // pool config would be wrong advice stated confidently.
    $lines[] = '    not there';
    $localMissing = true;
    $local = [];
} elseif (!is_readable($localFile)) {
    $owner = function_exists('posix_getpwuid')
        ? posix_getpwuid(fileowner($localFile)) : null;
    $lines[] = sprintf('    there but not readable by %s - owner %s, mode %04o',
        function_exists('posix_getpwuid')
            ? posix_getpwuid(posix_geteuid())['name'] : 'this user',
        $owner ? $owner['name'] : fileowner($localFile),
        fileperms($localFile) & 0777);
    $problems[] = 'config/app_local.php cannot be read. Check its owner and '
        . 'mode against the user the php-fpm pool runs as - the application '
        . 'reads this file as that user, not as root.';
    $local = [];
} else {
    $local = (array)include $localFile;
    $lines[] = sprintf('    readable, mode %04o', fileperms($localFile) & 0777);
    if ((fileperms($localFile) & 0044) !== 0) {
        $notes[] = 'config/app_local.php is readable by users other than its '
            . 'owner and group. It holds the database password. chmod 640 it.';
    }
}

// ------------------------------------------------------------- database
$lines[] = '';
$lines[] = 'Database';
$dbUser = fromEnv('TMM_DB_USERNAME');
$dbPass = fromEnv('TMM_DB_PASSWORD');
$dbUser = $dbUser === null ? at($local, ['Datasources', 'default', 'username']) : $dbUser;
$dbPass = $dbPass === null ? at($local, ['Datasources', 'default', 'password']) : $dbPass;
$dbHost = at($local, ['Datasources', 'default', 'host']);
$lines[] = '    host       ' . ($dbHost === null ? 'not set (localhost assumed)' : $dbHost);
$lines[] = '    username   ' . present($dbUser);
$lines[] = '    password   ' . present($dbPass);

foreach (['username' => $dbUser, 'password' => $dbPass] as $what => $value) {
    if ($value === null || trim((string)$value) === '') {
        $problems[] = sprintf('The database %s is %s. Nothing will load: '
            . 'config/app_datasources.php refuses to build a connection '
            . 'without it.', $what, $value === null ? 'not set' : 'empty');
    }
}

// ------------------------------------------------------------- the salt
$lines[] = '';
$lines[] = 'Security salt';
$salt = fromEnv('SECURITY_SALT');
$salt = $salt === null ? at($local, ['Security', 'salt']) : $salt;
$lines[] = '    salt       ' . present($salt);
if ($salt === null || trim((string)$salt) === '' || $salt === '__SALT__'
    || $salt === 'CHANGE_ME') {
    $problems[] = 'The security salt is the placeholder or unset. It hashes '
        . 'cookies and CSRF tokens, so leaving it at a published value is a '
        . 'hole. Generate one: php -r \'echo bin2hex(random_bytes(32)), PHP_EOL;\'';
} elseif (strlen((string)$salt) < 32) {
    $notes[] = 'The security salt is shorter than 32 characters. Longer is better.';
}

// ------------------------------------------------------------- the mail
$lines[] = '';
$lines[] = 'Outgoing mail';
$transport = at($local, ['EmailTransport', 'default']);
$className = is_array($transport) && isset($transport['className'])
    ? $transport['className'] : null;

if ($className !== null && strpos($className, 'HttpApiTransport') !== false) {
    $service = isset($transport['service']) ? $transport['service'] : '(not named)';
    $lines[] = '    through    ' . $service . ' over HTTPS';
    $lines[] = '    api key    ' . present(isset($transport['apiKey']) ? $transport['apiKey'] : null);
    if (empty($transport['apiKey'])) {
        $problems[] = 'The mail API key is missing, so no mail can be sent. '
            . 'Nothing fails visibly until somebody registers an institution '
            . 'and the verification link never arrives; the reason is recorded '
            . 'in email_logs.error_message.';
    }
} else {
    $smtpUser = fromEnv('TMM_SMTP_USERNAME');
    $smtpPass = fromEnv('TMM_SMTP_PASSWORD');
    $smtpUser = $smtpUser === null ? (is_array($transport) && isset($transport['username'])
        ? $transport['username'] : null) : $smtpUser;
    $smtpPass = $smtpPass === null ? (is_array($transport) && isset($transport['password'])
        ? $transport['password'] : null) : $smtpPass;
    $lines[] = '    through    SMTP (config/app.php: smtp.gmail.com:587)';
    $lines[] = '    username   ' . present($smtpUser);
    $lines[] = '    password   ' . present($smtpPass);

    if ($smtpUser === null || trim((string)$smtpUser) === ''
        || $smtpUser === 'CHANGE_ME@example.com'
        || $smtpPass === null || trim((string)$smtpPass) === ''
        || $smtpPass === 'CHANGE_ME') {
        $problems[] = 'No mail credentials, so no mail can be sent. Nothing '
            . 'fails visibly until somebody registers an institution and the '
            . 'verification link never arrives.';
    } else {
        $notes[] = 'Mail is set to go out over SMTP. On the demo server all '
            . 'three SMTP ports time out, and the reason recorded in '
            . 'email_logs is "Connection timed out" with no authentication '
            . 'attempted. If that is this server, use the HttpApiTransport '
            . 'block in config/app_local.example.php instead.';
    }
}

// ------------------------------------------- the library nobody packaged
$lines[] = '';
$lines[] = 'ImageResize library';
$imageResize = $root . '/vendor/ImageResize/ImageResize.php';
if (is_readable($imageResize)) {
    $lines[] = '    installed at vendor/ImageResize/ImageResize.php';
} else {
    $lines[] = '    not installed';
    $notes[] = 'vendor/ImageResize/ImageResize.php is missing. Uploaded photos '
        . 'are stored at the size they arrive rather than shrunk to 800x800, '
        . 'and no watermark is added. Nothing else breaks. The library is in no '
        . 'repository and composer does not provide it - it is copied to the '
        . 'server by hand (see upload_imageresize_to_production.ps1), which is '
        . 'why it goes missing. Copying the file into src/ and autoloading it '
        . 'from there would end that.';
}

// ------------------------------------------------------------- the report
if ($localMissing && !$problems) {
    $notes[] = 'config/app_local.php is not there, and nothing needs it: every '
        . 'setting is coming from the environment. Nothing to restore.';
} elseif ($localMissing) {
    $notes[] = 'config/app_local.php is not there. Either create it from '
        . 'config/app_local.example.php, or set the missing values in the '
        . 'environment - for php-fpm that means env[...] lines in the pool '
        . 'config, since putenv() from a web request does not reach the '
        . 'config files.';
}

if (!$quiet || $problems || $notes) {
    echo implode("\n", $lines), "\n";
}

if ($problems) {
    echo "\n", count($problems), ' thing(s) will not work:', "\n";
    foreach ($problems as $i => $problem) {
        echo "\n  ", $i + 1, '. ', wordwrap($problem, 72, "\n     "), "\n";
    }
}

if ($notes) {
    echo "\n", count($notes), ' thing(s) worth knowing:', "\n";
    foreach ($notes as $i => $note) {
        echo "\n  ", $i + 1, '. ', wordwrap($note, 72, "\n     "), "\n";
    }
}

if (!$problems) {
    echo "\nthe local configuration is complete\n";
}

exit($problems ? 1 : 0);
