-- Delete the first/old Stakeholder Management menu
-- This removes the menu and all its child menus

-- First, delete child menus
DELETE FROM menus 
WHERE parent_id IN (
    SELECT id FROM (
        SELECT id FROM menus 
        WHERE title = 'Stakeholder Management'
        ORDER BY id ASC
        LIMIT 1
    ) AS temp
);

-- Then, delete the first Stakeholder Management menu
DELETE FROM menus 
WHERE id = (
    SELECT id FROM (
        SELECT id FROM menus 
        WHERE title = 'Stakeholder Management'
        ORDER BY id ASC
        LIMIT 1
    ) AS temp
);
