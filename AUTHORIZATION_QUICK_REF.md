# Authorization System - Quick Reference Card

## 🔐 Key Features

| Feature | Status | Location |
|---------|--------|----------|
| **Menu Filtering by Role** | ✅ | `AppController::beforeRender()` |
| **Permission Checking** | ✅ | `AppController::hasPermission()` |
| **Data Scope Validation** | ✅ | `AppController::canAccessRecord()` |
| **Enhanced Notifications** | ✅ | `AppController::handleUnauthorizedAccess()` |
| **Database Scope Control** | ✅ | `AppController::hasConnectionAccess()` |

---

## 🚀 Quick Usage

### Check Permission in Controller
```php
// In controller action
if (!$this->hasPermission('ControllerName', 'actionName')) {
    return $this->handleUnauthorizedAccess('actionName');
}
```

### Validate Institution Scope
```php
// Before edit/delete
$entity = $this->Model->get($id, ['contain' => ['Candidates']]);
if (!$this->canAccessRecord($entity->candidate, 'vocational_training_institution_id')) {
    $this->Flash->error(__('Access Denied: Wrong institution.'));
    return $this->redirect(['action' => 'index']);
}
```

### Filter Query by Institution
```php
// In index/list actions for LPK users
if ($this->hasRole('lpk-penyangga')) {
    $institutionId = $this->getUserInstitutionId();
    $query->matching('Candidates', function($q) use ($institutionId) {
        return $q->where(['Candidates.vocational_training_institution_id' => $institutionId]);
    });
}
```

### Hide Buttons in Templates
```php
<?php if ($this->hasPermission('Candidates', 'add')) : ?>
    <?= $this->Html->link(__('Add'), ['action' => 'add'], ['class' => 'btn btn-success']) ?>
<?php endif; ?>
```

---

## 📋 Role Permissions Matrix

| Role | Candidates | Trainees | Apprentices | Documents | Scope |
|------|-----------|----------|-------------|-----------|-------|
| **administrator** | ✅ All | ✅ All | ✅ All | ✅ All | ALL |
| **management** | 👁️ View | 👁️ View | 👁️ View | 👁️ View | ALL |
| **tmm-recruitment** | ✅ All | ❌ | ✅ All | ✅ Candidate | ALL |
| **tmm-training** | 👁️ View | ✅ All | ❌ | ✅ Trainee | ALL |
| **tmm-documentation** | 👁️ View | 👁️ View | 👁️ View | ✅ All | ALL |
| **lpk-penyangga** | ✅ All | ❌ | ❌ | ✅ Candidate | 🏢 Institution |
| **lpk-so** | ✅ All | ❌ | ❌ | ✅ Candidate | 🏢 Institution |

**Legend**: ✅ Full Access | 👁️ Read-Only | ❌ No Access | 🏢 Scoped

---

## 🎯 Test Credentials

| Username | Password | Role | Institution | Use For |
|----------|----------|------|-------------|---------|
| admin | admin123 | administrator | - | Full access test |
| manager123 | manager123 | management | - | Read-only test |
| recruit123 | recruit123 | tmm-recruitment | - | Candidate mgmt |
| training123 | training123 | tmm-training | - | Trainee mgmt |
| doc123 | doc123 | tmm-documentation | - | Document mgmt |
| lpk_bekasi | lpk123 | lpk-penyangga | 5 | Institution scope |
| lpk_semarang | lpk123 | lpk-penyangga | 1 | Institution scope |

---

## 📝 Adding New Role Permissions

### Step 1: Create Permission Method
```php
// In AppController.php
protected function getNewRolePermissions()
{
    return [
        'Controller1' => ['*'], // All actions
        'Controller2' => ['index', 'view'], // Specific actions
    ];
}
```

### Step 2: Register in getRolePermissions()
```php
protected function getRolePermissions()
{
    if ($this->hasRole('new-role-name')) {
        return $this->getNewRolePermissions();
    }
    // ... other roles
}
```

### Step 3: Add Database Access
```php
protected function hasConnectionAccess($connectionName)
{
    $roleConnections = [
        'new-role-name' => ['cms_database1', 'cms_database2', 'cms_masters'],
    ];
}
```

### Step 4: Clear Cache
```bash
bin\cake cache clear_all
```

---

## 🔍 Debugging

### Check Current User Permissions
```php
// In controller
debug($this->currentUser);
debug($this->getAllowedControllers());
debug($this->getRolePermissions());
```

### Check Menu Filtering
```php
// In beforeRender() temporarily add:
debug($allowedControllers);
debug($menus->toArray());
```

### Check Logs
```bash
# View unauthorized attempts
tail -f logs/error.log | grep "Unauthorized access"
```

---

## ⚠️ Common Issues

| Problem | Solution |
|---------|----------|
| Menu not showing | Check controller name in menu table matches permission map |
| "Access Denied" incorrectly | Clear cache: `bin\cake cache clear_all` |
| Institution filter not working | Ensure field name is exact: `vocational_training_institution_id` |
| Can't edit own records | Check `canAccessRecord()` field parameter matches table field |

---

## 📞 Key Methods Reference

| Method | Purpose | Returns |
|--------|---------|---------|
| `hasRole($name)` | Check if user has role | bool |
| `hasPermission($ctrl, $act)` | Check controller/action access | bool |
| `canAccessRecord($entity, $field)` | Validate institution scope | bool |
| `getUserInstitutionId()` | Get user's institution | int/null |
| `getAllowedControllers()` | Get accessible controllers | array |
| `handleUnauthorizedAccess($action, $reason)` | Show error & redirect | Response |

---

## 🎨 Template Helpers

### Show/Hide Elements by Permission
```php
<?php if ($this->request->getSession()->check('Auth.User')) : ?>
    <?php $user = $this->request->getSession()->read('Auth.User'); ?>
    
    <?php if (in_array('administrator', $user['role_names'])) : ?>
        <!-- Admin only -->
    <?php endif; ?>
    
    <?php if (in_array('lpk-penyangga', $user['role_names'])) : ?>
        <!-- LPK only -->
    <?php endif; ?>
<?php endif; ?>
```

### Get Current User Info
```php
<?php
$username = $this->request->getSession()->read('Auth.User.username');
$roles = $this->request->getSession()->read('Auth.User.role_names');
$institutionId = $this->request->getSession()->read('Auth.User.institution_id');
?>
```

---

## ✅ Implementation Checklist

When adding new feature:

- [ ] Add permission to role permission method
- [ ] Add controller to database connection map (if new table)
- [ ] Add menu entry to database (with correct controller name)
- [ ] Apply institution filtering for LPK roles
- [ ] Hide unauthorized buttons in templates
- [ ] Test with all relevant roles
- [ ] Clear cache after changes
- [ ] Check security logs for errors

---

## 📚 Full Documentation

See **`AUTHORIZATION_SYSTEM_COMPLETE.md`** for:
- Detailed implementation guide
- Authorization flow diagrams
- All permission maps
- Security logging details
- Troubleshooting guide
- Extension examples
