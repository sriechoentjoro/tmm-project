<?php
/**
 * Core Configurations.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.1.11
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
$versionFile = file(CORE_PATH . 'VERSION.txt');
return [
    'Cake.version' => trim(array_pop($versionFile)),

    /**
     * Debug Level:
     *
     * false (production): visitors get a generic error page; the stack trace
     * goes to logs/error.log.
     * true (development): the stack trace, file paths and code excerpts are
     * rendered into the page instead.
     *
     * The default is false deliberately. A server that forgets to set DEBUG is
     * then safe rather than exposed, which is the right way round for a
     * public site. Turn it on for one machine with DEBUG in the environment,
     * or 'debug' => true in config/app_local.php.
     */
    'debug' => filter_var(env('DEBUG', false), FILTER_VALIDATE_BOOLEAN),

    /**
     * LPK registration, testing switches.
     *
     * reuseEmailForTesting - let an address be registered again.
     *
     * vocational_training_institutions.email is unique, so an address that has
     * been through the flow once can never be used again. That makes
     * register -> verify -> set password a one-shot per mailbox and impossible
     * to rehearse: testing it twice needs two real inboxes, or a trip into the
     * database between runs.
     *
     * With this on, registering an address that already exists deletes the
     * earlier institution and its verification tokens first, then proceeds as
     * if the address were new. It DELETES A REAL RECORD without asking.
     *
     * Off by default. It was on while the registration flow was being repaired,
     * because testing it twice otherwise needed two real inboxes; now that
     * Resend Verification Email exists on both LPK screens, a stuck
     * registration can be pushed along without destroying anything, and the
     * reason to keep a record-deleting switch armed is gone.
     *
     * Turn it on for one machine with LPK_REUSE_EMAIL=1 in the environment.
     * Never on a machine holding real institutions.
     */
    'Lpk' => [
        'reuseEmailForTesting' => filter_var(env('LPK_REUSE_EMAIL', false), FILTER_VALIDATE_BOOLEAN),
    ],

    /**
     * Configure basic information about the application.
     */
    'App' => [
        'namespace' => 'App',
        'encoding' => env('APP_ENCODING', 'UTF-8'),
        'defaultLocale' => env('APP_DEFAULT_LOCALE', 'en_US'),
        'defaultTimezone' => env('APP_DEFAULT_TIMEZONE', 'Asia/Jakarta'),
        'base' => false,
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        'fullBaseUrl' => false,
        'imageBaseUrl' => 'img/',
        'cssBaseUrl' => 'css/',
        'jsBaseUrl' => 'js/',
        'paths' => [
            'plugins' => [ROOT . DS . 'plugins' . DS],
            'templates' => [APP . 'Template' . DS],
            'locales' => [APP . 'Locale' . DS],
        ],
    ],

    /**
     * Security and encryption configuration
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],

    /**
     * Apply timestamps with the last modified time to asset files (js, css, images).
     */
    'Asset' => [
        'timestamp' => true,
    ],

    /**
     * Configure the cache adapters.
     */
    'Cache' => [
        'default' => [
            'className' => 'File',
            'path' => CACHE,
        ],
        '_cake_core_' => [
            'className' => 'File',
            'prefix' => 'myapp_cake_core_',
            'path' => CACHE . 'persistent/',
            'serialize' => true,
            'duration' => '+1 years',
        ],
        '_cake_model_' => [
            'className' => 'File',
            'prefix' => 'myapp_cake_model_',
            'path' => CACHE . 'models/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKEMODEL_URL', null),
        ],
        '_cake_routes_' => [
            'className' => 'File',
            'prefix' => 'myapp_cake_routes_',
            'path' => CACHE,
            'serialize' => true,
            'duration' => '+1 years',
        ],
    ],

    /**
     * Configure the Error and Exception handlers used by your application.
     */
    'Error' => [
        'errorLevel' => E_ALL & ~E_USER_DEPRECATED,
        'exceptionRenderer' => 'Cake\Error\ExceptionRenderer',
        'skipLog' => [],
        'log' => true,
        'trace' => true,
    ],

    /**
     * Email configuration.
     *
     * Gmail requires an App Password, not the account password:
     * 1. Enable 2-Step Verification: https://myaccount.google.com/security
     * 2. Create an App Password: https://myaccount.google.com/apppasswords
     * 3. Put it in config/app_local.php, or set TMM_SMTP_PASSWORD in the
     *    environment. Never write it in this file — the repository is public.
     */
    'EmailTransport' => [
        'default' => [
            'className' => 'Smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'timeout' => 30,
            // Credentials come from the environment. This file is in a public
            // repository, so nothing secret may be written here.
            'username' => env('TMM_SMTP_USERNAME', null),
            'password' => env('TMM_SMTP_PASSWORD', null),
            'tls' => true,
        ],
        // Fallback to PHP mail() if SMTP fails
        'fallback' => [
            'className' => 'Mail',
        ],
    ],

    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => ['sriechoentjoro@gmail.com' => 'TMM Apprentice Management System'],
            'charset' => 'utf-8',
            'headerCharset' => 'utf-8',
        ],
    ],

    /**
     * Connection information used by the ORM to connect to your application's datastores.
     * LOADS FROM: config/app_datasources.php (13 CMS database connections)
     */
    'Datasources' => require __DIR__ . '/app_datasources.php',

    /**
     * Configures logging options
     */
    'Log' => [
        'debug' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'debug',
            'levels' => ['notice', 'info', 'debug'],
            'url' => env('LOG_DEBUG_URL', null),
        ],
        'error' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'error',
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
            'url' => env('LOG_ERROR_URL', null),
        ],
    ],

    /**
     * Session configuration.
     */
    'Session' => [
        'defaults' => 'php',
    ],
];
