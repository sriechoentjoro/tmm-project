<?php
/**
 * Import SQL files into MySQL databases
 * Run from command line: php import_databases.php
 */

$sqlDir = __DIR__ . '/tmp/sql';
$host = 'localhost';
$user = 'root';
$pass = '62xe6zyr'; // XAMPP MySQL password

echo "=== DATABASE IMPORT SCRIPT ===\n\n";

// Get all SQL files
$sqlFiles = glob($sqlDir . '/*.sql');

if (empty($sqlFiles)) {
    die("No SQL files found in $sqlDir\n");
}

echo "Found " . count($sqlFiles) . " SQL files\n\n";

foreach ($sqlFiles as $sqlFile) {
    $dbName = basename($sqlFile, '.sql');
    
    echo "Processing: $dbName\n";
    
    try {
        // Connect to MySQL
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database
        echo "  - Creating database...\n";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Select database
        $pdo->exec("USE `$dbName`");
        
        // Read SQL file
        echo "  - Reading SQL file...\n";
        $sql = file_get_contents($sqlFile);
        
        if ($sql === false) {
            echo "  ERROR: Could not read file\n\n";
            continue;
        }
        
        // Execute SQL
        echo "  - Executing SQL statements...\n";
        $pdo->exec($sql);
        
        echo "  SUCCESS!\n\n";
        
    } catch (PDOException $e) {
        echo "  ERROR: " . $e->getMessage() . "\n\n";
    }
}

echo "=== IMPORT COMPLETE ===\n";
echo "\nDatabases created:\n";

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $result = $pdo->query("SHOW DATABASES LIKE 'cms_%'");
    
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        echo "  - " . $row[0] . "\n";
    }
} catch (PDOException $e) {
    echo "Could not list databases: " . $e->getMessage() . "\n";
}
