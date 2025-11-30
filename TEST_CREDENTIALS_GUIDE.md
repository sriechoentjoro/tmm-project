# TMM System - Test Credentials Guide

## 🔐 Login Page Features

### Anti Password Manager Features
- ✅ **Autocomplete OFF** - Form tidak akan menyimpan username/password ke browser
- ✅ **Auto-clear fields** - Field username dan password dibersihkan saat page load
- ✅ **No autofill** - Mencegah browser autofill password lama
- ✅ **Fresh session** - Setiap kali buka login page, form kosong

### Quick Login Features
- ✅ **Click to fill** - Klik pada credential card untuk auto-fill username dan password
- ✅ **Visual feedback** - Field berubah hijau saat di-fill
- ✅ **Scrollable list** - Daftar credentials dengan scroll untuk banyak user

---

## 👥 Test Credentials

### 1. Administrator (Full Access)
- **Username:** `admin`
- **Password:** `admin123`
- **Role:** Administrator
- **Access:** Full system access, all modules
- **Color Badge:** Red (ADMIN)

### 2. Management Level

#### Director
- **Username:** `director`
- **Password:** `director123`
- **Role:** Management
- **Access:** Management reports, approval workflows
- **Color Badge:** Primary Blue (MGT)

#### General Manager
- **Username:** `manager`
- **Password:** `manager123`
- **Role:** Management
- **Access:** Management reports, approval workflows
- **Color Badge:** Primary Blue (MGT)

### 3. TMM Recruitment Department

#### Recruitment Officer 1
- **Username:** `recruitment1`
- **Password:** `recruit123`
- **Role:** TMM Recruitment
- **Access:** Candidate management, recruitment process
- **Color Badge:** Green (RECRUIT)

#### Recruitment Officer 2
- **Username:** `recruitment2`
- **Password:** `recruit123`
- **Role:** TMM Recruitment
- **Access:** Candidate management, recruitment process
- **Color Badge:** Green (RECRUIT)

### 4. TMM Training Department

#### Training Coordinator 1
- **Username:** `training1`
- **Password:** `training123`
- **Role:** TMM Training
- **Access:** Trainee management, training schedules
- **Color Badge:** Warning Yellow (TRAINING)

#### Training Coordinator 2
- **Username:** `training2`
- **Password:** `training123`
- **Role:** TMM Training
- **Access:** Trainee management, training schedules
- **Color Badge:** Warning Yellow (TRAINING)

### 5. TMM Documentation Department

#### Documentation Officer 1
- **Username:** `documentation1`
- **Password:** `doc123`
- **Role:** TMM Documentation
- **Access:** Document management, archiving
- **Color Badge:** Info Blue (DOC)

#### Documentation Officer 2
- **Username:** `documentation2`
- **Password:** `doc123`
- **Role:** TMM Documentation
- **Access:** Document management, archiving
- **Color Badge:** Info Blue (DOC)

### 6. LPK Penyangga (Vocational Training Institutions)

#### LPK Semarang
- **Username:** `lpk_semarang`
- **Password:** `lpk123`
- **Role:** LPK Penyangga
- **Access:** Candidate documents, training materials
- **Color Badge:** Secondary Gray (LPK)

#### LPK Bekasi
- **Username:** `lpk_bekasi`
- **Password:** `lpk123`
- **Role:** LPK Penyangga
- **Access:** Candidate documents, training materials
- **Color Badge:** Secondary Gray (LPK)

#### LPK Makassar
- **Username:** `lpk_makasar`
- **Password:** `lpk123`
- **Role:** LPK Penyangga

#### LPK Medan
- **Username:** `lpk_medan`
- **Password:** `lpk123`
- **Role:** LPK Penyangga

#### LPK Padang
- **Username:** `lpk_padang`
- **Password:** `lpk123`
- **Role:** LPK Penyangga

---

## 🚀 How to Use

### Method 1: Click to Auto-Fill (RECOMMENDED)
1. Buka login page: `http://localhost/tmm/users/login`
2. Lihat daftar credentials di sebelah kanan
3. **Klik pada credential card** yang ingin digunakan
4. Username dan password akan otomatis ter-isi
5. Klik tombol "Login"

### Method 2: Manual Input
1. Buka login page
2. Ketik username secara manual
3. Ketik password secara manual
4. Klik tombol "Login"

---

## 🔧 Technical Details

### Password Hashing
- **Algorithm:** bcrypt (CakePHP DefaultPasswordHasher)
- **Cost Factor:** 10
- **Example Hash:** `$2y$10$cka06dLU8frl88JDTPaHZug1RT9KiB9N54hxRnPvvUssJL1mSXJoG`

### Form Security Features
```html
<!-- Form attributes -->
autocomplete="off"          // Prevent form-level autocomplete
autocomplete="new-password" // Prevent password field autofill
```

```javascript
// JavaScript preventions
- Clear fields on page load
- Clear fields on browser back button
- Disable autofill with timeout
- Remove autocomplete attributes dynamically
```

### Database Schema
```sql
Table: system_authentication_authorization.users
- id (PK)
- username (unique)
- email (unique)
- password (bcrypt hash)
- full_name
- is_active (1 = active, 0 = inactive)
- institution_id
- institution_type
```

---

## 🛠️ Setup Commands

### Generate New Password Hashes
```bash
php generate_test_passwords.php
```

### Reset All Test Passwords
```bash
Get-Content setup_test_credentials.sql | mysql -u root -p62xe6zyr
```

### Verify Active Users
```sql
USE system_authentication_authorization;
SELECT u.username, u.full_name, u.is_active, r.name as role_name
FROM users u
LEFT JOIN user_roles ur ON u.id = ur.user_id
LEFT JOIN roles r ON ur.role_id = r.id
WHERE u.is_active = 1
ORDER BY u.id;
```

---

## 📁 Related Files

- **Login Template:** `src/Template/Users/login.ctp`
- **Password Generator:** `generate_test_passwords.php`
- **SQL Setup Script:** `setup_test_credentials.sql`
- **Users Controller:** `src/Controller/UsersController.php`
- **Users Model:** `src/Model/Table/UsersTable.php`

---

## 🎨 UI/UX Features

### Credential Card Styling
- **Hover Effect:** Card elevates with shadow
- **Color Coding:** Each role has unique badge color
- **Icons:** Font Awesome icons for visual identification
- **Responsive:** Works on mobile and desktop
- **Scrollable:** List scrolls if many users

### Visual Feedback
- **Green Border:** Fields turn green when auto-filled
- **Badge Colors:**
  - 🔴 Red (ADMIN) - Administrator
  - 🔵 Blue (MGT) - Management
  - 🟢 Green (RECRUIT) - Recruitment
  - 🟡 Yellow (TRAINING) - Training
  - 🔵 Info (DOC) - Documentation
  - ⚪ Gray (LPK) - Vocational Training

---

## 🔒 Security Notes

⚠️ **IMPORTANT - Test Environment Only**
- These credentials are for **TESTING ONLY**
- **DO NOT use in production** with these simple passwords
- All passwords are stored in **bcrypt hashed format**
- Change passwords immediately in production environment
- Enable HTTPS for production login

---

## 📝 Testing Checklist

- [ ] Login page loads without autofill
- [ ] Username field is empty on page load
- [ ] Password field is empty on page load
- [ ] Click credential card auto-fills both fields
- [ ] Visual feedback (green border) appears
- [ ] Login works with each test user
- [ ] Role-based access works correctly
- [ ] Browser does NOT save passwords
- [ ] Browser does NOT suggest old passwords
- [ ] Form clears on browser back button

---

## 🆘 Troubleshooting

### Problem: Browser still shows old passwords
**Solution:**
1. Clear browser password cache
2. Hard refresh page (Ctrl+F5)
3. Open in incognito/private window
4. Check browser password manager settings

### Problem: Auto-fill not working
**Solution:**
1. Verify JavaScript is enabled
2. Check browser console for errors
3. Ensure jQuery is loaded
4. Try different browser

### Problem: Login fails with correct credentials
**Solution:**
1. Check `is_active = 1` in database
2. Verify password hash in database
3. Run `setup_test_credentials.sql` again
4. Check error logs: `logs/error.log`

---

**Last Updated:** November 28, 2025  
**Generated By:** GitHub Copilot
