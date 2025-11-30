#!/usr/bin/env php
<?php
/**
 * Migration Runner untuk Purchase Receipts System
 * Usage: php run_migration.php
 */

// Database configuration
$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '', // XAMPP default
    'database' => 'asahi_inventories',
    'charset' => 'utf8mb4'
];

// SQL file path
$sqlFile = __DIR__ . '/purchase_receipts_system.sql';

echo "========================================\n";
echo "Purchase Receipts System - Migration\n";
echo "========================================\n\n";

if (!file_exists($sqlFile)) {
    die("ERROR: SQL file not found: $sqlFile\n");
}

echo "Connecting to database...\n";
echo "Host: {$config['host']}\n";
echo "Database: {$config['database']}\n\n";

try {
    // Create PDO connection
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "✓ Connected successfully!\n\n";
    
    // Select database
    $pdo->exec("USE {$config['database']}");
    echo "✓ Using database: {$config['database']}\n\n";
    
    // Read SQL file
    echo "Reading SQL file...\n";
    $sql = file_get_contents($sqlFile);
    
    // Split by delimiter change and process
    $parts = preg_split('/^DELIMITER\s+(\S+)\s*$/m', $sql, -1, PREG_SPLIT_DELIM_CAPTURE);
    
    $currentDelimiter = ';';
    $statements = [];
    
    for ($i = 0; $i < count($parts); $i++) {
        if ($i % 2 == 1) {
            // This is a delimiter definition
            $currentDelimiter = trim($parts[$i]);
        } else {
            // This is SQL content
            $content = trim($parts[$i]);
            if (empty($content)) continue;
            
            // Split by current delimiter
            $queries = array_filter(
                array_map('trim', explode($currentDelimiter, $content)),
                function($q) { return !empty($q); }
            );
            
            $statements = array_merge($statements, $queries);
        }
    }
    
    echo "Found " . count($statements) . " SQL statements\n\n";
    echo "Executing migration...\n";
    echo "----------------------------------------\n";
    
    $executed = 0;
    $errors = 0;
    
    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        // Skip comments
        if (preg_match('/^(--|#|\/\*)/', $statement)) continue;
        
        try {
            $pdo->exec($statement);
            $executed++;
            
            // Show progress for important statements
            if (stripos($statement, 'CREATE TABLE') === 0) {
                preg_match('/CREATE TABLE.*?`(\w+)`/i', $statement, $matches);
                $tableName = isset($matches[1]) ? $matches[1] : 'unknown';
                echo "✓ Created table: $tableName\n";
            } elseif (stripos($statement, 'ALTER TABLE') === 0) {
                preg_match('/ALTER TABLE\s+`?(\w+)`?/i', $statement, $matches);
                $tableName = isset($matches[1]) ? $matches[1] : 'unknown';
                echo "✓ Altered table: $tableName\n";
            } elseif (stripos($statement, 'INSERT INTO') === 0) {
                preg_match('/INSERT INTO\s+`?(\w+)`?/i', $statement, $matches);
                $tableName = isset($matches[1]) ? $matches[1] : 'unknown';
                echo "✓ Inserted data into: $tableName\n";
            } elseif (stripos($statement, 'CREATE TRIGGER') !== false) {
                preg_match('/CREATE TRIGGER\s+`?(\w+)`?/i', $statement, $matches);
                $triggerName = isset($matches[1]) ? $matches[1] : 'unknown';
                echo "✓ Created trigger: $triggerName\n";
            } elseif (stripos($statement, 'DROP TRIGGER') !== false) {
                preg_match('/DROP TRIGGER.*?`(\w+)`/i', $statement, $matches);
                $triggerName = isset($matches[1]) ? $matches[1] : 'unknown';
                echo "  Dropped trigger: $triggerName\n";
            }
            
        } catch (PDOException $e) {
            $errors++;
            $preview = substr($statement, 0, 80);
            echo "✗ Error in statement " . ($index + 1) . ":\n";
            echo "  SQL: $preview...\n";
            echo "  Error: " . $e->getMessage() . "\n";
            
            // Don't stop on duplicate key or already exists errors
            if (stripos($e->getMessage(), 'Duplicate') !== false ||
                stripos($e->getMessage(), 'already exists') !== false ||
                stripos($e->getMessage(), 'check that column') !== false) {
                echo "  (Continuing...)\n";
            }
        }
    }
    
    echo "----------------------------------------\n\n";
    echo "Migration completed!\n";
    echo "Executed: $executed statements\n";
    echo "Errors: $errors\n\n";
    
    // Verify tables created
    echo "Verifying tables...\n";
    $tables = ['purchase_receipts', 'purchase_receipt_items', 'accounting_transactions', 'transaction_types', 'chart_of_accounts'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' NOT found\n";
        }
    }
    
    // Check stock_outgoings alteration
    $stmt = $pdo->query("SHOW COLUMNS FROM stock_outgoings LIKE 'purchase_receipt_id'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Column 'stock_outgoings.purchase_receipt_id' added\n";
    } else {
        echo "✗ Column 'stock_outgoings.purchase_receipt_id' NOT found\n";
    }
    
    echo "\n✓ Migration finished successfully!\n\n";
    
} catch (PDOException $e) {
    echo "\n✗ DATABASE ERROR:\n";
    echo $e->getMessage() . "\n\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ ERROR:\n";
    echo $e->getMessage() . "\n\n";
    exit(1);
}
