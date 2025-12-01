# Phase 3-4: Testing Guide - LPK Registration Wizard

## 🧪 Complete Testing Checklist

**Status:** Phase 3-4 Implementation 95% Complete  
**Testing Phase:** Ready for Manual Testing  
**Date:** December 1, 2025

---

## ✅ Pre-Testing Checklist

Before starting tests, verify all components are in place:

- [x] **Database:** email_verification_tokens table exists
- [x] **Models:** EmailVerificationTokensTable + Entity created
- [x] **Controller:** LpkRegistrationController (512 lines, 6 actions)
- [x] **Views:** 4 templates (create, verify, setPassword, index)
- [x] **Emails:** 4 templates (verification + welcome, HTML + text)
- [x] **JavaScript:** Cascade dropdowns, password strength meter
- [ ] **Email Service:** Configured and tested (Gmail SMTP)
- [ ] **Database Connection:** cms_authentication_authorization working

---

## 📋 Test Scenarios

### Scenario 1: Happy Path - Complete Registration Flow

**Test ID:** LPK-001  
**Priority:** HIGH  
**Estimated Time:** 10 minutes

**Steps:**

1. **Admin Creates LPK Registration**
   ```
   Navigate to: /admin/lpk-registration/create
   
   Fill form:
   - Institution Name: TEST LPK INDONESIA
   - Registration Number: REG-TEST-2025-001
   - Director Name: John Doe
   - Email: test@example.com
   - Phone: 081234567890
   - Address: Jl. Test No. 123
   - Province: DKI Jakarta
   - City: Jakarta Pusat (cascade filtered)
   
   Submit form
   ```

   **Expected Results:**
   - ✅ Form submits successfully
   - ✅ Flash message: "LPK registered successfully. Verification email sent."
   - ✅ Redirects to /admin/lpk-registration
   - ✅ New record appears in list with status "Pending Verification"
   - ✅ Database: vocational_training_institutions.status = 'pending_verification'
   - ✅ Database: email_verification_tokens table has new token
   - ✅ Token is 64 characters (hexadecimal)
   - ✅ Token expires_at is 24 hours from now
   - ✅ Email sent to test@example.com
   - ✅ Activity logged in stakeholder_activities

2. **LPK Receives Verification Email**
   ```
   Check email inbox (test@example.com)
   ```

   **Expected Results:**
   - ✅ Email received with subject "Verify Your Email - TMM System Registration"
   - ✅ HTML version displays correctly (gradient header, styled)
   - ✅ Institution name displayed: TEST LPK INDONESIA
   - ✅ Director name displayed: John Doe
   - ✅ Registration number displayed: REG-TEST-2025-001
   - ✅ Registered by admin name displayed
   - ✅ Registration date displayed
   - ✅ Verification button visible and styled
   - ✅ Alternative text link visible
   - ✅ Footer with company info displayed

3. **LPK Clicks Verification Link**
   ```
   Click "VERIFY EMAIL ADDRESS" button in email
   or
   Copy/paste verification URL to browser
   ```

   **Expected Results:**
   - ✅ Redirects to /lpk-registration/verify-email/{token}
   - ✅ Loading screen appears briefly
   - ✅ Success screen displays with checkmark
   - ✅ Institution name shown
   - ✅ Countdown timer starts from 5 seconds
   - ✅ "Next Step" info box visible
   - ✅ Database: institution status updated to 'verified'
   - ✅ Database: email_verified_at timestamp set
   - ✅ Database: token marked as used (is_used = 1)
   - ✅ Activity logged: email verification
   - ✅ Auto-redirects after 5 seconds to /lpk-registration/set-password/{id}

4. **LPK Sets Password**
   ```
   Password setup page loads automatically
   
   Try weak password first:
   - Password: test
   - Confirm: test
   ```

   **Expected Results:**
   - ✅ Page displays institution info
   - ✅ Password requirements list visible (5 items)
   - ✅ Strength meter at 0/5 - Gray - "No password"
   - ✅ Requirements show red circles (not met)
   - ✅ Submit button DISABLED

   ```
   Try medium password:
   - Password: Test1234
   - Confirm: Test1234
   ```

   **Expected Results:**
   - ✅ Strength meter shows 4/5 - Yellow - "Medium"
   - ✅ 4 requirements turn green (missing special char)
   - ✅ Submit button still DISABLED

   ```
   Try strong password:
   - Password: Test1234!
   - Confirm: Test1234!
   - Check "I agree to Terms & Conditions"
   ```

   **Expected Results:**
   - ✅ Strength meter shows 5/5 - Green - "Strong"
   - ✅ All 5 requirements turn green (checkmarks)
   - ✅ Password match message: "Passwords match" (green)
   - ✅ Submit button ENABLED
   - ✅ Submit form

5. **Account Activation**
   ```
   After form submission
   ```

   **Expected Results:**
   - ✅ User account created in users table
   - ✅ Username auto-generated (e.g., test_lpk_indonesia)
   - ✅ Password bcrypt hashed
   - ✅ institution_id links to LPK
   - ✅ institution_type = 'vocational_training'
   - ✅ status = 'active', is_active = 1
   - ✅ Database: institution status = 'active'
   - ✅ Welcome email sent to test@example.com
   - ✅ Activity logged: account activation
   - ✅ Flash message: "Password set successfully. Welcome email sent."
   - ✅ Redirects to /users/login

6. **LPK Receives Welcome Email**
   ```
   Check email inbox again
   ```

   **Expected Results:**
   - ✅ Email received with subject "Welcome to TMM System - Account Activated"
   - ✅ HTML version displays correctly (green gradient)
   - ✅ Success banner displayed
   - ✅ Username shown correctly
   - ✅ Email shown
   - ✅ Institution name shown
   - ✅ "LOGIN NOW" button visible
   - ✅ Feature list displayed (7 features)
   - ✅ Getting started tips (5 steps)
   - ✅ Support information visible

7. **LPK Logs In**
   ```
   Login page: /users/login
   
   Enter credentials:
   - Username: {auto-generated username}
   - Password: Test1234!
   ```

   **Expected Results:**
   - ✅ Login successful
   - ✅ Redirects to dashboard
   - ✅ User session created
   - ✅ Institution data accessible
   - ✅ User can access LPK features

---

### Scenario 2: Error Handling - Expired Token

**Test ID:** LPK-002  
**Priority:** HIGH  
**Estimated Time:** 5 minutes

**Setup:**
```sql
-- Manually create expired token in database
INSERT INTO email_verification_tokens (user_email, token, token_type, expires_at, is_used, created)
VALUES ('expired@example.com', REPEAT('a', 64), 'email_verification', DATE_SUB(NOW(), INTERVAL 25 HOUR), 0, NOW());
```

**Steps:**

1. **Try to verify with expired token**
   ```
   Navigate to: /lpk-registration/verify-email/aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
   ```

   **Expected Results:**
   - ✅ Invalid token screen displays
   - ✅ Warning icon (red) shown
   - ✅ Error message: "The verification link is invalid..."
   - ✅ Reasons list includes "Link has expired (valid for 24 hours)"
   - ✅ "Request New Link" button visible
   - ✅ "Contact Support" button visible
   - ✅ No auto-redirect

---

### Scenario 3: Error Handling - Used Token

**Test ID:** LPK-003  
**Priority:** HIGH  
**Estimated Time:** 5 minutes

**Steps:**

1. **Complete Scenario 1 first** (to create used token)

2. **Try to verify with same token again**
   ```
   Use the same verification link from email
   Click verification button again
   ```

   **Expected Results:**
   - ✅ Invalid token screen displays
   - ✅ Reasons list includes "Link has already been used"
   - ✅ "Request New Link" button visible
   - ✅ Database: token is_used = 1, used_at timestamp set
   - ✅ Institution status already 'active' (no change)

---

### Scenario 4: Error Handling - Malformed Token

**Test ID:** LPK-004  
**Priority:** MEDIUM  
**Estimated Time:** 3 minutes

**Steps:**

1. **Try short token**
   ```
   Navigate to: /lpk-registration/verify-email/shorttoken
   ```

   **Expected Results:**
   - ✅ Invalid token screen displays
   - ✅ Reasons include "Link is incomplete or corrupted"

2. **Try SQL injection**
   ```
   Navigate to: /lpk-registration/verify-email/a' OR '1'='1
   ```

   **Expected Results:**
   - ✅ Invalid token screen (no SQL error)
   - ✅ CakePHP ORM prevents injection

---

### Scenario 5: Resend Verification Email

**Test ID:** LPK-005  
**Priority:** HIGH  
**Estimated Time:** 5 minutes

**Setup:** Register LPK but don't verify email yet

**Steps:**

1. **Admin resends verification**
   ```
   Navigate to: /admin/lpk-registration
   Find LPK with status "Pending Verification"
   Click "Resend Verification Email" button
   ```

   **Expected Results:**
   - ✅ Flash message: "Verification email resent successfully"
   - ✅ Database: Old tokens marked as used (is_used = 1)
   - ✅ Database: New token generated (64 chars, not used)
   - ✅ New email sent with new token
   - ✅ Old verification link no longer works
   - ✅ New verification link works

---

### Scenario 6: Password Validation - All Requirements

**Test ID:** LPK-006  
**Priority:** HIGH  
**Estimated Time:** 10 minutes

**Setup:** Complete verification step first

**Steps:**

1. **Test length requirement**
   ```
   Password: Test1!
   ```
   **Expected:** ❌ Length requirement not met (red)

2. **Test uppercase requirement**
   ```
   Password: test1234!
   ```
   **Expected:** ❌ Uppercase requirement not met (red)

3. **Test lowercase requirement**
   ```
   Password: TEST1234!
   ```
   **Expected:** ❌ Lowercase requirement not met (red)

4. **Test number requirement**
   ```
   Password: TestTest!
   ```
   **Expected:** ❌ Number requirement not met (red)

5. **Test special character requirement**
   ```
   Password: Test1234
   ```
   **Expected:** ❌ Special char requirement not met (red)

6. **Test password mismatch**
   ```
   Password: Test1234!
   Confirm: Test1234@
   ```
   **Expected:** ❌ "Passwords do not match" (red)

7. **Test terms not accepted**
   ```
   Password: Test1234!
   Confirm: Test1234!
   Terms: [ ] Unchecked
   ```
   **Expected:** ❌ Submit button DISABLED

8. **Test all requirements met**
   ```
   Password: Test1234!
   Confirm: Test1234!
   Terms: [x] Checked
   ```
   **Expected:** ✅ Submit button ENABLED, all green

---

### Scenario 7: Cascade Dropdowns - Geographic Selection

**Test ID:** LPK-007  
**Priority:** MEDIUM  
**Estimated Time:** 5 minutes

**Steps:**

1. **Test initial state**
   ```
   Navigate to: /admin/lpk-registration/create
   Check dropdowns
   ```

   **Expected Results:**
   - ✅ Province dropdown shows all provinces
   - ✅ City dropdown empty (placeholder: "Select City")
   - ✅ Subdistrict dropdown empty
   - ✅ Village dropdown empty

2. **Select Province**
   ```
   Select: DKI Jakarta
   ```

   **Expected Results:**
   - ✅ City dropdown populates with Jakarta cities only
   - ✅ Cities: Jakarta Pusat, Jakarta Barat, Jakarta Selatan, etc.
   - ✅ Subdistrict dropdown cleared
   - ✅ Village dropdown cleared

3. **Select City**
   ```
   Select: Jakarta Pusat
   ```

   **Expected Results:**
   - ✅ Subdistrict dropdown populates with Jakarta Pusat subdistricts
   - ✅ Village dropdown cleared

4. **Change Province**
   ```
   Change Province to: Jawa Barat
   ```

   **Expected Results:**
   - ✅ City dropdown resets and shows Jawa Barat cities
   - ✅ Previous city selection cleared
   - ✅ Subdistrict dropdown cleared
   - ✅ Village dropdown cleared

---

### Scenario 8: Mobile Responsive Design

**Test ID:** LPK-008  
**Priority:** MEDIUM  
**Estimated Time:** 10 minutes

**Test Devices:**
- iPhone 12 (375px width)
- iPad (768px width)
- Desktop (1920px width)

**Pages to Test:**
1. Registration form (/admin/lpk-registration/create)
2. Email verification (/lpk-registration/verify-email/{token})
3. Password setup (/lpk-registration/set-password/{id})
4. Registration list (/admin/lpk-registration)

**Expected Results:**
- ✅ All forms readable and usable on mobile
- ✅ Buttons appropriately sized for touch
- ✅ No horizontal scrolling
- ✅ Cards stack properly on small screens
- ✅ Text remains readable (min 14px)
- ✅ Dropdown menus work on touch devices
- ✅ Progress bars visible on mobile

---

## 🔒 Security Testing

### Security Test 1: Token Security

**Test ID:** SEC-001  
**Priority:** CRITICAL

**Tests:**

1. **Token format validation**
   ```
   Test various token formats:
   - 63 characters: Should fail
   - 64 characters: Should work
   - 65 characters: Should fail
   - Special chars: Should fail (not hex)
   ```

2. **Token reuse prevention**
   ```
   1. Use token to verify
   2. Try to use same token again
   Expected: Fails with "already used" error
   ```

3. **Token expiry**
   ```
   Wait 25 hours or manually expire token
   Try to verify
   Expected: Fails with "expired" error
   ```

### Security Test 2: SQL Injection

**Test ID:** SEC-002  
**Priority:** CRITICAL

**Tests:**

```
Try these in token parameter:
1. ' OR '1'='1
2. '; DROP TABLE users; --
3. 1' UNION SELECT * FROM users--

Expected: All fail gracefully, no SQL errors
```

### Security Test 3: XSS Prevention

**Test ID:** SEC-003  
**Priority:** CRITICAL

**Tests:**

```
Try these in form fields:
1. Institution Name: <script>alert('XSS')</script>
2. Director Name: <img src=x onerror=alert('XSS')>
3. Email: test@example.com<script>alert('XSS')</script>

Expected: All escaped and displayed as text, no script execution
```

### Security Test 4: Password Hashing

**Test ID:** SEC-004  
**Priority:** CRITICAL

**Test:**

```sql
-- After setting password "Test1234!"
SELECT password FROM users WHERE username = '{username}';

Expected:
- Password NOT stored in plain text
- Starts with "$2y$" (bcrypt)
- Length 60 characters
- Example: $2y$10$abcdefghijklmnopqrstuvwxyz0123456789ABCDEF
```

---

## 📊 Database Verification

### Test 1: Verify Token Creation

```sql
-- After admin creates LPK
SELECT 
    user_email,
    token,
    token_type,
    LENGTH(token) as token_length,
    is_used,
    expires_at,
    TIMESTAMPDIFF(HOUR, NOW(), expires_at) as hours_until_expiry
FROM email_verification_tokens
WHERE user_email = 'test@example.com'
ORDER BY created DESC
LIMIT 1;

Expected:
- token_length = 64
- token_type = 'email_verification'
- is_used = 0
- hours_until_expiry ≈ 24
```

### Test 2: Verify Institution Status Flow

```sql
-- After each step
SELECT 
    id,
    name,
    email,
    status,
    email_verified_at,
    created
FROM vocational_training_institutions
WHERE email = 'test@example.com';

Expected:
Step 1 (Create): status = 'pending_verification', email_verified_at = NULL
Step 2 (Verify): status = 'verified', email_verified_at = NOW()
Step 3 (Password): status = 'active', email_verified_at unchanged
```

### Test 3: Verify User Account Creation

```sql
-- After password setup
SELECT 
    u.id,
    u.username,
    u.email,
    u.fullname,
    u.institution_id,
    u.institution_type,
    u.status,
    u.is_active,
    LENGTH(u.password) as password_length,
    SUBSTRING(u.password, 1, 4) as password_prefix
FROM users u
WHERE u.email = 'test@example.com';

Expected:
- username matches auto-generated format
- institution_id matches LPK id
- institution_type = 'vocational_training'
- status = 'active'
- is_active = 1
- password_length = 60
- password_prefix = '$2y$' (bcrypt)
```

### Test 4: Verify Activity Logs

```sql
-- After complete flow
SELECT 
    activity_type,
    stakeholder_type,
    stakeholder_id,
    description,
    additional_data,
    created
FROM stakeholder_activities
WHERE stakeholder_type = 'vocational_training'
AND stakeholder_id = (SELECT id FROM vocational_training_institutions WHERE email = 'test@example.com')
ORDER BY created ASC;

Expected 3 activities:
1. activity_type = 'registration', description = 'LPK registered...'
2. activity_type = 'verification', description = 'Email verified...'
3. activity_type = 'activation', description = 'Account activated...'
```

---

## 📧 Email Testing

### Email Test 1: SMTP Configuration

**Before any tests:**

```php
// Check config/app.php
'EmailTransport' => [
    'default' => [
        'className' => 'Smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'timeout' => 30,
        'username' => 'your-email@gmail.com', // ✅ Set correctly
        'password' => 'app-password', // ✅ Gmail App Password
        'tls' => true,
    ],
]
```

**Test:**
```bash
# Send test email from CakePHP console
cd /var/www/tmm
bin/cake console

# In console:
$email = new \Cake\Mailer\Email('default');
$email->from(['noreply@asahifamily.id' => 'TMM System'])
    ->to('test@example.com')
    ->subject('Test Email')
    ->send('This is a test email.');
```

**Expected:** Email received in test inbox

### Email Test 2: HTML vs Text

**Test:**
1. Send verification email
2. Check inbox with HTML enabled
3. Check inbox with HTML disabled (text-only)

**Expected:**
- HTML version: Styled with gradients, buttons, colors
- Text version: Plain text, readable, no HTML tags

### Email Test 3: Email Clients Compatibility

**Test email display in:**
- Gmail (web)
- Outlook (web)
- Apple Mail (iOS)
- Gmail (Android app)
- Outlook (desktop)

**Expected:** All display correctly, no broken layouts

---

## 🐛 Bug Report Template

Use this template when reporting issues:

```
Bug ID: BUG-XXX
Severity: [Critical/High/Medium/Low]
Status: [New/In Progress/Fixed]

**Title:** Short description

**Environment:**
- OS: Windows/Linux/Mac
- Browser: Chrome/Firefox/Safari
- PHP Version: 7.4
- Database: MySQL 8.0

**Steps to Reproduce:**
1. Step 1
2. Step 2
3. Step 3

**Expected Behavior:**
What should happen

**Actual Behavior:**
What actually happened

**Screenshots:**
[Attach screenshots]

**Error Logs:**
```
logs/error.log excerpt
```

**Database State:**
```sql
SELECT * FROM relevant_table WHERE condition;
```

**Additional Notes:**
Any other relevant information
```

---

## ✅ Testing Completion Checklist

### Functional Testing
- [ ] Scenario 1: Happy Path (all steps work)
- [ ] Scenario 2: Expired token handling
- [ ] Scenario 3: Used token handling
- [ ] Scenario 4: Malformed token handling
- [ ] Scenario 5: Resend verification
- [ ] Scenario 6: Password validation
- [ ] Scenario 7: Cascade dropdowns
- [ ] Scenario 8: Mobile responsive

### Security Testing
- [ ] SEC-001: Token security
- [ ] SEC-002: SQL injection prevention
- [ ] SEC-003: XSS prevention
- [ ] SEC-004: Password hashing

### Database Testing
- [ ] Token creation verified
- [ ] Status flow verified
- [ ] User account creation verified
- [ ] Activity logs verified

### Email Testing
- [ ] SMTP configured and working
- [ ] HTML emails render correctly
- [ ] Text emails readable
- [ ] Compatible with major email clients

### Performance Testing
- [ ] Page load times < 3 seconds
- [ ] Email sent within 5 seconds
- [ ] Database queries optimized
- [ ] No N+1 query problems

### Browser Compatibility
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

### Device Testing
- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

---

## 📝 Test Results Summary

**To be filled after testing:**

| Test ID | Scenario | Status | Notes |
|---------|----------|--------|-------|
| LPK-001 | Happy Path | ⏳ Pending | |
| LPK-002 | Expired Token | ⏳ Pending | |
| LPK-003 | Used Token | ⏳ Pending | |
| LPK-004 | Malformed Token | ⏳ Pending | |
| LPK-005 | Resend Email | ⏳ Pending | |
| LPK-006 | Password Validation | ⏳ Pending | |
| LPK-007 | Cascade Dropdowns | ⏳ Pending | |
| LPK-008 | Mobile Responsive | ⏳ Pending | |
| SEC-001 | Token Security | ⏳ Pending | |
| SEC-002 | SQL Injection | ⏳ Pending | |
| SEC-003 | XSS Prevention | ⏳ Pending | |
| SEC-004 | Password Hashing | ⏳ Pending | |

---

**Testing Conducted By:** _________________  
**Date:** _________________  
**Sign-off:** _________________  

---

*Last Updated: December 1, 2025*  
*Phase 3-4 Status: Ready for Testing*
