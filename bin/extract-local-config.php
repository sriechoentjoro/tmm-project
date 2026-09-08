#!/usr/bin/env php
<?php
/**
 * Build config/app_local.php from the values config/app.php is currently
 * serving.
 *
 * Credentials used to live inline in config/app.php and
 * config/app_datasources.php. They now come from the environment or from
 * config/app_local.php, which is git-ignored. A server that was configured
 * under the old layout still has the real values in those tracked files, and
 * `git checkout --` would throw them away.
 *
 * Run this BEFORE reverting anything. It reads the live configuration through
 * the same code path the application uses and writes the values into
 * config/app_local.php, so the revert becomes safe.
 *
 *     php bin/extract-local-config.php [--force] [--base-url=https://example.com]
 *
 * It does not have to live in the application. Copied elsewhere it uses the
 * current directory, or --root=/path/to/app when run from somewhere else again:
 *
 *     php /root/extract-local-config.php --root=/var/www/html/tmm
 *
 * It refuses to overwrite an existing config/app_local.php unless --force is
 * given, and it refuses to write placeholder or empty credentials at all —
 * config/bootstrap.php loads app_local.php after app.php and the merged result
 * wins, so a bad file here takes the site down immediately.
 *
 * Nothing secret is printed: the summary shows usernames and string lengths.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script is for the command line only.\n");
}

$args = array_slice($argv, 1);
$force = in_array('--force', $args, true);
$baseUrl = null;
$rootArg = null;
foreach ($args as $arg) {
    if (strpos($arg, '--base-url=') === 0) {
        $baseUrl = substr($arg, strlen('--base-url='));
    } elseif (strpos($arg, '--root=') === 0) {
        $rootArg = rtrim(substr($arg, strlen('--root=')), '/');
    }
}

/**
 * The application root. This script is meant to be copied outside the
 * application — downloaded to /root and run from there is the normal case —
 * so it cannot assume it is sitting in the app's own bin/ directory.
 *
 * In order: an explicit --root=, the current directory, then the parent of
 * wherever this file happens to live.
 */
$isAppRoot = function ($dir) {
    return $dir !== ''
        && is_file($dir . '/config/paths.php')
        && is_file($dir . '/config/app.php')
        && is_file($dir . '/vendor/autoload.php');
};

$root = null;
foreach ([$rootArg, getcwd(), dirname(__DIR__)] as $candidate) {
    if ($candidate !== null && $isAppRoot($candidate)) {
        $root = $candidate;
        break;
    }
}

if ($root === null) {
    fwrite(STDERR, sprintf(
        "Could not find the application root.\n\n"
        . "Looked for config/paths.php, config/app.php and vendor/autoload.php in:\n"
        . "%s\n\n"
        . "Run this from the application directory, or pass --root=/path/to/app.\n",
        implode("\n", array_map(
            function ($d) { return '  ' . ($d === null ? '(--root not given)' : $d); },
            [$rootArg, getcwd(), dirname(__DIR__)]
        ))
    ));
    exit(1);
}

chdir($root);

require $root . '/config/paths.php';
require $root . '/vendor/autoload.php';

$target = $root . '/config/app_local.php';
if (file_exists($target) && !$force) {
    fwrite(STDERR, "config/app_local.php already exists. Pass --force to overwrite it.\n");
    exit(1);
}

// Once config/app_datasources.php is on its new version it aborts with
// E_USER_ERROR when no credentials are configured — which is exactly the state
// this script exists to fix. Report that as guidance rather than a raw fatal.
set_error_handler(function ($severity, $message) {
    if ($severity !== E_USER_ERROR) {
        return false;
    }
    fwrite(STDERR, sprintf(
        "config/app.php could not be loaded:\n\n  %s\n\n"
        . "There is nothing left to extract — the credentials are already out of the\n"
        . "tracked files. Create config/app_local.php from config/app_local.example.php\n"
        . "and fill in the values by hand.\n",
        $message
    ));
    exit(1);
});

$app = include $root . '/config/app.php';

restore_error_handler();

$db = isset($app['Datasources']['default']) ? $app['Datasources']['default'] : [];
$mail = isset($app['EmailTransport']['default']) ? $app['EmailTransport']['default'] : [];
$salt = isset($app['Security']['salt']) ? (string)$app['Security']['salt'] : '';

$value = function ($config, $key) {
    return isset($config[$key]) ? (string)$config[$key] : '';
};

$dbHost = $value($db, 'host') !== '' ? $value($db, 'host') : 'localhost';
$dbUser = $value($db, 'username');
$dbPass = $value($db, 'password');
$mailUser = $value($mail, 'username');
$mailPass = $value($mail, 'password');

// A placeholder here would take the site down the moment the file is written.
$bad = [];
if ($dbUser === '' || $dbUser === 'CHANGE_ME') {
    $bad[] = 'database username';
}
if ($dbPass === '' || $dbPass === 'CHANGE_ME') {
    $bad[] = 'database password';
}
if ($salt === '' || $salt === '__SALT__' || $salt === 'CHANGE_ME') {
    $bad[] = 'Security.salt';
}
if ($bad) {
    fwrite(STDERR, sprintf(
        "Refusing to write: %s still %s a placeholder or empty in config/app.php.\n"
        . "Those values are probably coming from the php-fpm pool environment, which a CLI\n"
        . "process does not see. Set them in config/app_local.php by hand instead.\n",
        implode(' and ', $bad),
        count($bad) === 1 ? 'is' : 'are'
    ));
    exit(1);
}

// SMTP is not fatal — a site that never sends mail can leave it unset.
$warnings = [];
if ($mailUser === '' || $mailUser === 'CHANGE_ME' || strpos($mailUser, 'CHANGE_ME@') === 0) {
    $warnings[] = 'SMTP username is empty or a placeholder';
}
if ($mailPass === '' || $mailPass === 'CHANGE_ME') {
    $warnings[] = 'SMTP password is empty or a placeholder';
}

$export = function ($string) {
    return var_export((string)$string, true);
};

$lines = [
    '<?php',
    '/**',
    ' * Real values for this server. Generated by bin/extract-local-config.php on '
        . date('Y-m-d H:i:s') . '.',
    ' *',
    ' * This file is git-ignored and must never be committed — the repository is public.',
    ' */',
    'return [',
    "    'Datasources' => [",
    "        'default' => [",
    "            'host' => " . $export($dbHost) . ',',
    "            'username' => " . $export($dbUser) . ',',
    "            'password' => " . $export($dbPass) . ',',
    '        ],',
    '    ],',
    '',
    "    'EmailTransport' => [",
    "        'default' => [",
    "            'username' => " . $export($mailUser) . ',',
    "            'password' => " . $export($mailPass) . ',',
    '        ],',
    '    ],',
    '',
    "    'Security' => [",
    "        'salt' => " . $export($salt) . ',',
    '    ],',
];

if ($baseUrl !== null && $baseUrl !== '') {
    $lines[] = '';
    $lines[] = "    'App' => [";
    $lines[] = "        'fullBaseUrl' => " . $export($baseUrl) . ',';
    $lines[] = '    ],';
}

$lines[] = '];';
$contents = implode("\n", $lines) . "\n";

if (file_put_contents($target, $contents) === false) {
    fwrite(STDERR, "Failed to write config/app_local.php.\n");
    exit(1);
}
chmod($target, 0640);

printf("Wrote config/app_local.php (%d bytes)\n\n", strlen($contents));
printf("  db host   : %s\n", $dbHost);
printf("  db user   : %s\n", $dbUser);
printf("  db pass   : %d characters\n", strlen($dbPass));
printf("  smtp user : %s\n", $mailUser !== '' ? $mailUser : '(unset)');
printf("  smtp pass : %d characters\n", strlen($mailPass));
printf("  salt      : %d characters\n", strlen($salt));
if ($baseUrl !== null && $baseUrl !== '') {
    printf("  base url  : %s\n", $baseUrl);
}

foreach ($warnings as $warning) {
    fwrite(STDERR, "\nWarning: " . $warning . ".\n");
}

echo "\nNext: chown the file to the web user, then revert the tracked config files.\n";
