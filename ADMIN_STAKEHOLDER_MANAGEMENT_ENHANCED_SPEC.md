# Administrator Stakeholder Management - Enhanced Specification

**Date:** December 1, 2025  
**Status:** Specification Phase  
**Authorization Level:** Administrator Only

---

## 🎯 Overview

Enhanced Stakeholder Management system for Administrators to manage all institutional stakeholders with comprehensive registration wizards, email verification, permission matrices, and monitoring dashboards.

---

## 📋 Table of Contents

1. [Help & Documentation](#1-help--documentation)
2. [Monitoring Dashboard](#2-monitoring-dashboard)
3. [Acceptance Organizations Management](#3-acceptance-organizations-management)
4. [Cooperative Associations Management](#4-cooperative-associations-management)
5. [Vocational Training Institutions (LPK) Management](#5-vocational-training-institutions-lpk-management)
6. [Special Skill Support Institutions Management](#6-special-skill-support-institutions-management)
7. [Technical Implementation](#technical-implementation)
8. [Database Schema](#database-schema)
9. [Email Templates](#email-templates)
10. [Permission Matrix](#permission-matrix)

---

## 1. Help & Documentation

### 1.1 Contextual Help System

**Location:** Sidebar on all Stakeholder Management pages

**Features:**
- 📖 **Quick Guide** - Step-by-step instructions for current page
- 🎥 **Video Tutorials** - Embedded tutorial videos
- 📞 **Contact Support** - Direct link to admin support
- 📊 **Status Indicators** - Real-time system status
- 🔍 **Search Help** - Searchable help topics

**Implementation:**
```php
// Element: src/Template/Element/stakeholder_help.ctp
<div class="help-sidebar">
    <div class="help-header">
        <i class="fas fa-question-circle"></i>
        <h4>Help & Guide</h4>
    </div>
    
    <div class="help-content">
        <div class="help-section">
            <h5>Current Page: <?= $currentPage ?></h5>
            <p><?= $helpText ?></p>
        </div>
        
        <div class="help-section">
            <h5>Quick Actions</h5>
            <ul>
                <li><a href="#video-tutorial">Watch Tutorial</a></li>
                <li><a href="#faq">View FAQ</a></li>
                <li><a href="#contact">Contact Support</a></li>
            </ul>
        </div>
        
        <div class="help-section status-indicators">
            <h5>System Status</h5>
            <div class="status-item">
                <span class="status-dot success"></span>
                <span>Email Service: Active</span>
            </div>
            <div class="status-item">
                <span class="status-dot success"></span>
                <span>Database: Connected</span>
            </div>
        </div>
    </div>
</div>
```

---

## 2. Monitoring Dashboard

### 2.1 Stakeholder Overview Dashboard

**Route:** `/admin/stakeholder-dashboard`  
**Controller:** `AdminDashboardController::stakeholderDashboard()`

**Features:**

#### A. Statistics Cards
```
┌─────────────────────────────────────────────────────────┐
│  Vocational Training Institutions (LPK)                 │
│  Total: 45  |  Active: 42  |  Pending: 3  |  New: 2    │
│  Total Candidates: 1,234                                │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Special Skill Support Institutions                     │
│  Total: 12  |  Active: 10  |  Pending: 2  |  New: 1    │
│  Total Trainees: 567                                    │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Acceptance Organizations                               │
│  Total: 78  |  Active: 75  |  Pending: 3  |  New: 5    │
│  Total Apprentices: 2,345                               │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Cooperative Associations                               │
│  Total: 23  |  Active: 21  |  Pending: 2  |  New: 1    │
└─────────────────────────────────────────────────────────┘
```

#### B. Candidate Distribution Chart
- Pie chart showing candidates per institution
- Bar chart showing monthly registration trends
- Geographic distribution map

#### C. Recent Activities Feed
```
[2 mins ago] New LPK registered: PT Asahi Training Center
[5 mins ago] Special Skill Institution verified: Osaka Language School
[1 hour ago] Acceptance Organization updated: Toyota Manufacturing
[2 hours ago] 15 new candidates registered by PT Karya LPK
```

#### D. Pending Actions
```
⚠️ 3 LPK registrations awaiting verification
⚠️ 2 Special Skill institutions pending approval
⚠️ 5 email verifications pending
⚠️ 1 permission matrix update required
```

**Controller Implementation:**
```php
// src/Controller/AdminDashboardController.php
public function stakeholderDashboard()
{
    // Authorization check
    if (!$this->hasPermission('Admin', 'stakeholderDashboard')) {
        return $this->handleUnauthorizedAccess();
    }
    
    // Get statistics
    $stats = [
        'lpk' => [
            'total' => $this->VocationalTrainingInstitutions->find()->count(),
            'active' => $this->VocationalTrainingInstitutions->find()
                ->where(['status' => 'active'])->count(),
            'pending' => $this->VocationalTrainingInstitutions->find()
                ->where(['status' => 'pending'])->count(),
            'new' => $this->VocationalTrainingInstitutions->find()
                ->where(['created >=' => date('Y-m-d', strtotime('-30 days'))])->count(),
            'totalCandidates' => $this->getCandidatesByInstitutionType('lpk')
        ],
        'specialSkill' => [
            'total' => $this->SpecialSkillSupportInstitutions->find()->count(),
            'active' => $this->SpecialSkillSupportInstitutions->find()
                ->where(['status' => 'active'])->count(),
            'pending' => $this->SpecialSkillSupportInstitutions->find()
                ->where(['status' => 'pending'])->count(),
            'new' => $this->SpecialSkillSupportInstitutions->find()
                ->where(['created >=' => date('Y-m-d', strtotime('-30 days'))])->count(),
            'totalTrainees' => $this->getTraineesByInstitutionType('special_skill')
        ],
        'acceptanceOrgs' => [
            'total' => $this->AcceptanceOrganizations->find()->count(),
            'active' => $this->AcceptanceOrganizations->find()
                ->where(['status' => 'active'])->count(),
            'pending' => $this->AcceptanceOrganizations->find()
                ->where(['status' => 'pending'])->count(),
            'new' => $this->AcceptanceOrganizations->find()
                ->where(['created >=' => date('Y-m-d', strtotime('-30 days'))])->count(),
            'totalApprentices' => $this->getApprenticesByOrganization()
        ],
        'cooperativeAssocs' => [
            'total' => $this->CooperativeAssociations->find()->count(),
            'active' => $this->CooperativeAssociations->find()
                ->where(['status' => 'active'])->count(),
            'pending' => $this->CooperativeAssociations->find()
                ->where(['status' => 'pending'])->count(),
            'new' => $this->CooperativeAssociations->find()
                ->where(['created >=' => date('Y-m-d', strtotime('-30 days'))])->count()
        ]
    ];
    
    // Recent activities
    $recentActivities = $this->getRecentStakeholderActivities(20);
    
    // Pending actions
    $pendingActions = $this->getPendingAdminActions();
    
    // Chart data
    $chartData = [
        'candidateDistribution' => $this->getCandidateDistributionData(),
        'monthlyTrends' => $this->getMonthlyRegistrationTrends(),
        'geographicDistribution' => $this->getGeographicDistribution()
    ];
    
    $this->set(compact('stats', 'recentActivities', 'pendingActions', 'chartData'));
}
```

---

## 3. Acceptance Organizations Management

### 3.1 Registration Wizard

**Route:** `/admin/acceptance-organizations/add-wizard`

**Features:**
- Simple registration form (no email verification)
- Direct admin creation
- Immediate activation

**Form Fields:**
```
Organization Information:
- Name *
- Registration Number *
- Address *
- Province, City, District, Village *
- Postal Code
- Phone Number *
- Email
- Website
- Industry Type *
- Capacity (number of apprentices)
- Status (Active/Inactive) *

Contact Person:
- Contact Name *
- Contact Position *
- Contact Phone *
- Contact Email *

Documents:
- Company Registration Certificate
- Business License
- Company Profile
```

**Implementation:**
```php
// src/Controller/Admin/AcceptanceOrganizationsController.php
public function addWizard()
{
    $organization = $this->AcceptanceOrganizations->newEntity();
    
    if ($this->request->is('post')) {
        $data = $this->request->getData();
        $data['status'] = 'active'; // Admin-created are active by default
        $data['created_by'] = $this->Auth->user('id');
        
        $organization = $this->AcceptanceOrganizations->patchEntity($organization, $data);
        
        if ($this->AcceptanceOrganizations->save($organization)) {
            $this->Flash->success('Acceptance Organization registered successfully.');
            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error('Unable to register organization.');
    }
    
    // Load dropdowns
    $provinces = $this->loadProvinces();
    $industryTypes = $this->loadIndustryTypes();
    
    $this->set(compact('organization', 'provinces', 'industryTypes'));
}
```

### 3.2 List View with Advanced Features

**Route:** `/admin/acceptance-organizations/index`

**Features:**
- Sortable columns
- Advanced filters (status, industry, location)
- Export to Excel/PDF
- Bulk actions (activate, deactivate, delete)
- Quick view modal
- Apprentice count per organization

---

## 4. Cooperative Associations Management

### 4.1 Registration Wizard

**Route:** `/admin/cooperative-associations/add-wizard`

**Similar to Acceptance Organizations but with:**
- Association-specific fields
- Member list management
- Association type classification

---

## 5. Vocational Training Institutions (LPK) Management

### 5.1 Registration Wizard (Multi-Step)

**Route:** `/admin/lpk/add-wizard`

#### **Step 1: Admin Creates Initial Record**

**Form Fields:**
```
Institution Information:
- Institution Name *
- Registration Number (from Ministry) *
- Email * (will be used for admin account)
- Phone Number *
- Address *
- Province, City, District, Village *
```

**Process:**
1. Admin submits form
2. System validates email format
3. System creates:
   - LPK institution record (status: `pending_verification`)
   - User account (role: `lpk_admin`, status: `pending_verification`)
   - Verification token (expires in 24 hours)
4. System sends two emails:
   - **To LPK Email:** Verification link + temporary password
   - **To Admin:** Notification of registration

**Email Template 1: To LPK Owner**
```
Subject: Verify Your LPK Account - TMM System

Dear LPK Administrator,

Welcome to the Technical Intern Training Management System!

An administrator has registered your institution:
Institution Name: [Name]
Registration Number: [Number]

Please verify your email address by clicking the link below:
[Verification Link - expires in 24 hours]

Your temporary login credentials:
Email: [email]
Password: [auto-generated password]

After verification, you will be redirected to your LPK Profile page where you can:
1. Complete your institution profile
2. Register candidates using our Candidate Registration Wizard

Best regards,
TMM System Administrator
```

**Email Template 2: To Admin**
```
Subject: LPK Registration Created - Pending Verification

Administrator,

You have successfully registered a new LPK institution:

Institution Name: [Name]
Email: [email]
Status: Pending Verification

The verification email has been sent to the LPK owner.
You can continue editing the institution details or wait for email verification.

View Institution: [Link]

Best regards,
TMM System
```

#### **Step 2: LPK Email Verification**

**Route:** `/lpk/verify-email/:token`

**Process:**
1. LPK owner clicks verification link in email
2. System validates token (not expired, matches user)
3. System updates:
   - User status: `verified`
   - Institution status: `active`
   - Email verified timestamp
4. System redirects to: `/lpk/profile`
5. System sends confirmation email with login instructions

**LPK Profile Page:**
```
┌─────────────────────────────────────────────────────────┐
│  Welcome to Your LPK Dashboard                          │
│  Institution: [Name]                                    │
│  Status: ✅ Verified                                    │
└─────────────────────────────────────────────────────────┘

Quick Actions:
[ Register New Candidate ] [ View Candidates ] [ Edit Profile ]

Profile Completion: 60% ▓▓▓▓▓▓░░░░
Please complete your profile:
⚠️ Add institution logo
⚠️ Add certifications and licenses
⚠️ Add staff information
```

#### **Step 3: Admin Completes Registration (Optional)**

**Route:** `/admin/lpk/edit/:id`

**Features:**
- Admin can edit/complete institution details anytime
- Admin can reset LPK admin password
- Admin can view registration audit log

**Additional Fields Admin Can Complete:**
```
Advanced Information:
- Establishment Date
- Accreditation Details
- Capacity (max students)
- Specializations/Programs
- Facilities Description
- Staff Count
- Success Rate Statistics

Documents Upload:
- Business License
- Accreditation Certificate
- Insurance Documents
- Tax Registration
```

### 5.2 Permission Matrix View

**Route:** `/admin/lpk/permissions`

**Features:**
- Matrix table showing all LPKs and their permissions
- Quick toggle permissions
- Bulk permission updates
- Permission templates (basic, standard, premium)

**Matrix Layout:**
```
┌──────────────────────────────────────────────────────────────┐
│ LPK Name           │ View │ Add │ Edit │ Delete │ Export     │
├──────────────────────────────────────────────────────────────┤
│ PT Asahi Training  │  ✅  │ ✅  │  ✅  │   ❌   │   ✅       │
│ PT Karya LPK       │  ✅  │ ✅  │  ✅  │   ❌   │   ✅       │
│ LPK Sukses Mandiri │  ✅  │ ✅  │  ❌  │   ❌   │   ❌       │
└──────────────────────────────────────────────────────────────┘

Permission Scope:
- Candidates: Can register/view/edit their own candidates only
- Reports: Can view reports for their candidates only
- Documents: Can manage documents for their candidates only
```

**Implementation:**
```php
// src/Controller/Admin/LpkController.php
public function permissions()
{
    $lpkList = $this->VocationalTrainingInstitutions->find('all')
        ->contain(['Users' => function($q) {
            return $q->where(['role' => 'lpk_admin']);
        }])
        ->toArray();
    
    if ($this->request->is(['post', 'put'])) {
        // Update permissions
        $permissions = $this->request->getData('permissions');
        
        foreach ($permissions as $lpkId => $perms) {
            $this->updateLpkPermissions($lpkId, $perms);
        }
        
        $this->Flash->success('Permissions updated successfully.');
        return $this->redirect(['action' => 'permissions']);
    }
    
    $this->set(compact('lpkList'));
}
```

### 5.3 List View

**Route:** `/admin/lpk/index`

**Features:**
- Status indicators (pending, verified, active, suspended)
- Email verification status
- Last login timestamp
- Candidate count per LPK
- Quick actions (resend verification, reset password, suspend)
- Advanced filters

**List Columns:**
```
┌────────────────────────────────────────────────────────────────────────────┐
│ Logo │ Name              │ Status    │ Email Status │ Candidates │ Actions │
├────────────────────────────────────────────────────────────────────────────┤
│ [🏢] │ PT Asahi Training │ ✅ Active │ ✅ Verified  │ 234        │ [⚙️]    │
│ [🏢] │ PT Karya LPK      │ ⏳ Pending│ ⏳ Pending   │ 0          │ [⚙️]    │
│ [🏢] │ LPK Sukses        │ ✅ Active │ ✅ Verified  │ 156        │ [⚙️]    │
└────────────────────────────────────────────────────────────────────────────┘
```

---

## 6. Special Skill Support Institutions Management

### 6.1 Registration Wizard (Multi-Step)

**Route:** `/admin/special-skill/add-wizard`

**Same process as LPK but with different email templates and redirects:**

#### **Email Template: To Special Skill Institution Owner**
```
Subject: Verify Your Special Skill Institution Account - TMM System

Dear Institution Administrator,

Welcome to the Technical Intern Training Management System!

An administrator has registered your institution:
Institution Name: [Name]
Type: Special Skill Support Institution

Please verify your email address by clicking the link below:
[Verification Link - expires in 24 hours]

Your temporary login credentials:
Email: [email]
Password: [auto-generated password]

After verification, you will be redirected to your Institution Profile page where you can:
1. Complete your institution profile
2. Register trainees using our Trainee Registration Wizard
3. Manage training programs

Best regards,
TMM System Administrator
```

#### **Profile Page After Verification**

**Route:** `/special-skill/profile`

```
┌─────────────────────────────────────────────────────────┐
│  Welcome to Your Institution Dashboard                  │
│  Institution: [Name]                                    │
│  Type: Special Skill Support                            │
│  Status: ✅ Verified                                    │
└─────────────────────────────────────────────────────────┘

Quick Actions:
[ Register New Trainee ] [ View Trainees ] [ Edit Profile ]

Profile Completion: 65% ▓▓▓▓▓▓▓░░░
Please complete your profile:
⚠️ Add institution logo
⚠️ Add training programs
⚠️ Add instructor information
```

### 6.2 Permission Matrix & List View

Same features as LPK but for Special Skill institutions.

---

## Technical Implementation

### Database Schema Changes

#### 1. Add `email_verified_at` and `status` to Users table
```sql
ALTER TABLE users ADD COLUMN email_verified_at DATETIME NULL AFTER email;
ALTER TABLE users ADD COLUMN status VARCHAR(50) DEFAULT 'pending_verification' 
    AFTER email_verified_at;
ALTER TABLE users ADD COLUMN verification_token VARCHAR(255) NULL 
    AFTER status;
ALTER TABLE users ADD COLUMN verification_token_expires DATETIME NULL 
    AFTER verification_token;
ALTER TABLE users ADD INDEX idx_verification_token (verification_token);
ALTER TABLE users ADD INDEX idx_status (status);
```

#### 2. Add `status` to Institution tables
```sql
-- Vocational Training Institutions
ALTER TABLE vocational_training_institutions 
    ADD COLUMN status VARCHAR(50) DEFAULT 'pending_verification' AFTER name;
ALTER TABLE vocational_training_institutions 
    ADD INDEX idx_status (status);

-- Special Skill Support Institutions
ALTER TABLE special_skill_support_institutions 
    ADD COLUMN status VARCHAR(50) DEFAULT 'pending_verification' AFTER name;
ALTER TABLE special_skill_support_institutions 
    ADD INDEX idx_status (status);

-- Acceptance Organizations
ALTER TABLE acceptance_organizations 
    ADD COLUMN status VARCHAR(50) DEFAULT 'active' AFTER name;
ALTER TABLE acceptance_organizations 
    ADD INDEX idx_status (status);

-- Cooperative Associations
ALTER TABLE cooperative_associations 
    ADD COLUMN status VARCHAR(50) DEFAULT 'active' AFTER name;
ALTER TABLE cooperative_associations 
    ADD INDEX idx_status (status);
```

#### 3. Create Activity Log table
```sql
CREATE TABLE stakeholder_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_type VARCHAR(100) NOT NULL,
    stakeholder_type VARCHAR(100) NOT NULL,
    stakeholder_id INT NOT NULL,
    description TEXT,
    user_id INT NULL,
    ip_address VARCHAR(45) NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_stakeholder (stakeholder_type, stakeholder_id),
    INDEX idx_user (user_id),
    INDEX idx_created (created)
);
```

#### 4. Create Permission Matrix table
```sql
CREATE TABLE stakeholder_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stakeholder_type VARCHAR(100) NOT NULL,
    stakeholder_id INT NOT NULL,
    permission_key VARCHAR(100) NOT NULL,
    permission_value TINYINT(1) DEFAULT 0,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_permission (stakeholder_type, stakeholder_id, permission_key),
    INDEX idx_stakeholder (stakeholder_type, stakeholder_id)
);
```

### Controller Structure

```
src/Controller/Admin/
├── StakeholderDashboardController.php
├── AcceptanceOrganizationsController.php
├── CooperativeAssociationsController.php
├── LpkController.php (VocationalTrainingInstitutions)
├── SpecialSkillController.php
└── PermissionMatrixController.php
```

### Email Service Component

```php
// src/Controller/Component/EmailServiceComponent.php
class EmailServiceComponent extends Component
{
    public function sendLpkVerificationEmail($user, $institution, $token)
    {
        $email = new Email('default');
        $email->from(['noreply@tmm-system.com' => 'TMM System'])
            ->to($user->email)
            ->subject('Verify Your LPK Account - TMM System')
            ->template('lpk_verification')
            ->emailFormat('html')
            ->viewVars([
                'institutionName' => $institution->name,
                'registrationNumber' => $institution->registration_number,
                'verificationLink' => Router::url([
                    'controller' => 'Lpk',
                    'action' => 'verifyEmail',
                    $token
                ], true),
                'email' => $user->email,
                'password' => $user->temp_password
            ])
            ->send();
    }
    
    public function sendAdminNotificationEmail($institution, $type)
    {
        // Send notification to admin about new registration
    }
    
    public function sendVerificationConfirmationEmail($user, $institution)
    {
        // Send confirmation after successful verification
    }
}
```

### Authorization Integration

```php
// src/Controller/AppController.php - Add to beforeFilter
public function beforeFilter(Event $event)
{
    parent::beforeFilter($event);
    
    // Admin-only routes
    if ($this->request->getParam('prefix') === 'admin') {
        if (!$this->hasPermission('Admin', '*')) {
            return $this->handleUnauthorizedAccess();
        }
    }
    
    // LPK routes
    if ($this->request->getParam('controller') === 'Lpk' && 
        !in_array($this->request->getParam('action'), ['verifyEmail', 'profile'])) {
        if (!$this->hasPermission('Lpk', $this->request->getParam('action'))) {
            return $this->handleUnauthorizedAccess();
        }
    }
}
```

---

## Implementation Phases

### Phase 1: Database & Core Setup (Week 1)
- [ ] Create database migrations
- [ ] Add status fields to all institution tables
- [ ] Create activity log table
- [ ] Create permission matrix table
- [ ] Update User model with email verification fields

### Phase 2: Email Service (Week 1-2)
- [ ] Create Email Service Component
- [ ] Design email templates (HTML + plain text)
- [ ] Implement token generation and validation
- [ ] Test email delivery (SMTP configuration)

### Phase 3: Admin Dashboard (Week 2)
- [ ] Create StakeholderDashboardController
- [ ] Build monitoring dashboard view
- [ ] Implement statistics queries
- [ ] Create chart components (Chart.js)
- [ ] Add recent activities feed

### Phase 4: Simple Registrations (Week 2-3)
- [ ] Acceptance Organizations wizard
- [ ] Cooperative Associations wizard
- [ ] List views with filters
- [ ] Export functionality

### Phase 5: LPK Registration Wizard (Week 3-4)
- [ ] Admin creates LPK (Step 1)
- [ ] Email verification system (Step 2)
- [ ] LPK profile page (Step 3)
- [ ] Admin completion interface
- [ ] Permission matrix view

### Phase 6: Special Skill Registration Wizard (Week 4-5)
- [ ] Admin creates Special Skill institution
- [ ] Email verification system
- [ ] Institution profile page
- [ ] Permission matrix view

### Phase 7: Help System & Documentation (Week 5)
- [ ] Create help sidebar element
- [ ] Write help content for each page
- [ ] Create video tutorials (optional)
- [ ] Build FAQ section

### Phase 8: Testing & Refinement (Week 6)
- [ ] Unit tests for email service
- [ ] Integration tests for wizard flows
- [ ] User acceptance testing
- [ ] Performance optimization
- [ ] Security audit

---

## Security Considerations

1. **Email Verification Tokens**
   - Must be cryptographically secure (use `Security::randomBytes()`)
   - Must expire after 24 hours
   - One-time use only
   - Stored as hash in database

2. **Password Generation**
   - Auto-generated passwords must be strong (16+ characters)
   - Must include uppercase, lowercase, numbers, symbols
   - Never log passwords
   - Force password change on first login

3. **Authorization**
   - Admin-only access to all management functions
   - LPK/Special Skill institutions can only access their own data
   - Row-level security based on institution_id

4. **Audit Logging**
   - Log all admin actions (create, edit, delete)
   - Log all email sends
   - Log all permission changes
   - Log all verification attempts

---

## Next Steps

1. **Review this specification** with stakeholders
2. **Approve database changes**
3. **Configure email SMTP settings** (production)
4. **Begin Phase 1 implementation**
5. **Create detailed task breakdown** for each phase

---

## Questions for Clarification

1. **Email Service**: Do we have SMTP server configured? Need credentials.
2. **Domain**: What domain should verification emails come from?
3. **Templates**: Do you have design/branding guidelines for email templates?
4. **Notifications**: Should system also send SMS notifications?
5. **Approval Workflow**: Do LPK/Special Skill registrations need manual admin approval after verification?
6. **Permissions**: What specific permissions should be configurable in the matrix?

---

**Status:** ✅ Specification Complete - Awaiting Approval

**Estimated Development Time:** 6 weeks  
**Complexity:** High  
**Priority:** High

