-- Add Process Menu with Process Sequence submenu
-- This script adds a new "Process" parent menu with "Process Sequence" child menu

-- Insert Parent Menu "Process"
INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES (NULL, 'Process', '#', 'fa-cogs', 4, 1, NOW(), NOW());

-- Get the ID of the inserted parent
SET @processMenuId = LAST_INSERT_ID();

-- Insert Child Menu "Process Sequence"
INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES 
(@processMenuId, 'Process Sequence', '/candidates/dashboard', 'fa-list-ol', 1, 1, NOW(), NOW());

-- Verification Query
-- SELECT 
--     CASE WHEN parent_id IS NULL THEN '●' ELSE '  ├─' END AS level,
--     id, 
--     title, 
--     url, 
--     icon, 
--     sort_order 
-- FROM menus 
-- WHERE parent_id IS NULL 
--    OR parent_id IN (SELECT id FROM menus WHERE title = 'Process')
-- ORDER BY COALESCE(parent_id, id), sort_order;
