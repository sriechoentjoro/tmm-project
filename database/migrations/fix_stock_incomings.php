#!/usr/bin/env php
<?php
/**
 * Quick Fix: Move purchase_receipt_id to stock_incomings
 */

$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '', // XAMPP default, ganti jika perlu
    'database' => 'asahi_inventories'
];

try {
    $pdo = new PDO("mysql:host={$config['host']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE {$config['database']}");
    
    echo "Connected to {$config['database']}\n\n";
    
    // Check stock_outgoings
    $stmt = $pdo->query("SHOW COLUMNS FROM stock_outgoings LIKE 'purchase_receipt_id'");
    $hasInOutgoing = $stmt->rowCount() > 0;
    
    if ($hasInOutgoing) {
        echo "Removing purchase_receipt_id from stock_outgoings...\n";
        try {
            $pdo->exec("ALTER TABLE stock_outgoings DROP FOREIGN KEY fk_so_purchase_receipt");
            echo "  ✓ Dropped foreign key\n";
        } catch (Exception $e) {
            echo "  ℹ FK might not exist: " . $e->getMessage() . "\n";
        }
        
        $pdo->exec("ALTER TABLE stock_outgoings DROP COLUMN purchase_receipt_id, DROP COLUMN receipt_verified");
        echo "  ✓ Dropped columns from stock_outgoings\n";
    } else {
        echo "✓ stock_outgoings: clean (no purchase_receipt_id)\n";
    }
    
    // Check stock_incomings
    $stmt = $pdo->query("SHOW COLUMNS FROM stock_incomings LIKE 'purchase_receipt_id'");
    $hasInIncoming = $stmt->rowCount() > 0;
    
    if (!$hasInIncoming) {
        echo "\nAdding purchase_receipt_id to stock_incomings...\n";
        $pdo->exec("
            ALTER TABLE stock_incomings
              ADD COLUMN purchase_receipt_id INT(11) UNSIGNED NULL COMMENT 'FK ke purchase_receipts' AFTER inventory_id,
              ADD COLUMN receipt_verified TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kwitansi sudah diverifikasi' AFTER purchase_receipt_id,
              ADD KEY idx_purchase_receipt_id (purchase_receipt_id),
              ADD CONSTRAINT fk_si_purchase_receipt 
                FOREIGN KEY (purchase_receipt_id) 
                REFERENCES purchase_receipts (id) 
                ON DELETE RESTRICT ON UPDATE CASCADE
        ");
        echo "  ✓ Added columns to stock_incomings\n";
        echo "  ✓ Added foreign key constraint\n";
    } else {
        echo "✓ stock_incomings: already has purchase_receipt_id\n";
    }
    
    echo "\n✓ Fix completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
