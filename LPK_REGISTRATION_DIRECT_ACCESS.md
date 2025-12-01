# LPK Registration - Direct Access URLs

## Error: "Could not load content"

**Cause**: Menu items not yet added to database. The `add_lpk_registration_menu.sql` script hasn't been executed yet.

---

## ✅ IMMEDIATE SOLUTION: Use Direct URLs

You can access the LPK Registration pages **right now** without the menu:

### Direct URLs (Working Now):

```
http://localhost/tmm/admin/lpk-registration/create
- Create new LPK registration (Step 1 of wizard)

http://localhost/tmm/admin/lpk-registration
- View all LPK registrations (list page)
```

These URLs work because:
- ✅ Controller exists: `LpkRegistrationController.php`
- ✅ Routes configured: Admin prefix with fallbacks
- ✅ Templates exist: `create.ctp` and `index.ctp`
- ✅ Cache cleared
- ❌ Menu items: Not yet in database (causing the menu error)

---

## 🔧 PERMANENT FIX: Add Menu Items

**Execute this in phpMyAdmin** (5 minutes):

### Step-by-Step:

1. **Open phpMyAdmin**: `http://localhost/phpmyadmin`

2. **Select Database**: Click `cms_masters` in left sidebar

3. **Open SQL Tab**: Click "SQL" in top menu

4. **Copy SQL File**:
   - Open: `d:\xampp\htdocs\tmm\add_lpk_registration_menu.sql`
   - Select all: `Ctrl+A`
   - Copy: `Ctrl+C`

5. **Execute SQL**:
   - Paste in SQL editor
   - Click "Go" button
   - Wait for completion

6. **Expected Result**:
   ```
   Query OK, 1 row affected (for Admin menu - if not exists)
   Query OK, 1 row affected (for LPK Registration parent)
   Query OK, 4 rows affected (for LPK Registration submenu items)
   Query OK, 1 row affected (for Email Tokens parent)
   Query OK, 3 rows affected (for Email Tokens submenu items)
   ```

7. **Verify**:
   - In phpMyAdmin, browse `menus` table
   - Search for "LPK Registration"
   - Should see 10 new menu items

8. **Clear Browser Cache**:
   - Press `Ctrl+Shift+Del`
   - Clear cached images and files
   - Close and reopen browser

9. **Reload Application**:
   - Visit: `http://localhost/tmm/`
   - Login as admin
   - Menu should now show "Admin" tab with "LPK Registration" submenu

---

## 📊 What Menu SQL Does

The `add_lpk_registration_menu.sql` creates this structure:

```
● Admin (parent menu)
  ├─ LPK Registration (submenu)
  │   ├─ View Registrations → /admin/lpk-registration
  │   ├─ Register New LPK → /admin/lpk-registration/create
  │   ├─ Registration Reports → /admin/lpk-registration/reports
  │   └─ Help & Documentation → /admin/lpk-registration/help
  └─ Email Verification Tokens (submenu)
      ├─ View All Tokens → /admin/email-verification-tokens
      ├─ Token Statistics → /admin/email-verification-tokens/stats
      └─ Cleanup Expired → /admin/email-verification-tokens/cleanup
```

Total: 10 menu items (1 parent + 2 submenus + 7 items)

---

## 🧪 Testing Without Menu (Right Now)

You can test the complete workflow immediately using direct URLs:

### Test Flow:

1. **Create LPK** (Admin Step):
   ```
   URL: http://localhost/tmm/admin/lpk-registration/create
   
   Fill form:
   - Institution Name: Test LPK Local
   - Registration Number: TEST-001
   - Director Name: Test Director
   - Email: [your-email]@gmail.com
   - Phone: 081234567890
   - Address: Test Address
   - Province: DKI Jakarta
   - City: Jakarta Pusat
   
   Submit → Email sent
   ```

2. **Verify Email** (LPK Step):
   ```
   Check email inbox
   Click verification link
   Format: http://localhost/tmm/lpk-registration/verify-email/[TOKEN]
   
   Result: Email verified, redirect to password setup
   ```

3. **Set Password** (LPK Step):
   ```
   Auto-redirected to: http://localhost/tmm/lpk-registration/set-password/[TOKEN]
   
   Enter password: TestLocal2025!
   Confirm password: TestLocal2025!
   Check: I agree to terms
   
   Submit → Account activated
   ```

4. **Login** (LPK Step):
   ```
   URL: http://localhost/tmm/users/login
   Username: test_lpk_local
   Password: TestLocal2025!
   
   Result: Login successful
   ```

---

## 🎯 Summary

**Current Status**:
- ✅ Controller: Working
- ✅ Routes: Configured
- ✅ Templates: Exist
- ✅ Database table: `email_verification_tokens` created
- ❌ Menu items: Not yet added

**To Fix Menu Error**:
1. Execute `add_lpk_registration_menu.sql` in phpMyAdmin (cms_masters database)
2. Clear browser cache
3. Refresh application

**Workaround (Works Now)**:
- Use direct URL: `http://localhost/tmm/admin/lpk-registration/create`
- Bypass menu entirely

---

**Last Updated**: December 1, 2025  
**Error**: "Could not load content" when clicking menu  
**Root Cause**: Menu SQL not executed  
**Quick Fix**: Use direct URL above
