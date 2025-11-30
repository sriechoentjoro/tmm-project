-- ============================================================================
-- PURCHASE RECEIPTS SYSTEM - Database Schema
-- ============================================================================
-- Created: 2025-11-04
-- Description: Sistem kwitansi pembelian inventory yang terintegrasi dengan
--              stock_incomings dan accounting system
-- ============================================================================

USE asahi_inventories;

-- ============================================================================
-- TABLE: purchase_receipts
-- Description: Master table untuk kwitansi pembelian
-- ============================================================================
CREATE TABLE IF NOT EXISTS `purchase_receipts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `receipt_number` VARCHAR(50) NOT NULL COMMENT 'Nomor kwitansi unik',
  `receipt_date` DATE NOT NULL COMMENT 'Tanggal kwitansi',
  `supplier_name` VARCHAR(255) NOT NULL COMMENT 'Nama supplier/vendor',
  `supplier_address` TEXT NULL COMMENT 'Alamat supplier',
  `supplier_phone` VARCHAR(50) NULL COMMENT 'Telepon supplier',
  `file_path` VARCHAR(500) NOT NULL COMMENT 'Path file kwitansi di server',
  `file_name` VARCHAR(255) NOT NULL COMMENT 'Nama file asli',
  `file_size` INT(11) NULL COMMENT 'Ukuran file dalam bytes',
  `file_type` VARCHAR(100) NULL COMMENT 'MIME type file (image/jpeg, application/pdf, etc)',
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total nilai kwitansi',
  `tax_amount` DECIMAL(15,2) NULL DEFAULT 0.00 COMMENT 'Nilai pajak (PPN)',
  `discount_amount` DECIMAL(15,2) NULL DEFAULT 0.00 COMMENT 'Nilai diskon',
  `notes` TEXT NULL COMMENT 'Catatan tambahan',
  `status` ENUM('draft','submitted','approved','rejected','cancelled') DEFAULT 'draft' COMMENT 'Status kwitansi',
  `approved_by` INT(11) UNSIGNED NULL COMMENT 'User ID yang approve',
  `approved_at` DATETIME NULL COMMENT 'Waktu approval',
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `idx_receipt_date` (`receipt_date`),
  KEY `idx_supplier_name` (`supplier_name`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Master kwitansi pembelian inventory';

-- ============================================================================
-- TABLE: purchase_receipt_items
-- Description: Detail items dalam kwitansi (one receipt, many items)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `purchase_receipt_items` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_receipt_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK ke purchase_receipts',
  `inventory_id` INT(11) UNSIGNED NULL COMMENT 'FK ke inventories (nullable saat draft)',
  `item_description` VARCHAR(500) NOT NULL COMMENT 'Deskripsi item',
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00 COMMENT 'Jumlah barang',
  `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Harga satuan',
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal (qty * price)',
  `unit` VARCHAR(50) NULL COMMENT 'Satuan (pcs, box, unit, dll)',
  `notes` TEXT NULL COMMENT 'Catatan item',
  `line_number` INT(11) NOT NULL DEFAULT 1 COMMENT 'Urutan item dalam kwitansi',
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_purchase_receipt_id` (`purchase_receipt_id`),
  KEY `idx_inventory_id` (`inventory_id`),
  KEY `idx_line_number` (`line_number`),
  CONSTRAINT `fk_pri_purchase_receipt` 
    FOREIGN KEY (`purchase_receipt_id`) 
    REFERENCES `purchase_receipts` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Detail items kwitansi pembelian';

-- ============================================================================
-- TABLE: accounting_transactions
-- Description: Transaksi akuntansi yang di-trigger dari stock_outgoings
-- ============================================================================
CREATE TABLE IF NOT EXISTS `accounting_transactions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_number` VARCHAR(50) NOT NULL COMMENT 'Nomor transaksi unik',
  `transaction_date` DATE NOT NULL COMMENT 'Tanggal transaksi',
  `transaction_type_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK ke transaction_types (debit/credit)',
  `coa_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK ke chart_of_accounts',
  `debit_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Nilai debit',
  `credit_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Nilai kredit',
  `description` TEXT NULL COMMENT 'Deskripsi transaksi',
  `reference_type` VARCHAR(100) NULL COMMENT 'Tipe referensi (StockIncomings, PurchaseReceipts, dll)',
  `reference_id` INT(11) UNSIGNED NULL COMMENT 'ID referensi ke table lain',
  `stock_incoming_id` INT(11) UNSIGNED NULL COMMENT 'FK ke stock_incomings (untuk trigger)',
  `purchase_receipt_id` INT(11) UNSIGNED NULL COMMENT 'FK ke purchase_receipts',
  `is_posted` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Apakah sudah di-posting',
  `posted_at` DATETIME NULL COMMENT 'Waktu posting',
  `posted_by` INT(11) UNSIGNED NULL COMMENT 'User ID yang posting',
  `notes` TEXT NULL COMMENT 'Catatan tambahan',
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
  KEY `idx_is_posted` (`is_posted`),
  CONSTRAINT `fk_at_purchase_receipt` 
    FOREIGN KEY (`purchase_receipt_id`) 
    REFERENCES `purchase_receipts` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Transaksi akuntansi dari stock movements';

-- ============================================================================
-- ALTER TABLE: stock_incomings
-- Description: Tambahkan foreign key ke purchase_receipts
-- ============================================================================
ALTER TABLE `stock_incomings`
  ADD COLUMN `purchase_receipt_id` INT(11) UNSIGNED NULL COMMENT 'FK ke purchase_receipts' AFTER `inventory_id`,
  ADD COLUMN `receipt_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kwitansi sudah diverifikasi' AFTER `purchase_receipt_id`,
  ADD KEY `idx_purchase_receipt_id` (`purchase_receipt_id`),
  ADD CONSTRAINT `fk_si_purchase_receipt` 
    FOREIGN KEY (`purchase_receipt_id`) 
    REFERENCES `purchase_receipts` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- ============================================================================
-- HELPER TABLES: transaction_types & chart_of_accounts (if not exist)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `transaction_types` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(20) NOT NULL COMMENT 'Kode tipe (DBT, CRT, dll)',
  `name` VARCHAR(100) NOT NULL COMMENT 'Nama tipe transaksi',
  `description` TEXT NULL,
  `is_debit` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Debit, 0=Credit',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tipe transaksi akuntansi';

CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_code` VARCHAR(50) NOT NULL COMMENT 'Kode akun (ex: 1-1000)',
  `account_name` VARCHAR(255) NOT NULL COMMENT 'Nama akun',
  `account_type` ENUM('asset','liability','equity','revenue','expense') NOT NULL COMMENT 'Tipe akun',
  `parent_id` INT(11) UNSIGNED NULL COMMENT 'Parent account untuk sub-akun',
  `level` INT(11) NOT NULL DEFAULT 1 COMMENT 'Level hierarchy',
  `normal_balance` ENUM('debit','credit') NOT NULL COMMENT 'Saldo normal',
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_code` (`account_code`),
  KEY `idx_account_type` (`account_type`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Chart of Accounts (Daftar Akun)';

-- ============================================================================
-- SAMPLE DATA: transaction_types
-- ============================================================================
INSERT INTO `transaction_types` (`code`, `name`, `description`, `is_debit`, `is_active`) VALUES
('DBT', 'Debit', 'Transaksi Debit (menambah aset/expense)', 1, 1),
('CRT', 'Credit', 'Transaksi Kredit (mengurangi aset, menambah liability)', 0, 1),
('PURCHASE', 'Pembelian', 'Transaksi pembelian inventory', 1, 1),
('PAYMENT', 'Pembayaran', 'Transaksi pembayaran', 0, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ============================================================================
-- SAMPLE DATA: chart_of_accounts
-- ============================================================================
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

-- ============================================================================
-- TRIGGERS: Auto-calculate subtotal pada purchase_receipt_items
-- ============================================================================
DELIMITER $$

DROP TRIGGER IF EXISTS `trg_pri_before_insert_calculate_subtotal`$$
CREATE TRIGGER `trg_pri_before_insert_calculate_subtotal`
BEFORE INSERT ON `purchase_receipt_items`
FOR EACH ROW
BEGIN
  SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END$$

DROP TRIGGER IF EXISTS `trg_pri_before_update_calculate_subtotal`$$
CREATE TRIGGER `trg_pri_before_update_calculate_subtotal`
BEFORE UPDATE ON `purchase_receipt_items`
FOR EACH ROW
BEGIN
  SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END$$

-- ============================================================================
-- TRIGGERS: Auto-update total_amount pada purchase_receipts
-- ============================================================================
DROP TRIGGER IF EXISTS `trg_pri_after_insert_update_receipt_total`$$
CREATE TRIGGER `trg_pri_after_insert_update_receipt_total`
AFTER INSERT ON `purchase_receipt_items`
FOR EACH ROW
BEGIN
  UPDATE `purchase_receipts` 
  SET `total_amount` = (
    SELECT COALESCE(SUM(subtotal), 0) 
    FROM `purchase_receipt_items` 
    WHERE `purchase_receipt_id` = NEW.purchase_receipt_id
  )
  WHERE `id` = NEW.purchase_receipt_id;
END$$

DROP TRIGGER IF EXISTS `trg_pri_after_update_update_receipt_total`$$
CREATE TRIGGER `trg_pri_after_update_update_receipt_total`
AFTER UPDATE ON `purchase_receipt_items`
FOR EACH ROW
BEGIN
  UPDATE `purchase_receipts` 
  SET `total_amount` = (
    SELECT COALESCE(SUM(subtotal), 0) 
    FROM `purchase_receipt_items` 
    WHERE `purchase_receipt_id` = NEW.purchase_receipt_id
  )
  WHERE `id` = NEW.purchase_receipt_id;
END$$

DROP TRIGGER IF EXISTS `trg_pri_after_delete_update_receipt_total`$$
CREATE TRIGGER `trg_pri_after_delete_update_receipt_total`
AFTER DELETE ON `purchase_receipt_items`
FOR EACH ROW
BEGIN
  UPDATE `purchase_receipts` 
  SET `total_amount` = (
    SELECT COALESCE(SUM(subtotal), 0) 
    FROM `purchase_receipt_items` 
    WHERE `purchase_receipt_id` = OLD.purchase_receipt_id
  )
  WHERE `id` = OLD.purchase_receipt_id;
END$$

DELIMITER ;

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
