<?php
/**
 * Production Configuration Override
 * 
 * This file overrides settings from config/app.php for production environment
 * Server: asahifamily.id (103.214.112.58)
 * Date: November 10, 2025
 */

return [
    /**
     * PRODUCTION MODE - Disable debug
     */
    'debug' => false,

    /**
     * App Configuration for Production
     */
    'App' => [
        'namespace' => 'App',
        'encoding' => 'UTF-8',
        'defaultLocale' => 'en_US',
        'defaultTimezone' => 'Asia/Jakarta', // Update timezone untuk Indonesia
        'base' => false, // Changed from '/asahi_v3' for root domain
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        'fullBaseUrl' => 'http://asahifamily.id', // Production URL
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
     * Database Connections - Production Credentials
     * MySQL root password: #62xe6ZYR
     */
    'Datasources' => [
		'default' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_masters',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_masters' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_masters',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_lpk_candidates' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_lpk_candidates',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_lpk_candidate_documents' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_lpk_candidate_documents',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_apprentices' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_apprentices',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_apprentice_documents' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_apprentice_documents',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_apprentice_document_ticketings' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_apprentice_document_ticketings',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_organizations' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_organizations',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_stakeholders' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_stakeholders',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_trainees' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_trainees',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_trainee_accountings' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_trainee_accountings',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_trainee_trainings' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_trainee_trainings',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
		'cms_tmm_trainee_training_scorings' => [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'username' => 'root',
			'password' => '62xe6zyr',
			'database' => 'cms_tmm_trainee_training_scorings',
			'encoding' => 'utf8mb4',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
		],
    ],

    /**
     * Email Configuration - Gmail SMTP
     * Using existing credentials from app.php
     */
    'EmailTransport' => [
        'default' => [
            'className' => 'Smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'timeout' => 30,
            'username' => 'afimaintenanceservice@gmail.com',
            'password' => 'hpjyllphdjbmlxam', // Gmail App Password
            'client' => null,
            'tls' => true,
        ],
    ],

    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => ['noreply@asahifamily.id' => 'Asahi Inventory System'],
            'charset' => 'utf-8',
            'headerCharset' => 'utf-8',
        ],
    ],

    /**
     * Error Logging for Production
     */
    'Error' => [
        'errorLevel' => E_ALL,
        'exceptionRenderer' => 'Cake\Error\ExceptionRenderer',
        'skipLog' => [],
        'log' => true, // Log all errors
        'trace' => false, // Don't show traces in production
    ],

    /**
     * Security
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '9d87e8cab0f5d1f4d4e6a5a8e6c8e6d8f5e6a8b6c8d8e6f8a8b6c8d8e6f8a8b6'),
    ],
];
