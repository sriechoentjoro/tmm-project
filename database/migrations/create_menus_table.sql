-- Create Navigation Menus Table
-- Execute via phpMyAdmin SQL tab database: asahi_inventories

USE asahi_inventories;

-- Create menus table
CREATE TABLE IF NOT EXISTS `menus` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT(11) UNSIGNED NULL COMMENT 'Parent menu ID for submenu',
  `title` VARCHAR(100) NOT NULL COMMENT 'Menu title',
  `url` VARCHAR(255) NULL COMMENT 'Menu URL',
  `icon` VARCHAR(50) NULL COMMENT 'Font Awesome icon class',
  `position` INT(11) NOT NULL DEFAULT 0 COMMENT 'Sort order',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Active status',
  `target` VARCHAR(20) NOT NULL DEFAULT '_self' COMMENT 'Link target (_self, _blank)',
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_position` (`position`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Navigation menu structure';

-- Insert default menus
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(1, NULL, 'Dashboard', '/dashboard', 'fa-tachometer-alt', 1, 1, '_self'),
(2, NULL, 'Inventory', 'javascript:void(0)', 'fa-boxes', 2, 1, '_self'),
(3, NULL, 'Personnel', 'javascript:void(0)', 'fa-users', 3, 1, '_self'),
(4, NULL, 'Vehicle', 'javascript:void(0)', 'fa-car', 4, 1, '_self'),
(5, NULL, 'Maintenance', 'javascript:void(0)', 'fa-wrench', 5, 1, '_self'),
(6, NULL, 'Accounting', 'javascript:void(0)', 'fa-calculator', 6, 1, '_self'),
(7, NULL, 'Reports', 'javascript:void(0)', 'fa-chart-bar', 7, 1, '_self'),
(8, NULL, 'Settings', 'javascript:void(0)', 'fa-cog', 8, 1, '_self');

-- Inventory submenus
INSERT INTO `menus` (`parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(2, 'Inventories', '/inventories', 'fa-warehouse', 1, 1, '_self'),
(2, 'Stock Incomings', '/stock-incomings', 'fa-arrow-circle-down', 2, 1, '_self'),
(2, 'Stock Outgoings', '/stock-outgoings', 'fa-arrow-circle-up', 3, 1, '_self'),
(2, 'Stock Takes', '/stock-takes', 'fa-clipboard-check', 4, 1, '_self'),
(2, 'Adjust Stocks', '/adjust-stocks', 'fa-exchange-alt', 5, 1, '_self'),
(2, 'Purchase Receipts', '/purchase-receipts', 'fa-receipt', 6, 1, '_self'),
(2, 'Purchase Items', '/purchase-receipt-items', 'fa-list-ul', 7, 1, '_self'),
(2, 'Storages', '/storages', 'fa-box-open', 8, 1, '_self'),
(2, 'UOMs', '/uoms', 'fa-balance-scale', 9, 1, '_self');

-- Personnel submenus
INSERT INTO `menus` (`parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(3, 'Personnels', '/personnels', 'fa-user-tie', 1, 1, '_self'),
(3, 'Companies', '/companies', 'fa-building', 2, 1, '_self'),
(3, 'Departments', '/departments', 'fa-sitemap', 3, 1, '_self'),
(3, 'Positions', '/positions', 'fa-id-badge', 4, 1, '_self'),
(3, 'Sections', '/sections', 'fa-object-group', 5, 1, '_self');

-- Vehicle submenus
INSERT INTO `menus` (`parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(4, 'Vehicles', '/vehicles', 'fa-truck', 1, 1, '_self'),
(4, 'Drivers', '/drivers', 'fa-id-card', 2, 1, '_self'),
(4, 'Vehicle Types', '/vehicle-types', 'fa-tags', 3, 1, '_self'),
(4, 'Lesor Companies', '/lesor-companies', 'fa-handshake', 4, 1, '_self');

-- Maintenance submenus
INSERT INTO `menus` (`parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(5, 'Action Plans', '/action-plans', 'fa-tasks', 1, 1, '_self'),
(5, 'Daily Activities', '/daily-activities', 'fa-calendar-day', 2, 1, '_self'),
(5, 'Planned Jobs', '/planned-jobs', 'fa-clipboard-list', 3, 1, '_self'),
(5, 'Maintenance Cards', '/maintenance-cards', 'fa-id-card-alt', 4, 1, '_self');

-- Accounting submenus
INSERT INTO `menus` (`parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(6, 'Transactions', '/accounting-transactions', 'fa-file-invoice-dollar', 1, 1, '_self'),
(6, 'Chart of Accounts', '/chart-of-accounts', 'fa-chart-line', 2, 1, '_self'),
(6, 'Transaction Types', '/transaction-types', 'fa-tags', 3, 1, '_self'),
(6, 'Banks', '/banks', 'fa-university', 4, 1, '_self'),
(6, 'Payment Methods', '/payment-methods', 'fa-credit-card', 5, 1, '_self');

-- Settings submenus
INSERT INTO `menus` (`parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(8, 'Users', '/users', 'fa-user', 1, 1, '_self'),
(8, 'Roles', '/roles', 'fa-user-shield', 2, 1, '_self'),
(8, 'Authorizations', '/authorizations', 'fa-key', 3, 1, '_self');

-- Show results
SELECT 
  CASE 
    WHEN m.parent_id IS NULL THEN CONCAT('📁 ', m.title)
    ELSE CONCAT('  └─ ', m.title) 
  END AS menu_structure,
  m.url,
  m.icon,
  m.position
FROM menus m
WHERE m.parent_id IS NULL 
   OR m.parent_id IN (SELECT id FROM menus WHERE title = 'Inventory')
ORDER BY 
  COALESCE(m.parent_id, m.id), 
  m.position
LIMIT 20;

SELECT '✓ Menus table created and populated successfully!' AS result;
