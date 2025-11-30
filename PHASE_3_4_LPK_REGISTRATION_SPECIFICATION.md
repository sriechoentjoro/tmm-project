# Phase 3-4: LPK Registration Wizard Specification

**Date:** December 1, 2025  
**Phase:** 3-4 - LPK Registration with Email Verification  
**Status:** 🔄 IN PROGRESS  
**Complexity:** High (3-step wizard with email verification)

---

## 📋 Overview

Phase 3-4 implements a comprehensive 3-step registration wizard for Lembaga Pelatihan Kerja (LPK) / Vocational Training Institutions. Unlike the simple registrations in Phase 2B, LPK registration requires email verification and a multi-step approval process.

---

## 🎯 Business Requirements

### Stakeholder: Vocational Training Institutions (LPK)

**Why Multi-Step Registration?**
- LPKs manage candidates and trainees (sensitive data)
- Email verification required for security
- Admin oversight needed for approval
- Password management for account security
- Permission assignment based on institution type

**User Journey:**
1. **Admin creates LPK record** → Status: `pending_verification`
2. **System sends verification email** → 24-hour token validity
3. **LPK clicks verification link** → Status: `verified`
4. **LPK changes password** → Status: `active`
5. **LPK can now manage candidates** → Full access granted

---

## 🔄 Registration Workflow

### Step 1: Admin Creates LPK Record

**Performed by:** System Administrator  
**Access:** `/admin/lpk-registration/create`  
**Status Transition:** N/A → `pending_verification`

**Form Fields:**
- **Basic Information (Required):**
  - Institution Name (Nama LPK) - varchar(256)
  - Registration Number (Nomor Registrasi) - varchar(50)
  - Director Name (Nama Direktur) - varchar(256)
  - Email Address (UNIQUE, REQUIRED) - varchar(100)
  
- **Contact Information:**
  - Telephone - varchar(20)
  - Address - text
  - Province (Propinsi) - foreign key
  - City/District (Kabupaten) - foreign key
  - Subdistrict (Kecamatan) - foreign key
  - Village (Kelurahan) - foreign key
  - Postal Code - varchar(10)

- **Administrative:**
  - Establishment Date - date
  - License Number - varchar(100)
  - License Expiry - date

**Validation:**
- Email must be unique (not already registered)
- Registration number must be unique
- All required fields must be filled
- Email format must be valid

**Actions on Save:**
1. Create `vocational_training_institutions` record
2. Set `status = 'pending_verification'`
3. Generate 64-character verification token
4. Set token expiry (24 hours from now)
5. Store token in `email_verification_tokens` table
6. Send verification email via `EmailServiceComponent`
7. Log activity: 'registration' type
8. Create default permissions (view_dashboard, etc.)
9. Show admin success message with verification status

**Database Operations:**
```sql
-- Insert institution
INSERT INTO vocational_training_institutions 
(name, registration_number, director_name, email, status, created, modified)
VALUES ('...', '...', '...', 'lpk@example.com', 'pending_verification', NOW(), NOW());

-- Generate token
INSERT INTO email_verification_tokens 
(user_email, token, token_type, expires_at, created)
VALUES ('lpk@example.com', 'abc123...xyz', 'email_verification', DATE_ADD(NOW(), INTERVAL 24 HOUR), NOW());

-- Log activity
INSERT INTO stakeholder_activities 
(activity_type, stakeholder_type, stakeholder_id, description, admin_id, created)
VALUES ('registration', 'lpk', 123, 'New LPK registered: ABC Training Center', 1, NOW());

-- Create default permissions
INSERT INTO stakeholder_permissions 
(stakeholder_type, stakeholder_id, permission_key, permission_value, granted_by, created)
VALUES 
('lpk', 123, 'view_dashboard', 1, 1, NOW()),
('lpk', 123, 'manage_candidates', 1, 1, NOW()),
('lpk', 123, 'manage_trainees', 1, 1, NOW()),
('lpk', 123, 'export_data', 1, 1, NOW());
```

---

### Step 2: Email Verification

**Performed by:** LPK Representative  
**Access:** `/verify-email/{token}`  
**Status Transition:** `pending_verification` → `verified`

**Email Content:**
```
Subject: Verify Your Email - TMM System Registration

Dear [Director Name],

Your Vocational Training Institution (LPK) "[Institution Name]" has been registered 
in the TMM (Trainee Management) system.

To activate your account, please verify your email address by clicking the link below:

[Verify Email Button/Link]
https://asahifamily.id/tmm/verify-email/abc123...xyz

This verification link will expire in 24 hours.

Registration Details:
- Institution Name: [Name]
- Registration Number: [Number]
- Email: [Email]
- Registered on: [Date]

After verification, you will be able to:
- Set your password
- Access the LPK dashboard
- Manage candidates and trainees
- Export data and reports

If you did not register this account, please ignore this email.

Best regards,
TMM System Administrator
```

**Verification Page Flow:**
1. User clicks link in email
2. System validates token:
   - Token exists in database
   - Token not already used
   - Token not expired (< 24 hours old)
   - Token type = 'email_verification'
3. If valid:
   - Update institution status: `verified`
   - Mark token as used
   - Log activity: 'verification' type
   - Show success message
   - Redirect to password change page
4. If invalid:
   - Show error message (expired/invalid/already used)
   - Option to request new verification email

**Token Validation Logic:**
```php
public function verifyEmail($token = null)
{
    // Validate token format
    if (empty($token) || strlen($token) !== 64) {
        $this->Flash->error(__('Invalid verification link.'));
        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }
    
    // Load token from database
    $this->loadModel('EmailVerificationTokens');
    $tokenRecord = $this->EmailVerificationTokens->find()
        ->where([
            'token' => $token,
            'token_type' => 'email_verification',
            'is_used' => 0
        ])
        ->first();
    
    if (!$tokenRecord) {
        $this->Flash->error(__('Verification link not found or already used.'));
        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }
    
    // Check expiry
    $now = new DateTime();
    $expiresAt = new DateTime($tokenRecord->expires_at);
    if ($now > $expiresAt) {
        $this->Flash->error(__('Verification link has expired. Please request a new one.'));
        return $this->redirect(['action' => 'resendVerification']);
    }
    
    // Find institution by email
    $this->loadModel('VocationalTrainingInstitutions');
    $institution = $this->VocationalTrainingInstitutions->find()
        ->where(['email' => $tokenRecord->user_email])
        ->first();
    
    if (!$institution) {
        $this->Flash->error(__('Institution not found.'));
        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }
    
    // Update institution status
    $institution->status = 'verified';
    if ($this->VocationalTrainingInstitutions->save($institution)) {
        // Mark token as used
        $tokenRecord->is_used = 1;
        $tokenRecord->used_at = $now->format('Y-m-d H:i:s');
        $this->EmailVerificationTokens->save($tokenRecord);
        
        // Log activity
        $this->loadModel('StakeholderActivities');
        $this->StakeholderActivities->logActivity(
            'verification',
            'lpk',
            $institution->id,
            'Email verified for: ' . $institution->name,
            array('email' => $institution->email),
            null,
            null
        );
        
        $this->Flash->success(__('Email verified successfully! Please set your password.'));
        return $this->redirect(['action' => 'setPassword', $institution->id]);
    }
    
    $this->Flash->error(__('Verification failed. Please try again.'));
    return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
}
```

---

### Step 3: Password Setup & Activation

**Performed by:** LPK Representative  
**Access:** `/lpk-registration/set-password/{id}`  
**Status Transition:** `verified` → `active`

**Password Requirements:**
- Minimum 8 characters
- At least 1 uppercase letter
- At least 1 lowercase letter
- At least 1 number
- At least 1 special character (!@#$%^&*)
- Must not contain institution name
- Must not contain email username

**Form Fields:**
- New Password (password input)
- Confirm Password (password input)
- Terms & Conditions (checkbox, required)

**Password Strength Indicator:**
- Weak (red): < 6 chars or no complexity
- Medium (yellow): 6-8 chars with some complexity
- Strong (green): 8+ chars with full complexity

**Validation:**
```php
$validator
    ->scalar('password')
    ->minLength('password', 8, __('Password must be at least 8 characters.'))
    ->requirePresence('password', 'create')
    ->notEmptyString('password')
    ->add('password', 'complexity', [
        'rule' => function ($value, $context) {
            $hasUppercase = preg_match('/[A-Z]/', $value);
            $hasLowercase = preg_match('/[a-z]/', $value);
            $hasNumber = preg_match('/[0-9]/', $value);
            $hasSpecial = preg_match('/[!@#$%^&*]/', $value);
            
            return $hasUppercase && $hasLowercase && $hasNumber && $hasSpecial;
        },
        'message' => __('Password must contain uppercase, lowercase, number, and special character.')
    ])
    ->add('password', 'notInstitutionName', [
        'rule' => function ($value, $context) {
            if (isset($context['data']['institution_name'])) {
                return stripos($value, $context['data']['institution_name']) === false;
            }
            return true;
        },
        'message' => __('Password must not contain institution name.')
    ]);
```

**Actions on Save:**
1. Validate password meets requirements
2. Hash password using bcrypt
3. Update institution status: `active`
4. Update user record (if exists) or create new user
5. Log activity: 'activation' type
6. Send welcome email with login instructions
7. Redirect to login page with success message

**User Account Creation:**
```sql
-- Create or update user account
INSERT INTO users 
(username, email, password, role, fullname, is_active, vocational_training_institution_id, created, modified)
VALUES 
('lpk_abc', 'lpk@example.com', '$2y$10$...', 'lpk', 'ABC Training Center', 1, 123, NOW(), NOW())
ON DUPLICATE KEY UPDATE
password = '$2y$10$...',
is_active = 1,
modified = NOW();

-- Update institution status
UPDATE vocational_training_institutions
SET status = 'active', modified = NOW()
WHERE id = 123;

-- Log activation
INSERT INTO stakeholder_activities 
(activity_type, stakeholder_type, stakeholder_id, description, created)
VALUES ('activation', 'lpk', 123, 'LPK activated: ABC Training Center', NOW());
```

---

## 📧 Email Templates

### 1. Verification Email

**File:** `src/Template/Email/html/lpk_verification.ctp`

```php
<?php
$verificationUrl = $this->Url->build([
    'controller' => 'LpkRegistration',
    'action' => 'verifyEmail',
    $token
], true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                  color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; padding: 15px 30px; background: #667eea; 
                  color: white; text-decoration: none; border-radius: 5px; 
                  font-weight: bold; margin: 20px 0; }
        .button:hover { background: #764ba2; }
        .info-box { background: white; padding: 20px; border-left: 4px solid #667eea; 
                    margin: 20px 0; }
        .footer { text-align: center; color: #666; margin-top: 30px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 TMM System Registration</h1>
            <p>Vocational Training Institution</p>
        </div>
        <div class="content">
            <h2>Dear <?= h($directorName) ?>,</h2>
            
            <p>Your Vocational Training Institution (LPK) <strong>"<?= h($institutionName) ?>"</strong> 
            has been successfully registered in the TMM (Trainee Management) system.</p>
            
            <p>To activate your account and start managing candidates, please verify your email address:</p>
            
            <div style="text-align: center;">
                <a href="<?= $verificationUrl ?>" class="button">
                    ✓ Verify Email Address
                </a>
            </div>
            
            <p style="font-size: 0.9em; color: #666;">
                Or copy and paste this link:<br>
                <code><?= $verificationUrl ?></code>
            </p>
            
            <div class="info-box">
                <h3>📋 Registration Details:</h3>
                <ul>
                    <li><strong>Institution Name:</strong> <?= h($institutionName) ?></li>
                    <li><strong>Registration Number:</strong> <?= h($registrationNumber) ?></li>
                    <li><strong>Email:</strong> <?= h($email) ?></li>
                    <li><strong>Registered on:</strong> <?= date('F j, Y') ?></li>
                    <li><strong>Link expires:</strong> 24 hours</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h3>🔓 After Verification You Can:</h3>
                <ul>
                    <li>✓ Set your secure password</li>
                    <li>✓ Access the LPK dashboard</li>
                    <li>✓ Manage candidates and trainees</li>
                    <li>✓ Export data and generate reports</li>
                    <li>✓ Track apprenticeship placements</li>
                </ul>
            </div>
            
            <p><strong>⚠ Important:</strong> This verification link will expire in <strong>24 hours</strong>. 
            If it expires, please contact your system administrator for a new verification email.</p>
            
            <p>If you did not register this account, please ignore this email or contact us immediately.</p>
            
            <div class="footer">
                <p>Best regards,<br>
                <strong>TMM System Administrator</strong><br>
                Asahi Family</p>
                
                <p style="font-size: 0.8em; color: #999; margin-top: 20px;">
                    This is an automated email. Please do not reply to this message.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
```

### 2. Welcome Email (After Password Set)

**File:** `src/Template/Email/html/lpk_welcome.ctp`

```php
<?php
$loginUrl = $this->Url->build([
    'controller' => 'Users',
    'action' => 'login'
], true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Same styles as verification email */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to TMM System!</h1>
            <p>Your Account is Now Active</p>
        </div>
        <div class="content">
            <h2>Congratulations <?= h($directorName) ?>!</h2>
            
            <p>Your LPK account <strong>"<?= h($institutionName) ?>"</strong> is now fully activated 
            and ready to use.</p>
            
            <div class="info-box">
                <h3>🔑 Login Credentials:</h3>
                <ul>
                    <li><strong>Username:</strong> <?= h($username) ?></li>
                    <li><strong>Email:</strong> <?= h($email) ?></li>
                    <li><strong>Role:</strong> LPK Administrator</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="<?= $loginUrl ?>" class="button">
                    🔐 Login to Dashboard
                </a>
            </div>
            
            <div class="info-box">
                <h3>🚀 Get Started:</h3>
                <ol>
                    <li>Login using your email and password</li>
                    <li>Complete your institution profile</li>
                    <li>Add your first candidate</li>
                    <li>Explore the dashboard features</li>
                </ol>
            </div>
            
            <div class="info-box">
                <h3>💡 Features Available:</h3>
                <ul>
                    <li>✓ Candidate Management - Add, edit, track candidates</li>
                    <li>✓ Trainee Management - Manage active trainees</li>
                    <li>✓ Reports & Analytics - Export data and generate reports</li>
                    <li>✓ Dashboard - Real-time statistics and insights</li>
                </ul>
            </div>
            
            <p><strong>Need Help?</strong> Check out our help center or contact support.</p>
            
            <div class="footer">
                <p>Best regards,<br>
                <strong>TMM System Administrator</strong><br>
                Asahi Family</p>
            </div>
        </div>
    </div>
</body>
</html>
```

---

## 🗄️ Database Schema

### New Table: email_verification_tokens

```sql
CREATE TABLE IF NOT EXISTS email_verification_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    token_type ENUM('email_verification', 'password_reset') NOT NULL DEFAULT 'email_verification',
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    used_at DATETIME NULL,
    expires_at DATETIME NOT NULL,
    created DATETIME NOT NULL,
    
    INDEX idx_token (token),
    INDEX idx_email (user_email),
    INDEX idx_expires (expires_at),
    INDEX idx_used (is_used)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Update users Table

```sql
ALTER TABLE users 
ADD COLUMN vocational_training_institution_id INT NULL AFTER role,
ADD COLUMN special_skill_support_institution_id INT NULL AFTER vocational_training_institution_id,
ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER email,
ADD INDEX idx_lpk (vocational_training_institution_id),
ADD INDEX idx_special_skill (special_skill_support_institution_id),
ADD INDEX idx_active (is_active);
```

---

## 🎨 UI Components

### LPK Registration Form (Step 1)

**Route:** `/admin/lpk-registration/create`  
**Layout:** Admin layout with sidebar  
**Title:** "Register New Vocational Training Institution (LPK)"

**Form Sections:**
1. **Basic Information** (Collapsible card, expanded by default)
2. **Contact Information** (Collapsible card)
3. **Geographic Location** (Collapsible card with cascade dropdowns)
4. **Administrative Details** (Collapsible card)
5. **Permissions** (Checkbox list, pre-selected defaults)

**JavaScript Features:**
- Real-time email validation (check if already exists)
- Registration number format validation
- Province → City → District → Village cascade
- Form auto-save to localStorage (draft)
- Password strength meter (for admin viewing)

### Email Verification Page

**Route:** `/verify-email/{token}`  
**Layout:** Public layout (no auth required)  
**Components:**
- Loading spinner while validating
- Success message with countdown to password page
- Error message with resend option
- Token expiry countdown display

### Password Setup Page

**Route:** `/lpk-registration/set-password/{id}`  
**Layout:** Public layout  
**Components:**
- Password strength meter (live feedback)
- Requirements checklist (visual indicators)
- Show/hide password toggle
- Terms & conditions modal
- Submit button (disabled until valid)

---

## 🔐 Security Considerations

### Token Generation
- 64-character random string (cryptographically secure)
- No sequential IDs exposed
- One-time use only
- 24-hour expiration
- Stored hashed in database (optional enhancement)

### Password Security
- Bcrypt hashing (cost factor 10)
- Never stored in plain text
- Not sent via email
- Complexity requirements enforced
- No common passwords allowed

### Email Validation
- SMTP connection verification
- Disposable email detection
- MX record validation
- Unique constraint in database

### Rate Limiting
- Max 3 verification email requests per hour
- Max 5 password reset attempts per hour
- CAPTCHA after 3 failed attempts

---

## 📋 Testing Checklist

### Step 1: Admin Registration
- [ ] Create LPK with valid email
- [ ] Try duplicate email (should fail)
- [ ] Try invalid email format (should fail)
- [ ] Verify institution created with status 'pending_verification'
- [ ] Verify token generated in database
- [ ] Verify email sent successfully
- [ ] Verify activity logged

### Step 2: Email Verification
- [ ] Click verification link from email
- [ ] Verify status changes to 'verified'
- [ ] Verify token marked as used
- [ ] Try using same token again (should fail)
- [ ] Try expired token (should fail)
- [ ] Try invalid token format (should fail)
- [ ] Verify activity logged

### Step 3: Password Setup
- [ ] Set strong password (should succeed)
- [ ] Try weak password (should fail)
- [ ] Try password without uppercase (should fail)
- [ ] Try password with institution name (should fail)
- [ ] Verify status changes to 'active'
- [ ] Verify user account created
- [ ] Verify welcome email sent
- [ ] Verify activity logged

### End-to-End
- [ ] Complete full registration flow
- [ ] Login with new credentials
- [ ] Access LPK dashboard
- [ ] Verify permissions applied
- [ ] Check activity timeline

---

## 📊 Success Metrics

**Registration Completion Rate:**
- Target: 95% of started registrations complete within 48 hours

**Email Verification Rate:**
- Target: 90% verify within 24 hours
- Target: 5% or less expired tokens

**Password Setup Rate:**
- Target: 98% set password after verification
- Target: Average time < 5 minutes

**User Satisfaction:**
- Target: 4.5/5 stars for registration process
- Target: < 2 support tickets per 100 registrations

---

## 🚀 Implementation Phases

### Phase 3A: Core Registration (Week 1-2)
- [ ] LpkRegistrationController
- [ ] Registration form (Step 1)
- [ ] Email verification logic
- [ ] Token management
- [ ] Activity logging

### Phase 3B: Email & UI (Week 2-3)
- [ ] Email templates (verification, welcome)
- [ ] Verification page UI
- [ ] Password setup page UI
- [ ] Password strength meter
- [ ] Error handling

### Phase 4A: Testing & Polish (Week 3)
- [ ] Unit tests
- [ ] Integration tests
- [ ] UI/UX refinement
- [ ] Security audit
- [ ] Performance optimization

### Phase 4B: Deployment (Week 4)
- [ ] Documentation
- [ ] Admin training
- [ ] Soft launch (limited users)
- [ ] Monitor and fix issues
- [ ] Full deployment

---

**Document Version:** 1.0  
**Status:** Ready for Implementation  
**Next Step:** Create LpkRegistrationController

