-- Add stock_incoming_id to stock_outgoings
-- Create MySQL triggers for auto-create accounting_transactions
-- Compatible dengan MariaDB versi lama (single trigger per event)

USE asahi_inventories;

-- 1. Add stock_incoming_id column
ALTER TABLE stock_outgoings
  ADD COLUMN stock_incoming_id INT(11) NULL COMMENT 'Reference to stock_incoming (NO FK)' AFTER inventory_id,
  ADD COLUMN unit_price DECIMAL(15,2) NULL COMMENT 'Harga satuan dari stock_incoming' AFTER qty_outgoing,
  ADD INDEX idx_stock_incoming (stock_incoming_id);

-- 2. Drop existing triggers if any
DROP TRIGGER IF EXISTS trg_stock_outgoing_insert;
DROP TRIGGER IF EXISTS trg_stock_outgoing_update;
DROP TRIGGER IF EXISTS trg_stock_outgoing_delete;

-- 3. Create AFTER INSERT trigger
DELIMITER $$

CREATE TRIGGER trg_stock_outgoing_insert
AFTER INSERT ON stock_outgoings
FOR EACH ROW
BEGIN
    DECLARE v_inventory_name VARCHAR(255);
    DECLARE v_amount DECIMAL(15,2);
    DECLARE v_receipt_path VARCHAR(255);
    DECLARE v_incoming_price DECIMAL(15,2);
    
    -- Get inventory name
    SELECT name INTO v_inventory_name 
    FROM inventories 
    WHERE id = NEW.inventory_id;
    
    -- Get price and receipt dari stock_incoming
    SET v_receipt_path = NULL;
    SET v_incoming_price = 0;
    
    IF NEW.stock_incoming_id IS NOT NULL THEN
        SELECT 
            si.price_po,
            pr.file_path
        INTO 
            v_incoming_price,
            v_receipt_path
        FROM stock_incomings si
        LEFT JOIN purchase_receipts pr ON pr.id = si.purchase_receipt_id
        WHERE si.id = NEW.stock_incoming_id;
    END IF;
    
    -- Calculate amount
    SET v_amount = NEW.qty_outgoing * COALESCE(NEW.unit_price, v_incoming_price, 0);
    
    -- Insert accounting transaction
    INSERT INTO accounting_transactions (
        transaction_date,
        description,
        debit_account,
        credit_account,
        amount,
        reference_type,
        reference_id,
        receipt_file_path,
        created,
        modified
    ) VALUES (
        DATE(NEW.outgoing_date),
        CONCAT('Pemakaian Sparepart: ', v_inventory_name, ' (', NEW.qty_outgoing, ' unit) - ', COALESCE(NEW.usage_description, '')),
        'Beban Pemeliharaan',
        'Persediaan Inventory',
        v_amount,
        'StockOutgoing',
        NEW.id,
        v_receipt_path,
        NOW(),
        NOW()
    );
END$$

DELIMITER ;

-- 4. Create AFTER UPDATE trigger
DELIMITER $$

CREATE TRIGGER trg_stock_outgoing_update
AFTER UPDATE ON stock_outgoings
FOR EACH ROW
BEGIN
    DECLARE v_inventory_name VARCHAR(255);
    DECLARE v_amount DECIMAL(15,2);
    DECLARE v_receipt_path VARCHAR(255);
    DECLARE v_incoming_price DECIMAL(15,2);
    
    -- Get inventory name
    SELECT name INTO v_inventory_name 
    FROM inventories 
    WHERE id = NEW.inventory_id;
    
    -- Get price and receipt
    SET v_receipt_path = NULL;
    SET v_incoming_price = 0;
    
    IF NEW.stock_incoming_id IS NOT NULL THEN
        SELECT 
            si.price_po,
            pr.file_path
        INTO 
            v_incoming_price,
            v_receipt_path
        FROM stock_incomings si
        LEFT JOIN purchase_receipts pr ON pr.id = si.purchase_receipt_id
        WHERE si.id = NEW.stock_incoming_id;
    END IF;
    
    SET v_amount = NEW.qty_outgoing * COALESCE(NEW.unit_price, v_incoming_price, 0);
    
    -- Update accounting transaction
    UPDATE accounting_transactions
    SET
        transaction_date = DATE(NEW.outgoing_date),
        description = CONCAT('Pemakaian Sparepart: ', v_inventory_name, ' (', NEW.qty_outgoing, ' unit) - ', COALESCE(NEW.usage_description, '')),
        amount = v_amount,
        receipt_file_path = v_receipt_path,
        modified = NOW()
    WHERE
        reference_type = 'StockOutgoing'
        AND reference_id = NEW.id;
END$$

DELIMITER ;

-- 5. Create AFTER DELETE trigger
DELIMITER $$

CREATE TRIGGER trg_stock_outgoing_delete
AFTER DELETE ON stock_outgoings
FOR EACH ROW
BEGIN
    DELETE FROM accounting_transactions
    WHERE
        reference_type = 'StockOutgoing'
        AND reference_id = OLD.id;
END$$

DELIMITER ;

-- Verify
SELECT 'Stock Outgoings columns and triggers created!' AS Status;
SHOW COLUMNS FROM stock_outgoings WHERE Field IN ('stock_incoming_id', 'unit_price');
SHOW TRIGGERS WHERE `Table` = 'stock_outgoings';
