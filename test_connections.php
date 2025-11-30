<?php
require 'config/bootstrap.php';
use Cake\Datasource\ConnectionManager;

echo "=== Testing Database Connections ===\n\n";

$datasources = require 'config/app_datasources.php';
$connections = array_keys($datasources['Datasources']);

foreach ($connections as $name) {
    try {
        $connection = ConnectionManager::get($name);
        $result = $connection->execute("SELECT DATABASE() as db")->fetch('assoc');
        echo "[] $name => Connected to: {$result['db']}\n";
    } catch (Exception $e) {
        echo "[] $name => ERROR: {$e->getMessage()}\n";
    }
}

echo "\n=== Testing Table Counts ===\n\n";
$testDatabases = ['cms_masters', 'cms_lpk_candidates', 'cms_tmm_apprentices'];
foreach ($testDatabases as $dbName) {
    try {
        $connection = ConnectionManager::get($dbName);
        $tables = $connection->execute("SHOW TABLES")->fetchAll('assoc');
        $count = count($tables);
        echo "$dbName: $count tables\n";
        if ($count > 0 && $count <= 5) {
            foreach ($tables as $table) {
                echo "  - " . array_values($table)[0] . "\n";
            }
        }
    } catch (Exception $e) {
        echo "$dbName: ERROR - {$e->getMessage()}\n";
    }
}
