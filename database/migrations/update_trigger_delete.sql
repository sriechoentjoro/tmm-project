USE asahi_inventories;

-- Drop old DELETE trigger
DROP TRIGGER IF EXISTS `update_after_stock-outgoings_delete`;

-- Create new combined DELETE trigger
DELIMITER $$

CREATE TRIGGER `update_after_stock-outgoings_delete`
AFTER DELETE ON stock_outgoings
FOR EACH ROW
BEGIN
    -- Update inventory qty (existing logic - revert qty)
    UPDATE inventories SET qty = qty + OLD.qty_outgoing WHERE id = OLD.inventory_id;
    
    -- Delete accounting transaction
    DELETE FROM accounting_transactions
    WHERE reference_type = 'StockOutgoing' AND reference_id = OLD.id;
END$$

DELIMITER ;

SELECT 'DELETE trigger updated!' AS Status;
