
# Custom Bake Templates & Database Mapping - Quick Reference


## 📢 DATABASE MAPPING & ASSOCIATION STRATEGY (Nov 15, 2025)

### Database Connection Mapping (from config/app_datasources.php)

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

- Set the correct connection in each Table class:
    ```php
    public static function defaultConnectionName()
    {
            return '<connection_name>';
    }
    ```

### Cross-Database Association Strategy

- If an association points to a table in a different connection/database, add:
    ```php
    'strategy' => 'select'
    ```
    Example:
    ```php
    $this->belongsTo('MasterPropinsis', [
            'foreignKey' => 'master_propinsi_id',
            'strategy' => 'select', // REQUIRED if MasterPropinsis is in a different connection
    ]);
    ```

- If both tables are in the same database, 'strategy' is optional but recommended for consistency.

### Bake Workflow

1. Bake model/controller/template with correct connection:
     ```powershell
     bin\cake bake all TableName --connection <connection> --force
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

### ⚠️ CRITICAL FIX: Empty Controller Files (0 bytes)

**Problem:** Controllers generated as empty (0 byte) files despite bake reporting success.

**Root Cause:** `config/bootstrap_bake.php` used `setTemplatePath()` which broke Twig rendering.

**Solution:**
1. **Fixed `config/bootstrap_bake.php`:**
   ```php
   // CORRECT: Use _paths variable
   $view->set('_paths', [
       ROOT . DS . 'src' . DS . 'Template' . DS . 'Bake' . DS,
       CAKE . 'Template' . DS . 'Bake' . DS
   ]);
   
   // WRONG: This breaks rendering!
   // $view->setTemplatePath($path);
   ```

2. **Fixed `controller.twig`:**
   - Removed `.DISABLED` extension
   - Adjusted whitespace for proper Twig rendering

**Verify After Baking:**
```powershell
Get-Item src\Controller\TableNameController.php | Select-Object Length
# Should be 5000-8000 bytes, NOT 0!
```

**See:** `CONTROLLER_TWIG_FIX_NOV2025.md` for complete troubleshooting guide.

---

## 📢 Previous Update (Nov 7, 2025)

### ✅ Fixed AJAX Filter Column Order Issue

**Problem:** Controller `filter()` method generated columns in WRONG order causing header/data misalignment.

**Root Cause:** Bake template rendered foreign keys inline when encountered, not in database schema position.

**Solution:** Updated `src/Template/Bake/Element/Controller/filterSearch.ctp` to:
- Build `$foreignKeys` lookup array BEFORE loop
- Render columns in exact DATABASE SCHEMA order
- Auto-format numeric fields with `number_format()`

**Result:** All future baked controllers will have correct column alignment! 🎉

**See:** `BAKE_TEMPLATE_COLUMN_ORDER.md` for full details

---

## 🎯 Jawaban untuk Pertanyaan User

### 1. Upload File/Image di Controller Bake Template ✅

**SEBELUM (Manual di setiap controller):**
```php
// Harus copy-paste code ini di setiap controller
public function add() {
    if ($this->request->is('post')) {
        $data = $this->request->getData();
        if (!empty($data['file_path'])) {
            $file = $data['file_path'];
            // 50+ lines upload code...
        }
    }
}
```

**SEKARANG (Auto-generated dari bake):**
```bash
bin\cake bake controller Inventories
```

File `src/Template/Bake/Element/Controller/add.twig` akan **AUTO-GENERATE** code upload:
- ✅ Auto-detect field dengan kata `file` atau `image`
- ✅ Auto-handle upload dengan nama unik (timestamp)
- ✅ Auto-organize ke folder: `img/uploads/` (image) atau `files/uploads/` (file)
- ✅ Error handling dengan Flash message
- ✅ Logging aktivitas upload

**Contoh Generated Code:**
```php
// Auto-generated oleh custom bake template
$uploadFields = ['image_url', 'file_path', 'photo'];

foreach ($uploadFields as $fieldName) {
    if (!empty($data[$fieldName]) && is_object($data[$fieldName])) {
        $file = $data[$fieldName];
        if ($file->getError() === UPLOAD_ERR_OK) {
            $uniqueFileName = $baseName . '_' . date('YmdHis') . '.' . $extension;
            
            // Auto-detect folder
            if (preg_match('/image/i', $fieldName)) {
                $uploadPath = WWW_ROOT . 'img/uploads/';
            } else {
                $uploadPath = WWW_ROOT . 'files/uploads/';
            }
            
            $file->moveTo($uploadPath . $uniqueFileName);
            $data[$fieldName] = 'img/uploads/' . $uniqueFileName;
        }
    }
}
```

### 2. Auto-Detect Multipart Form di View Template ✅

**SEBELUM (Manual check):**
```php
<?php
$word_fields = array('file', 'image');
$enctype = ',array()';
foreach ($fields as $_field) {
    foreach ($word_fields as $word){
        if (preg_match('/' . $word . '/i',$_field )) 
            $enctype = ",array('enctype'=>'multipart/form-data')";
    }
}
?>
```

**SEKARANG (Auto di bake template):**

File `src/Template/Bake/Template/form.ctp` sudah memiliki logic:

```php
<%
// AUTO-DETECT multipart form
$hasFileUpload = false;
foreach ($fields as $field) {
    if (preg_match('/(file|image)/i', $field)) {
        $hasFileUpload = true;
        break;
    }
}
%>

<!-- AUTO-SET enctype jika ada file field -->
<?= $this->Form->create($entity<% if ($hasFileUpload): %>, ['type' => 'file']<% endif; %>) ?>
```

**Result:**
```php
// Jika ada field: image_url, file_path, photo, attachment
<?= $this->Form->create($inventory, ['type' => 'file', 'data-confirm' => 'true']) ?>

// Jika TIDAK ada field file/image
<?= $this->Form->create($supplier, ['data-confirm' => 'true']) ?>
```

### 3. Smart Field Detection di View Template ✅ LEBIH BAIK!

**SEBELUM (Manual if-elseif banyak):**
```php
if (preg_match('/date/i', $field)) {
    echo "datePicker";
} elseif (preg_match('/file|image/i', $field)) {
    echo "file input";
} elseif (preg_match('/email/i', $field)) {
    echo "email validation";
}
// dst... banyak sekali
```

**SEKARANG (Sudah built-in di bake template):**

File `src/Template/Bake/Template/form.ctp` memiliki **11 tipe field detection**:

| Pattern | Auto-Generated Input |
|---------|---------------------|
| `*date*`, `*tanggal*` | Datepicker (Bootstrap) |
| `*file*`, `*attachment*` | File input (PDF/DOC/XLS) + download link |
| `*image*`, `*photo*`, `*gambar*` | File input (image/*) + thumbnail |
| `*email*` | Email input + real-time validation |
| `*katakana*` | Text input + kana.js (katakana mode) |
| `*hiragana*` | Text input + kana.js (hiragana mode) |
| `*propinsi*`, `*province*` | Address selector (cascading) |
| Foreign keys (`*_id`) | Dropdown dari associated table |
| Boolean (`TINYINT(1)`) | Checkbox (Bootstrap) |
| Text/Longtext | Textarea (4 rows) |
| Others | Text input (default) |

**Contoh Auto-Generated Code:**

```php
// Field: purchase_date → AUTO-DETECT sebagai date
<div class="col-md-6 mb-3">
    <label class="form-label"><?= __('Purchase Date') ?></label>
    <?= $this->Form->control('purchase_date', [
        'type' => 'text',
        'class' => 'form-control datepicker',
        'placeholder' => __('Select Purchase Date'),
        'autocomplete' => 'off'
    ]) ?>
</div>

// Field: image_url → AUTO-DETECT sebagai image
<div class="col-md-6 mb-3">
    <label class="form-label"><?= __('Image Url') ?></label>
    <?= $this->Form->control('image_url', [
        'type' => 'file',
        'class' => 'form-control',
        'accept' => 'image/*',
    ]) ?>
    <?php if (!empty($inventory->image_url)): ?>
        <div class="mt-2">
            <img src="<?= $this->Url->build('/' . $inventory->image_url) ?>" 
                 class="img-thumbnail" style="max-height: 150px;">
        </div>
    <?php endif; ?>
</div>

// Field: email → AUTO-DETECT sebagai email
<div class="col-md-6 mb-3">
    <label class="form-label"><?= __('Email') ?></label>
    <?= $this->Form->control('email', [
        'type' => 'email',
        'class' => 'form-control email-validate',
        'placeholder' => __('Enter Email'),
    ]) ?>
    <small class="text-muted">Format: user@example.com</small>
</div>
<script>
$(document).ready(function() {
    $('.email-validate').on('keyup blur', function() {
        var email = $(this).val();
        var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (email && !regex.test(email)) {
            $(this).css('border-color', 'red');
        } else {
            $(this).css('border-color', '#ced4da');
        }
    });
});
</script>
```

## 🚀 Cara Pakai

### Option 1: Bake Model/Controller/View Baru

```bash
# Bake semua sekaligus
bin\cake bake all Products

# Atau satu per satu
bin\cake bake model Products
bin\cake bake controller Products
bin\cake bake template Products
```

**Hasil:**
- ✅ Controller memiliki file upload logic (jika ada field file/image)
- ✅ Form auto-detect multipart jika perlu
- ✅ Semua field types ter-detect otomatis
- ✅ Bootstrap 5 styling
- ✅ Form confirmation terintegrasi

### Option 2: Re-bake Existing Controller

```bash
# Force overwrite existing controller
bin\cake bake controller Inventories -f

# Force overwrite existing templates
bin\cake bake template Inventories -f
```

**Warning:** Ini akan overwrite file yang ada. Backup dulu jika ada custom code!

### Option 3: Copy-Paste Manual (untuk existing controllers)

Ambil code dari generated controller, lalu paste ke controller yang sudah ada.

## 📁 File Locations

Custom bake templates sudah dibuat di:

```
src/Template/Bake/
├── Element/
│   └── Controller/
│       ├── add.twig      ← Controller add() dengan file upload
│       └── edit.twig     ← Controller edit() dengan file upload + delete old file
└── Template/
    └── form.ctp          ← Form view dengan smart field detection
```

## 🎨 Field Naming Best Practices

Untuk hasil maksimal, gunakan naming convention ini:

| Tujuan | Recommended Field Name |
|--------|------------------------|
| Upload PDF/DOC | `file_path`, `attachment`, `document_file` |
| Upload Image | `image_url`, `photo_path`, `gambar`, `foto` |
| Date Input | `purchase_date`, `delivery_date`, `tanggal_lahir` |
| Email | `email`, `user_email`, `contact_email` |
| Japanese Katakana | `name_katakana`, `katakana_name` |
| Japanese Hiragana | `name_hiragana`, `hiragana_name` |
| Province/Address | `propinsi_id`, `province_id` |
| Phone | `phone`, `telephone`, `telp`, `hp` (custom) |

## ✅ Test Custom Bake

### Test 1: Buat Test Table

```sql
CREATE TABLE test_uploads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    image_url VARCHAR(255),          -- Will trigger file input (image)
    file_path VARCHAR(255),          -- Will trigger file input (file)
    purchase_date DATE,              -- Will trigger datepicker
    email VARCHAR(100),              -- Will trigger email validation
    created DATETIME,
    modified DATETIME
);
```

### Test 2: Bake

```bash
bin\cake bake model TestUploads
bin\cake bake controller TestUploads
bin\cake bake template TestUploads
```

### Test 3: Verify

Check generated files:

```bash
# Check controller upload code
type src\Controller\TestUploadsController.php | findstr "uploadFields"

# Check form multipart
type src\Template\TestUploads\add.ctp | findstr "type.*file"

# Check datepicker
type src\Template\TestUploads\add.ctp | findstr "datepicker"
```

### Test 4: Browser Test

1. Buka: `http://asahi.local/test-uploads/add`
2. Upload image (JPG/PNG)
3. Upload file (PDF)
4. Pilih tanggal (datepicker)
5. Input email (lihat validasi)
6. Submit
7. Check: `webroot/img/uploads/` dan `webroot/files/uploads/`

## 🔧 Customization

### Tambah Pattern Baru

Edit `src/Template/Bake/Template/form.ctp`, tambahkan setelah line 80:

```php
// Tambah pattern untuk phone number
elseif (preg_match('/(phone|telephone|telp|hp)/i', $field)):
%>
    <div class="col-md-6 mb-3">
        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
        <?= $this->Form->control('<%= $field %>', [
            'type' => 'tel',
            'class' => 'form-control',
            'placeholder' => __('Enter <%= $humanize %>'),
            'pattern' => '[0-9]{10,13}'
        ]) ?>
    </div>
<%
```

### Ubah Upload Folder Structure

Edit `src/Template/Bake/Element/Controller/add.twig`, line ~40:

```twig
// Organize by year-month
$yearMonth = date('Y-m');
if (preg_match('/image/i', $fieldName)) {
    $uploadPath = WWW_ROOT . 'img' . DS . 'uploads' . DS . $yearMonth . DS;
    $webPath = 'img/uploads/' . $yearMonth . '/' . $uniqueFileName;
}
```

## 📊 Comparison

| Aspect | Before (Manual) | After (Custom Bake) |
|--------|----------------|---------------------|
| File Upload Code | 50+ lines per controller | Auto-generated |
| Form Multipart | Manual check | Auto-detect |
| Field Type Detection | Manual if-elseif | 11 patterns auto-detect |
| Consistency | Varies per developer | Standardized |
| Error Handling | Manual | Built-in |
| Time to Create | 30+ min per module | 2 min (bake command) |
| Maintenance | Update each file | Update template once |

## 🎯 Summary

**3 Pertanyaan User = 3 Fitur Terjawab:**

1. **Upload file/image di controller?** ✅ 
   - Custom `add.twig` & `edit.twig` dengan auto file upload logic

2. **Auto-detect multipart form?** ✅
   - Custom `form.ctp` dengan auto-detect file/image fields

3. **Smart field type detection?** ✅ LEBIH BAIK!
   - 11 field patterns (date, file, image, email, katakana, hiragana, address, foreign key, boolean, textarea, default)
   - Bootstrap 5 styling
   - Real-time validation
   - Image preview & file download links

**Cara Pakai:**
```bash
bin\cake bake all YourModel
```

**Dokumentasi Lengkap:**
- `CUSTOM_BAKE_TEMPLATES.md` - Full documentation
- `test_custom_bake.php` - Automated test script
- `database/test_smart_forms.sql` - Test table SQL

---
✅ **Ready to Use!** Tinggal bake model baru atau re-bake yang sudah ada.
