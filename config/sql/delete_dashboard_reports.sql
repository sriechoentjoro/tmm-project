-- Delete Dashboard and Reports Menus
-- This script removes these menus and all their child menus

-- First, delete child menus
DELETE FROM menus 
WHERE parent_id IN (
    SELECT id FROM (
        SELECT id FROM menus 
        WHERE title IN ('Dashboard', 'Reports')
    ) AS temp
);

-- Then, delete the parent menus
DELETE FROM menus 
WHERE title IN ('Dashboard', 'Reports');
