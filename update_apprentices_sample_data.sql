-- Update sample apprentices data with valid apprentice_order_id for testing
-- This links some apprentices to apprentice_orders for AJAX tab testing

USE cms_tmm_apprentices;

-- First, check which apprentice_order IDs exist in cms_tmm_trainees.apprentice_orders
-- Assuming IDs 1-10 exist based on typical setup

-- Update first 10 apprentices to link to apprentice_order_id 1
UPDATE apprentices 
SET apprentice_order_id = 1 
WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

-- Update next 10 to link to apprentice_order_id 2
UPDATE apprentices 
SET apprentice_order_id = 2 
WHERE id IN (11, 12, 13, 14, 15, 16, 17, 18, 19, 20);

-- Update next 10 to link to apprentice_order_id 3
UPDATE apprentices 
SET apprentice_order_id = 3 
WHERE id IN (21, 22, 23, 24, 25, 26, 27, 28, 29, 30);

-- Update next 10 to link to apprentice_order_id 4
UPDATE apprentices 
SET apprentice_order_id = 4 
WHERE id IN (31, 32, 33, 34, 35, 36, 37, 38, 39, 40);

-- Update next 10 to link to apprentice_order_id 5
UPDATE apprentices 
SET apprentice_order_id = 5 
WHERE id IN (41, 42, 43, 44, 45, 46, 47, 48, 49, 50);

-- Verify the updates
SELECT 
    apprentice_order_id,
    COUNT(*) as count,
    GROUP_CONCAT(name SEPARATOR ', ') as sample_names
FROM apprentices
WHERE apprentice_order_id IS NOT NULL
GROUP BY apprentice_order_id
ORDER BY apprentice_order_id;
