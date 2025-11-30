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
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

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
     * IMPORTANT: For Gmail, you need to:
     * 1. Enable 2-Step Verification: https://myaccount.google.com/security
     * 2. Create App Password: https://myaccount.google.com/apppasswords
     * 3. Replace 'YOUR_APP_PASSWORD_HERE' below with the generated password
     */
    'EmailTransport' => [
        'default' => [
            'className' => 'Smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'timeout' => 30,
            'username' => 'sriechoentjoro@gmail.com',
            'password' => 'unqqevrzplpwysnk', // Replace with 16-character app password from Google
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
