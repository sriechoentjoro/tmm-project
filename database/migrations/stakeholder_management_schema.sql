-- =====================================================
-- Admin Stakeholder Management - Database Migrations
-- Phase 1: Core Schema Changes
-- Date: December 1, 2025
-- =====================================================

-- =====================================================
-- 1. ALTER USERS TABLE - Add Email Verification Fields
-- =====================================================

USE cms_authentication_authorization;

-- Add email verification and status fields
ALTER TABLE users 
    ADD COLUMN email_verified_at DATETIME NULL 
    AFTER email,
    ADD COLUMN status VARCHAR(50) DEFAULT 'pending_verification' 
    AFTER email_verified_at,
    ADD COLUMN verification_token VARCHAR(255) NULL 
    AFTER status,
    ADD COLUMN verification_token_expires DATETIME NULL 
    AFTER verification_token,
    ADD COLUMN temp_password VARCHAR(255) NULL COMMENT 'Temporary password sent via email'
    AFTER verification_token_expires;

-- Add indexes for performance
ALTER TABLE users 
    ADD INDEX idx_verification_token (verification_token),
    ADD INDEX idx_status (status),
    ADD INDEX idx_email_verified (email_verified_at);

-- Update existing admin users to active status
UPDATE users 
SET status = 'active', 
    email_verified_at = NOW() 
WHERE role = 'admin' AND status = 'pending_verification';

-- =====================================================
-- 2. ALTER VOCATIONAL TRAINING INSTITUTIONS TABLE
-- =====================================================

USE cms_tmm_stakeholders;

-- Add status field
ALTER TABLE vocational_training_institutions 
    ADD COLUMN status VARCHAR(50) DEFAULT 'pending_verification' 
    COMMENT 'pending_verification, verified, active, suspended, inactive'
    AFTER name;

-- Add indexes
ALTER TABLE vocational_training_institutions 
    ADD INDEX idx_status (status);

-- Update existing records to active
UPDATE vocational_training_institutions 
SET status = 'active' 
WHERE status IS NULL OR status = '';

-- =====================================================
-- 3. ALTER SPECIAL SKILL SUPPORT INSTITUTIONS TABLE
-- =====================================================

-- Add status field
ALTER TABLE special_skill_support_institutions 
    ADD COLUMN status VARCHAR(50) DEFAULT 'pending_verification' 
    COMMENT 'pending_verification, verified, active, suspended, inactive'
    AFTER name;

-- Add indexes
ALTER TABLE special_skill_support_institutions 
    ADD INDEX idx_status (status);

-- Update existing records to active
UPDATE special_skill_support_institutions 
SET status = 'active' 
WHERE status IS NULL OR status = '';

-- =====================================================
-- 4. ALTER ACCEPTANCE ORGANIZATIONS TABLE
-- =====================================================

USE cms_tmm_stakeholders;

-- Add status field (no email verification needed)
ALTER TABLE acceptance_organizations 
    ADD COLUMN status VARCHAR(50) DEFAULT 'active' 
    COMMENT 'active, suspended, inactive'
    AFTER name;

-- Add indexes
ALTER TABLE acceptance_organizations 
    ADD INDEX idx_status (status);

-- Update existing records to active
UPDATE acceptance_organizations 
SET status = 'active' 
WHERE status IS NULL OR status = '';

-- =====================================================
-- 5. ALTER COOPERATIVE ASSOCIATIONS TABLE
-- =====================================================

-- Add status field (no email verification needed)
ALTER TABLE cooperative_associations 
    ADD COLUMN status VARCHAR(50) DEFAULT 'active' 
    COMMENT 'active, suspended, inactive'
    AFTER name;

-- Add indexes
ALTER TABLE cooperative_associations 
    ADD INDEX idx_status (status);

-- Update existing records to active
UPDATE cooperative_associations 
SET status = 'active' 
WHERE status IS NULL OR status = '';

-- =====================================================
-- 6. CREATE STAKEHOLDER ACTIVITIES TABLE (Audit Log)
-- =====================================================

USE cms_authentication_authorization;

CREATE TABLE IF NOT EXISTS stakeholder_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_type VARCHAR(100) NOT NULL COMMENT 'registration, verification, approval, status_change, permission_change',
    stakeholder_type VARCHAR(100) NOT NULL COMMENT 'lpk, special_skill, acceptance_org, cooperative_assoc',
    stakeholder_id INT NOT NULL,
    description TEXT,
    user_id INT NULL COMMENT 'User who performed the action',
    admin_id INT NULL COMMENT 'Admin who performed the action',
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    additional_data JSON NULL COMMENT 'Store additional context as JSON',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_stakeholder (stakeholder_type, stakeholder_id),
    INDEX idx_user (user_id),
    INDEX idx_admin (admin_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created (created),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. CREATE STAKEHOLDER PERMISSIONS TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS stakeholder_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stakeholder_type VARCHAR(100) NOT NULL COMMENT 'lpk, special_skill',
    stakeholder_id INT NOT NULL,
    permission_key VARCHAR(100) NOT NULL COMMENT 'candidates.view, candidates.add, candidates.edit, candidates.delete, candidates.export',
    permission_value TINYINT(1) DEFAULT 0 COMMENT '0=denied, 1=allowed',
    granted_by INT NULL COMMENT 'Admin user ID who granted permission',
    granted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME ON UPDATE CURRENT_TIMESTAMP,
    notes TEXT NULL COMMENT 'Admin notes about this permission',
    
    UNIQUE KEY unique_permission (stakeholder_type, stakeholder_id, permission_key),
    INDEX idx_stakeholder (stakeholder_type, stakeholder_id),
    INDEX idx_permission_key (permission_key),
    INDEX idx_granted_by (granted_by),
    
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. CREATE EMAIL LOGS TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(255) NULL,
    subject VARCHAR(500) NOT NULL,
    email_type VARCHAR(100) NOT NULL COMMENT 'lpk_verification, special_skill_verification, admin_notification, password_reset',
    stakeholder_type VARCHAR(100) NULL,
    stakeholder_id INT NULL,
    user_id INT NULL,
    status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, sent, failed, bounced',
    error_message TEXT NULL,
    sent_at DATETIME NULL,
    opened_at DATETIME NULL COMMENT 'Track email opens if using tracking pixel',
    clicked_at DATETIME NULL COMMENT 'Track link clicks',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_recipient (recipient_email),
    INDEX idx_email_type (email_type),
    INDEX idx_status (status),
    INDEX idx_stakeholder (stakeholder_type, stakeholder_id),
    INDEX idx_user (user_id),
    INDEX idx_created (created),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. CREATE ADMIN APPROVAL QUEUE TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS admin_approval_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    approval_type VARCHAR(100) NOT NULL COMMENT 'lpk_registration, special_skill_registration',
    stakeholder_type VARCHAR(100) NOT NULL,
    stakeholder_id INT NOT NULL,
    user_id INT NULL COMMENT 'Institution admin user',
    status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT NULL COMMENT 'Admin who reviewed',
    reviewed_at DATETIME NULL,
    rejection_reason TEXT NULL,
    notes TEXT NULL,
    
    INDEX idx_status (status),
    INDEX idx_stakeholder (stakeholder_type, stakeholder_id),
    INDEX idx_approval_type (approval_type),
    INDEX idx_reviewed_by (reviewed_by),
    INDEX idx_submitted_at (submitted_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. INSERT DEFAULT PERMISSIONS FOR EXISTING LPKs
-- =====================================================

-- Default permission set for all LPK institutions
INSERT INTO stakeholder_permissions (stakeholder_type, stakeholder_id, permission_key, permission_value)
SELECT 
    'lpk' as stakeholder_type,
    id as stakeholder_id,
    permission_key,
    1 as permission_value
FROM vocational_training_institutions
CROSS JOIN (
    SELECT 'candidates.view' as permission_key
    UNION SELECT 'candidates.add'
    UNION SELECT 'candidates.edit'
    UNION SELECT 'candidates.export'
    -- Note: candidates.delete is NOT granted by default
) perms
ON DUPLICATE KEY UPDATE permission_value = permission_value;

-- Default permission set for all Special Skill institutions
INSERT INTO stakeholder_permissions (stakeholder_type, stakeholder_id, permission_key, permission_value)
SELECT 
    'special_skill' as stakeholder_type,
    id as stakeholder_id,
    permission_key,
    1 as permission_value
FROM special_skill_support_institutions
CROSS JOIN (
    SELECT 'trainees.view' as permission_key
    UNION SELECT 'trainees.add'
    UNION SELECT 'trainees.edit'
    UNION SELECT 'trainees.export'
    -- Note: trainees.delete is NOT granted by default
) perms
ON DUPLICATE KEY UPDATE permission_value = permission_value;

-- =====================================================
-- 11. CREATE HELPER VIEWS
-- =====================================================

-- View: Pending Verifications
CREATE OR REPLACE VIEW vw_pending_verifications AS
SELECT 
    u.id as user_id,
    u.email,
    u.fullname,
    u.role,
    u.institution_type,
    u.institution_id,
    u.verification_token_expires,
    CASE 
        WHEN u.institution_type = 'vocational_training' THEN vti.name
        WHEN u.institution_type = 'special_skill' THEN ssi.name
        ELSE NULL
    END as institution_name,
    CASE
        WHEN u.verification_token_expires < NOW() THEN 'expired'
        ELSE 'pending'
    END as token_status,
    u.created
FROM cms_authentication_authorization.users u
LEFT JOIN cms_tmm_stakeholders.vocational_training_institutions vti 
    ON u.institution_type = 'vocational_training' AND u.institution_id = vti.id
LEFT JOIN cms_tmm_stakeholders.special_skill_support_institutions ssi 
    ON u.institution_type = 'special_skill' AND u.institution_id = ssi.id
WHERE u.status = 'pending_verification'
    AND u.verification_token IS NOT NULL
ORDER BY u.created DESC;

-- View: Admin Approval Dashboard
CREATE OR REPLACE VIEW vw_admin_approval_dashboard AS
SELECT 
    aaq.id as queue_id,
    aaq.approval_type,
    aaq.stakeholder_type,
    aaq.stakeholder_id,
    aaq.status,
    aaq.submitted_at,
    u.email as user_email,
    u.fullname as user_fullname,
    CASE 
        WHEN aaq.stakeholder_type = 'lpk' THEN vti.name
        WHEN aaq.stakeholder_type = 'special_skill' THEN ssi.name
        ELSE NULL
    END as institution_name,
    admin.fullname as reviewed_by_name,
    aaq.reviewed_at,
    TIMESTAMPDIFF(HOUR, aaq.submitted_at, NOW()) as hours_waiting
FROM cms_authentication_authorization.admin_approval_queue aaq
LEFT JOIN cms_authentication_authorization.users u ON aaq.user_id = u.id
LEFT JOIN cms_tmm_stakeholders.vocational_training_institutions vti 
    ON aaq.stakeholder_type = 'lpk' AND aaq.stakeholder_id = vti.id
LEFT JOIN cms_tmm_stakeholders.special_skill_support_institutions ssi 
    ON aaq.stakeholder_type = 'special_skill' AND aaq.stakeholder_id = ssi.id
LEFT JOIN cms_authentication_authorization.users admin ON aaq.reviewed_by = admin.id
WHERE aaq.status = 'pending'
ORDER BY aaq.submitted_at ASC;

-- =====================================================
-- 12. VERIFICATION & TESTING QUERIES
-- =====================================================

-- Verify all tables exist
SELECT 'users' as table_name, COUNT(*) as record_count FROM cms_authentication_authorization.users
UNION ALL
SELECT 'stakeholder_activities', COUNT(*) FROM cms_authentication_authorization.stakeholder_activities
UNION ALL
SELECT 'stakeholder_permissions', COUNT(*) FROM cms_authentication_authorization.stakeholder_permissions
UNION ALL
SELECT 'email_logs', COUNT(*) FROM cms_authentication_authorization.email_logs
UNION ALL
SELECT 'admin_approval_queue', COUNT(*) FROM cms_authentication_authorization.admin_approval_queue
UNION ALL
SELECT 'vocational_training_institutions', COUNT(*) FROM cms_tmm_stakeholders.vocational_training_institutions
UNION ALL
SELECT 'special_skill_support_institutions', COUNT(*) FROM cms_tmm_stakeholders.special_skill_support_institutions
UNION ALL
SELECT 'acceptance_organizations', COUNT(*) FROM cms_tmm_stakeholders.acceptance_organizations
UNION ALL
SELECT 'cooperative_associations', COUNT(*) FROM cms_tmm_stakeholders.cooperative_associations;

-- Check pending verifications
SELECT * FROM vw_pending_verifications LIMIT 10;

-- Check approval queue
SELECT * FROM vw_admin_approval_dashboard LIMIT 10;

-- =====================================================
-- MIGRATION COMPLETE
-- =====================================================

SELECT 'Migration completed successfully!' as status;
