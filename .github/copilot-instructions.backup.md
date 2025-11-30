# Copilot Instructions - Asahi Inventory Management System

## Project Overview

**CakePHP 3.9** inventory management system for Asahi with multi-database architecture spanning inventory, personnel, maintenance, vehicles, accounting, and approval workflows. Uses XAMPP on Windows with PowerShell automation.

**Base URL**: `http://localhost/asahi_v3/` or `http://asahi.local/`  
**Primary Layout**: `elegant.ctp` - Purple gradient theme with tab-based navigation  
**Static Assets**: `http://localhost/static-assets/` (OUTSIDE project folder - XAMPP htdocs root)

## Static Assets Location

**CRITICAL**: All CSS and JavaScript files are served from **external static-assets folder** at XAMPP root, NOT from project webroot.

**Folder structure:**
```
d:\xampp\htdocs\
├── asahi_v3\              (CakePHP project)
│   ├── src\
│   ├── webroot\
│   └── ...
└── static-assets\         (External assets - OUTSIDE project)
    ├── css\
    │   ├── fontawesome-all.min.css
    │   ├── actions-table.css
    │   ├── mobile-responsive.css
    │   ├── table-enhanced.css
    │   └── form-styles.css
    ├── js\
    │   ├── ajax-table-filter.js    (Server-side AJAX filtering)
    │   ├── table-filter.js         (Legacy client-side)
    │   ├── context-menu.js
    │   ├── submenu-position.js
    │   ├── table-drag-scroll.js
    │   ├── form-confirm.js
    │   └── image-preview.js
    └── webfonts\
        └── (Font Awesome fonts)
```

**URL Pattern in Templates:**
```php
<!-- ALWAYS use absolute URLs to static-assets -->
<link href="http://localhost/static-assets/css/actions-table.css" rel="stylesheet">
<script src="http://localhost/static-assets/js/ajax-table-filter.js"></script>

<!-- DO NOT use CakePHP Url helper for external assets -->
❌ <?= $this->Url->build('/css/file.css') ?>  // WRONG - looks in webroot
✅ http://localhost/static-assets/css/file.css  // CORRECT - external folder
```

**Bake Template Pattern:**
```php
<!-- In src/Template/Bake/Template/index.ctp -->
<script src="http://localhost/static-assets/js/ajax-table-filter.js"></script>
```

**Layout Pattern (elegant.ctp):**
```php
<link rel="stylesheet" href="http://localhost/static-assets/css/fontawesome-all.min.css">
<link rel="stylesheet" href="http://localhost/static-assets/css/actions-table.css?v=7.0">
<script src="http://localhost/static-assets/js/submenu-position.js?v=1.0"></script>
```

**Why External Assets:**
- Prevents browser tracking prevention blocks
- Shared across multiple projects
- No CakePHP routing overhead
- Direct Apache serving (faster)
- Version control separation

**Deployment**: Copy entire `static-assets` folder to production server root, update URLs to production domain.

## Multi-Database Architecture

The system uses **6 MySQL databases** with separate connections defined in `config/app_datasources.php`:

- `default` (asahi_inventories) - Inventory, stock movements, purchase receipts, accounting transactions
- `personnel` (asahi_common_personnels) - Company organizational structure (companies, departments)
- `common` (asahi_commons) - **Employee records (personnels)**, users, roles, sections, suppliers, shift groups
- `district` (asahi_common_districts) - Indonesian address data (provinces, cities, districts, villages)
- `maintenance` (asahi_maintenance) - Maintenance cards, planned jobs, daily activities, actions
- `vehicle` (asahi_vehicles) - Fleet management, drivers, delivery orders

**CRITICAL DATABASE MAPPING (Updated Nov 2025):**
```php
'personnel' => ['Companies', 'Departments', 'EmployeeStatuses', 'Genders', 
               'LanguageAbilities', 'MaritalStatuses', 'Positions', 'Religions', 
               'Roles', 'Stratas']
'common' => ['Personnels', 'Users', 'Groups', 'Menus', 'Sections', 'Suppliers', 
             'ShiftGroups', 'Languages', 'Titles', 'Shifts', 'Authorizations']
'default' => ['Inventories', 'Storages', 'Uoms', 'StockIncomings', 'StockOutgoings',
              'PurchaseReceipts', 'AccountingTransactions', 'Banks', 'PaymentMethods',
              'ChartOfAccounts', 'Counters']
'maintenance' => ['MaintenanceCards', 'PlannedJobs', 'Actions', 'InsuranceClaims',
                  'JobCategories', 'JobStatuses', 'Priorities', 'TestSmartForms']
'vehicle' => ['Vehicles', 'VehicleTypes', 'Drivers', 'Customers', 'DeliveryOrders']
'district' => ['Propinsis', 'Kabupatens', 'Kecamatans', 'Kelurahans']
'approvals' => ['Approvals', 'ApprovalStatuses', 'ApprovalWorkflows', 'ApprovalMatrix']
```

**Common Misconception**: `asahi_common_personnels` is NOT where employee records live! 
- ✅ Employee records (personnels table) are in `asahi_commons` (common connection)
- ✅ Sections table is in `asahi_commons`, NOT in personnel database
- ⚠️ See `CROSS_DATABASE_FIX_REPORT.md` for complete verified mapping

**Critical**: When generating models/controllers, always specify the connection:
```bash
bin\cake bake model TableName --connection personnel
```

Models must explicitly set connections:
```php
public function initialize(array $config) {
    parent::initialize($config);
    $this->setConnection(ConnectionManager::get('personnel'));
}
```

**Cross-Database Associations**: When associating tables from different databases, MUST use `strategy => 'select'`:
```php
// PersonnelsTable (common database) associating to ShiftGroups (also common)
$this->belongsTo('ShiftGroups', [
    'foreignKey' => 'shift_group_id',
    'strategy' => 'select', // Required for cross-database associations
]);

// PersonnelsTable (common) associating to Departments (personnel database)
$this->belongsTo('Departments', [
    'foreignKey' => 'department_id',
    'strategy' => 'select', // Forces separate SELECT instead of JOIN
]);
```

Without `strategy => 'select'`, CakePHP will try to use LEFT JOIN which fails across databases with error: "Base table or view not found". The `select` strategy forces CakePHP to execute separate SELECT queries instead of attempting a cross-database JOIN.

**Auto-Fix Scripts** (run after baking tables):
```powershell
# Fix connection configuration for personnel tables
.\fix_personnel_connections.ps1

# Add 'strategy' => 'select' to all cross-database associations
.\fix_all_cross_db_associations.ps1

# Verify connections
php verify_personnel_connections.php
```

See `CROSS_DATABASE_FIX_REPORT.md` and `CROSS_DATABASE_BAKE_GUIDE.md` for complete documentation.

## URL Routing Convention

**DashedRoute** is enforced. Multi-word controllers use dashes in URLs:

- ✅ `/purchase-receipts` → `PurchaseReceiptsController`
- ✅ `/stock-incomings` → `StockIncomingsController`
- ❌ `/purchase_receipts` → Error

Always use CakePHP helpers for URLs:
```php
<?= $this->Html->link('View', ['controller' => 'PurchaseReceipts', 'action' => 'view', $id]) ?>
```

## Custom Bake Templates

Located in `src/Template/Bake/` with smart field detection:

**Auto-detected patterns** (in `form.ctp`):
- `*date*`, `*tanggal*` → Bootstrap datepicker
- `*file*`, `*attachment*` → File input (PDF/DOC) with download link
- `*image*`, `*photo*`, `*gambar*` → Image input with thumbnail preview
- `*email*` → Email input with real-time validation
- `*katakana*`, `*hiragana*` → Japanese character input with kana.js
- `*_id` (foreign keys) → Dropdown from associated table
- `TINYINT(1)` → Bootstrap checkbox

**File uploads**: Controllers auto-generated with upload handling (via `Element/Controller/add.twig`):
- Image fields → `webroot/img/uploads/`
- File fields → `webroot/files/uploads/`
- Unique timestamps, old file deletion on edit

**Re-bake command**:
```powershell
bin\cake bake all ModelName --connection default --force
```

## File Upload Standard

Use `AppController::handleFileUploads()` helper method for consistent file handling:

```php
public function add() {
    $data = $this->request->getData();
    $data = $this->handleFileUploads($data, ['image_url', 'file_path']);
    // ... save logic
}
```

Auto-detects folder based on field name (image fields → img/uploads/, others → files/uploads/).

## Approval Workflow System

**Database**: `asahi_online_approvals` with tables:
- `approval_matrix` - Approval scenarios (Standard, Emergency, etc)
- `approval_workflows` - Multi-level rules based on amount + department + position
- `approvals` - Approval records with security tokens (configurable expiry)
- `approval_statuses` - Draft, Pending, Approved, Rejected, Cancelled
- `notification_templates` - Email templates for purchase/usage approvals

**Multilevel Approval Structure** (see `MULTILEVEL_APPROVAL_DESIGN.md`):

**Standard Purchase**:
- L1 (0-10M): Section Head (auto-approve ≤1M)
- L2 (10-50M): Department Manager
- L3 (50-150M): General Manager
- L4 (150-500M): Finance Director + Procurement Director (parallel approval)
- L5 (>500M): CEO

**Emergency Purchase** (fast-track):
- L1 (0-150M): General Manager (skip Supervisor & Manager)
- L2 (>150M): Director

**Key Features**:
- Amount-based thresholds with parallel approval support at L4
- Approval matrix for different scenarios (normal, emergency)
- Auto-approve for small amounts
- Escalation mechanism with configurable timeout days
- Sequential or parallel approval per level

**Integration pattern** (see `PurchaseReceiptsController`, `StockOutgoingsController`):
```php
// After saving entity
$approvalsController = new ApprovalsController();
$approvalsController->submitForApproval(
    'PurchaseReceipt', 
    $entity->id, 
    $entity->purchase_amount,
    $personnelId,
    $matrixId  // 1=Standard, 2=Emergency
);
```

**Stored Procedure for Dynamic Approval Chain**:
```sql
-- Get required approvers based on amount and scenario
CALL sp_get_required_approvers('PurchaseReceipt', 75000000, 1);
-- Returns: approval_level, approver_personnel_id, parallel_approval, escalation_days
```

**Email config**: Gmail SMTP in `config/app.php` line ~218. Requires App Password (not regular password).  
**Approver emails**: Stored in `personnels.contact_email` field.

**Setup**: Run `database/migrations/enhanced_approval_workflows.sql` for full multilevel implementation.

## Table UI Enhancements

All 83+ index pages include:

**Auto-generated table filters** (`/static-assets/js/table-filter.js`):
- Detects all tables, adds filter row automatically
- Real-time search, multi-column filtering
- No backend changes needed

**Hover-based action buttons** - Modern icon buttons that appear on row hover:
```php
<td class="actions">
    <div class="action-buttons-hover">
        <?= $this->Html->link('<i class="fas fa-edit"></i>', 
            ['action' => 'edit', $id], 
            ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false]) ?>
        <?= $this->Form->postLink('<i class="fas fa-trash"></i>', 
            ['action' => 'delete', $id], 
            ['class' => 'btn-action-icon btn-delete-icon', 'escape' => false]) ?>
        <?= $this->Html->link('<i class="fas fa-plus"></i>', 
            ['action' => 'view', $id], 
            ['class' => 'btn-action-icon btn-view-icon', 'escape' => false]) ?>
    </div>
</td>
```

**Button colors**:
- Edit (pencil icon): Orange/Yellow (#f39c12)
- Delete (trash icon): Red (#e74c3c)
- View/Plus icon: Blue (#3498db)
- All buttons have dark background (#2c3e50) with colored hover states

**Behavior**:
- Buttons hidden by default (`opacity: 0`)
- Appear on row hover with smooth transition
- Slide-in animation from left
- Hover on individual button lifts it up with shadow

**Table structure**:
```html
<div class="table-responsive">
    <table class="table">
        <thead><!-- Auto-filter inserts after this --></thead>
        <tbody>...</tbody>
    </table>
</div>
```

**CSS dependencies** (loaded in `elegant.ctp`):
- `/static-assets/css/actions-table.css` - Tables, buttons, filters
- `/static-assets/css/mobile-responsive.css` - Breakpoint at 768px
- `/static-assets/css/fontawesome-all.min.css` - Icons

## Menu System

Database-driven navigation from `asahi_commons.menus` table with hierarchical structure (parent_id, position).

Rendered in `src/Template/Element/elegant_menu.ctp` - Tab-based with click-to-expand submenus.

Auto-loaded in `AppController::beforeRender()`:
```php
$menus = $menusTable->find('threaded')
    ->where(['parent_id IS' => null, 'is_active' => 1])
    ->contain(['ChildMenus'])
    ->all();
$this->set('navigationMenus', $menus);
```

Manage at: `http://localhost/asahi_v3/menus`

## PowerShell Automation Scripts

Located in project root. Common workflows:

**Bake all 93 tables**: `.\bake_all_real.ps1`  
**Fix UI regressions**: `.\fix_all_issues.ps1` (removes old CSS/JS refs, fixes button classes)  
**Verify fixes**: `.\verify_fixes.ps1`  
**Copy config**: `.\copy_config.ps1` (syncs app_datasources.php to app.php)

When modifying templates globally, create PowerShell scripts for atomic batch updates.

## Development Workflow

**Start server**:
```powershell
cd d:\xampp\htdocs\asahi_v3
bin\cake server -p 8765
```

**Clear cache** after config changes:
```powershell
bin\cake cache clear_all
```

**Test emails**:
```powershell
php test_email.php
```

**Database connections** are pooled. If connection errors occur, restart Apache or use `ConnectionManager::drop()`.

## Common Pitfalls

1. **Don't hardcode localhost URLs** - Use `$this->Url->build()` for portability
2. **Don't mix connections** - Models from different DBs require manual joins (use queries, not ORM associations)
3. **Don't skip connection parameter** when baking - Defaults to `default`, causing wrong DB
4. **Don't use underscores in URLs** - Use dashes for multi-word controllers
5. **Don't forget multipart forms** - Add `['type' => 'file']` to Form::create() for uploads
6. **Don't commit passwords** - Use `config/app_local.php` for sensitive data (already in .gitignore)

## Key Documentation Files

- `QUICK_REFERENCE.md` - Table filters, buttons, mobile CSS
- `BAKE_QUICK_REFERENCE.md` - Custom templates, field patterns
- `ROUTING_GUIDE.md` - DashedRoute examples
- `QUICK_START_APPROVAL.md` - Approval workflow setup
- `FILE_UPLOAD_PRACTICAL_GUIDE.md` - Upload helper usage
- `ELEGANT_MENU_DOCUMENTATION.md` - Menu system details

## Testing Checklist

Before committing UI changes:
- [ ] Test on Chrome/Edge/Firefox
- [ ] Hard reload (Ctrl+Shift+R) to clear cache
- [ ] Check F12 console for 404 errors
- [ ] Test mobile view (resize to 768px)
- [ ] Verify table filters auto-appear
- [ ] Confirm gradient buttons display correctly
- [ ] Ensure no inline styles override global CSS

## Code Style Preferences

- Use `AppController` helper methods for common tasks (uploads, redirects)
- Keep controllers thin - move business logic to Table classes
- Use CakePHP 3.x conventions (not 4.x syntax)
- Flash messages: `$this->Flash->success()` / `->error()` / `->warning()`
- Log important actions: `$this->log("Message", 'info|error|debug')`
- Prefer `find('all')` with conditions over custom SQL
- Always contain associations in queries to avoid N+1

## Authentication & Authorization

**Current State**: Basic user management without authentication middleware.

- User records stored in `common` database (`asahi_commons.users` table)
- Personnel records in `personnel` database (`asahi_common_personnels.personnels` table)
- Session handling via `AppController::beforeRender()` (TODOs for personnel_id in `ApprovalsController`)
- No Auth component configured - authentication is **pending implementation**

**Pattern for future auth**:
```php
// In AppController::initialize()
$this->loadComponent('Auth', [
    'authenticate' => [
        'Form' => [
            'fields' => ['username' => 'email', 'password' => 'password']
        ]
    ],
    'loginAction' => ['controller' => 'Users', 'action' => 'login'],
    'unauthorizedRedirect' => $this->referer()
]);
```

**Role-based access**: `users` table has associations to `roles`, `groups`, `departments` - use for authorization when implementing.

## Internationalization (i18n)

**Language Support**: Indonesian and Japanese

- Use `__()` function for all user-facing strings
- Japanese input fields detected by name patterns: `*katakana*`, `*hiragana*`
- Custom bake templates auto-add kana.js for Japanese fields
- Date/time uses `Cake\I18n\FrozenTime` and `FrozenDate` entities
- Locale setting in `config/app.php`: `'defaultLocale' => 'en_US'`

**Japanese field example**:
```php
// Auto-generated by bake for field: name_katakana
<?= $this->Form->control('name_katakana', [
    'class' => 'form-control kana-input',
    'data-kana' => 'katakana'
]) ?>
<script src="/js/kana.js"></script>
```

**Translation pattern**:
```php
// In views
<?= __('Purchase Receipt') ?>
<?= __('The {0} has been saved.', __('inventory')) ?>

// In controllers
$this->Flash->success(__('The user has been saved.'));
```

## AJAX & API Patterns

**AjaxFilterTrait** provides JSON endpoints for dynamic filtering:

Located in `src/Controller/AjaxFilterTrait.php`, used by inventory-related controllers.

**Usage in controllers**:
```php
class InventoriesController extends AppController {
    use AjaxFilterTrait;
    
    public function index() {
        // Handle AJAX filter request
        if ($this->request->is('ajax') || $this->request->getQuery('ajax')) {
            return $this->handleAjaxFilter();
        }
        // Normal pagination
    }
}
```

**Response format**:
```json
{
    "success": true,
    "html": "<tr>...</tr>",
    "count": 15,
    "total": 100
}
```

**AJAX requests**:
- Content-Type: `application/json`
- Query params: `?ajax=1&filter[field]=value`
- Returns pre-rendered HTML rows (not just data)
- Used by client-side table filtering

**No REST API**: This is a traditional server-rendered app. AJAX is limited to table filtering.

## Testing Strategy

**Framework**: PHPUnit 5/6 (see `composer.json`)

**Test Structure**:
```
tests/
├── bootstrap.php
├── Fixture/           # Database fixtures for tests
└── TestCase/
    ├── Controller/    # Controller integration tests
    └── Model/Table/   # Table/Entity unit tests
```

**Fixture Pattern** (see `tests/Fixture/PlannedJobOwnersFixture.php`):
- Extends `Cake\TestSuite\Fixture\TestFixture`
- Defines schema in `$fields` array
- Sample data in `init()` method
- Auto-loaded via `public $fixtures = ['app.PlannedJobOwners']`

**Multi-database fixtures**: Must specify connection in fixture:
```php
public $connection = 'personnel'; // For non-default databases
```

**Running tests**:
```powershell
# All tests
vendor\bin\phpunit

# Specific test
vendor\bin\phpunit tests\TestCase\Controller\InventoriesControllerTest.php

# With coverage
vendor\bin\phpunit --coverage-html coverage/
```

**Current status**: Fixtures generated by bake, but most test methods are empty stubs. **Tests are not actively maintained.**

## Production Deployment

**Pre-deployment checklist** (from `QUICK_REFERENCE.md`):

1. **Update static asset URLs** in `src/Template/Layout/elegant.ctp`:
   ```php
   // Change from:
   <link href="http://localhost/static-assets/css/actions-table.css">
   // To production URL:
   <link href="https://yourdomain.com/static-assets/css/actions-table.css">
   ```

2. **Copy `/static-assets/` folder** to production server (currently separate from webroot)

3. **Update `config/app.php`**:
   - Set `'debug' => false`
   - Update database credentials
   - Change `'App.base'` from `/asahi_v3` to production path
   - Update Gmail SMTP credentials for approvals

4. **Environment-specific config**: Use `config/app_local.php` (gitignored) for:
   - Database passwords
   - SMTP credentials
   - API keys

5. **Performance optimizations**:
   ```powershell
   # Enable route caching
   bin\cake cache clear_all
   
   # Minify assets (manual - no build tools configured)
   # Consider CDN for static-assets/
   ```

6. **Apache configuration**:
   - Update VirtualHost in `httpd-vhosts.conf`
   - Remove `localhost` as first VirtualHost to avoid conflicts
   - Enable mod_rewrite, mod_ssl

7. **Run regression check**:
   ```powershell
   .\check_regressions.ps1
   ```

**No CI/CD configured**. `.travis.yml` exists but is default CakePHP template - not customized for this project.

**Database migrations**: Not used. Schema changes via direct SQL. Backup databases before deployment.
