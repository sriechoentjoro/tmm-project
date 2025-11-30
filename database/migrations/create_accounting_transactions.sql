-- Create accounting_transactions table
-- Simple table untuk pencatatan transaksi akuntansi
-- NO FK constraint untuk fleksibilitas

USE asahi_inventories;

CREATE TABLE IF NOT EXISTS accounting_transactions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    transaction_date DATE NOT NULL COMMENT 'Tanggal transaksi',
    description TEXT NULL COMMENT 'Deskripsi transaksi',
    debit_account VARCHAR(100) NULL COMMENT 'Akun debit (misal: Persediaan)',
    credit_account VARCHAR(100) NULL COMMENT 'Akun kredit (misal: Hutang Usaha)',
    amount DECIMAL(15,2) NOT NULL COMMENT 'Jumlah transaksi',
    reference_type VARCHAR(50) NULL COMMENT 'Tipe referensi: StockIncoming, StockOutgoing, dll',
    reference_id INT(11) NULL COMMENT 'ID referensi',
    receipt_file_path VARCHAR(255) NULL COMMENT 'Path ke file kwitansi',
    created DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_receipt (receipt_file_path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabel transaksi akuntansi dengan link ke kwitansi';

-- Verify
DESCRIBE accounting_transactions;

SELECT 'Accounting Transactions table created successfully!' AS Status;
