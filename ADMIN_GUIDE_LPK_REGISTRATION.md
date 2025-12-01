# Admin Guide: LPK Registration Management

## 📖 Overview

This guide explains how to manage Vocational Training Institution (LPK) registrations in the TMM system using the new 3-step registration wizard.

**Target Audience:** System Administrators  
**Version:** 1.0  
**Last Updated:** December 1, 2025

---

## 🎯 Registration Process Overview

The LPK registration process has 3 steps:

```
┌─────────────────────────────────────────────────────────────────┐
│                    3-STEP REGISTRATION FLOW                      │
└─────────────────────────────────────────────────────────────────┘

STEP 1: ADMIN REGISTRATION (You)
┌──────────────────────────────────────┐
│ Admin creates LPK profile            │
│ - Institution details                │
│ - Contact information                │
│ - Geographic location                │
│ - System generates secure token      │
│ - Verification email sent            │
│ Status: pending_verification         │
└──────────────────────────────────────┘
                 ↓
STEP 2: EMAIL VERIFICATION (LPK)
┌──────────────────────────────────────┐
│ LPK receives verification email      │
│ - Clicks verification link           │
│ - Token validated (24-hour expiry)   │
│ - Email confirmed                    │
│ Status: verified                     │
└──────────────────────────────────────┘
                 ↓
STEP 3: PASSWORD SETUP (LPK)
┌──────────────────────────────────────┐
│ LPK creates secure password          │
│ - Must meet 5 requirements           │
│ - User account created               │
│ - Welcome email sent                 │
│ Status: active                       │
└──────────────────────────────────────┘
                 ↓
            ✅ COMPLETE
```

---

## 🚀 Quick Start

### Accessing the Registration Module

1. **Login** to admin panel: `https://asahifamily.id/tmm/users/login`
2. **Navigate** to: `Admin` → `LPK Registration` → `Register New LPK`
3. **Or direct URL:** `/admin/lpk-registration/create`

---

## 📝 Step-by-Step Guide

### STEP 1: Creating a New LPK Registration

#### 1.1 Fill Basic Information

**Required Fields (marked with red asterisk *):**
- **Institution Name*** - Full legal name of the LPK
  - Example: `Lembaga Pelatihan Kerja MANDIRI`
  - Max 256 characters
  
- **Registration Number*** - Unique identifier
  - Example: `REG-LPK-2025-001`
  - Max 50 characters
  - Must be unique in system

**Optional Fields:**
- **License Number** - Government-issued license
  - Example: `LIC/KEP/2024/12345`
  
- **License Expiry Date** - When license expires
  - Format: YYYY-MM-DD
  - Click calendar icon to select
  
- **Establishment Date** - When LPK was founded
  - Format: YYYY-MM-DD

#### 1.2 Fill Contact Information

**Required Fields:**
- **Director Name*** - Name of LPK director/head
  - Example: `Budi Santoso`
  - Max 256 characters
  
- **Email Address*** - Official LPK email
  - Example: `info@lpkmandiri.com`
  - **IMPORTANT:** This email will receive verification link
  - Must be valid and accessible
  - Real-time validation checks format
  
- **Address*** - Complete physical address
  - Example: `Jl. Industri No. 45, RT 03/RW 05`
  - Max 500 characters
  - Include street name, RT/RW, house number

**Optional Fields:**
- **Phone Number** - Contact telephone
  - Example: `021-12345678`
  - Max 20 characters
  
- **Website** - Official website URL
  - Example: `https://www.lpkmandiri.com`
  - Must start with http:// or https://

#### 1.3 Fill Geographic Location

**All Optional (but recommended for reporting):**

- **Province** - Select from dropdown
  - Example: `DKI Jakarta`
  - Loads all Indonesian provinces
  
- **City/District** - Automatically filtered after Province selection
  - Example: `Jakarta Pusat`
  - Only shows cities in selected province
  
- **Subdistrict** - Automatically filtered after City selection
  - Example: `Tanah Abang`
  - Only shows subdistricts in selected city
  
- **Village** - Automatically filtered after Subdistrict selection
  - Example: `Kebon Kacang`
  - Only shows villages in selected subdistrict
  
- **Postal Code** - 5-digit ZIP code
  - Example: `10240`
  - Must be exactly 5 digits

**📌 Note:** Cascade dropdowns automatically filter based on parent selection. If you change Province, City/District/Village will reset.

#### 1.4 Submit Registration

Click **"Register LPK & Send Verification Email"** button

**What happens next:**
1. ✅ Form data validated
2. ✅ LPK record created with status: `pending_verification`
3. ✅ Secure 64-character token generated (24-hour validity)
4. ✅ Verification email sent to LPK email address
5. ✅ Activity logged in system
6. ✅ You're redirected to registration list
7. ✅ Success message displayed: _"LPK registered successfully. Verification email sent to {email}."_

---

### STEP 2: LPK Email Verification (Automated)

**This step is performed by the LPK, not by you.**

#### What the LPK sees:

1. **Email Received** (usually within 1 minute)
   - Subject: "Verify Your Email - TMM System Registration"
   - Professional purple gradient design
   - Institution details displayed
   - Large "VERIFY EMAIL ADDRESS" button

2. **Click Verification Link**
   - Opens verification page
   - Token validated automatically
   - Success message with countdown timer
   - Auto-redirects to password setup after 5 seconds

3. **Status Updated**
   - Database: status changes to `verified`
   - Email verified timestamp recorded
   - Token marked as used (prevents reuse)

#### Your Role (Admin):

**Monitor the registration status:**
- Navigate to: `/admin/lpk-registration`
- Look for status badge: 🔵 **Verified** (blue badge)
- Check "Email Verified" column for timestamp

**If email not received:**
- Check spam/junk folder (most common issue)
- Verify email address correct in registration
- Click **"Resend Verification Email"** button
- New token generated, old one invalidated

---

### STEP 3: LPK Password Setup (Automated)

**This step is performed by the LPK, not by you.**

#### What the LPK sees:

1. **Password Setup Page** (auto-redirect from verification)
   - Institution info displayed at top
   - Password requirements checklist (5 requirements)
   - Real-time password strength meter
   - Confirm password field
   - Terms & conditions checkbox

2. **Password Requirements** (all must be met):
   - ✅ At least 8 characters
   - ✅ At least 1 uppercase letter (A-Z)
   - ✅ At least 1 lowercase letter (a-z)
   - ✅ At least 1 number (0-9)
   - ✅ At least 1 special character (!@#$%^&*)

3. **Submit Password**
   - User account created automatically
   - Username: Auto-generated from institution name
     - Example: `lembaga_pelatihan_kerja_mandiri`
   - Password: Bcrypt hashed (60 characters)
   - Institution linked to user account
   - Status updated to `active`
   - Welcome email sent with login credentials

#### Your Role (Admin):

**Monitor completion:**
- Navigate to: `/admin/lpk-registration`
- Look for status badge: 🟢 **Active** (green badge)
- User can now login to system

**If LPK has issues:**
- Verify status is `verified` (not `pending_verification`)
- Check if user account created in `users` table
- Can manually redirect LPK to: `/lpk-registration/set-password/{id}`

---

## 📊 Managing Registrations

### Registration List View

**URL:** `/admin/lpk-registration`

#### Table Columns:

| Column | Description |
|--------|-------------|
| **ID** | System ID (auto-increment) |
| **Institution Name** | LPK name (bold, main identifier) |
| **Registration Number** | Unique reg number |
| **Director Name** | LPK director/head name |
| **Email** | Email address with envelope icon |
| **Location** | Province, City/District |
| **Status** | Color-coded badge (see below) |
| **Email Verified** | Timestamp or "Not yet" |
| **Registered** | Creation date |
| **Actions** | Button group (see below) |

#### Status Badges:

| Badge | Color | Meaning | Next Action |
|-------|-------|---------|-------------|
| **Pending Verification** | 🟡 Yellow | Waiting for email verification | LPK must click email link |
| **Verified** | 🔵 Blue | Email confirmed, waiting password | LPK must set password |
| **Active** | 🟢 Green | Complete, can login | None - registration complete |

#### Action Buttons:

**View Details** (Always available)
- 👁️ Eye icon
- Shows full LPK profile
- Read-only view
- Shows all registration data

**Resend Verification Email** (Only if `Pending Verification`)
- 📧 Envelope icon
- Invalidates old token
- Generates new 64-char token
- Sends new verification email
- Flash message: _"Verification email resent successfully to {email}"_

**Set Password Link** (Only if `Verified`, opens in new tab)
- 🔑 Key icon
- Opens password setup page
- Can share link with LPK if needed
- Format: `/lpk-registration/set-password/{id}`

---

## 🔍 Troubleshooting Common Issues

### Issue 1: Email Not Received

**Symptoms:**
- LPK says "I didn't receive the email"
- Status stuck on `pending_verification`

**Solutions:**

1. **Check email address is correct**
   ```
   - Click "View" button
   - Verify email field
   - Check for typos (common: .com vs .co.id)
   ```

2. **Check spam/junk folder**
   ```
   - Ask LPK to check spam folder
   - Gmail: Check "Promotions" tab
   - Outlook: Check "Junk Email" folder
   ```

3. **Resend verification email**
   ```
   - Click "Resend Verification Email" button
   - Wait 1-2 minutes
   - Check logs for errors: logs/error.log
   ```

4. **Verify email server working**
   ```bash
   # Test email delivery (in CakePHP console)
   bin/cake console
   
   $email = new \Cake\Mailer\Email('default');
   $email->from(['sriechoentjoro@gmail.com' => 'TMM System'])
       ->to('test@example.com')
       ->subject('Test Email')
       ->send('This is a test.');
   ```

5. **Check email service logs**
   ```bash
   tail -50 logs/error.log | grep -i email
   ```

---

### Issue 2: Verification Link Expired

**Symptoms:**
- LPK clicks link and sees "Link has expired"
- Red error icon displayed

**Cause:**
- Token valid for 24 hours only
- LPK waited > 24 hours to verify

**Solution:**

1. **Resend verification email**
   ```
   - Navigate to /admin/lpk-registration
   - Find the LPK record
   - Click "Resend Verification Email"
   - New token generated (fresh 24-hour window)
   - LPK receives new email
   ```

2. **Verify token in database**
   ```sql
   SELECT 
       user_email,
       token,
       expires_at,
       is_used,
       TIMESTAMPDIFF(HOUR, NOW(), expires_at) as hours_remaining
   FROM email_verification_tokens
   WHERE user_email = 'lpk@example.com'
   ORDER BY created DESC
   LIMIT 1;
   
   -- If hours_remaining < 0, token is expired
   ```

---

### Issue 3: Verification Link Already Used

**Symptoms:**
- LPK clicks link twice
- Error: "Link has already been used"

**Cause:**
- Token can only be used once
- LPK already verified but didn't set password

**Solution:**

**Check current status:**
```sql
SELECT id, name, email, status, email_verified_at
FROM vocational_training_institutions
WHERE email = 'lpk@example.com';
```

**If status = 'verified':**
```
- LPK should proceed to password setup
- Direct them to: /lpk-registration/set-password/{id}
- Or click "Set Password Link" button in admin list
```

**If status = 'pending_verification':**
```
- Verification didn't complete
- Resend verification email
```

---

### Issue 4: Password Too Weak

**Symptoms:**
- LPK can't submit password form
- Submit button disabled
- Some requirements still red

**Cause:**
- Password doesn't meet all 5 requirements

**Solution:**

**Guide LPK to check requirements:**
1. At least 8 characters ✅
2. At least 1 uppercase letter (A-Z) ✅
3. At least 1 lowercase letter (a-z) ✅
4. At least 1 number (0-9) ✅
5. At least 1 special character (!@#$%^&*) ✅

**Example strong passwords:**
- `Mandiri2025!`
- `LPK@Jakarta99`
- `Pelatihan#2025`

**Password strength meter:**
- Red = Weak (< 3 requirements)
- Yellow = Medium (3-4 requirements)
- Green = Strong (all 5 requirements)

---

### Issue 5: Can't Login After Registration

**Symptoms:**
- Registration complete (status = `active`)
- LPK can't login
- "Invalid username or password" error

**Solutions:**

1. **Verify user account created**
   ```sql
   SELECT 
       u.id,
       u.username,
       u.email,
       u.fullname,
       u.status,
       u.is_active,
       u.institution_id,
       u.institution_type
   FROM users u
   WHERE u.email = 'lpk@example.com';
   
   -- Should return 1 record with:
   -- status = 'active'
   -- is_active = 1
   -- institution_type = 'vocational_training'
   ```

2. **Check username format**
   ```
   - Username auto-generated from institution name
   - Example: "LPK Mandiri Indonesia" → "lpk_mandiri_indonesia"
   - All lowercase
   - Spaces replaced with underscores
   - Special characters removed
   - Check welcome email for exact username
   ```

3. **Password case-sensitive**
   ```
   - Remind LPK: Password is CASE-SENSITIVE
   - "Test1234!" ≠ "test1234!"
   - Check Caps Lock key
   ```

4. **Account not active**
   ```sql
   -- Check account status
   SELECT status, is_active FROM users WHERE email = 'lpk@example.com';
   
   -- If is_active = 0, manually activate:
   UPDATE users SET is_active = 1 WHERE email = 'lpk@example.com';
   ```

5. **Password reset (if forgotten)**
   ```
   - Currently no password reset feature
   - Admin can manually reset in database:
   
   -- Generate new password hash (for "TempPassword123!")
   -- Use CakePHP console or online bcrypt generator
   
   UPDATE users 
   SET password = '$2y$10$...' -- bcrypt hash
   WHERE email = 'lpk@example.com';
   ```

---

## 📧 Email Templates

### Verification Email

**Subject:** "Verify Your Email - TMM System Registration"

**Content:**
- Greeting with director name
- Institution name and registration number
- Registration details (registered by admin, date)
- Large "VERIFY EMAIL ADDRESS" button
- 24-hour expiry warning
- Alternative text link
- Company footer with support contact

**Design:**
- Purple gradient header (#667eea to #764ba2)
- Mobile-responsive (max 600px width)
- Professional branding
- Plain text fallback version

### Welcome Email

**Subject:** "Welcome to TMM System - Account Activated"

**Content:**
- Success banner
- Login credentials (username, email, institution)
- Large "LOGIN NOW" button
- Feature list (7 key features)
- Getting started tips (5 steps)
- Support contact information
- Company footer

**Design:**
- Green gradient header (#28a745 to #20c997)
- Mobile-responsive
- Professional branding
- Plain text fallback version

---

## 🔒 Security Features

### Token Security

**Token Generation:**
- 64 characters (hexadecimal)
- Cryptographically secure (`random_bytes(32)`)
- Unique per registration
- Stored hashed in database

**Token Validation:**
- 24-hour expiry (from creation)
- One-time use only
- Cannot be reused
- Automatically invalidated after verification

**Token Tracking:**
```sql
-- View active tokens
SELECT 
    user_email,
    token,
    expires_at,
    is_used,
    TIMESTAMPDIFF(HOUR, NOW(), expires_at) as hours_remaining,
    created
FROM email_verification_tokens
WHERE is_used = 0
AND expires_at >= NOW()
ORDER BY created DESC;
```

### Password Security

**Password Requirements:**
1. Minimum 8 characters
2. At least 1 uppercase letter (A-Z)
3. At least 1 lowercase letter (a-z)
4. At least 1 number (0-9)
5. At least 1 special character (!@#$%^&*)

**Password Storage:**
- Never stored in plain text
- Bcrypt hashing algorithm
- 60-character hash
- Salt automatically included

**Password Verification:**
```sql
-- Check password is hashed
SELECT 
    username,
    LENGTH(password) as pwd_length,
    SUBSTRING(password, 1, 4) as pwd_prefix
FROM users
WHERE email = 'lpk@example.com';

-- Expected:
-- pwd_length = 60
-- pwd_prefix = '$2y$' (bcrypt)
```

### Activity Logging

All registration activities are logged:

```sql
-- View registration activities
SELECT 
    activity_type,
    description,
    additional_data,
    admin_id,
    created
FROM stakeholder_activities
WHERE stakeholder_type = 'vocational_training'
AND stakeholder_id = {lpk_id}
ORDER BY created DESC;

-- Expected activities:
-- 1. 'registration' - Admin created LPK
-- 2. 'verification' - LPK verified email
-- 3. 'activation' - LPK set password, account active
```

---

## 📊 Reporting & Analytics

### Registration Statistics

**Query: Count by Status**
```sql
SELECT 
    status,
    COUNT(*) as total,
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM vocational_training_institutions), 2), '%') as percentage
FROM vocational_training_institutions
GROUP BY status
ORDER BY total DESC;

-- Shows:
-- pending_verification: X (XX%)
-- verified: X (XX%)
-- active: X (XX%)
```

**Query: Recent Registrations (Last 7 Days)**
```sql
SELECT 
    id,
    name,
    email,
    status,
    created
FROM vocational_training_institutions
WHERE created >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created DESC;
```

**Query: Incomplete Registrations (Pending > 48 hours)**
```sql
SELECT 
    id,
    name,
    email,
    status,
    created,
    TIMESTAMPDIFF(HOUR, created, NOW()) as hours_pending
FROM vocational_training_institutions
WHERE status = 'pending_verification'
AND created <= DATE_SUB(NOW(), INTERVAL 48 HOUR)
ORDER BY created ASC;

-- These need follow-up
```

### Email Delivery Metrics

**Query: Token Usage Statistics**
```sql
SELECT 
    COUNT(*) as total_tokens,
    SUM(CASE WHEN is_used = 1 THEN 1 ELSE 0 END) as used_tokens,
    SUM(CASE WHEN expires_at < NOW() THEN 1 ELSE 0 END) as expired_tokens,
    SUM(CASE WHEN is_used = 0 AND expires_at >= NOW() THEN 1 ELSE 0 END) as active_tokens
FROM email_verification_tokens
WHERE token_type = 'email_verification';

-- Health metrics for email system
```

---

## 🛠️ Maintenance Tasks

### Daily Tasks

**1. Monitor Pending Registrations**
```bash
# Check registrations pending > 24 hours
mysql -u root -p -e "
SELECT COUNT(*) as pending_count
FROM cms_authentication_authorization.vocational_training_institutions
WHERE status = 'pending_verification'
AND created <= DATE_SUB(NOW(), INTERVAL 24 HOUR);
" | tail -1

# If > 10, investigate email delivery issues
```

**2. Check Error Logs**
```bash
# View recent errors
tail -50 logs/error.log

# Filter email-related errors
grep -i "email\|verification\|token" logs/error.log | tail -20
```

### Weekly Tasks

**1. Clean Up Expired Tokens (Automated)**
```sql
-- Manually trigger cleanup (or let cron do it)
DELETE FROM email_verification_tokens
WHERE expires_at < DATE_SUB(NOW(), INTERVAL 48 HOUR);

-- This runs automatically but can be manual
```

**2. Generate Weekly Report**
```sql
-- Registrations this week
SELECT 
    DATE(created) as date,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as completed
FROM vocational_training_institutions
WHERE created >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created)
ORDER BY date DESC;
```

### Monthly Tasks

**1. Archive Old Activity Logs**
```sql
-- Archive logs older than 6 months to separate table
INSERT INTO stakeholder_activities_archive
SELECT * FROM stakeholder_activities
WHERE created < DATE_SUB(NOW(), INTERVAL 6 MONTH);

DELETE FROM stakeholder_activities
WHERE created < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

**2. Review Inactive LPKs**
```sql
-- LPKs registered but never logged in (> 30 days)
SELECT 
    vti.id,
    vti.name,
    vti.email,
    vti.created,
    u.last_login,
    TIMESTAMPDIFF(DAY, vti.created, NOW()) as days_registered
FROM vocational_training_institutions vti
LEFT JOIN users u ON u.institution_id = vti.id AND u.institution_type = 'vocational_training'
WHERE vti.status = 'active'
AND (u.last_login IS NULL OR u.last_login < DATE_SUB(NOW(), INTERVAL 30 DAY))
AND vti.created < DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY days_registered DESC;

-- Consider sending reminder emails
```

---

## 📞 Support Contact

**For Admin Support:**
- Email: support@asahifamily.id
- Phone: +62 21 8984 4450
- Hours: Monday - Friday, 08:00 - 17:00 WIB

**For Technical Issues:**
- Check error logs: `logs/error.log`
- Check email logs: grep "email" logs/error.log
- Database issues: Contact DBA
- Email service issues: Check Gmail App Password

**Escalation:**
- Critical bugs: Create issue in GitHub
- Security concerns: Contact immediately
- Data loss: Contact DBA for backup restore

---

## 📚 Related Documentation

1. **PHASE_3_4_COMPLETE_SUMMARY.md** - Implementation overview
2. **PHASE_3_4_TESTING_GUIDE.md** - Testing procedures
3. **PHASE_3_4_IMPLEMENTATION_COMPLETE.md** - Technical details
4. **copilot-instructions.md** - System architecture

---

## ✅ Quick Reference Checklist

**Creating New LPK:**
- [ ] Fill all required fields (marked with *)
- [ ] Verify email address is correct
- [ ] Select geographic location (optional but recommended)
- [ ] Submit form
- [ ] Verify success message displayed
- [ ] Check registration list for new entry

**Monitoring Registration:**
- [ ] Check status badge color
- [ ] If yellow (pending), ask LPK to check email
- [ ] If blue (verified), wait for password setup
- [ ] If green (active), registration complete

**Troubleshooting:**
- [ ] Check email address correct
- [ ] Ask LPK to check spam folder
- [ ] Resend verification if needed
- [ ] Check error logs if issues persist
- [ ] Verify email service working

---

**Document Version:** 1.0  
**Last Updated:** December 1, 2025  
**Maintained By:** TMM System Team  
**Contact:** sriechoentjoro@gmail.com
