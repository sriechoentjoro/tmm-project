-- Add triggers to auto-update title field in apprentice_orders table
-- Format: "Organization Name (Year Month)"

USE cms_tmm_trainees;

-- Update existing records with title (backfill)
UPDATE apprentice_orders ao
LEFT JOIN cms_masters.acceptance_organizations org ON ao.acceptance_organization_id = org.id
SET ao.title = CONCAT(
    'Apprenticeship Order: ',
    IFNULL(org.title, IFNULL(org.name, 'No Organization')),
    ' (',
    IFNULL(ao.departure_year, ''),
    ' - ',
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
)
WHERE ao.title IS NULL OR ao.title = '';

-- Drop existing triggers if any
DROP TRIGGER IF EXISTS apprentice_orders_before_insert;
DROP TRIGGER IF EXISTS apprentice_orders_before_update;

-- Create trigger to auto-update title on INSERT
DELIMITER $$
CREATE TRIGGER apprentice_orders_before_insert
BEFORE INSERT ON apprentice_orders
FOR EACH ROW
BEGIN
    DECLARE org_name VARCHAR(255);
    DECLARE month_name VARCHAR(20);
    
    -- Get organization name from acceptance_organizations
    SELECT IFNULL(title, IFNULL(name, 'No Organization'))
    INTO org_name
    FROM cms_masters.acceptance_organizations
    WHERE id = NEW.acceptance_organization_id
    LIMIT 1;
    
    -- Convert month number to name
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
    
    -- Set title field
    SET NEW.title = CONCAT(
        'Apprenticeship Order: ',
        IFNULL(org_name, 'No Organization'),
        ' (',
        IFNULL(NEW.departure_year, ''),
        ' - ',
        month_name,
        ')'
    );
END;
DELIMITER ;

-- Create trigger to auto-update title on UPDATE
DELIMITER $$
CREATE TRIGGER apprentice_orders_before_update
BEFORE UPDATE ON apprentice_orders
FOR EACH ROW
BEGIN
    DECLARE org_name VARCHAR(255);
    DECLARE month_name VARCHAR(20);
    
    -- Only update title if related fields changed
    IF NEW.acceptance_organization_id <> OLD.acceptance_organization_id OR
       NEW.departure_year <> OLD.departure_year OR
       NEW.departure_month <> OLD.departure_month THEN
        
        -- Get organization name from acceptance_organizations
        SELECT IFNULL(title, IFNULL(name, 'No Organization'))
        INTO org_name
        FROM cms_masters.acceptance_organizations
        WHERE id = NEW.acceptance_organization_id
        LIMIT 1;
        
        -- Convert month number to name
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
        
        -- Set title field
        SET NEW.title = CONCAT(
            'Apprenticeship Order: ',
            IFNULL(org_name, 'No Organization'),
            ' (',
            IFNULL(NEW.departure_year, ''),
            ' - ',
            month_name,
            ')'
        );
    END IF;
END$$
DELIMITER ;

-- Verify triggers were created successfully
SHOW TRIGGERS FROM cms_tmm_trainees WHERE `Table` = 'apprentice_orders';

-- Show sample of updated titles
SELECT 
    id, 
    title, 
    acceptance_organization_id,
    departure_year, 
    departure_month 
FROM apprentice_orders 
ORDER BY id 
LIMIT 10;

-- Summary
SELECT 
    COUNT(*) as total_records,
    SUM(CASE WHEN title IS NOT NULL AND title != '' THEN 1 ELSE 0 END) as records_with_title,
    SUM(CASE WHEN title IS NULL OR title = '' THEN 1 ELSE 0 END) as records_without_title
FROM apprentice_orders;
