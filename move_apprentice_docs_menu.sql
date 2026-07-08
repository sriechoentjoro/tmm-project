-- Move "Apprentice Docs" (menu 77) from a top-level Documentation tab into the
-- "Apprentice Management" parent menu (59, category 'Apprentice in Japan'),
-- after Update Apprentice Info / Apprentice Stories / Post-Apprentice Records.
-- Also grant tmm-documentation (role 5) explicit full actions on it
-- ('*' only expands to index,view in AppController).

USE cms_masters;

UPDATE menus
SET parent_id = 59,
    category = 'Apprentice in Japan',
    sort_order = 4,
    modified = NOW()
WHERE id = 77;

USE cms_authentication_authorization;

UPDATE role_menus
SET granted_actions = 'index,view,add,edit,delete', modified = NOW()
WHERE role_id = 5 AND menu_id = 77;

-- Verify
SELECT m.id, m.title, m.parent_id, m.category, m.sort_order, rm.granted_actions
FROM cms_masters.menus m
LEFT JOIN role_menus rm ON rm.menu_id = m.id AND rm.role_id = 5
WHERE m.id = 77 OR m.parent_id = 59
ORDER BY m.sort_order;
