#!/usr/bin/env php
<?php
/**
 * Check Purchase Receipts System Tables
 */

try {
    $pdo = new PDO("mysql:host=localhost;dbname=asahi_inventories", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = [
        'purchase_receipts',
        'purchase_receipt_items',
        'accounting_transactions',
        'transaction_types',
        'chart_of_accounts'
    ];
    
    echo "Checking Purchase Receipts System Tables\n";
    echo str_repeat("=", 50) . "\n\n";
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        
        if ($exists) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "[✓] $table (rows: $count)\n";
        } else {
            echo "[✗] $table NOT FOUND\n";
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Checking stock_incomings columns\n";
    echo str_repeat("=", 50) . "\n\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM stock_incomings LIKE 'purchase_receipt_id'");
    if ($stmt->rowCount() > 0) {
        echo "[✓] stock_incomings.purchase_receipt_id EXISTS\n";
    } else {
        echo "[✗] stock_incomings.purchase_receipt_id NOT FOUND\n";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM stock_incomings LIKE 'receipt_verified'");
    if ($stmt->rowCount() > 0) {
        echo "[✓] stock_incomings.receipt_verified EXISTS\n";
    } else {
        echo "[✗] stock_incomings.receipt_verified NOT FOUND\n";
    }
    
    echo "\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. MySQL is running\n";
    echo "2. Database 'asahi_inventories' exists\n";
    echo "3. MySQL root password (default empty for XAMPP)\n";
    exit(1);
}
