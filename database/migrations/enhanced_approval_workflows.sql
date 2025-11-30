-- =====================================================================
-- ENHANCED MULTILEVEL APPROVAL FOR ASAHI INVENTORY SYSTEM
-- =====================================================================
-- Database: asahi_online_approvals
-- Date: November 7, 2025
-- 
-- Menggabungkan:
-- 1. Amount-based approval
-- 2. Department hierarchy
-- 3. Position level
-- 4. Category-based (inventory type)
-- =====================================================================

USE asahi_online_approvals;

-- =====================================================================
-- STEP 1: Enhance approval_workflows table
-- =====================================================================

ALTER TABLE approval_workflows
ADD COLUMN approver_personnel_id INT(11) NULL COMMENT 'Specific personnel (FK: personnels.id)' AFTER approver_role,
ADD COLUMN approver_position_id INT(11) NULL COMMENT 'Position required (FK: positions.id)' AFTER approver_personnel_id,
ADD COLUMN approver_department_id INT(11) NULL COMMENT 'Department scope (FK: departments.id)' AFTER approver_position_id,
ADD COLUMN item_category VARCHAR(50) NULL COMMENT 'Inventory category filter' AFTER max_amount,
ADD COLUMN auto_approve_threshold DECIMAL(15,2) NULL COMMENT 'Auto-approve if <= amount' AFTER item_category,
ADD COLUMN parallel_approval TINYINT(1) DEFAULT 0 COMMENT '1=Bisa approve bersamaan, 0=Sequential' AFTER is_required,
ADD COLUMN skip_if_requester_is VARCHAR(100) NULL COMMENT 'Skip level jika requester punya role ini' AFTER parallel_approval,
ADD COLUMN notification_cc VARCHAR(500) NULL COMMENT 'Email CC (comma-separated)' AFTER send_whatsapp,
ADD COLUMN escalation_days INT DEFAULT 3 COMMENT 'Eskalasi ke level atas jika tidak direspon' AFTER notification_cc;

-- =====================================================================
-- STEP 2: Create enhanced approval matrix table
-- =====================================================================

CREATE TABLE IF NOT EXISTS approval_matrix (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    
    -- Matrix definition
    matrix_name VARCHAR(100) NOT NULL COMMENT 'e.g. Standard Purchase, Emergency Purchase',
    reference_type VARCHAR(50) NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing',
    is_default TINYINT(1) DEFAULT 0 COMMENT 'Default matrix untuk reference_type ini',
    
    -- Priority (jika ada multiple matrix yang match)
    priority INT DEFAULT 100 COMMENT 'Lower = higher priority',
    
    -- Active status
    active TINYINT(1) DEFAULT 1,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_reference (reference_type),
    INDEX idx_active (active)
) ENGINE=InnoDB COMMENT='Approval matrix untuk different scenarios';

-- Link workflows to matrix
ALTER TABLE approval_workflows
ADD COLUMN matrix_id INT(11) NULL COMMENT 'FK: approval_matrix.id' AFTER id,
ADD INDEX idx_matrix (matrix_id);

-- =====================================================================
-- STEP 3: Sample Implementation - Standard Purchase Approval
-- =====================================================================

-- Matrix 1: Standard Purchase (Normal procurement)
INSERT INTO approval_matrix (matrix_name, reference_type, is_default, priority, active)
VALUES ('Standard Purchase Approval', 'PurchaseReceipt', 1, 100, 1);

SET @standard_matrix_id = LAST_INSERT_ID();

-- Clear existing workflows
DELETE FROM approval_workflows WHERE reference_type = 'PurchaseReceipt';

-- LEVEL 1: Section Head / Supervisor (0 - 10 Juta)
-- Approve di level section/group
INSERT INTO approval_workflows 
(matrix_id, workflow_name, reference_type, approval_level, approver_role, 
 min_amount, max_amount, auto_approve_threshold, is_required, parallel_approval, 
 escalation_days, active) 
VALUES
(@standard_matrix_id, 'Purchase L1 - Section Head', 'PurchaseReceipt', 1, 'Section Head', 
 0, 10000000, 1000000, 1, 0, 2, 1);

-- LEVEL 2: Department Manager (10 - 50 Juta)
-- Manager departemen yang bersangkutan
INSERT INTO approval_workflows 
(matrix_id, workflow_name, reference_type, approval_level, approver_role, 
 min_amount, max_amount, is_required, parallel_approval, escalation_days, active) 
VALUES
(@standard_matrix_id, 'Purchase L2 - Dept Manager', 'PurchaseReceipt', 2, 'Manager', 
 10000001, 50000000, 1, 0, 3, 1);

-- LEVEL 3: General Manager (50 - 150 Juta)
-- GM untuk approval cross-department
INSERT INTO approval_workflows 
(matrix_id, workflow_name, reference_type, approval_level, approver_role, 
 min_amount, max_amount, is_required, parallel_approval, escalation_days, active) 
VALUES
(@standard_matrix_id, 'Purchase L3 - General Manager', 'PurchaseReceipt', 3, 'General Manager', 
 50000001, 150000000, 1, 0, 5, 1);

-- LEVEL 4: Finance Director + Procurement Director (150 - 500 Juta)
-- Parallel approval: Finance DAN Procurement harus approve
INSERT INTO approval_workflows 
(matrix_id, workflow_name, reference_type, approval_level, approver_role, 
 min_amount, max_amount, is_required, parallel_approval, escalation_days, active) 
VALUES
(@standard_matrix_id, 'Purchase L4A - Finance Director', 'PurchaseReceipt', 4, 'Finance Director', 
 150000001, 500000000, 1, 1, 7, 1),
(@standard_matrix_id, 'Purchase L4B - Procurement Director', 'PurchaseReceipt', 4, 'Procurement Director', 
 150000001, 500000000, 1, 1, 7, 1);

-- LEVEL 5: CEO (> 500 Juta)
-- Final approval untuk high-value procurement
INSERT INTO approval_workflows 
(matrix_id, workflow_name, reference_type, approval_level, approver_role, 
 min_amount, max_amount, is_required, parallel_approval, escalation_days, active) 
VALUES
(@standard_matrix_id, 'Purchase L5 - CEO', 'PurchaseReceipt', 5, 'CEO', 
 500000001, NULL, 1, 0, 10, 1);

-- =====================================================================
-- STEP 4: Sample Implementation - Emergency Purchase
-- =====================================================================

-- Matrix 2: Emergency Purchase (Fast-track untuk urgent needs)
INSERT INTO approval_matrix (matrix_name, reference_type, is_default, priority, active)
VALUES ('Emergency Purchase Approval', 'PurchaseReceipt', 0, 90, 1);

SET @emergency_matrix_id = LAST_INSERT_ID();

-- Emergency: Langsung ke GM (skip Supervisor & Manager)
INSERT INTO approval_workflows 
(matrix_id, workflow_name, reference_type, approval_level, approver_role, 
 min_amount, max_amount, is_required, escalation_days, active) 
VALUES
(@emergency_matrix_id, 'Emergency L1 - GM', 'PurchaseReceipt', 1, 'General Manager', 
 0, 150000000, 1, 1, 1),
(@emergency_matrix_id, 'Emergency L2 - Director', 'PurchaseReceipt', 2, 'Director', 
 150000001, NULL, 1, 2, 1);

-- =====================================================================
-- STEP 5: Stock Outgoing Approval Matrix
-- =====================================================================

-- Matrix 3: Standard Stock Usage
INSERT INTO approval_matrix (matrix_name, reference_type, is_default, priority, active)
VALUES ('Standard Stock Usage Approval', 'StockOutgoing', 1, 100, 1);

SET @stock_matrix_id = LAST_INSERT_ID();

DELETE FROM approval_workflows WHERE reference_type = 'StockOutgoing';

-- LEVEL 1: Warehouse Supervisor
-- Untuk pemakaian normal (< 5 unit atau < 5 juta)
INSERT INTO approval_workflows 
(matrix_id, workflow_name, reference_type, approval_level, approver_role, 
 min_amount, max_amount, auto_approve_threshold, is_required, escalation_days, active) 
VALUES
(@stock_matrix_id, 'Usage L1 - Warehouse Supervisor', 'StockOutgoing', 1, 'Warehouse Supervisor', 
 0, 5000000, 500000, 1, 2, 1);

-- LEVEL 2: Department Manager
-- Untuk pemakaian besar atau barang critical
INSERT INTO approval_workflows 
(matrix_id, workflow_name, reference_type, approval_level, approver_role, 
 min_amount, max_amount, is_required, escalation_days, active) 
VALUES
(@stock_matrix_id, 'Usage L2 - Dept Manager', 'StockOutgoing', 2, 'Manager', 
 5000001, NULL, 1, 3, 1);

-- =====================================================================
-- STEP 6: Update existing approval_workflows with personnel mapping
-- =====================================================================

-- Mapping approver_role ke actual personnel_id
-- Anda harus UPDATE ini sesuai organisasi Asahi

-- Contoh: Set specific approvers untuk Standard Purchase
UPDATE approval_workflows 
SET approver_personnel_id = 1  -- ID Supervisor (ganti sesuai data)
WHERE matrix_id = @standard_matrix_id AND approval_level = 1;

UPDATE approval_workflows 
SET approver_personnel_id = 2  -- ID Manager (ganti sesuai data)
WHERE matrix_id = @standard_matrix_id AND approval_level = 2;

UPDATE approval_workflows 
SET approver_personnel_id = 3  -- ID GM (ganti sesuai data)
WHERE matrix_id = @standard_matrix_id AND approval_level = 3;

-- Finance Director
UPDATE approval_workflows 
SET approver_personnel_id = 4  -- ID Finance Director
WHERE matrix_id = @standard_matrix_id AND approver_role = 'Finance Director';

-- Procurement Director
UPDATE approval_workflows 
SET approver_personnel_id = 5  -- ID Procurement Director
WHERE matrix_id = @standard_matrix_id AND approver_role = 'Procurement Director';

-- CEO
UPDATE approval_workflows 
SET approver_personnel_id = 6  -- ID CEO
WHERE matrix_id = @standard_matrix_id AND approval_level = 5;

-- =====================================================================
-- STEP 7: Create helper views
-- =====================================================================

-- View: Show complete approval chain untuk setiap amount
CREATE OR REPLACE VIEW v_approval_chain AS
SELECT 
    am.matrix_name,
    am.reference_type,
    aw.approval_level,
    aw.approver_role,
    p.name as approver_name,
    p.contact_email as approver_email,
    pos.title as position_title,
    dept.department_name,
    aw.min_amount,
    aw.max_amount,
    aw.parallel_approval,
    aw.escalation_days,
    aw.active
FROM approval_workflows aw
LEFT JOIN approval_matrix am ON am.id = aw.matrix_id
LEFT JOIN asahi_common_personnels.personnels p ON p.id = aw.approver_personnel_id
LEFT JOIN asahi_common_personnels.positions pos ON pos.id = aw.approver_position_id
LEFT JOIN asahi_commons.departments dept ON dept.id = aw.approver_department_id
WHERE aw.active = 1
ORDER BY am.reference_type, am.priority, aw.approval_level;

-- View: Get approval requirements untuk specific purchase amount
DROP PROCEDURE IF EXISTS sp_get_required_approvers;

DELIMITER $$
CREATE PROCEDURE sp_get_required_approvers(
    IN p_reference_type VARCHAR(50),
    IN p_amount DECIMAL(15,2),
    IN p_matrix_id INT
)
BEGIN
    SELECT 
        aw.id as workflow_id,
        aw.approval_level,
        aw.approver_role,
        aw.approver_personnel_id,
        p.name as approver_name,
        p.contact_email as approver_email,
        aw.parallel_approval,
        aw.escalation_days,
        CASE 
            WHEN aw.auto_approve_threshold IS NOT NULL 
                 AND p_amount <= aw.auto_approve_threshold 
            THEN 1 
            ELSE 0 
        END as can_auto_approve
    FROM approval_workflows aw
    LEFT JOIN asahi_common_personnels.personnels p ON p.id = aw.approver_personnel_id
    WHERE aw.active = 1
        AND aw.reference_type = p_reference_type
        AND (aw.matrix_id = p_matrix_id OR p_matrix_id IS NULL)
        AND (aw.min_amount IS NULL OR p_amount >= aw.min_amount)
        AND (aw.max_amount IS NULL OR p_amount <= aw.max_amount)
        AND aw.is_required = 1
    ORDER BY aw.approval_level, aw.parallel_approval DESC;
END$$
DELIMITER ;

-- =====================================================================
-- STEP 8: Verify Setup
-- =====================================================================

SELECT '=== APPROVAL MATRICES ===' as info;
SELECT * FROM approval_matrix WHERE active = 1;

SELECT '=== STANDARD PURCHASE WORKFLOW ===' as info;
SELECT * FROM v_approval_chain 
WHERE reference_type = 'PurchaseReceipt' 
  AND matrix_name LIKE '%Standard%'
ORDER BY approval_level;

SELECT '=== EMERGENCY PURCHASE WORKFLOW ===' as info;
SELECT * FROM v_approval_chain 
WHERE reference_type = 'PurchaseReceipt' 
  AND matrix_name LIKE '%Emergency%'
ORDER BY approval_level;

SELECT '=== STOCK OUTGOING WORKFLOW ===' as info;
SELECT * FROM v_approval_chain 
WHERE reference_type = 'StockOutgoing'
ORDER BY approval_level;

SELECT '=== APPROVERS WITHOUT EMAIL ===' as info;
SELECT DISTINCT
    aw.approver_personnel_id,
    p.name,
    p.contact_email,
    aw.approver_role
FROM approval_workflows aw
LEFT JOIN asahi_common_personnels.personnels p ON p.id = aw.approver_personnel_id
WHERE aw.active = 1
  AND aw.approver_personnel_id IS NOT NULL
  AND (p.contact_email IS NULL OR p.contact_email = '');

-- =====================================================================
-- USAGE EXAMPLE
-- =====================================================================

-- Get required approvers untuk purchase 75 juta
SELECT '=== REQUIRED APPROVERS FOR 75 MILLION IDR ===' as info;
CALL sp_get_required_approvers('PurchaseReceipt', 75000000, NULL);

-- Get required approvers untuk purchase 250 juta
SELECT '=== REQUIRED APPROVERS FOR 250 MILLION IDR ===' as info;
CALL sp_get_required_approvers('PurchaseReceipt', 250000000, NULL);

-- Get required approvers untuk stock outgoing 2 juta
SELECT '=== REQUIRED APPROVERS FOR STOCK USAGE 2 MILLION IDR ===' as info;
CALL sp_get_required_approvers('StockOutgoing', 2000000, NULL);

