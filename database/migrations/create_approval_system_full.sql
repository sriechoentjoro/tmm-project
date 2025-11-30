-- ============================================================================
-- STEP 1: CREATE NEW DATABASES
-- ============================================================================

CREATE DATABASE IF NOT EXISTS asahi_accountings 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS asahi_online_approvals 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SELECT 'New databases created: asahi_accountings, asahi_online_approvals' as status;

-- ============================================================================
-- STEP 2: MOVE accounting_transactions to asahi_accountings
-- ============================================================================

USE asahi_accountings;

-- Create accounting_transactions in new database
CREATE TABLE IF NOT EXISTS accounting_transactions (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    transaction_date DATE NOT NULL,
    description TEXT,
    debit_account VARCHAR(100),
    credit_account VARCHAR(100),
    amount DECIMAL(15,2) NOT NULL,
    reference_type VARCHAR(50),
    reference_id INT(11),
    personnel_id INT(11) NULL COMMENT 'Pelaksana (NO FK)',
    vehicle_id INT(11) NULL COMMENT 'Alokasi kendaraan (NO FK)',
    receipt_file_path VARCHAR(500),
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_personnel (personnel_id),
    INDEX idx_vehicle (vehicle_id),
    INDEX idx_receipt (receipt_file_path)
) ENGINE=InnoDB COMMENT='Journal entries - moved from asahi_inventories';

-- Copy existing data if table exists in old location
INSERT IGNORE INTO asahi_accountings.accounting_transactions 
SELECT * FROM asahi_inventories.accounting_transactions;

SELECT CONCAT('Copied ', COUNT(*), ' records to asahi_accountings.accounting_transactions') as status
FROM asahi_accountings.accounting_transactions;

-- ============================================================================
-- STEP 3: CREATE APPROVAL SYSTEM in asahi_online_approvals
-- ============================================================================

USE asahi_online_approvals;

-- Table 1: approval_statuses (Master/Reference Table)
CREATE TABLE IF NOT EXISTS approval_statuses (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    status_code VARCHAR(20) NOT NULL UNIQUE COMMENT 'draft, pending, approved, rejected, cancelled',
    status_name VARCHAR(50) NOT NULL COMMENT 'Display name',
    status_color VARCHAR(20) NULL COMMENT 'badge-info, badge-warning, badge-success, badge-danger',
    description TEXT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (status_code),
    INDEX idx_active (active)
) ENGINE=InnoDB COMMENT='Master table untuk approval status';

-- Insert default statuses
INSERT INTO approval_statuses (id, status_code, status_name, status_color, description) VALUES
(1, 'draft', 'Draft', 'badge-info', 'Masih draft, belum submit'),
(2, 'pending', 'Pending', 'badge-warning', 'Menunggu approval'),
(3, 'approved', 'Approved', 'badge-success', 'Sudah di-approve'),
(4, 'rejected', 'Rejected', 'badge-danger', 'Di-reject/ditolak'),
(5, 'cancelled', 'Cancelled', 'badge-secondary', 'Dibatalkan oleh user');

-- Table 2: approvals (Main approval records)
CREATE TABLE IF NOT EXISTS approvals (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    
    -- Reference ke transaksi yang di-approve
    reference_type VARCHAR(50) NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing, dll',
    reference_id INT(11) NOT NULL COMMENT 'ID dari transaksi (NO FK)',
    
    -- Approval level & status
    approval_level TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=First, 2=Second, dst',
    approval_status_id INT(11) NOT NULL DEFAULT 2 COMMENT 'Reference to approval_statuses (NO FK)',
    
    -- Approver personnel
    approver_personnel_id INT(11) NULL COMMENT 'Personnel yang approve/reject (NO FK)',
    
    -- Timing
    submitted_date DATETIME NOT NULL COMMENT 'Kapan diajukan',
    approved_date DATETIME NULL COMMENT 'Kapan di-approve/reject',
    
    -- Notes
    notes TEXT NULL COMMENT 'Catatan approver',
    rejection_reason TEXT NULL COMMENT 'Alasan reject',
    
    -- Security token untuk approval via link
    approval_token VARCHAR(64) NULL COMMENT 'Unique token untuk approval link',
    token_expires DATETIME NULL COMMENT 'Token expiry datetime',
    
    -- Notification tracking
    email_sent TINYINT(1) DEFAULT 0 COMMENT 'Email notification sent',
    whatsapp_sent TINYINT(1) DEFAULT 0 COMMENT 'WhatsApp notification sent',
    email_sent_date DATETIME NULL,
    whatsapp_sent_date DATETIME NULL,
    
    -- Audit trail
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_status (approval_status_id),
    INDEX idx_approver (approver_personnel_id),
    INDEX idx_submitted (submitted_date),
    INDEX idx_approved (approved_date),
    INDEX idx_token (approval_token)
) ENGINE=InnoDB COMMENT='Multi-level approval records';

-- Table 3: approval_workflows (Workflow configuration)
CREATE TABLE IF NOT EXISTS approval_workflows (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    
    -- Workflow definition
    workflow_name VARCHAR(100) NOT NULL,
    reference_type VARCHAR(50) NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing',
    
    -- Level configuration
    approval_level TINYINT(1) NOT NULL COMMENT 'Level approval (1, 2, 3)',
    approver_role VARCHAR(50) NOT NULL COMMENT 'Supervisor, Manager, Director',
    
    -- Approver personnel (specific person or null for role-based)
    approver_personnel_id INT(11) NULL COMMENT 'Specific approver (NO FK), NULL = any with role',
    
    -- Conditional approval
    min_amount DECIMAL(15,2) NULL COMMENT 'Min amount untuk level ini',
    max_amount DECIMAL(15,2) NULL COMMENT 'Max amount (NULL=unlimited)',
    
    -- Notification settings
    send_email TINYINT(1) DEFAULT 1 COMMENT 'Send email notification',
    send_whatsapp TINYINT(1) DEFAULT 1 COMMENT 'Send WhatsApp notification',
    
    -- Settings
    is_required TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Wajib, 0=Optional',
    auto_approve TINYINT(1) DEFAULT 0 COMMENT 'Auto approve if conditions met',
    active TINYINT(1) NOT NULL DEFAULT 1,
    
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_workflow (reference_type, approval_level),
    INDEX idx_reference (reference_type),
    INDEX idx_amount_range (min_amount, max_amount),
    INDEX idx_approver (approver_personnel_id)
) ENGINE=InnoDB COMMENT='Workflow rules untuk approval';

-- Table 4: notification_templates
CREATE TABLE IF NOT EXISTS notification_templates (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    
    template_name VARCHAR(100) NOT NULL UNIQUE,
    notification_type ENUM('email', 'whatsapp') NOT NULL,
    reference_type VARCHAR(50) NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing',
    
    -- Template content
    subject VARCHAR(255) NULL COMMENT 'Email subject (NULL for WhatsApp)',
    body_template TEXT NOT NULL COMMENT 'Template body with placeholders',
    
    -- Variables available: {title}, {amount}, {date}, {requester}, {approve_link}, {reject_link}
    
    active TINYINT(1) DEFAULT 1,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_type (notification_type, reference_type)
) ENGINE=InnoDB COMMENT='Email & WhatsApp templates';

-- Insert default templates
INSERT INTO notification_templates (template_name, notification_type, reference_type, subject, body_template) VALUES
('purchase_receipt_email', 'email', 'PurchaseReceipt', 
 'Purchase Approval Required: {title}', 
 '<h2>Purchase Approval Request</h2>
  <p><strong>Title:</strong> {title}</p>
  <p><strong>Amount:</strong> Rp {amount}</p>
  <p><strong>Date:</strong> {date}</p>
  <p><strong>Requested by:</strong> {requester}</p>
  <p><strong>Description:</strong> {description}</p>
  <hr>
  <p>
    <a href="{approve_link}" style="background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;">APPROVE</a>
    <a href="{reject_link}" style="background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-left:10px;">REJECT</a>
  </p>
  <p><small>Token expires in 7 days</small></p>'),

('purchase_receipt_whatsapp', 'whatsapp', 'PurchaseReceipt', 
 NULL,
 '🔔 *PURCHASE APPROVAL REQUIRED*\n\n📋 *{title}*\n💰 Amount: *Rp {amount}*\n📅 Date: {date}\n👤 Requester: {requester}\n\n{description}\n\n✅ Approve: {approve_link}\n❌ Reject: {reject_link}\n\n_Token expires in 7 days_'),

('stock_outgoing_email', 'email', 'StockOutgoing',
 'Stock Usage Approval Required',
 '<h2>Stock Usage Approval Request</h2>
  <p><strong>Item:</strong> {item_name}</p>
  <p><strong>Quantity:</strong> {quantity} units</p>
  <p><strong>Amount:</strong> Rp {amount}</p>
  <p><strong>Vehicle:</strong> {vehicle}</p>
  <p><strong>Usage:</strong> {usage_description}</p>
  <p><strong>Requested by:</strong> {requester}</p>
  <hr>
  <p>
    <a href="{approve_link}" style="background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;">APPROVE</a>
    <a href="{reject_link}" style="background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-left:10px;">REJECT</a>
  </p>'),

('stock_outgoing_whatsapp', 'whatsapp', 'StockOutgoing',
 NULL,
 '🔔 *STOCK USAGE APPROVAL*\n\n📦 Item: *{item_name}*\n🔢 Qty: {quantity} units\n💰 Amount: *Rp {amount}*\n🚗 Vehicle: {vehicle}\n📝 Usage: {usage_description}\n👤 By: {requester}\n\n✅ Approve: {approve_link}\n❌ Reject: {reject_link}');

-- Sample workflows
INSERT INTO approval_workflows (workflow_name, reference_type, approval_level, approver_role, min_amount, max_amount, is_required, send_email, send_whatsapp) VALUES
('Purchase Level 1', 'PurchaseReceipt', 1, 'Supervisor', 0, 10000000, 1, 1, 1),
('Purchase Level 2', 'PurchaseReceipt', 2, 'Manager', 10000001, 50000000, 1, 1, 1),
('Purchase Level 3', 'PurchaseReceipt', 3, 'Director', 50000001, NULL, 1, 1, 0),
('Stock Usage Level 1', 'StockOutgoing', 1, 'Supervisor', NULL, NULL, 1, 1, 1);

SELECT 'Approval system created in asahi_online_approvals' as status;

-- ============================================================================
-- STEP 4: ADD approval_status_id to asahi_inventories tables
-- ============================================================================

USE asahi_inventories;

-- Add to purchase_receipts
ALTER TABLE purchase_receipts 
ADD COLUMN approval_status_id INT(11) NOT NULL DEFAULT 2 COMMENT 'Ref: asahi_online_approvals.approval_statuses (NO FK)' AFTER description,
ADD COLUMN approved_by_personnel_id INT(11) NULL COMMENT 'Personnel approver (NO FK)' AFTER approval_status_id,
ADD COLUMN approved_date DATETIME NULL AFTER approved_by_personnel_id,
ADD COLUMN rejection_reason TEXT NULL AFTER approved_date,
ADD INDEX idx_approval_status (approval_status_id);

-- Add to stock_outgoings
ALTER TABLE stock_outgoings 
ADD COLUMN approval_status_id INT(11) NOT NULL DEFAULT 2 COMMENT 'Ref: asahi_online_approvals.approval_statuses (NO FK)' AFTER usage_description,
ADD COLUMN approved_by_personnel_id INT(11) NULL COMMENT 'Personnel approver (NO FK)' AFTER approval_status_id,
ADD COLUMN approved_date DATETIME NULL AFTER approved_by_personnel_id,
ADD COLUMN rejection_reason TEXT NULL AFTER approved_date,
ADD INDEX idx_approval_status (approval_status_id);

SELECT 'approval_status_id added to purchase_receipts and stock_outgoings' as status;

-- ============================================================================
-- STEP 5: DROP old accounting_transactions from asahi_inventories (OPTIONAL)
-- ============================================================================

-- Uncomment to drop after verifying data copied successfully
-- USE asahi_inventories;
-- DROP TABLE IF EXISTS accounting_transactions;

-- ============================================================================
-- SUMMARY
-- ============================================================================

SELECT '
╔════════════════════════════════════════════════════════════════════════╗
║              DATABASE MIGRATION & APPROVAL SYSTEM CREATED              ║
╚════════════════════════════════════════════════════════════════════════╝

✅ NEW DATABASES:
   1. asahi_accountings
      - accounting_transactions
   
   2. asahi_online_approvals
      - approval_statuses (5 default statuses)
      - approvals (with email/whatsapp tracking)
      - approval_workflows (4 default workflows)
      - notification_templates (4 templates: email & whatsapp)

✅ UPDATED: asahi_inventories
   - purchase_receipts: +approval_status_id
   - stock_outgoings: +approval_status_id

✅ FEATURES:
   - Multi-level approval workflow
   - Email & WhatsApp notifications
   - Security token-based approval links
   - Template system for notifications
   - Approval tracking & audit trail

🔧 NEXT: Update config/app.php with new database connections

' as summary;
