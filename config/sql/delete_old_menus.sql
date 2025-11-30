-- Delete Old Menus: Master Data, Candidates, Organizations, and Settings
-- This script removes these menus and all their child menus

-- First, delete child menus of these parent menus
DELETE FROM menus 
WHERE parent_id IN (
    SELECT id FROM (
        SELECT id FROM menus 
        WHERE title IN ('Master Data', 'Candidates', 'Organizations', 'Settings', 'Setting')
    ) AS temp
);

-- Then, delete the parent menus themselves
DELETE FROM menus 
WHERE title IN ('Master Data', 'Candidates', 'Organizations', 'Settings', 'Setting');

-- Verify deletion (optional - uncomment to see remaining menus)
-- SELECT id, parent_id, title, url, sort_order 
-- FROM menus 
-- WHERE parent_id IS NULL 
-- ORDER BY sort_order;
