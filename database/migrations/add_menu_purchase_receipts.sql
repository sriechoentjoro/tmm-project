-- Add Purchase Receipts to Navigation Menu
-- Execute via phpMyAdmin SQL tab

USE asahi_inventories;

-- 1. Cari ID menu "Master" atau buat baru
SET @master_menu_id = (SELECT id FROM navigation_menus WHERE title = 'Master' AND parent_id IS NULL LIMIT 1);

-- Jika tidak ada, buat menu Master
INSERT INTO navigation_menus (title, url, icon, parent_id, sort_order, is_active, target)
SELECT 'Master', 'javascript:void(0)', 'fa-database', NULL, 1, 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM navigation_menus WHERE title = 'Master' AND parent_id IS NULL);

-- Get ID again
SET @master_menu_id = (SELECT id FROM navigation_menus WHERE title = 'Master' AND parent_id IS NULL LIMIT 1);

-- 2. Tambahkan Purchase Receipts submenu
INSERT INTO navigation_menus (title, url, icon, parent_id, sort_order, is_active, target)
VALUES 
('Purchase Receipts', '/purchase-receipts', 'fa-receipt', @master_menu_id, 10, 1, '_self'),
('Purchase Items', '/purchase-receipt-items', 'fa-list-ul', @master_menu_id, 11, 1, '_self'),
('Accounting Transactions', '/accounting-transactions', 'fa-file-invoice-dollar', @master_menu_id, 12, 1, '_self'),
('Transaction Types', '/transaction-types', 'fa-tags', @master_menu_id, 13, 1, '_self'),
('Chart of Accounts', '/chart-of-accounts', 'fa-chart-line', @master_menu_id, 14, 1, '_self')
ON DUPLICATE KEY UPDATE 
  url = VALUES(url),
  icon = VALUES(icon),
  sort_order = VALUES(sort_order);

-- 3. Atau buat menu terpisah "Accounting"
INSERT INTO navigation_menus (title, url, icon, parent_id, sort_order, is_active, target)
SELECT 'Accounting', 'javascript:void(0)', 'fa-calculator', NULL, 5, 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM navigation_menus WHERE title = 'Accounting' AND parent_id IS NULL);

SET @accounting_menu_id = (SELECT id FROM navigation_menus WHERE title = 'Accounting' AND parent_id IS NULL LIMIT 1);

-- Submenu Accounting
INSERT INTO navigation_menus (title, url, icon, parent_id, sort_order, is_active, target)
VALUES 
('Purchase Receipts', '/purchase-receipts', 'fa-receipt', @accounting_menu_id, 1, 1, '_self'),
('Accounting Transactions', '/accounting-transactions', 'fa-file-invoice-dollar', @accounting_menu_id, 2, 1, '_self'),
('Chart of Accounts', '/chart-of-accounts', 'fa-chart-line', @accounting_menu_id, 3, 1, '_self'),
('Transaction Types', '/transaction-types', 'fa-tags', @accounting_menu_id, 4, 1, '_self')
ON DUPLICATE KEY UPDATE 
  url = VALUES(url),
  icon = VALUES(icon),
  sort_order = VALUES(sort_order);

-- Verify
SELECT 
  CASE WHEN parent_id IS NULL THEN title ELSE CONCAT('  └─ ', title) END AS menu_structure,
  url,
  icon,
  is_active
FROM navigation_menus 
WHERE parent_id IS NULL 
   OR parent_id IN (SELECT id FROM navigation_menus WHERE title IN ('Master', 'Accounting'))
ORDER BY 
  COALESCE(parent_id, id), 
  sort_order;
