-- Verify Current Menu Structure
SELECT 
    CASE WHEN parent_id IS NULL THEN '●' ELSE '  ├─' END AS level,
    id, 
    title, 
    url, 
    sort_order,
    is_active
FROM menus 
WHERE parent_id IS NULL 
   OR parent_id IN (SELECT id FROM menus WHERE parent_id IS NULL)
ORDER BY COALESCE(parent_id, id), sort_order;
