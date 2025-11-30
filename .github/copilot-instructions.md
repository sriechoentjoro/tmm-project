



# Copilot & AI Agent Chat Instructions - Multi-Database CMS Bake Guide

## 🚨 CRITICAL: Read This First - Form Field Standards

**BEFORE creating or editing ANY add.ctp or edit.ctp template, ALWAYS apply these rules:**

### Required Field Indicators (MANDATORY)
- ⚠️ **CHECK Table Model FIRST**: Only add asterisks to fields with `requirePresence()` + `notEmptyString/Date()` in Table model's `validationDefault()` method
- ✅ Red asterisk `*` ONLY for fields that have `requirePresence()` in validation rules
- ❌ NO asterisk for fields with `allowEmptyString()` or `allowEmptyFile()` - these are OPTIONAL
- ❌ `existsIn()` validates foreign keys but does NOT make field required - check for `requirePresence()` separately
- ✅ Max length hint in label: `(Max X characters)`
- ✅ Help text with icon below EVERY input field
- ✅ HTML validation attributes: `required`, `maxlength`, `type`

### Field Name Alignment (CRITICAL)
- ✅ Use EXACT database field names: `master_propinsi_id`, `master_kabupaten_id`, `master_kecamatan_id`, `master_kelurahan_id`
- ✅ Controller variables: `$masterPropinsis`, `$masterKabupatens` (NOT shortened)
- ❌ NEVER use: `$propinsis`, `$kabupatens`, `propinsi_id`, `kabupaten_id`
- ⚠️ **Geographic cascade fields are almost always OPTIONAL** (no asterisk) - these foreign keys typically have `allowEmptyString()` in validation

### Date Formatting (ALWAYS)
- ✅ View templates: `$entity->date_field ? $entity->date_field->format('Y-m-d') : '-'`
- ✅ Edit forms: `'value' => $entity->date_field ? $entity->date_field->format('Y-m-d') : ''`
- ✅ Datepicker: `'class' => 'form-control datepicker'`, `'autocomplete' => 'off'`
- ✅ Format help: `<small>(Format: YYYY-MM-DD)</small>` in label

### Standard Field Patterns
See "CRITICAL: Form Validation Indicators & User Guidance" section below for complete examples.

---

## Key Principles

- **Framework:** CakePHP 3.9 (PHP 5.6 only; no PHP 7+ syntax)
- **Multi-database:** Each table is mapped to a specific connection. Always use the correct connection from `config/app_datasources.php` and check `DATABASE_MAPPING_REFERENCE.md` for mapping.
- **Authorization System:** Comprehensive role-based authorization with menu filtering, permission checking, and institution-based data scoping. See `AUTHORIZATION_SYSTEM_COMPLETE.md` for full guide. Key features:
  - **Menu Filtering**: Menus automatically filtered by role permissions in `AppController::beforeRender()`
  - **Permission Checking**: Centralized permission system via `hasPermission($controller, $action)`
  - **Data Scoping**: Institution-based filtering for LPK users via `canAccessRecord($entity, $field)`
  - **Enhanced Notifications**: Clear unauthorized access messages via `handleUnauthorizedAccess()`
  - **Security Logging**: All unauthorized attempts logged to `logs/error.log`
- **Bake Templates:** Use custom templates in `src/Template/Bake/` for smart field detection (date, image, file, email, foreign key). Bake auto-generates file/image upload, export buttons, table filters, and mobile-optimized UI.
- **File Uploads:** Auto-generated for fields with `*image*`, `*file*`, etc. Images go to `webroot/img/uploads/`, files to `webroot/files/uploads/`. Thumbnails and watermarking handled by `vendor/ImageResize/`.
- **File Viewer:** Use `file_viewer` element with `showPreview => true` for view templates (inline 300px preview) or default for index templates (icon + modal). Auto-detects extension, shows color-coded icons. See `FILE_VIEWER_USAGE.md` for details.
- **Display Fields:** All tables must have meaningful `displayField` (title, name, fullname, or computed title field). For complex displays (e.g., "Apprenticeship Order: Organization (Year - Month)"), use title field pattern with beforeSave callback and database triggers. See `DISPLAY_FIELD_PATTERN.md` for implementation.
- **Association Display:** Index and view templates ALWAYS show association names/titles, NEVER IDs. Use `$entity->has('association') ? h($entity->association->title) : ''` pattern. Run `fix_all_association_displays_v2.ps1` after baking to automatically fix templates.
- **Cascade Dropdowns:** For Propinsi → Kabupaten → Kecamatan → Kelurahan, implement AJAX cascade filtering. Edit mode shows only stored values, Province change triggers cascade reset. See cascade implementation pattern below.
- **Export Features:** Use `ExportTrait.php` for CSV, Excel, PDF, and print. No manual code needed.
- **Table UI:** Index pages have export buttons, filter/search row, hover action buttons, and are mobile responsive. See `TEMPLATE_IMPROVEMENTS.css` for styling.
- **Delete Button Policy:** Delete buttons ONLY appear in view templates at the bottom. Index templates show ONLY View and Edit buttons in action column. No delete button in index row actions for data safety.
- **Menu System:** Database-driven, loaded in `AppController::beforeRender()`, rendered via `src/Template/Element/elegant_menu.ctp`, automatically filtered by user permissions.
- **Automation Scripts:** Use PowerShell scripts in project root for batch bake, cross-db fix, and config sync.
- **BOM Handling:** Always save PHP files as UTF-8 WITHOUT BOM. Use `fix_bom_all_php_files.ps1` after mass file updates. In PowerShell scripts, use `[System.IO.File]::WriteAllText()` with `UTF8Encoding($false)` instead of `Set-Content -Encoding UTF8`. See `BOM_FIX_SUMMARY.md` for details.
- **Testing:** Minimal PHPUnit tests, mostly stubs. See `tests/` folder.
- **Deployment:** Update static asset URLs in layout, copy assets to webroot, update database config in `config/app_datasources.php`.
- **AUTO-GENERATED FEATURES:** Bake templates now auto-generate the following features (no manual coding required after bake):
  - **Preview System:** All add/edit forms include a "Preview & Validate Before Save" button that validates data without writing to database, shows field metadata, displays association names (not IDs), and renders checkboxes for boolean fields.
  - **Visual Validation Indicators:** Required fields automatically show red asterisk (*), max length hints appear for varchar fields, date fields show format guidance (YYYY-MM-DD).
  - **Smart Field Detection:** Date fields use HTML5 date input with YYYY-MM-DD format, is_* tinyint fields render as checkboxes, email fields have validation, file/image fields have upload handlers.
  - **Hidden ID Field:** Edit forms automatically include hidden ID field for proper update validation.
  - **Preview Template:** Automatically generated for all tables showing validation errors, field metadata (type, nullable, length), and association names.
  - **Controller Preview Method:** Automatically generated with date array conversion, schema metadata extraction, and association loading.

## Bake Workflow (Always check mapping in DATABASE_MAPPING_REFERENCE.md)

1. **Bake Table:**
   ```powershell
   bin\cake bake all TableName --connection <connection> --force
   # After bake, run fix_all_cross_db_associations.ps1 if needed
   bin\cake cache clear_all
   ```
2. **Verify Controller & Associations:**
    - Controller file size > 5 KB (not empty)
    - Associations use `'strategy' => 'select'` for cross-db
    - Aliased associations use `'className' => 'ActualTable'`
    - **Post-bake verification:**
       - For every baked file (model, controller, index, view, add, edit):
          - Check all association definitions (`belongsTo`, `hasMany`, `hasOne`).
          - If the associated table is located in a different database connection (see `config/app_datasources.php` and `DATABASE_MAPPING_REFERENCE.md`), ensure `'strategy' => 'select'` is present in the association options.
          - If missing, add `'strategy' => 'select'` to prevent cross-database errors.
          - For aliased associations, also verify `'className'` is set correctly.
       - Recommended: Use or update automation scripts (e.g., `fix_all_cross_db_associations.ps1`) to scan and fix missing `'strategy' => 'select'` in all Table classes.
3. **Fix Association Displays (CRITICAL POST-BAKE STEP):**
   - After baking, templates will show IDs by default
   - **MUST run:** `powershell -ExecutionPolicy Bypass -File fix_all_association_displays_v2.ps1`
   - This script automatically updates:
     - Index templates: Convert ID columns to show association names/titles
     - View templates: Convert ID displays to association names/titles with proper labels
     - Header sorting: Update to sort by association fields
   - See "Association Display" section for patterns and field mappings
4. **File Upload:**
   - No manual code needed if using bake templates
   - For displaying files, use `file_viewer` element for icon + modal preview
5. **File Display:**
   - Use `<?= $this->element('file_viewer', ['filePath' => $entity->field]) ?>` for any file/document field
   - Automatic icon detection, color coding, and modal preview
   - See `FILE_VIEWER_USAGE.md` for full documentation
6. **Export:**
   - Use auto-generated export buttons in index pages
7. **Cascade Dropdowns (for geographic fields):**
   - Use pattern: Province (all) → Kabupaten (filtered) → Kecamatan (filtered) → Kelurahan (filtered)
   - Edit mode: Show only stored values initially, Province change resets cascade
   - View mode: Display all values as plain text or links
8. **Menu:**
   - Managed via database, rendered in `elegant_menu.ctp`

## Conventions

- **No PHP 7+ syntax:** Only PHP 5.6 features allowed
- **DashedRoute:** URLs use dashes, not underscores
- **Static Assets:** Served from webroot (`/css/`, `/js/`, `/webfonts/`)
- **Smart Form Patterns:** Field names auto-detect input type (see bake templates)
- **Mobile First:** All templates and CSS are mobile-optimized

## CRITICAL: Form Validation Indicators & User Guidance (ALWAYS APPLY)

**MANDATORY for ALL add.ctp and edit.ctp templates:**

### 1. Required Field Indicators
- **ALWAYS add red asterisk** `<span class="text-danger">*</span>` for required fields
- **ALWAYS add max length hint** in label: `<small class="text-muted">(Max X characters)</small>`
- **ALWAYS add field description** below input using `<small class="form-text text-muted">`
- **ALWAYS use icons** for visual cues: 📧 (email), 📞 (phone), 📅 (date), ℹ️ (info), ⚠️ (warning)

### 2. Field Name Alignment (CRITICAL)
- **Database field names:** MUST match exactly, including `master_` prefix
- **Geographic cascade fields:** Use `master_propinsi_id`, `master_kabupaten_id`, `master_kecamatan_id`, `master_kelurahan_id`
- **Controller variables:** Use `$masterPropinsis`, `$masterKabupatens`, etc. (NOT shortened versions)
- **Element IDs:** Use full names like `CandidateMasterPropinsiId` for JavaScript targeting

### 3. Label Format Pattern (ALWAYS USE THIS)
```php
<label class="form-label">
    <?= __('Field Name') ?> 
    <span class="text-danger">*</span>  <!-- For required fields -->
    <small class="text-muted">(Max X characters - description)</small>
</label>
```

### 4. Input Field Pattern with Validation Attributes
```php
<?= $this->Form->control('field_name', [
    'class' => 'form-control',
    'placeholder' => __('Clear example placeholder'),
    'label' => false,
    'required' => true,  // For required fields
    'maxlength' => 100,  // From database schema
    'type' => 'text'     // or 'email', 'tel', 'number', 'date'
]) ?>
```

### 5. Help Text Pattern (MANDATORY BELOW EACH INPUT)
```php
<small class="form-text text-muted">
    <i class="fas fa-info-circle"></i> Clear, helpful guidance about what to enter and format expected.
</small>
```

### 6. Field-Specific Patterns

**Identity Number (NIK/KTP):**
```php
<label class="form-label">
    <?= __('Identity Number') ?> 
    <span class="text-danger">*</span>
    <small class="text-muted">(Max 16 characters - NIK/KTP number)</small>
</label>
<?= $this->Form->control('identity_number', [
    'class' => 'form-control',
    'placeholder' => __('Enter 16-digit Identity Number (NIK/KTP)'),
    'label' => false,
    'required' => true,
    'maxlength' => 16
]) ?>
<small class="form-text text-muted">
    <i class="fas fa-info-circle"></i> Enter your national identity number (KTP). Must be 16 digits.
</small>
```

**Date Fields (YYYY-MM-DD Format):**
```php
<label class="form-label">
    <?= __('Birth Date') ?> 
    <span class="text-danger">*</span>
    <small class="text-muted">(Format: YYYY-MM-DD)</small>
</label>
<?= $this->Form->control('birth_date', [
    'type' => 'text',
    'class' => 'form-control datepicker',
    'placeholder' => __('Select Birth Date (YYYY-MM-DD)'),
    'label' => false,
    'required' => true,
    'autocomplete' => 'off'
]) ?>
<small class="form-text text-muted">
    <i class="fas fa-calendar-alt"></i> Click to select date from calendar. Format: Year-Month-Day (e.g., 1990-05-15)
</small>
```

**Email Fields:**
```php
<label class="form-label">
    <?= __('Email') ?>
    <small class="text-muted">(Optional - Valid email format)</small>
</label>
<?= $this->Form->control('email', [
    'type' => 'email',
    'class' => 'form-control',
    'placeholder' => __('user@example.com'),
    'label' => false
]) ?>
<small class="text-muted">
    <i class="fas fa-envelope"></i> Format: user@example.com. Valid email address required if provided.
</small>
```

**Phone/Mobile Fields:**
```php
<label class="form-label">
    <?= __('Telephone Mobile') ?>
    <small class="text-muted">(Optional - Max 12 digits)</small>
</label>
<?= $this->Form->control('telephone_mobile', [
    'class' => 'form-control',
    'placeholder' => __('Enter Mobile Number (e.g., 081234567890)'),
    'label' => false,
    'maxlength' => 12,
    'type' => 'tel'
]) ?>
<small class="form-text text-muted">
    <i class="fas fa-phone"></i> Enter your mobile phone number without spaces or dashes.
</small>
```

**Emergency Contact Fields:**
```php
<label class="form-label">
    <?= __('Telephone Emergency') ?> 
    <span class="text-danger">*</span>
    <small class="text-muted">(Max 256 characters)</small>
</label>
<?= $this->Form->control('telephone_emergency', [
    'class' => 'form-control',
    'placeholder' => __('Enter Emergency Contact Number'),
    'label' => false,
    'required' => true,
    'maxlength' => 256,
    'type' => 'tel'
]) ?>
<small class="form-text text-muted">
    <i class="fas fa-exclamation-triangle"></i> Emergency contact number (family member or close relative).
</small>
```

**Japanese Katakana Fields:**
```php
<label class="form-label">
    <?= __('Name Katakana') ?>
    <small class="text-muted">(Optional - Max 256 characters)</small>
</label>
<?= $this->Form->control('name_katakana', [
    'class' => 'form-control katakana-input',
    'placeholder' => __('カタカナで名前を入力してください'),
    'label' => false,
    'maxlength' => 256
]) ?>
<small class="form-text text-muted">
    <i class="fas fa-info-circle"></i> Enter your name in Katakana (カタカナ). Example: タナカ タロウ
</small>
```

**Textarea/Address Fields:**
```php
<label class="form-label">
    <?= __('Address') ?> 
    <span class="text-danger">*</span>
    <small class="text-muted">(Max 256 characters)</small>
</label>
<?= $this->Form->control('address', [
    'class' => 'form-control',
    'placeholder' => __('Enter Complete Address (Street, RT/RW, etc.)'),
    'label' => false,
    'required' => true,
    'maxlength' => 256,
    'rows' => 3
]) ?>
<small class="form-text text-muted">
    <i class="fas fa-home"></i> Enter your complete residential address including street name, RT/RW, and house number.
</small>
```

**Postal Code Fields:**
```php
<label class="form-label">
    <?= __('Post Code') ?>
    <small class="text-muted">(Optional - 5 digits)</small>
</label>
<?= $this->Form->control('post_code', [
    'class' => 'form-control',
    'placeholder' => __('Enter Postal Code (e.g., 12345)'),
    'label' => false,
    'type' => 'number',
    'maxlength' => 5
]) ?>
<small class="form-text text-muted">
    <i class="fas fa-map-marker-alt"></i> 5-digit Indonesian postal code.
</small>
```

### 7. Date Formatting Rules (CRITICAL)

**Display Dates in View Templates:**
```php
<!-- WRONG - Shows DateTime object -->
<?= h($entity->birth_date) ?>

<!-- CORRECT - Formatted as YYYY-MM-DD -->
<?= $entity->birth_date ? $entity->birth_date->format('Y-m-d') : '-' ?>

<!-- CORRECT - Indonesian format DD/MM/YYYY -->
<?= $entity->birth_date ? $entity->birth_date->format('d/m/Y') : '-' ?>

<!-- CORRECT - Full Indonesian format -->
<?= $entity->birth_date ? $entity->birth_date->i18nFormat('dd MMMM yyyy') : '-' ?>
```

**Date Input in Edit Forms:**
```php
<!-- CORRECT - Pre-fill with formatted date -->
<?= $this->Form->control('birth_date', [
    'type' => 'text',
    'class' => 'form-control datepicker',
    'value' => $entity->birth_date ? $entity->birth_date->format('Y-m-d') : '',
    'label' => false
]) ?>
```

**Controller Date Handling:**
```php
// Preview method - Convert HTML5 date arrays to YYYY-MM-DD string
if (isset($data['birth_date']) && is_array($data['birth_date'])) {
    $birthDate = $data['birth_date'];
    if (!empty($birthDate['year']) && !empty($birthDate['month']) && !empty($birthDate['day'])) {
        $data['birth_date'] = sprintf(
            '%04d-%02d-%02d',
            $birthDate['year'],
            $birthDate['month'],
            $birthDate['day']
        );
    }
}
```

### 8. Icon Reference (Use Font Awesome 5)

| Icon Class | Usage | Field Type |
|------------|-------|------------|
| `fa-info-circle` | General information | All fields |
| `fa-exclamation-triangle` | Warning/Important | Emergency contacts |
| `fa-envelope` | Email | Email fields |
| `fa-phone` | Phone | Mobile/phone fields |
| `fa-calendar-alt` | Calendar | Date fields |
| `fa-map-marker-alt` | Location | Address/postal code |
| `fa-home` | Home address | Address fields |
| `fa-user` | Person | Name/identity fields |
| `fa-id-card` | Identity | ID number fields |

### 9. Pre-Bake Checklist (BEFORE GENERATING TEMPLATES)

✅ Check database schema for:
- Field max lengths (VARCHAR)
- Required fields (NOT NULL)
- Field types (date, text, int, tinyint for boolean)
- Foreign keys (master_* prefix)

✅ Verify Table model for:
- `validationDefault()` rules (requirePresence, notEmptyString)
- Association names (MasterPropinsis, not Propinsis)
- Foreign key field names (master_propinsi_id)

✅ Plan user guidance:
- What examples to show (phone format, date format)
- What warnings to display (emergency contact importance)
- What hints help users (Katakana examples)

### 10. Post-Bake Review Checklist (AFTER GENERATING TEMPLATES)

✅ **Every required field has:**
- Red asterisk (*) in label
- `required="true"` attribute
- Help text explaining what to enter
- Max length indicator

✅ **Every optional field has:**
- "(Optional)" text in label
- Clear guidance on when/why to fill it
- Max length if applicable

✅ **All date fields have:**
- Format guidance (YYYY-MM-DD)
- Datepicker class
- Example in help text
- `autocomplete="off"` to prevent browser autofill

✅ **All foreign key dropdowns have:**
- Correct variable names ($masterPropinsis, not $propinsis)
- Correct field names (master_propinsi_id, not propinsi_id)
- Empty option text ("-- Select Province --")
- Association loaded in controller

✅ **All text inputs have:**
- Clear placeholder with example
- Max length attribute from database
- Appropriate input type (email, tel, text, number)
- Help text below input

## Database Mapping Reference
- See `DATABASE_MAPPING_REFERENCE.md` for up-to-date connection/table mapping and cross-database association rules.
- Always check this file before baking or updating associations.

## Integration Points
- **ImageResize:** Used for image upload/thumbnail/watermark
- **PhpSpreadsheet:** Used for Excel export
- **Mpdf:** Used for PDF export
- **FileViewer:** Element and Helper for file display with icons and modal preview (see `FILE_VIEWER_USAGE.md`)

## File Viewer Feature
- **Element:** `src/Template/Element/file_viewer.ctp` - Use for displaying any file/document field
- **Helper:** `src/View/Helper/FileViewerHelper.php` - Registered in AppView
- **Features:**
  - Auto-detect file extension (PDF, DOC, XLS, Images, ZIP, etc.)
  - Color-coded icons (PDF=Red, DOC=Blue, XLS=Green, Images=Purple)
  - Modal preview for PDF and images with iframe
  - Direct download for DOC, XLS, ZIP files
  - Mobile-responsive Bootstrap 4 modals
  - **NEW:** File existence check with warning message
  - **NEW:** Shows "File not found" or "Image not found" with blinking icon
  - **NEW:** Provides "Click to edit & upload" link when file is missing
- **Usage (with edit link):** 
  ```php
  <?php if (!empty($entity->reference_file)): ?>
      <?= $this->element('file_viewer', [
          'filePath' => $entity->reference_file,
          'editUrl' => $this->Url->build(['action' => 'edit', $entity->id])
      ]) ?>
  <?php else: ?>
      <span style="color: #999; font-style: italic;">
          No file - <?= $this->Html->link('Upload', ['action' => 'edit', $entity->id]) ?>
      </span>
  <?php endif; ?>
  ```
- **Usage (without edit link):** 
  ```php
  <?= $this->element('file_viewer', [
      'filePath' => $entity->reference_file
  ]) ?>
  ```
- **Auto-apply scripts:** 
  - `apply_file_viewer_to_all_templates.ps1` - Automatically update all existing templates
  - `apply_file_viewer_with_edit_link.ps1` - Apply with edit link to view templates
- **Documentation:** See `FILE_VIEWER_USAGE_WITH_EDIT_LINK.md` for complete guide

## Cascade Dropdown Feature (Geographic Fields)
- **Purpose:** Hierarchical filtering for Propinsi → Kabupaten → Kecamatan → Kelurahan
- **Tables:** `master_propinsis`, `master_kabupatens`, `master_kecamatans`, `master_kelurahans` (in cms_masters database)
- **Implementation Pattern:**

### Controller Pattern (Edit Method)
```php
// Show only stored values initially
if (!empty($entity->master_kabupaten_id)) {
    $masterKabupatens = $this->EntityTable->MasterKabupatens->find('list')
        ->where(['id' => $entity->master_kabupaten_id])->toArray();
} else {
    $masterKabupatens = [];
}

// Provide full cascade data for JavaScript
$masterKabupatensData = $this->EntityTable->MasterKabupatens->find('all')
    ->select(['id', 'title', 'propinsi_id'])->toArray();
$masterKecamatansData = $this->EntityTable->MasterKecamatans->find('all')
    ->select(['id', 'title', 'kabupaten_id'])->toArray();
$masterKelurahansData = $this->EntityTable->MasterKelurahans->find('all')
    ->select(['id', 'title', 'kecamatan_id'])->toArray();
```

### Template Pattern (Edit/Add Form)
```php
// Propinsi - Show all
<?= $this->Form->control('master_propinsi_id', [
    'options' => $masterPropinsis,
    'empty' => 'Select Province',
    'class' => 'form-control'
]) ?>

// Kabupaten - Show only stored or filtered
<?= $this->Form->control('master_kabupaten_id', [
    'options' => $masterKabupatens,
    'empty' => 'Select City/District',
    'class' => 'form-control'
]) ?>

// JavaScript for cascade filtering
<script>
var kabupatensData = <?= json_encode($masterKabupatensData) ?>;
var kecamatansData = <?= json_encode($masterKecamatansData) ?>;
var kelurahansData = <?= json_encode($masterKelurahansData) ?>;

$('#master-propinsi-id').on('change', function() {
    var propinsiId = $(this).val();
    var kabupatenSelect = $('#master-kabupaten-id');
    
    // Clear and reset cascading dropdowns
    kabupatenSelect.empty().append('<option value="">Select City/District</option>');
    $('#master-kecamatan-id').empty().append('<option value="">Select Subdistrict</option>');
    $('#master-kelurahan-id').empty().append('<option value="">Select Village</option>');
    
    // Populate Kabupaten based on Propinsi
    if (propinsiId) {
        kabupatensData.forEach(function(kab) {
            if (kab.propinsi_id == propinsiId) {
                kabupatenSelect.append('<option value="' + kab.id + '">' + kab.title + '</option>');
            }
        });
    }
});
</script>
```

### View Pattern (Display Mode)
```php
<tr>
    <th>Province</th>
    <td><?= h($entity->master_propinsi->title) ?></td>
</tr>
<tr>
    <th>City/District</th>
    <td><?= h($entity->master_kabupaten->title) ?></td>
</tr>
<tr>
    <th>Subdistrict</th>
    <td><?= h($entity->master_kecamatan->title) ?></td>
</tr>
<tr>
    <th>Village</th>
    <td><?= h($entity->master_kelurahan->title) ?></td>
</tr>
```

### Key Rules:
- **Edit mode:** Initially show only stored values for Kabupaten/Kecamatan/Kelurahan
- **Province change:** Reset all cascading dropdowns and repopulate
- **View mode:** Display all values with proper associations
- **Add mode:** Start with empty cascading dropdowns, populate on Province selection
- **JavaScript:** Use data arrays passed from controller for client-side filtering
- **Association names:** Use correct Table association names (MasterKabupatens, not Kabupatens)

## Validation System & Preview Page Pattern

### Overview
Complete validation implementation with database schema detection, preview page for error checking before save, and visual indicators in forms.

### 1. Model Validation (Table Class)

**Location:** `src/Model/Table/[Entity]Table.php`

**Pattern:**
```php
public function validationDefault(Validator $validator)
{
    // ID validation - different rules for create vs update
    $validator
        ->integer('id')
        ->allowEmptyString('id', null, 'create')  // Empty OK on create (auto-increment)
        ->notEmptyString('id', 'ID is required for update', 'update');  // Required on update

    // Text fields with max length
    $validator
        ->scalar('identity_number')
        ->maxLength('identity_number', 16)
        ->requirePresence('identity_number', 'create')
        ->notEmptyString('identity_number');

    // Date fields with strict format
    $validator
        ->date('birth_date')
        ->requirePresence('birth_date', 'create')
        ->notEmptyDate('birth_date')
        ->add('birth_date', 'validFormat', [
            'rule' => ['date', 'ymd'],  // YYYY-MM-DD only
            'message' => 'Birth date must be in YYYY-MM-DD format (e.g., 1990-05-15)'
        ]);

    // Optional fields (nullable in database)
    $validator
        ->integer('master_propinsi_id')
        ->allowEmptyString('master_propinsi_id');

    // Boolean/Checkbox fields
    $validator
        ->boolean('is_training_pass')
        ->allowEmptyString('is_training_pass');

    return $validator;
}
```

**Key Rules:**
- Match database nullable constraints exactly
- Use `requirePresence()` for required fields on create
- Use `notEmptyString()` or `notEmptyDate()` for non-null fields
- Add format validation for dates: `['date', 'ymd']` for YYYY-MM-DD
- Optional fields use `allowEmptyString()` or `allowEmptyFile()`

### 2. Controller Preview Method

**Location:** `src/Controller/[Entity]Controller.php`

**Pattern:**
```php
public function preview()
{
    if (!$this->request->is(['post', 'put', 'patch'])) {
        return $this->redirect(['action' => 'index']);
    }

    $data = $this->request->getData();
    
    // Process date fields - convert HTML5 date array to string
    if (isset($data['birth_date']) && is_array($data['birth_date'])) {
        $birthDate = $data['birth_date'];
        if (!empty($birthDate['year']) && !empty($birthDate['month']) && !empty($birthDate['day'])) {
            $data['birth_date'] = sprintf(
                '%04d-%02d-%02d',
                $birthDate['year'],
                $birthDate['month'],
                $birthDate['day']
            );
        }
    }
    
    // Determine mode: edit (has ID) vs add (no ID)
    $isEditMode = !empty($data['id']);
    
    // Create entity for validation (no save)
    if ($isEditMode) {
        $entity = $this->EntityTable->get($data['id'], ['contain' => []]);
        $entity = $this->EntityTable->patchEntity($entity, $data);
    } else {
        $entity = $this->EntityTable->newEntity($data);
    }

    // Get validation errors
    $validationErrors = $entity->getErrors();
    
    // Manual check: Edit mode requires ID in data
    if ($isEditMode && empty($data['id'])) {
        $validationErrors['id'] = ['ID is required for updating existing records'];
    }
    
    // Get database schema metadata
    $schema = $this->EntityTable->getSchema();
    $fieldMetadata = [];
    
    foreach ($schema->columns() as $column) {
        $columnData = $schema->getColumn($column);
        $fieldMetadata[$column] = [
            'type' => $schema->getColumnType($column),
            'nullable' => $schema->isNullable($column),
            'length' => isset($columnData['length']) ? $columnData['length'] : null,
            'default' => isset($columnData['default']) ? $columnData['default'] : null,
        ];
    }
    
    // Ensure ID in data for edit mode display
    if (!empty($entity->id) && empty($data['id'])) {
        $data['id'] = $entity->id;
    }
    
    // Load associations for display
    if (!empty($entity->id)) {
        $entity = $this->EntityTable->get($entity->id, [
            'contain' => [
                'AssociationName1',
                'AssociationName2',
                // ... all associations
            ]
        ]);
    }

    $this->set(compact('entity', 'validationErrors', 'fieldMetadata', 'data'));
}
```

**Key Points:**
- Convert HTML5 date arrays to YYYY-MM-DD strings
- Detect edit vs add mode based on ID presence
- Validate WITHOUT saving to database
- Extract database schema for field metadata
- Load associations for display
- Manual ID check for edit mode

### 3. Preview Template

**Location:** `src/Template/[Entity]/preview.ctp`

**Pattern:**
```php
<div class="validation-summary">
    <?php if (empty($validationErrors)): ?>
        <h4 style="color: #28a745;">✓ All Validation Passed!</h4>
        <p>Your data meets all requirements and is ready to be saved.</p>
    <?php else: ?>
        <h4 style="color: #dc3545;">⚠ Validation Errors Found</h4>
        <p><strong><?= count($validationErrors) ?> field(s)</strong> need attention</p>
    <?php endif; ?>
</div>

<h3>Data Preview with Validation Rules</h3>

<?php foreach ($fieldMetadata as $field => $meta): ?>
    <?php 
    $hasError = isset($validationErrors[$field]);
    $isRequired = !$meta['nullable'];
    $value = isset($data[$field]) ? $data[$field] : null;
    $displayValue = $value;
    
    // Handle foreign keys - show association name instead of ID
    $isForeignKey = substr($field, -3) === '_id';
    if ($isForeignKey && !empty($entity->id)) {
        $associationName = Inflector::camelize(str_replace('_id', '', $field));
        $associationProperty = Inflector::variable($associationName);
        $associationPropertyPlural = Inflector::variable(Inflector::pluralize($associationName));
        
        // Try singular then plural
        if ($entity->has($associationProperty)) {
            $assoc = $entity->get($associationProperty);
            $displayValue = isset($assoc->title) ? h($assoc->title) 
                         : (isset($assoc->name) ? h($assoc->name) 
                         : (isset($assoc->fullname) ? h($assoc->fullname) : h($value)));
        } elseif ($entity->has($associationPropertyPlural)) {
            $assoc = $entity->get($associationPropertyPlural);
            $displayValue = isset($assoc->title) ? h($assoc->title) 
                         : (isset($assoc->name) ? h($assoc->name) : h($value));
        }
    }
    // Handle boolean/checkbox fields
    elseif ($meta['type'] === 'boolean' || $meta['type'] === 'tinyint') {
        $displayValue = '<input type="checkbox" ' . ($value ? 'checked' : '') . ' disabled> ' 
                      . ($value ? 'Yes' : 'No');
    }
    // Handle date arrays
    elseif (is_array($value) && $meta['type'] === 'date') {
        $displayValue = sprintf('%04d-%02d-%02d', $value['year'], $value['month'], $value['day']);
    }
    // Empty values
    elseif ($value === null || $value === '') {
        $displayValue = '<span class="empty-value">(empty)</span>';
    }
    else {
        $displayValue = h($value);
    }
    ?>
    
    <div class="field-preview <?= $hasError ? 'has-error' : '' ?>">
        <div class="field-label">
            <?= Inflector::humanize($field) ?>
            <?php if ($isRequired): ?>
                <span class="badge badge-required">REQUIRED</span>
            <?php else: ?>
                <span class="badge badge-optional">Optional</span>
            <?php endif; ?>
        </div>
        
        <div class="field-value"><?= $displayValue ?></div>
        
        <div class="field-meta">
            <span class="badge badge-type"><?= strtoupper($meta['type']) ?></span>
            <?php if ($meta['length']): ?>
                <span>Max: <?= $meta['length'] ?> chars</span>
            <?php endif; ?>
            <span style="color: <?= $meta['nullable'] ? '#6c757d' : '#dc3545' ?>">
                Nullable: <?= $meta['nullable'] ? 'Yes' : 'No' ?>
            </span>
        </div>
        
        <?php if ($hasError): ?>
            <div class="validation-error">
                <?php foreach ($validationErrors[$field] as $error): ?>
                    <div class="error-message">❌ <?= h($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<div class="form-actions">
    <?php if (empty($validationErrors)): ?>
        <?= $this->Form->create($entity, ['url' => ['action' => 'edit', $entity->id]]) ?>
        <?= $this->Form->hidden('id') ?>
        <?= $this->Form->button(__('Confirm & Save'), ['class' => 'btn btn-success']) ?>
        <?= $this->Form->end() ?>
    <?php else: ?>
        <button class="btn btn-success" disabled>Cannot Save - Fix Errors First</button>
    <?php endif; ?>
    
    <?= $this->Html->link(__('Back to Edit'), 
        ['action' => 'edit', isset($entity->id) ? $entity->id : null], 
        ['class' => 'btn btn-secondary']) ?>
</div>
```

### 4. Form Template Patterns

**Edit Form ID Field:**
```php
<?= $this->Form->create($entity, ['type' => 'file', 'id' => 'entityForm']) ?>
<?php if (!empty($entity->id)): ?>
    <?= $this->Form->hidden('id') ?>
<?php endif; ?>
```

**Visual Indicators for Required Fields:**
```php
<label class="form-label">
    <?= __('Field Name') ?> 
    <span class="text-danger">*</span>
    <small class="text-muted">(Max 100 characters)</small>
</label>
<?= $this->Form->control('field_name', [
    'class' => 'form-control',
    'label' => false,
    'required' => true,
    'maxlength' => 100
]) ?>
```

**Date Fields with YYYY-MM-DD Format:**
```php
<label class="form-label">
    <?= __('Birth Date') ?> 
    <span class="text-danger">*</span>
    <small class="text-muted">(Format: YYYY-MM-DD, e.g., 1990-05-15)</small>
</label>
<?= $this->Form->control('birth_date', [
    'type' => 'date',
    'class' => 'form-control',
    'label' => false,
    'required' => true,
    'value' => $entity->birth_date ? $entity->birth_date->format('Y-m-d') : ''
]) ?>
```

**Boolean/Checkbox Fields (is_* fields):**
```php
<div class="form-check">
    <?= $this->Form->control('is_training_pass', [
        'type' => 'checkbox',
        'class' => 'form-check-input',
        'label' => [
            'text' => __('Is Training Pass'),
            'class' => 'form-check-label'
        ]
    ]) ?>
    <small class="text-muted d-block">Check if candidate has passed training</small>
</div>
```

**Association Dropdowns (Show title/name, not ID):**
```php
<label class="form-label">
    <?= __('Vocational Training Institution') ?> 
    <span class="text-danger">*</span>
</label>
<?= $this->Form->control('vocational_training_institution_id', [
    'options' => $vocationalTrainingInstitutions,  // Already uses title/name via find('list')
    'class' => 'form-control',
    'label' => false,
    'empty' => __('-- Select Vocational Training Institution --'),
    'required' => true
]) ?>
```

**Preview Button:**
```php
<div class="form-actions mt-4">
    <button type="button" class="btn btn-info" onclick="previewBeforeSave()">
        <i class="fas fa-eye"></i> <?= __('Preview & Validate Before Save') ?>
    </button>
    <?= $this->Form->button(__('Save'), ['class' => 'btn btn-primary']) ?>
    <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
</div>

<script>
function previewBeforeSave() {
    var form = document.getElementById('entityForm');
    form.action = '<?= $this->Url->build(['action' => 'preview']) ?>';
    form.submit();
}
</script>
```

### 5. Validation Workflow Summary

**Complete Flow:**
1. **Edit Form** → User fills in data with visual indicators (*, max length hints)
2. **Click Preview** → JavaScript changes form action to `/preview`
3. **Controller Preview Method** → 
   - Converts date arrays to strings
   - Detects edit vs add mode
   - Validates without saving
   - Extracts schema metadata
   - Loads associations
4. **Preview Template** → 
   - Shows validation summary (pass/fail)
   - Displays all fields with metadata
   - Shows association names (not IDs)
   - Renders checkboxes for boolean fields
   - Highlights errors in red
   - Enables/disables "Confirm & Save" button
5. **User Actions** →
   - If errors: Click "Back to Edit" and fix
   - If passed: Click "Confirm & Save" to write to database

**Key Benefits:**
- ✅ Validates BEFORE database save
- ✅ Shows exact database constraints
- ✅ Displays readable association names
- ✅ Visual error highlighting
- ✅ Prevents invalid data entry
- ✅ User-friendly guidance

## Automation & Scripts
- Use PowerShell scripts for batch bake, cross-db fix, and config sync.
- Key scripts: 
  - `bake_all_cms_databases.ps1` - Batch bake all tables
  - `fix_all_cross_db_associations.ps1` - Fix cross-database associations
  - `fix_all_association_displays_v2.ps1` - **CRITICAL: Fix all index/view templates to show association names instead of IDs**
  - `post_bake_fix.ps1` - Post-bake cleanup
  - `apply_file_viewer_to_all_templates.ps1` - Apply file viewer to all file/document fields
  - `fix_bom_all_php_files.ps1` - **CRITICAL: Remove BOM from all PHP files (prevents namespace errors)**
  
- **Recommended Post-Bake Workflow:**
  1. Bake table: `bin\cake bake all TableName --connection <connection> --force`
  2. Fix cross-db associations: `powershell -ExecutionPolicy Bypass -File fix_all_cross_db_associations.ps1`
  3. **Fix association displays: `powershell -ExecutionPolicy Bypass -File fix_all_association_displays_v2.ps1`**
  4. **Fix BOM issues: `powershell -ExecutionPolicy Bypass -File fix_bom_all_php_files.ps1`**
  5. Clear cache: `bin\cake cache clear_all`
  6. Test in browser: Verify index and view pages show names/titles not IDs

## Troubleshooting
- If controller files are empty, check bake template paths and clear cache.
- For cross-database associations, always add `'strategy' => 'select'`.
- For alias associations, add `'className' => 'ActualTable'`.
- For file display issues, ensure file path is relative to webroot and file exists.
- For modal preview not working, check Bootstrap 4+ and jQuery are loaded.
- For cascade dropdowns not working, check:
  - JavaScript data arrays are properly encoded with `json_encode()`
  - Element IDs match the form field IDs
  - Association names use correct Table class names (e.g., MasterKabupatens, not Kabupatens)
  - Data includes necessary fields (id, title, parent_id)
- **BOM Issues:** If you get "Namespace declaration must be first statement" error:
  - Cause: UTF-8 BOM (Byte Order Mark) in PHP files
  - Solution: Run `fix_bom_all_php_files.ps1` to scan and remove BOM from all PHP files
  - Prevention: In PowerShell scripts, always use `[System.IO.File]::WriteAllText($path, $content, (New-Object System.Text.UTF8Encoding $false))` instead of `Set-Content -Encoding UTF8`
  - After fixing: Clear cache with `bin\cake cache clear_all`
  - See `BOM_FIX_SUMMARY.md` for complete documentation
- See `DATABASE_MAPPING_REFERENCE.md` for error handling and update steps.
- See `FILE_VIEWER_USAGE.md` for file viewer troubleshooting.

## Best Practices for View Templates

### File/Document Fields
- **Index Templates:** Use `file_viewer` element with default settings (icon + modal)
  ```php
  <?= $this->element('file_viewer', [
      'filePath' => $entity->reference_file
  ]) ?>
  ```
- **View Templates:** Use `file_viewer` with `showPreview => true` (inline 300px preview)
  ```php
  <?= $this->element('file_viewer', [
      'filePath' => $entity->reference_file,
      'showPreview' => true,
      'editUrl' => $this->Url->build(['action' => 'edit', $entity->id])
  ]) ?>
  ```
- **Missing Files:** Auto-shows compact warning with upload link

### Association Display (CRITICAL - ALWAYS FOLLOW THIS RULE)
- **NEVER show IDs in templates:** Always display association title/name, NEVER foreign key IDs
- **Apply to ALL templates:** Index pages, View pages, and any data display
- **Pattern for Index Templates:**
  ```php
  <!-- WRONG - Shows ID number -->
  <td><?= $this->Number->format($entity->cooperative_association_id) ?></td>
  
  <!-- CORRECT - Shows association name -->
  <td><?= $entity->has('cooperative_association') ? 
          h($entity->cooperative_association->name) : '' ?></td>
  ```
- **Pattern for View Templates:**
  ```php
  <!-- WRONG - Shows ID and wrong label -->
  <tr>
      <th>Cooperative Association Id</th>
      <td><?= $this->Number->format($entity->cooperative_association_id) ?></td>
  </tr>
  
  <!-- CORRECT - Shows name with proper label -->
  <tr>
      <th>Cooperative Association</th>
      <td><?= $entity->has('cooperative_association') ? 
              h($entity->cooperative_association->name) : '' ?></td>
  </tr>
  ```
- **Header Sorting (Index Templates):**
  ```php
  <!-- WRONG -->
  <?= $this->Paginator->sort('cooperative_association_id') ?>
  
  <!-- CORRECT - Sortable by association field -->
  <?= $this->Paginator->sort('CooperativeAssociations.name', 'Cooperative Association') ?>
  ```
- **Standard Field Mapping (MUST USE THESE):**
  - CooperativeAssociations → `name`
  - AcceptanceOrganizations → `title` or `name`
  - MasterJobCategories → `title`
  - Candidates → `fullname`
  - Trainees → `fullname`
  - Users → `fullname`
  - VocationalTrainingInstitutions → `name`
  - Master tables (Propinsi, Kabupaten, Kecamatan, Kelurahan) → `title`
  - All audit fields (created_by, modified_by) → use Users association → `fullname`

- **Controller Requirements:**
  ```php
  // MUST include associations in paginate contain
  $this->paginate = [
      'contain' => ['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories'],
  ];
  
  // For view action, load all related associations
  $entity = $this->EntityTable->get($id, [
      'contain' => ['CooperativeAssociations', 'AcceptanceOrganizations', ...]
  ]);
  ```

- **Automation Script:**
  - Use `fix_all_association_displays.ps1` or `fix_all_association_displays_v2.ps1` to automatically fix all templates
  - Script converts ID displays to association name/title displays
  - Run after baking new tables: `powershell -ExecutionPolicy Bypass -File fix_all_association_displays_v2.ps1`

### Form Add/Edit Association Handling (CRITICAL - PERFORMANCE RULE)
- **DO NOT load all association records in forms:** Loading thousands of records causes severe performance issues and memory overload
- **Apply to:** Add and Edit forms for ALL tables with associations
- **Problem:** Default bake generates `$this->Form->control('association_id', ['options' => $associations])` which loads ALL records
- **Solution:** Use AJAX search/autocomplete for large associations, or contextual filtering

#### WRONG ❌ - Loading All Records
```php
// Controller (BAD - loads 10,000+ records)
public function add()
{
    $entity = $this->EntityTable->newEntity();
    
    // DON'T DO THIS - loads entire table into memory
    $cooperativeAssociations = $this->EntityTable->CooperativeAssociations->find('list')->toArray();
    $acceptanceOrganizations = $this->EntityTable->AcceptanceOrganizations->find('list')->toArray();
    
    $this->set(compact('entity', 'cooperativeAssociations', 'acceptanceOrganizations'));
}
```

#### CORRECT ✅ - Contextual or AJAX Loading
```php
// Option 1: Empty dropdown with AJAX search (RECOMMENDED for large datasets)
public function add()
{
    $entity = $this->EntityTable->newEntity();
    
    // Don't load any data - use AJAX search
    $cooperativeAssociations = [];
    $acceptanceOrganizations = [];
    
    $this->set(compact('entity', 'cooperativeAssociations', 'acceptanceOrganizations'));
}

// Option 2: Load only relevant records (for moderate datasets)
public function add()
{
    $entity = $this->EntityTable->newEntity();
    
    // Only active records, limited to reasonable number
    $cooperativeAssociations = $this->EntityTable->CooperativeAssociations
        ->find('list')
        ->where(['is_active' => 1])
        ->order(['name' => 'ASC'])
        ->limit(100)
        ->toArray();
    
    $this->set(compact('entity', 'cooperativeAssociations'));
}

// Option 3: Edit mode - show only stored value
public function edit($id = null)
{
    $entity = $this->EntityTable->get($id, [
        'contain' => ['CooperativeAssociations']
    ]);
    
    // Only show current selection, not all options
    if (!empty($entity->cooperative_association_id)) {
        $cooperativeAssociations = $this->EntityTable->CooperativeAssociations
            ->find('list')
            ->where(['id' => $entity->cooperative_association_id])
            ->toArray();
    } else {
        $cooperativeAssociations = [];
    }
    
    $this->set(compact('entity', 'cooperativeAssociations'));
}
```

#### Template Pattern - AJAX Search
```php
<!-- Replace default dropdown with AJAX search -->
<div class="form-group">
    <label for="cooperative-association-id">Cooperative Association</label>
    <select name="cooperative_association_id" 
            id="cooperative-association-id" 
            class="form-control select2-ajax"
            data-ajax-url="<?= $this->Url->build(['controller' => 'CooperativeAssociations', 'action' => 'search']) ?>">
        <?php if (!empty($entity->cooperative_association)): ?>
            <option value="<?= $entity->cooperative_association_id ?>" selected>
                <?= h($entity->cooperative_association->name) ?>
            </option>
        <?php endif; ?>
    </select>
</div>

<script>
// Initialize Select2 with AJAX search
$('.select2-ajax').select2({
    ajax: {
        url: function() { return $(this).data('ajax-url'); },
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return { q: params.term, page: params.page || 1 };
        },
        processResults: function(data) {
            return {
                results: data.results,
                pagination: { more: data.pagination.more }
            };
        }
    },
    minimumInputLength: 2,
    placeholder: 'Type to search...'
});
</script>
```

#### When to Use Each Approach

| Association Size | Approach | Example |
|-----------------|----------|---------|
| < 100 records | Load all with `find('list')` | MasterJobCategories, small lookup tables |
| 100-1000 records | Load with filters (active only, limit) | VocationalTrainingInstitutions |
| > 1000 records | AJAX search only | Candidates, Trainees, Organizations |
| Hierarchical (geographic) | Cascade dropdowns | Province → City → District → Village |

#### Performance Rules
1. **Never load > 1000 records** in form dropdowns
2. **Use AJAX search** for Candidates, Trainees, Organizations, Users
3. **Use cascade filtering** for geographic fields (Province → City)
4. **Edit mode:** Only show stored value, not all options
5. **Add mode:** Start with empty dropdown + AJAX search
6. **Test with production data:** Forms must work with 10,000+ records in database

### Display Field in Headers
- **Use title field for page titles:**
  ```php
  <h1>
      <?= __('Apprentice Order') ?>: <?= h($apprenticeOrder->title) ?>
  </h1>
  ```
- **Output Example:** "Apprentice Order: Apprenticeship Order: PT ASAHI FAMILY (2026 - February)"
- **Implementation:** Use title field with beforeSave callback (PHP) and database triggers (MySQL) for auto-generation

## Complete Association Display Examples

### Index Template Example
```php
<!-- Table Header -->
<thead>
    <tr>
        <th><?= $this->Paginator->sort('id') ?></th>
        <!-- CORRECT: Sort by association field -->
        <th><?= $this->Paginator->sort('CooperativeAssociations.name', 'Cooperative Association') ?></th>
        <th><?= $this->Paginator->sort('AcceptanceOrganizations.title', 'Acceptance Organization') ?></th>
        <th><?= $this->Paginator->sort('MasterJobCategories.title', 'Job Category') ?></th>
    </tr>
</thead>

<tbody>
    <?php foreach ($entities as $entity): ?>
    <tr>
        <td><?= $this->Number->format($entity->id) ?></td>
        <!-- CORRECT: Display association name/title -->
        <td><?= $entity->has('cooperative_association') ? 
                h($entity->cooperative_association->name) : '' ?></td>
        <td><?= $entity->has('acceptance_organization') ? 
                h($entity->acceptance_organization->title) : '' ?></td>
        <td><?= $entity->has('master_job_category') ? 
                h($entity->master_job_category->title) : '' ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>
```

### View Template Example
```php
<table>
    <!-- CORRECT: Proper label and association display -->
    <tr>
        <th>Cooperative Association</th>
        <td><?= $entity->has('cooperative_association') ? 
                h($entity->cooperative_association->name) : '' ?></td>
    </tr>
    <tr>
        <th>Acceptance Organization</th>
        <td><?= $entity->has('acceptance_organization') ? 
                h($entity->acceptance_organization->title) : '' ?></td>
    </tr>
    <tr>
        <th>Job Category</th>
        <td><?= $entity->has('master_job_category') ? 
                h($entity->master_job_category->title) : '' ?></td>
    </tr>
    <!-- Geographic fields -->
    <tr>
        <th>Province</th>
        <td><?= $entity->has('master_propinsi') ? 
                h($entity->master_propinsi->title) : '' ?></td>
    </tr>
    <tr>
        <th>City/District</th>
        <td><?= $entity->has('master_kabupaten') ? 
                h($entity->master_kabupaten->title) : '' ?></td>
    </tr>
    <!-- Audit fields -->
    <tr>
        <th>Created By</th>
        <td><?= $entity->has('creator') ? 
                h($entity->creator->fullname) : '' ?></td>
    </tr>
    <tr>
        <th>Modified By</th>
        <td><?= $entity->has('modifier') ? 
                h($entity->modifier->fullname) : '' ?></td>
    </tr>
</table>
```

### Controller Example
```php
// Index action
public function index()
{
    // MUST contain all associations displayed in index template
    $this->paginate = [
        'contain' => [
            'CooperativeAssociations',
            'AcceptanceOrganizations', 
            'MasterJobCategories',
            'MasterPropinsis',
            'MasterKabupatens',
            'Creator' => ['fields' => ['id', 'fullname']],
            'Modifier' => ['fields' => ['id', 'fullname']]
        ],
    ];
    $entities = $this->paginate($this->EntityTable);
    $this->set(compact('entities'));
}

// View action
public function view($id = null)
{
    // Load with ALL associations needed for view template
    $entity = $this->EntityTable->get($id, [
        'contain' => [
            'CooperativeAssociations',
            'AcceptanceOrganizations',
            'MasterJobCategories',
            'MasterPropinsis',
            'MasterKabupatens',
            'MasterKecamatans',
            'MasterKelurahans',
            'VocationalTrainingInstitutions',
            'Creator',
            'Modifier'
        ]
    ]);
    $this->set('entity', $entity);
}
```

## Best Practices for File/Document Fields (Deprecated - See Above)
- **Display:** Always use `file_viewer` element instead of plain text
- **Pattern:**
  ```php
  <?php if (!empty($entity->file_field)): ?>
      <?= $this->element('file_viewer', [
          'filePath' => $entity->file_field,
          'label' => basename($entity->file_field)
      ]) ?>
  <?php else: ?>
      <span style="color: #999; font-style: italic;">No file</span>
  <?php endif; ?>
  ```
- **Supported extensions:** PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, ZIP, RAR, TXT
- **Modal preview:** Auto-enabled for PDF and images
- **Direct download:** Auto-enabled for DOC, XLS, ZIP files

## Best Practices for Cascade Dropdowns (Geographic Fields)
- **Always load full data arrays** in controller for JavaScript filtering
- **Initial state in Edit mode:** Show only stored values, not full list
- **Province change:** Must reset all dependent dropdowns (Kabupaten, Kecamatan, Kelurahan)
- **Use correct association names:** MasterKabupatens, MasterKecamatans, MasterKelurahans (not shortened versions)
- **Data structure:** Always include parent_id fields (propinsi_id, kabupaten_id, kecamatan_id)
- **JavaScript encoding:** Use `<?= json_encode($data) ?>` for passing data to JavaScript
- **Element IDs:** Match form field IDs exactly (e.g., `master-propinsi-id`)
- **Empty options:** Always provide empty option text (e.g., "Select Province")
- **View mode:** Display all values as plain text using associations
- **Add mode:** Start with empty cascading dropdowns, populate on selection

## General Best Practices
- Always reference `DATABASE_MAPPING_REFERENCE.md` for connection/table mapping and bake workflow.
- Follow custom bake template patterns for forms, uploads, exports, and server-side filtering.
- Use only PHP 5.6 syntax (no PHP 7+ features).
- For server-side filtering, use the pattern in `SERVER_SIDE_FILTER_IMPLEMENTATION.md` and ensure all filters apply to the full dataset, not just visible rows.
- For cascade district filters, use correct table/association names as mapped in `config/app_datasources.php` and `DATABASE_MAPPING_REFERENCE.md`.
- **CRITICAL - Form Performance:** DO NOT load all records from association tables in add/edit forms. Use AJAX search for large datasets (>1000 records), contextual filtering for medium datasets (100-1000), or load only stored values in edit mode.
- If a workflow or mapping is unclear, ask for clarification or request the latest mapping.
  
**Post-bake verification for associations:**
- After baking, always verify every Table class and controller for lost or missing `'strategy' => 'select'` in associations where the related table is in a different database connection.
- Use automation scripts or manual review to ensure all cross-database associations are correct.
- Refer to `DATABASE_MAPPING_REFERENCE.md` for mapping and troubleshooting.

**Post-bake association display fix (CRITICAL):**
- After baking any new table, ALWAYS run: `powershell -ExecutionPolicy Bypass -File fix_all_association_displays_v2.ps1`
- This script automatically converts all ID displays to association name/title displays in index and view templates
- Verify in browser that index and view pages show readable names/titles, not numeric IDs
- If manual fixes needed, follow patterns in "Complete Association Display Examples" section above
