# Quick Reference: Complete Task 1 & 2

## Current Status
- ✅ Task 3 (Docs): COMPLETE - 850 lines admin guide
- ✅ Task 4 (Menu): COMPLETE - 10 menu items SQL ready
- ❌ Task 1 (Test): BLOCKED - Need to execute 2 SQL files
- ⏳ Task 2 (Deploy): WAITING - For local testing

## 🚀 UNBLOCK IN 5 MINUTES

### Step 1: Execute SQL via phpMyAdmin

```
1. Open: http://localhost/phpmyadmin
2. Login (usually no password in XAMPP)
3. Click: cms_authentication_authorization (left sidebar)
4. Click: SQL tab (top menu)
```

**Execute Migration 1:**
```
5. Open file: phase_3_4_simple_migration.sql
6. Copy all (Ctrl+A, Ctrl+C)
7. Paste in SQL editor
8. Click: Go
9. Success: "1 row affected" or "Table created"
```

**Execute Migration 2:**
```
10. Open file: add_lpk_registration_menu.sql
11. Copy all (Ctrl+A, Ctrl+C)
12. Paste in SQL editor
13. Click: Go
14. Success: Multiple "1 row inserted" messages
```

**Verify:**
```
15. Left sidebar: Click email_verification_tokens table
16. Should see: 8 columns (id, user_email, token, expires_at...)
17. Left sidebar: Click menus table
18. Click: Browse tab
19. Search: "LPK Registration" - should appear
```

### Step 2: Run Automated Tests

```powershell
# Clear cache
Remove-Item tmp\cache\models\* -Force -Recurse
Remove-Item tmp\cache\persistent\* -Force -Recurse

# Run tests
powershell -File test_lpk_registration_simple.ps1

# Expected: 6/6 tests pass
```

### Step 3: Manual Test (30 minutes)

```
URL: http://localhost/tmm/admin/lpk-registration/create

Test Flow:
1. Fill form with test LPK data
2. Use YOUR real email address
3. Submit → Check email inbox
4. Click "Verify Email" in email
5. Set password (8+ chars, mixed case, number, symbol)
6. Login with new credentials
7. Verify: Can access system

Database Check (phpMyAdmin):
SELECT * FROM vocational_training_institutions ORDER BY id DESC LIMIT 1;
SELECT * FROM email_verification_tokens ORDER BY id DESC LIMIT 1;
SELECT * FROM users ORDER BY id DESC LIMIT 1;
-- All should show your test record
```

### Step 4: Deploy to Production (45 minutes)

```bash
# Connect
ssh root@103.214.112.58

# Backup (15 min)
cd /var/www/tmm
mysqldump -u root -p cms_authentication_authorization > backups/phase3_4_$(date +%Y%m%d).sql

# Deploy code (5 min)
git pull origin main

# Execute migrations (5 min)
mysql -u root -p cms_authentication_authorization < phase_3_4_simple_migration.sql
mysql -u root -p cms_authentication_authorization < add_lpk_registration_menu.sql

# Clear cache (2 min)
rm -rf tmp/cache/*

# Restart (2 min)
systemctl restart php7.4-fpm
systemctl reload nginx

# Test (20 min)
curl -I https://asahifamily.id/tmm/admin/lpk-registration
# Then test registration workflow in browser

# Monitor (24 hours)
tail -f /var/www/tmm/logs/error.log
```

## 📊 Success Checklist

### Local Testing Complete When:
- [ ] Both SQL migrations executed in phpMyAdmin
- [ ] Automated tests: 6/6 passing
- [ ] Manual test completed (email received, login works)
- [ ] Database has test records (LPK, token, user)
- [ ] Menu visible in browser (Admin > LPK Registration)
- [ ] No errors in `logs/error.log`

### Production Deploy Complete When:
- [ ] Database backed up
- [ ] Code deployed (git pull)
- [ ] Both migrations executed
- [ ] Cache cleared
- [ ] Services restarted
- [ ] Test registration successful
- [ ] Menu visible on production
- [ ] Monitoring active (24 hours)

## 🆘 Troubleshooting

**phpMyAdmin won't open:**
- Check XAMPP Apache is running
- Try: http://127.0.0.1/phpmyadmin

**SQL error "Table already exists":**
- Skip to next SQL (table already created)

**Tests fail - "Table doesn't exist":**
- Re-check phpMyAdmin: Did migration really execute?
- Look for green "Success" message

**Email not received:**
- Check spam folder
- Verify SMTP settings in `config/app.php`
- Check `logs/error.log` for email errors

**Menu not visible:**
- Clear browser cache (Ctrl+Shift+Del)
- Clear CakePHP cache: `rm -rf tmp/cache/*`
- Refresh page (Ctrl+F5)

## 📚 Documentation

- **Detailed Status**: `DEPLOYMENT_STATUS_PHASE_3_4.md`
- **Testing Guide**: `PHASE_3_4_TESTING_GUIDE.md`
- **Deployment Guide**: `PRODUCTION_DEPLOYMENT_GUIDE.md`
- **Admin Guide**: `ADMIN_GUIDE_LPK_REGISTRATION.md`
- **Menu Guide**: `MENU_UPDATE_GUIDE.md`

## 🎯 Your Next Command

```powershell
# After completing phpMyAdmin SQL execution:
powershell -File test_lpk_registration_simple.ps1
```

---

**Total Time Estimate:**
- SQL execution (phpMyAdmin): 5 minutes
- Automated tests: 5 minutes
- Manual testing: 30 minutes
- Production deployment: 45 minutes
- **Total: ~90 minutes** to complete all tasks

**Current Blocker:** Execute 2 SQL files via phpMyAdmin (5 min task)
