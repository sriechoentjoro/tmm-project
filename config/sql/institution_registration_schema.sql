-- ============================================================================
-- Database Schema Changes for Institution Email Registration System
-- ============================================================================

-- Step 1: Modify vocational_training_institutions table
ALTER TABLE `vocational_training_institutions` 
  MODIFY `password` VARCHAR(255) NULL COMMENT 'Hashed password, set during registration',
  ADD `registration_token` VARCHAR(255) NULL COMMENT 'Unique token for email registration' AFTER `password`,
  ADD `token_expires_at` DATETIME NULL COMMENT 'Token expiry datetime' AFTER `registration_token`,
  ADD `is_registered` TINYINT(1) DEFAULT 0 COMMENT 'Registration completion status' AFTER `token_expires_at`,
  ADD `registered_at` DATETIME NULL COMMENT 'Registration completion datetime' AFTER `is_registered`,
  ADD INDEX `idx_registration_token` (`registration_token`),
  ADD INDEX `idx_is_registered` (`is_registered`);

-- Step 2: Modify special_skill_support_institutions table
ALTER TABLE `special_skill_support_institutions` 
  MODIFY `password` VARCHAR(255) NULL COMMENT 'Hashed password, set during registration',
  ADD `registration_token` VARCHAR(255) NULL COMMENT 'Unique token for email registration' AFTER `password`,
  ADD `token_expires_at` DATETIME NULL COMMENT 'Token expiry datetime' AFTER `registration_token`,
  ADD `is_registered` TINYINT(1) DEFAULT 0 COMMENT 'Registration completion status' AFTER `token_expires_at`,
  ADD `registered_at` DATETIME NULL COMMENT 'Registration completion datetime' AFTER `is_registered`,
  ADD INDEX `idx_registration_token` (`registration_token`),
  ADD INDEX `idx_is_registered` (`is_registered`);

-- Step 3: Create email_templates table in cms_authentication_authorization
USE `cms_authentication_authorization`;

CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `template_key` VARCHAR(50) NOT NULL COMMENT 'Unique identifier for template',
  `subject` VARCHAR(255) NOT NULL COMMENT 'Email subject line',
  `body_html` TEXT NOT NULL COMMENT 'HTML version of email body',
  `body_text` TEXT NULL COMMENT 'Plain text version of email body',
  `variables` TEXT NULL COMMENT 'JSON array of available variables',
  `description` VARCHAR(255) NULL COMMENT 'Template description',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT 'Template active status',
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Email templates for system notifications';

-- Step 4: Insert default email templates
INSERT INTO `email_templates` 
(`template_key`, `subject`, `body_html`, `body_text`, `variables`, `description`, `is_active`, `created`, `modified`) 
VALUES 
('institution_registration', 
 'Complete Your Institution Registration - TMM System',
 '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .info-box { background: white; padding: 15px; border-left: 4px solid #667eea; margin: 15px 0; }
        .footer { text-align: center; color: #6c757d; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to TMM System</h1>
        </div>
        <div class="content">
            <p>Dear <strong>{{institution_name}}</strong>,</p>
            
            <p>Your institution has been successfully registered in our Training Management System. To complete your registration and access the system, please follow the steps below:</p>
            
            <div class="info-box">
                <strong>Your Login Credentials:</strong><br>
                Username: <strong>{{username}}</strong><br>
                Email: <strong>{{email}}</strong>
            </div>
            
            <p>Click the button below to set your password and complete the registration:</p>
            
            <p style="text-align: center;">
                <a href="{{registration_url}}" class="button">Complete Registration</a>
            </p>
            
            <p><strong>Important:</strong> This registration link will expire on <strong>{{expiry_date}}</strong>. Please complete your registration before this date.</p>
            
            <p>If you did not expect this email or have any questions, please contact our support team.</p>
            
            <div class="footer">
                <p>Best regards,<br>TMM System Administration Team</p>
                <p>&copy; 2025 Training Management System. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>',
 'Welcome to TMM System

Dear {{institution_name}},

Your institution has been successfully registered in our Training Management System. To complete your registration and access the system, please follow the steps below:

Your Login Credentials:
Username: {{username}}
Email: {{email}}

Complete your registration by visiting this link:
{{registration_url}}

IMPORTANT: This registration link will expire on {{expiry_date}}. Please complete your registration before this date.

If you did not expect this email or have any questions, please contact our support team.

Best regards,
TMM System Administration Team

© 2025 Training Management System. All rights reserved.',
 '["institution_name", "username", "email", "registration_url", "expiry_date"]',
 'Email sent to institutions to complete registration process',
 1,
 NOW(), 
 NOW());

-- Step 5: Create email_logs table for tracking
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `template_key` VARCHAR(50) NULL,
  `recipient_email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `body` TEXT NULL,
  `status` ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
  `error_message` TEXT NULL,
  `sent_at` DATETIME NULL,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_recipient` (`recipient_email`),
  INDEX `idx_status` (`status`),
  INDEX `idx_template_key` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Email sending logs';

-- Verification queries
SELECT 'Database schema updated successfully!' AS status;
SHOW COLUMNS FROM `vocational_training_institutions` WHERE Field LIKE '%registration%' OR Field LIKE '%registered%';
SHOW COLUMNS FROM `special_skill_support_institutions` WHERE Field LIKE '%registration%' OR Field LIKE '%registered%';
SELECT * FROM `cms_authentication_authorization`.`email_templates`;
