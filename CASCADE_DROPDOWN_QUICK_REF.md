# Cascade Dropdown - Quick Reference

## 🎯 Quick Setup

### 1. Controller Pattern (Edit Mode)
```php
// Show only stored values initially
$masterKabupatens = !empty($entity->master_kabupaten_id) 
    ? $this->Table->MasterKabupatens->find('list')->where(['id' => $entity->master_kabupaten_id])->toArray()
    : [];

// Load full data for JavaScript
$masterKabupatensData = $this->Table->MasterKabupatens->find('all')
    ->select(['id', 'title', 'propinsi_id'])->toArray();
```

### 2. Template Pattern
```php
<?= $this->Form->control('master_propinsi_id', [
    'options' => $masterPropinsis,
    'empty' => 'Select Province',
    'class' => 'form-control'
]) ?>
```

### 3. JavaScript Pattern
```javascript
var kabupatensData = <?= json_encode($masterKabupatensData) ?>;

$('#master-propinsi-id').on('change', function() {
    var propinsiId = $(this).val();
    var kabupatenSelect = $('#master-kabupaten-id');
    
    kabupatenSelect.empty().append('<option value="">Select City</option>');
    
    if (propinsiId) {
        kabupatensData.forEach(function(kab) {
            if (kab.propinsi_id == propinsiId) {
                kabupatenSelect.append('<option value="' + kab.id + '">' + kab.title + '</option>');
            }
        });
    }
});
```

## 📊 Data Flow

```
Province (All)
    ↓ (change event)
Kabupaten (filtered by propinsi_id)
    ↓ (change event)
Kecamatan (filtered by kabupaten_id)
    ↓ (change event)
Kelurahan (filtered by kecamatan_id)
```

## 🔑 Key Rules

| Mode | Province | Kabupaten | Kecamatan | Kelurahan |
|------|----------|-----------|-----------|-----------|
| **Add** | All | Empty | Empty | Empty |
| **Edit** | All | Stored only | Stored only | Stored only |
| **View** | Display | Display | Display | Display |

## 🏗️ Table Structure

```
master_propinsis (Province)
master_kabupatens (City) → propinsi_id
master_kecamatans (Subdistrict) → kabupaten_id
master_kelurahans (Village) → kecamatan_id
```

## ✅ Checklist

- [ ] Associations defined with `'strategy' => 'select'`
- [ ] Controller loads only stored values in edit mode
- [ ] Controller provides full data arrays for JavaScript
- [ ] Template uses correct element IDs
- [ ] JavaScript resets dependent dropdowns on change
- [ ] View mode displays all values correctly

## 🐛 Common Issues

| Issue | Solution |
|-------|----------|
| Dropdowns empty | Check `console.log(dataArray)` |
| Wrong IDs | Use `master-propinsi-id` not `master_propinsi_id` |
| All options show | Filter by ID in edit: `->where(['id' => $entity->field_id])` |
| Not resetting | Clear dropdowns: `select.empty().append('<option>')` |

## 📝 Association Names

✅ **Correct:**
- `MasterPropinsis`
- `MasterKabupatens`
- `MasterKecamatans`
- `MasterKelurahans`

❌ **Wrong:**
- `Propinsis`, `Kabupatens`, `Kecamatans`, `Kelurahans`

## 🔧 Element ID Mapping

| Field Name | Element ID |
|------------|------------|
| `master_propinsi_id` | `#master-propinsi-id` |
| `master_kabupaten_id` | `#master-kabupaten-id` |
| `master_kecamatan_id` | `#master-kecamatan-id` |
| `master_kelurahan_id` | `#master-kelurahan-id` |

## 📚 Full Documentation

See `CASCADE_DROPDOWN_GUIDE.md` for complete implementation guide.
