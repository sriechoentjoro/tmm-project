-- Comprehensive Testing Script for Institution Registration System
-- Run this in phpMyAdmin to set up test data

-- ============================================
-- PART 1: Create Test Institution for Wizard
-- ============================================

-- First, get valid IDs for foreign keys
SELECT 'Step 1: Getting valid location IDs...' as status;

-- You may need to adjust these IDs based on your master data
SET @propinsi_id = (SELECT id FROM master_propinsis LIMIT 1);
SET @kabupaten_id = (SELECT id FROM master_kabupatens LIMIT 1);
SET @kecamatan_id = (SELECT id FROM master_kecamatans LIMIT 1);
SET @kelurahan_id = (SELECT id FROM master_kelurahans LIMIT 1);

SELECT 
    @propinsi_id as propinsi_id,
    @kabupaten_id as kabupaten_id,
    @kecamatan_id as kecamatan_id,
    @kelurahan_id as kelurahan_id;

-- Create test institution with registration token
SELECT 'Step 2: Creating test institution...' as status;

INSERT INTO vocational_training_institutions (
    is_special_skill_support_institution,
    name,
    master_propinsi_id,
    master_kabupaten_id,
    master_kecamatan_id,
    master_kelurahan_id,
    address,
    post_code,
    director,
    director_katakana,
    email,
    username,
    registration_token,
    token_expires_at,
    is_registered,
    mou_file,
    created,
    modified
) VALUES (
    0,
    'Test LPK Institution - Wizard Test',
    @propinsi_id,
    @kabupaten_id,
    @kecamatan_id,
    @kelurahan_id,
    'Jl. Test Wizard No. 123, Jakarta Pusat',
    '10110',
    'Test Director',
    'テストディレクター',
    'sriechoentjoro@gmail.com',
    'testwizard',
    'WIZARD_TEST_TOKEN_123',
    DATE_ADD(NOW(), INTERVAL 48 HOUR),
    0,
    'test_mou.pdf',
    NOW(),
    NOW()
);

-- Get the registration URL
SELECT 'Step 3: Registration URL for testing...' as status;

SELECT 
    id,
    name,
    email,
    username,
    registration_token,
    token_expires_at,
    is_registered,
    CONCAT('http://localhost/tmm/institution-registration/complete/', registration_token) as registration_url
FROM vocational_training_institutions
WHERE username = 'testwizard';

-- ============================================
-- PART 2: Verification Queries
-- ============================================

-- Check email templates exist
SELECT 'Step 4: Checking email templates...' as status;
SELECT COUNT(*) as template_count FROM cms_authentication_authorization.email_templates;

-- Check if role ID 6 exists
SELECT 'Step 5: Checking institution role...' as status;
SELECT * FROM cms_authentication_authorization.roles WHERE id = 6;

-- If role doesn't exist, create it
INSERT IGNORE INTO cms_authentication_authorization.roles (id, name, description, created, modified)
VALUES (6, 'Institution', 'Institution User Role', NOW(), NOW());

-- ============================================
-- PART 3: Testing Instructions
-- ============================================

SELECT '
============================================
TESTING INSTRUCTIONS
============================================

TEST 1: REGISTRATION WIZARD
----------------------------
1. Copy the registration_url from the query above
2. Open it in your browser
3. You should see the registration wizard
4. Set a password (min 8 characters)
5. Submit the form
6. You should be auto-logged in

Expected URL format:
http://localhost/tmm/institution-registration/complete/WIZARD_TEST_TOKEN_123


TEST 2: ADMIN INTERFACE
----------------------------
1. Login as admin
2. Go to: http://localhost/tmm/vocational-training-institutions/add
3. Fill in the form:
   - Name: Test LPK Admin Created
   - Email: sriechoentjoro@gmail.com
   - Username: testadmin
   - Address: Test Address
   - Select locations
   - Upload MOU file
4. Submit
5. Check the success message for registration URL


TEST 3: VERIFY DATABASE
----------------------------
Run these queries after completing registration:

-- Check institution is registered
SELECT id, name, email, username, is_registered, registered_at
FROM vocational_training_institutions
WHERE username IN (''testwizard'', ''testadmin'');

-- Check user created
SELECT id, username, email, full_name
FROM cms_authentication_authorization.users
WHERE username IN (''testwizard'', ''testadmin'');

-- Check role assigned
SELECT u.username, r.name as role_name
FROM cms_authentication_authorization.users u
JOIN cms_authentication_authorization.user_roles ur ON u.id = ur.user_id
JOIN cms_authentication_authorization.roles r ON ur.role_id = r.id
WHERE u.username IN (''testwizard'', ''testadmin'');


TEST 4: LOGIN TEST
----------------------------
1. Logout from admin
2. Login with:
   Username: testwizard (or testadmin)
   Password: (what you set in wizard)
3. Verify successful login


CLEANUP (Optional)
----------------------------
To reset and test again:

DELETE FROM cms_authentication_authorization.user_roles 
WHERE user_id IN (
    SELECT id FROM cms_authentication_authorization.users 
    WHERE username IN (''testwizard'', ''testadmin'')
);

DELETE FROM cms_authentication_authorization.users 
WHERE username IN (''testwizard'', ''testadmin'');

DELETE FROM vocational_training_institutions 
WHERE username IN (''testwizard'', ''testadmin'');

============================================
' as instructions;
