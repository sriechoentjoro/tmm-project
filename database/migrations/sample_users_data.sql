-- =====================================================
-- Sample User Data for Authentication & Authorization
-- =====================================================
-- This file contains sample users for all roles
-- Password for all users: "password123" (hashed)
-- =====================================================

USE system_authentication_authorization;

-- Insert sample users
-- Password hash for "password123" using bcrypt
-- Note: In production, use proper password hashing via CakePHP

INSERT INTO users (username, email, password, full_name, is_active, institution_id, institution_type, created, modified) VALUES
-- Administrator
('admin', 'admin@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 1, NULL, NULL, NOW(), NOW()),

-- Management/Director
('director', 'director@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Director TMM', 1, NULL, NULL, NOW(), NOW()),
('manager', 'manager@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'General Manager', 1, NULL, NULL, NOW(), NOW()),

-- TMM Recruitment
('recruitment1', 'recruitment1@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Recruitment Officer 1', 1, NULL, NULL, NOW(), NOW()),
('recruitment2', 'recruitment2@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Recruitment Officer 2', 1, NULL, NULL, NOW(), NOW()),

-- TMM Training
('training1', 'training1@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Training Coordinator 1', 1, NULL, NULL, NOW(), NOW()),
('training2', 'training2@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Training Coordinator 2', 1, NULL, NULL, NOW(), NOW()),

-- TMM Documentation
('documentation1', 'documentation1@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Documentation Officer 1', 1, NULL, NULL, NOW(), NOW()),
('documentation2', 'documentation2@tmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Documentation Officer 2', 1, NULL, NULL, NOW(), NOW()),

-- LPK Penyangga Users (linked to vocational_training_institutions)
-- Based on existing data from cms_tmm_stakeholders.vocational_training_institutions
('lpk_semarang', 'lpk.semarang@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin LPK Semarang', 1, 1, 'vocational_training', NOW(), NOW()),
('lpk_makasar', 'lpk.makasar@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin LPK Makasar', 1, 2, 'vocational_training', NOW(), NOW()),
('lpk_medan', 'lpk.medan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin LPK Medan', 1, 3, 'vocational_training', NOW(), NOW()),
('lpk_padang', 'lpk.padang@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin LPK Padang', 1, 4, 'vocational_training', NOW(), NOW()),
('lpk_bekasi', 'lpk.bekasi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin LPK Bekasi', 1, 5, 'vocational_training', NOW(), NOW());

-- Assign roles to users
-- Get role IDs
SET @admin_role = (SELECT id FROM roles WHERE name = 'administrator');
SET @management_role = (SELECT id FROM roles WHERE name = 'management');
SET @recruitment_role = (SELECT id FROM roles WHERE name = 'tmm-recruitment');
SET @training_role = (SELECT id FROM roles WHERE name = 'tmm-training');
SET @documentation_role = (SELECT id FROM roles WHERE name = 'tmm-documentation');
SET @lpk_role = (SELECT id FROM roles WHERE name = 'lpk-penyangga');

-- Get user IDs
SET @admin_user = (SELECT id FROM users WHERE username = 'admin');
SET @director_user = (SELECT id FROM users WHERE username = 'director');
SET @manager_user = (SELECT id FROM users WHERE username = 'manager');
SET @recruitment1_user = (SELECT id FROM users WHERE username = 'recruitment1');
SET @recruitment2_user = (SELECT id FROM users WHERE username = 'recruitment2');
SET @training1_user = (SELECT id FROM users WHERE username = 'training1');
SET @training2_user = (SELECT id FROM users WHERE username = 'training2');
SET @documentation1_user = (SELECT id FROM users WHERE username = 'documentation1');
SET @documentation2_user = (SELECT id FROM users WHERE username = 'documentation2');
SET @lpk_semarang_user = (SELECT id FROM users WHERE username = 'lpk_semarang');
SET @lpk_makasar_user = (SELECT id FROM users WHERE username = 'lpk_makasar');
SET @lpk_medan_user = (SELECT id FROM users WHERE username = 'lpk_medan');
SET @lpk_padang_user = (SELECT id FROM users WHERE username = 'lpk_padang');
SET @lpk_bekasi_user = (SELECT id FROM users WHERE username = 'lpk_bekasi');

-- Assign roles
INSERT INTO user_roles (user_id, role_id, created) VALUES
-- Administrator
(@admin_user, @admin_role, NOW()),

-- Management
(@director_user, @management_role, NOW()),
(@manager_user, @management_role, NOW()),

-- Recruitment
(@recruitment1_user, @recruitment_role, NOW()),
(@recruitment2_user, @recruitment_role, NOW()),

-- Training
(@training1_user, @training_role, NOW()),
(@training2_user, @training_role, NOW()),

-- Documentation
(@documentation1_user, @documentation_role, NOW()),
(@documentation2_user, @documentation_role, NOW()),

-- LPK Penyangga
(@lpk_semarang_user, @lpk_role, NOW()),
(@lpk_makasar_user, @lpk_role, NOW()),
(@lpk_medan_user, @lpk_role, NOW()),
(@lpk_padang_user, @lpk_role, NOW()),
(@lpk_bekasi_user, @lpk_role, NOW());

-- =====================================================
-- Summary of Sample Users
-- =====================================================
-- Username          | Password     | Role              | Institution
-- ------------------|--------------|-------------------|------------------
-- admin             | password123  | Administrator     | -
-- director          | password123  | Management        | -
-- manager           | password123  | Management        | -
-- recruitment1      | password123  | TMM Recruitment   | -
-- recruitment2      | password123  | TMM Recruitment   | -
-- training1         | password123  | TMM Training      | -
-- training2         | password123  | TMM Training      | -
-- documentation1    | password123  | TMM Documentation | -
-- documentation2    | password123  | TMM Documentation | -
-- lpk_semarang      | password123  | LPK Penyangga     | LPK SEMARANG (ID: 1)
-- lpk_makasar       | password123  | LPK Penyangga     | LPK MAKASAR (ID: 2)
-- lpk_medan         | password123  | LPK Penyangga     | LPK MEDAN (ID: 3)
-- lpk_padang        | password123  | LPK Penyangga     | LPK PADANG (ID: 4)
-- lpk_bekasi        | password123  | LPK Penyangga     | LPK BEKASI (ID: 5)
-- =====================================================

SELECT 'Sample users created successfully!' as Status;
SELECT COUNT(*) as TotalUsers FROM users;
SELECT r.display_name as Role, COUNT(ur.id) as UserCount 
FROM roles r 
LEFT JOIN user_roles ur ON r.id = ur.role_id 
GROUP BY r.id, r.display_name 
ORDER BY r.id;
