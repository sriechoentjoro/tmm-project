-- Alter purchase_receipts table
-- Add missing columns for file upload metadata
-- NO FOREIGN KEY - untuk fleksibilitas bindingModel di controller

USE asahi_inventories;

-- Add file metadata columns
ALTER TABLE purchase_receipts
  MODIFY COLUMN file_path VARCHAR(255) NULL COMMENT 'Path file kwitansi',
  ADD COLUMN file_name VARCHAR(255) NULL COMMENT 'Nama file asli' AFTER file_path,
  ADD COLUMN file_size INT(11) NULL COMMENT 'Ukuran file (bytes)' AFTER file_name,
  ADD COLUMN file_type VARCHAR(50) NULL COMMENT 'MIME type' AFTER file_size,
  ADD COLUMN upload_date DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal upload' AFTER file_type,
  ADD COLUMN description TEXT NULL COMMENT 'Deskripsi kwitansi' AFTER upload_date,
  ADD COLUMN created DATETIME NULL DEFAULT CURRENT_TIMESTAMP AFTER description,
  ADD COLUMN modified DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created;

-- Add indexes only (NO FK constraints)
ALTER TABLE purchase_receipts
  ADD INDEX idx_upload_date (upload_date),
  ADD INDEX idx_supplier (supplier_id),
  ADD INDEX idx_purchase_date (purchase_date);

-- Verify structure
DESCRIBE purchase_receipts;

SELECT 'Purchase Receipts table updated successfully (NO FK)!' AS Status;
