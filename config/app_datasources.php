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

$dbHost = env('TMM_DB_HOST', isset($localDb['host']) ? $localDb['host'] : 'localhost');
$dbUser = env('TMM_DB_USERNAME', isset($localDb['username']) ? $localDb['username'] : null);
$dbPass = env('TMM_DB_PASSWORD', isset($localDb['password']) ? $localDb['password'] : null);

if ($dbUser === null || $dbPass === null) {
    // Fail loudly at boot rather than surfacing as a confusing connection
    // error on the first query.
    trigger_error(
        'Database credentials are not configured. Set TMM_DB_USERNAME and '
        . 'TMM_DB_PASSWORD in the environment, or create config/app_local.php '
        . 'from config/app_local.example.php.',
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
