# Production Deployment Guide: Phase 3-4 LPK Registration

## 📋 Pre-Deployment Checklist

**Before you begin:**
- [ ] All code tested locally
- [ ] Database backup completed
- [ ] Production server access verified
- [ ] Email service configured and tested
- [ ] Admin team notified of deployment

**Estimated Time:** 30-45 minutes  
**Downtime Required:** None (zero-downtime deployment)  
**Rollback Time:** 10 minutes

---

## 🚀 Deployment Steps

### STEP 1: Backup Production Database (5 minutes)

**Connect to production server:**
```bash
ssh root@103.214.112.58
```

**Create backup directory:**
```bash
cd /var/www/tmm
mkdir -p backups/phase_3_4
cd backups/phase_3_4
```

**Backup cms_authentication_authorization database:**
```bash
mysqldump -u root -p cms_authentication_authorization > cms_authentication_authorization_before_phase3_4_$(date +%Y%m%d_%H%M%S).sql

# Verify backup created
ls -lh *.sql

# Expected output:
# -rw-r--r-- 1 root root 2.5M Dec 01 10:30 cms_authentication_authorization_before_phase3_4_20251201_103045.sql
```

**Test backup integrity:**
```bash
# Should show table count without errors
mysql -u root -p cms_authentication_authorization < cms_authentication_authorization_before_phase3_4_*.sql --dry-run 2>&1 | head -20
```

---

### STEP 2: Upload Migration File (5 minutes)

**On your local machine (Windows PowerShell):**
```powershell
# Navigate to project directory
cd d:\xampp\htdocs\tmm

# Upload migration file to production
Get-Content phase_3_4_simple_migration.sql -Raw | ssh root@103.214.112.58 'cat > /var/www/tmm/phase_3_4_simple_migration.sql'

# Verify upload
ssh root@103.214.112.58 "wc -l /var/www/tmm/phase_3_4_simple_migration.sql"
# Expected: ~50 lines
```

---

### STEP 3: Execute Migration (5 minutes)

**On production server:**
```bash
cd /var/www/tmm

# Execute migration
mysql -u root -p cms_authentication_authorization < phase_3_4_simple_migration.sql

# Verify table created
mysql -u root -p -e "DESCRIBE email_verification_tokens;" cms_authentication_authorization

# Expected output:
# +----------------+--------------+------+-----+---------+----------------+
# | Field          | Type         | Null | Key | Default | Extra          |
# +----------------+--------------+------+-----+---------+----------------+
# | id             | int(11)      | NO   | PRI | NULL    | auto_increment |
# | token          | varchar(255) | NO   | UNI | NULL    |                |
# | token_type     | varchar(50)  | NO   | MUL | NULL    |                |
# | user_email     | varchar(255) | NO   | MUL | NULL    |                |
# | user_id        | int(11)      | YES  | MUL | NULL    |                |
# | expires_at     | datetime     | NO   | MUL | NULL    |                |
# | is_used        | tinyint(1)   | YES  |     | 0       |                |
# | used_at        | datetime     | YES  |     | NULL    |                |
# | created        | datetime     | YES  |     | NULL    |                |
# +----------------+--------------+------+-----+---------+----------------+

# Check indexes
mysql -u root -p -e "SHOW INDEX FROM email_verification_tokens;" cms_authentication_authorization

# Expected: 5 indexes (PRIMARY, token, idx_token_type, idx_user_email, idx_expires_at)
```

**If migration fails:**
```bash
# Check error message
tail -50 /var/www/tmm/logs/error.log

# Common issue: Table already exists
# Solution: Skip migration if table exists
mysql -u root -p -e "DROP TABLE IF EXISTS email_verification_tokens;" cms_authentication_authorization
mysql -u root -p cms_authentication_authorization < phase_3_4_simple_migration.sql
```

---

### STEP 4: Deploy Code (10 minutes)

**On production server:**
```bash
cd /var/www/tmm

# Pull latest code from GitHub
git fetch origin
git status

# Verify current branch
git branch
# Should show: * main

# Pull Phase 3-4 code
git pull origin main

# Expected output:
# Updating [commit]...[commit]
# Fast-forward
#  src/Model/Table/EmailVerificationTokensTable.php  | 437 +++++++++++++++++++
#  src/Model/Entity/EmailVerificationToken.php       |  85 ++++
#  src/Controller/Admin/LpkRegistrationController.php| 512 ++++++++++++++++++++
#  src/Template/Admin/LpkRegistration/create.ctp     | 470 ++++++++++++++++++
#  src/Template/Admin/LpkRegistration/index.ctp      | 280 +++++++++++
#  src/Template/LpkRegistration/verify_email.ctp     | 250 ++++++++++
#  src/Template/LpkRegistration/set_password.ctp     | 385 +++++++++++++++
#  src/Template/Email/html/lpk_verification.ctp      | 200 ++++++++
#  src/Template/Email/text/lpk_verification.ctp      | 100 ++++
#  src/Template/Email/html/lpk_welcome.ctp           | 250 ++++++++++
#  src/Template/Email/text/lpk_welcome.ctp           | 150 ++++++
#  12 files changed, 3119 insertions(+)
```

**Verify files deployed:**
```bash
# Check critical files exist
ls -la src/Controller/Admin/LpkRegistrationController.php
ls -la src/Model/Table/EmailVerificationTokensTable.php
ls -la src/Template/Admin/LpkRegistration/create.ctp
ls -la src/Template/LpkRegistration/verify_email.ctp
ls -la src/Template/Email/html/lpk_verification.ctp

# All should show file size > 0 bytes
```

---

### STEP 5: Set Permissions (2 minutes)

**On production server:**
```bash
cd /var/www/tmm

# Set ownership
chown -R www-data:www-data tmp/ logs/

# Set permissions
chmod -R 775 tmp/cache/
chmod -R 775 logs/

# Verify
ls -la tmp/cache/ | head -5
ls -la logs/ | head -5
```

---

### STEP 6: Clear Cache (2 minutes)

**On production server:**
```bash
cd /var/www/tmm

# Clear all cache
rm -rf tmp/cache/models/*
rm -rf tmp/cache/persistent/*
rm -rf tmp/cache/views/*

# Verify cache cleared
ls -la tmp/cache/models/
# Should show: total 0 (empty directory)

ls -la tmp/cache/persistent/
# Should show: total 0 (empty directory)
```

---

### STEP 7: Restart Services (2 minutes)

**On production server:**
```bash
# Restart PHP-FPM (clears opcache)
systemctl restart php7.4-fpm

# Verify PHP-FPM running
systemctl status php7.4-fpm
# Should show: active (running)

# Reload Nginx (no downtime)
systemctl reload nginx

# Verify Nginx running
systemctl status nginx
# Should show: active (running)
```

---

### STEP 8: Verify Deployment (5 minutes)

#### Test 1: Check Page Loads
```bash
# Test registration page loads (returns 200 OK)
curl -I https://asahifamily.id/tmm/admin/lpk-registration/create

# Expected output:
# HTTP/1.1 200 OK
# Content-Type: text/html; charset=UTF-8
```

#### Test 2: Check Database Connection
```bash
cd /var/www/tmm

# Test database query
mysql -u root -p -e "SELECT COUNT(*) as count FROM email_verification_tokens;" cms_authentication_authorization

# Expected: 0 (table exists, no records yet)
```

#### Test 3: Check Error Logs
```bash
# Check for errors in last 50 lines
tail -50 /var/www/tmm/logs/error.log

# Should NOT show:
# - "Table doesn't exist"
# - "Fatal error"
# - "Syntax error"
# - "Class not found"

# May show warnings (OK):
# - "Deprecated" warnings (PHP 7.4)
# - "Notice" messages (safe to ignore)
```

#### Test 4: Access Web Interface
**In browser, navigate to:**
```
https://asahifamily.id/tmm/admin/lpk-registration
```

**Expected:**
- Page loads without errors
- Table displays with columns: ID, Institution Name, Registration Number, etc.
- "Register New LPK" button visible
- No "500 Internal Server Error"

**Click "Register New LPK":**
```
https://asahifamily.id/tmm/admin/lpk-registration/create
```

**Expected:**
- Form displays all fields
- Province dropdown populated
- Required field indicators (*) visible
- Submit button enabled

---

### STEP 9: Test Registration Workflow (10 minutes)

**Create Test LPK Registration:**

1. **Fill form:**
   - Institution Name: `TEST LPK PRODUCTION`
   - Registration Number: `PROD-TEST-001`
   - Director Name: `Test Director`
   - Email: Use a real email you can access (e.g., your Gmail)
   - Phone: `081234567890`
   - Address: `Jl. Test Production No. 123`
   - Province: Select any
   - City: Select any

2. **Submit form**
   - Expected: Success message "LPK registered successfully. Verification email sent to {email}"
   - Redirects to: `/admin/lpk-registration`
   - New record visible in list with status: **Pending Verification** (yellow badge)

3. **Check email inbox:**
   - Subject: "Verify Your Email - TMM System Registration"
   - From: sriechoentjoro@gmail.com
   - Body: Professional purple gradient design
   - Button: "VERIFY EMAIL ADDRESS"

4. **Click verification link:**
   - Expected: Success page with green checkmark
   - Message: "Email verified successfully!"
   - Countdown timer: 5 seconds
   - Auto-redirects to password setup page

5. **Set password:**
   - Password: `TestProd2025!`
   - Confirm: `TestProd2025!`
   - Check "I agree to Terms & Conditions"
   - Click "Set Password & Activate Account"
   - Expected: Success message, welcome email sent

6. **Check welcome email:**
   - Subject: "Welcome to TMM System - Account Activated"
   - From: sriechoentjoro@gmail.com
   - Body: Green gradient design with login credentials
   - Username shown: `test_lpk_production`

7. **Test login:**
   ```
   Navigate to: https://asahifamily.id/tmm/users/login
   Username: test_lpk_production
   Password: TestProd2025!
   ```
   - Expected: Login successful
   - Redirects to dashboard
   - Can access LPK features

**Database Verification:**
```bash
# Check LPK status
mysql -u root -p -e "SELECT id, name, email, status, email_verified_at FROM vocational_training_institutions WHERE name='TEST LPK PRODUCTION';" cms_authentication_authorization

# Expected:
# status = 'active'
# email_verified_at = (current timestamp)

# Check user created
mysql -u root -p -e "SELECT username, email, status, is_active, institution_type FROM users WHERE email='your_test_email@example.com';" cms_authentication_authorization

# Expected:
# username = 'test_lpk_production'
# status = 'active'
# is_active = 1
# institution_type = 'vocational_training'

# Check token used
mysql -u root -p -e "SELECT token, is_used, used_at FROM email_verification_tokens WHERE user_email='your_test_email@example.com';" cms_authentication_authorization

# Expected:
# is_used = 1
# used_at = (timestamp)
```

---

### STEP 10: Monitor for 24 Hours

**Set up monitoring:**
```bash
# Watch error logs live
tail -f /var/www/tmm/logs/error.log

# In another terminal, watch activity
watch -n 60 'mysql -u root -p -e "SELECT COUNT(*) as total_lpks, SUM(CASE WHEN status=\"active\" THEN 1 ELSE 0 END) as active FROM vocational_training_institutions;" cms_authentication_authorization'
```

**Schedule periodic checks:**
```bash
# Create cron job to check for stuck registrations
crontab -e

# Add line:
0 */6 * * * mysql -u root -p -e "SELECT COUNT(*) FROM vocational_training_institutions WHERE status='pending_verification' AND created <= DATE_SUB(NOW(), INTERVAL 48 HOUR);" cms_authentication_authorization >> /var/log/lpk_stuck_registrations.log
```

---

## 🔄 Rollback Procedure (if needed)

**If deployment fails or critical bugs found:**

### Quick Rollback (10 minutes)

```bash
cd /var/www/tmm

# Step 1: Restore database
mysql -u root -p cms_authentication_authorization < backups/phase_3_4/cms_authentication_authorization_before_phase3_4_*.sql

# Step 2: Revert code
git log --oneline | head -20  # Find commit before Phase 3-4
git revert [commit_hash_of_phase3_4]  # Or git reset --hard [previous_commit]

# Step 3: Clear cache
rm -rf tmp/cache/*

# Step 4: Restart services
systemctl restart php7.4-fpm
systemctl reload nginx

# Step 5: Verify rollback
curl -I https://asahifamily.id/tmm/admin/lpk-registration
# Should show: 404 Not Found (feature removed)

# Step 6: Notify team
echo "Phase 3-4 rolled back at $(date)" >> /var/log/deployment_rollbacks.log
```

---

## ✅ Post-Deployment Checklist

**Immediately after deployment:**
- [ ] Registration page loads without errors
- [ ] Test registration completes successfully
- [ ] Verification email delivered
- [ ] Password setup works
- [ ] Login successful
- [ ] No errors in logs
- [ ] Database records created correctly

**Within 24 hours:**
- [ ] No critical bugs reported
- [ ] Email delivery working consistently
- [ ] Performance acceptable (page load < 2 seconds)
- [ ] No database errors
- [ ] Admin team trained on new feature
- [ ] Documentation distributed

**Within 1 week:**
- [ ] First real LPK registration completed
- [ ] Token cleanup cron job working
- [ ] Activity logs reviewed
- [ ] User feedback collected
- [ ] Performance metrics collected

---

## 📞 Support & Troubleshooting

### Common Issues

**Issue 1: Email not delivered**
```bash
# Check SMTP logs
grep -i "email\|smtp" /var/www/tmm/logs/error.log | tail -20

# Test email manually
cd /var/www/tmm
php -r "require 'vendor/autoload.php'; \$email = new \Cake\Mailer\Email('default'); \$email->from('sriechoentjoro@gmail.com')->to('test@example.com')->subject('Test')->send('Test');"

# Check Gmail account limits (500 emails/day)
```

**Issue 2: Token not found**
```bash
# Check token in database
mysql -u root -p -e "SELECT * FROM email_verification_tokens ORDER BY created DESC LIMIT 5;" cms_authentication_authorization

# Common cause: Token expired (24 hours)
# Solution: Resend verification email from admin panel
```

**Issue 3: Password setup page not loading**
```bash
# Check user status
mysql -u root -p -e "SELECT id, name, status FROM vocational_training_institutions WHERE email='lpk@example.com';" cms_authentication_authorization

# Should be: status = 'verified'
# If status = 'pending_verification', email not verified yet
```

### Emergency Contacts

**For deployment issues:**
- Tech Lead: sriechoentjoro@gmail.com
- Phone: +62 21 8984 4450
- GitHub Issues: https://github.com/sriechoentjoro/tmm-project/issues

**For database issues:**
- DBA Team: Contact server admin
- Backup location: `/var/www/tmm/backups/phase_3_4/`

---

## 📊 Deployment Metrics

**Track these metrics:**
```sql
-- Total registrations
SELECT COUNT(*) as total FROM vocational_training_institutions;

-- By status
SELECT 
    status,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM vocational_training_institutions), 2) as percentage
FROM vocational_training_institutions
GROUP BY status;

-- Average time to complete registration
SELECT 
    AVG(TIMESTAMPDIFF(MINUTE, created, email_verified_at)) as avg_verification_minutes,
    AVG(TIMESTAMPDIFF(MINUTE, email_verified_at, modified)) as avg_activation_minutes
FROM vocational_training_institutions
WHERE status = 'active'
AND email_verified_at IS NOT NULL;

-- Token usage rate
SELECT 
    COUNT(*) as total_tokens,
    SUM(CASE WHEN is_used = 1 THEN 1 ELSE 0 END) as used_tokens,
    ROUND(SUM(CASE WHEN is_used = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as usage_rate
FROM email_verification_tokens;
```

---

## 📚 Related Documents

- **ADMIN_GUIDE_LPK_REGISTRATION.md** - Admin user guide
- **PHASE_3_4_TESTING_GUIDE.md** - Complete testing procedures
- **PHASE_3_4_COMPLETE_SUMMARY.md** - Implementation summary
- **copilot-instructions.md** - Production deployment section

---

**Document Version:** 1.0  
**Last Updated:** December 1, 2025  
**Deployment Date:** [TO BE FILLED]  
**Deployed By:** [TO BE FILLED]  
**Rollback Plan:** Included in this document  
**Backup Location:** `/var/www/tmm/backups/phase_3_4/`  
**Estimated Downtime:** 0 minutes (zero-downtime deployment)
