-- Create Menus Table for Elegant Tab Navigation
CREATE TABLE IF NOT EXISTS menus (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    parent_id INT(11) NULL DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    icon VARCHAR(100) NULL,
    url VARCHAR(255) NULL,
    target VARCHAR(50) NULL DEFAULT '_self',
    position INT(11) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created DATETIME NULL,
    modified DATETIME NULL,
    INDEX idx_parent (parent_id),
    INDEX idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert Sample Menu Data
INSERT INTO menus (parent_id, title, icon, url, position, is_active) VALUES
-- Main Menus (Parent)
(NULL, 'Dashboard', 'fa-home', '/', 1, 1),
(NULL, 'Inventory', 'fa-boxes', '#', 2, 1),
(NULL, 'Personnel', 'fa-users', '#', 3, 1),
(NULL, 'Maintenance', 'fa-wrench', '#', 4, 1),
(NULL, 'Vehicles', 'fa-car', '#', 5, 1),
(NULL, 'Reports', 'fa-chart-bar', '#', 6, 1),
(NULL, 'Settings', 'fa-cog', '#', 7, 1);

-- Get IDs for submenus
SET @inventory_id = (SELECT id FROM menus WHERE title = 'Inventory' AND parent_id IS NULL);
SET @personnel_id = (SELECT id FROM menus WHERE title = 'Personnel' AND parent_id IS NULL);
SET @maintenance_id = (SELECT id FROM menus WHERE title = 'Maintenance' AND parent_id IS NULL);
SET @vehicles_id = (SELECT id FROM menus WHERE title = 'Vehicles' AND parent_id IS NULL);
SET @reports_id = (SELECT id FROM menus WHERE title = 'Reports' AND parent_id IS NULL);
SET @settings_id = (SELECT id FROM menus WHERE title = 'Settings' AND parent_id IS NULL);

-- Inventory Submenus
INSERT INTO menus (parent_id, title, icon, url, position, is_active) VALUES
(@inventory_id, 'Inventories', 'fa-list', '/inventories', 1, 1),
(@inventory_id, 'Storages', 'fa-warehouse', '/storages', 2, 1),
(@inventory_id, 'Stock Incoming', 'fa-arrow-down', '/stock-incomings', 3, 1),
(@inventory_id, 'Stock Outgoing', 'fa-arrow-up', '/stock-outgoings', 4, 1),
(@inventory_id, 'Stock Take', 'fa-clipboard-check', '/stock-takes', 5, 1),
(@inventory_id, 'Adjust Stocks', 'fa-balance-scale', '/adjust-stocks', 6, 1),
(@inventory_id, 'Suppliers', 'fa-truck', '/suppliers', 7, 1);

-- Personnel Submenus
INSERT INTO menus (parent_id, title, icon, url, position, is_active) VALUES
(@personnel_id, 'Personnels', 'fa-user', '/personnels', 1, 1),
(@personnel_id, 'Companies', 'fa-building', '/companies', 2, 1),
(@personnel_id, 'Departments', 'fa-sitemap', '/departments', 3, 1),
(@personnel_id, 'Positions', 'fa-id-badge', '/positions', 4, 1),
(@personnel_id, 'Employee Status', 'fa-user-check', '/employee-statuses', 5, 1);

-- Maintenance Submenus
INSERT INTO menus (parent_id, title, icon, url, position, is_active) VALUES
(@maintenance_id, 'Planned Jobs', 'fa-calendar-alt', '/planned-jobs', 1, 1),
(@maintenance_id, 'Maintenance Cards', 'fa-id-card', '/maintenance-cards', 2, 1),
(@maintenance_id, 'Daily Activities', 'fa-tasks', '/daily-activities', 3, 1),
(@maintenance_id, 'Action Plans', 'fa-clipboard-list', '/action-plans', 4, 1),
(@maintenance_id, 'Insurance Claims', 'fa-file-contract', '/insurance-claims', 5, 1);

-- Vehicles Submenus
INSERT INTO menus (parent_id, title, icon, url, position, is_active) VALUES
(@vehicles_id, 'Vehicles', 'fa-car', '/vehicles', 1, 1),
(@vehicles_id, 'Drivers', 'fa-id-card-alt', '/drivers', 2, 1),
(@vehicles_id, 'Vehicle Types', 'fa-car-side', '/vehicle-types', 3, 1),
(@vehicles_id, 'Lesor Companies', 'fa-building', '/lesor-companies', 4, 1);

-- Reports Submenus
INSERT INTO menus (parent_id, title, icon, url, position, is_active) VALUES
(@reports_id, 'Inventory Report', 'fa-file-alt', '/reports/inventory', 1, 1),
(@reports_id, 'Personnel Report', 'fa-file-alt', '/reports/personnel', 2, 1),
(@reports_id, 'Maintenance Report', 'fa-file-alt', '/reports/maintenance', 3, 1),
(@reports_id, 'Vehicle Report', 'fa-file-alt', '/reports/vehicle', 4, 1);

-- Settings Submenus
INSERT INTO menus (parent_id, title, icon, url, position, is_active) VALUES
(@settings_id, 'Users', 'fa-users-cog', '/users', 1, 1),
(@settings_id, 'Roles', 'fa-user-shield', '/roles', 2, 1),
(@settings_id, 'Configurations', 'fa-sliders-h', '/settings', 3, 1);
