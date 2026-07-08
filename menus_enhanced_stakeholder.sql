-- =====================================================================
-- cms_masters.menus — Enhanced menu with `category` column
-- Generated: 2026-06-10
--
-- Changes vs previous live table:
--   * Adds `category` column; IDs renumbered sequentially 1..113
--   * New "Stakeholder" category covering all four stakeholder types:
--       - Vocational Training Institutions (LPK)  + stories
--       - Acceptance Organizations                + stories
--       - Cooperative Associations                + stories
--       - Special Skill Support Institutions
--   * Preserves User Management, Master Data, Reports submenus from
--     the previous live table
--   * Table converted to utf8mb4 to match the app datasource config
--
-- Rollback: mysql -u tmm_user -p cms_masters < /root/menus_backup_20260610.sql
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `category` varchar(256) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(500) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `target` varchar(20) DEFAULT '_self',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menus` (`id`, `category`, `parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `is_active`, `created`, `modified`) VALUES
-- ------------------------------------------------ Dashboards
(1,  'Dashboards', NULL, 'Dashboard', '/dashboard', 'fas fa-tachometer-alt', '_self', 1, 1, NOW(), NOW()),
(2,  'Dashboards', 1, 'Recruitment Dashboard', '/dashboard/recruitment', 'fas fa-user-tie', '_self', 1, 1, NOW(), NOW()),
(3,  'Dashboards', 1, 'Training Dashboard', '/dashboard/training', 'fas fa-graduation-cap', '_self', 2, 1, NOW(), NOW()),
(4,  'Dashboards', 1, 'Documentation Dashboard', '/dashboard/documentation', 'fas fa-file-signature', '_self', 3, 1, NOW(), NOW()),
(5,  'Dashboards', 1, 'Accounting Dashboard', '/dashboard/accounting', 'fas fa-calculator', '_self', 4, 1, NOW(), NOW()),
-- ------------------------------------------------ Stakeholder
(6,  'Stakeholder', NULL, 'Vocational Training Institutions (LPK)', '#', 'fas fa-school', '_self', 1, 1, NOW(), NOW()),
(7,  'Stakeholder', 6, 'Register LPK', '/vocational-training-institutions/register', 'fas fa-plus-circle', '_self', 1, 1, NOW(), NOW()),
(8,  'Stakeholder', 6, 'Manage LPK', '/vocational-training-institutions', 'fas fa-list', '_self', 2, 1, NOW(), NOW()),
(9,  'Stakeholder', 6, 'LPK Verification', '/vocational-training-institutions/verify', 'fas fa-check-circle', '_self', 3, 1, NOW(), NOW()),
(10, 'Stakeholder', 6, 'LPK Registration (Admin)', '/admin/lpk-registration/create', 'fas fa-user-plus', '_self', 4, 1, NOW(), NOW()),
(11, 'Stakeholder', 6, 'LPK Stories', '/vocational-training-institution-stories', 'fas fa-book-open', '_self', 5, 1, NOW(), NOW()),
(12, 'Stakeholder', NULL, 'Acceptance Organizations', '#', 'fas fa-building', '_self', 2, 1, NOW(), NOW()),
(13, 'Stakeholder', 12, 'Add Organization', '/acceptance-organizations/add', 'fas fa-plus-circle', '_self', 1, 1, NOW(), NOW()),
(14, 'Stakeholder', 12, 'Manage Organizations', '/acceptance-organizations', 'fas fa-list', '_self', 2, 1, NOW(), NOW()),
(15, 'Stakeholder', 12, 'Organization Stories', '/acceptance-organization-stories', 'fas fa-book-open', '_self', 3, 1, NOW(), NOW()),
(16, 'Stakeholder', NULL, 'Cooperative Associations', '#', 'fas fa-handshake', '_self', 3, 1, NOW(), NOW()),
(17, 'Stakeholder', 16, 'Add Cooperative', '/cooperative-associations/add', 'fas fa-plus-circle', '_self', 1, 1, NOW(), NOW()),
(18, 'Stakeholder', 16, 'Manage Cooperatives', '/cooperative-associations', 'fas fa-list', '_self', 2, 1, NOW(), NOW()),
(19, 'Stakeholder', 16, 'Cooperative Stories', '/cooperative-association-stories', 'fas fa-book-open', '_self', 3, 1, NOW(), NOW()),
(20, 'Stakeholder', NULL, 'Special Skill Support Institutions', '#', 'fas fa-user-shield', '_self', 4, 1, NOW(), NOW()),
(21, 'Stakeholder', 20, 'Add Institution', '/special-skill-support-institutions/add', 'fas fa-plus-circle', '_self', 1, 1, NOW(), NOW()),
(22, 'Stakeholder', 20, 'Manage Institutions', '/special-skill-support-institutions', 'fas fa-list', '_self', 2, 1, NOW(), NOW()),
-- ------------------------------------------------ Recruitment
(23, 'Recruitment', NULL, 'Candidate Management', '#', 'fas fa-users', '_self', 1, 1, NOW(), NOW()),
(24, 'Recruitment', 23, 'Register New Candidate', '/candidates/add', 'fas fa-magic', '_self', 1, 1, NOW(), NOW()),
(25, 'Recruitment', 23, 'Candidate List', '/candidates', 'fas fa-users', '_self', 2, 1, NOW(), NOW()),
(26, 'Recruitment', 23, 'Candidate Document Management', '/candidate-documents', 'fas fa-folder-open', '_self', 3, 1, NOW(), NOW()),
(27, 'Recruitment', 23, 'Candidate Physical Test', '/lpk-physical-tests', 'fas fa-running', '_self', 4, 1, NOW(), NOW()),
(28, 'Recruitment', 23, 'Candidate Medical Check-Up', '/candidate-record-medical-check-ups', 'fas fa-heartbeat', '_self', 5, 1, NOW(), NOW()),
(29, 'Recruitment', 23, 'Candidate Interview Records', '/candidate-record-interviews', 'fas fa-comments', '_self', 6, 1, NOW(), NOW()),
(30, 'Recruitment', 23, 'Candidate Integrated Scoring', '/lpk-candidate-scoring', 'fas fa-clipboard-check', '_self', 7, 1, NOW(), NOW()),
(31, 'Recruitment', NULL, 'Apprentice Orders', '#', 'fas fa-clipboard-list', '_self', 2, 1, NOW(), NOW()),
(32, 'Recruitment', 31, 'Create New Order', '/apprentice-orders/add', 'fas fa-plus-circle', '_self', 1, 1, NOW(), NOW()),
(33, 'Recruitment', 31, 'Manage Orders', '/apprentice-orders', 'fas fa-list', '_self', 2, 1, NOW(), NOW()),
(34, 'Recruitment', 31, 'Order Statistics', '/apprentice-orders/statistics', 'fas fa-chart-pie', '_self', 3, 1, NOW(), NOW()),
(35, 'Recruitment', 31, 'Email Templates', '/email-templates', 'fas fa-envelope-open-text', '_self', 4, 1, NOW(), NOW()),
(36, 'Recruitment', NULL, 'Candidate Promotion', '#', 'fas fa-user-graduate', '_self', 3, 1, NOW(), NOW()),
(37, 'Recruitment', 36, 'Promote to Trainee', '/candidates/promote-to-trainee', 'fas fa-level-up-alt', '_self', 1, 1, NOW(), NOW()),
(38, 'Recruitment', 36, 'Promotion History', '/candidates/promotion-history', 'fas fa-history', '_self', 2, 1, NOW(), NOW()),
-- ------------------------------------------------ Training
(39, 'Training', NULL, 'Training Batches', '/trainee-training-batches/index', 'fas fa-chalkboard-teacher', '_self', 1, 1, NOW(), NOW()),
(40, 'Training', 39, 'Create Training Batch', '/trainee-training-batches/add', 'fas fa-plus-circle', '_self', 1, 1, NOW(), NOW()),
(41, 'Training', 39, 'Manage Batches', '/trainee-training-batches/index', 'fas fa-list', '_self', 2, 1, NOW(), NOW()),
(42, 'Training', 39, 'Register Trainees', '/trainee-training-batches/register', 'fas fa-user-plus', '_self', 3, 1, NOW(), NOW()),
(43, 'Training', 39, 'Location Shifting', '/trainings/location-shift', 'fas fa-exchange-alt', '_self', 4, 1, NOW(), NOW()),
(44, 'Training', NULL, 'Additional Training', '/trainees/additional-training', 'fas fa-plus-square', '_self', 2, 1, NOW(), NOW()),
(45, 'Training', 44, 'Add Additional Training', '/trainees/additional-training', 'fas fa-plus-circle', '_self', 1, 1, NOW(), NOW()),
(46, 'Training', 44, 'Manage Additional Training', '/trainees/additional-training', 'fas fa-list', '_self', 2, 1, NOW(), NOW()),
(47, 'Training', NULL, 'Training Scoring', '/trainee-training-test-scores/daily', 'fas fa-star', '_self', 3, 1, NOW(), NOW()),
(48, 'Training', 47, 'Daily Score Entry', '/trainee-training-test-scores/daily', 'fas fa-edit', '_self', 1, 1, NOW(), NOW()),
(49, 'Training', 47, 'Score Summary', '/trainee-training-test-scores/index', 'fas fa-table', '_self', 2, 1, NOW(), NOW()),
(50, 'Training', 47, 'Pass/Fail Report', '/trainee-training-test-scores/report', 'fas fa-check-double', '_self', 3, 1, NOW(), NOW()),
(51, 'Training', NULL, 'Certificates & Cards', '/trainee-name-cards/index', 'fas fa-certificate', '_self', 4, 1, NOW(), NOW()),
(52, 'Training', 51, 'Issue Certificate', '/trainee-certificates/index', 'fas fa-award', '_self', 1, 1, NOW(), NOW()),
(53, 'Training', 51, 'Issue Name Card', '/trainee-name-cards/index', 'fas fa-id-card', '_self', 2, 1, NOW(), NOW()),
(54, 'Training', 51, 'Bulk Generation', '/trainee-name-cards/print-batch', 'fas fa-copy', '_self', 3, 1, NOW(), NOW()),
(55, 'Training', NULL, 'Trainee Promotions', '/trainees/promote-to-apprentice', 'fas fa-level-up-alt', '_self', 5, 1, NOW(), NOW()),
(56, 'Training', 55, 'Promote to Apprentice', '/trainees/promote-to-apprentice', 'fas fa-user-check', '_self', 1, 1, NOW(), NOW()),
(57, 'Training', 55, 'Promotion Checklist', '/trainees/promotion-checklist', 'fas fa-clipboard-check', '_self', 2, 1, NOW(), NOW()),
(58, 'Training', 55, 'Promotion History', '/trainees/promotion-history', 'fas fa-history', '_self', 3, 1, NOW(), NOW()),
-- ------------------------------------------------ Apprentice in Japan
(59, 'Apprentice in Japan', NULL, 'Apprentice Management', '/apprentices/index', 'fas fa-user-tie', '_self', 1, 1, NOW(), NOW()),
(60, 'Apprentice in Japan', 59, 'Update Apprentice Info', '/apprentices/index', 'fas fa-edit', '_self', 1, 1, NOW(), NOW()),
(61, 'Apprentice in Japan', 59, 'Apprentice Stories', '/apprentice-stories/index', 'fas fa-book-open', '_self', 2, 1, NOW(), NOW()),
(62, 'Apprentice in Japan', 59, 'Post-Apprentice Records', '/post-apprentices/index', 'fas fa-archive', '_self', 3, 1, NOW(), NOW()),
-- ------------------------------------------------ Documentation
(63, 'Documentation', NULL, 'Trainee Submissions', '/trainee-submission-documents', 'fas fa-file-alt', '_self', 1, 1, NOW(), NOW()),
(64, 'Documentation', 63, 'Document Checklist', '/trainee-submission-documents', 'fas fa-clipboard-list', '_self', 1, 1, NOW(), NOW()),
(65, 'Documentation', 63, 'Progress Tracking', '/trainee-submission-documents/progress', 'fas fa-tasks', '_self', 2, 1, NOW(), NOW()),
(66, 'Documentation', 63, 'Cost Management', '/trainee-submission-documents/costs', 'fas fa-dollar-sign', '_self', 3, 1, NOW(), NOW()),
(67, 'Documentation', NULL, 'Departure Docs', '/trainee-documents/departure', 'fas fa-passport', '_self', 2, 1, NOW(), NOW()),
(68, 'Documentation', 67, 'Passport Management', '/trainee-record-pasports', 'fas fa-id-card-alt', '_self', 1, 1, NOW(), NOW()),
(69, 'Documentation', 67, 'COE/Visa Management', '/trainee-record-coe-visas', 'fas fa-stamp', '_self', 2, 1, NOW(), NOW()),
(70, 'Documentation', 67, 'Health Certificates', '/trainee-record-medical-check-ups', 'fas fa-heartbeat', '_self', 3, 1, NOW(), NOW()),
(71, 'Documentation', NULL, 'Ticketing & Transit', '/trainee-documents/ticketing', 'fas fa-plane-departure', '_self', 3, 1, NOW(), NOW()),
(72, 'Documentation', 71, 'Create Ticket', '/tickets/add', 'fas fa-plus-circle', '_self', 1, 1, NOW(), NOW()),
(73, 'Documentation', 71, 'Departure Management', '/tickets/departures', 'fas fa-calendar-check', '_self', 2, 1, NOW(), NOW()),
(74, 'Documentation', 71, 'Transit Routes', '/tickets/transit', 'fas fa-route', '_self', 3, 1, NOW(), NOW()),
(75, 'Documentation', 71, 'Ticket Cost Tracking', '/tickets/costs', 'fas fa-money-bill-wave', '_self', 4, 1, NOW(), NOW()),
(76, 'Documentation', NULL, 'Candidate Docs', '/candidate-documents/submission-checklist', 'fas fa-check-square', '_self', 4, 1, NOW(), NOW()),
(77, 'Documentation', NULL, 'Apprentice Docs', '/apprentice-documents/index', 'fas fa-file-invoice', '_self', 5, 1, NOW(), NOW()),
(78, 'Documentation', NULL, 'TMM Documentation', '#', 'fas fa-file-signature', '_self', 6, 1, NOW(), NOW()),
(79, 'Documentation', 78, 'Documentation Dashboard', '/dashboard/documentation', 'fas fa-tachometer-alt', '_self', 1, 1, NOW(), NOW()),
(80, 'Documentation', 78, 'Candidate Documents', '/candidate-documents', 'fas fa-folder-open', '_self', 2, 1, NOW(), NOW()),
(81, 'Documentation', 78, 'Trainee Document Submission', '/trainee-submission-documents/index', 'fas fa-file-alt', '_self', 3, 1, NOW(), NOW()),
(82, 'Documentation', 78, 'Departure Documents', '/trainee-documents/departure', 'fas fa-passport', '_self', 4, 1, NOW(), NOW()),
(83, 'Documentation', 78, 'Passport Records', '/trainee-record-pasports', 'fas fa-id-card-alt', '_self', 5, 1, NOW(), NOW()),
(84, 'Documentation', 78, 'COE & Visa Records', '/trainee-record-coe-visas', 'fas fa-stamp', '_self', 6, 1, NOW(), NOW()),
(85, 'Documentation', 78, 'Tickets & Flights', '/tickets', 'fas fa-plane', '_self', 7, 1, NOW(), NOW()),
-- ------------------------------------------------ Accounting
(86, 'Accounting', NULL, 'Trainee Installments', '/trainee-installments', 'fas fa-money-check-alt', '_self', 1, 1, NOW(), NOW()),
(87, 'Accounting', 86, 'Payment Tracking', '/trainee-installments/tracking', 'fas fa-search-dollar', '_self', 1, 1, NOW(), NOW()),
(88, 'Accounting', 86, 'Receipt Management', '/trainee-installments/receipts', 'fas fa-receipt', '_self', 2, 1, NOW(), NOW()),
(89, 'Accounting', NULL, 'Chart of Accounts', '/chart-of-accounts', 'fas fa-sitemap', '_self', 2, 1, NOW(), NOW()),
(90, 'Accounting', 89, 'Manage COA', '/chart-of-accounts', 'fas fa-list-alt', '_self', 1, 1, NOW(), NOW()),
(91, 'Accounting', 89, 'Account Hierarchy', '/chart-of-accounts/hierarchy', 'fas fa-project-diagram', '_self', 2, 1, NOW(), NOW()),
(92, 'Accounting', NULL, 'Journal Entries', '/journals', 'fas fa-book', '_self', 3, 1, NOW(), NOW()),
(93, 'Accounting', 92, 'Auto-Generated Entries', '/journals/auto-generated', 'fas fa-robot', '_self', 1, 1, NOW(), NOW()),
(94, 'Accounting', 92, 'Manual Adjustments', '/journals/adjustments', 'fas fa-edit', '_self', 2, 1, NOW(), NOW()),
(95, 'Accounting', NULL, 'Financial Reports', '/reports/income-statement', 'fas fa-chart-line', '_self', 4, 1, NOW(), NOW()),
(96, 'Accounting', 95, 'Income Statement', '/reports/income-statement', 'fas fa-file-invoice', '_self', 1, 1, NOW(), NOW()),
(97, 'Accounting', 95, 'Balance Sheet', '/reports/balance-sheet', 'fas fa-balance-scale', '_self', 2, 1, NOW(), NOW()),
(98, 'Accounting', 95, 'Cash Flow', '/reports/cash-flow', 'fas fa-hand-holding-usd', '_self', 3, 1, NOW(), NOW()),
-- ------------------------------------------------ Reports
(99, 'Reports', NULL, 'Reports & Analytics', '/dashboard/executive', 'fas fa-chart-bar', '_self', 1, 1, NOW(), NOW()),
(100, 'Reports', 99, 'Executive Dashboard', '/dashboard/executive', 'fas fa-tachometer-alt', '_self', 1, 1, NOW(), NOW()),
(101, 'Reports', 99, 'Candidate Pipeline', '/reports/candidate-pipeline', 'fas fa-filter', '_self', 2, 1, NOW(), NOW()),
(102, 'Reports', 99, 'Training Progress', '/reports/training-progress', 'fas fa-chart-line', '_self', 3, 1, NOW(), NOW()),
(103, 'Reports', 99, 'Active Apprentices', '/reports/active-apprentices', 'fas fa-user-check', '_self', 4, 1, NOW(), NOW()),
-- ------------------------------------------------ System
(104, 'System', NULL, 'Audit', '/audit', 'fas fa-clipboard-check', '_self', 1, 1, NOW(), NOW()),
(105, 'System', NULL, 'User Management', '#', 'fas fa-users-cog', '_self', 2, 1, NOW(), NOW()),
(106, 'System', 105, 'Register User', '/users/add', 'fas fa-user-plus', '_self', 1, 1, NOW(), NOW()),
(107, 'System', 105, 'Manage Users', '/users', 'fas fa-users', '_self', 2, 1, NOW(), NOW()),
(108, 'System', 105, 'Role Permissions', '/permissions', 'fas fa-user-lock', '_self', 3, 1, NOW(), NOW()),
(109, 'System', NULL, 'Master Data', '#', 'fas fa-database', '_self', 3, 1, NOW(), NOW()),
(110, 'System', 109, 'Job Categories', '/master-job-categories', 'fas fa-briefcase', '_self', 1, 1, NOW(), NOW()),
(111, 'System', 109, 'Gender & Religion', '/master-genders', 'fas fa-venus-mars', '_self', 2, 1, NOW(), NOW()),
(112, 'System', 109, 'Geographic Data', '/master-propinsis', 'fas fa-map-marked-alt', '_self', 3, 1, NOW(), NOW()),
(113, 'System', 109, 'Education Levels', '/master-education-levels', 'fas fa-school', '_self', 4, 1, NOW(), NOW());

ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parent_id` (`parent_id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_sort_order` (`sort_order`),
  ADD KEY `idx_category` (`category`(64));

ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

COMMIT;
