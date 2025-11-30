# 🎯 MASTER BAKE_GUIDE - CMS Multi-Database System
**Complete Reference for CakePHP 3.9 Baking with Multi-Database Architecture (CMS Edition)**

Last Updated: November 15, 2025  
Version: 3.0 ⭐ Synced to config/app_datasources.php

---

> ## ⚠️ IMPORTANT: READ THIS BEFORE EVERY BAKE!
> 
> **This is the SINGLE SOURCE OF TRUTH for all baking operations.**
> 
> **ALWAYS follow this workflow:**
> ```powershell
> # 1. Read relevant sections in this guide first
> # 2. Run bake command with correct --connection
> # 3. IMMEDIATELY run post_bake_fix.ps1 (fixes all cross-DB issues)
> # 4. Test in browser with hard reload (Ctrl+Shift+R)
> ```
> 
> **⭐ KEY RULES:**
> - ✅ Each table exists in ONE database only (no duplication!)
> - ✅ Use `fix_all_cross_db_associations.ps1` for cross-DB references
> - ✅ Always run `post_bake_fix.ps1` after baking
> - ✅ Table-to-connection mapping must match `config/app_datasources.php`
> 
> **📖 Jump to:** [Quick Start](#quick-start-workflow) | [Cross-DB Principle](#cross-database-reference-principle) | [Troubleshooting](#common-issues--solutions)

---

## 📋 TABLE OF CONTENTS

1. [Quick Start Workflow](#quick-start-workflow)
2. [Multi-Database Architecture](#multi-database-architecture)
3. [Bake Template Reference](#bake-template-reference)
4. [Table UX Enhancements (Nov 2025)](#table-ux-enhancements-nov-2025) ⭐ NEW
5. [Post-Bake Automation Scripts](#post-bake-automation-scripts)
6. [Cross-Database Reference Principle](#cross-database-reference-principle)
7. [Common Issues & Solutions](#common-issues--solutions)
8. [File Upload System](#file-upload-system)
9. [Export Features](#export-features)
10. [Static Assets Structure](#static-assets-structure)
11. [Testing Checklist](#testing-checklist)
12. [Production Deployment](#production-deployment)
13. [FAQ](#frequently-asked-questions)
14. [How to Use This Guide](#how-to-use-this-guide)

---

## 🚀 QUICK START WORKFLOW

> **💡 TL;DR Version:** See **BAKE_QUICK_REMINDER.md** for a printable 1-page cheat sheet!
>
> **⭐ KEY PRINCIPLE**: Each table exists in ONE database only. Use `fix_all_cross_db_associations.ps1` to add `'strategy' => 'select'` for cross-database references. **DO NOT duplicate tables!**
>
> See [Cross-Database Reference Principle](#cross-database-reference-principle) for detailed explanation.

### Standard Bake Process (5 Steps)

```powershell
# Step 1: Bake the table/controller/views
bin\cake bake all TableName --connection <connection> --force

# Step 2: Run post-bake automation (fixes all issues automatically)
powershell -ExecutionPolicy Bypass -File post_bake_fix.ps1
#          ↑ This runs fix_all_cross_db_associations.ps1 automatically!

# Step 3: Verify generated files
# - Check src/Model/Table/TableNameTable.php for associations
# - Check src/Template/TableName/index.ctp for purple thead
# - Check src/Controller/TableNameController.php for export methods

# Step 4: Test in browser
# http://localhost/asahi_v3/table-names (note: use dashed-route)

# Step 5: Hard reload (Ctrl+Shift+R) to clear browser cache
```

### Quick Bake Reference by Connection (CMS)

```powershell
# CMS Masters Database
bin\cake bake all MasterPropinsis --connection default --force
bin\cake bake all MasterKabupatens --connection default --force
bin\cake bake all MasterKecamatans --connection default --force
bin\cake bake all MasterKelurahans --connection default --force

# LPK Candidates Database
bin\cake bake all LpkCandidates --connection cms_lpk_candidates --force

# LPK Candidate Documents Database
bin\cake bake all LpkCandidateDocuments --connection cms_lpk_candidate_documents --force

# TMM Apprentices Database
bin\cake bake all TmmApprentices --connection cms_tmm_apprentices --force

# TMM Apprentice Documents Database
bin\cake bake all TmmApprenticeDocuments --connection cms_tmm_apprentice_documents --force

# TMM Apprentice Document Ticketings Database
bin\cake bake all TmmApprenticeDocumentTicketings --connection cms_tmm_apprentice_document_ticketings --force

# TMM Organizations Database
bin\cake bake all TmmOrganizations --connection cms_tmm_organizations --force

# TMM Stakeholders Database
bin\cake bake all TmmStakeholders --connection cms_tmm_stakeholders --force

# TMM Trainees Database
bin\cake bake all TmmTrainees --connection cms_tmm_trainees --force

# TMM Trainee Accountings Database
bin\cake bake all TmmTraineeAccountings --connection cms_tmm_trainee_accountings --force

# TMM Trainee Trainings Database
bin\cake bake all TmmTraineeTrainings --connection cms_tmm_trainee_trainings --force

# TMM Trainee Training Scorings Database
bin\cake bake all TmmTraineeTrainingScorings --connection cms_tmm_trainee_training_scorings --force
```

---

## 🗄️ MULTI-DATABASE ARCHITECTURE

### Database Connection Mapping (CMS)

| Connection Key | Database Name | Example Tables | Usage |
|---------------|--------------|----------------|-------|
| `default` | cms_masters | MasterPropinsis, MasterKabupatens, MasterKecamatans, MasterKelurahans | Master reference tables |
| `cms_lpk_candidates` | cms_lpk_candidates | LpkCandidates | LPK candidate data |
| `cms_lpk_candidate_documents` | cms_lpk_candidate_documents | LpkCandidateDocuments | Candidate document uploads |
| `cms_tmm_apprentices` | cms_tmm_apprentices | TmmApprentices | TMM apprentice data |
| `cms_tmm_apprentice_documents` | cms_tmm_apprentice_documents | TmmApprenticeDocuments | Apprentice document uploads |
| `cms_tmm_apprentice_document_ticketings` | cms_tmm_apprentice_document_ticketings | TmmApprenticeDocumentTicketings | Document ticketing system |
| `cms_tmm_organizations` | cms_tmm_organizations | TmmOrganizations | Organization master data |
| `cms_tmm_stakeholders` | cms_tmm_stakeholders | TmmStakeholders | Stakeholder master data |
| `cms_tmm_trainees` | cms_tmm_trainees | TmmTrainees | Trainee data |
| `cms_tmm_trainee_accountings` | cms_tmm_trainee_accountings | TmmTraineeAccountings | Trainee accounting records |
| `cms_tmm_trainee_trainings` | cms_tmm_trainee_trainings | TmmTraineeTrainings | Trainee training records |
| `cms_tmm_trainee_training_scorings` | cms_tmm_trainee_training_scorings | TmmTraineeTrainingScorings | Trainee scoring records |

**⚠️ UPDATED November 2025**: Table counts and mapping synced to config/app_datasources.php. Always check this file for the latest connection and table mapping.

**CRITICAL**: `personnels` table is in `common` database, NOT in `personnel` database!

### Cross-Database Association Rules

**📚 CakePHP 2.x vs 3.x Behavior**

| Aspect | CakePHP 2.4.4 | CakePHP 3.9.x (This Project) |
|--------|---------------|------------------------------|
| **Cross-DB JOINs** | ✅ Automatic (MySQL handles it) | ❌ Must use `'strategy' => 'select'` |
| **Syntax** | `database.table` in JOIN | Separate queries, merged in PHP |
| **Configuration** | Just define connection | Connection + explicit strategy |
| **Performance** | Single query (faster) | 2+ queries (more flexible) |
| **Multi-Server** | ❌ Same MySQL server only | ✅ Different servers supported |

**Why CakePHP 3.x is different**:
```php
// CakePHP 2.x - Just works!
public $belongsTo = array(
    'Department' => array('foreignKey' => 'department_id')
);
// MySQL: SELECT ... FROM asahi_inventories.table 
//        LEFT JOIN asahi_personnel.departments ON ...

// CakePHP 3.x - Must be explicit!
$this->belongsTo('Departments', [
    'foreignKey' => 'department_id',
    'strategy' => 'select', // Forces separate queries
]);
// Query 1: SELECT ... FROM asahi_inventories.table
// Query 2: SELECT ... FROM asahi_personnel.departments WHERE id IN (...)
// Merged in PHP memory
```

**🎯 Bottom Line**: CakePHP 3.x **intentionally prevents** cross-database JOINs for better control and flexibility. All cross-database associations need `'strategy' => 'select'`.

---

**Rule 1**: Always use `'strategy' => 'select'` for cross-database associations

```php
// Correct - Cross-database association
$this->belongsTo('MasterKabupatens', [
    'foreignKey' => 'kabupaten_id',
    'strategy' => 'select', // Forces separate SELECT query
]);
```

**Rule 2**: For any association where the target table is in a different connection/database, always add `'strategy' => 'select'`.

**Rule 3**: For alias associations, add `'className' => 'TargetTable'` + `'strategy' => 'select'`

```php
// Correct - Alias association to MasterPropinsis table
$this->belongsTo('ApprovedByMasterPropinsis', [
    'className' => 'MasterPropinsis',
    'foreignKey' => 'approved_by_propinsi_id',
    'strategy' => 'select',
]);
```

**Rule 4**: Same-database associations can use default JOIN strategy

```php
// Correct - Same database, no strategy needed
$this->belongsTo('MasterKabupatens', [
    'foreignKey' => 'kabupaten_id',
    'joinType' => 'INNER',
]);
```

---

## 📝 BAKE TEMPLATE REFERENCE

### Index Template Features (src/Template/Bake/Template/index.ctp)

**Auto-Generated Elements**:

1. **Purple Gradient Thead** (Line ~102)

**Standard Style (All Tables):**
```php
<thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
    <tr>
        <th><?= $this->Paginator->sort('field', 'Label') ?></th>
        <!-- More columns... -->
    </tr>
</thead>
```

**Color Scheme**: Matches `btn-export-light` buttons for consistency
- Base gradient: #667eea (purple-blue) to #764ba2 (deep purple)
- Opacity: 0.15 (15%) for subtle background
- Direction: 135deg diagonal

**Visual Consistency**:
```css
/* Table Header */
background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);

/* Button Background (btn-export-light) */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
color: white;
border: 1px solid rgba(102, 126, 234, 0.3);

/* Both use same color palette - header is subtle (15% opacity), buttons are solid */
```

**Applied to**:
- ✅ All index pages (data tables)
- ✅ All view pages (detail tables)  
- ✅ All export prints (PDF/Print layout)
- ✅ Consistent across all 93+ baked tables

---

2. **Hamburger Dropdown Menu** (Lines ~44-105)

**Structure**: Three-layer navigation system with icons

```php
<div style="display: flex; align-items: center; gap: 10px;">
    <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" 
            data-bs-toggle="dropdown" aria-expanded="false" 
            style="padding: 6px 8px; font-size: 18px; color: #24292f; 
                   background: transparent; border: none;">
        <i class="fas fa-bars"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <!-- Current Model Actions -->
        <?= $this->Html->link(
            '<i class="fas fa-list"></i> ' . __('List <%= $pluralHumanName %>'),
            ['action' => 'index'],
            ['class' => 'dropdown-item', 'escape' => false]
        ) ?>
        <?= $this->Html->link(
            '<i class="fas fa-plus"></i> ' . __('New <%= $singularHumanName %>'),
            ['action' => 'add'],
            ['class' => 'dropdown-item', 'escape' => false]
        ) ?>
        
        <!-- BelongsTo Associations -->
<%
    if (!empty($associations['BelongsTo'])):
        foreach ($associations['BelongsTo'] as $alias => $details):
%>
        <div class="dropdown-divider"></div>
        <?= $this->Html->link(
            '<i class="fas fa-list"></i> ' . __('List <%= $alias %>'),
            ['controller' => '<%= $details['controller'] %>', 'action' => 'index'],
            ['class' => 'dropdown-item', 'escape' => false]
        ) ?>
        <?= $this->Html->link(
            '<i class="fas fa-plus"></i> ' . __('New <%= Inflector::singularize($alias) %>'),
            ['controller' => '<%= $details['controller'] %>', 'action' => 'add'],
            ['class' => 'dropdown-item', 'escape' => false]
        ) ?>
<%
        endforeach;
    endif;
%>
    </div>
    
    <h2 style="margin: 0;"><?= __('<%= $pluralHumanName %>') ?></h2>
</div>
```

**Auto-Generated Menu Items**:
- **Current Model**: List + New actions
- **BelongsTo Relations**: List + New for each associated model
- **Dividers**: Separate each association group

**Example** (Inventories model):
```
☰ Inventories
├── 📋 List Inventories
├── ➕ New Inventory
├── ──────────────
├── 📋 List Storages
├── ➕ New Storage
├── ──────────────
├── 📋 List Uoms
└── ➕ New Uom
```

**JavaScript Functionality** (Auto-included):
```javascript
// Dropdown toggle
dropdownButton.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    dropdownMenu.classList.toggle('show');
});

// Close on outside click
document.addEventListener('click', function(e) {
    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.remove('show');
    }
});

// Close after navigation (100ms delay)
dropdownMenu.addEventListener('click', function(e) {
    if (e.target.tagName === 'A' || e.target.closest('a')) {
        setTimeout(() => dropdownMenu.classList.remove('show'), 100);
    }
});
```

**CSS Styling** (Auto-included):
```css
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 200px;
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}
```

**Benefits**:
- ✅ Clean UI: No sidebar clutter
- ✅ Contextual: Only shows related models
- ✅ Fast Access: 2 clicks to related records
- ✅ Mobile-Friendly: Click/tap to open
- ✅ Auto-Generated: No manual coding

---

3. **Filter Row with Operators** (Lines ~185-290)

**Structure**: Combined operator dropdown + filter input per column

```php
<!-- Filter Row -->
<tr class="filter-row" style="background-color: #f8f9fa;">
    <th style="padding: 4px;">
        <!-- Text Fields -->
        <select class="filter-operator form-control form-control-sm" 
                data-column="field_name" 
                style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
            <option value="like">LIKE</option>
            <option value="not_like">NOT LIKE</option>
            <option value="=">=</option>
            <option value="!=">!=</option>
            <option value="starts_with">Starts With</option>
            <option value="ends_with">Ends With</option>
        </select>
        <input type="text" 
               class="filter-input form-control form-control-sm" 
               placeholder="Filter..." 
               data-column="field_name" 
               data-type="text" 
               style="font-size: 0.85rem;">
    </th>
    
    <th style="padding: 4px;">
        <!-- Number Fields -->
        <select class="filter-operator form-control form-control-sm" 
                data-column="qty" 
                style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
            <option value="=">=</option>
            <option value="!=">!=</option>
            <option value="<">&lt;</option>
            <option value=">">&gt;</option>
            <option value="<=">≤</option>
            <option value=">=">≥</option>
            <option value="between">Between</option>
        </select>
        <input type="number" 
               class="filter-input form-control form-control-sm" 
               placeholder="Quantity..." 
               data-column="qty" 
               data-type="number" 
               style="font-size: 0.85rem;">
        <input type="number" 
               class="filter-input-range form-control form-control-sm" 
               placeholder="To..." 
               data-column="qty" 
               style="display:none; margin-top: 2px; font-size: 0.85rem;">
    </th>
    
    <th style="padding: 4px;">
        <!-- Foreign Key Fields (Dropdown) -->
        <select class="filter-input form-control form-control-sm" 
                data-column="storage_id" 
                data-type="select" 
                style="font-size: 0.85rem; padding: 4px;">
            <option value="">All Storages</option>
            <?php foreach ($storages as $id => $title): ?>
                <option value="<?= $id ?>"><?= h($title) ?></option>
            <?php endforeach; ?>
        </select>
    </th>
    
    <th style="padding: 4px;">
        <!-- Date Fields -->
        <select class="filter-operator form-control form-control-sm" 
                data-column="created" 
                style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
            <option value="=">=</option>
            <option value="!=">!=</option>
            <option value="<">&lt;</option>
            <option value=">">&gt;</option>
            <option value="<=">≤</option>
            <option value=">=">≥</option>
            <option value="between">Between</option>
        </select>
        <input type="date" 
               class="filter-input form-control form-control-sm" 
               data-column="created" 
               data-type="date" 
               style="font-size: 0.85rem;">
        <input type="date" 
               class="filter-input-range form-control form-control-sm" 
               data-column="created" 
               style="display:none; margin-top: 2px; font-size: 0.85rem;">
    </th>
</tr>
```

**Auto-Detection by Field Type**:

| Field Type | Operators | Input Type | Range Support |
|------------|-----------|------------|---------------|
| Text | LIKE, NOT LIKE, =, !=, Starts With, Ends With | `<input type="text">` | No |
| Number | =, !=, <, >, ≤, ≥, Between | `<input type="number">` | Yes (Between) |
| Date | =, !=, <, >, ≤, ≥, Between | `<input type="date">` | Yes (Between) |
| Foreign Key | N/A (direct select) | `<select>` with options | No |
| Image/File | No filter | Icon: `<i class="fas fa-ban">` | No |

**JavaScript Filter Logic** (Auto-included):

```javascript
function applyFilter(cellText, filterValue, operator, filterValue2) {
    const cellVal = cellText.trim().toLowerCase();
    const filterVal = filterValue.trim().toLowerCase();
    
    if (filterVal === '') return true;
    
    // Smart numeric detection
    const cellNum = parseFloat(cellVal.replace(/[^0-9.-]/g, ''));
    const filterNum = parseFloat(filterVal);
    const isNumeric = !isNaN(cellNum) && !isNaN(filterNum);
    
    switch(operator) {
        case '=':
        case 'equals':
            return isNumeric ? cellNum === filterNum : cellVal === filterVal;
        
        case '!=':
            return isNumeric ? cellNum !== filterNum : cellVal !== filterVal;
        
        case '<':
        case 'lt':
            return isNumeric ? cellNum < filterNum : cellVal < filterVal;
        
        case '>':
        case 'gt':
            return isNumeric ? cellNum > filterNum : cellVal > filterVal;
        
        case '<=':
        case 'lte':
            return isNumeric ? cellNum <= filterNum : cellVal <= filterVal;
        
        case '>=':
        case 'gte':
            return isNumeric ? cellNum >= filterNum : cellVal >= filterVal;
        
        case 'between':
            if (filterValue2 && isNumeric) {
                const filterNum2 = parseFloat(filterValue2);
                return cellNum >= filterNum && cellNum <= filterNum2;
            }
            return cellVal.includes(filterVal);
        
        case 'like':
        case 'contains':
            return cellVal.includes(filterVal);
        
        case 'not_like':
            return !cellVal.includes(filterVal);
        
        case 'starts_with':
        case 'starts':
            return cellVal.startsWith(filterVal);
        
        case 'ends_with':
        case 'ends':
            return cellVal.endsWith(filterVal);
        
        default:
            return cellVal.includes(filterVal);
    }
}

// Toggle range input visibility for "between" operator
filterOperators.forEach(select => {
    select.addEventListener('change', function() {
        const columnName = this.getAttribute('data-column');
        rangeInputs.forEach(rangeInput => {
            if (rangeInput.getAttribute('data-column') === columnName) {
                rangeInput.style.display = this.value === 'between' ? 'block' : 'none';
            }
        });
        filterTable();
    });
});
```

**Filter Behavior**:
- ✅ **Real-time**: Filters apply as you type
- ✅ **Multi-column**: All filters work together (AND logic)
- ✅ **Smart Types**: Auto-detects numeric vs. text comparison
- ✅ **Range Support**: "Between" operator shows second input field
- ✅ **Console Logging**: Shows row count after each filter

**Example Usage**:

**Filter Inventories where quantity > 10**:
1. Find "Quantity" column
2. Select ">" from operator dropdown
3. Type "10" in input field
4. Table instantly shows only rows with qty > 10

**Filter Inventories with code starting with "INV"**:
1. Find "Code" column
2. Select "Starts With" from operator dropdown
3. Type "INV" in input field
4. Table shows only codes like "INV-001", "INV-002", etc.

**Filter Inventories with quantity between 10 and 50**:
1. Find "Quantity" column
2. Select "Between" from operator dropdown
3. Second input field appears
4. Type "10" in first field, "50" in second field
5. Table shows only rows with 10 ≤ qty ≤ 50

**Filter by Storage (dropdown)**:
1. Find "Storage" column
2. Click dropdown (no operator - direct select)
3. Choose storage from list
4. Table shows only inventories in that storage

---

4. **Drag-to-Scroll Table** (Lines ~580-650)

**CSS Implementation**:
```css
.table-scroll-wrapper {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-scroll-wrapper:active {
    cursor: grabbing;
}

.table-scroll-wrapper.dragging {
    cursor: grabbing;
    user-select: none;
}
```

**JavaScript Implementation**:
```javascript
const scrollContainer = document.querySelector('.table-scroll-wrapper');
let isDown = false, startX, scrollLeft;

scrollContainer.addEventListener('mousedown', function(e) {
    // Smart exclusions
    if (e.target.tagName === 'INPUT' || 
        e.target.tagName === 'BUTTON' || 
        e.target.tagName === 'A' ||
        e.target.closest('a') ||
        e.target.closest('button')) {
        return; // Don't drag when clicking interactive elements
    }
    
    isDown = true;
    scrollContainer.classList.add('dragging');
    startX = e.pageX - scrollContainer.offsetLeft;
    scrollLeft = scrollContainer.scrollLeft;
});

scrollContainer.addEventListener('mousemove', function(e) {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - scrollContainer.offsetLeft;
    const walk = (x - startX) * 2; // 2x scroll speed for comfort
    scrollContainer.scrollLeft = scrollLeft - walk;
});

scrollContainer.addEventListener('mouseup', function() {
    isDown = false;
    scrollContainer.classList.remove('dragging');
});

scrollContainer.addEventListener('mouseleave', function() {
    isDown = false;
    scrollContainer.classList.remove('dragging');
});
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with buttons, links, inputs
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
 }

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;

    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Exclude input fields, buttons, and links
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('a')) {
                return;
            }
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // 2x scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
    });
});
</script>
```

**Key Features**:
- ✅ **Visual Feedback**: Cursor grab → grabbing
- ✅ **Smart Exclusions**: Doesn't interfere with inputs, buttons, links
- ✅ **2x Scroll Speed**: Comfortable navigation
- ✅ **Touch-Friendly**: Works on touch devices
- ✅ **Auto-Applied**: All index pages get this feature

**User Experience**:
```
Hover:     [cursor: grab ✋] Ready to drag
Drag:      [cursor: grabbing ✊] Table scrolls with mouse
Release:   [cursor: grab ✋] Scrolling stops
Click Link: Normal link behavior (no drag initiated)
```

---

## 🎨 TABLE UX ENHANCEMENTS (Nov 2025)

This section documents major user experience improvements added to bake templates in November 2025 session. These features enhance table usability, filtering, and form layouts.

### Enhancement 1: Drag-to-Scroll Tables (View Pages)

**Feature**: Tables in view pages can be dragged left/right for horizontal scrolling.

**Location**: `src/Template/Bake/Template/view.ctp` (lines 1084-1141)

**CSS Implementation** (lines 1084-1099):
```css
<style>
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    user-select: none;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
</style>
```

**JavaScript Implementation** (lines 1102-1141):
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;