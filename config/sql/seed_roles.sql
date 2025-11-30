-- Seed Roles
INSERT INTO roles (name, description, created, modified) VALUES
('administrator', 'Full system access', NOW(), NOW()),
('management', 'Read-only access to all data', NOW(), NOW()),
('director', 'Read-only access to all data (same as management)', NOW(), NOW()),
('tmm-recruitment', 'Manage candidates and recruitment process', NOW(), NOW()),
('tmm-training', 'Manage trainees and training process', NOW(), NOW()),
('tmm-documentation', 'Manage documentation and reports', NOW(), NOW()),
('lpk-penyangga', 'Access for LPK Penyangga (Special Skill Support)', NOW(), NOW()),
('lpk-so', 'Access for LPK SO (Vocational Training)', NOW(), NOW())
ON DUPLICATE KEY UPDATE description = VALUES(description);
