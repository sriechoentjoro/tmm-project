-- Phase 3-4: LPK Registration Wizard - Database Migration
-- Date: December 1, 2025
-- Purpose: Email verification and token management

USE cms_authentication_authorization;

-- ============================================
-- 1. Create email_verification_tokens table
-- ============================================

CREATE TABLE IF NOT EXISTS email_verification_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL COMMENT 'Email address for verification',
    token VARCHAR(64) NOT NULL UNIQUE COMMENT '64-character unique token',
    token_type ENUM('email_verification', 'password_reset') NOT NULL DEFAULT 'email_verification' COMMENT 'Type of token',
    is_used TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = not used, 1 = already used',
    used_at DATETIME NULL COMMENT 'When token was used',
    expires_at DATETIME NOT NULL COMMENT 'Token expiration datetime',
    created DATETIME NOT NULL COMMENT 'Token creation datetime',
    
    INDEX idx_token (token),
    INDEX idx_email (user_email),
    INDEX idx_expires (expires_at),
    INDEX idx_used (is_used),
    INDEX idx_type (token_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores email verification and password reset tokens';

-- ============================================
-- 2. Update users table (add LPK/Special Skill references)
-- ============================================

-- Check if columns exist before adding
SET @query = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS 
     WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'vocational_training_institution_id') = 0,
    'ALTER TABLE users 
     ADD COLUMN vocational_training_institution_id INT NULL COMMENT "Reference to LPK institution" AFTER role',
    'SELECT "Column vocational_training_institution_id already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS 
     WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'special_skill_support_institution_id') = 0,
    'ALTER TABLE users 
     ADD COLUMN special_skill_support_institution_id INT NULL COMMENT "Reference to Special Skill institution" AFTER vocational_training_institution_id',
    'SELECT "Column special_skill_support_institution_id already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS 
     WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'is_active') = 0,
    'ALTER TABLE users 
     ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT "0 = inactive, 1 = active" AFTER email',
    'SELECT "Column is_active already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add indexes if not exist
SET @query = IF(
    (SELECT COUNT(*) FROM information_schema.STATISTICS 
     WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
     AND TABLE_NAME = 'users' 
     AND INDEX_NAME = 'idx_lpk') = 0,
    'ALTER TABLE users ADD INDEX idx_lpk (vocational_training_institution_id)',
    'SELECT "Index idx_lpk already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    (SELECT COUNT(*) FROM information_schema.STATISTICS 
     WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
     AND TABLE_NAME = 'users' 
     AND INDEX_NAME = 'idx_special_skill') = 0,
    'ALTER TABLE users ADD INDEX idx_special_skill (special_skill_support_institution_id)',
    'SELECT "Index idx_special_skill already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    (SELECT COUNT(*) FROM information_schema.STATISTICS 
     WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
     AND TABLE_NAME = 'users' 
     AND INDEX_NAME = 'idx_active') = 0,
    'ALTER TABLE users ADD INDEX idx_active (is_active)',
    'SELECT "Index idx_active already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 3. Verify table structure
-- ============================================

SELECT 
    'email_verification_tokens' AS table_name,
    COUNT(*) AS column_count
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
AND TABLE_NAME = 'email_verification_tokens'
UNION ALL
SELECT 
    'users' AS table_name,
    COUNT(*) AS column_count
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
AND TABLE_NAME = 'users';

-- Show indexes
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
AND TABLE_NAME IN ('email_verification_tokens', 'users')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- ============================================
-- 4. Test data (optional - for development only)
-- ============================================

-- Sample token (expires in 24 hours)
-- INSERT INTO email_verification_tokens 
-- (user_email, token, token_type, is_used, expires_at, created)
-- VALUES 
-- ('test@example.com', 
--  '1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef', 
--  'email_verification', 
--  0, 
--  DATE_ADD(NOW(), INTERVAL 24 HOUR), 
--  NOW());

-- ============================================
-- MIGRATION COMPLETE
-- ============================================

SELECT 
    'Phase 3-4 Migration Complete' AS status,
    NOW() AS completed_at,
    'email_verification_tokens table created' AS table_1,
    'users table updated with institution references' AS table_2,
    'All indexes created successfully' AS indexes;
