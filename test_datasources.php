<?php
/**
 * IMPORTANT: This file replaces the Datasources section in config/app.php
 * To activate, merge this into app.php replacing old Datasources section
 */

// Load datasources from external file
$datasources = (file_exists(CONFIG . 'app_datasources.php')) 
    ? require CONFIG . 'app_datasources.php'
    : [
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
        ]
    ];

echo "=== CMS Database Configuration Ready ===\n\n";
echo "Loaded " . count($datasources['Datasources']) . " database connections:\n";
foreach($datasources['Datasources'] as $name => $config) {
    echo "  - $name => {$config['database']}\n";
}
echo "\nTo use this configuration:\n";
echo "1. Backup current app.php: copy config\app.php config\app.php.backup\n";
echo "2. Replace 'Datasources' section in app.php starting at line ~130\n";
echo "3. Replace with: 'Datasources' => (file_exists(CONFIG . 'app_datasources.php')) ? require CONFIG . 'app_datasources.php' : [...fallback...]\n";
echo "4. Clear cache: bin\cake cache clear_all\n";
echo "5. Test: bin\cake server -p 8765\n";
