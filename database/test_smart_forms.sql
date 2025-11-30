-- Test Table for Custom Bake Templates
-- Includes various field types to test smart field detection

DROP TABLE IF EXISTS test_smart_forms;

CREATE TABLE test_smart_forms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    
    -- Basic fields
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    
    -- File upload fields (will trigger multipart form)
    image_url VARCHAR(255) COMMENT 'Will be detected as image upload',
    photo_path VARCHAR(255) COMMENT 'Will be detected as image upload',
    file_path VARCHAR(255) COMMENT 'Will be detected as file upload',
    attachment VARCHAR(255) COMMENT 'Will be detected as file upload',
    
    -- Date fields (will trigger datepicker)
    purchase_date DATE COMMENT 'Will use datepicker',
    delivery_date DATE COMMENT 'Will use datepicker',
    tanggal_lahir DATE COMMENT 'Will use datepicker (Indonesian)',
    
    -- Email field (will trigger email validation)
    email VARCHAR(100) COMMENT 'Will have email validation',
    contact_email VARCHAR(100) COMMENT 'Will have email validation',
    
    -- Japanese input fields
    name_katakana VARCHAR(100) COMMENT 'Will use kana.js for katakana',
    name_hiragana VARCHAR(100) COMMENT 'Will use kana.js for hiragana',
    
    -- Foreign key (will be dropdown)
    supplier_id INT COMMENT 'Will be dropdown select',
    storage_id INT COMMENT 'Will be dropdown select',
    
    -- Numeric fields
    quantity INT DEFAULT 0,
    price DECIMAL(10,2) DEFAULT 0.00,
    
    -- Timestamps
    created DATETIME,
    modified DATETIME
    
    -- No foreign key constraints untuk test table
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO test_smart_forms (title, description, is_active, quantity, price, created, modified) VALUES
('Sample 1', 'Test description', 1, 10, 100.50, NOW(), NOW()),
('Sample 2', 'Another test', 1, 5, 50.00, NOW(), NOW());

-- Verification query
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'asahi_inventories' 
  AND TABLE_NAME = 'test_smart_forms'
ORDER BY ORDINAL_POSITION;
