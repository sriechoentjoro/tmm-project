-- Comprehensive Menu Update for Asahi Inventory System
-- Run this in phpMyAdmin or MySQL client on asahi_commons database

-- First, let's see what parent menus exist
-- SELECT id, title FROM menus WHERE parent_id IS NULL ORDER BY position;

-- ============================================================================
-- INVENTORY MANAGEMENT (Assuming parent_id = 1)
-- ============================================================================
INSERT INTO menus (parent_id, title, url, controller, action, icon, position, is_active, created, modified) VALUES
(1, 'Inventories', '/inventories', 'Inventories', 'index', 'fas fa-boxes', 1, 1, NOW(), NOW()),
(1, 'Storages', '/storages', 'Storages', 'index', 'fas fa-warehouse', 2, 1, NOW(), NOW()),
(1, 'Stock Incomings', '/stock-incomings', 'StockIncomings', 'index', 'fas fa-arrow-down', 3, 1, NOW(), NOW()),
(1, 'Stock Outgoings', '/stock-outgoings', 'StockOutgoings', 'index', 'fas fa-arrow-up', 4, 1, NOW(), NOW()),
(1, 'Stock Takes', '/stock-takes', 'StockTakes', 'index', 'fas fa-clipboard-check', 5, 1, NOW(), NOW()),
(1, 'Adjust Stocks', '/adjust-stocks', 'AdjustStocks', 'index', 'fas fa-balance-scale', 6, 1, NOW(), NOW()),
(1, 'Purchase Receipts', '/purchase-receipts', 'PurchaseReceipts', 'index', 'fas fa-receipt', 7, 1, NOW(), NOW()),
(1, 'Suppliers', '/suppliers', 'Suppliers', 'index', 'fas fa-truck', 8, 1, NOW(), NOW()),
(1, 'UOMs', '/uoms', 'Uoms', 'index', 'fas fa-ruler', 9, 1, NOW(), NOW());

-- ============================================================================
-- PERSONNEL MANAGEMENT (Assuming parent_id = 2)
-- ============================================================================
INSERT INTO menus (parent_id, title, url, controller, action, icon, position, is_active, created, modified) VALUES
(2, 'Personnels', '/personnels', 'Personnels', 'index', 'fas fa-users', 1, 1, NOW(), NOW()),
(2, 'Companies', '/companies', 'Companies', 'index', 'fas fa-building', 2, 1, NOW(), NOW()),
(2, 'Departments', '/departments', 'Departments', 'index', 'fas fa-sitemap', 3, 1, NOW(), NOW()),
(2, 'Sections', '/sections', 'Sections', 'index', 'fas fa-layer-group', 4, 1, NOW(), NOW()),
(2, 'Positions', '/positions', 'Positions', 'index', 'fas fa-user-tag', 5, 1, NOW(), NOW()),
(2, 'Employee Statuses', '/employee-statuses', 'EmployeeStatuses', 'index', 'fas fa-user-check', 6, 1, NOW(), NOW()),
(2, 'Shift Groups', '/shift-groups', 'ShiftGroups', 'index', 'fas fa-calendar-alt', 7, 1, NOW(), NOW());

-- ============================================================================
-- MAINTENANCE MANAGEMENT (Assuming parent_id = 3)
-- ============================================================================
INSERT INTO menus (parent_id, title, url, controller, action, icon, position, is_active, created, modified) VALUES
(3, 'Planned Jobs', '/planned-jobs', 'PlannedJobs', 'index', 'fas fa-tasks', 1, 1, NOW(), NOW()),
(3, 'Planned Job Tasks', '/planned-job-tasks', 'PlannedJobTasks', 'index', 'fas fa-list-check', 2, 1, NOW(), NOW()),
(3, 'Maintenance Cards', '/maintenance-cards', 'MaintenanceCards', 'index', 'fas fa-id-card', 3, 1, NOW(), NOW()),
(3, 'Safety Cards', '/safety-cards', 'SafetyCards', 'index', 'fas fa-shield-alt', 4, 1, NOW(), NOW()),
(3, 'Insurance Claims', '/insurance-claims', 'InsuranceClaims', 'index', 'fas fa-file-invoice', 5, 1, NOW(), NOW()),
(3, 'Break Down Causes', '/break-down-causes', 'BreakDownCauses', 'index', 'fas fa-wrench', 6, 1, NOW(), NOW());

-- ============================================================================
-- VEHICLE MANAGEMENT (Assuming parent_id = 4)
-- ============================================================================
INSERT INTO menus (parent_id, title, url, controller, action, icon, position, is_active, created, modified) VALUES
(4, 'Vehicles', '/vehicles', 'Vehicles', 'index', 'fas fa-car', 1, 1, NOW(), NOW()),
(4, 'Vehicle Types', '/vehicle-types', 'VehicleTypes', 'index', 'fas fa-list', 2, 1, NOW(), NOW()),
(4, 'Drivers', '/drivers', 'Drivers', 'index', 'fas fa-id-badge', 3, 1, NOW(), NOW()),
(4, 'Lesor Companies', '/lesor-companies', 'LesorCompanies', 'index', 'fas fa-handshake', 4, 1, NOW(), NOW()),
(4, 'Customers', '/customers', 'Customers', 'index', 'fas fa-user-tie', 5, 1, NOW(), NOW());

-- ============================================================================
-- ACCOUNTING (Assuming parent_id = 5)
-- ============================================================================
INSERT INTO menus (parent_id, title, url, controller, action, icon, position, is_active, created, modified) VALUES
(5, 'Accounting Transactions', '/accounting-transactions', 'AccountingTransactions', 'index', 'fas fa-exchange-alt', 1, 1, NOW(), NOW()),
(5, 'Chart of Accounts', '/chart-of-accounts', 'ChartOfAccounts', 'index', 'fas fa-chart-pie', 2, 1, NOW(), NOW()),
(5, 'Banks', '/banks', 'Banks', 'index', 'fas fa-university', 3, 1, NOW(), NOW()),
(5, 'Payment Methods', '/payment-methods', 'PaymentMethods', 'index', 'fas fa-credit-card', 4, 1, NOW(), NOW());

-- ============================================================================
-- SYSTEM SETTINGS (Assuming parent_id = 6)
-- ============================================================================
INSERT INTO menus (parent_id, title, url, controller, action, icon, position, is_active, created, modified) VALUES
(6, 'Users', '/users', 'Users', 'index', 'fas fa-user', 1, 1, NOW(), NOW()),
(6, 'Roles', '/roles', 'Roles', 'index', 'fas fa-user-shield', 2, 1, NOW(), NOW()),
(6, 'Groups', '/groups', 'Groups', 'index', 'fas fa-users-cog', 3, 1, NOW(), NOW()),
(6, 'Menus', '/menus', 'Menus', 'index', 'fas fa-bars', 4, 1, NOW(), NOW()),
(6, 'Authorizations', '/authorizations', 'Authorizations', 'index', 'fas fa-key', 5, 1, NOW(), NOW());

-- ============================================================================
-- DAILY ACTIVITIES (Assuming parent_id = 7)
-- ============================================================================
INSERT INTO menus (parent_id, title, url, controller, action, icon, position, is_active, created, modified) VALUES
(7, 'Daily Activities', '/daily-activities', 'DailyActivities', 'index', 'fas fa-clipboard-list', 1, 1, NOW(), NOW()),
(7, 'Actions', '/actions', 'Actions', 'index', 'fas fa-bolt', 2, 1, NOW(), NOW()),
(7, 'Action Plans', '/action-plans', 'ActionPlans', 'index', 'fas fa-project-diagram', 3, 1, NOW(), NOW()),
(7, 'Action Statuses', '/action-statuses', 'ActionStatuses', 'index', 'fas fa-flag', 4, 1, NOW(), NOW()),
(7, 'Job Categories', '/job-categories', 'JobCategories', 'index', 'fas fa-briefcase', 5, 1, NOW(), NOW()),
(7, 'Job Statuses', '/job-statuses', 'JobStatuses', 'index', 'fas fa-tasks', 6, 1, NOW(), NOW());

-- ============================================================================
-- NOTES:
-- 1. Adjust parent_id values based on your actual parent menu IDs
-- 2. Run: SELECT id, title FROM menus WHERE parent_id IS NULL; to see parent IDs
-- 3. You may need to adjust position numbers to fit your existing menu structure
-- 4. Set is_active = 0 for menus you want to hide temporarily
-- ============================================================================
