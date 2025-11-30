USE asahi_inventories;

-- Drop old INSERT trigger
DROP TRIGGER IF EXISTS `update_after_stock-outgoings_add`;

-- Create new combined INSERT trigger (inventory update + accounting)
DELIMITER $$

CREATE TRIGGER `update_after_stock-outgoings_add`
AFTER INSERT ON stock_outgoings
FOR EACH ROW
BEGIN
    DECLARE v_inventory_name VARCHAR(255);
    DECLARE v_amount DECIMAL(15,2);
    DECLARE v_receipt_path VARCHAR(255);
    DECLARE v_incoming_price DECIMAL(15,2);
    
    -- Update inventory qty (existing logic)
    UPDATE inventories SET qty = qty - NEW.qty_outgoing WHERE id = NEW.inventory_id;
    
    -- Get inventory name untuk accounting
    SELECT name INTO v_inventory_name FROM inventories WHERE id = NEW.inventory_id;
    
    -- Get price and receipt dari stock_incoming
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
    
    -- Create accounting transaction
    INSERT INTO accounting_transactions (
        transaction_date, description, debit_account, credit_account,
        amount, reference_type, reference_id, receipt_file_path, created, modified
    ) VALUES (
        DATE(NEW.outgoing_date),
        CONCAT('Pemakaian Sparepart: ', COALESCE(v_inventory_name, ''), ' (', NEW.qty_outgoing, ' unit) - ', COALESCE(NEW.usage_description, '')),
        'Beban Pemeliharaan', 'Persediaan Inventory',
        v_amount, 'StockOutgoing', NEW.id, v_receipt_path, NOW(), NOW()
    );
END$$

DELIMITER ;

SELECT 'INSERT trigger updated!' AS Status;
