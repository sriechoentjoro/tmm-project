-- ============================================================================
-- ROLLBACK & FIX: Move purchase_receipt_id from stock_outgoings to stock_incomings
-- ============================================================================
-- Execute this if you accidentally added purchase_receipt_id to stock_outgoings
-- ============================================================================

USE asahi_inventories;

-- Check if stock_outgoings has the columns (akan error jika tidak ada, it's OK)
SET @check_so = (SELECT COUNT(*) 
                 FROM information_schema.COLUMNS 
                 WHERE TABLE_SCHEMA = 'asahi_inventories' 
                   AND TABLE_NAME = 'stock_outgoings' 
                   AND COLUMN_NAME = 'purchase_receipt_id');

-- Drop from stock_outgoings if exists
SET @drop_so_fk = IF(@check_so > 0, 
    'ALTER TABLE `stock_outgoings` DROP FOREIGN KEY `fk_so_purchase_receipt`',
    'SELECT "FK not exists in stock_outgoings" AS status');
PREPARE stmt FROM @drop_so_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @drop_so_col = IF(@check_so > 0,
    'ALTER TABLE `stock_outgoings` DROP COLUMN `purchase_receipt_id`, DROP COLUMN `receipt_verified`',
    'SELECT "Columns not exists in stock_outgoings" AS status');
PREPARE stmt FROM @drop_so_col;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if stock_incomings already has the columns
SET @check_si = (SELECT COUNT(*) 
                 FROM information_schema.COLUMNS 
                 WHERE TABLE_SCHEMA = 'asahi_inventories' 
                   AND TABLE_NAME = 'stock_incomings' 
                   AND COLUMN_NAME = 'purchase_receipt_id');

-- Add to stock_incomings if not exists
SET @add_si = IF(@check_si = 0,
    'ALTER TABLE `stock_incomings`
       ADD COLUMN `purchase_receipt_id` INT(11) UNSIGNED NULL COMMENT "FK ke purchase_receipts" AFTER `inventory_id`,
       ADD COLUMN `receipt_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT "Kwitansi sudah diverifikasi" AFTER `purchase_receipt_id`,
       ADD KEY `idx_purchase_receipt_id` (`purchase_receipt_id`),
       ADD CONSTRAINT `fk_si_purchase_receipt` 
         FOREIGN KEY (`purchase_receipt_id`) 
         REFERENCES `purchase_receipts` (`id`) 
         ON DELETE RESTRICT ON UPDATE CASCADE',
    'SELECT "Columns already exists in stock_incomings" AS status');
PREPARE stmt FROM @add_si;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify
SELECT 'stock_outgoings' AS table_name, 
       COUNT(*) AS has_purchase_receipt_id
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'asahi_inventories' 
  AND TABLE_NAME = 'stock_outgoings' 
  AND COLUMN_NAME = 'purchase_receipt_id'

UNION ALL

SELECT 'stock_incomings' AS table_name, 
       COUNT(*) AS has_purchase_receipt_id
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'asahi_inventories' 
  AND TABLE_NAME = 'stock_incomings' 
  AND COLUMN_NAME = 'purchase_receipt_id';

SELECT '✓ Migration completed: purchase_receipt_id moved to stock_incomings' AS result;
