-- Simple Purchase Receipts Table
-- Database: asahi_inventories
-- Purpose: Upload dan simpan kwitansi pembelian untuk traceability

USE asahi_inventories;

-- Table: purchase_receipts (simple - hanya upload file)
CREATE TABLE IF NOT EXISTS purchase_receipts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    file_path VARCHAR(255) NOT NULL COMMENT 'Path file kwitansi (relative to webroot)',
    file_name VARCHAR(255) NOT NULL COMMENT 'Nama file asli',
    file_size INT(11) NULL COMMENT 'Ukuran file dalam bytes',
    file_type VARCHAR(50) NULL COMMENT 'MIME type (image/jpeg, application/pdf, etc)',
    upload_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal upload',
    description TEXT NULL COMMENT 'Deskripsi/catatan kwitansi',
    supplier_id INT(11) NULL COMMENT 'FK ke suppliers (optional)',
    total_amount DECIMAL(15,2) NULL COMMENT 'Total nilai pembelian (optional)',
    created DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_upload_date (upload_date),
    KEY idx_supplier (supplier_id),
    CONSTRAINT fk_purchase_receipts_supplier 
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabel untuk upload dan simpan kwitansi pembelian';

-- Verify structure
DESCRIBE purchase_receipts;

-- Sample data (optional)
-- INSERT INTO purchase_receipts (file_path, file_name, file_size, file_type, description, supplier_id, total_amount) VALUES
-- ('uploads/receipts/2025/11/kwitansi_001.pdf', 'Kwitansi Pembelian Sparepart Nov 2025.pdf', 524288, 'application/pdf', 'Pembelian oli dan filter', 1, 1500000.00),
-- ('uploads/receipts/2025/11/kwitansi_002.jpg', 'Nota Toko ABC.jpg', 256000, 'image/jpeg', 'Pembelian tools', 2, 750000.00);

SELECT 'Purchase Receipts table created successfully!' AS Status;
