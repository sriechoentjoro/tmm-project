-- Complete Menu Structure for Asahi Application
-- Execute via phpMyAdmin SQL tab database: pt_asahi_menus

USE pt_asahi_menus;

-- Tambahkan menu utama lainnya
INSERT INTO menus (id, parent_id, title, url, icon, position, is_active, target) VALUES
(1, NULL, 'Dashboard', '/dashboard', 'fa-tachometer-alt', 1, 1, '_self'),
(5, NULL, 'Personnel', 'javascript:void(0)', 'fa-users', 3, 1, '_self'),
(6, NULL, 'Vehicle', 'javascript:void(0)', 'fa-car', 4, 1, '_self'),
(7, NULL, 'Maintenance', 'javascript:void(0)', 'fa-wrench', 5, 1, '_self'),
(8, NULL, 'Accounting', 'javascript:void(0)', 'fa-calculator', 6, 1, '_self'),
(9, NULL, 'Reports', 'javascript:void(0)', 'fa-chart-bar', 7, 1, '_self'),
(10, NULL, 'Settings', 'javascript:void(0)', 'fa-cog', 8, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url),
  icon = VALUES(icon),
  position = VALUES(position);

-- Inventory submenus (parent_id = 2)
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target) VALUES
(2, 'Inventories', '/inventories', 'fa-warehouse', 1, 1, '_self'),
(2, 'Stock Outgoings', '/stock-outgoings', 'fa-arrow-circle-up', 3, 1, '_self'),
(2, 'Stock Takes', '/stock-takes', 'fa-clipboard-check', 4, 1, '_self'),
(2, 'Adjust Stocks', '/adjust-stocks', 'fa-exchange-alt', 5, 1, '_self'),
(2, 'Storages', '/storages', 'fa-box-open', 8, 1, '_self'),
(2, 'Suppliers', '/suppliers', 'fa-truck', 9, 1, '_self'),
(2, 'UOMs', '/uoms', 'fa-balance-scale', 10, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url),
  position = VALUES(position);

-- Personnel submenus (parent_id = 5)
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target) VALUES
(5, 'Personnels', '/personnels', 'fa-user-tie', 1, 1, '_self'),
(5, 'Companies', '/companies', 'fa-building', 2, 1, '_self'),
(5, 'Departments', '/departments', 'fa-sitemap', 3, 1, '_self'),
(5, 'Positions', '/positions', 'fa-id-badge', 4, 1, '_self'),
(5, 'Sections', '/sections', 'fa-object-group', 5, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url);

-- Vehicle submenus (parent_id = 6)
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target) VALUES
(6, 'Vehicles', '/vehicles', 'fa-truck', 1, 1, '_self'),
(6, 'Drivers', '/drivers', 'fa-id-card', 2, 1, '_self'),
(6, 'Vehicle Types', '/vehicle-types', 'fa-tags', 3, 1, '_self'),
(6, 'Lesor Companies', '/lesor-companies', 'fa-handshake', 4, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url);

-- Maintenance submenus (parent_id = 7)
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target) VALUES
(7, 'Action Plans', '/action-plans', 'fa-tasks', 1, 1, '_self'),
(7, 'Daily Activities', '/daily-activities', 'fa-calendar-day', 2, 1, '_self'),
(7, 'Planned Jobs', '/planned-jobs', 'fa-clipboard-list', 3, 1, '_self'),
(7, 'Maintenance Cards', '/maintenance-cards', 'fa-id-card-alt', 4, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url);

-- Accounting submenus (parent_id = 8)
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target) VALUES
(8, 'Transactions', '/accounting-transactions', 'fa-file-invoice-dollar', 1, 1, '_self'),
(8, 'Chart of Accounts', '/chart-of-accounts', 'fa-chart-line', 2, 1, '_self'),
(8, 'Transaction Types', '/transaction-types', 'fa-tags', 3, 1, '_self'),
(8, 'Banks', '/banks', 'fa-university', 4, 1, '_self'),
(8, 'Payment Methods', '/payment-methods', 'fa-credit-card', 5, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url);

-- Settings submenus (parent_id = 10)
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target) VALUES
(10, 'Users', '/users', 'fa-user', 1, 1, '_self'),
(10, 'Roles', '/roles', 'fa-user-shield', 2, 1, '_self'),
(10, 'Authorizations', '/authorizations', 'fa-key', 3, 1, '_self'),
(10, 'Menus', '/menus', 'fa-bars', 4, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url);

-- Update AUTO_INCREMENT
ALTER TABLE menus AUTO_INCREMENT = 100;

-- Verify complete menu structure (simple version without special characters)
SELECT 
  m.id,
  CASE 
    WHEN m.parent_id IS NULL THEN CONCAT('[PARENT] ', m.title)
    ELSE CONCAT('  -> ', m.title) 
  END AS menu_structure,
  m.url,
  m.icon,
  m.position,
  m.is_active
FROM menus m
ORDER BY 
  COALESCE(m.parent_id, m.id), 
  m.position;
