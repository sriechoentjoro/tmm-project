-- Update INSERT trigger untuk stock_outgoings
-- Add personnel_id and vehicle_id to accounting_transactions

USE asahi_inventories;

DROP TRIGGER IF EXISTS `update_after_stock-outgoings_add`;

DELIMITER $$

CREATE TRIGGER `update_after_stock-outgoings_add` 
AFTER INSERT ON `stock_outgoings` 
FOR EACH ROW 
BEGIN
    -- Update inventory qty (existing logic)
    UPDATE inventories 
    SET qty = qty - NEW.qty_outgoing,
        qty_after = qty,
        qty_before = qty + NEW.qty_outgoing
    WHERE id = NEW.inventory_id;
    
    -- Get receipt_file_path from purchase_receipts via stock_incomings
    SET @receipt_path = NULL;
    SELECT pr.file_path INTO @receipt_path
    FROM stock_incomings si
    LEFT JOIN purchase_receipts pr ON pr.id = si.purchase_receipt_id
    WHERE si.id = NEW.stock_incoming_id
    LIMIT 1;
    
    -- Calculate amount
    SET @amount = NEW.qty_outgoing * COALESCE(NEW.unit_price, 
        (SELECT price_po FROM stock_incomings WHERE id = NEW.stock_incoming_id), 
        0);
    
    -- Get inventory name for description
    SET @inventory_name = (SELECT name FROM inventories WHERE id = NEW.inventory_id);
    
    -- Insert accounting transaction with personnel_id and vehicle_id
    INSERT INTO accounting_transactions (
        transaction_date,
        description,
        debit_account,
        credit_account,
        amount,
        reference_type,
        reference_id,
        personnel_id,
        vehicle_id,
        receipt_file_path,
        created,
        modified
    ) VALUES (
        NEW.outgoing_date,
        CONCAT('Pemakaian Inventory: ', @inventory_name, ' (', NEW.qty_outgoing, ' unit)', 
               CASE WHEN NEW.usage_description IS NOT NULL 
                    THEN CONCAT(' - ', NEW.usage_description) 
                    ELSE '' END),
        'Beban Pemeliharaan',
        'Persediaan Inventory',
        @amount,
        'StockOutgoing',
        NEW.id,
        NEW.personnel_id,  -- Pelaksana pemakai barang
        NEW.vehicle_id,    -- Alokasi kendaraan
        @receipt_path,
        NOW(),
        NOW()
    );
END$$

DELIMITER ;

SELECT 'INSERT trigger updated with personnel_id and vehicle_id!' as status;
