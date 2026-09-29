<?php
/**
 * What bin/check-config.php says, in each state the demo server has been in.
 *
 * config/app_local.php holds the database credentials, the mail key and the
 * security salt, and it is git-ignored - so it survives no deployment and is
 * removed by anything that cleans ignored files. When it went missing the
 * symptom was a five hundred on every page and the cause took an afternoon of
 * log reading to reach. Worse, an empty password in it booted the application
 * and then failed on the first query with "Access denied for user", which
 * points at MySQL grants rather than at the file that is wrong.
 */
require __DIR__ . '/lib/harness.php';

/**
 * Run the checker against a copy of the tree holding this app_local.php, so
 * the real one is never touched.
 *
 * @param string|null $localContents The file to write, or null for none.
 * @param array $env Environment variables to set for the run.
 * @return array [flattened output, exit code]
 */
function runChecker($localContents, array $env = [])
{
    $dir = sys_get_temp_dir() . '/tmm_cfgcheck_' . bin2hex(random_bytes(4));
    mkdir($dir . '/bin', 0777, true);
    mkdir($dir . '/config', 0777, true);
    copy(TMM_ROOT . '/bin/check-config.php', $dir . '/bin/check-config.php');
    copy(TMM_ROOT . '/config/app_local.example.php', $dir . '/config/app_local.example.php');
    if ($localContents !== null) {
        file_put_contents($dir . '/config/app_local.php', $localContents);
    }

    // Unset rather than blank: a blank variable is a state of its own, tested
    // below, and must not leak in from whatever shell runs this.
    $prefix = 'env -u TMM_DB_USERNAME -u TMM_DB_PASSWORD -u SECURITY_SALT '
        . '-u TMM_SMTP_USERNAME -u TMM_SMTP_PASSWORD ';
    foreach ($env as $key => $value) {
        $prefix .= $key . '=' . escapeshellarg($value) . ' ';
    }

    $output = [];
    $code = 0;
    exec($prefix . 'php ' . escapeshellarg($dir . '/bin/check-config.php') . ' 2>&1',
        $output, $code);
    exec('rm -rf ' . escapeshellarg($dir));

    // The report is word-wrapped for reading, so a phrase can straddle two
    // lines. Match against it with the wrapping undone.
    return [preg_replace('/\s+/', ' ', implode("\n", $output)), $code];
}

$salt = str_repeat('a', 64);
$complete = '<?php return [
    "Datasources" => ["default" => ["host" => "localhost",
        "username" => "tmm_user", "password" => "a-32-character-random-password!!"]],
    "Security" => ["salt" => "' . $salt . '"],
    "EmailTransport" => ["default" => [
        "className" => "App\\\\Mailer\\\\Transport\\\\HttpApiTransport",
        "service" => "brevo", "apiKey" => "xkeysib-not-a-real-key"]],
];';

echo "  everything in place\n";
list($out, $code) = runChecker($complete);
checkTrue('reports complete', strpos($out, 'the local configuration is complete') !== false);
check('and exits zero', $code, 0);
checkTrue('the mail service is named', strpos($out, 'brevo over HTTPS') !== false);

echo "  and no secret is printed\n";
checkTrue('the api key is reported as set, not shown',
    strpos($out, 'xkeysib') === false && strpos($out, 'api key set (') !== false);
checkTrue('nor is the database password',
    strpos($out, 'a-32-character-random-password') === false);
checkTrue('nor the salt', strpos($out, $salt) === false);

echo "  the file is not there\n";
list($out, $code) = runChecker(null);
checkTrue('it is named as missing', strpos($out, 'not there') !== false);
checkTrue('and says where to copy one from',
    strpos($out, 'app_local.example.php') !== false);
check('and exits non-zero, because nothing else supplies it', $code, 1);

echo "  the state a bad recovery left behind\n";
// A command that read the password from a file that was not there wrote the
// empty string it got back. The application booted and then failed on the
// first query.
$emptyPassword = '<?php return ["Datasources" => ["default" => [
    "host" => "localhost", "username" => "tmm_user", "password" => ""]],
    "Security" => ["salt" => "' . str_repeat('b', 64) . '"]];';
list($out, $code) = runChecker($emptyPassword);
checkTrue('an empty password reads EMPTY, not "set"',
    strpos($out, 'password EMPTY') !== false);
checkTrue('and is called out', strpos($out, 'The database password is empty') !== false);
checkTrue('while the username it did have is not blamed',
    strpos($out, 'username is') === false);

echo "  the mail key lost with the file\n";
$noKey = '<?php return [
    "Datasources" => ["default" => ["username" => "tmm_user", "password" => "x-very-long-password"]],
    "Security" => ["salt" => "' . str_repeat('c', 64) . '"],
    "EmailTransport" => ["default" => [
        "className" => "App\\\\Mailer\\\\Transport\\\\HttpApiTransport",
        "service" => "brevo", "apiKey" => null]],
];';
list($out, $code) = runChecker($noKey);
checkTrue('a missing key is reported', strpos($out, 'mail API key is missing') !== false);
checkTrue('and says why nothing complained about it',
    strpos($out, 'verification link never arrives') !== false);
checkTrue('the database is not blamed for it',
    strpos($out, 'Nothing will load') === false);

echo "  the published placeholder salt\n";
list($out, $code) = runChecker('<?php return [
    "Datasources" => ["default" => ["username" => "tmm_user", "password" => "x-very-long-password"]],
    "Security" => ["salt" => "__SALT__"],
];');
checkTrue('is refused', strpos($out, 'salt is the placeholder') !== false);

echo "  the environment instead of the file\n";
list($out, $code) = runChecker(null, ['TMM_DB_USERNAME' => 'tmm_user',
    'TMM_DB_PASSWORD' => 'from-the-environment', 'SECURITY_SALT' => str_repeat('d', 64),
    'TMM_SMTP_USERNAME' => 'a@b.c', 'TMM_SMTP_PASSWORD' => 'a-real-app-password']);
check('counts as configured', $code, 0);
checkTrue('and a missing file is then only worth knowing, not a fault',
    strpos($out, 'nothing needs it') !== false);
checkTrue('the SMTP warning for this server is given',
    strpos($out, 'all three SMTP ports time out') !== false);

finish();
