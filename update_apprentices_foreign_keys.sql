-- Update Foreign Keys untuk Apprentices Table
-- Mengisi candidate_id dan trainee_id berdasarkan ID apprentice

USE cms_tmm_apprentices;

-- Backup dulu (optional, bisa di-comment jika tidak perlu)
-- CREATE TABLE apprentices_backup_20251116 AS SELECT * FROM apprentices;

-- Update candidate_id = apprentice.id
-- Asumsi: setiap apprentice memiliki 1 candidate dengan ID yang sama
UPDATE apprentices a
SET a.candidate_id = a.id
WHERE a.candidate_id IS NULL OR a.candidate_id = 0;

-- Update trainee_id = apprentice.id  
-- Asumsi: setiap apprentice memiliki 1 trainee dengan ID yang sama
UPDATE apprentices a
SET a.trainee_id = a.id
WHERE a.trainee_id IS NULL OR a.trainee_id = 0;

-- Verify results
SELECT 
    COUNT(*) as total_records,
    SUM(CASE WHEN candidate_id IS NOT NULL THEN 1 ELSE 0 END) as has_candidate,
    SUM(CASE WHEN trainee_id IS NOT NULL THEN 1 ELSE 0 END) as has_trainee,
    SUM(CASE WHEN candidate_id IS NULL THEN 1 ELSE 0 END) as null_candidate,
    SUM(CASE WHEN trainee_id IS NULL THEN 1 ELSE 0 END) as null_trainee
FROM apprentices;

-- Show sample records
SELECT id, candidate_id, trainee_id, name, applicant_code 
FROM apprentices 
LIMIT 10;
