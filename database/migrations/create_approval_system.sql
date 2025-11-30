-- APPROVAL SYSTEM FOR PURCHASE RECEIPTS & STOCK OUTGOINGS
-- Design: Multi-level approval dengan approval_statuses reference table
-- NO FOREIGN KEYS (per design requirement)

USE asahi_inventories;

-- =============================================================================
-- TABLE 1: approval_statuses (Reference/Master Table)
-- Status options untuk approval workflow
-- =============================================================================
CREATE TABLE IF NOT EXISTS approval_statuses (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    status_code VARCHAR(20) NOT NULL UNIQUE COMMENT 'pending, approved, rejected, cancelled, etc',
    status_name VARCHAR(50) NOT NULL COMMENT 'Display name',
    status_color VARCHAR(20) NULL COMMENT 'badge-warning, badge-success, badge-danger',
    description TEXT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (status_code),
    INDEX idx_active (active)
) ENGINE=InnoDB COMMENT='Master table untuk approval status';

-- Insert default statuses
INSERT INTO approval_statuses (status_code, status_name, status_color, description) VALUES
('pending', 'Pending', 'badge-warning', 'Menunggu approval'),
('approved', 'Approved', 'badge-success', 'Sudah di-approve'),
('rejected', 'Rejected', 'badge-danger', 'Di-reject/ditolak'),
('cancelled', 'Cancelled', 'badge-secondary', 'Dibatalkan oleh user'),
('draft', 'Draft', 'badge-info', 'Masih draft, belum submit');

-- =============================================================================
-- TABLE 2: approvals
-- Main approval table untuk semua jenis transaksi
-- =============================================================================
CREATE TABLE IF NOT EXISTS approvals (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    
    -- Reference ke transaksi yang di-approve
    reference_type VARCHAR(50) NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing, dll',
    reference_id INT(11) NOT NULL COMMENT 'ID dari transaksi yang di-approve',
    
    -- Approval level & status
    approval_level TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=First Approval, 2=Second Approval, dst',
    approval_status_id INT(11) NOT NULL DEFAULT 1 COMMENT 'Reference ke approval_statuses (NO FK)',
    
    -- Personnel yang approve/reject
    approver_personnel_id INT(11) NULL COMMENT 'Personnel yang melakukan approval (NO FK)',
    
    -- Timing
    submitted_date DATETIME NOT NULL COMMENT 'Kapan diajukan untuk approval',
    approved_date DATETIME NULL COMMENT 'Kapan di-approve/reject',
    
    -- Notes & reason
    notes TEXT NULL COMMENT 'Catatan dari approver',
    rejection_reason TEXT NULL COMMENT 'Alasan reject (jika status=rejected)',
    
    -- Audit trail
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes (NO FOREIGN KEYS)
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_status (approval_status_id),
    INDEX idx_approver (approver_personnel_id),
    INDEX idx_submitted (submitted_date),
    INDEX idx_approved (approved_date)
) ENGINE=InnoDB COMMENT='Multi-level approval system untuk purchase & usage';

-- =============================================================================
-- TABLE 3: approval_workflows
-- Define workflow rules untuk setiap jenis transaksi
-- =============================================================================
CREATE TABLE IF NOT EXISTS approval_workflows (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    
    -- Workflow definition
    workflow_name VARCHAR(100) NOT NULL COMMENT 'Nama workflow',
    reference_type VARCHAR(50) NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing',
    
    -- Level configuration
    approval_level TINYINT(1) NOT NULL COMMENT 'Level approval (1, 2, 3, dst)',
    approver_role VARCHAR(50) NOT NULL COMMENT 'Role yang bisa approve: Supervisor, Manager, Director',
    
    -- Conditional approval (optional)
    min_amount DECIMAL(15,2) NULL COMMENT 'Minimal amount untuk level ini (NULL = semua)',
    max_amount DECIMAL(15,2) NULL COMMENT 'Maksimal amount untuk level ini (NULL = unlimited)',
    
    -- Required approval
    is_required TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Wajib, 0=Optional',
    
    -- Timing
    active TINYINT(1) NOT NULL DEFAULT 1,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_workflow (reference_type, approval_level),
    INDEX idx_reference (reference_type),
    INDEX idx_amount_range (min_amount, max_amount)
) ENGINE=InnoDB COMMENT='Workflow rules untuk approval system';

-- =============================================================================
-- ALTER EXISTING TABLES
-- Add approval status & approver fields (NO FOREIGN KEYS)
-- =============================================================================

-- Add approval fields to purchase_receipts
ALTER TABLE purchase_receipts 
ADD COLUMN approval_status_id INT(11) NOT NULL DEFAULT 1 COMMENT 'Reference to approval_statuses (NO FK)' AFTER description,
ADD COLUMN approved_by_personnel_id INT(11) NULL COMMENT 'Personnel yang approve pembelian (NO FK)' AFTER approval_status_id,
ADD COLUMN approved_date DATETIME NULL AFTER approved_by_personnel_id,
ADD COLUMN rejection_reason TEXT NULL AFTER approved_date,
ADD INDEX idx_approval_status (approval_status_id);

-- Add approval fields to stock_outgoings
ALTER TABLE stock_outgoings 
ADD COLUMN approval_status_id INT(11) NOT NULL DEFAULT 1 COMMENT 'Reference to approval_statuses (NO FK)' AFTER usage_description,
ADD COLUMN approved_by_personnel_id INT(11) NULL COMMENT 'Personnel yang approve pemakaian (NO FK)' AFTER approval_status_id,
ADD COLUMN approved_date DATETIME NULL AFTER approved_by_personnel_id,
ADD COLUMN rejection_reason TEXT NULL AFTER approved_date,
ADD INDEX idx_approval_status (approval_status_id);

-- =============================================================================
-- SAMPLE WORKFLOW CONFIGURATION
-- =============================================================================

INSERT INTO approval_workflows (workflow_name, reference_type, approval_level, approver_role, min_amount, max_amount, is_required) VALUES
-- Purchase Receipt Workflow
('Purchase Approval Level 1', 'PurchaseReceipt', 1, 'Supervisor', 0, 10000000, 1),
('Purchase Approval Level 2', 'PurchaseReceipt', 2, 'Manager', 10000001, 50000000, 1),
('Purchase Approval Level 3', 'PurchaseReceipt', 3, 'Director', 50000001, NULL, 1),

-- Stock Outgoing Workflow
('Usage Approval Level 1', 'StockOutgoing', 1, 'Supervisor', NULL, NULL, 1),
('Usage Approval Level 2', 'StockOutgoing', 2, 'Manager', NULL, NULL, 0); -- Optional for high-value items

-- =============================================================================
-- VIEWS FOR REPORTING
-- =============================================================================

-- View: Pending Purchase Receipts
CREATE OR REPLACE VIEW v_pending_purchase_approvals AS
SELECT 
    pr.id,
    pr.title,
    pr.purchase_date,
    pr.purchase_amount,
    pr.approval_status_id,
    ast.status_name as approval_status_name,
    ast.status_color,
    pr.personnel_id as requester_personnel_id,
    a.approval_level,
    a.approver_personnel_id,
    a.submitted_date,
    aw.approver_role as required_role
FROM purchase_receipts pr
LEFT JOIN approval_statuses ast ON ast.id = pr.approval_status_id
LEFT JOIN approvals a ON a.reference_type = 'PurchaseReceipt' AND a.reference_id = pr.id
LEFT JOIN approval_workflows aw ON aw.reference_type = 'PurchaseReceipt' 
    AND aw.approval_level = COALESCE(a.approval_level, 1)
    AND (aw.min_amount IS NULL OR pr.purchase_amount >= aw.min_amount)
    AND (aw.max_amount IS NULL OR pr.purchase_amount <= aw.max_amount)
WHERE ast.status_code = 'pending';

-- View: Pending Stock Outgoing Approvals
CREATE OR REPLACE VIEW v_pending_outgoing_approvals AS
SELECT 
    so.id,
    so.outgoing_date,
    so.qty_outgoing,
    so.unit_price,
    (so.qty_outgoing * COALESCE(so.unit_price, 0)) as total_amount,
    so.approval_status_id,
    ast.status_name as approval_status_name,
    ast.status_color,
    so.personnel_id as requester_personnel_id,
    so.vehicle_id,
    a.approval_level,
    a.approver_personnel_id,
    a.submitted_date,
    aw.approver_role as required_role
FROM stock_outgoings so
LEFT JOIN approval_statuses ast ON ast.id = so.approval_status_id
LEFT JOIN approvals a ON a.reference_type = 'StockOutgoing' AND a.reference_id = so.id
LEFT JOIN approval_workflows aw ON aw.reference_type = 'StockOutgoing' 
    AND aw.approval_level = COALESCE(a.approval_level, 1)
WHERE ast.status_code = 'pending';

-- View: Current Inventory Value (Approved purchases not yet used)
CREATE OR REPLACE VIEW v_current_inventory_value AS
SELECT 
    i.id as inventory_id,
    i.name as inventory_name,
    i.qty as current_qty,
    COALESCE(SUM(si.price_po * si.qty_incoming), 0) as total_purchase_value,
    COALESCE(SUM(so.unit_price * so.qty_outgoing), 0) as total_usage_value,
    (COALESCE(SUM(si.price_po * si.qty_incoming), 0) - COALESCE(SUM(so.unit_price * so.qty_outgoing), 0)) as current_asset_value
FROM inventories i
LEFT JOIN stock_incomings si ON si.inventory_id = i.id 
    AND si.purchase_receipt_id IN (
        SELECT pr.id FROM purchase_receipts pr
        JOIN approval_statuses ast ON ast.id = pr.approval_status_id
        WHERE ast.status_code = 'approved'
    )
LEFT JOIN stock_outgoings so ON so.inventory_id = i.id 
    AND so.approval_status_id IN (
        SELECT id FROM approval_statuses WHERE status_code = 'approved'
    )
GROUP BY i.id, i.name, i.qty;

-- =============================================================================
-- SUMMARY
-- =============================================================================
SELECT 'Approval system tables created!' as status;
SELECT 'Tables: approval_statuses, approvals, approval_workflows' as tables;
SELECT 'Altered: purchase_receipts, stock_outgoings (added approval_status_id)' as altered_tables;
SELECT 'Views: v_pending_purchase_approvals, v_pending_outgoing_approvals, v_current_inventory_value' as views;
SELECT 'NO FOREIGN KEYS - All relationships via bindingModel' as design_note;

DESCRIBE approval_statuses;
