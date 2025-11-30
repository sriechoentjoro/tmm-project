-- Phase 3-4 LPK Registration Wizard - Simplified Migration
-- Only create email_verification_tokens table
-- Users table already has required columns: is_active, institution_id, institution_type

USE cms_authentication_authorization;

-- Create email verification tokens table
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

-- Verification query
SELECT 'email_verification_tokens created' AS status;
DESCRIBE email_verification_tokens;
