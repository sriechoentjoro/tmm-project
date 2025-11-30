-- =====================================================
-- Authentication & Authorization Database Tables
-- =====================================================
-- Database: system_authentication_authorization
-- =====================================================

USE system_authentication_authorization;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    institution_id INT NULL COMMENT 'Link to vocational_training_institutions or special_skill_support_institutions for lpk-penyangga users',
    institution_type ENUM('vocational_training', 'special_skill_support') NULL COMMENT 'Type of institution for lpk-penyangga users',
    created DATETIME,
    modified DATETIME,
    INDEX idx_institution_id (institution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    is_system TINYINT(1) DEFAULT 0,
    created DATETIME,
    modified DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Roles junction table (CakePHP will detect association via user_id and role_id)
CREATE TABLE IF NOT EXISTS user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    created DATETIME,
    UNIQUE KEY unique_user_role (user_id, role_id),
    INDEX idx_user_id (user_id),
    INDEX idx_role_id (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Database Connection Scopes
CREATE TABLE IF NOT EXISTS database_connection_scopes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    connection_name VARCHAR(100) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    created DATETIME,
    modified DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role Database Scopes (CakePHP will detect associations via role_id and database_connection_scope_id)
CREATE TABLE IF NOT EXISTS role_database_scopes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    database_connection_scope_id INT NOT NULL,
    access_level ENUM('none', 'read', 'write', 'full') DEFAULT 'read',
    created DATETIME,
    UNIQUE KEY unique_role_scope (role_id, database_connection_scope_id),
    INDEX idx_role_id (role_id),
    INDEX idx_database_connection_scope_id (database_connection_scope_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Controller Permissions
CREATE TABLE IF NOT EXISTS controller_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    controller_name VARCHAR(100) NOT NULL,
    database_connection_name VARCHAR(100) NOT NULL,
    description TEXT,
    created DATETIME,
    modified DATETIME,
    UNIQUE KEY unique_controller (controller_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role Controller Permissions (CakePHP will detect associations via role_id and controller_permission_id)
CREATE TABLE IF NOT EXISTS role_controller_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    controller_permission_id INT NOT NULL,
    can_view TINYINT(1) DEFAULT 0,
    can_add TINYINT(1) DEFAULT 0,
    can_edit TINYINT(1) DEFAULT 0,
    can_delete TINYINT(1) DEFAULT 0,
    created DATETIME,
    UNIQUE KEY unique_role_controller (role_id, controller_permission_id),
    INDEX idx_role_id (role_id),
    INDEX idx_controller_permission_id (controller_permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default roles (including management role for read-only access)
INSERT INTO roles (name, display_name, description, is_system, created, modified) VALUES
('administrator', 'Administrator', 'Full system access', 1, NOW(), NOW()),
('management', 'Management/Director', 'Read-only access to all interfaces for review and monitoring', 1, NOW(), NOW()),
('tmm-recruitment', 'TMM Recruitment', 'TMM Recruitment team', 1, NOW(), NOW()),
('tmm-training', 'TMM Training', 'TMM Training team', 1, NOW(), NOW()),
('tmm-documentation', 'TMM Documentation', 'TMM Documentation team', 1, NOW(), NOW()),
('lpk-penyangga', 'LPK Penyangga', 'LPK Penyangga access for all LPK institutions', 1, NOW(), NOW());

-- Insert database connection scopes
INSERT INTO database_connection_scopes (connection_name, display_name, description, created, modified) VALUES
('cms_masters', 'Master Data', 'Master data tables', NOW(), NOW()),
('cms_lpk_candidates', 'LPK Candidates', 'LPK candidate data', NOW(), NOW()),
('cms_lpk_candidate_documents', 'LPK Candidate Documents', 'LPK candidate documents', NOW(), NOW()),
('cms_tmm_apprentices', 'TMM Apprentices', 'TMM apprentice data', NOW(), NOW()),
('cms_tmm_apprentice_documents', 'TMM Apprentice Documents', 'TMM apprentice documents', NOW(), NOW()),
('cms_tmm_organizations', 'TMM Organizations', 'TMM organization data', NOW(), NOW()),
('cms_tmm_stakeholders', 'TMM Stakeholders', 'TMM stakeholder data', NOW(), NOW()),
('cms_tmm_trainees', 'TMM Trainees', 'TMM trainee data', NOW(), NOW());

SELECT 'Authentication & Authorization tables created successfully!' as Status;
