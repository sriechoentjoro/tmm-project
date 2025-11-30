-- Quick Import Script - Purchase Receipts System
-- Execute this via: d:\xampp\mysql\bin\mysql.exe -u root asahi_inventories < quick_import.sql

USE asahi_inventories;

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Purchase Receipt Items (jika belum ada)
CREATE TABLE IF NOT EXISTS `purchase_receipt_items` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_receipt_id` INT(11) UNSIGNED NOT NULL,
  `inventory_id` INT(11) UNSIGNED NULL,
  `item_description` VARCHAR(500) NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `unit` VARCHAR(50) NULL,
  `notes` TEXT NULL,
  `line_number` INT(11) NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_purchase_receipt_id` (`purchase_receipt_id`),
  KEY `idx_inventory_id` (`inventory_id`),
  KEY `idx_line_number` (`line_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Transaction Types (jika belum ada)
CREATE TABLE IF NOT EXISTS `transaction_types` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(20) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `is_debit` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Chart of Accounts (jika belum ada)
CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_code` VARCHAR(50) NOT NULL,
  `account_name` VARCHAR(255) NOT NULL,
  `account_type` ENUM('asset','liability','equity','revenue','expense') NOT NULL,
  `parent_id` INT(11) UNSIGNED NULL,
  `level` INT(11) NOT NULL DEFAULT 1,
  `normal_balance` ENUM('debit','credit') NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_code` (`account_code`),
  KEY `idx_account_type` (`account_type`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Accounting Transactions (jika belum ada)
CREATE TABLE IF NOT EXISTS `accounting_transactions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_number` VARCHAR(50) NOT NULL,
  `transaction_date` DATE NOT NULL,
  `transaction_type_id` INT(11) UNSIGNED NOT NULL,
  `coa_id` INT(11) UNSIGNED NOT NULL,
  `debit_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `credit_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `description` TEXT NULL,
  `reference_type` VARCHAR(100) NULL,
  `reference_id` INT(11) UNSIGNED NULL,
  `stock_incoming_id` INT(11) UNSIGNED NULL,
  `purchase_receipt_id` INT(11) UNSIGNED NULL,
  `is_posted` TINYINT(1) NOT NULL DEFAULT 0,
  `posted_at` DATETIME NULL,
  `posted_by` INT(11) UNSIGNED NULL,
  `notes` TEXT NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_number` (`transaction_number`),
  KEY `idx_transaction_date` (`transaction_date`),
  KEY `idx_transaction_type_id` (`transaction_type_id`),
  KEY `idx_coa_id` (`coa_id`),
  KEY `idx_stock_incoming_id` (`stock_incoming_id`),
  KEY `idx_purchase_receipt_id` (`purchase_receipt_id`),
  KEY `idx_reference` (`reference_type`, `reference_id`),
  KEY `idx_is_posted` (`is_posted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Insert sample data
INSERT INTO `transaction_types` (`code`, `name`, `description`, `is_debit`, `is_active`) VALUES
('DBT', 'Debit', 'Transaksi Debit', 1, 1),
('CRT', 'Credit', 'Transaksi Kredit', 0, 1),
('PURCHASE', 'Pembelian', 'Transaksi pembelian inventory', 1, 1),
('PAYMENT', 'Pembayaran', 'Transaksi pembayaran', 0, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `chart_of_accounts` (`account_code`, `account_name`, `account_type`, `parent_id`, `level`, `normal_balance`, `description`) VALUES
('1-0000', 'ASET', 'asset', NULL, 1, 'debit', 'Aset Perusahaan'),
('1-1000', 'Kas & Bank', 'asset', NULL, 2, 'debit', 'Kas dan setara kas'),
('1-2000', 'Persediaan', 'asset', NULL, 2, 'debit', 'Persediaan Barang'),
('1-2100', 'Persediaan Inventory', 'asset', NULL, 3, 'debit', 'Persediaan dari pembelian'),
('2-0000', 'KEWAJIBAN', 'liability', NULL, 1, 'credit', 'Kewajiban Perusahaan'),
('2-1000', 'Hutang Usaha', 'liability', NULL, 2, 'credit', 'Hutang kepada supplier'),
('5-0000', 'BEBAN', 'expense', NULL, 1, 'debit', 'Beban Operasional'),
('5-1000', 'Beban Pembelian', 'expense', NULL, 2, 'debit', 'Beban pembelian inventory')
ON DUPLICATE KEY UPDATE `account_name` = VALUES(`account_name`);

SELECT 'Tables created successfully!' AS status;
