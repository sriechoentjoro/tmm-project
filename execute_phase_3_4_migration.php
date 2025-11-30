<?php
/**
 * Phase 3-4: Database Migration Executor
 * Run with: php execute_phase_3_4_migration.php
 */

require 'config/bootstrap.php';

use Cake\Datasource\ConnectionManager;

echo "Phase 3-4: LPK Registration Wizard - Database Migration\n";
echo str_repeat("=", 60) . "\n\n";

// Get authentication database connection
$connection = ConnectionManager::get('cms_authentication_authorization');

echo "Connected to database: cms_authentication_authorization\n\n";

// ============================================
// 1. Create email_verification_tokens table
// ============================================

echo "Step 1: Creating email_verification_tokens table...\n";

$sql = "
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
COMMENT='Stores email verification and password reset tokens'
";

try {
    $connection->execute($sql);
    echo "✓ email_verification_tokens table created successfully\n\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "✓ email_verification_tokens table already exists\n\n";
    } else {
        echo "✗ Error creating table: " . $e->getMessage() . "\n\n";
        exit(1);
    }
}

// ============================================
// 2. Add columns to users table
// ============================================

echo "Step 2: Updating users table...\n";

// Check and add vocational_training_institution_id
$checkColumn = "SELECT COUNT(*) as count FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
                AND TABLE_NAME = 'users' 
                AND COLUMN_NAME = 'vocational_training_institution_id'";
$result = $connection->execute($checkColumn)->fetch('assoc');

if ($result['count'] == 0) {
    $sql = "ALTER TABLE users ADD COLUMN vocational_training_institution_id INT NULL 
            COMMENT 'Reference to LPK institution' AFTER role";
    try {
        $connection->execute($sql);
        echo "✓ Added vocational_training_institution_id column\n";
    } catch (Exception $e) {
        echo "✗ Error adding column: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ vocational_training_institution_id column already exists\n";
}

// Check and add special_skill_support_institution_id
$checkColumn = "SELECT COUNT(*) as count FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
                AND TABLE_NAME = 'users' 
                AND COLUMN_NAME = 'special_skill_support_institution_id'";
$result = $connection->execute($checkColumn)->fetch('assoc');

if ($result['count'] == 0) {
    $sql = "ALTER TABLE users ADD COLUMN special_skill_support_institution_id INT NULL 
            COMMENT 'Reference to Special Skill institution' AFTER vocational_training_institution_id";
    try {
        $connection->execute($sql);
        echo "✓ Added special_skill_support_institution_id column\n";
    } catch (Exception $e) {
        echo "✗ Error adding column: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ special_skill_support_institution_id column already exists\n";
}

// Check and add is_active
$checkColumn = "SELECT COUNT(*) as count FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
                AND TABLE_NAME = 'users' 
                AND COLUMN_NAME = 'is_active'";
$result = $connection->execute($checkColumn)->fetch('assoc');

if ($result['count'] == 0) {
    $sql = "ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 
            COMMENT '0 = inactive, 1 = active' AFTER email";
    try {
        $connection->execute($sql);
        echo "✓ Added is_active column\n";
    } catch (Exception $e) {
        echo "✗ Error adding column: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ is_active column already exists\n";
}

echo "\n";

// ============================================
// 3. Add indexes
// ============================================

echo "Step 3: Creating indexes...\n";

// Index for vocational_training_institution_id
$checkIndex = "SELECT COUNT(*) as count FROM information_schema.STATISTICS 
               WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
               AND TABLE_NAME = 'users' 
               AND INDEX_NAME = 'idx_lpk'";
$result = $connection->execute($checkIndex)->fetch('assoc');

if ($result['count'] == 0) {
    try {
        $connection->execute("ALTER TABLE users ADD INDEX idx_lpk (vocational_training_institution_id)");
        echo "✓ Created idx_lpk index\n";
    } catch (Exception $e) {
        echo "✗ Error creating index: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ idx_lpk index already exists\n";
}

// Index for special_skill_support_institution_id
$checkIndex = "SELECT COUNT(*) as count FROM information_schema.STATISTICS 
               WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
               AND TABLE_NAME = 'users' 
               AND INDEX_NAME = 'idx_special_skill'";
$result = $connection->execute($checkIndex)->fetch('assoc');

if ($result['count'] == 0) {
    try {
        $connection->execute("ALTER TABLE users ADD INDEX idx_special_skill (special_skill_support_institution_id)");
        echo "✓ Created idx_special_skill index\n";
    } catch (Exception $e) {
        echo "✗ Error creating index: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ idx_special_skill index already exists\n";
}

// Index for is_active
$checkIndex = "SELECT COUNT(*) as count FROM information_schema.STATISTICS 
               WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
               AND TABLE_NAME = 'users' 
               AND INDEX_NAME = 'idx_active'";
$result = $connection->execute($checkIndex)->fetch('assoc');

if ($result['count'] == 0) {
    try {
        $connection->execute("ALTER TABLE users ADD INDEX idx_active (is_active)");
        echo "✓ Created idx_active index\n";
    } catch (Exception $e) {
        echo "✗ Error creating index: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ idx_active index already exists\n";
}

echo "\n";

// ============================================
// 4. Verification
// ============================================

echo "Step 4: Verifying migration...\n\n";

$sql = "SELECT COUNT(*) as count FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
        AND TABLE_NAME = 'email_verification_tokens'";
$result = $connection->execute($sql)->fetch('assoc');
echo "✓ email_verification_tokens has " . $result['count'] . " columns\n";

$sql = "SELECT COUNT(*) as count FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
        AND TABLE_NAME = 'users'";
$result = $connection->execute($sql)->fetch('assoc');
echo "✓ users table has " . $result['count'] . " columns\n";

$sql = "SELECT COUNT(*) as count FROM information_schema.STATISTICS 
        WHERE TABLE_SCHEMA = 'cms_authentication_authorization' 
        AND TABLE_NAME IN ('email_verification_tokens', 'users')";
$result = $connection->execute($sql)->fetch('assoc');
echo "✓ Total indexes: " . $result['count'] . "\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "Migration completed successfully!\n";
echo str_repeat("=", 60) . "\n";
