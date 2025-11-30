# File Viewer - Quick Reference Card

## 🚀 Quick Start

### Display File with Icon & Modal
```php
<?= $this->element('file_viewer', [
    'filePath' => $entity->reference_file,
    'label' => basename($entity->reference_file)
]) ?>
```

### With Null Check
```php
<?php if (!empty($entity->document_path)): ?>
    <?= $this->element('file_viewer', ['filePath' => $entity->document_path]) ?>
<?php else: ?>
    <span style="color: #999;">No file</span>
<?php endif; ?>
```

## 📁 Supported File Types

| Extension | Color | Preview | Icon |
|-----------|-------|---------|------|
| PDF | 🔴 Red | ✅ Modal | 📄 |
| DOC/DOCX | 🔵 Blue | ⬇️ Download | 📝 |
| XLS/XLSX | 🟢 Green | ⬇️ Download | 📊 |
| JPG/PNG | 🟣 Purple | ✅ Modal | 🖼️ |
| ZIP/RAR | ⚫ Gray | ⬇️ Download | 📦 |
| TXT | ⚫ Gray | ✅ Modal | 📋 |

## ⚙️ Options

```php
<?= $this->element('file_viewer', [
    'filePath' => $entity->file,      // Required
    'label' => 'Custom Label',        // Optional
    'showIcon' => true,                // Optional (default: true)
    'showModal' => true                // Optional (default: true)
]) ?>
```

## 🔧 Auto-Apply to All Templates

```powershell
.\apply_file_viewer_to_all_templates.ps1
```

## 📍 File Locations

- **Element:** `src/Template/Element/file_viewer.ctp`
- **Helper:** `src/View/Helper/FileViewerHelper.php`
- **Docs:** `FILE_VIEWER_USAGE.md`

## 🎨 Modal Features

- 📱 Responsive (90% width, 80vh height)
- 🎨 Gradient header with icon
- 🔍 Full preview with iframe/image
- ⬇️ Download button
- 🔗 Open in new tab button

## 💡 Common Patterns

### In Index Table
```php
<td>
    <?php if (!empty($item->file)): ?>
        <?= $this->element('file_viewer', ['filePath' => $item->file]) ?>
    <?php endif; ?>
</td>
```

### In View Details
```php
<tr>
    <th>Document</th>
    <td>
        <?= $this->element('file_viewer', [
            'filePath' => $entity->document,
            'label' => 'View Document'
        ]) ?>
    </td>
</tr>
```

### Multiple Files
```php
<?php foreach ($entity->documents as $doc): ?>
    <div class="file-item">
        <?= $this->element('file_viewer', [
            'filePath' => $doc->path,
            'label' => $doc->name
        ]) ?>
    </div>
<?php endforeach; ?>
```

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Modal not showing | Check Bootstrap 4+ is loaded |
| File not found | Verify path is relative to webroot |
| Preview not working | Ensure browser supports file type |
| Icon not showing | Check file extension is supported |

## 🔄 Cache Clear

After any changes:
```powershell
.\bin\cake.bat cache clear_all
```

## 📚 Full Documentation

See `FILE_VIEWER_USAGE.md` for complete guide.
