<?php
/**
 * Local, machine-specific configuration — TEMPLATE.
 *
 * Copy this to config/app_local.php and fill in the real values:
 *
 *     cp config/app_local.example.php config/app_local.php
 *     chmod 640 config/app_local.php        # readable by the web user only
 *
 * config/app_local.php is git-ignored and must NEVER be committed. This
 * repository is public, so anything committed here is world-readable.
 *
 * config/app_datasources.php reads Datasources.default from this file when the
 * matching environment variables are not set. Environment variables win when
 * both are present, so a server can use either mechanism:
 *
 *     TMM_DB_HOST        TMM_SMTP_USERNAME     SECURITY_SALT
 *     TMM_DB_USERNAME    TMM_SMTP_PASSWORD
 *     TMM_DB_PASSWORD
 *
 * For php-fpm, set them in the pool config (env[TMM_DB_PASSWORD] = ...) —
 * putenv() from a web request does not reach the config files.
 *
 * All 15 database connections share these credentials; only the database name
 * differs per connection, and those names live in config/app_datasources.php.
 */
return [
    'Datasources' => [
        'default' => [
            'host' => 'localhost',
            //'port' => 'non_standard_port_number',
            'username' => 'CHANGE_ME',
            'password' => 'CHANGE_ME',
        ],
    ],

    /*
     * SMTP credentials for outgoing mail (registration links, notifications).
     * For Gmail this is a 16-character App Password, not the account password.
     */
    'EmailTransport' => [
        'default' => [
            'username' => 'CHANGE_ME@example.com',
            'password' => 'CHANGE_ME',
        ],
    ],

    /*
     * Used to hash cookies, CSRF tokens and other security data. Generate a
     * fresh random value per environment and keep it stable afterwards —
     * changing it invalidates existing sessions and CSRF tokens.
     *
     *     php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'
     */
    'Security' => [
        'salt' => 'CHANGE_ME',
    ],
];
