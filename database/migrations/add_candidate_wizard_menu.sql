-- The candidate registration wizard had no way in.
--
-- /candidates/wizard is the six-step registration form with the CV preview
-- beside it, and the only links to it are a button on /candidates/dashboard
-- and one on /candidates/help. Nothing in the menu points at it: menu 24,
-- "Register New Candidate", goes to /candidates/add - the plain form, no
-- preview. So the screen was reachable only by typing the address.
--
-- This adds a menu row of its own rather than repointing menu 24, because the
-- plain form is still the shorter way in when nobody needs the CV. It sits
-- directly after it under the same Recruitment parent (23).
--
-- Every role that has menu 24 gets the new one with the same granted_actions,
-- so whoever may register a candidate may do it here too. Note what '*'
-- expands to for this row - [wizard, index, view], which does NOT contain add
-- - and see CandidatesController::isAuthorized(), which reads either grant for
-- exactly that reason.
--
-- Safe to run twice: nothing happens the second time.
--
--   php bin/apply-migration.php database/migrations/add_candidate_wizard_menu.sql
--   php bin/apply-migration.php database/migrations/add_candidate_wizard_menu.sql --go

USE cms_masters;

-- Read before anything is written, so a second run shifts nothing.
SET @already := (SELECT COUNT(*) FROM menus WHERE url = '/candidates/wizard');
SET @after := (SELECT sort_order FROM menus WHERE id = 24);

-- Make room directly after "Register New Candidate".
UPDATE menus
   SET sort_order = sort_order + 1, modified = NOW()
 WHERE parent_id = 23
   AND sort_order > @after
   AND @already = 0;

INSERT INTO menus
    (parent_id, title, controller, action, url, icon, target, sort_order,
     is_active, created, modified)
SELECT 23, 'Register Candidate with CV Preview', 'Candidates', 'wizard',
       '/candidates/wizard', 'fa-id-card', '_self', @after + 1, 1, NOW(), NOW()
  FROM DUAL
 WHERE @already = 0;

SET @wizard := (SELECT id FROM menus WHERE url = '/candidates/wizard' LIMIT 1);

-- The same roles, with the same granted_actions, as the plain form's menu.
INSERT INTO cms_authentication_authorization.role_menus
    (role_id, menu_id, is_active, granted_actions, created, modified)
SELECT rm.role_id, @wizard, rm.is_active, rm.granted_actions, NOW(), NOW()
  FROM cms_authentication_authorization.role_menus rm
 WHERE rm.menu_id = 24
   AND @wizard IS NOT NULL
   AND NOT EXISTS (
       SELECT 1
         FROM cms_authentication_authorization.role_menus already
        WHERE already.role_id = rm.role_id
          AND already.menu_id = @wizard
   );

-- What it did.
SELECT m.id, m.title, m.controller, m.action, m.url, m.sort_order, m.is_active
  FROM menus m
 WHERE m.parent_id = 23
 ORDER BY m.sort_order, m.id;
