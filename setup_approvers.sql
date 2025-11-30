-- Setup Approval Workflow Approvers
-- Setelah email test sukses, update personnel_id approver sesuai organisasi Anda

-- =================================================================
-- STEP 1: Update Approver Personnel ID di Workflows
-- =================================================================

-- Purchase Level 1 (≤10 Juta) → Supervisor
-- Ganti personnel_id=1 dengan ID supervisor Anda
UPDATE asahi_online_approvals.approval_workflows 
SET approver_personnel_id = 1  -- HALIM DAMANHURI (ganti sesuai kebutuhan)
WHERE id = 1;

-- Purchase Level 2 (10-50 Juta) → Manager
-- Ganti personnel_id=2 dengan ID manager Anda
UPDATE asahi_online_approvals.approval_workflows 
SET approver_personnel_id = 2  -- DIAN KURNIAWAN (ganti sesuai kebutuhan)
WHERE id = 2;

-- Purchase Level 3 (>50 Juta) → Director
-- Ganti personnel_id=3 dengan ID director Anda
UPDATE asahi_online_approvals.approval_workflows 
SET approver_personnel_id = 3  -- SRI KUNCORO (ganti sesuai kebutuhan)
WHERE id = 3;

-- Stock Usage Level 1 → Supervisor
UPDATE asahi_online_approvals.approval_workflows 
SET approver_personnel_id = 1  -- HALIM DAMANHURI (ganti sesuai kebutuhan)
WHERE id = 4;

-- =================================================================
-- STEP 2: Update Contact Email untuk Approvers
-- =================================================================

-- Update email untuk approver level 1 (Supervisor)
UPDATE asahi_common_personnels.personnels 
SET contact_email = 'supervisor@asahi.com'  -- GANTI dengan email asli
WHERE id = 1;

-- Update email untuk approver level 2 (Manager)
UPDATE asahi_common_personnels.personnels 
SET contact_email = 'manager@asahi.com'  -- GANTI dengan email asli
WHERE id = 2;

-- Update email untuk approver level 3 (Director)
UPDATE asahi_common_personnels.personnels 
SET contact_email = 'director@asahi.com'  -- GANTI dengan email asli
WHERE id = 3;

-- =================================================================
-- VERIFY: Check Setup
-- =================================================================

-- Check workflows
SELECT 
    aw.id,
    aw.workflow_name,
    aw.approver_personnel_id,
    p.name as approver_name,
    p.contact_email,
    aw.min_amount,
    aw.max_amount
FROM asahi_online_approvals.approval_workflows aw
LEFT JOIN asahi_common_personnels.personnels p ON aw.approver_personnel_id = p.id
WHERE aw.active = 1
ORDER BY aw.id;

-- Check approvers yang masih NULL email
SELECT 
    p.id,
    p.name,
    p.contact_email,
    'Used in workflow' as note
FROM asahi_common_personnels.personnels p
INNER JOIN asahi_online_approvals.approval_workflows aw ON p.id = aw.approver_personnel_id
WHERE (p.contact_email IS NULL OR p.contact_email = '')
AND aw.active = 1;
