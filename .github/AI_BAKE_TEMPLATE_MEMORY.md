
# AI Agent Bake & Database Memory Guide

## 📢 DATABASE CONNECTIONS & MAPPING (Always check config/app_datasources.php)

### Current Database Connections (from config/app_datasources.php)

| Connection Name                        | Database Name                          |
|----------------------------------------|----------------------------------------|
| default                                | cms_masters                            |
| cms_masters                            | cms_masters                            |
| cms_lpk_candidates                     | cms_lpk_candidates                     |
| cms_lpk_candidate_documents            | cms_lpk_candidate_documents            |
| cms_tmm_apprentices                    | cms_tmm_apprentices                    |
| cms_tmm_apprentice_documents           | cms_tmm_apprentice_documents           |
| cms_tmm_apprentice_document_ticketings | cms_tmm_apprentice_document_ticketings |
| cms_tmm_organizations                  | cms_tmm_organizations                  |
| cms_tmm_stakeholders                   | cms_tmm_stakeholders                   |
| cms_tmm_trainees                       | cms_tmm_trainees                       |
| cms_tmm_trainee_accountings            | cms_tmm_trainee_accountings            |
| cms_tmm_trainee_trainings              | cms_tmm_trainee_trainings              |
| cms_tmm_trainee_training_scorings      | cms_tmm_trainee_training_scorings      |

### Table Class Setup
- Always override `defaultConnectionName()` in each Table class:
    ```php
    public static function defaultConnectionName() {
        return '<connection_name>';
    }
    ```

### Cross-Database Association Strategy
- For associations to tables in a different connection/database, add:
    ```php
    'strategy' => 'select'
    ```
    Example:
    ```php
    $this->belongsTo('Organizations', [
        'foreignKey' => 'organization_id',
        'strategy' => 'select', // REQUIRED for cross-database
    ]);
    ```
- For aliased associations, add `'className' => 'ActualTable'`.

### Bake Workflow
1. Bake table with correct connection:
    ```powershell
    bin\cake bake all TableName --connection <connection> --force
    # After bake, run fix_all_cross_db_associations.ps1 if needed
    bin\cake cache clear_all
    ```
2. After bake, check all associations. For any cross-database association, add `'strategy' => 'select'`.
3. Always override `defaultConnectionName()` in Table class.
4. Clear cache after changes:
    ```powershell
    bin\cake cache clear_all
    ```

### Error Prevention
- If you see errors like "Base table or view not found", check the connection and add `'strategy' => 'select'` for cross-database associations.

---

**Purpose:** Quick reference for AI agents to ensure consistent bake/database mapping, asset storage, and template features.
**System:** CakePHP 3.9 Multi-Database Application
**PHP Version:** 5.6 (CRITICAL - No PHP 7+ syntax allowed)

---

## 🔄 POST-BAKE WORKFLOW (CRITICAL - Follow Every Time!)

**Quick Checklist:**
1. Controller file not empty (>5 KB)
2. Add `'strategy' => 'select'` to cross-database associations
3. Add cascade data + JavaScript (if district fields)
4. Verify NO `??` operator (PHP 5.6 only)
5. Verify NO AJAX to Daerahs
6. Verify NO duplicate form fields (only ONE set of address dropdowns)
7. Verify element IDs match JavaScript selectors
8. Clear cache
9. Browser test

**Cascade Filtering Documentation:**
- Form Cascade (add/edit): See Step 3 below
- Index Filter Cascade: See "Cascade Filtering in Index Page Filters" section

After running `bin\cake bake all TableName --connection <connection> --force`, ALWAYS perform these steps:

### Step 1: Verify Controller File Size
```powershell
Get-Item src\Controller\TableNameController.php | Select-Object Name,Length
# Should be >5 KB, NOT 0 bytes!
```
If 0 bytes: Check bake template paths and clear cache.

### Step 2: Add 'strategy' => 'select' to Cross-Database Associations
Required for any association when model uses non-default database connection.
```php
$this->belongsTo('AssociatedModel', [
    'foreignKey' => 'foreign_key_id',
    'strategy' => 'select',
]);
```



**District Table Aliases (Cascade Filtering):**
- Propinsi: `MasterPropinsis`
- Kabupaten: `MasterKabupatens`
- Kecamatan: `MasterKecamatans`
- Kelurahan: `MasterKelurahans`
Use these names in controllers, templates, and cascade filtering logic.
For aliased associations, always add `'className'` and `'strategy' => 'select'` if cross-db.

### Step 3: Add Cascade Filtering for District Dropdowns (If Applicable)

Only if table has Province/Kabupaten/Kecamatan/Kelurahan fields:

**3a. Update Controller (add() and edit() methods):**
District tables must use the correct connection as mapped in `config/app_datasources.php`.

```php
// Example for project_tmm (adjust TableName and connection as needed):
$this->loadModel('Propinsis', 'district');
$this->loadModel('Kabupatens', 'district');
$this->loadModel('Kecamatans', 'district');
$this->loadModel('Kelurahans', 'district');

$propinsis = $this->Propinsis->find('list', ['limit' => 200]);
$kabupatens = $this->Kabupatens->find('list', ['limit' => 200]);
$kecamatans = $this->Kecamatans->find('list', ['limit' => 200]);
$kelurahans = $this->Kelurahans->find('list', ['limit' => 200]);

$kabupatensData = $this->Kabupatens->find('all')->select(['id', 'title', 'propinsi_id'])->toArray();
$kecamatansData = $this->Kecamatans->find('all')->select(['id', 'title', 'propinsi_id', 'kabupaten_id'])->toArray();
$kelurahansData = $this->Kelurahans->find('all')->select(['id', 'title', 'propinsi_id', 'kabupaten_id', 'kecamatan_id'])->toArray();

$this->set(compact('propinsis', 'kabupatens', 'kecamatans', 'kelurahans', 'kabupatensData', 'kecamatansData', 'kelurahansData'));
```

**Why explicit select() is critical:**
- Without `->select()`, CakePHP may not load foreign key fields needed for cascade filtering.

**Repeat for edit() method!**

**3b. Add JavaScript to Templates (add.ctp and edit.ctp):**

Insert BEFORE `});` at end of `$(document).ready()`:

**CRITICAL CASCADE LOGIC:**
- **Province selected** → Filter Kabupatens WHERE `kabupaten.propinsi_id = selected_propinsi_id`
- **Kabupaten selected** → Filter Kecamatans WHERE `kecamatan.propinsi_id = propinsi_id` AND `kecamatan.kabupaten_id = selected_kabupaten_id`
- **Kecamatan selected** → Filter Kelurahans WHERE `kelurahan.propinsi_id = propinsi_id` AND `kelurahan.kabupaten_id = kabupaten_id` AND `kelurahan.kecamatan_id = selected_kecamatan_id`

**Critical element IDs:**
- Form fields must have explicit PascalCase IDs: `'id' => 'ModelPropinsiId'`
- JavaScript must use same IDs: `$('#ModelPropinsiId')`
- Do not create duplicate form fields—only one set of address dropdowns per form.

```javascript
// Client-side cascade filtering for address dropdowns
var kabupatensData = <?= json_encode(array_map(function($k) { 
    return ['id' => $k->id, 'title' => $k->title, 'propinsi_id' => $k->propinsi_id]; 
}, isset($kabupatensData) ? $kabupatensData : [])) ?>;

var kecamatansData = <?= json_encode(array_map(function($k) { 
    return ['id' => $k->id, 'title' => $k->title, 'propinsi_id' => $k->propinsi_id, 'kabupaten_id' => $k->kabupaten_id]; 
}, isset($kecamatansData) ? $kecamatansData : [])) ?>;

var kelurahansData = <?= json_encode(array_map(function($k) { 
    return ['id' => $k->id, 'title' => $k->title, 'propinsi_id' => $k->propinsi_id, 'kabupaten_id' => $k->kabupaten_id, 'kecamatan_id' => $k->kecamatan_id]; 
}, isset($kelurahansData) ? $kelurahansData : [])) ?>;

// Province change - filter Kabupaten
$('#ModelPropinsiId').change(function() {  // ← Use PascalCase ID!
    var propinsiId = $(this).val();
    var kabupatenSelect = $('#ModelKabupatenId');
    var kecamatanSelect = $('#ModelKecamatanId');
    var kelurahanSelect = $('#ModelKelurahanId');
    
    kabupatenSelect.html('<option value="">-- <?= __("Select Kabupaten") ?> --</option>');
    kecamatanSelect.html('<option value="">-- <?= __("Select Kecamatan") ?> --</option>');
    kelurahanSelect.html('<option value="">-- <?= __("Select Kelurahan") ?> --</option>');
    
    if (propinsiId) {
        kabupatensData.forEach(function(kabupaten) {
            if (kabupaten.propinsi_id == propinsiId) {
                kabupatenSelect.append('<option value="' + kabupaten.id + '">' + kabupaten.title + '</option>');
            }
        });
    }
});

// Kabupaten change - filter Kecamatan
$('#ModelKabupatenId').change(function() {  // ← Use PascalCase ID!
    var propinsiId = $('#ModelPropinsiId').val();
    var kabupatenId = $(this).val();
    var kecamatanSelect = $('#ModelKecamatanId');
    var kelurahanSelect = $('#ModelKelurahanId');
    
    kecamatanSelect.html('<option value="">-- <?= __("Select Kecamatan") ?> --</option>');
    kelurahanSelect.html('<option value="">-- <?= __("Select Kelurahan") ?> --</option>');
    
    if (kabupatenId && propinsiId) {
        kecamatansData.forEach(function(kecamatan) {
            if (kecamatan.kabupaten_id == kabupatenId && kecamatan.propinsi_id == propinsiId) {
                kecamatanSelect.append('<option value="' + kecamatan.id + '">' + kecamatan.title + '</option>');
            }
        });
    }
});

// Kecamatan change - filter Kelurahan
$('#ModelKecamatanId').change(function() {  // ← Use PascalCase ID!
    var propinsiId = $('#ModelPropinsiId').val();
    var kabupatenId = $('#ModelKabupatenId').val();
    var kecamatanId = $(this).val();
    var kelurahanSelect = $('#ModelKelurahanId');
    
    kelurahanSelect.html('<option value="">-- <?= __("Select Kelurahan") ?> --</option>');
    
    if (kecamatanId && kabupatenId && propinsiId) {
        kelurahansData.forEach(function(kelurahan) {
            if (kelurahan.kecamatan_id == kecamatanId && kelurahan.kabupaten_id == kabupatenId && kelurahan.propinsi_id == propinsiId) {
                kelurahanSelect.append('<option value="' + kelurahan.id + '">' + kelurahan.title + '</option>');
            }
        });
    }
});
```

**CRITICAL: For edit.ctp ONLY - Add Initialization Code (AFTER the .change() handlers, BEFORE closing `});`):**

This populates the cascading dropdowns when editing existing records:

```javascript
// Initialize cascading dropdowns on page load for edit mode
var currentPropinsiId = $('#PersonnelPropinsiId').val();  // ← Use PascalCase ID!
var currentKabupatenId = $('#PersonnelKabupatenId').val();
var currentKecamatanId = $('#PersonnelKecamatanId').val();
var currentKelurahanId = $('#PersonnelKelurahanId').val();

if (currentPropinsiId) {
    // Populate Kabupaten based on selected Province
    var kabupatenSelect = $('#PersonnelKabupatenId');
    kabupatenSelect.html('<option value="">-- <?= __("Select Kabupaten") ?> --</option>');
    kabupatensData.forEach(function(kabupaten) {
        if (kabupaten.propinsi_id == currentPropinsiId) {
            var selected = (kabupaten.id == currentKabupatenId) ? ' selected' : '';
            kabupatenSelect.append('<option value="' + kabupaten.id + '"' + selected + '>' + kabupaten.title + '</option>');
        }
    });
    
    if (currentKabupatenId) {
        // Populate Kecamatan based on selected Kabupaten + Province
        var kecamatanSelect = $('#PersonnelKecamatanId');
        kecamatanSelect.html('<option value="">-- <?= __("Select Kecamatan") ?> --</option>');
        kecamatansData.forEach(function(kecamatan) {
            // CRITICAL: Check BOTH kabupaten_id AND propinsi_id
            if (kecamatan.kabupaten_id == currentKabupatenId && kecamatan.propinsi_id == currentPropinsiId) {
                var selected = (kecamatan.id == currentKecamatanId) ? ' selected' : '';
                kecamatanSelect.append('<option value="' + kecamatan.id + '"' + selected + '>' + kecamatan.title + '</option>');
            }
        });
        
        if (currentKecamatanId) {
            // Populate Kelurahan based on selected Kecamatan + Kabupaten + Province
            var kelurahanSelect = $('#PersonnelKelurahanId');
            kelurahanSelect.html('<option value="">-- <?= __("Select Kelurahan") ?> --</option>');
            kelurahansData.forEach(function(kelurahan) {
                // CRITICAL: Check ALL THREE - kecamatan_id, kabupaten_id, AND propinsi_id
                if (kelurahan.kecamatan_id == currentKecamatanId && kelurahan.kabupaten_id == currentKabupatenId && kelurahan.propinsi_id == currentPropinsiId) {
                    var selected = (kelurahan.id == currentKelurahanId) ? ' selected' : '';
                    kelurahanSelect.append('<option value="' + kelurahan.id + '"' + selected + '>' + kelurahan.title + '</option>');
                }
            });
        }
    }
}
```

**REMEMBER:**
- Replace `Personnel` with actual model name.
- Element IDs are PascalCase as set in form.
- jQuery selectors must match exactly.
- add.ctp does not need initialization code; edit.ctp does.
- Never create duplicate form fields.

### Step 4: Verify No AJAX Code + PHP 5.6 Syntax

**4a. Check for old AJAX code:**
```powershell
Select-String -Path src\Template\TableName\*.ctp -Pattern "Daerahs|optionKabupaten|optionKecamatan"
# Should return: NO MATCHES
```

If found: Old AJAX code from bake template—manually remove it (see Step 3b for correct pattern).

**4b. CRITICAL: Check for PHP 7+ null coalescing operator:**
```powershell
Select-String -Path src\Template\TableName\add.ctp,src\Template\TableName\edit.ctp -Pattern '\?\?' -Context 0,1
# Should return: NO MATCHES
# If found: Fatal error "syntax error, unexpected '?'" on PHP 5.6!
```

If `??` found: Replace with `isset($var) ? $var : []` (PHP 5.6 compatible). Use fix_php56_syntax_post_bake.ps1 or manual replace.

### Step 5: Clear Cache

```powershell
bin\cake cache clear_all
```

### Step 6: Browser Test

1. Hard refresh: `Ctrl+Shift+R`
2. Open F12 Console
3. Test cascade (if applicable): Province → Kabupaten → Kecamatan → Kelurahan
4. Verify: NO 403 errors, NO "Error loading..." alerts

Test: index, add, edit, view, delete operations. Check for Auto-Tables errors, missing associations, className issues.

---

## 🚨 BAKE TEMPLATE VERIFICATION CHECKLIST

**Before baking, verify these files are correct:**
- `src/Template/Bake/Template/add.ctp` - No AJAX code
- `src/Template/Bake/Template/edit.ctp` - No AJAX code
- `src/Template/Bake/Template/form.ctp` - Uses `isset()` not `??`
- `config/bootstrap_bake.php` - Uses `_paths` variable
- `src/Template/Bake/Controller/controller.twig` - Not `.DISABLED`, proper whitespace
**After baking, verify these common issues:**
- Controller file size > 0 bytes (>5 KB expected)
- Cross-database associations have `'strategy' => 'select'`
- Aliased associations have `'className' => 'ActualTableName'`
- No PHP 7+ syntax (`??` operator)
- Cache cleared: `bin\cake cache clear_all`
$content = $content -replace "'\s*options'\s*=>\s*\$propinsis\s*\?\?\s*\[\]", "'options' => isset(`$propinsis) ? `$propinsis : []"
$content = $content -replace "'\s*options'\s*=>\s*\$kabupatens\s*\?\?\s*\[\]", "'options' => isset(`$kabupatens) ? `$kabupatens : []"
$content = $content -replace "'\s*options'\s*=>\s*\$kecamatans\s*\?\?\s*\[\]", "'options' => isset(`$kecamatans) ? `$kecamatans : []"
$content = $content -replace "'\s*options'\s*=>\s*\$kelurahans\s*\?\?\s*\[\]", "'options' => isset(`$kelurahans) ? `$kelurahans : []"
Set-Content $file -Value $content -NoNewline

# Fix edit.ctp
$file = "src\Template\Bake\Template\edit.ctp"
$content = Get-Content $file -Raw
$content = $content -replace "'\s*options'\s*=>\s*\$propinsis\s*\?\?\s*\[\]", "'options' => isset(`$propinsis) ? `$propinsis : []"
$content = $content -replace "'\s*options'\s*=>\s*\$kabupatens\s*\?\?\s*\[\]", "'options' => isset(`$kabupatens) ? `$kabupatens : []"
$content = $content -replace "'\s*options'\s*=>\s*\$kecamatans\s*\?\?\s*\[\]", "'options' => isset(`$kecamatans) ? `$kecamatans : []"
$content = $content -replace "'\s*options'\s*=>\s*\$kelurahans\s*\?\?\s*\[\]", "'options' => isset(`$kelurahans) ? `$kelurahans : []"
Set-Content $file -Value $content -NoNewline

Write-Host "✅ Fixed PHP 5.6 syntax in bake templates"
```

---

## ⚠️ CRITICAL: PHP 5.6 Compatibility

**Server PHP Version: 5.6**

### Forbidden Syntax (PHP 7.0+):
- Null Coalescing Operator (`??`)
- Spaceship Operator (`<=>`)
- Scalar Type Declarations (`int $x, string $y`)
- Return Type Declarations (`: void`, `: array`)
- Null Coalescing Assignment (`??=`)

### Allowed PHP 5.6 Syntax:
- Ternary operator: `$x ? $y : $z`
- isset(): `isset($var) ? $var : 'default'`
- empty(): `!empty($var) ? $var : []`
- Short array syntax: `[]` instead of `array()`
- Short echo tags: `<?= ?>`
Always test templates on PHP 5.6 after baking!

### Common PHP 5.6 Issues Found in Production

**Issue 1: `??` operator in baked templates**
- **Error:** `Parse error: syntax error, unexpected '?'`
- **Fix:** Replace with `isset($var) ? $var : default`
- **Automated:** Run `.\fix_php56_syntax_post_bake.ps1 TableName`

**Issue 2: Missing className in aliased associations**
- **Error:** `Table 'database.aliased_table' doesn't exist` (Auto-Tables)
- **Fix:** Add `'className' => 'ActualTableName'` to association
- **Guide:** See `ALIASED_ASSOCIATIONS_QUICK_REFERENCE.md`

**Issue 3: Non-existent table references**
- **Error:** `Table 'database.references' doesn't exist`
- **Fix:** Comment out or remove association
- **Examples:** References, LineSections, LineProcesses (see `COMPREHENSIVE_DATABASE_TEST_SUMMARY.md`)

See `DATABASE_MAPPING_REFERENCE.md` for error handling and update steps.

---

## 📍 Address/District Dropdowns - Client-Side Cascade Filtering
District dropdowns (Province/Kabupaten/Kecamatan/Kelurahan) use static pre-loading with client-side cascade filtering.
Why this approach:
- Small dataset (~200-300 records)
- All data pre-loaded once (fast, no AJAX overhead)
- Client-side JavaScript filters options based on parent selection
- No server calls, no AJAX errors, simpler code

### Controller Pattern (Required for cascade):

```php
public function add() {
    // ... entity creation code ...
    
    // Load district dropdowns for display
    $propinsis = $this->ModelName->Propinsis->find('list', ['limit' => 200]);
    $kabupatens = $this->ModelName->Kabupatens->find('list', ['limit' => 200]);
    $kecamatans = $this->ModelName->Kecamatans->find('list', ['limit' => 200]);
    $kelurahans = $this->ModelName->Kelurahans->find('list', ['limit' => 200]);
    
    // CRITICAL: Get full entity data with parent IDs for cascade filtering
    $kabupatensData = $this->ModelName->Kabupatens->find('all', ['limit' => 200])->toArray();
    $kecamatansData = $this->ModelName->Kecamatans->find('all', ['limit' => 200])->toArray();
    $kelurahansData = $this->ModelName->Kelurahans->find('all', ['limit' => 200])->toArray();
    
    $this->set(compact('propinsis', 'kabupatens', 'kecamatans', 'kelurahans', 
                       'kabupatensData', 'kecamatansData', 'kelurahansData'));
}

public function edit($id = null) {
    // ... entity loading and save logic ...
    
    // Same as add() - load both list and full data
    $propinsis = $this->ModelName->Propinsis->find('list', ['limit' => 200]);
    
    $kabupatensData = $this->ModelName->Kabupatens->find('all', ['limit' => 200])->toArray();
    $kecamatansData = $this->ModelName->Kecamatans->find('all', ['limit' => 200])->toArray();
    $kelurahansData = $this->ModelName->Kelurahans->find('all', ['limit' => 200])->toArray();
    
    $kabupatens = $this->ModelName->Kabupatens->find('list', ['limit' => 200]);
    $kecamatans = $this->ModelName->Kecamatans->find('list', ['limit' => 200]);
    $kelurahans = $this->ModelName->Kelurahans->find('list', ['limit' => 200]);
    
    $this->set(compact('entity', 'propinsis', 'kabupatens', 'kecamatans', 'kelurahans',
                       'kabupatensData', 'kecamatansData', 'kelurahansData'));
}
```

### Template Pattern (add.ctp / edit.ctp):

```php
// Form controls remain simple - just use isset() for PHP 5.6
<?= $this->Form->control('propinsi_id', [
    'options' => isset($propinsis) ? $propinsis : [],
    'class' => 'form-control',
    'empty' => __('-- Select Province --')
]) ?>
<?= $this->Form->control('kabupaten_id', [
    'options' => isset($kabupatens) ? $kabupatens : [],
    'class' => 'form-control',
    'empty' => __('-- Select Kabupaten --')
]) ?>
<?= $this->Form->control('kecamatan_id', [
    'options' => isset($kecamatans) ? $kecamatans : [],
    'class' => 'form-control',
    'empty' => __('-- Select Kecamatan --')
]) ?>
<?= $this->Form->control('kelurahan_id', [
    'options' => isset($kelurahans) ? $kelurahans : [],
    'class' => 'form-control',
    'empty' => __('-- Select Kelurahan --')
]) ?>
```

### JavaScript Cascade Filtering (add to bottom of add.ctp/edit.ctp):

```javascript
<?php $this->start('script'); ?>
<script>
$(document).ready(function() {
    // ... existing password strength code ...
    
    // CRITICAL: Embed district data as JavaScript arrays
    var kabupatensData = <?= json_encode(array_map(function($k) { 
        return ['id' => $k->id, 'title' => $k->title, 'propinsi_id' => $k->propinsi_id]; 
    }, isset($kabupatensData) ? $kabupatensData : [])) ?>;
    
    var kecamatansData = <?= json_encode(array_map(function($k) { 
        return ['id' => $k->id, 'title' => $k->title, 'propinsi_id' => $k->propinsi_id, 'kabupaten_id' => $k->kabupaten_id]; 
    }, isset($kecamatansData) ? $kecamatansData : [])) ?>;
    
    var kelurahansData = <?= json_encode(array_map(function($k) { 
        return ['id' => $k->id, 'title' => $k->title, 'propinsi_id' => $k->propinsi_id, 'kabupaten_id' => $k->kabupaten_id, 'kecamatan_id' => $k->kecamatan_id]; 
    }, isset($kelurahansData) ? $kelurahansData : [])) ?>;
    
    // Province change - filter Kabupaten
    $('#model-name-propinsi-id').change(function() {
        var propinsiId = $(this).val();
        var kabupatenSelect = $('#model-name-kabupaten-id');
        var kecamatanSelect = $('#model-name-kecamatan-id');
        var kelurahanSelect = $('#model-name-kelurahan-id');
        
        // Reset dependent selects
        kabupatenSelect.html('<option value="">-- <?= __("Select Kabupaten") ?> --</option>');
        kecamatanSelect.html('<option value="">-- <?= __("Select Kecamatan") ?> --</option>');
        kelurahanSelect.html('<option value="">-- <?= __("Select Kelurahan") ?> --</option>');
        
        if (propinsiId) {
            // Filter kabupatens by propinsi
            kabupatensData.forEach(function(kabupaten) {
                if (kabupaten.propinsi_id == propinsiId) {
                    kabupatenSelect.append('<option value="' + kabupaten.id + '">' + kabupaten.title + '</option>');
                }
            });
        }
    });
    
    // Kabupaten change - filter Kecamatan
    $('#model-name-kabupaten-id').change(function() {
        var propinsiId = $('#model-name-propinsi-id').val();
        var kabupatenId = $(this).val();
        var kecamatanSelect = $('#model-name-kecamatan-id');
        var kelurahanSelect = $('#model-name-kelurahan-id');
        
        kecamatanSelect.html('<option value="">-- <?= __("Select Kecamatan") ?> --</option>');
        kelurahanSelect.html('<option value="">-- <?= __("Select Kelurahan") ?> --</option>');
        
        if (kabupatenId && propinsiId) {
            kecamatansData.forEach(function(kecamatan) {
                if (kecamatan.kabupaten_id == kabupatenId && kecamatan.propinsi_id == propinsiId) {
                    kecamatanSelect.append('<option value="' + kecamatan.id + '">' + kecamatan.title + '</option>');
                }
            });
        }
    });
    
    // Kecamatan change - filter Kelurahan
    $('#model-name-kecamatan-id').change(function() {
        var propinsiId = $('#model-name-propinsi-id').val();
        var kabupatenId = $('#model-name-kabupaten-id').val();
        var kecamatanId = $(this).val();
        var kelurahanSelect = $('#model-name-kelurahan-id');
        
        kelurahanSelect.html('<option value="">-- <?= __("Select Kelurahan") ?> --</option>');
        
        if (kecamatanId && kabupatenId && propinsiId) {
            kelurahansData.forEach(function(kelurahan) {
                if (kelurahan.kecamatan_id == kecamatanId && kelurahan.kabupaten_id == kabupatenId && kelurahan.propinsi_id == propinsiId) {
                    kelurahanSelect.append('<option value="' + kelurahan.id + '">' + kelurahan.title + '</option>');
                }
            });
        }
    });
});
</script>
<?php $this->end(); ?>
```

REMEMBER: Replace `model-name` with actual model name.

### DO NOT:
- Use AJAX to load district options from server
- Reference non-existent controllers
- Use PHP 7+ null coalescing `??` operator
- Forget to pass district data from controller
- Use dashed/lowercase IDs—use PascalCase
- Create duplicate form fields
- Reference tables that don't exist
- Forget className for aliased associations

### Check for Duplicate Fields After Baking

**Common Issue:** Bake templates may generate address fields TWICE:
1. In "Address Information" section (with custom IDs) ✅ KEEP THIS
2. Below as separate fields labeled "Kabupaten Id", "Kecamatan Id", "Kelurahan Id" ❌ DELETE THESE!

**Verification:**
```powershell
# Check for duplicate kabupaten_id fields
Select-String -Path src\Template\TableName\add.ctp,src\Template\TableName\edit.ctp -Pattern "kabupaten_id" -Context 1,1
# Should only appear ONCE in Address Information section!
```

**Manual Fix:** Delete the duplicate fields (usually found after Address Information card, before Post Code field)

### Remove Old AJAX Code After Bake

**If templates contain old AJAX cascade code, REMOVE IT IMMEDIATELY:**

```javascript
// ❌ DELETE THIS PATTERN if found in add.ctp/edit.ctp:
$('#PersonnelPropinsiId').change(function() {
    $.ajax({
        type: 'POST',
        url: '<?= $this->Url->build(["controller" => "Daerahs", "action" => "optionKabupaten"]) ?>/...',
        // ... AJAX calls to DaerahsController
    });
});
```

**Verification after bake:**
```powershell
# Check for old AJAX code
Select-String -Path src\Template\*\*.ctp -Pattern "Daerahs|optionKabupaten|optionKecamatan"
# Should return 0 matches!
```

Symptoms of old AJAX code:
- Browser console shows: 403 Forbidden errors
- POST requests to AJAX endpoints
- Error loading alerts
- Duplicate JavaScript blocks in template

Solution: Manually remove lines containing AJAX patterns. Keep only new client-side cascade code.

### Benefits:
- Fast cascading (no server calls)
- Reliable (no AJAX errors)
- Simple client-side logic
- PHP 5.6 compatible
- Works offline after page load

---

## 🎨 Design System

### Color Palette

```css
/* Primary Theme: Purple Gradient */
--primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%)
--primary-purple: #667eea
--primary-dark-purple: #764ba2

/* Button Colors */
--btn-edit: #f39c12 (orange/yellow)
--btn-delete: #e74c3c (red)
--btn-view: #3498db (blue)
--btn-add: #2ecc71 (green)
--btn-background: #2c3e50 (dark)

/* Table Colors */
--table-header: Purple gradient
--table-zebra-odd: #f8f9fa (light gray)
--table-zebra-even: white
--table-hover: #e9ecef (light blue-gray)
```

### Typography

```css
/* Font Family */
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif

/* Font Sizes */
--font-size-base: 14px
--font-size-heading: 1.75rem (h2)
--font-size-subheading: 1.25rem (h3)
--font-size-small: 0.875rem
```

### Layout

```css
/* Container */
max-width: 100%
padding: 20px

/* Cards */
background: white
border-radius: 8px
box-shadow: 0 2px 4px rgba(0,0,0,0.1)
padding: 20px

/* Spacing */
margin-bottom: 20px (between sections)
padding: 15px (inside cards)
```

---

## 📋 Model / Table Bake Template

**Location:** `src/Template/Bake/Model/table.twig`

### Key Features to Remember

1. **Connection Configuration**
   ```php
    // ALWAYS check connection from DATABASE_MAPPING_REFERENCE.md
    // NEVER hardcode connection names
   public function initialize(array $config) {
       parent::initialize($config);
        $this->setConnection(ConnectionManager::get('{{ connection }}')); // Auto-detected
   }
   ```

2. **Cross-Database Associations**
   ```php
    // CRITICAL: Add 'strategy' => 'select' for cross-DB associations
   $this->belongsTo('Departments', [
       'foreignKey' => 'department_id',
       'joinType' => 'LEFT',
       'strategy' => 'select', // ← REQUIRED for cross-database
   ]);
   ```

3. **Association Patterns**
   ```php
   // belongsTo - Parent relationship
   $this->belongsTo('ParentTable', [
       'foreignKey' => 'parent_id',
       'joinType' => 'LEFT', // Use LEFT for optional relationships
   ]);
   
    // hasMany - Child relationships (will need tabs in view)
   $this->hasMany('ChildRecords', [
       'foreignKey' => 'parent_id',
       'dependent' => true, // Delete children when parent deleted
   ]);
   ```

4. **Display Field**
   ```php
   // Auto-detect name field: name, title, code, id (in that order)
    $this->setDisplayField('name'); // or 'title', 'code', 'id'
   ```

5. **Validation Rules**
   ```php
    // Common patterns
   ->requirePresence('field_name', 'create')
   ->notEmptyString('field_name')
   ->email('email_field')
   ->maxLength('field_name', 255)
   ->date('date_field')
   ->integer('integer_field')
   ```

### Post-Bake Actions Required

- ✅ Run `post_bake_fix.ps1` to add 'strategy' => 'select'
- ✅ Verify associations in `DATABASE_ASSOCIATIONS_REFERENCE.md`
- ✅ Check for cross-database relationships
- ✅ Check for aliased associations needing `className` (see `ALIASED_ASSOCIATIONS_QUICK_REFERENCE.md`)
- ✅ Comment out non-existent table references (References, Line* tables)
- ✅ Follow complete post-bake checklist at top of this document

---

## 🎛️ Controller Bake Template

**Location:** `src/Template/Bake/Controller/controller.twig`
**Element Templates:** `src/Template/Bake/Element/Controller/`

### ⚠️ CRITICAL: Bake Configuration Requirements

**To prevent empty controller files (0 bytes), you MUST:**

1. **Bootstrap Configuration** (`config/bootstrap_bake.php`):
   ```php
   EventManager::instance()->on('Bake.initialize', function ($event) {
       /** @var \Bake\View\BakeView $view */
       $view = $event->getSubject();
       
        // CRITICAL: Use _paths variable, NOT setTemplatePath()
       $view->set('_paths', [
           ROOT . DS . 'src' . DS . 'Template' . DS . 'Bake' . DS,
           CAKE . 'Template' . DS . 'Bake' . DS
       ]);
   });
   ```
   
   ❌ **WRONG** (causes empty files):
   ```php
    $view->setTemplatePath($path); // This breaks Twig rendering!
   ```

2. **Controller Template** (`src/Template/Bake/Controller/controller.twig`):
    - Must NOT have `.DISABLED` extension
    - Must have proper whitespace (no extra blank lines between class and use statement)
   
   ```twig
   class {{ name }}Controller extends AppController
   {
       use \App\Controller\ExportTrait;
   {% set helpers = Bake.arrayProperty('helpers', helpers, {'indent': false})|raw %}
   ```

3. **Verify After Baking**:
   ```powershell
    Get-Item src\Controller\TableNameController.php | Select-Object Length
    # Should be >5 KB, NOT 0!
   ```

If controllers are still empty:
- Check: `src/Template/Bake/Controller/controller.twig` exists (no `.DISABLED`)
- Check: `config/bootstrap_bake.php` uses `_paths` variable
- Check: Element templates exist in `src/Template/Bake/Element/Controller/`
- Clear cache: `bin\cake cache clear_all`

See `DATABASE_MAPPING_REFERENCE.md` for troubleshooting guide.

### Standard Structure

```php
namespace App\Controller;

use App\Controller\AppController;

class {{ TableName }}Controller extends AppController {
    use ExportTrait; // For CSV/Excel/PDF exports
    
    public function index() { }
    public function view($id = null) { }
    public function add() { }
    public function edit($id = null) { }
    public function delete($id = null) { }
    public function exportCsv() { }
    public function exportExcel() { }
    public function exportPdf() { }
    public function printReport() { }
}
```

### Features to Include

1. **Pagination (Index)**
   ```php
   ${{ pluralVar }} = $this->paginate($this->{{ TableName }}, [
       'contain' => ['AssociatedTables'],
       'order' => ['{{ TableName }}.id' => 'DESC'],
       'limit' => 20,
   ]);
   ```

2. **File Upload Handling (Add/Edit)**
   ```php
    // Auto-detect upload fields: *image*, *photo*, *file*, *attachment*
   public function add() {
       ${{ singularVar }} = $this->{{ TableName }}->newEntity();
       if ($this->request->is('post')) {
           $data = $this->request->getData();
           
           // Handle file uploads
           $data = $this->handleFileUploads($data, ['image_url', 'file_path']);
           
           ${{ singularVar }} = $this->{{ TableName }}->patchEntity(${{ singularVar }}, $data);
           if ($this->{{ TableName }}->save(${{ singularVar }})) {
               $this->Flash->success(__('The {{ humanSingular }} has been saved.'));
               return $this->redirect(['action' => 'index']);
           }
       }
   }
   ```

3. **Export Methods (CSV/Excel/PDF)**
   ```php
   public function exportCsv() {
       $query = $this->{{ TableName }}->find('all')->contain([...]);
       $headers = ['ID', 'Name', 'Created', ...];
       $fields = ['id', 'name', 'created', ...];
       return $this->doExportCsv($query, '{{ TableName }}', $headers, $fields);
   }
   
   public function exportExcel() {
       // Same as CSV but returns .xlsx
   }
   
   public function exportPdf() {
       $this->layout = 'print'; // CRITICAL: Override AppController layout
       $this->viewBuilder()->setLayout('print');
       return $this->printReport();
   }
   
   public function printReport() {
       $this->layout = 'print';
       $this->viewBuilder()->setLayout('print');
       
       $query = $this->{{ TableName }}->find('all')
           ->contain(['Association1', 'Association2']); // Load needed associations
       
       // CRITICAL: Choose USEFUL columns, not just timestamps
       $title = '{{ TableName }} Report';
       $headers = ['ID', 'Name', 'Email', 'Company']; // User-friendly headers
       $fields = ['id', 'name', 'email', 'company.name']; // Field paths
       
       return $this->doExportPrint($query, $title, $headers, $fields);
   }
   ```
   
   **CRITICAL - PDF Export Field Rules:**
   - Direct fields: Use exact database column name (e.g., `id`, `name`, `contact_email`)
   - Associated fields: **Use lowercase_underscore** format (e.g., `company.name`, `department.name`, `employee_status.name`)
   - ❌ NOT CamelCase: `Company.name` or `EmployeeStatus.name` will NOT work
   - Choose business-relevant columns (name, email, company) NOT just metadata (created, modified)
   
   **Common Mistakes:**
   ```php
   // ❌ BAD - Only timestamps, not useful
   $headers = ['ID', 'Name', 'Created', 'Modified'];
   $fields = ['id', 'name', 'created', 'modified'];
   
   // ✅ GOOD - Useful business data
   $headers = ['ID', 'Name', 'Email', 'Phone', 'Company', 'Department', 'Position'];
   $fields = ['id', 'name', 'contact_email', 'telephone', 'company.name', 'department.name', 'position.name'];
   
   // ❌ BAD - CamelCase associations (will be empty)
   $fields = ['id', 'name', 'Company.name', 'Department.name'];
   
   // ✅ GOOD - lowercase_underscore associations
   $fields = ['id', 'name', 'company.name', 'department.name'];
   ```
   
   **Troubleshooting "Can't Calculate" Browser Errors:**
   - ExportTrait doExportPrint() must use `$query->all()->toArray()` (not just `$query->all()`)
   - Print layout (src/Template/Layout/print.ctp) must be simplified:
     - No JavaScript (causes calculation errors)
     - No CSS gradients or shadows
     - No complex selectors like `:has()`
     - Use `table-layout: fixed` for predictable rendering
     - Set explicit `@page { size: A4 landscape; margin: 1cm; }`
   - Workaround: Press Ctrl+P immediately when page loads to bypass preview
   ```

4. **Flash Messages**
   ```php
   $this->Flash->success(__('The {{ humanSingular }} has been saved.'));
   $this->Flash->error(__('The {{ humanSingular }} could not be saved. Please, try again.'));
   $this->Flash->warning(__('The {{ humanSingular }} could not be deleted.'));
   ```

### Contains/Associations

```php
// ALWAYS contain foreign key associations for display
'contain' => [
    'ParentTable1',
    'ParentTable2',
    'ChildRecords' => ['SubAssociation'],
]
```

---

## 📊 Index Template (index.ctp)

**Location:** `src/Template/Bake/Template/index.ctp`

### Page Structure

```html
<div class="container-fluid">
    <!-- Header with Title + Export Buttons -->
    <div class="row mb-3">
        <div class="col-md-6">
            <h2><?= __('{{ PluralHumanName }}') ?></h2>
        </div>
        <div class="col-md-6 text-right">
            <?= $this->Html->link(__('New {{ SingularHumanName }}'), ['action' => 'add'], ['class' => 'btn btn-success']) ?>
            <!-- Export Buttons -->
            <?= $this->Html->link(__('CSV'), ['action' => 'exportCsv'], ['class' => 'btn btn-sm btn-info']) ?>
            <?= $this->Html->link(__('Excel'), ['action' => 'exportExcel'], ['class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link(__('PDF'), ['action' => 'exportPdf'], ['class' => 'btn btn-sm btn-danger']) ?>
        </div>
    </div>
    
    <!-- Table with Filters -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead><!-- Headers --></thead>
            <!-- Filter row auto-inserted by JavaScript -->
            <tbody><!-- Data rows --></tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="paginator">...</div>
</div>
```

### Features to Include

1. **Table Structure**
   ```html
   <table class="table table-striped table-hover table-bordered">
       <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
           <tr>
               <th><?= $this->Paginator->sort('id', 'ID') ?></th>
               <th><?= $this->Paginator->sort('name', 'Name') ?></th>
               <th><?= $this->Paginator->sort('created', 'Created') ?></th>
               <th class="actions-column" style="position: sticky; right: 0;"><?= __('Actions') ?></th>
           </tr>
       </thead>
   ```

2. **AJAX Filter Attributes**
   ```html
   <table 
       id="dataTable" 
       class="table table-striped filterable"
       data-filter-url="<?= $this->Url->build(['action' => 'index']) ?>"
       data-ajax-enabled="true">
   ```

3. **Action Buttons (Hover Style)**
   ```html
   <td class="actions">
       <div class="action-buttons-hover">
           <?= $this->Html->link('<i class="fas fa-eye"></i>', 
               ['action' => 'view', $record->id], 
               ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]) ?>
           <?= $this->Html->link('<i class="fas fa-edit"></i>', 
               ['action' => 'edit', $record->id], 
               ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]) ?>
           <?= $this->Form->postLink('<i class="fas fa-trash"></i>', 
               ['action' => 'delete', $record->id], 
               ['confirm' => __('Are you sure?'), 'class' => 'btn-action-icon btn-delete-icon', 'escape' => false, 'title' => __('Delete')]) ?>
       </div>
   </td>
   ```

4. **Foreign Key Display**
   ```html
   <!-- ALWAYS use has() and show related data -->
   <td><?= $record->has('department') ? $this->Html->link($record->department->name, ['controller' => 'Departments', 'action' => 'view', $record->department->id]) : '' ?></td>
   ```

5. **Pagination**
   ```html
   <div class="paginator">
       <ul class="pagination">
           <?= $this->Paginator->first('<< ' . __('first')) ?>
           <?= $this->Paginator->prev('< ' . __('previous')) ?>
           <?= $this->Paginator->numbers() ?>
           <?= $this->Paginator->next(__('next') . ' >') ?>
           <?= $this->Paginator->last(__('last') . ' >>') ?>
       </ul>
       <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
   </div>
   ```

6. **Horizontal Drag Scrolling**
   ```html
   <!-- Wrap table in scrollable container -->
   <div class="table-responsive table-drag-scroll" style="overflow-x: auto; cursor: grab;">
       <table class="table table-striped table-hover table-bordered">
           <!-- Table content -->
       </table>
   </div>
   ```

7. **Sticky Action Column (Left Side on Hover)**
   ```html
   <table class="table">
       <thead>
           <tr>
               <!-- Actions column FIRST (left-sticky) -->
               <th class="actions-column-left" style="position: sticky; left: 0; background: inherit; z-index: 10;">
                   <?= __('Actions') ?>
               </th>
               <th><?= $this->Paginator->sort('id', 'ID') ?></th>
               <!-- Other columns -->
           </tr>
       </thead>
       <tbody>
           <tr>
               <!-- Actions appear on hover, sticky on left -->
               <td class="actions-column-left" style="position: sticky; left: 0; background: white; z-index: 9;">
                   <div class="action-buttons-hover">
                       <?= $this->Html->link('<i class="fas fa-eye"></i>', 
                           ['action' => 'view', $record->id], 
                           ['class' => 'btn-action-icon btn-view-icon', 'escape' => false]) ?>
                       <?= $this->Html->link('<i class="fas fa-edit"></i>', 
                           ['action' => 'edit', $record->id], 
                           ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false]) ?>
                   </div>
               </td>
               <td><?= h($record->id) ?></td>
               <!-- Other columns -->
           </tr>
       </tbody>
   </table>
   ```

8. **Thumbnail Generation for Image Fields**
   ```php
   <!-- Auto-detect image fields: *photo*, *image*, *gambar* -->
   <?php
   // Check if field name contains 'photo', 'image', 'gambar'
   // AND there's a corresponding thumbnail field (e.g., image_url + thumbnail_url)
   ?>
   
   <!-- If thumbnail exists, show thumbnail column -->
   <?php if (isset($record->thumbnail_url) && !empty($record->thumbnail_url)): ?>
       <td class="thumbnail-column">
           <img src="<?= $this->Url->build('/' . h($record->thumbnail_url)) ?>" 
                alt="Thumbnail" 
                style="max-width: 60px; max-height: 60px; object-fit: cover; border-radius: 4px;"
                onerror="this.src='/img/no-image.png';">
       </td>
   <?php elseif (isset($record->image_url) && !empty($record->image_url)): ?>
       <!-- Fallback to full image if no thumbnail -->
       <td class="thumbnail-column">
           <img src="<?= $this->Url->build('/' . h($record->image_url)) ?>" 
                alt="Image" 
                style="max-width: 60px; max-height: 60px; object-fit: cover; border-radius: 4px;"
                onerror="this.src='/img/no-image.png';">
       </td>
   <?php else: ?>
       <td class="thumbnail-column">
           <span class="text-muted">No image</span>
       </td>
   <?php endif; ?>
   ```

9. **JavaScript Includes**
   ```html
   <script src="http://localhost/static-assets/js/ajax-table-filter.js"></script>
   <script src="http://localhost/static-assets/js/table-drag-scroll.js"></script>
   
   <script>
   // Enable horizontal drag scrolling
   $(document).ready(function() {
       const tableDragScroll = document.querySelector('.table-drag-scroll');
       let isDown = false;
       let startX;
       let scrollLeft;
       
       if (tableDragScroll) {
           tableDragScroll.addEventListener('mousedown', (e) => {
               isDown = true;
               tableDragScroll.style.cursor = 'grabbing';
               startX = e.pageX - tableDragScroll.offsetLeft;
               scrollLeft = tableDragScroll.scrollLeft;
           });
           
           tableDragScroll.addEventListener('mouseleave', () => {
               isDown = false;
               tableDragScroll.style.cursor = 'grab';
           });
           
           tableDragScroll.addEventListener('mouseup', () => {
               isDown = false;
               tableDragScroll.style.cursor = 'grab';
           });
           
           tableDragScroll.addEventListener('mousemove', (e) => {
               if (!isDown) return;
               e.preventDefault();
               const x = e.pageX - tableDragScroll.offsetLeft;
               const walk = (x - startX) * 2; // Scroll speed
               tableDragScroll.scrollLeft = scrollLeft - walk;
           });
       }
   });
   </script>
   ```

### Column Limits

- Max 10 visible columns (for readability)
- Exclude: binary fields, large text fields, duplicate image fields (if thumbnail exists)
- Include: ID, name/title, key foreign keys, dates, status
- **AUTO-ADD**: Thumbnail column if image/photo field exists
- **POSITION**: Actions column on LEFT (sticky), appears on hover

### 🌊 Cascade Filtering in Index Page Filters

**Cascade filtering** allows district dropdowns (Province → Kabupaten → Kecamatan → Kelurahan) to dynamically filter options based on parent selection in the **filter row** of index pages.

**Key Differences from Form Cascade:**
- **Forms (add/edit)**: Documented in POST-BAKE WORKFLOW section (lines ~80-220)
- **Index filters**: Uses same client-side logic but different event handling
- **Form cascade**: Triggers form validation/submission
- **Filter cascade**: Triggers `filterTable()` to reload data with AJAX

**When to Use Cascade in Index Filters:**
- Table has district foreign keys: `propinsi_id`, `kabupaten_id`, `kecamatan_id`, `kelurahan_id`
- User needs to filter by geographic location
- Pre-loading 200-300 district records is acceptable (client-side filtering)

#### Controller Requirements

Load district data in `index()` action:

```php
public function index()
{
    // Load districts for cascade filtering
    $this->loadModel('Kabupatens', 'district');
    $this->loadModel('Kecamatans', 'district');
    $this->loadModel('Kelurahans', 'district');
    
    $kabupatensData = $this->Kabupatens->find('all')
        ->select(['id', 'title', 'propinsi_id'])
        ->toArray();
    $kecamatansData = $this->Kecamatans->find('all')
        ->select(['id', 'title', 'propinsi_id', 'kabupaten_id'])
        ->toArray();
    $kelurahansData = $this->Kelurahans->find('all')
        ->select(['id', 'title', 'propinsi_id', 'kabupaten_id', 'kecamatan_id'])
        ->toArray();
    
    $this->set(compact('kabupatensData', 'kecamatansData', 'kelurahansData'));
    
    // Load propinsis for first dropdown
    $propinsis = $this->TableName->Propinsis->find('list', ['limit' => 200])->toArray();
    $this->set(compact('propinsis'));
}
```

**IMPORTANT:** Use `$this->loadModel('Kabupatens', 'district')` because district tables are in `district` connection, not `default`.

#### Filter Row HTML

Add cascade dropdowns to filter row in `index.ctp`:

```php
<tr class="filter-row">
    <!-- Province Filter -->
    <th>
        <select class="filter-input filter-cascade-propinsi" 
                data-column="propinsi_id" 
                name="filter[propinsi_id]">
            <option value="">-- All Provinces --</option>
            <?php foreach ($propinsis as $id => $title): ?>
                <option value="<?= $id ?>" <?= ($filters['propinsi_id'] ?? '') == $id ? 'selected' : '' ?>>
                    <?= h($title) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </th>
    
    <!-- Kabupaten Filter -->
    <th>
        <select class="filter-input filter-cascade-kabupaten" 
                data-column="kabupaten_id" 
                name="filter[kabupaten_id]" 
                disabled>
            <option value="">-- Select Province First --</option>
        </select>
    </th>
    
    <!-- Kecamatan Filter -->
    <th>
        <select class="filter-input filter-cascade-kecamatan" 
                data-column="kecamatan_id" 
                name="filter[kecamatan_id]" 
                disabled>
            <option value="">-- Select Kabupaten First --</option>
        </select>
    </th>
    
    <!-- Kelurahan Filter -->
    <th>
        <select class="filter-input filter-cascade-kelurahan" 
                data-column="kelurahan_id" 
                name="filter[kelurahan_id]" 
                disabled>
            <option value="">-- Select Kecamatan First --</option>
        </select>
    </th>
</tr>
```

**CSS Classes:**
- `filter-cascade-propinsi` - Province dropdown (always enabled)
- `filter-cascade-kabupaten` - City dropdown (enabled when province selected)
- `filter-cascade-kecamatan` - District dropdown (enabled when city selected)
- `filter-cascade-kelurahan` - Village dropdown (enabled when district selected)

#### JavaScript Cascade Logic

Add at bottom of `index.ctp`, after AJAX filter script:

```javascript
<script>
// Pre-load district data for client-side cascade filtering
var kabupatensData = <?= json_encode($kabupatensData) ?>;
var kecamatansData = <?= json_encode($kecamatansData) ?>;
var kelurahansData = <?= json_encode($kelurahansData) ?>;

// Initialize cascade filtering for filter row
$(document).ready(function() {
    // Province filter change
    $('.filter-cascade-propinsi').on('change', function() {
        var propinsiId = $(this).val();
        var $kabupatenSelect = $('.filter-cascade-kabupaten');
        var $kecamatanSelect = $('.filter-cascade-kecamatan');
        var $kelurahanSelect = $('.filter-cascade-kelurahan');
        
        // Reset child dropdowns
        $kabupatenSelect.html('<option value="">-- Select Kabupaten --</option>').prop('disabled', false);
        $kecamatanSelect.html('<option value="">-- Select Kecamatan First --</option>').prop('disabled', true);
        $kelurahanSelect.html('<option value="">-- Select Kelurahan First --</option>').prop('disabled', true);
        
        if (propinsiId) {
            // Filter kabupatens by province
            var filteredKabupatens = kabupatensData.filter(function(kab) {
                return kab.propinsi_id == propinsiId;
            });
            
            filteredKabupatens.forEach(function(kab) {
                $kabupatenSelect.append('<option value="' + kab.id + '">' + kab.title + '</option>');
            });
        } else {
            $kabupatenSelect.prop('disabled', true);
        }
    });
    
    // Kabupaten filter change
    $('.filter-cascade-kabupaten').on('change', function() {
        var kabupatenId = $(this).val();
        var $kecamatanSelect = $('.filter-cascade-kecamatan');
        var $kelurahanSelect = $('.filter-cascade-kelurahan');
        
        // Reset child dropdowns
        $kecamatanSelect.html('<option value="">-- Select Kecamatan --</option>').prop('disabled', false);
        $kelurahanSelect.html('<option value="">-- Select Kelurahan First --</option>').prop('disabled', true);
        
        if (kabupatenId) {
            // Filter kecamatans by kabupaten
            var filteredKecamatans = kecamatansData.filter(function(kec) {
                return kec.kabupaten_id == kabupatenId;
            });
            
            filteredKecamatans.forEach(function(kec) {
                $kecamatanSelect.append('<option value="' + kec.id + '">' + kec.title + '</option>');
            });
        } else {
            $kecamatanSelect.prop('disabled', true);
        }
    });
    
    // Kecamatan filter change
    $('.filter-cascade-kecamatan').on('change', function() {
        var kecamatanId = $(this).val();
        var $kelurahanSelect = $('.filter-cascade-kelurahan');
        
        // Reset child dropdown
        $kelurahanSelect.html('<option value="">-- Select Kelurahan --</option>').prop('disabled', false);
        
        if (kecamatanId) {
            // Filter kelurahans by kecamatan
            var filteredKelurahans = kelurahansData.filter(function(kel) {
                return kel.kecamatan_id == kecamatanId;
            });
            
            filteredKelurahans.forEach(function(kel) {
                $kelurahanSelect.append('<option value="' + kel.id + '">' + kel.title + '</option>');
            });
        } else {
            $kelurahanSelect.prop('disabled', true);
        }
    });
    
    // Initialize from URL parameters (if user bookmarked filtered page)
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('filter[propinsi_id]')) {
        var propinsiId = urlParams.get('filter[propinsi_id]');
        $('.filter-cascade-propinsi').val(propinsiId).trigger('change');
        
        // Wait for cascade to populate, then set kabupaten
        setTimeout(function() {
            if (urlParams.has('filter[kabupaten_id]')) {
                var kabupatenId = urlParams.get('filter[kabupaten_id]');
                $('.filter-cascade-kabupaten').val(kabupatenId).trigger('change');
                
                // Wait for cascade to populate, then set kecamatan
                setTimeout(function() {
                    if (urlParams.has('filter[kecamatan_id]')) {
                        var kecamatanId = urlParams.get('filter[kecamatan_id]');
                        $('.filter-cascade-kecamatan').val(kecamatanId).trigger('change');
                        
                        // Wait for cascade to populate, then set kelurahan
                        setTimeout(function() {
                            if (urlParams.has('filter[kelurahan_id]')) {
                                var kelurahanId = urlParams.get('filter[kelurahan_id]');
                                $('.filter-cascade-kelurahan').val(kelurahanId);
                            }
                        }, 100);
                    }
                }, 100);
            }
        }, 100);
    }
});
</script>
```

**Key Points:**
- Uses pre-loaded district data (client-side filtering, no AJAX)
- Triggers on `.change()` event of parent dropdown
- Populates child dropdown by filtering data array
- Disables child dropdowns when parent is empty
- Initializes from URL parameters (bookmarkable filters)

#### Differences from Form Cascade

| Aspect | Form Cascade (add/edit) | Filter Cascade (index) |
|--------|-------------------------|------------------------|
| **Element IDs** | `#PersonnelPropinsiId`, `#PersonnelKabupatenId` | `.filter-cascade-propinsi`, `.filter-cascade-kabupaten` |
| **Event Handler** | Triggers form validation | Triggers `filterTable()` via ajax-table-filter.js |
| **Initialization** | Loads from existing record (edit) or empty (add) | Loads from URL parameters (`?filter[propinsi_id]=X`) |
| **Disabled State** | Uses `:input[disabled]` selector workaround | Standard `disabled` attribute |
| **Data Loading** | Same (controller loads all districts) | Same (controller loads all districts) |
| **Documentation** | POST-BAKE WORKFLOW section | This section |

#### Testing Cascade in Index Filters

1. **Open index page** with district columns
2. **Select province** in filter row → Kabupaten dropdown should populate
3. **Select kabupaten** → Kecamatan dropdown should populate
4. **Select kecamatan** → Kelurahan dropdown should populate
5. **Clear province** → All child dropdowns should reset and disable
6. **Apply filter** → URL should show `?filter[propinsi_id]=X&filter[kabupaten_id]=Y`
7. **Refresh page** → Filters should persist from URL parameters
8. **Bookmark URL** → Should work when reopened

#### Common Issues

**Issue 1: Cascade doesn't work**
- **Cause:** JavaScript not loaded or district data not passed to view
- **Solution:** Check controller has `$this->set(compact('kabupatensData', ...))` and JSON encoding in view

**Issue 2: Wrong element IDs**
- **Cause:** Used form cascade IDs (`#PersonnelPropinsiId`) instead of filter classes (`.filter-cascade-propinsi`)
- **Solution:** Use CSS classes for filter row elements, not IDs

**Issue 3: URL parameters not persisting**
- **Cause:** Filter row not using `name="filter[propinsi_id]"` attributes
- **Solution:** Add `name` attributes to all filter dropdowns

**Issue 4: 403 Forbidden errors**
- **Cause:** Old code making AJAX requests to `/daerahs/get-kabupatens/X` (removed in favor of client-side)
- **Solution:** Remove AJAX requests, use pre-loaded data and client-side filtering

---

## 👁️ View Template (view.ctp)

**Location:** `src/Template/Bake/Template/view.ctp`

### Critical Feature: Nested Modal Tabs

**IMPORTANT:** View templates support **nested modals with independent tab states**. When viewing a related record in a modal (from a hasMany tab), that modal must have its own tab system that doesn't interfere with the parent page's tabs.

**Architecture:**
```
Main Page Tabs (Personnel view)
├── Details Tab (active)
├── PlannedJobs Tab
│   └── Table with "View" buttons
│       └── Opens Modal → PlannedJob Details
│           ├── Details Tab (modal, active)
│           ├── Actions Tab (modal)
│           └── Independent tab state!
└── StockOutgoings Tab
```

**Key Requirements:**
1. Each modal needs `initializeModalTabs()` function
2. Tab clicks must use scoped selectors (within modal only)
3. Event handlers must use `stopPropagation()` to prevent conflicts
4. Modal content loaded via AJAX with `?modal=1` parameter
5. Re-initialize tabs on `shown.bs.modal` event

### Page Structure

```html
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-md-8">
            <h2><?= h(${{ singularVar }}->{{ displayField }}) ?></h2>
        </div>
        <div class="col-md-4 text-right">
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', ${{ singularVar }}->id], ['class' => 'btn btn-warning']) ?>
            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', ${{ singularVar }}->id], ['confirm' => __('Are you sure?'), 'class' => 'btn btn-danger']) ?>
            <?= $this->Html->link(__('List'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>
    
    <!-- Tabs for hasMany associations -->
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-details"><?= __('Details') ?></a>
        </li>
        <!-- ADD TAB FOR EACH hasMany ASSOCIATION -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-child-records"><?= __('Child Records') ?></a>
        </li>
    </ul>
    
    <div class="tab-content">
        <!-- Details Tab -->
        <div id="tab-details" class="tab-pane fade show active">
            <table class="table table-bordered">
                <tr>
                    <th><?= __('ID') ?></th>
                    <td><?= $this->Number->format(${{ singularVar }}->id) ?></td>
                </tr>
                <!-- ... more fields ... -->
            </table>
        </div>
        
        <!-- Child Records Tab (for hasMany) -->
        <div id="tab-child-records" class="tab-pane fade">
            <!-- Related records table -->
        </div>
    </div>
</div>
```

### Features to Include

1. **Field Display Patterns**
   ```php
   // Text fields
   <tr>
       <th><?= __('Name') ?></th>
       <td><?= h(${{ singularVar }}->name) ?></td>
   </tr>
   
   // Foreign keys (belongsTo)
   <tr>
       <th><?= __('Department') ?></th>
       <td><?= ${{ singularVar }}->has('department') ? $this->Html->link(${{ singularVar }}->department->name, ['controller' => 'Departments', 'action' => 'view', ${{ singularVar }}->department->id]) : '' ?></td>
   </tr>
   
   // Dates
   <tr>
       <th><?= __('Created') ?></th>
       <td><?= h(${{ singularVar }}->created->format('Y-m-d H:i:s')) ?></td>
   </tr>
   
   // Numbers
   <tr>
       <th><?= __('Amount') ?></th>
       <td><?= $this->Number->format(${{ singularVar }}->amount) ?></td>
   </tr>
   
   // Boolean
   <tr>
       <th><?= __('Active') ?></th>
       <td><?= ${{ singularVar }}->is_active ? __('Yes') : __('No') ?></td>
   </tr>
   
   // Images
   <tr>
       <th><?= __('Image') ?></th>
       <td>
           <?php if (${{ singularVar }}->image_url): ?>
               <img src="<?= $this->Url->build('/' . ${{ singularVar }}->image_url) ?>" alt="Image" style="max-width: 300px;">
           <?php endif; ?>
       </td>
   </tr>
   
   // Files
   <tr>
       <th><?= __('File') ?></th>
       <td>
           <?php if (${{ singularVar }}->file_path): ?>
               <?= $this->Html->link(__('Download'), '/' . ${{ singularVar }}->file_path, ['target' => '_blank']) ?>
           <?php endif; ?>
       </td>
   </tr>
   
   // Long text
   <tr>
       <th><?= __('Description') ?></th>
       <td><?= $this->Text->autoParagraph(h(${{ singularVar }}->description)) ?></td>
   </tr>
   ```

2. **Tab Structure for hasMany (with Drag Scroll & Thumbnails)**
   ```html
   <!-- Tab Navigation -->
   <ul class="nav nav-tabs" role="tablist" id="mainTabs">
       <li class="nav-item">
           <a class="nav-link active" data-toggle="tab" href="#tab-details" role="tab">
               <?= __('Details') ?>
           </a>
       </li>
       <?php if (!empty(${{ singularVar }}->child_records)): ?>
       <li class="nav-item">
           <a class="nav-link" data-toggle="tab" href="#tab-child-records" role="tab">
               <?= __('Child Records') ?>
               <span class="badge badge-primary"><?= count(${{ singularVar }}->child_records) ?></span>
           </a>
       </li>
       <?php endif; ?>
   </ul>
   
   <!-- Tab Content -->
   <div class="tab-content" id="mainTabsContent">
       <!-- Child Records Tab -->
       <div id="tab-child-records" class="tab-pane fade" role="tabpanel">
           <div class="card">
               <div class="card-body">
                   <!-- CRITICAL: Horizontal drag scrolling wrapper -->
                   <div class="table-responsive table-drag-scroll" style="overflow-x: auto; cursor: grab;">
                       <table class="table table-sm table-bordered table-hover">
                           <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                               <tr>
                                   <!-- Actions column FIRST (left-sticky on hover) -->
                                   <th class="actions-column-left" style="position: sticky; left: 0; background: inherit; z-index: 10;">
                                       <?= __('Actions') ?>
                                   </th>
                                   <!-- Thumbnail column if image field exists -->
                                   <?php if (isset(${{ singularVar }}->child_records[0]->thumbnail_url) || isset(${{ singularVar }}->child_records[0]->image_url)): ?>
                                   <th><?= __('Thumbnail') ?></th>
                                   <?php endif; ?>
                                   <th><?= __('ID') ?></th>
                                   <th><?= __('Name') ?></th>
                                   <!-- Other columns -->
                               </tr>
                           </thead>
                           <tbody>
                               <?php foreach (${{ singularVar }}->child_records as $childRecord): ?>
                               <tr>
                                   <!-- Actions sticky on left, visible on hover -->
                                   <td class="actions-column-left" style="position: sticky; left: 0; background: white; z-index: 9;">
                                       <div class="action-buttons-hover">
                                           <?= $this->Html->link('<i class="fas fa-eye"></i>', 
                                               ['controller' => 'ChildRecords', 'action' => 'view', $childRecord->id], 
                                               ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]) ?>
                                           <?= $this->Html->link('<i class="fas fa-edit"></i>', 
                                               ['controller' => 'ChildRecords', 'action' => 'edit', $childRecord->id], 
                                               ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]) ?>
                                       </div>
                                   </td>
                                   <!-- Thumbnail column -->
                                   <?php if (isset($childRecord->thumbnail_url) || isset($childRecord->image_url)): ?>
                                   <td class="thumbnail-column">
                                       <?php if (!empty($childRecord->thumbnail_url)): ?>
                                           <img src="<?= $this->Url->build('/' . h($childRecord->thumbnail_url)) ?>" 
                                                alt="Thumbnail" 
                                                style="max-width: 60px; max-height: 60px; object-fit: cover; border-radius: 4px;"
                                                onerror="this.src='/img/no-image.png';">
                                       <?php elseif (!empty($childRecord->image_url)): ?>
                                           <img src="<?= $this->Url->build('/' . h($childRecord->image_url)) ?>" 
                                                alt="Image" 
                                                style="max-width: 60px; max-height: 60px; object-fit: cover; border-radius: 4px;"
                                                onerror="this.src='/img/no-image.png';">
                                       <?php else: ?>
                                           <span class="text-muted">No image</span>
                                       <?php endif; ?>
                                   </td>
                                   <?php endif; ?>
                                   <td><?= h($childRecord->id) ?></td>
                                   <td><?= h($childRecord->name) ?></td>
                                   <!-- Other columns -->
                               </tr>
                               <?php endforeach; ?>
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>
       </div>
   </div>
   
   <script>
   // Enable drag scrolling for hasMany tables
   $(document).ready(function() {
       $('.table-drag-scroll').each(function() {
           const container = this;
           let isDown = false;
           let startX;
           let scrollLeft;
           
           container.addEventListener('mousedown', (e) => {
               isDown = true;
               container.style.cursor = 'grabbing';
               startX = e.pageX - container.offsetLeft;
               scrollLeft = container.scrollLeft;
           });
           
           container.addEventListener('mouseleave', () => {
               isDown = false;
               container.style.cursor = 'grab';
           });
           
           container.addEventListener('mouseup', () => {
               isDown = false;
               container.style.cursor = 'grab';
           });
           
           container.addEventListener('mousemove', (e) => {
               if (!isDown) return;
               e.preventDefault();
               const x = e.pageX - container.offsetLeft;
               const walk = (x - startX) * 2;
               container.scrollLeft = scrollLeft - walk;
           });
       });
   });
   </script>
   ```

3. **JavaScript for Tabs**
   ```html
   <script>
   // Bootstrap 4 tab switching for main tabs
   $(document).ready(function() {
       $('#mainTabs a').on('click', function (e) {
           e.preventDefault();
           $(this).tab('show');
       });
       
       // Initialize tabs for dynamically loaded modals
       initializeModalTabs();
   });
   
   /**
    * Initialize tabs inside modals (for nested associations)
    * CRITICAL: Each modal needs independent tab state
    * Call this after modal content is loaded
    */
   function initializeModalTabs() {
       // Find all modals with tabs
       $('.modal').each(function() {
           var $modal = $(this);
           var modalId = $modal.attr('id');
           
           // Find tabs inside this modal
           $modal.find('.nav-tabs a[data-toggle="tab"]').each(function() {
               var $tab = $(this);
               
               // Remove any existing handlers to prevent duplicates
               $tab.off('click.modalTab');
               
               // Add scoped click handler for this modal's tabs
               $tab.on('click.modalTab', function (e) {
                   e.preventDefault();
                   e.stopPropagation(); // Prevent event bubbling
                   
                   // Show the tab within this modal's scope
                   var targetId = $(this).attr('href');
                   var $targetPane = $modal.find(targetId);
                   
                   // Hide all tab panes in this modal
                   $modal.find('.tab-pane').removeClass('show active');
                   
                   // Remove active class from all tabs in this modal
                   $modal.find('.nav-tabs .nav-link').removeClass('active');
                   
                   // Show target pane and activate tab
                   $targetPane.addClass('show active');
                   $(this).addClass('active');
               });
           });
       });
   }
   
   // Re-initialize tabs when modal is shown (for AJAX-loaded content)
   $(document).on('shown.bs.modal', '.modal', function() {
       initializeModalTabs();
   });
   </script>
   ```

4. **Modal View Links in hasMany Tables**
   ```html
   <!-- In hasMany tab, add View button with modal trigger -->
   <td>
       <?= $this->Html->link(__('View'), 
           ['controller' => 'ChildRecords', 'action' => 'view', $childRecord->id], 
           [
               'class' => 'btn btn-sm btn-info view-modal-trigger',
               'data-toggle' => 'modal',
               'data-target' => '#viewModal',
               'data-url' => $this->Url->build(['controller' => 'ChildRecords', 'action' => 'view', $childRecord->id, '?' => ['modal' => 1]])
           ]) ?>
   </td>
   ```

5. **Modal Container (Add to bottom of view.ctp)**
   ```html
   <!-- Modal for viewing related records -->
   <div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-lg" role="document">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title">View Details</h5>
                   <button type="button" class="close" data-dismiss="modal">
                       <span>&times;</span>
                   </button>
               </div>
               <div class="modal-body">
                   <!-- Content loaded via AJAX -->
               </div>
           </div>
       </div>
   </div>
   
   <script>
   // Load modal content via AJAX
   $(document).on('click', '.view-modal-trigger', function(e) {
       e.preventDefault();
       var url = $(this).data('url');
       
       $.ajax({
           url: url,
           type: 'GET',
           success: function(response) {
               $('#viewModal .modal-body').html(response);
               $('#viewModal').modal('show');
               
               // CRITICAL: Re-initialize tabs for modal content
               initializeModalTabs();
           },
           error: function() {
               alert('Failed to load content');
           }
       });
   });
   </script>
   ```

### Manual Steps Required

- ✅ Add tab for each hasMany association (not auto-generated yet)
- ✅ Check DATABASE_ASSOCIATIONS_REFERENCE.md for ✨ markers
- ✅ Add modal container at bottom of view.ctp
- ✅ Add `initializeModalTabs()` function for nested modals
- ✅ Include modal trigger buttons in hasMany tables
- ✅ Test all operations: index, view, add, edit, delete (see `manual_test_guide.md`)
- ✅ Check for association errors (see `COMPREHENSIVE_DATABASE_TEST_SUMMARY.md`)

---

## 🔍 AJAX Filter System for Related Tables (View Pages)

**CRITICAL:** All related tables on view pages should support **server-side AJAX filtering** for accurate search results.

### Architecture Overview

**Location:** `src/Controller/AjaxFilterTrait.php`

All controllers have access to two AJAX filtering methods:

1. **handleAjaxFilter()** - For index pages (page reload with filters)
2. **filterRelated()** - For view page related tables (AJAX, no page reload)

### Implementation Status

✅ **All 71 controllers** have AjaxFilterTrait  
✅ **filterRelated() method** available on all controllers  
✅ **Routes configured**: `/controller-name/filterRelated`  
✅ **Server-side filtering**: Searches entire database (not just visible rows)

### How It Works

---

## 📚 Related Documentation

**Must-read references for successful baking:**

1. **BAKE_CHECKLIST.md** - Pre-bake verification (prevents empty controllers)
2. **CONTROLLER_TWIG_FIX_NOV2025.md** - Troubleshooting empty controller files
3. **DATABASE_ASSOCIATIONS_REFERENCE.md** - Live database associations (auto-generated)
4. **CROSS_DATABASE_FIX_REPORT.md** - Cross-DB association patterns
5. **COMPREHENSIVE_DATABASE_TEST_SUMMARY.md** - Complete list of all errors found and fixed (Nov 14, 2025)
6. **ALIASED_ASSOCIATIONS_QUICK_REFERENCE.md** - Pattern guide for className requirements
7. **manual_test_guide.md** - Browser testing checklist with URLs for all databases
8. **MULTILEVEL_APPROVAL_DESIGN.md** - Approval workflow documentation
9. **QUICK_START_APPROVAL.md** - Approval system quick start
10. **ELEGANT_MENU_DOCUMENTATION.md** - Database-driven menu system

### Common Error Patterns (Nov 14, 2025)

Based on comprehensive testing of all 9 database connections (63+ tables):

**Pattern 1: Missing className in Aliased Associations**
```php
// ❌ WRONG (causes Auto-Tables error)
$this->belongsTo('ApprovedByPersonnels', [
    'foreignKey' => 'approved_by_personnel_id',
]);

// ✅ CORRECT
$this->belongsTo('ApprovedByPersonnels', [
    'className' => 'Personnels',  // MUST specify actual table
    'foreignKey' => 'approved_by_personnel_id',
    'strategy' => 'select',       // If cross-database
]);
```

**Pattern 2: Non-Existent Table References**
```php
// ❌ WRONG (table doesn't exist)
$this->belongsTo('References', [
    'foreignKey' => 'reference_id',
]);

// ✅ CORRECT (comment out until table is baked)
// $this->belongsTo('References', [
//     'foreignKey' => 'reference_id',
// ]);
```

**Pattern 3: Missing Line-Related Tables in Maintenance DB**
```php
// ❌ WRONG (6-7 associations to non-existent tables)
$this->belongsTo('LineSections', [...]);
$this->belongsTo('LineProcesses', [...]);
$this->belongsTo('Lines', [...]);
// ... etc

// ✅ CORRECT (comment out all Line* associations)
// Line-related associations commented out - tables don't exist yet
// $this->belongsTo('LineSections', [...]);
// ... etc
```

**Files Fixed (Nov 14, 2025):**
- ApprovalsTable.php (References removed)
- AccountingTransactionsTable.php (References removed)
- PurchaseReceiptsTable.php (ApprovedByPersonnels className added)
- StockOutgoingsTable.php (ApprovedByPersonnels className added)
- MaintenanceCardsTable.php (6 Line* associations commented)
- SafetyCardsTable.php (6 Line* associations commented)
- DailyActivitiesTable.php (7 Line* associations commented)

**Total:** 7 files, 22 associations fixed

**See:** `COMPREHENSIVE_DATABASE_TEST_SUMMARY.md` for complete details of all fixes applied.

---

## 🧪 Testing Workflow

### Standard Testing Procedure (After Every Bake)

**1. Verify Controller (< 1 minute)**
```powershell
Get-Item src\Controller\TableNameController.php | Select-Object Length
# Should be 5,000-15,000 bytes, NOT 0!
```

**2. Check Associations (< 2 minutes)**
```powershell
# Check for cross-DB associations without strategy
Select-String -Path src\Model\Table\TableNameTable.php -Pattern "belongsTo|hasMany" -Context 0,2

# Check for aliased associations without className
Select-String -Path src\Model\Table\TableNameTable.php -Pattern "belongsTo\('(.*Personnels|ParentMenus)" | Where-Object { $_.Line -notmatch "className" }
```

**3. Clear Cache (< 10 seconds)**
```powershell
bin\cake cache clear_all
```

**4. Browser Test (< 5 minutes per table)**

See `manual_test_guide.md` for complete URL list.

**Quick test URLs:**
- Index: `http://localhost:8765/table-name`
- Add: `http://localhost:8765/table-name/add`
- Edit: `http://localhost:8765/table-name/edit/1` (if data exists)
- View: `http://localhost:8765/table-name/view/1` (if data exists)

**What to check:**
- ✅ No "Auto-Tables" warnings
- ✅ No "doesn't exist" errors
- ✅ Dropdowns populate correctly
- ✅ Related data displays in tabs (view page)
- ✅ No JavaScript errors (F12 console)
- ✅ No 403 Forbidden errors (F12 network tab)

**5. Fix Any Errors (< 5 minutes)**

Common fixes:
- Add `'strategy' => 'select'` to cross-DB associations
- Add `'className' => 'ActualTable'` to aliased associations
- Comment out non-existent table references
- Remove duplicate form fields
- Replace `??` with `isset() ? : ` (PHP 5.6)

### Automated Testing (Optional)

**PowerShell test script:**
```powershell
.\test_all_tables.ps1  # Tests all 63+ tables across 9 databases
```

**Exports errors to:** `test_errors.json`

---

## 🔧 Quick Fix Commands

**Fix cross-database associations:**
```powershell
.\fix_all_cross_db_associations.ps1
```

**Fix PHP 5.6 syntax:**
```powershell
.\fix_php56_syntax_post_bake.ps1 TableName
```

**Fix aliased associations:**
```powershell
# Manual - add className to each aliased association
# See: ALIASED_ASSOCIATIONS_QUICK_REFERENCE.md for pattern
```

**Clear all caches:**
```powershell
bin\cake cache clear_all
```

---

## 📋 Post-Bake Checklist Summary

Use this for every bake operation:

- [ ] Controller file size > 0 bytes
- [ ] Cross-DB associations have `'strategy' => 'select'`
- [ ] Aliased associations have `'className' => 'ActualTable'`
- [ ] Non-existent tables commented out or removed
- [ ] No `??` operator (PHP 5.6 compatibility)
- [ ] No duplicate form fields (district dropdowns)
- [ ] Element IDs match JavaScript selectors (PascalCase)
- [ ] Cache cleared
- [ ] Browser test: index, add, edit, view
- [ ] No console errors (F12)
- [ ] No Auto-Tables warnings
- [ ] Related data displays correctly

**Time estimate:** 10-15 minutes per table (with experience)

**See:** `manual_test_guide.md` for complete browser testing URLs

---

**Example Scenario:**
```
Personnel View Page: /personnels/view/5
├── Related Companies Tab (hasMany)
│   ├── Table shows companies where personnel_id = 5
│   ├── User types "Toyota" in Name filter
│   └── AJAX: /personnels/filterRelated?model=Companies&foreign_key=personnel_id&foreign_value=5&filter[name]=Toyota
├── Related Departments Tab (hasMany)
│   └── Independent filtering (doesn't affect Companies tab)
```

**Benefits:**
- Each related table filters independently
- No page reload (smooth UX)
- Searches ALL related records (accurate results)
- Consistent operators across all tables

### Required Table Attributes

Add to each related table in hasMany tabs:

```html
<table class="table github-related-table" 
       data-ajax-filter="related"
       data-related-model="Companies"
       data-foreign-key="personnel_id"
       data-foreign-value="<?= $personnel->id ?>">
    <thead>
        <tr>
            <th><?= __('Actions') ?></th>
            <th><?= __('Name') ?></th>
            <th><?= __('Code') ?></th>
            <!-- Other columns -->
        </tr>
        <!-- Filter row -->
        <tr class="filter-row">
            <th></th> <!-- Empty for actions -->
            <th>
                <select class="filter-operator" data-column="name">
                    <option value="like">LIKE</option>
                    <option value="=">=</option>
                    <option value="starts_with">Starts</option>
                    <option value="ends_with">Ends</option>
                </select>
                <input type="text" class="filter-input" data-column="name" 
                       placeholder="<?= __('Filter...') ?>">
            </th>
            <th>
                <select class="filter-operator" data-column="code">
                    <option value="like">LIKE</option>
                    <option value="=">=</option>
                </select>
                <input type="text" class="filter-input" data-column="code" 
                       placeholder="<?= __('Filter...') ?>">
            </th>
        </tr>
    </thead>
    <tbody>
        <!-- Data rows loaded here -->
    </tbody>
</table>
```

### JavaScript Pattern for AJAX Filtering

Add to bottom of view.ctp (before `</script>`):

```javascript
/**
 * AJAX Filter for Related Tables on View Pages
 * Each table filters independently via server-side AJAX
 */
document.addEventListener('DOMContentLoaded', function() {
    const relatedTables = document.querySelectorAll('table[data-ajax-filter="related"]');
    
    relatedTables.forEach(function(table) {
        const filterInputs = table.querySelectorAll('.filter-input');
        const filterOperators = table.querySelectorAll('.filter-operator');
        const tbody = table.querySelector('tbody');
        
        const relatedModel = table.getAttribute('data-related-model');
        const foreignKey = table.getAttribute('data-foreign-key');
        const foreignValue = table.getAttribute('data-foreign-value');
        
        let filterTimeout;
        
        function debounceFilter() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(filterRelatedTable, 800);
        }
        
        function filterRelatedTable() {
            // Collect filter values and operators
            const filters = {};
            let hasFilters = false;
            
            filterInputs.forEach(function(input) {
                const column = input.getAttribute('data-column');
                const value = input.value.trim();
                
                if (value) {
                    hasFilters = true;
                    filters[column] = value;
                    
                    // Get operator
                    const operatorSelect = table.querySelector('.filter-operator[data-column="' + column + '"]');
                    if (operatorSelect) {
                        filters[column + '_operator'] = operatorSelect.value;
                    }
                }
            });
            
            // Build URL parameters
            const params = new URLSearchParams();
            params.append('model', relatedModel);
            params.append('foreign_key', foreignKey);
            params.append('foreign_value', foreignValue);
            
            for (let key in filters) {
                params.append('filter[' + key + ']', filters[key]);
            }
            
            // Get controller name from current URL
            const pathParts = window.location.pathname.split('/');
            const controller = pathParts[1]; // e.g., "personnels"
            
            // Show loading indicator
            tbody.innerHTML = '<tr><td colspan="100" class="text-center p-4">' +
                '<i class="fas fa-spinner fa-spin"></i> <?= __("Loading...") ?></td></tr>';
            
            // AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '/' + controller + '/filterRelated?' + params.toString(), true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            tbody.innerHTML = response.html;
                            
                            // Update count indicator if exists
                            const countEl = table.closest('.github-related-card')
                                ?.querySelector('.filter-count');
                            if (countEl) {
                                countEl.textContent = 'Showing ' + response.count + ' of ' + response.total;
                            }
                            
                            console.log('Filtered ' + relatedModel + ': ' + response.count + ' results');
                        } else {
                            tbody.innerHTML = '<tr><td colspan="100" class="text-center p-4 text-danger">' +
                                '<i class="fas fa-exclamation-triangle"></i> ' + 
                                (response.message || '<?= __("Filter failed") ?>') + '</td></tr>';
                        }
                    } catch (e) {
                        console.error('Invalid JSON response:', e);
                        tbody.innerHTML = '<tr><td colspan="100" class="text-center p-4 text-danger">' +
                            '<i class="fas fa-times-circle"></i> <?= __("Error parsing response") ?></td></tr>';
                    }
                } else {
                    tbody.innerHTML = '<tr><td colspan="100" class="text-center p-4 text-danger">' +
                        '<i class="fas fa-times-circle"></i> <?= __("Request failed") ?> (Status: ' + xhr.status + ')</td></tr>';
                }
            };
            
            xhr.onerror = function() {
                console.error('AJAX request failed');
                tbody.innerHTML = '<tr><td colspan="100" class="text-center p-4 text-danger">' +
                    '<i class="fas fa-exclamation-circle"></i> <?= __("Connection error") ?></td></tr>';
            };
            
            xhr.send();
        }
        
        // Event listeners - Debounced for text inputs, immediate for dropdowns
        filterInputs.forEach(function(input) {
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', filterRelatedTable);
            } else {
                input.addEventListener('input', debounceFilter);
            }
        });
        
        filterOperators.forEach(function(select) {
            select.addEventListener('change', filterRelatedTable);
        });
    });
});
```

### Filter Operators Supported

**Text Fields** (name, code, description, etc.):
- `like` - Contains (partial match): `WHERE field LIKE '%value%'`
- `=` - Exact match: `WHERE field = 'value'`
- `starts_with` - Starts with: `WHERE field LIKE 'value%'`
- `ends_with` - Ends with: `WHERE field LIKE '%value'`

**Date Fields** (created, updated, birth_date, etc.):
- `=` - Equal to
- `<` - Before
- `>` - After
- `<=` - On or before
- `>=` - On or after
- `between` - Date range (requires second input)

**Number Fields** (price, quantity, amount, etc.):
- Same as date fields: `=`, `<`, `>`, `<=`, `>=`, `between`

**Association Fields** (_id fields):
- Exact match only (dropdown selection)

### Controller filterRelated() Method

**Already implemented in AjaxFilterTrait** - No controller changes needed!

```php
// src/Controller/AjaxFilterTrait.php

public function filterRelated()
{
    $this->autoRender = false;
    $this->response = $this->response->withType('application/json');
    
    // Get parameters
    $relatedModel = $this->request->getQuery('model'); // e.g., "Companies"
    $foreignKey = $this->request->getQuery('foreign_key'); // e.g., "personnel_id"
    $foreignValue = $this->request->getQuery('foreign_value'); // e.g., 5
    $filters = $this->request->getQuery('filter', []); // Additional filters
    
    // Load related model
    $table = $this->loadModel($relatedModel);
    
    // Build query with foreign key filter
    $query = $table->find()
        ->where([$relatedModel . '.' . $foreignKey => $foreignValue]);
    
    // Apply additional filters with operators
    // Supports: like, =, <, >, <=, >=, starts_with, ends_with, between
    
    // Contain associations for display
    // Generate HTML rows
    
    return JSON response: {success, html, count, total, model}
}
```

### Routes Configuration

**Already configured in `config/routes.php`:**

```php
// AJAX filter routes for related tables
$routes->connect(
    '/:controller/filterRelated',
    ['action' => 'filterRelated']
);
```

This enables: `/personnels/filterRelated`, `/companies/filterRelated`, etc.

### URL Examples

**Index Page Filtering (Page Reload):**
```
/personnels/index?filter[name]=john&filter[name_operator]=like
/companies/index?filter[code]=ABC&filter[code_operator]=starts_with
```

**Related Table Filtering (AJAX):**
```
/personnels/filterRelated?model=Companies&foreign_key=personnel_id&foreign_value=5&filter[name]=Toyota&filter[name_operator]=like
/companies/filterRelated?model=Departments&foreign_key=company_id&foreign_value=10&filter[code]=IT
```

### Testing Checklist

**Index Page Filtering (Already Working):**
- [x] Filter inputs appear in header row
- [x] Type in filter → Wait 800ms → Page reloads
- [x] URL shows filter parameters
- [x] Results accurate across all pagination pages

**View Page Related Table Filtering:**
- [ ] Add table attributes: `data-ajax-filter="related"`, etc.
- [ ] Add filter row to table header
- [ ] Add AJAX JavaScript to view.ctp
- [ ] Test: Type in filter → Wait 800ms → Table updates via AJAX
- [ ] Verify: No page reload, only table updates
- [ ] Verify: Each tab filters independently

### Common Issues & Solutions

**Issue:** "filterRelated not found (404)"
```
Solution: Route must be added BEFORE fallbacks() in routes.php
```

**Issue:** AJAX returns HTML instead of JSON
```
Solution: Verify request header includes: X-Requested-With: XMLHttpRequest
```

**Issue:** Related table doesn't update
```
Check:
1. Table has data-ajax-filter="related" attribute
2. JavaScript loaded (check F12 console)
3. Controller has AjaxFilterTrait
```

**Issue:** Filters not working for specific field
```
Check:
1. Field name matches database column
2. Operator is supported (like, =, <, >, starts_with, ends_with)
3. Check browser console for JavaScript errors
```

### Documentation References

- **AJAX_FILTER_ALL_CONTROLLERS.md** - Complete implementation guide
- **AJAX_FILTER_COMPLETE_SUMMARY.md** - Implementation summary
- **AJAX_FILTER_QUICK_REFERENCE.md** - Quick reference card
- **SERVER_SIDE_FILTER_IMPLEMENTATION.md** - Index page details

### AI Memory Points

✅ **All controllers** have filterRelated() method available  
✅ **No controller changes** needed (uses AjaxFilterTrait)  
✅ **Add table attributes** to related tables in view.ctp  
✅ **Add AJAX JavaScript** to view.ctp (before closing script tag)  
✅ **Debouncing:** 800ms for text inputs, immediate for dropdowns  
✅ **Independent filtering:** Each related table filters separately  
✅ **Server-side search:** Accurate results across all records  
✅ **Consistent UX:** Same operators and behavior as index pages  

---

## ➕ Add Template (add.ctp)

**Location:** `src/Template/Bake/Template/add.ctp`

### Page Structure

```html
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3><?= __('Add {{ SingularHumanName }}') ?></h3>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(${{ singularVar }}, ['type' => 'file']) ?>
                    <fieldset>
                        <!-- Form fields -->
                    </fieldset>
                    <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Field Type Patterns

1. **Auto-Detected Field Types (Smart Pattern Recognition)**
   
   **CRITICAL**: AI must detect field names and apply appropriate input types automatically.
   
   ```php
   // ====== DATE FIELDS ======
   // Pattern: *date*, *tanggal*, *tgl*, *_at (created_at, updated_at)
   // Auto-convert to jQuery UI Datepicker
   <?= $this->Form->control('birth_date', [
       'type' => 'text',
       'class' => 'form-control datepicker',
       'placeholder' => 'YYYY-MM-DD',
       'autocomplete' => 'off',
       'data-date-format' => 'yy-mm-dd'
   ]) ?>
   
   // ====== TIME FIELDS ======
   // Pattern: *time*, *jam*, *waktu*
   <?= $this->Form->control('start_time', [
       'type' => 'time',
       'class' => 'form-control',
       'placeholder' => 'HH:MM'
   ]) ?>
   
   // ====== DATETIME FIELDS ======
   // Pattern: *datetime*, *timestamp*
   <?= $this->Form->control('scheduled_datetime', [
       'type' => 'text',
       'class' => 'form-control datetimepicker',
       'placeholder' => 'YYYY-MM-DD HH:MM:SS',
       'autocomplete' => 'off'
   ]) ?>
   
   // ====== EMAIL FIELDS ======
   // Pattern: *email*, *e_mail*, *mail*
   // HTML5 email validation + pattern check
   <?= $this->Form->control('email', [
       'type' => 'email',
       'class' => 'form-control',
       'placeholder' => 'user@example.com',
       'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
       'title' => 'Please enter a valid email address'
   ]) ?>
   
   // ====== PASSWORD FIELDS ======
   // Pattern: *password*, *pass*, *pwd*, *senha*
   // CRITICAL: Add password confirmation field + strength indicator
   <?= $this->Form->control('password', [
       'type' => 'password',
       'class' => 'form-control password-input',
       'placeholder' => '••••••••',
       'autocomplete' => 'new-password',
       'minlength' => 8,
       'pattern' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}',
       'title' => 'Must contain at least one number, one uppercase and lowercase letter, and at least 8 characters',
       'id' => 'passwordInput'
   ]) ?>
   <div class="password-strength-indicator" id="passwordStrength"></div>
   
   <?= $this->Form->control('password_confirm', [
       'type' => 'password',
       'class' => 'form-control',
       'placeholder' => 'Confirm password',
       'autocomplete' => 'new-password',
       'label' => __('Confirm Password'),
       'id' => 'passwordConfirm'
   ]) ?>
   
   // ====== PHONE FIELDS ======
   // Pattern: *phone*, *tel*, *mobile*, *hp*, *handphone*, *telepon*
   <?= $this->Form->control('phone', [
       'type' => 'tel',
       'class' => 'form-control',
       'placeholder' => '+62 812-3456-7890',
       'pattern' => '[\+]?[0-9]{10,15}',
       'title' => 'Please enter a valid phone number'
   ]) ?>
   
   // ====== URL FIELDS ======
   // Pattern: *url*, *website*, *link*, *site*
   <?= $this->Form->control('website', [
       'type' => 'url',
       'class' => 'form-control',
       'placeholder' => 'https://example.com',
       'pattern' => 'https?://.+',
       'title' => 'Please enter a valid URL starting with http:// or https://'
   ]) ?>
   
   // ====== NUMBER FIELDS ======
   // Pattern: *price*, *amount*, *qty*, *quantity*, *count*, *total*, *harga*, *jumlah*
   <?= $this->Form->control('price', [
       'type' => 'number',
       'class' => 'form-control',
       'placeholder' => '0.00',
       'step' => '0.01',
       'min' => '0',
       'data-number-format' => 'currency'
   ]) ?>
   
   // ====== COLOR FIELDS ======
   // Pattern: *color*, *colour*, *warna*
   <?= $this->Form->control('theme_color', [
       'type' => 'color',
       'class' => 'form-control form-control-color',
       'title' => 'Choose a color'
   ]) ?>
   
   // ====== IMAGE FIELDS ======
   // Pattern: *image*, *photo*, *picture*, *avatar*, *thumbnail*, *gambar*, *foto*
   // WITH LIVE PREVIEW
   <?= $this->Form->control('image_url', [
       'type' => 'file',
       'class' => 'form-control-file image-upload-input',
       'accept' => 'image/jpeg,image/png,image/gif,image/webp',
       'id' => 'imageUploadInput',
       'data-max-size' => '5242880' // 5MB in bytes
   ]) ?>
   <div id="imagePreview" class="mt-2" style="display: none;">
       <label><?= __('New Image Preview:') ?></label><br>
       <img id="imagePreviewImg" src="" alt="Preview" 
            style="max-width: 300px; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;">
   </div>
   
   // ====== FILE UPLOAD FIELDS ======
   // Pattern: *file*, *attachment*, *document*, *upload*, *dokumen*, *berkas*
   <?= $this->Form->control('file_path', [
       'type' => 'file',
       'class' => 'form-control-file',
       'accept' => '.pdf,.doc,.docx,.xls,.xlsx',
       'data-max-size' => '10485760' // 10MB
   ]) ?>
   
   // ====== JAPANESE INPUT FIELDS ======
   // Pattern: *katakana*, *hiragana*, *kanji*, *romaji*
   <?= $this->Form->control('name_katakana', [
       'class' => 'form-control kana-input',
       'data-kana' => 'katakana',
       'placeholder' => 'カタカナ'
   ]) ?>
   
   <?= $this->Form->control('name_hiragana', [
       'class' => 'form-control kana-input',
       'data-kana' => 'hiragana',
       'placeholder' => 'ひらがな'
   ]) ?>
   
   // ====== PERCENTAGE FIELDS ======
   // Pattern: *percent*, *percentage*, *rate*, *persen*
   <?= $this->Form->control('tax_rate', [
       'type' => 'number',
       'class' => 'form-control',
       'placeholder' => '10',
       'step' => '0.01',
       'min' => '0',
       'max' => '100',
       'append' => '<span class="input-group-text">%</span>'
   ]) ?>
   
   // ====== CURRENCY FIELDS ======
   // Pattern: *salary*, *revenue*, *cost*, *budget*, *gaji*
   <?= $this->Form->control('salary', [
       'type' => 'number',
       'class' => 'form-control currency-input',
       'placeholder' => '0',
       'step' => '1',
       'min' => '0',
       'data-currency' => 'IDR',
       'prepend' => '<span class="input-group-text">Rp</span>'
   ]) ?>
   
   // ====== SLUG FIELDS ======
   // Pattern: *slug*, *permalink*, *alias*
   // Auto-generate from title field
   <?= $this->Form->control('slug', [
       'class' => 'form-control slug-input',
       'placeholder' => 'auto-generated-from-title',
       'readonly' => true,
       'data-source' => '#title'
   ]) ?>
   
   // ====== ZIP/POSTAL CODE ======
   // Pattern: *zip*, *postal*, *postcode*, *kode_pos*
   <?= $this->Form->control('postal_code', [
       'type' => 'text',
       'class' => 'form-control',
       'placeholder' => '12345',
       'pattern' => '[0-9]{5}',
       'maxlength' => '5'
   ]) ?>
   
   // ====== FOREIGN KEYS ======
   // Pattern: *_id (department_id, category_id, etc.)
   // Auto-generate dropdown from associated table
   <?= $this->Form->control('department_id', [
       'options' => $departments,
       'empty' => '-- Select Department --',
       'class' => 'form-control select2',
       'data-placeholder' => 'Search department...'
   ]) ?>
   
   // ====== BOOLEAN FIELDS ======
   // Pattern: TINYINT(1), *is_*, *has_*, *can_*, *active*, *enabled*
   <?= $this->Form->control('is_active', [
       'type' => 'checkbox',
       'class' => 'form-check-input',
       'label' => ['class' => 'form-check-label'],
       'templates' => [
           'inputContainer' => '<div class="form-check">{{content}}</div>'
       ]
   ]) ?>
   
   // ====== TEXTAREA FIELDS ======
   // Pattern: *description*, *notes*, *comment*, *address*, *alamat*, *keterangan*, *catatan*
   // OR Database: TEXT, LONGTEXT, MEDIUMTEXT types
   <?= $this->Form->control('description', [
       'type' => 'textarea',
       'class' => 'form-control',
       'rows' => 4,
       'placeholder' => __('Enter description here...')
   ]) ?>
   
   // ====== RICH TEXT EDITOR ======
   // Pattern: *content*, *body*, *artikel*, *konten*
   <?= $this->Form->control('content', [
       'type' => 'textarea',
       'class' => 'form-control tinymce-editor',
       'rows' => 10,
       'data-editor' => 'tinymce'
   ]) ?>
   
   // ====== SELECT/ENUM FIELDS ======
   // Pattern: *status*, *type*, *category*, *gender*, *level*
   // Auto-detect from ENUM or predefined options
   <?= $this->Form->control('status', [
       'type' => 'select',
       'options' => ['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'],
       'class' => 'form-control',
       'empty' => '-- Select Status --'
   ]) ?>
   
   // ====== REGULAR TEXT FIELDS ======
   // Default fallback for unknown patterns
   <?= $this->Form->control('name', [
       'class' => 'form-control',
       'required' => true,
       'placeholder' => __('Enter name...')
   ]) ?>
   ```

2. **Form Confirmation & Preview Before Submit**
   ```php
   // CRITICAL: All forms must have confirmation dialog
   <?= $this->Form->create(${{ singularVar }}, [
       'type' => 'file',
       'id' => 'mainForm',
       'onsubmit' => 'return confirmFormSubmit(event)'
   ]) ?>
   ```

3. **Form Styling**
   ```php
   // Use Bootstrap 4 form-control classes
   'class' => 'form-control'
   'class' => 'form-control-file'
   'class' => 'form-check-input'
   
   // Add labels
   'label' => ['class' => 'form-label', 'text' => __('Field Name')]
   
   // Add placeholders
   'placeholder' => __('Enter value...')
   
   // Required fields
   'required' => true
   ```

4. **JavaScript Includes with Form Confirmation**
   ```html
   <!-- For datepicker -->
   <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
   <script>
   $(function() {
       $('.datepicker').datepicker({
           dateFormat: 'yy-mm-dd',
           changeMonth: true,
           changeYear: true
       });
   });
   </script>
   
   <!-- For image preview on upload -->
   <script>
   $(document).ready(function() {
       // Image upload preview
       $('.image-upload-input').on('change', function(e) {
           const file = e.target.files[0];
           if (file && file.type.startsWith('image/')) {
               const reader = new FileReader();
               reader.onload = function(event) {
                   $('#imagePreviewImg').attr('src', event.target.result);
                   $('#imagePreview').show();
               };
               reader.readAsDataURL(file);
           } else {
               $('#imagePreview').hide();
           }
       });
   });
   </script>
   
   <!-- For Japanese input -->
   <script src="http://localhost/static-assets/js/kana.js"></script>
   
   <!-- For password strength indicator -->
   <script>
   $(document).ready(function() {
       // Password strength indicator
       $('#passwordInput').on('keyup', function() {
           const password = $(this).val();
           let strength = 0;
           
           if (password.length >= 8) strength++;
           if (password.match(/[a-z]+/)) strength++;
           if (password.match(/[A-Z]+/)) strength++;
           if (password.match(/[0-9]+/)) strength++;
           if (password.match(/[$@#&!]+/)) strength++;
           
           const strengthLabels = ['Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'];
           const strengthColors = ['#e74c3c', '#e67e22', '#f39c12', '#27ae60', '#2ecc71'];
           
           $('#passwordStrength').html(
               '<div class="progress mt-2">' +
               '<div class="progress-bar" role="progressbar" style="width: ' + (strength * 20) + '%; background-color: ' + strengthColors[strength-1] + ';">' +
               strengthLabels[strength-1] +
               '</div></div>'
           );
       });
       
       // Password confirmation match
       $('#passwordConfirm').on('keyup', function() {
           const password = $('#passwordInput').val();
           const confirm = $(this).val();
           
           if (password !== confirm) {
               $(this).addClass('is-invalid').removeClass('is-valid');
               $(this).next('.invalid-feedback').remove();
               $(this).after('<div class="invalid-feedback">Passwords do not match</div>');
           } else {
               $(this).addClass('is-valid').removeClass('is-invalid');
               $(this).next('.invalid-feedback').remove();
           }
       });
       
       // Auto-slug generation
       $('[data-source]').each(function() {
           const $slug = $(this);
           const $source = $($slug.data('source'));
           
           $source.on('keyup', function() {
               const slug = $(this).val()
                   .toLowerCase()
                   .replace(/[^a-z0-9]+/g, '-')
                   .replace(/^-+|-+$/g, '');
               $slug.val(slug);
           });
       });
       
       // Currency formatting
       $('.currency-input').on('blur', function() {
           const value = parseFloat($(this).val());
           if (!isNaN(value)) {
               $(this).val(value.toLocaleString('id-ID'));
           }
       }).on('focus', function() {
           const value = $(this).val().replace(/[^0-9]/g, '');
           $(this).val(value);
       });
   });
   </script>
   
   <!-- CRITICAL: Form Confirmation & Preview -->
   <script>
   /**
    * Form Confirmation with Data Preview
    * Shows user all entered data before submitting
    * CRITICAL: Call this on form submit
    */
   function confirmFormSubmit(event) {
       event.preventDefault();
       
       const form = document.getElementById('mainForm');
       const formData = new FormData(form);
       
       // Build preview HTML
       let previewHtml = '<div class="form-preview-container">';
       previewHtml += '<h5 class="text-center mb-3"><i class="fas fa-check-circle text-success"></i> <?= __("Please confirm your data") ?></h5>';
       previewHtml += '<table class="table table-bordered table-sm">';
       
       // Iterate through form fields
       for (let [key, value] of formData.entries()) {
           // Skip hidden fields and buttons
           if (key.includes('_method') || key.includes('_Token')) continue;
           
           // Get field label from form
           const input = form.querySelector(`[name="${key}"]`);
           const label = input ? 
               (input.labels && input.labels[0] ? input.labels[0].textContent : key) : 
               key;
           
           // Handle file inputs
           if (input && input.type === 'file') {
               if (input.files && input.files[0]) {
                   value = input.files[0].name + ' (' + formatFileSize(input.files[0].size) + ')';
               } else {
                   continue; // Skip empty file inputs
               }
           }
           
           // Handle checkboxes
           if (input && input.type === 'checkbox') {
               value = input.checked ? '<?= __("Yes") ?>' : '<?= __("No") ?>';
           }
           
           // Skip empty values
           if (!value || value === '') continue;
           
           // Truncate long values
           if (typeof value === 'string' && value.length > 100) {
               value = value.substring(0, 100) + '...';
           }
           
           previewHtml += `<tr>
               <th style="width: 30%; background: #f8f9fa;">${label}</th>
               <td style="width: 70%;">${escapeHtml(String(value))}</td>
           </tr>`;
       }
       
       previewHtml += '</table>';
       previewHtml += '<p class="text-muted text-center"><i class="fas fa-info-circle"></i> <?= __("Review the information above before confirming") ?></p>';
       previewHtml += '</div>';
       
       // Show confirmation dialog with preview
       Swal.fire({
           title: '<?= __("Confirm Submission") ?>',
           html: previewHtml,
           icon: 'question',
           showCancelButton: true,
           confirmButtonColor: '#667eea',
           cancelButtonColor: '#6c757d',
           confirmButtonText: '<i class="fas fa-check"></i> <?= __("Confirm & Submit") ?>',
           cancelButtonText: '<i class="fas fa-times"></i> <?= __("Cancel") ?>',
           width: '700px',
           customClass: {
               popup: 'form-preview-modal',
               confirmButton: 'btn btn-primary',
               cancelButton: 'btn btn-secondary'
           }
       }).then((result) => {
           if (result.isConfirmed) {
               // Show loading indicator
               Swal.fire({
                   title: '<?= __("Saving...") ?>',
                   html: '<i class="fas fa-spinner fa-spin fa-3x text-primary"></i>',
                   showConfirmButton: false,
                   allowOutsideClick: false
               });
               
               // Submit form
               form.submit();
           }
       });
       
       return false;
   }
   
   // Helper: Format file size
   function formatFileSize(bytes) {
       if (bytes === 0) return '0 Bytes';
       const k = 1024;
       const sizes = ['Bytes', 'KB', 'MB', 'GB'];
       const i = Math.floor(Math.log(bytes) / Math.log(k));
       return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
   }
   
   // Helper: Escape HTML
   function escapeHtml(text) {
       const map = {
           '&': '&amp;',
           '<': '&lt;',
           '>': '&gt;',
           '"': '&quot;',
           "'": '&#039;'
       };
       return text.replace(/[&<>"']/g, m => map[m]);
   }
   </script>
   
   <!-- SweetAlert2 for beautiful confirmation dialogs -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
   <!-- Custom CSS for form preview -->
   <style>
   .form-preview-container {
       max-height: 400px;
       overflow-y: auto;
       text-align: left;
   }
   .form-preview-modal .swal2-html-container {
       overflow: visible !important;
   }
   </style>
   ```

---

## ✏️ Edit Template (edit.ctp)

**Location:** `src/Template/Bake/Template/edit.ctp`

### Structure

Same as Add template, but with additional features:

1. **Show Current File/Image with Upload Preview**
   ```php
   <!-- CRITICAL: Check if file exists on server before displaying -->
   <?php if (!empty(${{ singularVar }}->image_url) && file_exists(WWW_ROOT . ${{ singularVar }}->image_url)): ?>
   <div class="form-group">
       <label><?= __('Current Image') ?></label><br>
       <div id="currentImageContainer">
           <img src="<?= $this->Url->build('/' . ${{ singularVar }}->image_url) ?>" 
                alt="Current Image" 
                class="img-thumbnail"
                style="max-width: 300px; max-height: 300px; border: 2px solid #667eea;">
       </div>
       <p class="text-muted mt-2">
           <i class="fas fa-info-circle"></i> 
           <?= __('Upload new image to replace') ?>
       </p>
   </div>
   <?php endif; ?>
   
   <!-- Image upload field with preview -->
   <?= $this->Form->control('image_url', [
       'type' => 'file',
       'class' => 'form-control-file image-upload-input',
       'accept' => 'image/*',
       'id' => 'imageUploadInput',
       'label' => __('Upload New Image')
   ]) ?>
   
   <!-- NEW IMAGE PREVIEW (appears when file selected) -->
   <div id="imagePreview" class="mt-3" style="display: none;">
       <label class="text-success">
           <i class="fas fa-check-circle"></i> 
           <?= __('New Image Preview:') ?>
       </label><br>
       <img id="imagePreviewImg" 
            src="" 
            alt="Preview" 
            class="img-thumbnail"
            style="max-width: 300px; max-height: 300px; border: 2px solid #2ecc71;">
       <p class="text-muted mt-2">
           <i class="fas fa-arrow-up"></i> 
           <?= __('This image will replace the current one when you save') ?>
       </p>
   </div>
   
   <?php if (!empty(${{ singularVar }}->file_path) && file_exists(WWW_ROOT . ${{ singularVar }}->file_path)): ?>
   <div class="form-group">
       <label><?= __('Current File') ?></label><br>
       <?= $this->Html->link(
           '<i class="fas fa-download"></i> ' . __('Download Current File'), 
           '/' . ${{ singularVar }}->file_path, 
           ['target' => '_blank', 'class' => 'btn btn-sm btn-info', 'escape' => false]
       ) ?>
       <p class="text-muted mt-2"><?= __('Upload new file to replace') ?></p>
   </div>
   <?php endif; ?>
   
   <script>
   $(document).ready(function() {
       // Image upload preview for edit form
       $('#imageUploadInput').on('change', function(e) {
           const file = e.target.files[0];
           if (file && file.type.startsWith('image/')) {
               const reader = new FileReader();
               reader.onload = function(event) {
                   $('#imagePreviewImg').attr('src', event.target.result);
                   $('#imagePreview').slideDown(300);
                   
                   // Optional: Fade out current image to show it will be replaced
                   $('#currentImageContainer img').css('opacity', '0.5');
               };
               reader.readAsDataURL(file);
           } else {
               $('#imagePreview').slideUp(300);
               $('#currentImageContainer img').css('opacity', '1');
           }
       });
   });
   </script>
   ```

2. **Delete Button with Enhanced Confirmation**
   ```php
   <div class="card-footer">
       <?= $this->Form->button(__('Save Changes'), ['class' => 'btn btn-primary']) ?>
       <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
       
       <!-- Enhanced delete with SweetAlert confirmation -->
       <button type="button" class="btn btn-danger float-right" onclick="confirmDelete(<?= ${{ singularVar }}->id ?>)">
           <i class="fas fa-trash"></i> <?= __('Delete') ?>
       </button>
   </div>
   
   <script>
   function confirmDelete(id) {
       Swal.fire({
           title: '<?= __("Are you sure?") ?>',
           html: '<p><?= __("You are about to delete:") ?></p><strong><?= h(${{ singularVar }}->{{ displayField }}) ?></strong><br><br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?= __("This action cannot be undone!") ?></span>',
           icon: 'warning',
           showCancelButton: true,
           confirmButtonColor: '#e74c3c',
           cancelButtonColor: '#6c757d',
           confirmButtonText: '<i class="fas fa-trash"></i> <?= __("Yes, delete it!") ?>',
           cancelButtonText: '<i class="fas fa-times"></i> <?= __("Cancel") ?>',
           customClass: {
               confirmButton: 'btn btn-danger',
               cancelButton: 'btn btn-secondary'
           }
       }).then((result) => {
           if (result.isConfirmed) {
               // Create hidden form for DELETE request
               const form = document.createElement('form');
               form.method = 'POST';
               form.action = '<?= $this->Url->build(['action' => 'delete', ${{ singularVar }}->id]) ?>';
               
               // Add CSRF token
               const csrfInput = document.createElement('input');
               csrfInput.type = 'hidden';
               csrfInput.name = '_csrfToken';
               csrfInput.value = '<?= $this->request->getAttribute('csrfToken') ?>';
               form.appendChild(csrfInput);
               
               // Add method override
               const methodInput = document.createElement('input');
               methodInput.type = 'hidden';
               methodInput.name = '_method';
               methodInput.value = 'DELETE';
               form.appendChild(methodInput);
               
               document.body.appendChild(form);
               form.submit();
           }
       });
   }
   </script>
   ```

---

## 🎯 Quick Decision Tree for AI

### When Creating Model:

```
1. Check DATABASE_ASSOCIATIONS_REFERENCE.md for table
2. Extract connection name (auto-detect)
3. List all associations (belongsTo, hasMany)
4. Add 'strategy' => 'select' for cross-DB associations
5. Set display field (name > title > code > id)
6. Add validation rules
```

### When Creating Controller:

```
1. Include ExportTrait
2. Add pagination with contain
3. Handle file uploads in add/edit
4. Override layout for exportPdf()
5. Use Flash messages consistently
6. Include AJAX filter support
```

### When Creating Index:

```
1. Purple gradient table header
2. Add export buttons (CSV/Excel/PDF)
3. Hover action buttons (view/edit/delete) - STICKY LEFT on hover
4. Horizontal drag scrolling wrapper (cursor: grab)
5. Include AJAX filter JavaScript
6. Max 10 columns, exclude binary/large text
7. Show foreign key relationships as links
8. AUTO-ADD thumbnail column if image/photo field exists
9. Actions column positioned LEFT (not right) with sticky positioning
```

### When Creating View:

```
1. Check for hasMany associations
2. Create tab for each hasMany
3. Show details in first tab
4. Related records in subsequent tabs with DRAG SCROLL
5. Image preview, file download links
6. Foreign keys as clickable links
7. Format dates, numbers, booleans properly
8. Add initializeModalTabs() function for nested modals
9. Include modal container at bottom
10. Add modal trigger buttons in hasMany tables
11. Sticky LEFT action buttons (hover-visible) in hasMany tables
12. Auto-add thumbnail column if image field exists in hasMany records
```

### When Creating Add/Edit:

```
1. Form type='file' if image/file fields exist
2. Add onsubmit='return confirmFormSubmit(event)' to form
3. Bootstrap 4 form-control classes
4. CRITICAL: Auto-detect field types by NAME PATTERN:
   - *date*, *tanggal*, *_at → datepicker
   - *time*, *jam → time input
   - *datetime*, *timestamp → datetimepicker
   - *email*, *mail → email validation
   - *password*, *pass → password + strength indicator + confirm field
   - *phone*, *tel*, *mobile → phone input with pattern
   - *url*, *website*, *link → URL validation
   - *price*, *amount*, *salary → number/currency input
   - *color*, *warna → color picker
   - *image*, *photo*, *avatar → file input + LIVE PREVIEW
   - *file*, *attachment*, *document → file upload
   - *katakana*, *hiragana → Japanese kana.js
   - *percent*, *rate → percentage (0-100)
   - *slug*, *permalink → auto-generate from title
   - *zip*, *postal*, *kode_pos → postal code pattern
   - *description*, *notes*, *address → textarea
   - *content*, *body*, *artikel → rich text editor (TinyMCE)
   - *_id → foreign key dropdown
   - TINYINT(1), *is_*, *has_*, *active → checkbox
5. Show current file/image in edit (check file_exists first)
6. Show NEW image preview when upload selected (both add & edit)
7. Fade current image when new upload selected
8. Password fields: Add strength indicator + confirmation match
9. Auto-slug generation from title/name field
10. Currency fields: Format with thousand separators
11. CRITICAL: confirmFormSubmit() shows data preview before submit
12. Include SweetAlert2 for beautiful confirmation dialogs
13. Enhanced delete confirmation with preview of what will be deleted
```

---

## 🍔 Database-Driven Menu System

**Location:** `src/Template/Element/elegant_menu.ctp`  
**Database Table:** `asahi_commons.menus` (in `common` connection)  
**Loaded In:** `AppController::beforeRender()`

### Menu Architecture

**Database-Driven Navigation:**
- Tab-based horizontal menu (not sidebar)
- Hierarchical structure with `parent_id`
- Position-based ordering
- Active/inactive toggle (`is_active` field)

**Menu Structure:**
```
Database Table: menus
├── id (Primary Key)
├── parent_id (NULL for main tabs, ID for submenus)
├── title (Display text)
├── url (Controller/Action path or external URL)
├── icon (Font Awesome class, optional)
├── position (Sort order)
├── is_active (Show/Hide menu)
├── created, modified
```

### Menu Loading in AppController

```php
// src/Controller/AppController.php
public function beforeRender(Event $event)
{
    parent::beforeRender($event);
    
    // Load database-driven menu
    $menusTable = TableRegistry::getTableLocator()->get('Menus');
    $menusTable->setConnection(ConnectionManager::get('common'));
    
    $menus = $menusTable->find('threaded')
        ->where(['parent_id IS' => null, 'is_active' => 1])
        ->contain(['ChildMenus' => function($q) {
            return $q->where(['ChildMenus.is_active' => 1])
                     ->order(['ChildMenus.position' => 'ASC']);
        }])
        ->order(['Menus.position' => 'ASC'])
        ->all();
    
    $this->set('navigationMenus', $menus);
}
```

### Menu Template (elegant_menu.ctp)

```php
<!-- Tab-based navigation -->
<nav class="main-navigation" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container-fluid">
        <ul class="nav-tabs-menu">
            <?php foreach ($navigationMenus as $menu): ?>
                <li class="nav-tab-item <?= !empty($menu->child_menus) ? 'has-dropdown' : '' ?>">
                    <!-- Main Tab -->
                    <a href="<?= $this->Url->build($menu->url) ?>" 
                       class="nav-tab-link">
                        <?php if ($menu->icon): ?>
                            <i class="<?= h($menu->icon) ?>"></i>
                        <?php endif; ?>
                        <?= h($menu->title) ?>
                    </a>
                    
                    <!-- Submenu (Sandwich Dropdown) -->
                    <?php if (!empty($menu->child_menus)): ?>
                        <div class="submenu-dropdown">
                            <?php foreach ($menu->child_menus as $submenu): ?>
                                <a href="<?= $this->Url->build($submenu->url) ?>" 
                                   class="submenu-item">
                                    <?php if ($submenu->icon): ?>
                                        <i class="<?= h($submenu->icon) ?>"></i>
                                    <?php endif; ?>
                                    <?= h($submenu->title) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
```

### Sandwich Dropdown Submenu (Smart Positioning)

**Feature:** Submenus automatically position themselves to prevent overflow off-screen.

**JavaScript:** `http://localhost/static-assets/js/submenu-position.js`

**Smart Positioning Logic:**
```javascript
$(document).ready(function() {
    // Calculate submenu position on hover
    $('.nav-tab-item.has-dropdown').on('mouseenter', function() {
        var $item = $(this);
        var $submenu = $item.find('.submenu-dropdown');
        
        // Get positions
        var itemOffset = $item.offset();
        var submenuWidth = $submenu.outerWidth();
        var windowWidth = $(window).width();
        var rightEdge = itemOffset.left + submenuWidth;
        
        // Remove previous positioning classes
        $submenu.removeClass('align-left align-right');
        
        // If submenu goes off right edge, align to right
        if (rightEdge > windowWidth) {
            $submenu.addClass('align-right');
        } else {
            $submenu.addClass('align-left');
        }
    });
    
    // Show/hide submenu
    $('.nav-tab-item.has-dropdown').hover(
        function() {
            $(this).find('.submenu-dropdown').stop(true, true).fadeIn(200);
        },
        function() {
            $(this).find('.submenu-dropdown').stop(true, true).fadeOut(200);
        }
    );
});
```

### Menu CSS Patterns

```css
/* Main Navigation */
.main-navigation {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 0;
    margin-bottom: 20px;
}

/* Tab Items */
.nav-tabs-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}

.nav-tab-item {
    position: relative;
}

.nav-tab-link {
    display: block;
    padding: 15px 20px;
    color: white;
    text-decoration: none;
    transition: background 0.3s;
}

.nav-tab-link:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Submenu Dropdown (Sandwich Style) */
.submenu-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-width: 200px;
    z-index: 1000;
    border-radius: 4px;
}

/* Smart positioning classes */
.submenu-dropdown.align-left {
    left: 0;
}

.submenu-dropdown.align-right {
    left: auto;
    right: 0;
}

.submenu-item {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    border-bottom: 1px solid #eee;
    transition: background 0.2s;
}

.submenu-item:hover {
    background: #f8f9fa;
    color: #667eea;
}

.submenu-item:last-child {
    border-bottom: none;
}
```

### Menu Management

**Access:** `http://localhost/asahi_v3/menus`

**CRUD Operations:**
- Add new main tab: `parent_id = NULL`
- Add submenu item: `parent_id = [Main Menu ID]`
- Set position for ordering
- Toggle `is_active` to show/hide
- Use Font Awesome icons (e.g., `fas fa-home`)

**Example Menu Data:**
```sql
-- Main Tab: Inventory
INSERT INTO menus (parent_id, title, url, icon, position, is_active) 
VALUES (NULL, 'Inventory', '/inventories', 'fas fa-boxes', 1, 1);

-- Submenu: Stock Incoming
INSERT INTO menus (parent_id, title, url, icon, position, is_active) 
VALUES (1, 'Stock Incoming', '/stock-incomings', 'fas fa-arrow-down', 1, 1);

-- Submenu: Stock Outgoing
INSERT INTO menus (parent_id, title, url, icon, position, is_active) 
VALUES (1, 'Stock Outgoing', '/stock-outgoings', 'fas fa-arrow-up', 2, 1);
```

### Key Features

1. **Click-to-Expand Tabs** - Main tabs are clickable (not hover-only)
2. **Hover Submenus** - Submenus appear on hover (sandwich dropdown style)
3. **Smart Positioning** - Submenus auto-position to prevent overflow
4. **Database-Driven** - No hardcoded menus in templates
5. **Hierarchical** - Support parent-child relationships
6. **Position-Based** - Custom ordering via `position` field
7. **Active/Inactive** - Toggle visibility without deleting
8. **Icon Support** - Font Awesome icons for visual enhancement

### Critical for AI to Remember

When generating layouts or controllers:

✅ **Menu is auto-loaded** - No need to manually load in controllers  
✅ **Variable name** - `$navigationMenus` available in all views  
✅ **Connection** - Menus table uses `common` connection  
✅ **Threaded find** - Uses `find('threaded')` for hierarchy  
✅ **JavaScript required** - Include `submenu-position.js` in layout  
✅ **Purple gradient** - Menu background matches app theme  
✅ **No hardcoding** - Never hardcode menu items in templates  

---

## 🌐 Multilingual i18n System

**Supported Languages:** English (eng), Indonesian (ind), Japanese (jpn)  
**Translation Storage:** Database-driven with PO file generation  
**Auto-Detection:** Browser language + Session persistence

### Locale Configuration

**Location:** `config/bootstrap.php` or `src/Controller/AppController.php`

```php
/**
 * Locale directory paths for translation files
 */
if (!defined('LOCALE_JPN')) {
    define('LOCALE_JPN', APP . 'locale' . DS . 'jpn' . DS . 'LC_MESSAGES' . DS . 'default.po');
}
if (!defined('LOCALE_IND')) {
    define('LOCALE_IND', APP . 'locale' . DS . 'ind' . DS . 'LC_MESSAGES' . DS . 'default.po');
}
if (!defined('LOCALE_ENG')) {
    define('LOCALE_ENG', APP . 'locale' . DS . 'eng' . DS . 'LC_MESSAGES' . DS . 'default.po');
}
if (!defined('PERSISTENT')) {
    define('PERSISTENT', TMP . 'cache' . DS . 'persistent' . DS);
}
```

### Browser Language Detection

**CRITICAL:** Auto-detect user's browser language and set session locale.

```php
// In AppController
protected function _checkBrowserLanguage() {
    if (!$this->Session->check('Config.language') && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        preg_match_all(
            '/([a-z]{2,8})(-[a-z]{1,8})?(\\s*;\\s*q\\s*=\\s*(1|0\\.[0-9]+))?/i',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            $lang_parse
        );

        if (!empty($lang_parse[1])) {
            $langs = array_combine($lang_parse[1], $lang_parse[4]);
            
            // Normalize quality values
            foreach ($langs as $lang => $val) {
                if ($val === '') $langs[$lang] = 1;
            }
            arsort($langs, SORT_NUMERIC);

            // Map browser codes to app locales
            $availableLangs = [
                'en' => 'eng',  // English
                'ja' => 'jpn',  // Japanese
                'id' => 'ind'   // Indonesian
            ];
            
            foreach ($langs as $lang => $val) {
                foreach ($availableLangs as $key => $code) {
                    if (strpos($lang, $key) === 0) {
                        $this->Session->write('Config.language', $code);
                        return;
                    }
                }
            }
            
            // Default to Indonesian if no match
            $this->Session->write('Config.language', 'ind');
        }
    }
}
```

### Language Persistence System

```php
function _checkLanguage() {
    $this->_checkBrowserLanguage();
    
    if ($this->Session->check('Config.language')) {
        Configure::write('language', $this->Session->read('Config.language'));
    } else {
        Configure::write('language', Configure::read('language.default'));
    }
    
    $this->L10n = new L10n();
    
    if (!$this->Session->check('Config.language') || $this->name == "Switchto") {
        $supported_lang = Configure::read('language.supported');
        $default_lang = Configure::read('language.default');
        $lang = null;
        
        // Language switching via URL
        if (($this->params['controller'] == "switchto")) {
            $lang = $this->params['action'];
        }

        // Cookie configuration
        $this->Cookie->write('domain', $_SERVER['SERVER_NAME']);
        $this->Cookie->write('name', Configure::read('Security.salt'));
        $this->Cookie->write('time', '+360 days');
        $this->Cookie->write('path', '+360 days');
        $this->Cookie->write('key', Configure::read('Security.cipherSeed'));

        // Check supported languages
        if (is_array($supported_lang)) {
            if (!$lang || !in_array($lang, $supported_lang)) {
                // Try cookie first
                if ($this->Cookie->read('configuration.lang')) {
                    $lang = $this->Cookie->read('configuration.lang');
                    if (!in_array($lang, $supported_lang)) {
                        $lang = null;
                    }
                }
                
                // Try browser language
                if (!$lang) {
                    $browserLang = explode(',', env('HTTP_ACCEPT_LANGUAGE'));
                    foreach ($browserLang as $langKey) {
                        $langKey = trim(explode(';', $langKey)[0]);
                        if (isset($this->L10n->__l10nCatalog[$langKey]) &&
                            in_array($this->L10n->__l10nCatalog[$langKey]['locale'], $supported_lang)) {
                            $lang = $this->L10n->__l10nCatalog[$langKey]['locale'];
                            break;
                        }
                    }
                }
            }
        }
        
        // Default language fallback
        if (!$lang) {
            $lang = $default_lang;
        }
        
        // Set language and persist
        $this->L10n->get($lang);
        $this->Cookie->write('language', $lang);
        $this->Session->write('Config.language', $lang);
        Configure::write('Config.language', $lang);
        
        // Redirect after language switch
        if ($this->params['controller'] == "switchto") {
            $this->redirect($this->referer());
        }
    }
    
    return false;
}
```

### Translation Database to PO File Generator

**CRITICAL:** System generates PO files from database translations dynamically.

```php
public function writePO() {
    // Ensure locale paths are defined
    if (!defined('LOCALE_JPN')) {
        define('LOCALE_JPN', APP . 'locale' . DS . 'jpn' . DS . 'LC_MESSAGES' . DS . 'default.po');
    }
    if (!defined('LOCALE_IND')) {
        define('LOCALE_IND', APP . 'locale' . DS . 'ind' . DS . 'LC_MESSAGES' . DS . 'default.po');
    }
    if (!defined('LOCALE_ENG')) {
        define('LOCALE_ENG', APP . 'locale' . DS . 'eng' . DS . 'LC_MESSAGES' . DS . 'default.po');
    }
    if (!defined('PERSISTENT')) {
        define('PERSISTENT', TMP . 'cache' . DS . 'persistent' . DS);
    }

    $content_jpn = '';
    $content_ind = '';
    $content_eng = '';
    
    // Fetch all translations from database
    $language = $this->Translation->find('all');
    
    for ($i = 0; $i < count($language); $i++) {
        // Generate Japanese PO content
        if (is_file(LOCALE_JPN)) {
            $content_jpn .= <<<EOD

msgid "{$language[$i]['Translation']['title']}"
msgstr "{$language[$i]['Translation']['jpn']}"

EOD;
        }
        
        // Generate Indonesian PO content
        if (is_file(LOCALE_IND)) {
            $content_ind .= <<<EOD

msgid "{$language[$i]['Translation']['title']}"
msgstr "{$language[$i]['Translation']['ind']}"

EOD;
        }
        
        // Generate English PO content
        if (is_file(LOCALE_ENG)) {
            $content_eng .= <<<EOD

msgid "{$language[$i]['Translation']['title']}"
msgstr "{$language[$i]['Translation']['eng']}"

EOD;
        }
    }

    // Write Japanese PO file
    $handle_jpn = fopen(LOCALE_JPN, 'w');
    fwrite($handle_jpn, $content_jpn);
    fclose($handle_jpn);

    // Write Indonesian PO file
    $handle_ind = fopen(LOCALE_IND, 'w');
    fwrite($handle_ind, $content_ind);
    fclose($handle_ind);

    // Write English PO file
    $handle_eng = fopen(LOCALE_ENG, 'w');
    fwrite($handle_eng, $content_eng);
    fclose($handle_eng);

    // Clear cached translations
    $persistent = $this->readDir(PERSISTENT);
    for ($i = 0; $i < count($persistent); $i++) {
        if (strpos($persistent[$i], 'default') !== false) {
            unlink(PERSISTENT . $persistent[$i]);
        }
    }
}
```

### LanguagesController Pattern

**CRITICAL:** Always regenerate PO files after translation CRUD operations.

```php
if (!defined('LOCALE_JPN')) {
    define('LOCALE_JPN', APP . 'locale' . DS . 'jpn' . DS . 'LC_MESSAGES' . DS . 'default.po');
}
if (!defined('LOCALE_IND')) {
    define('LOCALE_IND', APP . 'locale' . DS . 'ind' . DS . 'LC_MESSAGES' . DS . 'default.po');
}
if (!defined('LOCALE_ENG')) {
    define('LOCALE_ENG', APP . 'locale' . DS . 'eng' . DS . 'LC_MESSAGES' . DS . 'default.po');
}
if (!defined('PERSISTENT')) {
    define('PERSISTENT', TMP . 'cache' . DS . 'persistent' . DS);
}

class LanguagesController extends AppController {
    
    public function add() {
        $language = $this->Language->newEntity();
        if ($this->request->is('post')) {
            $language = $this->Language->patchEntity($language, $this->request->getData());
            if ($this->Language->save($language)) {
                // CRITICAL: Regenerate PO files after adding translation
                parent::writePO();
                
                $this->Flash->success(__('The translation has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The translation could not be saved. Please, try again.'));
        }
        $this->set(compact('language'));
    }
    
    public function edit($id = null) {
        $language = $this->Language->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $language = $this->Language->patchEntity($language, $this->request->getData());
            if ($this->Language->save($language)) {
                // CRITICAL: Regenerate PO files after editing translation
                parent::writePO();
                
                $this->Flash->success(__('The translation has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The translation could not be updated. Please, try again.'));
        }
        $this->set(compact('language'));
    }
    
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $language = $this->Language->get($id);
        if ($this->Language->delete($language)) {
            // CRITICAL: Regenerate PO files after deleting translation
            parent::writePO();
            
            $this->Flash->success(__('The translation has been deleted.'));
        } else {
            $this->Flash->error(__('The translation could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
```

### Translation Usage in Templates

**CRITICAL:** All user-facing text must use `__()` function for translation.

```php
// Single string
<?= __('Welcome') ?>

// String with placeholder
<?= __('Hello, {0}', $username) ?>

// Multiple placeholders
<?= __('Order {0} for {1} items', $orderId, $itemCount) ?>

// Pluralization
<?= __n('1 item', '{0} items', $count, $count) ?>

// Domain-specific translation
<?= __d('admin', 'Dashboard') ?>
```

### Language Switcher UI

```php
<!-- In layout or menu -->
<div class="language-switcher">
    <?= $this->Html->link('English', ['controller' => 'Switchto', 'action' => 'eng']) ?>
    <?= $this->Html->link('Indonesia', ['controller' => 'Switchto', 'action' => 'ind']) ?>
    <?= $this->Html->link('日本語', ['controller' => 'Switchto', 'action' => 'jpn']) ?>
</div>
```

### Key Features

1. **Auto-Detection** - Browser language detection on first visit
2. **Session Persistence** - Language stored in session across requests
3. **Cookie Storage** - Long-term persistence (360 days)
4. **Database-Driven** - Translations managed via CRUD interface
5. **Dynamic PO Generation** - Auto-generates PO files from database
6. **Cache Invalidation** - Clears translation cache after updates
7. **Fallback System** - English → Indonesian → Browser default

### Translation Database Schema

```sql
CREATE TABLE `translations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL COMMENT 'msgid (original text)',
    `eng` text COMMENT 'English translation',
    `ind` text COMMENT 'Indonesian translation',
    `jpn` text COMMENT 'Japanese translation',
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Critical for AI to Remember

✅ **All text** - Wrap ALL user-facing strings in `__()` function  
✅ **PO regeneration** - Call `parent::writePO()` after translation CRUD  
✅ **Cache clearing** - Delete persistent cache files after PO update  
✅ **Browser detection** - Auto-detect on first visit, persist in session  
✅ **Cookie lifetime** - 360 days for language preference  
✅ **Supported locales** - eng, ind, jpn (English, Indonesian, Japanese)  
✅ **Default language** - Indonesian (ind) as fallback  
✅ **URL switching** - `/switchto/eng`, `/switchto/ind`, `/switchto/jpn`  

---

## 📝 Common Patterns Checklist

### Every Template Must Have:

- [x] Proper Bootstrap 4 styling
- [x] Purple gradient theme on headers
- [x] Responsive design (mobile-friendly)
- [x] Consistent button colors
- [x] Flash message support
- [x] **CRITICAL: Internationalization - ALL text wrapped in `__()` function**
- [x] **Multilingual support (eng/ind/jpn) with auto-detection**
- [x] Proper escaping (h() for output)
- [x] External static-assets URLs
- [x] Database-driven menu (auto-loaded via AppController)

### Index Page Must Have:

- [x] Export buttons (CSV/Excel/PDF)
- [x] Hover action buttons (STICKY LEFT position)
- [x] Horizontal drag scrolling (cursor: grab)
- [x] **AJAX server-side filtering** (searches entire database)
- [x] **Debounced filter inputs** (800ms delay for text/date, immediate for dropdowns)
- [x] **Filter operators** (LIKE, =, <, >, starts_with, ends_with, between)
- [x] **Cascade district filters** (Province → Kabupaten → Kecamatan → Kelurahan)
- [x] **Client-side cascade filtering** for district dropdowns (pre-loaded data)
- [x] **URL parameter persistence** (filter state in URL, bookmarkable)
- [x] Pagination
- [x] Foreign key links
- [x] Max 10 columns
- [x] Thumbnail column for image fields (auto-detect)
- [x] Actions column positioned LEFT (not right)

### View Page Must Have:

- [x] Tab structure (if hasMany exists)
- [x] Properly formatted fields
- [x] Image previews
- [x] File download links
- [x] Edit/Delete buttons
- [x] initializeModalTabs() function
- [x] Modal container for nested views
- [x] AJAX modal loading
- [x] Independent tab states per modal
- [x] Horizontal drag scrolling in hasMany tables
- [x] Sticky LEFT action buttons in hasMany tables
- [x] Thumbnail columns in hasMany tables (if image field exists)
- [x] **AJAX filter support for related tables (server-side)**
- [x] **Filter row in hasMany table headers**
- [x] **Filter operators: like, =, starts_with, ends_with**
- [x] **Table attributes: data-ajax-filter="related", data-related-model, data-foreign-key**
- [x] **Debounced filter inputs (800ms delay)**
- [x] **Independent filtering per related table**

### Add/Edit Pages Must Have:

- [x] Auto-detected field types
- [x] File upload support with LIVE PREVIEW
- [x] Form validation
- [x] Current file/image display (edit) - CHECK file_exists() FIRST
- [x] NEW image preview on upload (both add & edit)
- [x] Visual feedback (fade current image when new selected)
- [x] **CRITICAL: Form confirmation with DATA PREVIEW before submit**
- [x] **confirmFormSubmit() function showing all entered data in table**
- [x] SweetAlert2 for beautiful confirmation dialogs
- [x] Enhanced delete confirmation with preview
- [x] Bootstrap styling with icons
- [x] Loading indicator during form submission

### Controller Must Have:

- [x] ExportTrait
- [x] **AjaxFilterTrait** (for server-side filtering)
- [x] **filterRelated() method** (auto-included via trait)
- [x] **handleAjaxFilter() method** (auto-included via trait)
- [x] File upload handling
- [x] Proper contains
- [x] Flash messages
- [x] Layout override for PDF

### Model Must Have:

- [x] Correct connection
- [x] Cross-DB strategy => 'select'
- [x] Display field
- [x] Validation rules
- [x] All associations

---

## 🚀 Quick Start for AI

When asked to create/bake templates:

```
1. READ: DATABASE_ASSOCIATIONS_REFERENCE.md for the table
2. NOTE: Connection, associations (belongsTo, hasMany)
3. CREATE: Model with correct connection + strategy => 'select'
4. CREATE: Controller with ExportTrait + AjaxFilterTrait + file uploads
5. CREATE: Index with purple gradient + hover buttons + AJAX server-side filters
6. CREATE: View with tabs for each hasMany + modal support + AJAX related filters
7. CREATE: Add/Edit with auto-detected field types
8. VERIFY: All external static-assets URLs
9. ADD: initializeModalTabs() for nested modal views
10. ADD: AJAX filter JavaScript for related tables in view.ctp
11. ADD: Filter row in hasMany table headers
12. ADD: Table attributes (data-ajax-filter, data-related-model, etc.)
13. ADD: Cascade filtering for district dropdowns (forms: Step 3 in POST-BAKE WORKFLOW)
14. ADD: Cascade filtering for filter row (index: See "Cascade Filtering in Index Page Filters")
15. TEST: Purple gradient, responsive design, filters work
16. TEST: Related table filtering (AJAX, no page reload)
17. TEST: District cascade works in forms (Province → Kabupaten → Kecamatan → Kelurahan)
18. TEST: District cascade works in index filters (same 4-level cascade)
```

**End of AI Memory Guide**

---

**This document is designed for AI to quickly recall:**
- Design patterns (colors, fonts, layout)
- MVC template structures
- **CRITICAL: Smart field name pattern detection (20+ patterns)**
- **Auto-conversion rules: date→datepicker, email→validation, password→strength, etc.**
- **Multilingual i18n system (eng/ind/jpn) with database-driven translations**
- **Translation usage: ALL user text wrapped in `__()` function**
- **Auto PO file generation from database on translation CRUD**
- JavaScript integrations
- Database-driven menu system with smart submenu positioning
- Nested modal tabs with independent states
- **AJAX server-side filtering for index pages (page reload)**
- **AJAX server-side filtering for view page related tables (no page reload)**
- **filterRelated() method in all controllers via AjaxFilterTrait**
- Export features (CSV/Excel/PDF)
- File upload with watermarking
- Cross-database associations
- **Horizontal drag scrolling for wide tables**
- **Sticky LEFT action buttons (visible on hover)**
- **Auto-generated thumbnail columns for image fields**
- **Live image preview on upload (add & edit forms)**
- **File existence check before displaying current images**
- **CRITICAL: Form confirmation with data preview before submit**
- **SweetAlert2 confirmation dialogs with formatted data tables**
- **Enhanced delete confirmation showing what will be deleted**
- **Loading indicators during form submission**
- Common pitfalls to avoid
- Consistent styling across all templates

**Keep this reference handy for generating consistent, high-quality bake templates!**
