-- Complete Menu Structure for TMM System
-- This script creates three main menu categories with their respective submenus

-- First, let's clear any existing menus to start fresh (optional - comment out if you want to keep existing menus)
-- DELETE FROM menus WHERE title IN ('Stakeholder Management', 'Authentication & Authorization', 'LPK Menu');

-- ============================================================================
-- 1. STAKEHOLDER MANAGEMENT MENU (sort_order = 1)
-- ============================================================================
-- Purpose: Managing stakeholder data in cms_tmm_stakeholders database
-- Includes: Vocational Training Institutions, Special Skill Support Institutions, and other stakeholders

INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES (NULL, 'Stakeholder Management', '#', 'fa-users-cog', 1, 1, NOW(), NOW());

SET @stakeholderMenuId = LAST_INSERT_ID();

INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES 
(@stakeholderMenuId, 'Dashboard', '/dashboard/stakeholders', 'fa-tachometer-alt', 1, 1, NOW(), NOW()),
(@stakeholderMenuId, 'Help', '/stakeholders/help', 'fa-question-circle', 2, 1, NOW(), NOW()),
(@stakeholderMenuId, 'Vocational Training Institutions', '/vocational-training-institutions', 'fa-graduation-cap', 3, 1, NOW(), NOW()),
(@stakeholderMenuId, 'Special Skill Support Institutions', '/special-skill-support-institutions', 'fa-hands-helping', 4, 1, NOW(), NOW()),
(@stakeholderMenuId, 'Acceptance Organizations', '/acceptance-organizations', 'fa-building', 5, 1, NOW(), NOW()),
(@stakeholderMenuId, 'Cooperative Associations', '/cooperative-associations', 'fa-handshake', 6, 1, NOW(), NOW());

-- ============================================================================
-- 2. AUTHENTICATION & AUTHORIZATION MENU (sort_order = 2)
-- ============================================================================
-- Purpose: Managing cms_authentication_authorization database
-- Includes: Users, Roles, User-Role assignments, and related auth tables

INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES (NULL, 'Authentication & Authorization', '#', 'fa-shield-alt', 2, 1, NOW(), NOW());

SET @authMenuId = LAST_INSERT_ID();

INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES 
(@authMenuId, 'Dashboard', '/dashboard/authentication', 'fa-tachometer-alt', 1, 1, NOW(), NOW()),
(@authMenuId, 'Help', '/users/help', 'fa-question-circle', 2, 1, NOW(), NOW()),
(@authMenuId, 'Users', '/users', 'fa-user', 3, 1, NOW(), NOW()),
(@authMenuId, 'Roles', '/roles', 'fa-id-badge', 4, 1, NOW(), NOW()),
(@authMenuId, 'User Roles', '/user-roles', 'fa-user-tag', 5, 1, NOW(), NOW()),
(@authMenuId, 'Sessions', '/sessions', 'fa-clock', 6, 1, NOW(), NOW());

-- ============================================================================
-- 3. LPK MENU (sort_order = 3)
-- ============================================================================
-- Purpose: LPK-specific candidate management (cms_lpk_candidates & cms_lpk_candidate_documents)
-- Features: Wizard registration, document submission, institution-filtered data

INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES (NULL, 'LPK Menu', '#', 'fa-user-graduate', 3, 1, NOW(), NOW());

SET @lpkMenuId = LAST_INSERT_ID();

INSERT INTO menus (parent_id, title, url, icon, sort_order, is_active, created, modified)
VALUES 
(@lpkMenuId, 'Dashboard', '/dashboard/lpk', 'fa-tachometer-alt', 1, 1, NOW(), NOW()),
(@lpkMenuId, 'Help', '/candidates/help', 'fa-question-circle', 2, 1, NOW(), NOW()),
(@lpkMenuId, 'Candidate Registration (Wizard)', '/candidates/wizard', 'fa-user-plus', 3, 1, NOW(), NOW()),
(@lpkMenuId, 'My Candidates', '/candidates', 'fa-users', 4, 1, NOW(), NOW()),
(@lpkMenuId, 'Document Submission', '/candidate-documents', 'fa-file-upload', 5, 1, NOW(), NOW()),
(@lpkMenuId, 'Document Dashboard', '/candidate-document-management-dashboard-details', 'fa-chart-bar', 6, 1, NOW(), NOW()),
(@lpkMenuId, 'Document Submission Help', '/candidate-documents/help', 'fa-info-circle', 7, 1, NOW(), NOW());

-- ============================================================================
-- Verification Query
-- ============================================================================
-- Uncomment to verify the menu structure after insertion:
-- SELECT 
--     CASE WHEN parent_id IS NULL THEN '●' ELSE '  ├─' END AS level,
--     id, 
--     title, 
--     url, 
--     icon, 
--     sort_order 
-- FROM menus 
-- WHERE parent_id IS NULL 
--    OR parent_id IN (SELECT id FROM menus WHERE title IN ('Stakeholder Management', 'Authentication & Authorization', 'LPK Menu'))
-- ORDER BY COALESCE(parent_id, id), sort_order;
