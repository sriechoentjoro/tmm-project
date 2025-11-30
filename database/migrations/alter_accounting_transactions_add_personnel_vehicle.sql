-- Add personnel_id and vehicle_id to accounting_transactions
-- To track who used the items and for which vehicle

USE asahi_inventories;

ALTER TABLE accounting_transactions 
ADD COLUMN personnel_id INT(11) NULL AFTER reference_id,
ADD COLUMN vehicle_id INT(11) NULL AFTER personnel_id;

-- Add indexes for better query performance
ALTER TABLE accounting_transactions
ADD INDEX idx_personnel (personnel_id),
ADD INDEX idx_vehicle (vehicle_id);

-- Note: NO FOREIGN KEY constraints (per design requirement)
-- Relationships managed via bindingModel in controllers

SELECT 'accounting_transactions table updated with personnel_id and vehicle_id!' as status;

-- Show updated structure
DESCRIBE accounting_transactions;
