# Administrator Stakeholder Management System - Implementation Guide

**Date**: December 1, 2025  
**Status**: Implementation Specification  
**Authorization Level**: Administrator Only

---

## Overview

This document outlines the complete implementation of the Administrator Stakeholder Management System with role-based authorization, email verification workflows, and multi-institution dashboards.

---

## 1. System Architecture

### 1.1 User Roles & Authorization Hierarchy

```
Administrator (Super Admin)
├── Full system access
├── Manage all stakeholders
├── View all dashboards
├── Assign permissions
└── System configuration

Management (Read-Only)
├── Dashboard viewing
├── Report access
└── No editing rights

TMM Staff Roles
├── tmm-recruitment (Candidate & Apprentice management)
├── tmm-training (Trainee & Training management)
└── tmm-documentation (Document management)

Institution Admins
├── LPK Admin (Vocational Training Institution)
│   ├── Register candidates via wizard
│   ├── Manage own institution data
│   └── View own candidates/trainees
└── Special Skill Support Admin
    ├── Register candidates
    ├── Manage institution profile
    └── View assigned candidates
```

### 1.2 Database Schema Requirements

**New Tables to Create:**

```sql
-- Institution Admin Users (extends users table)
ALTER TABLE users 
ADD COLUMN institution_type ENUM('lpk', 'special_skill', 'cooperative', 'acceptance_org', 'none') DEFAULT 'none',
ADD COLUMN institution_id INT(11) NULL,
ADD COLUMN email_verified TINYINT(1) DEFAULT 0,
ADD COLUMN email_verification_token VARCHAR(255) NULL,
ADD COLUMN email_verification_expires DATETIME NULL,
ADD COLUMN password_reset_token VARCHAR(255) NULL,
ADD COLUMN password_reset_expires DATETIME NULL;

-- Permission Matrix Table
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL,
    controller_name VARCHAR(100) NOT NULL,
    action_name VARCHAR(100) NOT NULL,
    allowed TINYINT(1) DEFAULT 1,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_permission (role_name, controller_name, action_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Log for Admin Actions
CREATE TABLE IF NOT EXISTS admin_audit_logs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    admin_user_id INT(11) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    target_type VARCHAR(50) NOT NULL,
    target_id INT(11) NULL,
    old_values TEXT NULL,
    new_values TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_user (admin_user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created (created)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 2. Administrator Dashboard

### 2.1 Main Dashboard Components

**File**: `src/Template/Admin/Dashboard/index.ctp`

**Features**:
1. **Statistics Overview**
   - Total Candidates by Institution
   - Total Trainees by Institution
   - Total Apprentices by Organization
   - Total Active Institutions

2. **Institution Status Grid**
   ```
   ┌─────────────────────────────────────────────────┐
   │  LPK Institutions: 25 (20 Active, 5 Pending)   │
   │  Special Skill: 10 (8 Active, 2 Pending)       │
   │  Cooperative: 15 (15 Active)                   │
   │  Acceptance Org: 30 (28 Active, 2 Pending)     │
   └─────────────────────────────────────────────────┘
   ```

3. **Recent Activities Timeline**
   - New registrations
   - Email verifications
   - Permission changes
   - Data updates

4. **Quick Actions Menu**
   - Register New LPK
   - Register New Special Skill Institution
   - Register Cooperative Association
   - Register Acceptance Organization
   - View Permission Matrix
   - Generate Reports

### 2.2 Controller Implementation

**File**: `src/Controller/Admin/DashboardController.php`

```php
<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class DashboardController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        
        // Only administrators can access
        if (!$this->hasRole('administrator')) {
            $this->Flash->error(__('Access denied. Administrator role required.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function index()
    {
        // Load statistics
        $candidatesTable = TableRegistry::getTableLocator()->get('Candidates');
        $traineesTable = TableRegistry::getTableLocator()->get('Trainees');
        $apprenticesTable = TableRegistry::getTableLocator()->get('Apprentices');
        $lpkTable = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');
        $specialSkillTable = TableRegistry::getTableLocator()->get('SpecialSkillSupportInstitutions');
        $cooperativeTable = TableRegistry::getTableLocator()->get('CooperativeAssociations');
        $acceptanceOrgTable = TableRegistry::getTableLocator()->get('AcceptanceOrganizations');

        // Statistics by Institution
        $stats = [
            'total_candidates' => $candidatesTable->find()->count(),
            'total_trainees' => $traineesTable->find()->count(),
            'total_apprentices' => $apprenticesTable->find()->count(),
            'total_lpk' => $lpkTable->find()->count(),
            'total_lpk_active' => $lpkTable->find()->where(['is_active' => 1])->count(),
            'total_special_skill' => $specialSkillTable->find()->count(),
            'total_cooperative' => $cooperativeTable->find()->count(),
            'total_acceptance_org' => $acceptanceOrgTable->find()->count(),
        ];

        // Candidates by LPK
        $candidatesByLpk = $candidatesTable->find()
            ->contain(['VocationalTrainingInstitutions'])
            ->select([
                'vocational_training_institution_id',
                'count' => $candidatesTable->find()->func()->count('*')
            ])
            ->group('vocational_training_institution_id')
            ->toArray();

        // Recent audit logs
        $auditLogsTable = TableRegistry::getTableLocator()->get('AdminAuditLogs');
        $recentActivities = $auditLogsTable->find()
            ->contain(['Users'])
            ->order(['created' => 'DESC'])
            ->limit(20)
            ->toArray();

        $this->set(compact('stats', 'candidatesByLpk', 'recentActivities'));
    }

    public function institutionDetail($type, $id)
    {
        // View detailed statistics for specific institution
        // $type: lpk, special_skill, cooperative, acceptance_org
    }
}
```

---

## 3. Vocational Training Institution (LPK) Registration Wizard

### 3.1 Wizard Flow

```
Step 1: Administrator enters LPK email
    ↓
Step 2: System validates email format
    ↓
Step 3: System generates:
    - Random password
    - Email verification token
    - User account (role: lpk-admin, unverified)
    ↓
Step 4: Send verification email to LPK owner
    ↓
Step 5: LPK owner clicks verification link
    ↓
Step 6: Redirect to LPK Profile completion page
    ↓
Step 7: LPK owner logs in with emailed password
    ↓
Step 8: LPK admin can register candidates via candidates/wizard
    ↓
Step 9: Administrator can continue LPK profile setup
```

### 3.2 Controller Implementation

**File**: `src/Controller/Admin/VocationalTrainingInstitutionsController.php`

```php
<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Utility\Security;
use Cake\I18n\Time;

class VocationalTrainingInstitutionsController extends AppController
{
    public function addWizard()
    {
        // Only administrators
        if (!$this->hasRole('administrator')) {
            $this->Flash->error(__('Access denied.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        $step = $this->request->getQuery('step', 1);

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if ($step == 1) {
                // Step 1: Email validation
                $email = $data['email'];
                
                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->Flash->error(__('Invalid email format.'));
                    return;
                }

                // Check if email already exists
                $usersTable = $this->loadModel('Users');
                $existingUser = $usersTable->find()
                    ->where(['email' => $email])
                    ->first();

                if ($existingUser) {
                    $this->Flash->error(__('Email already registered.'));
                    return;
                }

                // Generate random password
                $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%'), 0, 12);
                
                // Generate verification token
                $verificationToken = Security::hash(Security::randomBytes(32), 'sha256', false);
                $verificationExpires = new Time('+24 hours');

                // Create user account
                $user = $usersTable->newEntity([
                    'email' => $email,
                    'password' => $password,
                    'role' => 'lpk-admin',
                    'institution_type' => 'lpk',
                    'email_verified' => 0,
                    'email_verification_token' => $verificationToken,
                    'email_verification_expires' => $verificationExpires,
                    'is_active' => 0, // Inactive until verified
                ]);

                if ($usersTable->save($user)) {
                    // Send verification email
                    $this->sendLpkVerificationEmail($email, $password, $verificationToken);

                    // Store user ID in session for next step
                    $this->request->getSession()->write('Admin.LpkWizard.user_id', $user->id);

                    $this->Flash->success(__('Verification email sent to {0}', $email));
                    return $this->redirect(['action' => 'addWizard', '?' => ['step' => 2]]);
                } else {
                    $this->Flash->error(__('Failed to create user account.'));
                }
            }

            if ($step == 2) {
                // Step 2: Administrator continues LPK registration
                $userId = $this->request->getSession()->read('Admin.LpkWizard.user_id');
                
                $lpk = $this->VocationalTrainingInstitutions->newEntity($data);
                $lpk->admin_user_id = $userId;

                if ($this->VocationalTrainingInstitutions->save($lpk)) {
                    // Update user with institution_id
                    $usersTable = $this->loadModel('Users');
                    $user = $usersTable->get($userId);
                    $user->institution_id = $lpk->id;
                    $usersTable->save($user);

                    // Log admin action
                    $this->logAdminAction('create_lpk', 'VocationalTrainingInstitution', $lpk->id, null, $data);

                    $this->Flash->success(__('LPK registered successfully.'));
                    $this->request->getSession()->delete('Admin.LpkWizard');
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('Failed to save LPK data.'));
                }
            }
        }

        $this->set(compact('step'));
    }

    protected function sendLpkVerificationEmail($email, $password, $token)
    {
        $verificationUrl = \Cake\Routing\Router::url([
            'controller' => 'Users',
            'action' => 'verifyEmail',
            $token
        ], true);

        $emailInstance = new Email('default');
        $emailInstance
            ->setFrom(['noreply@tmm.com' => 'TMM System'])
            ->setTo($email)
            ->setSubject('LPK Account Verification - TMM System')
            ->setEmailFormat('html')
            ->setViewVars([
                'password' => $password,
                'verificationUrl' => $verificationUrl
            ])
            ->setTemplate('lpk_verification')
            ->send();

        return true;
    }

    public function matrix()
    {
        // Permission matrix view for all LPK institutions
        $lpks = $this->VocationalTrainingInstitutions->find()
            ->contain(['Users'])
            ->toArray();

        $this->set(compact('lpks'));
    }

    protected function logAdminAction($action, $targetType, $targetId, $oldValues, $newValues)
    {
        $auditTable = $this->loadModel('AdminAuditLogs');
        $log = $auditTable->newEntity([
            'admin_user_id' => $this->Auth->user('id'),
            'action_type' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($newValues),
            'ip_address' => $this->request->clientIp(),
            'user_agent' => $this->request->getEnv('HTTP_USER_AGENT')
        ]);
        $auditTable->save($log);
    }
}
```

---

## 4. Special Skill Support Institution Registration Wizard

**Implementation**: Same pattern as LPK wizard

**File**: `src/Controller/Admin/SpecialSkillSupportInstitutionsController.php`

```php
<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

class SpecialSkillSupportInstitutionsController extends AppController
{
    // Same wizard implementation as LPK
    // Change:
    // - role: 'special-skill-admin'
    // - institution_type: 'special_skill'
    // - Email template: 'special_skill_verification'
}
```

---

## 5. Cooperative Association & Acceptance Organization

### 5.1 Simple Registration (No Email Verification)

These are managed directly by administrators without email workflows.

**File**: `src/Controller/Admin/CooperativeAssociationsController.php`

```php
<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

class CooperativeAssociationsController extends AppController
{
    public function add()
    {
        if (!$this->hasRole('administrator')) {
            $this->Flash->error(__('Access denied.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        $cooperative = $this->CooperativeAssociations->newEntity();

        if ($this->request->is('post')) {
            $cooperative = $this->CooperativeAssociations->patchEntity($cooperative, $this->request->getData());

            if ($this->CooperativeAssociations->save($cooperative)) {
                $this->logAdminAction('create_cooperative', 'CooperativeAssociation', $cooperative->id, null, $this->request->getData());
                $this->Flash->success(__('Cooperative Association registered successfully.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Failed to save. Please try again.'));
        }

        $this->set(compact('cooperative'));
    }

    public function index()
    {
        if (!$this->hasRole('administrator')) {
            $this->Flash->error(__('Access denied.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        $cooperatives = $this->paginate($this->CooperativeAssociations);
        $this->set(compact('cooperatives'));
    }
}
```

---

## 6. Permission Matrix View

### 6.1 Matrix Grid Display

**File**: `src/Template/Admin/Permissions/matrix.ctp`

```php
<div class="permissions-matrix">
    <h2>Authorization Permission Matrix</h2>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Role</th>
                <th>Dashboard</th>
                <th>Candidates</th>
                <th>Trainees</th>
                <th>Apprentices</th>
                <th>LPK</th>
                <th>Special Skill</th>
                <th>Cooperative</th>
                <th>Acceptance Org</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $role): ?>
            <tr>
                <td><strong><?= h($role->name) ?></strong></td>
                <?php foreach ($controllers as $controller): ?>
                <td>
                    <?php 
                    $permission = $this->Permission->get($role->name, $controller);
                    echo $this->Permission->badge($permission);
                    ?>
                </td>
                <?php endforeach; ?>
                <td>
                    <?= $this->Html->link('Edit', ['action' => 'edit', $role->id], ['class' => 'btn btn-sm btn-primary']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

---

## 7. Email Templates

### 7.1 LPK Verification Email

**File**: `src/Template/Email/html/lpk_verification.ctp`

```php
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f8f9fa; padding: 20px; margin: 20px 0; }
        .button { display: inline-block; padding: 12px 24px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        .credentials { background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 4px; }
        .footer { text-align: center; color: #6c757d; font-size: 12px; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to TMM System</h1>
            <p>Vocational Training Institution Registration</p>
        </div>

        <div class="content">
            <h2>Hello!</h2>
            
            <p>An administrator has registered your institution in the TMM (Technical Intern Training Management) system.</p>

            <p>Please verify your email address by clicking the button below:</p>

            <div style="text-align: center;">
                <a href="<?= $verificationUrl ?>" class="button">Verify Email & Complete Profile</a>
            </div>

            <div class="credentials">
                <h3>Your Login Credentials</h3>
                <p><strong>Email:</strong> <?= $email ?></p>
                <p><strong>Temporary Password:</strong> <code><?= $password ?></code></p>
                <p style="color: #dc3545;"><strong>Important:</strong> Please change this password after your first login.</p>
            </div>

            <h3>Next Steps:</h3>
            <ol>
                <li>Click the verification button above</li>
                <li>Complete your institution profile</li>
                <li>Login using the credentials provided</li>
                <li>Start registering candidates using the Candidate Wizard</li>
            </ol>

            <p><strong>Note:</strong> This verification link will expire in 24 hours.</p>
        </div>

        <div class="footer">
            <p>This is an automated email from TMM System. Please do not reply.</p>
            <p>If you did not request this registration, please ignore this email.</p>
            <p>&copy; <?= date('Y') ?> TMM System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
```

---

## 8. Routes Configuration

**File**: `config/routes.php`

Add admin routes:

```php
Router::prefix('admin', function ($routes) {
    // Only accessible by administrator role
    $routes->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);
    
    $routes->connect('/dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
    $routes->connect('/dashboard/institution/:type/:id', ['controller' => 'Dashboard', 'action' => 'institutionDetail']);
    
    // LPK Management
    $routes->connect('/lpk/wizard', ['controller' => 'VocationalTrainingInstitutions', 'action' => 'addWizard']);
    $routes->connect('/lpk/matrix', ['controller' => 'VocationalTrainingInstitutions', 'action' => 'matrix']);
    $routes->connect('/lpk/:action/*', ['controller' => 'VocationalTrainingInstitutions']);
    
    // Special Skill Management
    $routes->connect('/special-skill/wizard', ['controller' => 'SpecialSkillSupportInstitutions', 'action' => 'addWizard']);
    $routes->connect('/special-skill/matrix', ['controller' => 'SpecialSkillSupportInstitutions', 'action' => 'matrix']);
    $routes->connect('/special-skill/:action/*', ['controller' => 'SpecialSkillSupportInstitutions']);
    
    // Cooperative Management
    $routes->connect('/cooperative/:action/*', ['controller' => 'CooperativeAssociations']);
    
    // Acceptance Organization Management
    $routes->connect('/acceptance-org/:action/*', ['controller' => 'AcceptanceOrganizations']);
    
    // Permission Matrix
    $routes->connect('/permissions/matrix', ['controller' => 'Permissions', 'action' => 'matrix']);
    $routes->connect('/permissions/:action/*', ['controller' => 'Permissions']);
    
    // Audit Logs
    $routes->connect('/audit-logs', ['controller' => 'AdminAuditLogs', 'action' => 'index']);
    
    $routes->fallbacks('DashedRoute');
});
```

---

## 9. Menu Integration

**File**: Update database menus table

```sql
INSERT INTO menus (title, url, icon, parent_id, sort_order, is_active, roles) VALUES
('Stakeholder Management', '#', 'fa fa-users-cog', NULL, 90, 1, 'administrator'),
('Dashboard', '/admin/dashboard', 'fa fa-tachometer-alt', (SELECT id FROM menus WHERE title='Stakeholder Management'), 1, 1, 'administrator'),
('LPK Institutions', '/admin/lpk', 'fa fa-school', (SELECT id FROM menus WHERE title='Stakeholder Management'), 2, 1, 'administrator'),
('Special Skill Institutions', '/admin/special-skill', 'fa fa-graduation-cap', (SELECT id FROM menus WHERE title='Stakeholder Management'), 3, 1, 'administrator'),
('Cooperative Associations', '/admin/cooperative', 'fa fa-handshake', (SELECT id FROM menus WHERE title='Stakeholder Management'), 4, 1, 'administrator'),
('Acceptance Organizations', '/admin/acceptance-org', 'fa fa-building', (SELECT id FROM menus WHERE title='Stakeholder Management'), 5, 1, 'administrator'),
('Permission Matrix', '/admin/permissions/matrix', 'fa fa-key', (SELECT id FROM menus WHERE title='Stakeholder Management'), 6, 1, 'administrator'),
('Audit Logs', '/admin/audit-logs', 'fa fa-history', (SELECT id FROM menus WHERE title='Stakeholder Management'), 7, 1, 'administrator');
```

---

## 10. Helper Components

### 10.1 Permission Helper

**File**: `src/View/Helper/PermissionHelper.php`

```php
<?php
namespace App\View\Helper;

use Cake\View\Helper;

class PermissionHelper extends Helper
{
    public function get($role, $controller)
    {
        // Get permission from database or config
        $permissions = [
            'administrator' => ['*' => 'full'],
            'management' => ['*' => 'read'],
            'lpk-admin' => [
                'Candidates' => 'full',
                'Dashboard' => 'read'
            ]
        ];

        if (isset($permissions[$role][$controller])) {
            return $permissions[$role][$controller];
        }

        if (isset($permissions[$role]['*'])) {
            return $permissions[$role]['*'];
        }

        return 'none';
    }

    public function badge($permission)
    {
        $badges = [
            'full' => '<span class="badge badge-success">Full Access</span>',
            'read' => '<span class="badge badge-info">Read Only</span>',
            'write' => '<span class="badge badge-warning">Write Only</span>',
            'none' => '<span class="badge badge-danger">No Access</span>'
        ];

        return $badges[$permission] ?? $badges['none'];
    }
}
```

---

## 11. Testing Checklist

### 11.1 Administrator Functions
- [ ] Login as administrator
- [ ] Access admin dashboard
- [ ] View statistics by institution
- [ ] Register new LPK with email wizard
- [ ] Verify email sent successfully
- [ ] Register new Special Skill institution
- [ ] Register Cooperative Association (direct)
- [ ] Register Acceptance Organization (direct)
- [ ] View permission matrix
- [ ] View audit logs

### 11.2 LPK Admin Functions
- [ ] Receive verification email
- [ ] Click verification link
- [ ] Complete LPK profile
- [ ] Login with provided password
- [ ] Access candidates/wizard
- [ ] Register new candidate
- [ ] View own institution candidates
- [ ] Cannot access other institutions' data

### 11.3 Email Verification
- [ ] Token expires after 24 hours
- [ ] Invalid token shows error
- [ ] Already verified accounts redirect to login
- [ ] Password reset works

### 11.4 Authorization Tests
- [ ] Non-admin cannot access /admin/* routes
- [ ] LPK admin cannot access other LPK data
- [ ] Permission matrix correctly filters menus
- [ ] Audit logs record all admin actions

---

## 12. Deployment Steps

1. **Database Migration**
   ```bash
   mysql -u root -p tmm < admin_stakeholder_schema.sql
   ```

2. **Create Admin Controllers**
   ```bash
   mkdir -p src/Controller/Admin
   # Copy all controller files
   ```

3. **Create Templates**
   ```bash
   mkdir -p src/Template/Admin
   # Copy all template files
   ```

4. **Create Email Templates**
   ```bash
   mkdir -p src/Template/Email/html
   # Copy email templates
   ```

5. **Update Routes**
   ```bash
   # Edit config/routes.php
   ```

6. **Clear Cache**
   ```bash
   cd /var/www/tmm
   rm -rf tmp/cache/*
   bin/cake cache clear_all
   ```

7. **Test Email Configuration**
   ```bash
   # Configure email in config/app.php
   # Test send verification email
   ```

---

## 13. Security Considerations

1. **Email Verification**
   - Use secure random tokens (32+ bytes)
   - Token expires in 24 hours
   - One-time use only
   - HTTPS required for verification links

2. **Password Security**
   - Generate strong random passwords
   - Force password change on first login
   - Use bcrypt hashing (CakePHP default)

3. **Authorization**
   - Check administrator role on every admin action
   - Log all admin actions with IP and user agent
   - Institution admins can only access own data

4. **CSRF Protection**
   - CakePHP CSRF component enabled
   - All forms include CSRF tokens

5. **SQL Injection Prevention**
   - Use CakePHP ORM query builder
   - Never use raw SQL with user input

---

## 14. Next Steps

**Priority 1 (This Week)**:
- [ ] Create database schema
- [ ] Implement admin dashboard
- [ ] Create LPK registration wizard
- [ ] Setup email templates and configuration

**Priority 2 (Next Week)**:
- [ ] Implement Special Skill wizard
- [ ] Create permission matrix view
- [ ] Add audit logging
- [ ] Test email verification workflow

**Priority 3 (Following Week)**:
- [ ] Implement Cooperative/Acceptance Org management
- [ ] Add dashboard charts and graphs
- [ ] Create user documentation
- [ ] Perform security audit

---

**Document Version**: 1.0  
**Last Updated**: December 1, 2025  
**Prepared By**: AI Development Team  
**Status**: Ready for Implementation
