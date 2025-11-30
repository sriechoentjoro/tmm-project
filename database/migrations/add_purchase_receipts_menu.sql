-- Add Purchase Receipts System to Inventory Menu
-- Execute via phpMyAdmin SQL tab database: pt_asahi_menus

USE pt_asahi_menus;

-- 1. Find "Inventory" parent menu (assuming it exists)
SET @inventory_menu_id = (SELECT id FROM menus WHERE title LIKE '%Inventory%' AND parent_id IS NULL LIMIT 1);
SET @inventori_menu_id = (SELECT id FROM menus WHERE title LIKE '%Inventori%' AND parent_id IS NULL LIMIT 1);

-- Use whichever exists
SET @parent_menu_id = COALESCE(@inventory_menu_id, @inventori_menu_id);

-- 2. If Inventory menu doesn't exist, create it
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target)
SELECT NULL, 'Inventory', 'javascript:void(0)', 'fa-boxes', 20, 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM menus WHERE (title LIKE '%Inventory%' OR title LIKE '%Inventori%') AND parent_id IS NULL);

-- Get ID again
SET @parent_menu_id = COALESCE(
    (SELECT id FROM menus WHERE title LIKE '%Inventory%' AND parent_id IS NULL LIMIT 1),
    (SELECT id FROM menus WHERE title LIKE '%Inventori%' AND parent_id IS NULL LIMIT 1)
);

-- 3. Add Purchase Receipts under Inventory menu
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target)
VALUES 
(@parent_menu_id, 'Purchase Receipts', '/purchase-receipts', 'fa-receipt', 40, 1, '_self'),
(@parent_menu_id, 'Purchase Items', '/purchase-receipt-items', 'fa-list-ul', 41, 1, '_self')
ON DUPLICATE KEY UPDATE 
  url = VALUES(url),
  icon = VALUES(icon),
  position = VALUES(position);

-- 4. Create separate "Accounting" menu for accounting-specific items
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target)
SELECT NULL, 'Accounting', 'javascript:void(0)', 'fa-calculator', 100, 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM menus WHERE title = 'Accounting' AND parent_id IS NULL);

SET @accounting_menu_id = (SELECT id FROM menus WHERE title = 'Accounting' AND parent_id IS NULL LIMIT 1);

-- 5. Add accounting-only items under Accounting menu
INSERT INTO menus (parent_id, title, url, icon, position, is_active, target)
VALUES 
(@accounting_menu_id, 'Transactions', '/accounting-transactions', 'fa-file-invoice-dollar', 1, 1, '_self'),
(@accounting_menu_id, 'Chart of Accounts', '/chart-of-accounts', 'fa-chart-line', 2, 1, '_self'),
(@accounting_menu_id, 'Transaction Types', '/transaction-types', 'fa-tags', 3, 1, '_self')
ON DUPLICATE KEY UPDATE 
  url = VALUES(url),
  icon = VALUES(icon),
  position = VALUES(position);

-- Verify results - Show menu structure
SELECT 
  CASE 
    WHEN m.parent_id IS NULL THEN CONCAT('📁 ', m.title)
    ELSE CONCAT('  └─ ', m.title) 
  END AS menu_structure,
  m.url,
  m.icon,
  m.position,
  CASE WHEN m.is_active = 1 THEN '✓' ELSE '✗' END AS active
FROM menus m
LEFT JOIN menus p ON m.parent_id = p.id
WHERE m.parent_id IS NULL 
   OR p.title IN ('Inventory', 'Inventori', 'Accounting')
ORDER BY 
  COALESCE(m.parent_id, m.id), 
  m.position;

SELECT '✓ Menu structure updated successfully!' AS result;
