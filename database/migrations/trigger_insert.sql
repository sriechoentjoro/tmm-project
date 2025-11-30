-- Create MySQL triggers only (columns already exist)
USE asahi_inventories;

-- CREATE AFTER INSERT trigger
DELIMITER $$

CREATE TRIGGER trg_stock_outgoing_insert
AFTER INSERT ON stock_outgoings
FOR EACH ROW
BEGIN
    DECLARE v_inventory_name VARCHAR(255);
    DECLARE v_amount DECIMAL(15,2);
    DECLARE v_receipt_path VARCHAR(255);
    DECLARE v_incoming_price DECIMAL(15,2);
    
    SELECT name INTO v_inventory_name FROM inventories WHERE id = NEW.inventory_id;
    
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
    
    INSERT INTO accounting_transactions (
        transaction_date, description, debit_account, credit_account,
        amount, reference_type, reference_id, receipt_file_path, created, modified
    ) VALUES (
        DATE(NEW.outgoing_date),
        CONCAT('Pemakaian Sparepart: ', v_inventory_name, ' (', NEW.qty_outgoing, ' unit) - ', COALESCE(NEW.usage_description, '')),
        'Beban Pemeliharaan', 'Persediaan Inventory',
        v_amount, 'StockOutgoing', NEW.id, v_receipt_path, NOW(), NOW()
    );
END$$

DELIMITER ;
