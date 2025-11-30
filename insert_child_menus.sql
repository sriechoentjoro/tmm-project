-- Insert sample child menus for testing
-- Assuming parent menu ID 1 exists (change if needed)

-- Get parent menu IDs first, then insert children
-- Children for Dashboard (assuming ID 1)
INSERT INTO menus (parent_id, title, icon, url, target, position, is_active, created, modified) 
VALUES 
(1, 'Overview', 'fa-chart-line', '/dashboard', '_self', 1, 1, NOW(), NOW()),
(1, 'Statistics', 'fa-chart-bar', '/dashboard/stats', '_self', 2, 1, NOW(), NOW()),
(1, 'Reports', 'fa-file-alt', '/dashboard/reports', '_self', 3, 1, NOW(), NOW());

-- Children for Inventory (assuming ID 2)  
INSERT INTO menus (parent_id, title, icon, url, target, position, is_active, created, modified)
VALUES
(2, 'All Items', 'fa-boxes', '/inventories', '_self', 1, 1, NOW(), NOW()),
(2, 'Add New', 'fa-plus-circle', '/inventories/add', '_self', 2, 1, NOW(), NOW()),
(2, 'Categories', 'fa-tags', '/categories', '_self', 3, 1, NOW(), NOW()),
(2, 'Stock Report', 'fa-clipboard-list', '/stock-takes', '_self', 4, 1, NOW(), NOW());

-- Children for Personnel (assuming ID 3)
INSERT INTO menus (parent_id, title, icon, url, target, position, is_active, created, modified)
VALUES
(3, 'All Staff', 'fa-users', '/personnels', '_self', 1, 1, NOW(), NOW()),
(3, 'Add New', 'fa-user-plus', '/personnels/add', '_self', 2, 1, NOW(), NOW()),
(3, 'Departments', 'fa-building', '/departments', '_self', 3, 1, NOW(), NOW());
