-- Update Main Menu for Asahi Inventory System
-- Generated: November 11, 2025
-- Add menu items for all baked controllers

USE asahi_commons;

-- First, get current max position for each parent
-- Then insert new menu items

-- ===========================================
-- 1. INVENTORY MANAGEMENT (parent_id = 1)
-- ===========================================
INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Adjust Stocks', '/adjust-stocks', 'fa-balance-scale', 1, 50, 1, NOW(), NOW()),
('Stock Takes', '/stock-takes', 'fa-clipboard-check', 1, 60, 1, NOW(), NOW()),
('Inventory Images', '/inventory-images', 'fa-images', 1, 70, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

-- ===========================================
-- 2. ACCOUNTING (create new parent if not exists)
-- ===========================================
INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Accounting', '#', 'fa-calculator', NULL, 200, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

SET @accounting_id = LAST_INSERT_ID();

INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Chart of Accounts', '/chart-of-accounts', 'fa-list', @accounting_id, 10, 1, NOW(), NOW()),
('Counters', '/counters', 'fa-sort-numeric-up', @accounting_id, 20, 1, NOW(), NOW()),
('Banks', '/banks', 'fa-university', @accounting_id, 30, 1, NOW(), NOW()),
('Payment Methods', '/payment-methods', 'fa-credit-card', @accounting_id, 40, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

-- ===========================================
-- 3. PERSONNEL/HR (create new parent)
-- ===========================================
INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Personnel', '#', 'fa-users', NULL, 300, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

SET @personnel_id = LAST_INSERT_ID();

INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Companies', '/companies', 'fa-building', @personnel_id, 10, 1, NOW(), NOW()),
('Departments', '/departments', 'fa-sitemap', @personnel_id, 20, 1, NOW(), NOW()),
('Positions', '/positions', 'fa-user-tie', @personnel_id, 30, 1, NOW(), NOW()),
('Personnels', '/personnels', 'fa-id-badge', @personnel_id, 40, 1, NOW(), NOW()),
('Employee Statuses', '/employee-statuses', 'fa-user-check', @personnel_id, 50, 1, NOW(), NOW()),
('Sections', '/sections', 'fa-object-group', @personnel_id, 60, 1, NOW(), NOW()),
('Shift Groups', '/shift-groups', 'fa-clock', @personnel_id, 70, 1, NOW(), NOW()),
('Shifts', '/shifts', 'fa-calendar-alt', @personnel_id, 80, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

-- ===========================================
-- 4. MAINTENANCE (create new parent)
-- ===========================================
INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Maintenance', '#', 'fa-tools', NULL, 400, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

SET @maintenance_id = LAST_INSERT_ID();

INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Maintenance Cards', '/maintenance-cards', 'fa-clipboard-list', @maintenance_id, 10, 1, NOW(), NOW()),
('Planned Jobs', '/planned-jobs', 'fa-calendar-check', @maintenance_id, 20, 1, NOW(), NOW()),
('Daily Activities', '/daily-activities', 'fa-tasks', @maintenance_id, 30, 1, NOW(), NOW()),
('Actions', '/actions', 'fa-wrench', @maintenance_id, 40, 1, NOW(), NOW()),
('Action Plans', '/action-plans', 'fa-project-diagram', @maintenance_id, 50, 1, NOW(), NOW()),
('Safety Cards', '/safety-cards', 'fa-shield-alt', @maintenance_id, 60, 1, NOW(), NOW()),
('Job Categories', '/job-categories', 'fa-tags', @maintenance_id, 70, 1, NOW(), NOW()),
('Job Statuses', '/job-statuses', 'fa-flag', @maintenance_id, 80, 1, NOW(), NOW()),
('Priorities', '/priorities', 'fa-exclamation-circle', @maintenance_id, 90, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

-- ===========================================
-- 5. VEHICLES (create new parent)
-- ===========================================
INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Vehicles', '#', 'fa-truck', NULL, 500, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

SET @vehicle_id = LAST_INSERT_ID();

INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Vehicle List', '/vehicles', 'fa-car', @vehicle_id, 10, 1, NOW(), NOW()),
('Vehicle Types', '/vehicle-types', 'fa-list', @vehicle_id, 20, 1, NOW(), NOW()),
('Drivers', '/drivers', 'fa-id-card', @vehicle_id, 30, 1, NOW(), NOW()),
('Lesor Companies', '/lesor-companies', 'fa-handshake', @vehicle_id, 40, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

-- ===========================================
-- 6. APPROVALS (create new parent)
-- ===========================================
INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Approvals', '#', 'fa-check-circle', NULL, 600, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

SET @approval_id = LAST_INSERT_ID();

INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Approval List', '/approvals', 'fa-clipboard-check', @approval_id, 10, 1, NOW(), NOW()),
('Approval Statuses', '/approval-statuses', 'fa-info-circle', @approval_id, 20, 1, NOW(), NOW()),
('Approval Matrix', '/approval-matrix', 'fa-table', @approval_id, 30, 1, NOW(), NOW()),
('Approval Workflows', '/approval-workflows', 'fa-stream', @approval_id, 40, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

-- ===========================================
-- 7. MASTER DATA (create new parent)
-- ===========================================
INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Master Data', '#', 'fa-database', NULL, 700, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

SET @master_id = LAST_INSERT_ID();

INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Provinces', '/propinsis', 'fa-map-marked', @master_id, 10, 1, NOW(), NOW()),
('Cities', '/kabupatens', 'fa-city', @master_id, 20, 1, NOW(), NOW()),
('Districts', '/kecamatans', 'fa-map-pin', @master_id, 30, 1, NOW(), NOW()),
('Villages', '/kelurahans', 'fa-home', @master_id, 40, 1, NOW(), NOW()),
('Genders', '/genders', 'fa-venus-mars', @master_id, 50, 1, NOW(), NOW()),
('Religions', '/religions', 'fa-pray', @master_id, 60, 1, NOW(), NOW()),
('Languages', '/languages', 'fa-language', @master_id, 70, 1, NOW(), NOW()),
('Titles', '/titles', 'fa-graduation-cap', @master_id, 80, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

-- ===========================================
-- 8. SYSTEM (create new parent)
-- ===========================================
INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('System', '#', 'fa-cog', NULL, 900, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

SET @system_id = LAST_INSERT_ID();

INSERT INTO menus (name, url, icon, parent_id, position, is_active, created, modified) VALUES
('Menu Management', '/menus', 'fa-bars', @system_id, 10, 1, NOW(), NOW()),
('Users', '/users', 'fa-user', @system_id, 20, 1, NOW(), NOW()),
('Groups', '/groups', 'fa-users-cog', @system_id, 30, 1, NOW(), NOW()),
('Authorizations', '/authorizations', 'fa-key', @system_id, 40, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE modified = NOW();

-- Show results
SELECT 'Menu items updated successfully!' AS Result;
SELECT COUNT(*) AS TotalMenus FROM menus;
SELECT name, url, is_active FROM menus WHERE parent_id IS NULL ORDER BY position;
