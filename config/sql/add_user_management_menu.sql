-- Add Stakeholder Management Menu Items

-- Insert Parent Menu (sort_order = 1 to appear first)
INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES (NULL, 'Stakeholder Management', '#', 'fa-users-cog', 1, 1, NOW(), NOW());

-- Get the ID of the inserted parent
SET @parentId = LAST_INSERT_ID();

-- Insert Child Menus
INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES 
(@parentId, 'Users', '/users', 'fa-user', 1, 1, NOW(), NOW()),
(@parentId, 'Roles', '/roles', 'fa-id-badge', 2, 1, NOW(), NOW()),
(@parentId, 'Help', '/users/help', 'fa-question-circle', 3, 1, NOW(), NOW());
