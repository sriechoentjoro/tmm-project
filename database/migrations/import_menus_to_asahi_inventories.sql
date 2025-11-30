-- Import Menus from pt_asahi_menus to asahi_inventories
-- Execute via phpMyAdmin SQL tab database: asahi_inventories

USE asahi_inventories;

-- Create menus table if not exists
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `target` varchar(20) NOT NULL DEFAULT '_self',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert existing menus
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(2, NULL, 'Inventory', 'javascript:void(0)', 'fa-boxes', 2, 1, '_self'),
(3, 2, 'Purchase Receipts', '/purchase-receipts', 'fa-receipt', 6, 1, '_self'),
(4, 2, 'Stock Incomings', '/stock-incomings', 'fa-arrow-circle-down', 2, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url),
  icon = VALUES(icon),
  position = VALUES(position);

-- Add more inventory submenus
INSERT INTO `menus` (`parent_id`, `title`, `url`, `icon`, `position`, `is_active`, `target`) VALUES
(2, 'Inventories', '/inventories', 'fa-warehouse', 1, 1, '_self'),
(2, 'Stock Outgoings', '/stock-outgoings', 'fa-arrow-circle-up', 3, 1, '_self'),
(2, 'Stock Takes', '/stock-takes', 'fa-clipboard-check', 4, 1, '_self'),
(2, 'Adjust Stocks', '/adjust-stocks', 'fa-exchange-alt', 5, 1, '_self'),
(2, 'Purchase Items', '/purchase-receipt-items', 'fa-list-ul', 7, 1, '_self')
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  url = VALUES(url),
  position = VALUES(position);

-- Set AUTO_INCREMENT
ALTER TABLE `menus` AUTO_INCREMENT = 10;

-- Verify
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
   OR m.parent_id = 2
ORDER BY 
  COALESCE(m.parent_id, m.id), 
  m.position;

SELECT '✓ Menus imported to asahi_inventories successfully!' AS result;
