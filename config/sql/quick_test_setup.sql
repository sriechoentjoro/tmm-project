-- Quick Start: Manual Testing Without Email
-- Run this in phpMyAdmin to set up test data

-- Step 1: Apply schema (if not already done)
-- SOURCE d:/xampp/htdocs/tmm/config/sql/institution_registration_schema.sql;

-- Step 2: Create test institution with token
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
    'Test LPK Institution',
    1,
    1,
    1,
    1,
    'Jl. Test No. 123, Jakarta',
    '12345',
    'Test Director',
    'テストディレクター',
    'sriechoentjoro@gmail.com',
    'testlpk',
    'QUICKTEST123',
    DATE_ADD(NOW(), INTERVAL 48 HOUR),
    0,
    'test_mou.pdf',
    NOW(),
    NOW()
);

-- Step 3: Verify insertion
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
WHERE username = 'testlpk';

-- Step 4: Copy the registration_url from above and open in browser
-- Example: http://localhost/tmm/institution-registration/complete/QUICKTEST123

-- Step 5: After completing registration, verify user created
SELECT 
    u.id,
    u.username,
    u.email,
    u.full_name,
    u.is_active,
    r.name as role_name
FROM system_authentication_authorization.users u
LEFT JOIN system_authentication_authorization.user_roles ur ON u.id = ur.user_id
LEFT JOIN system_authentication_authorization.roles r ON ur.role_id = r.id
WHERE u.username = 'testlpk';

-- Step 6: Verify institution is registered
SELECT 
    id,
    name,
    email,
    username,
    is_registered,
    registered_at,
    registration_token
FROM vocational_training_institutions
WHERE username = 'testlpk';

-- Expected results:
-- is_registered = 1
-- registered_at = (timestamp)
-- registration_token = NULL (cleared)

-- CLEANUP (if you want to test again)
-- DELETE FROM system_authentication_authorization.user_roles WHERE user_id IN (SELECT id FROM system_authentication_authorization.users WHERE username = 'testlpk');
-- DELETE FROM system_authentication_authorization.users WHERE username = 'testlpk';
-- DELETE FROM vocational_training_institutions WHERE username = 'testlpk';
