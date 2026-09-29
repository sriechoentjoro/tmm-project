<?php
/**
 * CMS Database Connections Configuration
 * Loaded by config/app.php as the 'Datasources' key.
 *
 * NO CREDENTIALS IN THIS FILE. It is committed to a public repository, so the
 * host, username and password are read from the environment, falling back to
 * config/app_local.php, which is git-ignored.
 *
 * Set them either as environment variables:
 *
 *     TMM_DB_HOST, TMM_DB_USERNAME, TMM_DB_PASSWORD
 *
 * (for php-fpm, via env[...] in the pool config — note that plain putenv()
 * from a web request will not reach this file), or in config/app_local.php:
 *
 *     <?php
 *     return ['Datasources' => ['default' => [
 *         'host' => 'localhost',
 *         'username' => 'tmm',
 *         'password' => 'the-real-password',
 *     ]]];
 *
 * See config/app_local.example.php for a template.
 *
 * All connections below share one set of credentials and differ only by
 * database name, which is why they are generated from a list rather than
 * repeated by hand.
 */

$local = [];
$localFile = __DIR__ . DIRECTORY_SEPARATOR . 'app_local.php';
if (is_readable($localFile)) {
    $local = (array)include $localFile;
}
$localDb = isset($local['Datasources']['default']) && is_array($local['Datasources']['default'])
    ? $local['Datasources']['default']
    : [];

/**
 * An environment variable, or the fallback when it is absent OR blank.
 *
 * env() hands back whatever getenv() gives, and a variable exported empty -
 * TMM_DB_PASSWORD= in a shell, or an env[] line in a pool config with nothing
 * after the equals - gives an empty string, not false. That would shadow a
 * perfectly good app_local.php with nothing, and the failure would point at
 * the file rather than at the variable. Blank is treated as absent.
 *
 * @param string $key Variable name.
 * @param mixed $fallback What to use when it is absent or blank.
 * @return mixed
 */
$fromEnv = function ($key, $fallback) {
    $value = env($key);

    return ($value === null || trim((string)$value) === '') ? $fallback : $value;
};

$dbHost = $fromEnv('TMM_DB_HOST', isset($localDb['host']) ? $localDb['host'] : 'localhost');
$dbUser = $fromEnv('TMM_DB_USERNAME', isset($localDb['username']) ? $localDb['username'] : null);
$dbPass = $fromEnv('TMM_DB_PASSWORD', isset($localDb['password']) ? $localDb['password'] : null);

// An empty string is not a configured credential either.
//
// The check used to be === null, and a config file written with an empty
// password walked straight past it: the application booted, and then failed on
// the first query with "Access denied for user", which sends whoever reads it
// looking at MySQL grants rather than at the file that is actually wrong. One
// recovery was spent on exactly that. Whitespace counts as empty too, since a
// password read from a file usually arrives with a newline on it.
$missing = [];
if ($dbUser === null || trim((string)$dbUser) === '') {
    $missing[] = 'TMM_DB_USERNAME';
}
if ($dbPass === null || trim((string)$dbPass) === '') {
    $missing[] = 'TMM_DB_PASSWORD';
}

if ($missing) {
    // Fail loudly at boot rather than surfacing as a confusing connection
    // error on the first query. Name which one is missing, and say whether the
    // local file was even found, because "create app_local.php" is unhelpful
    // advice when it is sitting right there and unreadable.
    trigger_error(
        'Database credentials are not configured: ' . implode(' and ', $missing)
        . ' ' . (count($missing) > 1 ? 'are' : 'is') . ' empty or unset. '
        . 'config/app_local.php was ' . (is_readable($localFile)
            ? 'read, so check the Datasources.default values in it'
            : (file_exists($localFile)
                ? 'found but could not be read - check its owner and mode against the php-fpm pool user'
                : 'not found - create it from config/app_local.example.php'))
        . '. Environment variables win over the file where both are set.',
        E_USER_ERROR
    );
}

/**
 * Shared connection options. Only 'database' differs per connection.
 */
$common = [
    'className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Mysql',
    'persistent' => false,
    'host' => $dbHost,
    'username' => $dbUser,
    'password' => $dbPass,
    'encoding' => 'utf8mb4',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'init' => ['SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'],
    'timezone' => 'UTC',
    'flags' => [],
    'cacheMetadata' => true,
    'log' => false,
];

/**
 * connection alias => database name.
 * 'default' and 'cms_masters' deliberately point at the same database.
 */
$databases = [
    'default' => 'cms_masters',
    'cms_masters' => 'cms_masters',
    'cms_lpk_candidates' => 'cms_lpk_candidates',
    'cms_lpk_candidate_documents' => 'cms_lpk_candidate_documents',
    'cms_tmm_apprentices' => 'cms_tmm_apprentices',
    'cms_tmm_apprentice_documents' => 'cms_tmm_apprentice_documents',
    'cms_tmm_apprentice_document_ticketings' => 'cms_tmm_apprentice_document_ticketings',
    'cms_tmm_stakeholders' => 'cms_tmm_stakeholders',
    'cms_tmm_trainees' => 'cms_tmm_trainees',
    'cms_tmm_trainee_accountings' => 'cms_tmm_trainee_accountings',
    'cms_tmm_trainee_trainings' => 'cms_tmm_trainee_trainings',
    'cms_tmm_trainee_training_scorings' => 'cms_tmm_trainee_training_scorings',
    'cms_tmm_trainee_documents' => 'cms_tmm_trainee_documents',
    'cms_tmm_trainee_document_ticketings' => 'cms_tmm_trainee_document_ticketings',
    'cms_authentication_authorization' => 'cms_authentication_authorization',
];

$datasources = [];
foreach ($databases as $alias => $database) {
    $datasources[$alias] = ['database' => $database] + $common;
}

return $datasources;
