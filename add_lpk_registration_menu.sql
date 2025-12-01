-- Add LPK Registration Menu to Enhanced Tab Menu
-- This adds the new Phase 3-4 LPK Registration feature to the admin menu structure
-- Database: cms_authentication_authorization
-- Table: menus

USE cms_authentication_authorization;

-- ============================================================================
-- STEP 1: Check if "Admin" parent menu exists, if not create it
-- ============================================================================

-- Create Admin menu as parent if it doesn't exist
INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
SELECT NULL, 'Admin', '#', 'fa-user-shield', 10, 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM menus WHERE title = 'Admin' AND parent_id IS NULL
);

-- Get the Admin menu ID
SET @adminMenuId = (SELECT id FROM menus WHERE title = 'Admin' AND parent_id IS NULL LIMIT 1);

-- ============================================================================
-- STEP 2: Add "LPK Registration" submenu under Admin
-- ============================================================================

-- Remove old entries if they exist (for clean reinstall)
DELETE FROM menus 
WHERE title = 'LPK Registration' 
AND parent_id = @adminMenuId;

-- Insert LPK Registration parent item
INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES (@adminMenuId, 'LPK Registration', '/admin/lpk-registration', 'fa-university', 1, 1, NOW(), NOW());

SET @lpkRegMenuId = LAST_INSERT_ID();

-- ============================================================================
-- STEP 3: Add submenu items under LPK Registration
-- ============================================================================

INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES 
-- List all LPK registrations
(@lpkRegMenuId, 'View Registrations', '/admin/lpk-registration', 'fa-list', 1, 1, NOW(), NOW()),

-- Register new LPK (3-step wizard)
(@lpkRegMenuId, 'Register New LPK', '/admin/lpk-registration/create', 'fa-plus-circle', 2, 1, NOW(), NOW()),

-- Reports and statistics
(@lpkRegMenuId, 'Registration Reports', '/admin/lpk-registration/reports', 'fa-chart-bar', 3, 1, NOW(), NOW()),

-- Help and documentation
(@lpkRegMenuId, 'Help & Documentation', '/admin/lpk-registration/help', 'fa-question-circle', 4, 1, NOW(), NOW());

-- ============================================================================
-- STEP 4: Add "Email Tokens" submenu for administrators (under Admin)
-- ============================================================================

-- Remove old entries if they exist
DELETE FROM menus 
WHERE title = 'Email Verification Tokens' 
AND parent_id = @adminMenuId;

-- Insert Email Tokens management
INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES (@adminMenuId, 'Email Verification Tokens', '/admin/email-verification-tokens', 'fa-envelope-open-text', 2, 1, NOW(), NOW());

SET @emailTokensMenuId = LAST_INSERT_ID();

-- Add submenu items
INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES 
-- View all tokens
(@emailTokensMenuId, 'View All Tokens', '/admin/email-verification-tokens', 'fa-list', 1, 1, NOW(), NOW()),

-- Token statistics
(@emailTokensMenuId, 'Token Statistics', '/admin/email-verification-tokens/stats', 'fa-chart-pie', 2, 1, NOW(), NOW()),

-- Cleanup expired tokens
(@emailTokensMenuId, 'Cleanup Expired', '/admin/email-verification-tokens/cleanup', 'fa-broom', 3, 1, NOW(), NOW());

-- ============================================================================
-- STEP 5: Update existing "Vocational Training Institutions" menu (if needed)
-- ============================================================================

-- Make sure existing LPK menu has proper icon and is active
UPDATE menus 
SET icon = 'fa-graduation-cap',
    is_active = 1,
    modified = NOW()
WHERE title = 'Vocational Training Institutions'
AND url LIKE '%vocational-training-institutions%';

-- ============================================================================
-- VERIFICATION QUERY
-- ============================================================================
-- View the complete Admin menu structure with LPK Registration

SELECT 
    CASE 
        WHEN m.parent_id IS NULL THEN CONCAT('● ', m.title)
        WHEN m2.parent_id IS NULL THEN CONCAT('  ├─ ', m.title)
        ELSE CONCAT('     ├─ ', m.title)
    END AS menu_structure,
    m.id,
    m.url,
    m.icon,
    m.sort_order,
    CASE WHEN m.is_active = 1 THEN '✓' ELSE '✗' END AS active
FROM menus m
LEFT JOIN menus m2 ON m.parent_id = m2.id
WHERE m.id = @adminMenuId
   OR m.parent_id = @adminMenuId
   OR m.parent_id IN (SELECT id FROM menus WHERE parent_id = @adminMenuId)
ORDER BY 
    COALESCE(m2.id, m.parent_id, m.id),
    COALESCE(m.parent_id, m.id),
    m.sort_order;

-- ============================================================================
-- SUMMARY
-- ============================================================================
-- This script adds:
-- 1. Admin menu (parent) - if not exists
-- 2. LPK Registration submenu with 4 items:
--    - View Registrations
--    - Register New LPK
--    - Registration Reports
--    - Help & Documentation
-- 3. Email Verification Tokens submenu with 3 items:
--    - View All Tokens
--    - Token Statistics
--    - Cleanup Expired
--
-- Menu appears in enhanced tab menu at position 10 (after other menus)
-- All items active and properly nested
--
-- To deploy:
-- mysql -u root -p cms_authentication_authorization < add_lpk_registration_menu.sql
--
-- Or via phpMyAdmin:
-- 1. Select database: cms_authentication_authorization
-- 2. Click SQL tab
-- 3. Copy/paste this entire file
-- 4. Click Go
-- ============================================================================

-- Show success message
SELECT 
    'LPK Registration menu added successfully!' AS status,
    COUNT(*) AS total_menu_items,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_items
FROM menus
WHERE parent_id = @adminMenuId 
   OR id = @adminMenuId
   OR parent_id IN (SELECT id FROM menus WHERE parent_id = @adminMenuId);
