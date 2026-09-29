<?php
/**
 * The credential check config/app_datasources.php makes at boot.
 *
 * It used to be `=== null`, so a config file written with an empty password
 * walked past it: the application booted and then failed on the first query
 * with "Access denied for user", which sends whoever reads it looking at MySQL
 * grants rather than at the file. And a variable exported empty gives an empty
 * string rather than false, so TMM_DB_PASSWORD= in a shell would have replaced
 * a working file with nothing.
 */
require __DIR__ . '/lib/harness.php';

/**
 * Load config/app_datasources.php with a given environment and local file, and
 * say what it did.
 *
 * A separate process, because the failure is an E_USER_ERROR and cannot be
 * caught in the one that asked.
 *
 * @param array $env Environment variables to set.
 * @param string|null $localContents The app_local.php to write, or null.
 * @return string Either "OK: n connections" or "FATAL: <message>".
 */
function bootDatasources(array $env, $localContents)
{
    $dir = sys_get_temp_dir() . '/tmm_dsguard_' . bin2hex(random_bytes(4));
    mkdir($dir);
    copy(TMM_ROOT . '/config/app_datasources.php', $dir . '/app_datasources.php');
    if ($localContents !== null) {
        file_put_contents($dir . '/app_local.php', $localContents);
    }

    file_put_contents($dir . '/run.php', '<?php
require ' . var_export(TMM_ROOT . '/vendor/autoload.php', true) . ';
function env($key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
}
set_error_handler(function ($no, $msg) {
    // PHP 8.4 deprecates passing E_USER_ERROR to trigger_error(), and a
    // developer machine may run a newer PHP than the server. The deprecation
    // is not the failure under test.
    if ($no === E_DEPRECATED || $no === E_USER_DEPRECATED) {
        return true;
    }
    echo "FATAL: ", $msg, "\n";
    exit(0);
});
$connections = include ' . var_export($dir . '/app_datasources.php', true) . ';
echo "OK: ", count($connections), " connections\n";
');

    $prefix = 'env -u TMM_DB_HOST -u TMM_DB_USERNAME -u TMM_DB_PASSWORD ';
    foreach ($env as $key => $value) {
        $prefix .= $key . '=' . escapeshellarg($value) . ' ';
    }
    $out = shell_exec($prefix . 'php ' . escapeshellarg($dir . '/run.php') . ' 2>&1');

    array_map('unlink', glob($dir . '/*'));
    rmdir($dir);

    return (string)$out;
}

$good = '<?php return ["Datasources" => ["default" => ["host" => "localhost",
    "username" => "tmm_user", "password" => "a-real-password"]]];';
$empty = '<?php return ["Datasources" => ["default" => ["host" => "localhost",
    "username" => "tmm_user", "password" => ""]]];';

echo "  a filled-in file\n";
$out = bootDatasources([], $good);
checkTrue('boots', strpos($out, 'OK:') === 0);
checkTrue('and builds every connection', strpos($out, 'connections') !== false);

echo "  an empty password, which is what a failed recovery leaves\n";
$out = bootDatasources([], $empty);
checkTrue('is refused, not booted past', strpos($out, 'FATAL:') === 0);
checkTrue('and is named', strpos($out, 'TMM_DB_PASSWORD') !== false);
checkTrue('while the username it did have is not',
    strpos($out, 'TMM_DB_USERNAME') === false);
checkTrue('and the message says the file was read',
    strpos($out, 'read, so check the Datasources.default values') !== false);
checkTrue('a password of just a newline is empty too',
    strpos(bootDatasources([], '<?php return ["Datasources" => ["default" =>
        ["username" => "tmm_user", "password" => "\n"]]];'), 'FATAL:') === 0);

echo "  no file at all\n";
$out = bootDatasources([], null);
checkTrue('is refused', strpos($out, 'FATAL:') === 0);
checkTrue('and both are named', strpos($out, 'TMM_DB_USERNAME') !== false
    && strpos($out, 'TMM_DB_PASSWORD') !== false);
checkTrue('and the message says to create it',
    strpos($out, 'not found - create it') !== false);

echo "  the environment\n";
checkTrue('alone is enough', strpos(bootDatasources(
    ['TMM_DB_USERNAME' => 'tmm_user', 'TMM_DB_PASSWORD' => 'from-env'], null), 'OK:') === 0);
checkTrue('and fills in what an empty file left out', strpos(bootDatasources(
    ['TMM_DB_PASSWORD' => 'from-env'], $empty), 'OK:') === 0);
// A variable exported empty must not shadow a good file.
checkTrue('a blank variable falls back to the file rather than replacing it',
    strpos(bootDatasources(['TMM_DB_PASSWORD' => ''], $good), 'OK:') === 0);
checkTrue('and a blank one with no file to fall back to is still refused',
    strpos(bootDatasources(['TMM_DB_USERNAME' => '', 'TMM_DB_PASSWORD' => ''], null),
        'FATAL:') === 0);

finish();
