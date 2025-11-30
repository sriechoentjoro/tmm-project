-- Add title column to apprentice_orders table
-- This field will auto-populate with format: "Organization Name (Year Month)"

USE cms_tmm_trainees;

-- Add title column
ALTER TABLE apprentice_orders 
ADD COLUMN title VARCHAR(512) NULL AFTER other_requirements,
ADD INDEX idx_title (title);

-- Update existing records with title
UPDATE apprentice_orders ao
LEFT JOIN cms_masters.acceptance_organizations org ON ao.acceptance_organization_id = org.id
SET ao.title = CONCAT(
    IFNULL(org.title, IFNULL(org.name, 'No Organization')),
    ' (',
    IFNULL(ao.departure_year, ''),
    ' ',
    CASE ao.departure_month
        WHEN 1 THEN 'January'
        WHEN 2 THEN 'February'
        WHEN 3 THEN 'March'
        WHEN 4 THEN 'April'
        WHEN 5 THEN 'May'
        WHEN 6 THEN 'June'
        WHEN 7 THEN 'July'
        WHEN 8 THEN 'August'
        WHEN 9 THEN 'September'
        WHEN 10 THEN 'October'
        WHEN 11 THEN 'November'
        WHEN 12 THEN 'December'
        ELSE IFNULL(ao.departure_month, '')
    END,
    ')'
);

-- Create trigger to auto-update title on INSERT
DROP TRIGGER IF EXISTS apprentice_orders_before_insert;
DELIMITER $$
CREATE TRIGGER apprentice_orders_before_insert
BEFORE INSERT ON apprentice_orders
FOR EACH ROW
BEGIN
    DECLARE org_name VARCHAR(255);
    DECLARE month_name VARCHAR(20);
    
    -- Get organization name
    SELECT IFNULL(title, IFNULL(name, 'No Organization'))
    INTO org_name
    FROM cms_masters.acceptance_organizations
    WHERE id = NEW.acceptance_organization_id
    LIMIT 1;
    
    -- Get month name
    SET month_name = CASE NEW.departure_month
        WHEN 1 THEN 'January'
        WHEN 2 THEN 'February'
        WHEN 3 THEN 'March'
        WHEN 4 THEN 'April'
        WHEN 5 THEN 'May'
        WHEN 6 THEN 'June'
        WHEN 7 THEN 'July'
        WHEN 8 THEN 'August'
        WHEN 9 THEN 'September'
        WHEN 10 THEN 'October'
        WHEN 11 THEN 'November'
        WHEN 12 THEN 'December'
        ELSE IFNULL(NEW.departure_month, '')
    END;
    
    -- Set title
    SET NEW.title = CONCAT(
        IFNULL(org_name, 'No Organization'),
        ' (',
        IFNULL(NEW.departure_year, ''),
        ' ',
        month_name,
        ')'
    );
END$$
DELIMITER ;

-- Create trigger to auto-update title on UPDATE
DROP TRIGGER IF EXISTS apprentice_orders_before_update;
DELIMITER $$
CREATE TRIGGER apprentice_orders_before_update
BEFORE UPDATE ON apprentice_orders
FOR EACH ROW
BEGIN
    DECLARE org_name VARCHAR(255);
    DECLARE month_name VARCHAR(20);
    
    -- Only update if related fields changed
    IF NEW.acceptance_organization_id <> OLD.acceptance_organization_id OR
       NEW.departure_year <> OLD.departure_year OR
       NEW.departure_month <> OLD.departure_month THEN
        
        -- Get organization name
        SELECT IFNULL(title, IFNULL(name, 'No Organization'))
        INTO org_name
        FROM cms_masters.acceptance_organizations
        WHERE id = NEW.acceptance_organization_id
        LIMIT 1;
        
        -- Get month name
        SET month_name = CASE NEW.departure_month
            WHEN 1 THEN 'January'
            WHEN 2 THEN 'February'
            WHEN 3 THEN 'March'
            WHEN 4 THEN 'April'
            WHEN 5 THEN 'May'
            WHEN 6 THEN 'June'
            WHEN 7 THEN 'July'
            WHEN 8 THEN 'August'
            WHEN 9 THEN 'September'
            WHEN 10 THEN 'October'
            WHEN 11 THEN 'November'
            WHEN 12 THEN 'December'
            ELSE IFNULL(NEW.departure_month, '')
        END;
        
        -- Set title
        SET NEW.title = CONCAT(
            IFNULL(org_name, 'No Organization'),
            ' (',
            IFNULL(NEW.departure_year, ''),
            ' ',
            month_name,
            ')'
        );
    END IF;
END$$
DELIMITER ;

-- Verify triggers created
SHOW TRIGGERS FROM cms_tmm_trainees WHERE `Table` = 'apprentice_orders';

-- Sample title output
SELECT id, title, departure_year, departure_month 
FROM apprentice_orders 
LIMIT 5;
