SELECT id, parent_id, title, url, icon, sort_order, is_active 
FROM menus 
WHERE title IN ('Stakeholder Management', 'Users', 'Roles', 'Help')
ORDER BY parent_id, sort_order;
