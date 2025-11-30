# Authorization System - Complete Implementation Guide

## Overview
Comprehensive role-based authorization system with menu filtering, data scope validation, and enhanced unauthorized access notifications.

## Features Implemented

### 1. **Menu Filtering by Role Permissions** ✅
- Menus automatically filtered based on user role
- Only shows controllers/actions user has access to
- Parent menus without children are automatically hidden
- Admin sees all menus, other roles see only authorized menus

**Location**: `src/Controller/AppController.php` - `beforeRender()` method (lines 682-743)

**How it works**:
```php
// Get allowed controllers for current user based on role
$allowedControllers = $this->getAllowedControllers();

// Filter menus query
$menuQuery->where(function ($exp) use ($allowedControllers) {
    return $exp->or_([
        'Menus.controller IN' => $allowedControllers,
        'Menus.controller IS' => null // Include parent menus
    ]);
});
```

### 2. **Permission-Based Authorization** ✅
- Centralized permission system for all roles
- Controller/action level permissions
- Wildcard support (`'*'` = all actions)
- Database connection scope validation

**Location**: `src/Controller/AppController.php` - Permission methods (lines 441-670)

**Key Methods**:
- `hasPermission($controller, $action)` - Check if user can access controller/action
- `getRolePermissions()` - Get permission map for current role
- `getControllerConnection($controller)` - Get database connection for controller
- `hasConnectionAccess($connectionName)` - Check if role can access database

**Permission Maps** (configured per role):

| Role | Permissions |
|------|-------------|
| **administrator** | All controllers, all actions |
| **management** | Read-only access (index, view, export) |
| **tmm-recruitment** | Full access to Candidates, CandidateDocuments, Apprentices |
| **tmm-training** | Full access to Trainees, TraineeAccountings, TraineeTrainings |
| **tmm-documentation** | Full access to all Documents tables |
| **lpk-penyangga** | Full access to Candidates (institution-scoped) |
| **lpk-so** | Full access to Candidates (institution-scoped) |

### 3. **Enhanced Unauthorized Access Notifications** ✅
- Clear, informative error messages
- Shows current user and role
- Provides guidance on what to do next
- Logs unauthorized attempts for security auditing
- Link to login page with different account

**Location**: `src/Controller/AppController.php` - `handleUnauthorizedAccess()` method (lines 410-439)

**Example Message**:
```
❌ Access Denied: Your role does not have access to edit in CandidateDocuments.

Your account: lpk_bekasi
Your role: lpk-penyangga

Please contact your administrator if you believe this is an error, or login with different account
```

### 4. **Institution-Based Data Scope Validation** ✅
- LPK users can only access records from their institution
- Automatic filtering in index queries
- Validation before edit/delete operations
- Clear error messages for scope violations

**Location**: `src/Controller/AppController.php` - `canAccessRecord()` method (lines 388-408)

**Usage in Controllers**:
```php
public function edit($id = null)
{
    $entity = $this->CandidateDocuments->get($id, [
        'contain' => ['Candidates']
    ]);
    
    // Validate institution scope
    if (!$this->canAccessRecord($entity->candidate, 'vocational_training_institution_id')) {
        $this->Flash->error(__('Access Denied: This record belongs to another institution.'));
        return $this->redirect(['action' => 'index']);
    }
    
    // Continue with edit...
}
```

### 5. **Database Connection Scope Control** ✅
- Role-based access to database connections
- Prevents cross-database unauthorized access
- Configured per role

**Connection Access Map**:

| Role | Allowed Databases |
|------|-------------------|
| **administrator** | ALL databases |
| **management** | ALL databases (read-only) |
| **tmm-recruitment** | cms_lpk_candidates, cms_tmm_apprentices, cms_masters |
| **tmm-training** | cms_tmm_trainees, cms_tmm_trainee_trainings, cms_masters |
| **tmm-documentation** | cms_tmm_apprentice_documents, cms_lpk_candidate_documents, cms_masters |
| **lpk-penyangga** | cms_lpk_candidates, cms_lpk_candidate_documents, cms_masters |
| **lpk-so** | cms_lpk_candidates, cms_lpk_candidate_documents, cms_masters |

## Authorization Flow

```
User Request
    ↓
beforeFilter() - Check authentication
    ↓
isAuthorized() - Main authorization gate
    ↓
hasPermission() - Check controller/action permissions
    ↓
    ├─ hasConnectionAccess() - Validate database access
    │
    ├─ getRolePermissions() - Get allowed controllers/actions
    │
    └─ If DENIED → handleUnauthorizedAccess()
                    ├─ Log attempt
                    ├─ Set flash message with details
                    └─ Redirect to dashboard
    ↓
beforeRender() - Filter navigation menus
    ↓
    └─ getAllowedControllers() - Get accessible controllers
        └─ Filter menu query by controller names
```

## Role-Specific Authorization Methods

### Management Role (Read-Only)
```php
protected function getManagementPermissions()
{
    return [
        'Candidates' => ['index', 'view', 'export'],
        'CandidateDocuments' => ['index', 'view'],
        'Trainees' => ['index', 'view', 'export'],
        'Apprentices' => ['index', 'view', 'export'],
        // ... more controllers
    ];
}
```

### TMM Recruitment (Full Candidate Management)
```php
protected function getRecruitmentPermissions()
{
    return [
        'Candidates' => ['*'], // All actions
        'CandidateDocuments' => ['*'],
        'CandidateEducations' => ['*'],
        'CandidateExperiences' => ['*'],
        'ApprenticeOrders' => ['*'],
        'Apprentices' => ['*'],
        // ... read-only for stakeholders
    ];
}
```

### LPK Users (Institution-Scoped)
```php
protected function getLpkPermissions()
{
    return [
        'Candidates' => ['*'], // With institution filtering
        'CandidateDocuments' => ['*'],
        'CandidateEducations' => ['*'],
        'CandidateExperiences' => ['*'],
        'Dashboard' => ['index'],
    ];
}
```

## Testing the Authorization System

### Test Scenarios

#### 1. **Menu Filtering Test**
- **Login as**: lpk_bekasi (role: lpk-penyangga)
- **Expected**: Only see Candidates, CandidateDocuments menus
- **Not visible**: Trainees, Apprentices, Master Data menus

#### 2. **Unauthorized Access Test**
- **Login as**: lpk_bekasi
- **Try to access**: `/trainees/index`
- **Expected**: 
  - Blocked with detailed error message
  - Shows "lpk-penyangga" role
  - Link to login with different account
  - Redirected to dashboard

#### 3. **Institution Scope Test**
- **Login as**: lpk_bekasi (institution_id = 5)
- **Try to edit**: Candidate with vocational_training_institution_id = 1
- **Expected**:
  - Cannot see record in index (filtered out)
  - If direct URL, blocked with institution error
  - Message: "This record belongs to another institution"

#### 4. **Read-Only Role Test**
- **Login as**: manager123 (role: management)
- **Expected**:
  - Can view all candidates
  - Cannot see Add, Edit, Delete buttons
  - Attempting direct URL for /add or /edit blocked

#### 5. **Administrator Bypass Test**
- **Login as**: admin (role: administrator)
- **Expected**:
  - All menus visible
  - All actions allowed
  - No institution filtering
  - Can access all databases

### Test Credentials

| Username | Password | Role | Institution | Test Case |
|----------|----------|------|-------------|-----------|
| admin | admin123 | administrator | - | Full access |
| manager123 | manager123 | management | - | Read-only |
| recruit123 | recruit123 | tmm-recruitment | - | Candidate management |
| training123 | training123 | tmm-training | - | Trainee management |
| doc123 | doc123 | tmm-documentation | - | Document management |
| lpk_bekasi | lpk123 | lpk-penyangga | 5 | Institution-scoped |
| lpk_semarang | lpk123 | lpk-penyangga | 1 | Institution-scoped |

## Security Logging

All unauthorized access attempts are logged:

```php
$this->log(sprintf(
    'Unauthorized access attempt: User=%s, Role=%s, Action=%s, Controller=%s, Reason=%s',
    $username,
    $userRole,
    $action,
    $controller,
    $reason
), 'warning');
```

**Log Location**: `logs/error.log`

**Example Log Entry**:
```
2025-01-28 10:30:15 Warning: Unauthorized access attempt: User=lpk_bekasi, Role=lpk-penyangga, Action=index, Controller=Trainees, Reason=Your role does not have access to index in Trainees.
```

## Extending the System

### Adding New Role

1. **Create permission method**:
```php
protected function getNewRolePermissions()
{
    return [
        'Controller1' => ['index', 'view'],
        'Controller2' => ['*'], // All actions
    ];
}
```

2. **Update `getRolePermissions()`**:
```php
if ($this->hasRole('new-role-name')) {
    return $this->getNewRolePermissions();
}
```

3. **Add database connection access**:
```php
protected function hasConnectionAccess($connectionName)
{
    $roleConnections = [
        // ... existing roles
        'new-role-name' => ['cms_database1', 'cms_database2', 'cms_masters'],
    ];
}
```

### Adding New Controller Authorization

1. **Add to permission map**:
```php
protected function getRecruitmentPermissions()
{
    return [
        // ... existing
        'NewController' => ['*'], // Or specific actions
    ];
}
```

2. **Add database connection mapping**:
```php
protected function getControllerConnection($controller)
{
    $connectionMap = [
        // ... existing
        'NewController' => 'cms_database_name',
    ];
}
```

3. **Add menu entry** in database:
```sql
INSERT INTO menus (title, controller, action, icon, parent_id, sort_order, is_active)
VALUES ('New Menu', 'NewController', 'index', 'fa-icon', NULL, 10, 1);
```

## Best Practices

1. **Always check institution scope for LPK users**:
```php
if ($this->hasRole('lpk-penyangga')) {
    $institutionId = $this->getUserInstitutionId();
    $query->matching('Candidates', function($q) use ($institutionId) {
        return $q->where(['Candidates.vocational_training_institution_id' => $institutionId]);
    });
}
```

2. **Use permission checks in templates**:
```php
<?php if ($this->request->getSession()->read('Auth.User.role_names')) : ?>
    <?php if (in_array('administrator', $this->request->getSession()->read('Auth.User.role_names'))) : ?>
        <!-- Admin-only content -->
    <?php endif; ?>
<?php endif; ?>
```

3. **Validate before destructive operations**:
```php
public function delete($id = null)
{
    // Check permission
    if (!$this->hasPermission('ControllerName', 'delete')) {
        return $this->handleUnauthorizedAccess('delete');
    }
    
    $entity = $this->Model->get($id);
    
    // Check institution scope
    if (!$this->canAccessRecord($entity, 'institution_field')) {
        $this->Flash->error(__('Cannot delete records from other institutions.'));
        return $this->redirect(['action' => 'index']);
    }
    
    // Proceed with delete...
}
```

4. **Hide buttons for unauthorized actions** in templates:
```php
<?php if ($this->hasPermission('Candidates', 'add')) : ?>
    <?= $this->Html->link(__('Add Candidate'), ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
<?php endif; ?>
```

## Troubleshooting

### Menu Not Showing
- **Check**: Role has permission in `getRolePermissions()`
- **Check**: Menu entry has correct controller name in database
- **Check**: `is_active = 1` for menu
- **Clear cache**: `bin\cake cache clear_all`

### Unauthorized Access Even with Permission
- **Check**: Controller name matches exactly (case-sensitive)
- **Check**: Action name matches exactly
- **Check**: Database connection access granted for role
- **Check**: `isAuthorized()` method in specific controller doesn't override

### Institution Filtering Not Working
- **Check**: Field name matches exactly (e.g., `vocational_training_institution_id`)
- **Check**: User has `institution_id` in session
- **Check**: Association loaded in query (`contain` or `matching`)
- **Check**: Query uses correct field path (e.g., `Candidates.vocational_training_institution_id`)

## Summary

✅ **Menu Filtering**: Menus automatically filtered by role permissions
✅ **Permission System**: Centralized controller/action authorization
✅ **Scope Validation**: Institution-based data filtering for LPK users
✅ **Enhanced Notifications**: Clear error messages with user guidance
✅ **Security Logging**: All unauthorized attempts logged
✅ **Database Scope**: Connection-level access control by role

All authorization rules are centralized in `AppController.php` for easy maintenance and extension.
