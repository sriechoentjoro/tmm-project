# Quick Reference - Test Credentials

## 🚀 Quick Login Access

### How to Use:
1. Go to: `http://localhost/tmm/users/login`
2. **Click** any credential card to auto-fill
3. Click **Login** button

---

## 👥 Test Users (Quick Copy)

```
Administrator:
Username: admin
Password: admin123

Director:
Username: director
Password: director123

Manager:
Username: manager
Password: manager123

Recruitment:
Username: recruitment1
Password: recruit123

Training:
Username: training1
Password: training123

Documentation:
Username: documentation1
Password: doc123

LPK Semarang:
Username: lpk_semarang
Password: lpk123

LPK Bekasi:
Username: lpk_bekasi
Password: lpk123
```

---

## 🔧 Developer Commands

### Reset All Passwords:
```powershell
Get-Content setup_test_credentials.sql | mysql -u root -p62xe6zyr
```

### Generate New Password Hash:
```php
php -r "require 'vendor/autoload.php'; echo (new Cake\Auth\DefaultPasswordHasher())->hash('newpassword');"
```

### Clear Cache:
```bash
bin\cake cache clear_all
```

### Check Active Users:
```sql
SELECT u.username, u.full_name, u.is_active, r.name 
FROM system_authentication_authorization.users u
LEFT JOIN system_authentication_authorization.user_roles ur ON u.id = ur.user_id
LEFT JOIN system_authentication_authorization.roles r ON ur.role_id = r.id
WHERE u.is_active = 1;
```

---

## 📋 Role Access Matrix

| Role | Candidates | Trainees | Apprentices | Documents | Reports | Admin |
|------|-----------|----------|-------------|-----------|---------|-------|
| **Administrator** | ✅ Full | ✅ Full | ✅ Full | ✅ Full | ✅ Full | ✅ Yes |
| **Management** | ✅ View | ✅ View | ✅ View | ✅ View | ✅ Full | ❌ No |
| **Recruitment** | ✅ Full | ❌ No | ❌ No | ✅ View | ✅ Dept | ❌ No |
| **Training** | ✅ View | ✅ Full | ❌ No | ✅ View | ✅ Dept | ❌ No |
| **Documentation** | ✅ View | ✅ View | ✅ View | ✅ Full | ✅ View | ❌ No |
| **LPK** | ✅ View | ❌ No | ❌ No | ✅ Own | ❌ No | ❌ No |

---

## 🎯 Testing Scenarios

### Scenario 1: Admin Full Access
```
Login as: admin/admin123
Test: Access all modules
Expected: No restrictions
```

### Scenario 2: Department Specific
```
Login as: recruitment1/recruit123
Test: Access candidate module
Expected: Full CRUD for candidates
```

### Scenario 3: Read-Only Access
```
Login as: director/director123
Test: View reports, no delete
Expected: View only, no delete buttons
```

### Scenario 4: Institution Scope
```
Login as: lpk_semarang/lpk123
Test: View only own institution data
Expected: Filtered by institution_id
```

---

## 🐛 Troubleshooting

### Login Failed?
```bash
# Check user is active
mysql -u root -p62xe6zyr -e "SELECT username, is_active FROM system_authentication_authorization.users WHERE username='admin';"

# Reset password
mysql -u root -p62xe6zyr -e "UPDATE system_authentication_authorization.users SET password='$2y$10$cka06dLU8frl88JDTPaHZug1RT9KiB9N54hxRnPvvUssJL1mSXJoG' WHERE username='admin';"
```

### Browser Still Saves Password?
```
1. Clear browser cache (Ctrl+Shift+Del)
2. Open in incognito mode
3. Check browser settings → Passwords → Remove saved
```

### Auto-fill Not Working?
```
1. Check JavaScript console (F12)
2. Verify jQuery loaded
3. Hard refresh (Ctrl+F5)
```

---

## 📱 Mobile Testing

```
Device: iPhone/Android
URL: http://localhost/tmm/users/login
Layout: Stacked (form top, credentials bottom)
Touch: Tap credential card to fill
```

---

## 🔗 Related Files

- Login Template: `src/Template/Users/login.ctp`
- Controller: `src/Controller/UsersController.php`
- Model: `src/Model/Table/UsersTable.php`
- SQL Setup: `setup_test_credentials.sql`
- Full Guide: `TEST_CREDENTIALS_GUIDE.md`

---

**Print This Page for Quick Reference During Testing**
