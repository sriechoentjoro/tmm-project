-- SQL Script: Create Temporary Menu Data for Elegant Tab Menu
-- Database: cms_masters (default connection)
-- Purpose: Insert sample menu structure for testing elegant menu display

USE cms_masters;

-- Create menus table if not exists (in case it's missing)
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(500) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `target` varchar(20) DEFAULT '_self',
  `sort_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clear existing menu data (optional - comment out if you want to keep existing menus)
-- TRUNCATE TABLE menus;

-- Insert Main Parent Menus (Top Level Tabs)
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
(1, NULL, 'Dashboard', '/dashboard', 'fa-home', '_self', 1, 1, NOW(), NOW()),
(2, NULL, 'Master Data', NULL, 'fa-database', '_self', 2, 1, NOW(), NOW()),
(3, NULL, 'Candidates', NULL, 'fa-users', '_self', 3, 1, NOW(), NOW()),
(4, NULL, 'Trainees', NULL, 'fa-graduation-cap', '_self', 4, 1, NOW(), NOW()),
(5, NULL, 'Organizations', NULL, 'fa-building', '_self', 5, 1, NOW(), NOW()),
(6, NULL, 'Training', NULL, 'fa-chalkboard-teacher', '_self', 6, 1, NOW(), NOW()),
(7, NULL, 'Reports', NULL, 'fa-chart-bar', '_self', 7, 1, NOW(), NOW()),
(8, NULL, 'Settings', NULL, 'fa-cog', '_self', 8, 1, NOW(), NOW());

-- Insert Child Menus for "Master Data" (ID: 2)
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
(21, 2, 'Job Categories', '/master-job-categories', 'fa-briefcase', '_self', 1, 1, NOW(), NOW()),
(22, 2, 'Provinces', '/master-propinsis', 'fa-map-marker-alt', '_self', 2, 1, NOW(), NOW()),
(23, 2, 'Cities/Districts', '/master-kabupatens', 'fa-city', '_self', 3, 1, NOW(), NOW()),
(24, 2, 'Subdistricts', '/master-kecamatans', 'fa-map', '_self', 4, 1, NOW(), NOW()),
(25, 2, 'Villages', '/master-kelurahans', 'fa-home', '_self', 5, 1, NOW(), NOW());

-- Insert Child Menus for "Candidates" (ID: 3)
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
(31, 3, 'All Candidates', '/candidates', 'fa-list', '_self', 1, 1, NOW(), NOW()),
(32, 3, 'Add New Candidate', '/candidates/add', 'fa-plus', '_self', 2, 1, NOW(), NOW()),
(33, 3, 'Candidate Education', '/candidate-educations', 'fa-school', '_self', 3, 1, NOW(), NOW()),
(34, 3, 'Candidate Courses', '/candidate-courses', 'fa-book', '_self', 4, 1, NOW(), NOW()),
(35, 3, 'Candidate Experiences', '/candidate-experiences', 'fa-suitcase', '_self', 5, 1, NOW(), NOW());

-- Insert Child Menus for "Trainees" (ID: 4)
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
(41, 4, 'All Trainees', '/trainees', 'fa-list', '_self', 1, 1, NOW(), NOW()),
(42, 4, 'Add New Trainee', '/trainees/add', 'fa-plus', '_self', 2, 1, NOW(), NOW()),
(43, 4, 'Trainee Education', '/trainee-educations', 'fa-school', '_self', 3, 1, NOW(), NOW()),
(44, 4, 'Trainee Courses', '/trainee-courses', 'fa-book', '_self', 4, 1, NOW(), NOW()),
(45, 4, 'Trainee Experiences', '/trainee-experiences', 'fa-suitcase', '_self', 5, 1, NOW(), NOW()),
(46, 4, 'Trainee Families', '/trainee-families', 'fa-users', '_self', 6, 1, NOW(), NOW()),
(47, 4, 'Training Batches', '/trainee-training-batches', 'fa-layer-group', '_self', 7, 1, NOW(), NOW());

-- Insert Child Menus for "Organizations" (ID: 5)
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
(51, 5, 'Cooperative Associations', '/cooperative-associations', 'fa-handshake', '_self', 1, 1, NOW(), NOW()),
(52, 5, 'Acceptance Organizations', '/acceptance-organizations', 'fa-building', '_self', 2, 1, NOW(), NOW()),
(53, 5, 'Training Institutions', '/vocational-training-institutions', 'fa-university', '_self', 3, 1, NOW(), NOW()),
(54, 5, 'Organization Stories', '/acceptance-organization-stories', 'fa-file-alt', '_self', 4, 1, NOW(), NOW()),
(55, 5, 'Association Stories', '/cooperative-association-stories', 'fa-book', '_self', 5, 1, NOW(), NOW());

-- Insert Child Menus for "Training" (ID: 6)
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
(61, 6, 'Apprentice Orders', '/apprentice-orders', 'fa-clipboard-list', '_self', 1, 1, NOW(), NOW()),
(62, 6, 'Apprentices', '/apprentices', 'fa-user-graduate', '_self', 2, 1, NOW(), NOW()),
(63, 6, 'Departure Orders', '/departure-orders', 'fa-plane-departure', '_self', 3, 1, NOW(), NOW()),
(64, 6, 'Post Training', '/post-trainings', 'fa-certificate', '_self', 4, 1, NOW(), NOW()),
(65, 6, 'Return Orders', '/return-orders', 'fa-plane-arrival', '_self', 5, 1, NOW(), NOW());

-- Insert Child Menus for "Reports" (ID: 7)
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
(71, 7, 'Candidate Reports', '/reports/candidates', 'fa-file-pdf', '_self', 1, 1, NOW(), NOW()),
(72, 7, 'Trainee Reports', '/reports/trainees', 'fa-file-pdf', '_self', 2, 1, NOW(), NOW()),
(73, 7, 'Training Reports', '/reports/training', 'fa-file-pdf', '_self', 3, 1, NOW(), NOW()),
(74, 7, 'Organization Reports', '/reports/organizations', 'fa-file-pdf', '_self', 4, 1, NOW(), NOW()),
(75, 7, 'Export Data', '/reports/export', 'fa-download', '_self', 5, 1, NOW(), NOW());

-- Insert Child Menus for "Settings" (ID: 8)
INSERT INTO `menus` (`id`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
(81, 8, 'Users', '/users', 'fa-users-cog', '_self', 1, 1, NOW(), NOW()),
(82, 8, 'Roles & Permissions', '/roles', 'fa-user-shield', '_self', 2, 1, NOW(), NOW()),
(83, 8, 'System Settings', '/settings', 'fa-sliders-h', '_self', 3, 1, NOW(), NOW()),
(84, 8, 'Menu Management', '/menus', 'fa-bars', '_self', 4, 1, NOW(), NOW()),
(85, 8, 'Logs', '/logs', 'fa-history', '_self', 5, 1, NOW(), NOW());

-- Verify inserted data
SELECT 
    m.id,
    m.parent_id,
    m.title,
    m.url,
    m.icon,
    m.sort_order,
    CASE WHEN m.parent_id IS NULL THEN 'Parent Menu' ELSE 'Child Menu' END as menu_type
FROM menus m
ORDER BY 
    COALESCE(m.parent_id, m.id),
    m.sort_order;

-- Count summary
SELECT 
    CASE WHEN parent_id IS NULL THEN 'Parent Menus' ELSE 'Child Menus' END as type,
    COUNT(*) as total
FROM menus
GROUP BY CASE WHEN parent_id IS NULL THEN 'Parent Menus' ELSE 'Child Menus' END;

-- Show menu structure (hierarchical)
SELECT 
    CONCAT(
        CASE WHEN m.parent_id IS NULL 
            THEN CONCAT('📁 ', m.title) 
            ELSE CONCAT('  └─ ', m.title) 
        END
    ) as menu_structure,
    m.url,
    m.icon
FROM menus m
ORDER BY 
    COALESCE(m.parent_id, m.id),
    m.sort_order;
