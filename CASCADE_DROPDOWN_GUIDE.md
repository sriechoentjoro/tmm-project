# Cascade Dropdown Implementation Guide
## Propinsi → Kabupaten → Kecamatan → Kelurahan

## Overview
Cascade dropdown adalah fitur untuk hierarchical filtering pada field geografis Indonesia:
- **Propinsi** (Province)
- **Kabupaten/Kota** (City/District)
- **Kecamatan** (Subdistrict)
- **Kelurahan/Desa** (Village)

## Database Structure

### Tables (in cms_masters database)
- `master_propinsis` - Province data
- `master_kabupatens` - City/District data (has `propinsi_id`)
- `master_kecamatans` - Subdistrict data (has `kabupaten_id`)
- `master_kelurahans` - Village data (has `kecamatan_id`)

### Required Associations in Table Class
```php
$this->belongsTo('MasterPropinsis', [
    'foreignKey' => 'master_propinsi_id',
    'strategy' => 'select',
    'joinType' => 'LEFT',
]);
$this->belongsTo('MasterKabupatens', [
    'foreignKey' => 'master_kabupaten_id',
    'strategy' => 'select',
    'joinType' => 'LEFT',
]);
$this->belongsTo('MasterKecamatans', [
    'foreignKey' => 'master_kecamatan_id',
    'strategy' => 'select',
    'joinType' => 'LEFT',
]);
$this->belongsTo('MasterKelurahans', [
    'foreignKey' => 'master_kelurahan_id',
    'strategy' => 'select',
    'joinType' => 'LEFT',
]);
```

## Implementation

### 1. Controller - edit() Method

```php
public function edit($id = null)
{
    $entity = $this->EntityTable->get($id, [
        'contain' => []
    ]);
    
    if ($this->request->is(['patch', 'post', 'put'])) {
        // ... save logic ...
    }
    
    // Load Province (all options)
    $masterPropinsis = $this->EntityTable->MasterPropinsis->find('list')->toArray();
    
    // Load only stored values for cascading dropdowns
    if (!empty($entity->master_kabupaten_id)) {
        $masterKabupatens = $this->EntityTable->MasterKabupatens->find('list')
            ->where(['id' => $entity->master_kabupaten_id])->toArray();
    } else {
        $masterKabupatens = [];
    }
    
    if (!empty($entity->master_kecamatan_id)) {
        $masterKecamatans = $this->EntityTable->MasterKecamatans->find('list')
            ->where(['id' => $entity->master_kecamatan_id])->toArray();
    } else {
        $masterKecamatans = [];
    }
    
    if (!empty($entity->master_kelurahan_id)) {
        $masterKelurahans = $this->EntityTable->MasterKelurahans->find('list')
            ->where(['id' => $entity->master_kelurahan_id])->toArray();
    } else {
        $masterKelurahans = [];
    }
    
    // Load full data arrays for JavaScript cascade
    $masterKabupatensData = $this->EntityTable->MasterKabupatens->find('all')
        ->select(['id', 'title', 'propinsi_id'])->toArray();
    $masterKecamatansData = $this->EntityTable->MasterKecamatans->find('all')
        ->select(['id', 'title', 'kabupaten_id'])->toArray();
    $masterKelurahansData = $this->EntityTable->MasterKelurahans->find('all')
        ->select(['id', 'title', 'kecamatan_id'])->toArray();
    
    $this->set(compact(
        'entity',
        'masterPropinsis',
        'masterKabupatens',
        'masterKecamatans',
        'masterKelurahans',
        'masterKabupatensData',
        'masterKecamatansData',
        'masterKelurahansData'
    ));
}
```

### 2. Controller - add() Method

```php
public function add()
{
    $entity = $this->EntityTable->newEntity();
    
    if ($this->request->is('post')) {
        // ... save logic ...
    }
    
    // Load Province (all options)
    $masterPropinsis = $this->EntityTable->MasterPropinsis->find('list')->toArray();
    
    // Empty cascading dropdowns for add mode
    $masterKabupatens = [];
    $masterKecamatans = [];
    $masterKelurahans = [];
    
    // Load full data arrays for JavaScript cascade
    $masterKabupatensData = $this->EntityTable->MasterKabupatens->find('all')
        ->select(['id', 'title', 'propinsi_id'])->toArray();
    $masterKecamatansData = $this->EntityTable->MasterKecamatans->find('all')
        ->select(['id', 'title', 'kabupaten_id'])->toArray();
    $masterKelurahansData = $this->EntityTable->MasterKelurahans->find('all')
        ->select(['id', 'title', 'kecamatan_id'])->toArray();
    
    $this->set(compact(
        'entity',
        'masterPropinsis',
        'masterKabupatens',
        'masterKecamatans',
        'masterKelurahans',
        'masterKabupatensData',
        'masterKecamatansData',
        'masterKelurahansData'
    ));
}
```

### 3. Template - edit.ctp / add.ctp

```php
<!-- Province Dropdown (Show All) -->
<div class="col-12 mb-3">
    <label class="form-label"><?= __('Province') ?></label>
    <?= $this->Form->control('master_propinsi_id', [
        'options' => $masterPropinsis,
        'empty' => 'Select Province',
        'class' => 'form-control',
        'label' => false
    ]) ?>
</div>

<!-- City/District Dropdown (Cascading) -->
<div class="col-12 mb-3">
    <label class="form-label"><?= __('City/District') ?></label>
    <?= $this->Form->control('master_kabupaten_id', [
        'options' => $masterKabupatens,
        'empty' => 'Select City/District',
        'class' => 'form-control',
        'label' => false
    ]) ?>
</div>

<!-- Subdistrict Dropdown (Cascading) -->
<div class="col-12 mb-3">
    <label class="form-label"><?= __('Subdistrict') ?></label>
    <?= $this->Form->control('master_kecamatan_id', [
        'options' => $masterKecamatans,
        'empty' => 'Select Subdistrict',
        'class' => 'form-control',
        'label' => false
    ]) ?>
</div>

<!-- Village Dropdown (Cascading) -->
<div class="col-12 mb-3">
    <label class="form-label"><?= __('Village') ?></label>
    <?= $this->Form->control('master_kelurahan_id', [
        'options' => $masterKelurahans,
        'empty' => 'Select Village',
        'class' => 'form-control',
        'label' => false
    ]) ?>
</div>

<!-- JavaScript for Cascade Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== CASCADE DROPDOWN INITIALIZED ===');
    
    // Data arrays from controller
    var kabupatensData = <?= json_encode($masterKabupatensData) ?>;
    var kecamatansData = <?= json_encode($masterKecamatansData) ?>;
    var kelurahansData = <?= json_encode($masterKelurahansData) ?>;
    
    console.log('Kabupatens data loaded:', kabupatensData.length);
    console.log('Kecamatans data loaded:', kecamatansData.length);
    console.log('Kelurahans data loaded:', kelurahansData.length);
    
    // Province change event
    $('#master-propinsi-id').on('change', function() {
        var propinsiId = $(this).val();
        console.log('Province changed to:', propinsiId);
        
        var kabupatenSelect = $('#master-kabupaten-id');
        var kecamatanSelect = $('#master-kecamatan-id');
        var kelurahanSelect = $('#master-kelurahan-id');
        
        // Clear all cascading dropdowns
        kabupatenSelect.empty().append('<option value="">Select City/District</option>');
        kecamatanSelect.empty().append('<option value="">Select Subdistrict</option>');
        kelurahanSelect.empty().append('<option value="">Select Village</option>');
        
        // Populate Kabupaten based on Propinsi
        if (propinsiId) {
            kabupatensData.forEach(function(kab) {
                if (kab.propinsi_id == propinsiId) {
                    kabupatenSelect.append('<option value="' + kab.id + '">' + kab.title + '</option>');
                }
            });
        }
    });
    
    // Kabupaten change event
    $('#master-kabupaten-id').on('change', function() {
        var kabupatenId = $(this).val();
        console.log('Kabupaten changed to:', kabupatenId);
        
        var kecamatanSelect = $('#master-kecamatan-id');
        var kelurahanSelect = $('#master-kelurahan-id');
        
        // Clear dependent dropdowns
        kecamatanSelect.empty().append('<option value="">Select Subdistrict</option>');
        kelurahanSelect.empty().append('<option value="">Select Village</option>');
        
        // Populate Kecamatan based on Kabupaten
        if (kabupatenId) {
            kecamatansData.forEach(function(kec) {
                if (kec.kabupaten_id == kabupatenId) {
                    kecamatanSelect.append('<option value="' + kec.id + '">' + kec.title + '</option>');
                }
            });
        }
    });
    
    // Kecamatan change event
    $('#master-kecamatan-id').on('change', function() {
        var kecamatanId = $(this).val();
        console.log('Kecamatan changed to:', kecamatanId);
        
        var kelurahanSelect = $('#master-kelurahan-id');
        
        // Clear Kelurahan dropdown
        kelurahanSelect.empty().append('<option value="">Select Village</option>');
        
        // Populate Kelurahan based on Kecamatan
        if (kecamatanId) {
            kelurahansData.forEach(function(kel) {
                if (kel.kecamatan_id == kecamatanId) {
                    kelurahanSelect.append('<option value="' + kel.id + '">' + kel.title + '</option>');
                }
            });
        }
    });
    
    console.log('=== CASCADE EVENT HANDLERS ATTACHED ===');
});
</script>
```

### 4. Template - view.ctp

```php
<tr>
    <th class="github-detail-label"><?= __('Province') ?></th>
    <td class="github-detail-value">
        <?= $entity->has('master_propinsi') ? h($entity->master_propinsi->title) : '' ?>
    </td>
</tr>
<tr>
    <th class="github-detail-label"><?= __('City/District') ?></th>
    <td class="github-detail-value">
        <?= $entity->has('master_kabupaten') ? h($entity->master_kabupaten->title) : '' ?>
    </td>
</tr>
<tr>
    <th class="github-detail-label"><?= __('Subdistrict') ?></th>
    <td class="github-detail-value">
        <?= $entity->has('master_kecamatan') ? h($entity->master_kecamatan->title) : '' ?>
    </td>
</tr>
<tr>
    <th class="github-detail-label"><?= __('Village') ?></th>
    <td class="github-detail-value">
        <?= $entity->has('master_kelurahan') ? h($entity->master_kelurahan->title) : '' ?>
    </td>
</tr>
```

## Key Points

### ✅ Do's
1. **Always load full data arrays** for JavaScript cascade
2. **Show only stored values** in edit mode initially
3. **Use proper association names** (MasterKabupatens, not Kabupatens)
4. **Include parent_id fields** in data selects (propinsi_id, kabupaten_id, etc.)
5. **Reset all dependent dropdowns** when parent changes
6. **Use console.log** for debugging cascade logic
7. **Match element IDs** exactly with form field names

### ❌ Don'ts
1. **Don't load all options** for Kabupaten/Kecamatan/Kelurahan in edit mode
2. **Don't forget** to clear dependent dropdowns on parent change
3. **Don't use** shortened association names (use MasterKabupatens, not Kabupatens)
4. **Don't forget** to include parent_id in data arrays
5. **Don't hardcode** dropdown options in JavaScript

## Troubleshooting

### Issue: Dropdowns not populating
**Solution:** 
- Check browser console for JavaScript errors
- Verify data arrays are not empty: `console.log(kabupatensData)`
- Ensure jQuery is loaded before script

### Issue: Wrong data showing
**Solution:**
- Verify parent_id fields are correct (propinsi_id, kabupaten_id, kecamatan_id)
- Check association names match exactly
- Use `'strategy' => 'select'` for cross-database associations

### Issue: Dropdown IDs not matching
**Solution:**
- CakePHP converts field names to kebab-case for IDs
- `master_propinsi_id` becomes `master-propinsi-id`
- Use browser inspector to verify actual element IDs

### Issue: Edit mode shows all options
**Solution:**
- Controller should filter by stored ID only:
  ```php
  ->where(['id' => $entity->master_kabupaten_id])->toArray()
  ```
- Not: `->find('list')->toArray()` (this loads all)

## Testing Checklist

- [ ] Add mode: All cascades start empty
- [ ] Add mode: Province selection populates Kabupaten
- [ ] Add mode: Kabupaten selection populates Kecamatan
- [ ] Add mode: Kecamatan selection populates Kelurahan
- [ ] Edit mode: Shows only stored values initially
- [ ] Edit mode: Province change resets all cascades
- [ ] View mode: Displays all stored values correctly
- [ ] No JavaScript errors in console
- [ ] Mobile responsive
- [ ] Works on all browsers (Chrome, Firefox, Safari, Edge)

## Examples

### Example 1: Candidates Table
```php
// CandidatesTable.php - Associations already set up
// CandidatesController.php - edit() method implemented
// src/Template/Candidates/edit.ctp - Cascade dropdowns working
```

### Example 2: Trainees Table
```php
// TraineesTable.php - Associations already set up
// TraineesController.php - edit() method implemented
// src/Template/Trainees/edit.ctp - Cascade dropdowns working
```

### Example 3: Apprentices Table
```php
// ApprenticesTable.php - Associations already set up
// ApprenticesController.php - edit() method implemented
// src/Template/Apprentices/edit.ctp - Cascade dropdowns working
```

## Performance Notes

- Data arrays loaded once per page load
- Client-side filtering (no AJAX required)
- Fast response time
- Works offline after initial load
- Minimal server requests

## Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers

## Credits

Built with:
- CakePHP 3.9 ORM
- jQuery for event handling
- Bootstrap 4 for styling
- JavaScript ES5 (PHP 5.6 compatible)
