-- Documentation role: replace the Candidate List with a Trainee List.
--
-- The tmm-documentation role manages pre-departure documents for TRAINEES,
-- so its navigation should show the trainee list, not /candidates.
--
-- 1. Create a "Trainee List" menu under "TMM Documentation" (menu 78)
--    (no Trainees::index menu existed anywhere before).
-- 2. Grant it to role 5 (tmm-documentation) with read-only actions.
-- 3. Deactivate the role's "Candidate Management" tab (23) and
--    "Candidate List" (25) assignments - this also revokes the role's
--    Candidates::index/view permission.
-- Candidate DOCUMENT menus (76, 80) are intentionally kept - documents
-- remain the role's responsibility.

USE cms_masters;

-- Top-level Documentation tab (sort_order 0 puts it first, before Trainee Submissions)
INSERT INTO menus (category, parent_id, title, url, controller, action, icon, target, sort_order, is_active, created, modified)
SELECT 'Documentation', NULL, 'Trainee List', '/trainees', 'Trainees', 'index', 'fas fa-users', '_self', 0, 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM menus WHERE controller = 'Trainees' AND action = 'index' AND title = 'Trainee List'
);

SET @trainee_list_menu_id = (
    SELECT id FROM menus WHERE controller = 'Trainees' AND action = 'index' AND title = 'Trainee List' LIMIT 1
);

-- (If the menu was previously created as a child of menu 78, promote it to top level)
UPDATE menus SET parent_id = NULL, sort_order = 0, modified = NOW() WHERE id = @trainee_list_menu_id;

USE cms_authentication_authorization;

INSERT INTO role_menus (role_id, menu_id, is_active, granted_actions, created, modified)
SELECT 5, @trainee_list_menu_id, 1, 'index,view', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM role_menus WHERE role_id = 5 AND menu_id = @trainee_list_menu_id
);

UPDATE role_menus SET is_active = 0, modified = NOW()
WHERE role_id = 5 AND menu_id IN (23, 25);

-- Verify
SELECT rm.menu_id, m.title, m.controller, m.action, rm.granted_actions, rm.is_active
FROM role_menus rm
JOIN cms_masters.menus m ON m.id = rm.menu_id
WHERE rm.role_id = 5 AND (m.controller IN ('Candidates', 'Trainees') OR m.id = 23)
ORDER BY rm.menu_id;
