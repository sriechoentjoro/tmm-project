-- Documentation category tab order:
--   (Dashboard tab) -> TMM Documentation -> Candidate Docs -> Trainee List
--   -> Trainee Submissions -> Departure Docs -> Ticketing & Transit

USE cms_masters;

UPDATE menus SET sort_order = 1, modified = NOW() WHERE id = 78;  -- TMM Documentation
UPDATE menus SET sort_order = 2, modified = NOW() WHERE id = 76;  -- Candidate Docs
UPDATE menus SET sort_order = 3, modified = NOW() WHERE id = 118; -- Trainee List
UPDATE menus SET sort_order = 4, modified = NOW() WHERE id = 63;  -- Trainee Submissions
UPDATE menus SET sort_order = 5, modified = NOW() WHERE id = 67;  -- Departure Docs
UPDATE menus SET sort_order = 6, modified = NOW() WHERE id = 71;  -- Ticketing & Transit

SELECT id, title, sort_order FROM menus
WHERE parent_id IS NULL AND category = 'Documentation' AND is_active = 1
ORDER BY sort_order;
