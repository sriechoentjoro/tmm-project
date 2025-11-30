USE asahi_inventories;

-- Drop old UPDATE trigger
DROP TRIGGER IF EXISTS `updade_after_stock-outgoings_edit`;

-- Create new combined UPDATE trigger
DELIMITER $$

CREATE TRIGGER `updade_after_stock-outgoings_edit`
AFTER UPDATE ON stock_outgoings
FOR EACH ROW
BEGIN
    DECLARE v_inventory_name VARCHAR(255);
    DECLARE v_amount DECIMAL(15,2);
    DECLARE v_receipt_path VARCHAR(255);
    DECLARE v_incoming_price DECIMAL(15,2);
    
    -- Update inventory qty (existing logic)
    UPDATE inventories SET qty = qty - NEW.qty_outgoing WHERE id = NEW.inventory_id;
    
    -- Get inventory name
    SELECT name INTO v_inventory_name FROM inventories WHERE id = NEW.inventory_id;
    
    -- Get price and receipt
    SET v_receipt_path = NULL;
    SET v_incoming_price = 0;
    
    IF NEW.stock_incoming_id IS NOT NULL THEN
        SELECT si.price_po, pr.file_path
        INTO v_incoming_price, v_receipt_path
        FROM stock_incomings si
        LEFT JOIN purchase_receipts pr ON pr.id = si.purchase_receipt_id
        WHERE si.id = NEW.stock_incoming_id;
    END IF;
    
    SET v_amount = NEW.qty_outgoing * COALESCE(NEW.unit_price, v_incoming_price, 0);
    
    -- Update accounting transaction
    UPDATE accounting_transactions
    SET
        transaction_date = DATE(NEW.outgoing_date),
        description = CONCAT('Pemakaian Sparepart: ', COALESCE(v_inventory_name, ''), ' (', NEW.qty_outgoing, ' unit) - ', COALESCE(NEW.usage_description, '')),
        amount = v_amount,
        receipt_file_path = v_receipt_path,
        modified = NOW()
    WHERE
        reference_type = 'StockOutgoing' AND reference_id = NEW.id;
END$$

DELIMITER ;

SELECT 'UPDATE trigger updated!' AS Status;
