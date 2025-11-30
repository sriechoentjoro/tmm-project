-- SQL Script to rename apprenticeship_order_id to apprentice_order_id
-- Date: November 16, 2025
-- Run this in phpMyAdmin for each database

-- =====================================================
-- Database: cms_lpk_candidates
-- Table: candidates
-- =====================================================
USE cms_lpk_candidates;

-- Check if column exists before renaming
ALTER TABLE candidates 
CHANGE COLUMN apprenticeship_order_id apprentice_order_id INT(11) NULL DEFAULT NULL;

-- Update any foreign key constraints if needed
-- (Check if there are any named constraints first)

SELECT 'candidates table updated in cms_lpk_candidates' AS status;


-- =====================================================
-- Database: cms_tmm_trainees
-- Table: trainees
-- =====================================================
USE cms_tmm_trainees;

ALTER TABLE trainees 
CHANGE COLUMN apprenticeship_order_id apprentice_order_id INT(11) NULL DEFAULT NULL;

SELECT 'trainees table updated in cms_tmm_trainees' AS status;


-- =====================================================
-- Database: cms_tmm_apprentices
-- Table: apprentices
-- =====================================================
USE cms_tmm_apprentices;

ALTER TABLE apprentices 
CHANGE COLUMN apprenticeship_order_id apprentice_order_id INT(11) NULL DEFAULT NULL;

SELECT 'apprentices table updated in cms_tmm_apprentices' AS status;


-- =====================================================
-- Verification Queries
-- =====================================================

-- Verify candidates table
USE cms_lpk_candidates;
SHOW COLUMNS FROM candidates LIKE '%apprentice%';

-- Verify trainees table
USE cms_tmm_trainees;
SHOW COLUMNS FROM trainees LIKE '%apprentice%';

-- Verify apprentices table
USE cms_tmm_apprentices;
SHOW COLUMNS FROM apprentices LIKE '%apprentice%';

-- =====================================================
-- Summary
-- =====================================================
SELECT '=== Database Update Complete ===' AS status;
SELECT 'All apprenticeship_order_id columns have been renamed to apprentice_order_id' AS message;
