# Testing Without Email - Manual Testing Guide

## Overview

You can test the entire registration system without email by manually creating tokens and accessing the registration wizard directly.

## Step 1: Apply Database Schema

```sql
-- In phpMyAdmin, run:
SOURCE d:/xampp/htdocs/tmm/config/sql/institution_registration_schema.sql;
```

Verify:
```sql
SHOW TABLES FROM system_authentication_authorization LIKE 'email%';
SELECT * FROM system_authentication_authorization.email_templates;
```

## Step 2: Create Test Institution Manually

```sql
-- Insert test institution
INSERT INTO vocational_training_institutions (
    is_special_skill_support_institution,
    name,
    master_propinsi_id,
    master_kabupaten_id,
    master_kecamatan_id,
    master_kelurahan_id,
    address,
    director,
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
    1, -- Adjust to valid province ID
    1, -- Adjust to valid city ID
    1, -- Adjust to valid district ID
    1, -- Adjust to valid village ID
    'Test Address 123',
    'Test Director',
    'test@example.com',
    'testlpk',
    'TEST_TOKEN_12345',
    DATE_ADD(NOW(), INTERVAL 48 HOUR),
    0,
    'test.pdf',
    NOW(),
    NOW()
);
```

## Step 3: Access Registration Wizard Directly

Open browser:
```
http://localhost/tmm/institution-registration/complete/TEST_TOKEN_12345
```

## Step 4: Complete Registration

1. **Verify** institution details are displayed
2. **Set password:**
   - Enter strong password (min 8 chars)
   - Confirm password
3. **Submit**
4. **Expected:**
   - User created in auth database
   - Auto-login
   - Redirect to dashboard

## Step 5: Verify Database

```sql
-- Check institution is registered
SELECT id, name, email, username, is_registered, registered_at
FROM vocational_training_institutions
WHERE username = 'testlpk';

-- Check user created
SELECT id, username, email, full_name
FROM system_authentication_authorization.users
WHERE username = 'testlpk';

-- Check role assigned
SELECT ur.*, r.name
FROM system_authentication_authorization.user_roles ur
JOIN system_authentication_authorization.roles r ON ur.role_id = r.id
WHERE ur.user_id = (SELECT id FROM system_authentication_authorization.users WHERE username = 'testlpk');
```

## Step 6: Test Login

1. Logout from admin
2. Login with:
   - Username: `testlpk`
   - Password: (what you set in wizard)
3. Verify successful login

## Testing Checklist

### Registration Wizard
- [ ] Can access wizard with valid token
- [ ] Institution details displayed correctly
- [ ] Password strength indicator works
- [ ] Password confirmation validates
- [ ] Weak password rejected
- [ ] Registration completes successfully
- [ ] User auto-logged in

### Database
- [ ] `is_registered` = 1
- [ ] `registered_at` has timestamp
- [ ] `registration_token` = NULL (cleared)
- [ ] User exists in auth database
- [ ] Role ID 6 assigned
- [ ] Password is hashed

### Login
- [ ] Can login with credentials
- [ ] Dashboard accessible
- [ ] User has correct permissions

## Testing Different Scenarios

### Test Expired Token

```sql
UPDATE vocational_training_institutions
SET token_expires_at = DATE_SUB(NOW(), INTERVAL 1 HOUR)
WHERE username = 'testlpk';
```

Access wizard - should show "expired" message.

### Test Invalid Token

Access: `http://localhost/tmm/institution-registration/complete/INVALID_TOKEN`

Should show error message.

### Test Already Registered

```sql
UPDATE vocational_training_institutions
SET is_registered = 1,
    registered_at = NOW()
WHERE username = 'testlpk';
```

Access wizard - should redirect to login.

## Creating Multiple Test Institutions

```sql
-- Test institution 2
INSERT INTO vocational_training_institutions (
    is_special_skill_support_institution, name, master_propinsi_id,
    master_kabupaten_id, master_kecamatan_id, master_kelurahan_id,
    address, email, username, registration_token, token_expires_at,
    is_registered, mou_file, created, modified
) VALUES (
    0, 'Test LPK 2', 1, 1, 1, 1, 'Address 2',
    'test2@example.com', 'testlpk2', 'TOKEN_2',
    DATE_ADD(NOW(), INTERVAL 48 HOUR), 0, 'test.pdf', NOW(), NOW()
);

-- Test institution 3 (Special Skill)
INSERT INTO special_skill_support_institutions (
    company_name, address, contact_person, email, username,
    registration_token, token_expires_at, is_registered,
    created, modified
) VALUES (
    'Test Special Skill Company', 'Address 3', 'Contact Person',
    'test3@example.com', 'testspecial', 'TOKEN_3',
    DATE_ADD(NOW(), INTERVAL 48 HOUR), 0, NOW(), NOW()
);
```

## Simulating Email Workflow

Even without sending emails, you can simulate the full workflow:

1. **Admin creates institution** (via admin interface or SQL)
2. **System generates token** (automatic via Entity method)
3. **"Email sent"** (skip this step)
4. **Institution receives link** (manually construct URL)
5. **Institution completes registration** (access wizard directly)
6. **User account created** (automatic)
7. **Institution can login** (test login)

## Admin Interface Testing

### Create Institution via Admin

1. Go to: `http://localhost/tmm/vocational-training-institutions/add`
2. Fill form (all fields)
3. Submit
4. **Note the ID** from success message or database

### Get Registration URL

```sql
SELECT 
    id,
    name,
    email,
    username,
    registration_token,
    CONCAT('http://localhost/tmm/institution-registration/complete/', registration_token) as registration_url
FROM vocational_training_institutions
WHERE id = [ID_FROM_ABOVE]
ORDER BY id DESC
LIMIT 1;
```

Copy the `registration_url` and open in browser.

## Troubleshooting

### "Token not found"

Check token exists:
```sql
SELECT * FROM vocational_training_institutions WHERE registration_token = 'YOUR_TOKEN';
```

### "User already exists"

Delete existing user:
```sql
DELETE FROM system_authentication_authorization.user_roles WHERE user_id = (SELECT id FROM system_authentication_authorization.users WHERE username = 'testlpk');
DELETE FROM system_authentication_authorization.users WHERE username = 'testlpk';
```

### "Role ID 6 not found"

Create role:
```sql
INSERT INTO system_authentication_authorization.roles (id, name, description, created, modified)
VALUES (6, 'Institution', 'Institution User Role', NOW(), NOW());
```

## Next Steps

Once email is configured:
1. Test full workflow with real email sending
2. Test "Resend Email" functionality
3. Test email templates
4. Monitor email logs

Until then, this manual testing approach validates:
- ✅ Registration wizard functionality
- ✅ Password validation
- ✅ User account creation
- ✅ Role assignment
- ✅ Auto-login
- ✅ Database integrity

The only thing not tested is email sending itself, which can be verified later when SMTP is configured.
