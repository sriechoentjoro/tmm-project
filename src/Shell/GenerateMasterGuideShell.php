<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;

/**
 * Generate Master Guide Shell
 * 
 * Auto-generates the MASTER_BAKE_GUIDE.md from live database detection
 * Consolidates all baking documentation into one trusted reference
 * 
 * Usage: bin/cake generate_master_guide
 */
class GenerateMasterGuideShell extends Shell
{
    protected function getConfiguredConnections()
    {
        $connections = [];
        
        try {
            $configuredConnections = ConnectionManager::configured();
            
            foreach ($configuredConnections as $name) {
                if (strpos($name, 'test') !== false || strpos($name, 'debug') !== false) {
                    continue;
                }
                
                try {
                    $connection = ConnectionManager::get($name);
                    $config = $connection->config();
                    $dbName = isset($config['database']) ? $config['database'] : $name;
                    $connections[$name] = $dbName;
                } catch (\Exception $e) {
                    // Skip
                }
            }
        } catch (\Exception $e) {
            $connections = ['default' => 'default'];
        }
        
        // Remove duplicates
        $dbToConnections = [];
        foreach ($connections as $name => $dbName) {
            if (!isset($dbToConnections[$dbName])) {
                $dbToConnections[$dbName] = $name;
            }
        }
        
        $uniqueConnections = [];
        foreach ($dbToConnections as $dbName => $name) {
            $uniqueConnections[$name] = $dbName;
        }
        
        return $uniqueConnections;
    }

    public function main()
    {
        $this->out("");
        $this->out("Generating Master Bake Guide from live database...");
        $this->out("");
        
        // Get live database connections
        $connections = $this->getConfiguredConnections();
        
        // Collect table counts
        $tablesByConnection = [];
        $totalTables = 0;
        
        foreach ($connections as $connName => $dbName) {
            try {
                $connection = ConnectionManager::get($connName);
                $tables = $connection->getSchemaCollection()->listTables();
                $tablesByConnection[$connName] = [
                    'database' => $dbName,
                    'count' => count($tables),
                    'tables' => $tables
                ];
                $totalTables += count($tables);
            } catch (\Exception $e) {
                $tablesByConnection[$connName] = [
                    'database' => $dbName,
                    'count' => 0,
                    'tables' => []
                ];
            }
        }
        
        // Generate markdown content
        $content = $this->generateMarkdown($connections, $tablesByConnection, $totalTables);
        
        // Write to file
        $filePath = ROOT . DS . 'MASTER_BAKE_GUIDE.md';
        file_put_contents($filePath, $content);
        
        $this->out("<success>Master Bake Guide generated successfully!</success>");
        $this->out("Location: MASTER_BAKE_GUIDE.md");
        $this->out("");
        $this->out("Next steps:");
        $this->out("  1. Review the guide: MASTER_BAKE_GUIDE.md");
        $this->out("  2. Use unified script: .\\bake_smart.ps1 -TableName YourTable");
        $this->out("");
    }
    
    protected function generateMarkdown($connections, $tablesByConnection, $totalTables)
    {
        $date = date('Y-m-d H:i:s');
        
        $md = <<<MD
# Master Bake Guide - Asahi Inventory Management System
**Auto-Generated:** {$date}

**⚠️ This document is AUTO-GENERATED from live database. Do not edit manually.**  
**Regenerate:** `bin\\cake generate_master_guide`

---

## Quick Start

### Before Baking ANY Table:
```powershell
# 1. Check what tabs are needed
Select-String -Path DATABASE_ASSOCIATIONS_REFERENCE.md -Pattern "Table: \`your_table\`" -Context 20

# 2. Bake with auto-detection (recommended)
.\\bake_smart.ps1 -TableName YourTable

# 3. Verify in browser
```

---

## Live Database Structure

**Detected on:** {$date}

### Connection Summary
| # | Connection | Database | Tables | Status |
|---|------------|----------|--------|--------|

MD;

        $index = 1;
        foreach ($tablesByConnection as $connName => $info) {
            $status = $info['count'] > 0 ? '✅ Active' : '❌ Empty';
            $md .= "| {$index} | `{$connName}` | `{$info['database']}` | {$info['count']} | {$status} |\n";
            $index++;
        }
        
        $totalConn = count($connections);
        
        $md .= <<<MD


**Summary:**
- **Total Connections:** {$totalConn}
- **Total Tables:** {$totalTables}
- **Auto-Detected:** ✅ All connections dynamically loaded

---

## Database Connection Details


MD;

        foreach ($tablesByConnection as $connName => $info) {
            $dbName = $info['database'];
            $count = $info['count'];
            $tables = $info['tables'];
            
            $md .= <<<MD
### Connection: `{$connName}`
- **Database:** `{$dbName}`
- **Tables:** {$count}


MD;
            
            if ($count > 0) {
                $md .= "**Tables:**\n";
                foreach ($tables as $table) {
                    $modelName = Inflector::camelize($table);
                    $tablePath = ROOT . DS . 'src' . DS . 'Model' . DS . 'Table' . DS . $modelName . 'Table.php';
                    $status = file_exists($tablePath) ? '✅' : '❌ Needs baking';
                    $md .= "- `{$table}` → `{$modelName}` {$status}\n";
                }
                $md .= "\n";
            } else {
                $md .= "_No tables found_\n\n";
            }
            
            $md .= "---\n\n";
        }
        
        $md .= <<<'MD'
## Baking Procedure (Step-by-Step)

### Step 1: Check Associations Reference (MANDATORY)

**Before baking, ALWAYS check what associations exist:**

```powershell
# Generate/update associations reference
bin\cake show_associations 2>$null > DATABASE_ASSOCIATIONS_REFERENCE.md

# Find your table
Select-String -Path DATABASE_ASSOCIATIONS_REFERENCE.md -Pattern "Table: `your_table`" -Context 20
```

**Look for:**
- **BelongsTo** → Parent records to show in view header
- **HasMany** ✨ → **Each one needs a TAB in view template**
- **BelongsToMany** ✨ → **Each one needs a TAB in view template**
- **Foreign Keys** → Exact column names (e.g., `payment_method_id`)

### Step 2: Use Unified Bake Script

**Recommended Method (Auto-everything):**
```powershell
.\bake_smart.ps1 -TableName YourTable

# This automatically:
# ✅ Detects database connection
# ✅ Runs bake command
# ✅ Fixes cross-database associations
# ✅ Generates view tabs from hasMany
# ✅ Applies purple gradient theme
# ✅ Adds table filters & action buttons
# ✅ Configures file uploads
# ✅ Adds export buttons
# ✅ Clears cache
# ✅ Verifies success
```

**Manual Method (if needed):**
```powershell
# 1. Detect connection from reference
$connection = "default"  # Or personnel, common, etc.

# 2. Bake
bin\cake bake all YourTable --connection $connection --force

# 3. Post-bake fixes
.\bake_smart.ps1 -TableName YourTable -FixOnly
```

### Step 3: Verify Generated Files

```powershell
# Check model exists
Test-Path src\Model\Table\YourTableTable.php

# Check controller exists
Test-Path src\Controller\YourTablesController.php

# Check views exist
Test-Path src\Template\YourTables\index.ctp
Test-Path src\Template\YourTables\view.ctp
Test-Path src\Template\YourTables\add.ctp
Test-Path src\Template\YourTables\edit.ctp
```

### Step 4: Browser Testing

1. Navigate to `/your-tables`
2. Check table filters work
3. Test export buttons (CSV, Excel, PDF)
4. Click "Add" and verify form fields
5. Add test record
6. **View record and verify TABS appear for each hasMany association**
7. Test edit and delete

---

## Style & Features (Auto-Applied)

### UI Theme
- **Primary Gradient:** `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- **Layout:** `elegant.ctp` with database-driven menu
- **Static Assets:** `http://localhost/static-assets/` (external)
- **Icons:** Font Awesome 5+ (via CDN or local)

### Auto-Detected Form Fields

The bake templates auto-detect field types:

| Pattern | Field Type | Behavior |
|---------|-----------|----------|
| `*date*`, `*tanggal*` | Date | Bootstrap datepicker |
| `*file*`, `*attachment*` | File | Upload with download link |
| `*image*`, `*photo*`, `*gambar*` | Image | Upload + thumbnail + watermark |
| `*email*` | Email | Email validation |
| `*katakana*`, `*hiragana*` | Text | Japanese input (kana.js) |
| `*_id` | Select | Dropdown from association |
| `TINYINT(1)` | Checkbox | Bootstrap checkbox |
| `TEXT`, `LONGTEXT` | Textarea | Auto-resize |

### Index Page Features
- ✅ Auto-filter row (all columns searchable)
- ✅ Export buttons: CSV, Excel, PDF, Print
- ✅ Hover action buttons: Edit (orange), Delete (red), View (blue)
- ✅ Related models dropdown menu
- ✅ Mobile responsive tables
- ✅ Pagination with result counter

### View Page Features (Association-Driven)

**Parent Records (BelongsTo):**
- Shown in header section
- Linked to parent record view page
- Example: Supplier → Payment Method, Bank

**Child Records (HasMany) - AUTO-GENERATES TABS:**
```php
<!-- Auto-generated for each hasMany association -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#child-records">
            Child Records (<?= count($entity->child_records) ?>)
        </a>
    </li>
</ul>

<div class="tab-content">
    <div id="child-records" class="tab-pane fade show active">
        <!-- Related records table -->
        <!-- Add button with parent_id preset -->
    </div>
</div>
```

**Example:** Supplier view page:
- Header shows: Payment Method, Bank (belongsTo)
- Tab shows: Purchase Receipts list (hasMany)

---

## File Upload Handling

### Auto-Detection
Upload fields auto-detected by name pattern:
- `*image*`, `*photo*`, `*gambar*` → Image upload
- `*file*`, `*attachment*` → File upload

### Upload Locations
- **Images:** `webroot/img/uploads/{controller}/`
- **Files:** `webroot/files/uploads/{controller}/`

### Image Processing
```php
// Auto-applied by AppController::uploadImage()
- Resize to max 800x800px (maintains aspect ratio)
- Add watermark (logo.png) at bottom-right
- 30% opacity watermark
- Unique filename with random code
```

### Form Configuration
```php
// Auto-added to form
<?= $this->Form->create($entity, ['type' => 'file']) ?>
```

---

## Export Functionality

### Available Formats
1. **CSV** - UTF-8 with BOM (Excel compatible)
2. **Excel** - True .xlsx via PhpSpreadsheet
3. **PDF** - Print-optimized layout
4. **Print** - Browser print dialog

### Export Features
- ✅ Purple gradient headers (matches theme)
- ✅ Auto-filter enabled (Excel)
- ✅ Auto-sized columns (Excel)
- ✅ Nested field support (e.g., `supplier.name`)
- ✅ Respects active table filters

### Export Methods (Auto-Generated)
```php
// In controller
public function exportCsv() { }
public function exportExcel() { }
public function exportPdf() { }
public function printReport() { }
```

---

## Cross-Database Associations

### Auto-Detection
The system automatically detects when associations cross databases:
- Compares table locations across all connections
- Marks cross-DB associations with 🔗 in reference

### Auto-Fix
```php
// Automatically adds to associations
'strategy' => 'select'

// This forces separate SELECT queries instead of JOIN
// Required for cross-database relationships in MySQL
```

### Example
```php
// PersonnelsTable (personnel DB)
$this->belongsTo('Sections', [
    'foreignKey' => 'section_id',
    'strategy' => 'select'  // Auto-added because Sections in common DB
]);
```

---

## Unified PowerShell Script

### bake_smart.ps1 - All-in-One Baking

**Location:** `bake_smart.ps1` (project root)

**Features:**
- ✅ Auto-detects database connection
- ✅ Reads associations from reference
- ✅ Bakes Model + Controller + Views
- ✅ Fixes cross-database associations
- ✅ Generates view tabs from hasMany
- ✅ Applies UI theme
- ✅ Adds filters and export buttons
- ✅ Clears cache
- ✅ Verifies all files created
- ✅ Reports errors

**Usage:**
```powershell
# Standard bake (auto-everything)
.\bake_smart.ps1 -TableName Suppliers

# Force re-bake
.\bake_smart.ps1 -TableName Suppliers -Force

# Specify connection manually
.\bake_smart.ps1 -TableName Suppliers -Connection default

# Fix existing bake only (no re-bake)
.\bake_smart.ps1 -TableName Suppliers -FixOnly

# Dry run (show what would happen)
.\bake_smart.ps1 -TableName Suppliers -WhatIf
```

**Script consolidates these old scripts:**
- ❌ `bake_all_real.ps1`
- ❌ `fix_personnel_connections.ps1`
- ❌ `fix_all_cross_db_associations.ps1`
- ❌ `apply_theme.ps1`
- ❌ `add_view_tabs.ps1`
- ✅ **All replaced by:** `bake_smart.ps1`

---

## Common Patterns

### Pattern 1: Simple Table (No Associations)
```powershell
# Example: uoms table
.\bake_smart.ps1 -TableName Uoms

# Result:
# - Standard CRUD
# - No tabs (no hasMany)
# - No cross-DB fixes needed
```

### Pattern 2: Parent Table (HasMany)
```powershell
# Example: Suppliers table
.\bake_smart.ps1 -TableName Suppliers

# Auto-detects:
# - BelongsTo: PaymentMethods, Banks
# - HasMany: PurchaseReceipts
# 
# Auto-generates:
# - View header: Payment Method, Bank links
# - View tab: Purchase Receipts list
```

### Pattern 3: Child Table (BelongsTo)
```powershell
# Example: PurchaseReceipts table
.\bake_smart.ps1 -TableName PurchaseReceipts

# Auto-detects:
# - BelongsTo: Supplier
# - HasMany: PurchaseReceiptItems
#
# Auto-generates:
# - View header: Supplier link
# - View tab: Purchase Receipt Items list
```

### Pattern 4: Cross-Database
```powershell
# Example: Personnels table (references across DBs)
.\bake_smart.ps1 -TableName Personnels

# Auto-detects:
# - Connection: personnel
# - Cross-DB: Sections (in common), Districts (in district)
#
# Auto-applies:
# - strategy => 'select' for all cross-DB associations
```

---

## Troubleshooting

### Issue: Tabs not appearing in view

**Cause:** hasMany associations not defined in Model

**Solution:**
```powershell
# 1. Check associations reference
Select-String -Path DATABASE_ASSOCIATIONS_REFERENCE.md -Pattern "Table: `your_table`" -Context 20

# 2. Verify Model has hasMany
Get-Content src\Model\Table\YourTableTable.php | Select-String "hasMany"

# 3. Re-bake if missing
.\bake_smart.ps1 -TableName YourTable -Force
```

### Issue: Wrong database connection

**Cause:** Manual connection override incorrect

**Solution:**
```powershell
# Let script auto-detect
.\bake_smart.ps1 -TableName YourTable
# (Remove -Connection parameter)
```

### Issue: Cross-database errors

**Cause:** Missing `strategy => 'select'`

**Solution:**
```powershell
# Re-run bake_smart (includes fix)
.\bake_smart.ps1 -TableName YourTable -Force

# Or fix only
.\bake_smart.ps1 -TableName YourTable -FixOnly
```

### Issue: Static assets not loading

**Cause:** Wrong URL or missing folder

**Solution:**
```powershell
# 1. Check folder exists
Test-Path D:\xampp\htdocs\static-assets

# 2. Verify URLs in layout
Select-String -Path src\Template\Layout\elegant.ctp -Pattern "static-assets"

# Should be: http://localhost/static-assets/
```

---

## Update Workflow

### When to Regenerate This Guide

Regenerate when:
- ✅ New database connection added
- ✅ New tables created
- ✅ Associations modified
- ✅ Before major baking session
- ✅ When onboarding new developers

### How to Regenerate

```powershell
# Full update (recommended)
bin\cake generate_master_guide

# This updates:
# - MASTER_BAKE_GUIDE.md (this file)
# - DATABASE_ASSOCIATIONS_REFERENCE.md (via show_associations)
# - Connection mappings
# - Table counts
# - All code examples
```

---

## Best Practices Checklist

### Before Baking
- [ ] Run `bin\cake show_associations` to update reference
- [ ] Check `DATABASE_ASSOCIATIONS_REFERENCE.md` for your table
- [ ] Note all hasMany associations (= tabs needed)
- [ ] Identify cross-database associations (= need strategy fix)

### During Baking
- [ ] Use `.\bake_smart.ps1` (not raw bake command)
- [ ] Let script auto-detect connection
- [ ] Review console output for errors
- [ ] Verify all files generated

### After Baking
- [ ] Clear cache: `bin\cake cache clear_all`
- [ ] Test in browser (all CRUD operations)
- [ ] Verify tabs appear for hasMany
- [ ] Test filters and exports
- [ ] Check cross-DB associations work

### Before Committing
- [ ] No PHP errors in console
- [ ] Browser console clean (no 404s)
- [ ] All tests pass
- [ ] Code review (associations, tabs, theme)

---

## Quick Reference

### Key Commands
```powershell
# Update all references
bin\cake generate_master_guide

# Check associations
bin\cake show_associations 2>$null | more

# Bake table (recommended)
.\bake_smart.ps1 -TableName YourTable

# Manual bake (if needed)
bin\cake bake all YourTable --connection <conn> --force

# Clear cache
bin\cake cache clear_all

# List all tables
bin\cake list_tables 2>$null
```

### Key Files
- `MASTER_BAKE_GUIDE.md` - This guide (auto-generated)
- `DATABASE_ASSOCIATIONS_REFERENCE.md` - All associations (auto-generated)
- `bake_smart.ps1` - Unified bake script
- `src/Template/Bake/` - Bake templates (customize if needed)
- `config/app.php` - Database connections

### Key URLs
- Index: `/your-tables`
- Add: `/your-tables/add`
- View: `/your-tables/view/{id}`
- Edit: `/your-tables/edit/{id}`
- Export CSV: `/your-tables/export-csv`
- Export Excel: `/your-tables/export-excel`
- Export PDF: `/your-tables/export-pdf`

---

**End of Master Bake Guide**

**This guide is auto-generated from live database.**  
**Regenerate:** `bin\cake generate_master_guide`  
**Last Generated:** {$date}

MD;

        return $md;
    }
}
