-- Navigation tab reorder:
--  1. Audit gets its own category so it can be placed as the FIRST (left-most)
--     tab for all roles (ranking happens in AppController::$categoryOrder).
--  2. Fix menu 116 "Training Dashboard" lowercase category 'training' -> 'Training'
--     (case mismatch made it rank as "unknown" = last, which would otherwise
--     land it to the right of Apprentice in Japan).
-- The actual left-to-right order lives in src/Controller/AppController.php
-- ($categoryOrder): Audit first, 'Apprentice in Japan' moved to the far right.

USE cms_masters;

UPDATE menus SET category = 'Audit', modified = NOW() WHERE id = 104;
UPDATE menus SET category = 'Training', modified = NOW() WHERE id = 116 AND category = 'training';

SELECT id, title, category FROM menus WHERE id IN (104, 116);
