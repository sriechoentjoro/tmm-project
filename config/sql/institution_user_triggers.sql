-- ============================================================================
-- Triggers for Vocational Training Institutions and Special Skill Support Institutions
-- Automatically creates user accounts in cms_authentication_authorization
-- Role ID: 6 (Institution User)
-- ============================================================================

-- Note: Password field should be VARCHAR(255) not INT(255)
-- If you used INT, please run this first:
-- ALTER TABLE `vocational_training_institutions` MODIFY `password` VARCHAR(255) NOT NULL;
-- ALTER TABLE `special_skill_support_institutions` MODIFY `password` VARCHAR(255) NOT NULL;

DELIMITER $$

-- ============================================================================
-- VOCATIONAL TRAINING INSTITUTIONS TRIGGERS
-- ============================================================================

-- Trigger: After INSERT on vocational_training_institutions
DROP TRIGGER IF EXISTS `after_vocational_training_institution_insert`$$
CREATE TRIGGER `after_vocational_training_institution_insert`
AFTER INSERT ON `vocational_training_institutions`
FOR EACH ROW
BEGIN
    DECLARE user_id INT;
    
    -- Only create user if username and password are provided
    IF NEW.username IS NOT NULL AND NEW.username != '' AND NEW.password IS NOT NULL AND NEW.password != '' THEN
        
        -- Insert into users table
        INSERT INTO `cms_authentication_authorization`.`users` (
            `username`,
            `password`,
            `email`,
            `created`,
            `modified`,
            `active`
        ) VALUES (
            NEW.username,
            NEW.password,  -- Should already be hashed by the application
            NEW.email,
            NOW(),
            NOW(),
            1  -- Active by default
        );
        
        -- Get the newly created user ID
        SET user_id = LAST_INSERT_ID();
        
        -- Insert into user_roles table with role_id = 6
        INSERT INTO `cms_authentication_authorization`.`user_roles` (
            `user_id`,
            `role_id`,
            `created`
        ) VALUES (
            user_id,
            6,  -- Institution User role
            NOW()
        );
        
    END IF;
END$$

-- Trigger: After UPDATE on vocational_training_institutions
DROP TRIGGER IF EXISTS `after_vocational_training_institution_update`$$
CREATE TRIGGER `after_vocational_training_institution_update`
AFTER UPDATE ON `vocational_training_institutions`
FOR EACH ROW
BEGIN
    DECLARE existing_user_id INT;
    
    -- Check if username or email changed
    IF NEW.username != OLD.username OR NEW.email != OLD.email OR NEW.password != OLD.password THEN
        
        -- Try to find existing user by old username
        SELECT `id` INTO existing_user_id
        FROM `cms_authentication_authorization`.`users`
        WHERE `username` = OLD.username
        LIMIT 1;
        
        IF existing_user_id IS NOT NULL THEN
            -- Update existing user
            UPDATE `cms_authentication_authorization`.`users`
            SET 
                `username` = NEW.username,
                `email` = NEW.email,
                `password` = IF(NEW.password != OLD.password, NEW.password, `password`),
                `modified` = NOW()
            WHERE `id` = existing_user_id;
        ELSE
            -- User doesn't exist, create new one (same as INSERT trigger)
            IF NEW.username IS NOT NULL AND NEW.username != '' AND NEW.password IS NOT NULL AND NEW.password != '' THEN
                
                INSERT INTO `cms_authentication_authorization`.`users` (
                    `username`,
                    `password`,
                    `email`,
                    `created`,
                    `modified`,
                    `active`
                ) VALUES (
                    NEW.username,
                    NEW.password,
                    NEW.email,
                    NOW(),
                    NOW(),
                    1
                );
                
                SET existing_user_id = LAST_INSERT_ID();
                
                -- Insert role if not exists
                INSERT IGNORE INTO `cms_authentication_authorization`.`user_roles` (
                    `user_id`,
                    `role_id`,
                    `created`
                ) VALUES (
                    existing_user_id,
                    6,
                    NOW()
                );
                
            END IF;
        END IF;
        
    END IF;
END$$

-- ============================================================================
-- SPECIAL SKILL SUPPORT INSTITUTIONS TRIGGERS
-- ============================================================================

-- Trigger: After INSERT on special_skill_support_institutions
DROP TRIGGER IF EXISTS `after_special_skill_institution_insert`$$
CREATE TRIGGER `after_special_skill_institution_insert`
AFTER INSERT ON `special_skill_support_institutions`
FOR EACH ROW
BEGIN
    DECLARE user_id INT;
    
    -- Only create user if username and password are provided
    IF NEW.username IS NOT NULL AND NEW.username != '' AND NEW.password IS NOT NULL AND NEW.password != '' THEN
        
        -- Insert into users table
        INSERT INTO `cms_authentication_authorization`.`users` (
            `username`,
            `password`,
            `email`,
            `created`,
            `modified`,
            `active`
        ) VALUES (
            NEW.username,
            NEW.password,  -- Should already be hashed by the application
            NEW.email,
            NOW(),
            NOW(),
            1  -- Active by default
        );
        
        -- Get the newly created user ID
        SET user_id = LAST_INSERT_ID();
        
        -- Insert into user_roles table with role_id = 6
        INSERT INTO `cms_authentication_authorization`.`user_roles` (
            `user_id`,
            `role_id`,
            `created`
        ) VALUES (
            user_id,
            6,  -- Institution User role
            NOW()
        );
        
    END IF;
END$$

-- Trigger: After UPDATE on special_skill_support_institutions
DROP TRIGGER IF EXISTS `after_special_skill_institution_update`$$
CREATE TRIGGER `after_special_skill_institution_update`
AFTER UPDATE ON `special_skill_support_institutions`
FOR EACH ROW
BEGIN
    DECLARE existing_user_id INT;
    
    -- Check if username or email changed
    IF NEW.username != OLD.username OR NEW.email != OLD.email OR NEW.password != OLD.password THEN
        
        -- Try to find existing user by old username
        SELECT `id` INTO existing_user_id
        FROM `cms_authentication_authorization`.`users`
        WHERE `username` = OLD.username
        LIMIT 1;
        
        IF existing_user_id IS NOT NULL THEN
            -- Update existing user
            UPDATE `cms_authentication_authorization`.`users`
            SET 
                `username` = NEW.username,
                `email` = NEW.email,
                `password` = IF(NEW.password != OLD.password, NEW.password, `password`),
                `modified` = NOW()
            WHERE `id` = existing_user_id;
        ELSE
            -- User doesn't exist, create new one (same as INSERT trigger)
            IF NEW.username IS NOT NULL AND NEW.username != '' AND NEW.password IS NOT NULL AND NEW.password != '' THEN
                
                INSERT INTO `cms_authentication_authorization`.`users` (
                    `username`,
                    `password`,
                    `email`,
                    `created`,
                    `modified`,
                    `active`
                ) VALUES (
                    NEW.username,
                    NEW.password,
                    NEW.email,
                    NOW(),
                    NOW(),
                    1
                );
                
                SET existing_user_id = LAST_INSERT_ID();
                
                -- Insert role if not exists
                INSERT IGNORE INTO `cms_authentication_authorization`.`user_roles` (
                    `user_id`,
                    `role_id`,
                    `created`
                ) VALUES (
                    existing_user_id,
                    6,
                    NOW()
                );
                
            END IF;
        END IF;
        
    END IF;
END$$

DELIMITER ;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================
-- Run these to verify the triggers were created successfully:
-- SHOW TRIGGERS WHERE `Table` = 'vocational_training_institutions';
-- SHOW TRIGGERS WHERE `Table` = 'special_skill_support_institutions';
