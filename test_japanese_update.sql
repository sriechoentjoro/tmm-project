-- Test updating Japanese text with proper charset
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Update a test record with Japanese text
UPDATE cms_lpk_candidates.candidates 
SET name_katakana = 'タナカ タロウ'
WHERE id = 1;

-- Verify
SELECT id, fullname, name_katakana FROM cms_lpk_candidates.candidates WHERE id = 1;
