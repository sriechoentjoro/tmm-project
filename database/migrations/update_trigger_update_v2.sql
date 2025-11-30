-- Update UPDATE trigger untuk stock_outgoings
-- Update personnel_id and vehicle_id in accounting_transactions

USE asahi_inventories;

DROP TRIGGER IF EXISTS `updade_after_stock-outgoings_edit`;

DELIMITER $$

CREATE TRIGGER `updade_after_stock-outgoings_edit` 
AFTER UPDATE ON `stock_outgoings` 
FOR EACH ROW 
BEGIN
    -- Update inventory qty (existing logic)
    UPDATE inventories 
    SET qty = qty + OLD.qty_outgoing - NEW.qty_outgoing,
        qty_after = qty,
        qty_before = qty - OLD.qty_outgoing + NEW.qty_outgoing
    WHERE id = NEW.inventory_id;
    
    -- Get receipt_file_path from purchase_receipts via stock_incomings
    SET @receipt_path = NULL;
    SELECT pr.file_path INTO @receipt_path
    FROM stock_incomings si
    LEFT JOIN purchase_receipts pr ON pr.id = si.purchase_receipt_id
    WHERE si.id = NEW.stock_incoming_id
    LIMIT 1;
    
    -- Calculate new amount
    SET @amount = NEW.qty_outgoing * COALESCE(NEW.unit_price, 
        (SELECT price_po FROM stock_incomings WHERE id = NEW.stock_incoming_id), 
        0);
    
    -- Get inventory name for description
    SET @inventory_name = (SELECT name FROM inventories WHERE id = NEW.inventory_id);
    
    -- Update accounting transaction with personnel_id and vehicle_id
    UPDATE accounting_transactions 
    SET 
        transaction_date = NEW.outgoing_date,
        description = CONCAT('Pemakaian Inventory: ', @inventory_name, ' (', NEW.qty_outgoing, ' unit)', 
                            CASE WHEN NEW.usage_description IS NOT NULL 
                                 THEN CONCAT(' - ', NEW.usage_description) 
                                 ELSE '' END),
        amount = @amount,
        personnel_id = NEW.personnel_id,  -- Update pelaksana
        vehicle_id = NEW.vehicle_id,      -- Update alokasi kendaraan
        receipt_file_path = @receipt_path,
        modified = NOW()
    WHERE reference_type = 'StockOutgoing' 
      AND reference_id = NEW.id;
END$$

DELIMITER ;

SELECT 'UPDATE trigger updated with personnel_id and vehicle_id!' as status;
