# File Viewer Element - Usage with Edit Link

## Overview
The `file_viewer` element now supports showing a warning message with an edit link when a file is recorded in the database but the physical file doesn't exist.

## Features
- ✅ Detects if file exists physically using `file_exists()` and `is_readable()`
- ✅ Shows warning message: "File not found" or "Image not found" (auto-detect based on extension)
- ✅ Provides "Click to edit & upload" link when `editUrl` is provided
- ✅ Blinking warning icon animation
- ✅ Yellow warning box styling
- ✅ Modal preview only shows for existing files

## Basic Usage

### 1. In View Template (without edit link)
```php
<?php if (!empty($entity->file_field)): ?>
    <?= $this->element('file_viewer', [
        'filePath' => $entity->file_field
    ]) ?>
<?php else: ?>
    <span style="color: #999; font-style: italic;">No file uploaded</span>
<?php endif; ?>
```

### 2. In View Template (with edit link)
```php
<?php if (!empty($entity->reference_file)): ?>
    <?= $this->element('file_viewer', [
        'filePath' => $entity->reference_file,
        'editUrl' => $this->Url->build(['action' => 'edit', $entity->id])
    ]) ?>
<?php else: ?>
    <span style="color: #999; font-style: italic;">
        No file uploaded - 
        <?= $this->Html->link('Click to upload', ['action' => 'edit', $entity->id]) ?>
    </span>
<?php endif; ?>
```

### 3. For Image Fields
```php
<?php if (!empty($candidate->image_photo)): ?>
    <h4>Photo</h4>
    <?= $this->element('file_viewer', [
        'filePath' => 'img/uploads/' . $candidate->image_photo,
        'editUrl' => $this->Url->build(['action' => 'edit', $candidate->id])
    ]) ?>
<?php else: ?>
    <p style="color: #999; font-style: italic;">
        No photo uploaded - 
        <?= $this->Html->link('Upload photo', ['action' => 'edit', $candidate->id]) ?>
    </p>
<?php endif; ?>
```

## Element Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `filePath` | string | Yes | - | Relative path from webroot (e.g., `files/document.pdf` or `img/uploads/photo.jpg`) |
| `editUrl` | string | No | null | URL to edit page where user can upload the file |
| `label` | string | No | filename | Label for modal title |
| `showIcon` | bool | No | true | Show file icon |
| `showModal` | bool | No | true | Enable modal preview |

## Visual Output

### When File Exists
- Shows colored icon based on file type
- Clicking icon opens modal preview (for PDF/images)
- Direct download link for other files

### When File Does NOT Exist
```
⚠️ File not found   [Click to edit & upload]
```
- Yellow warning box with border
- Blinking warning icon
- Text: "File not found" or "Image not found"
- Edit link (if `editUrl` provided)

## Common Patterns

### Pattern 1: ApprenticeOrders View
```php
<!-- Reference File -->
<tr>
    <th><?= __('Reference File') ?></th>
    <td>
        <?php if (!empty($apprenticeOrder->reference_file)): ?>
            <?= $this->element('file_viewer', [
                'filePath' => $apprenticeOrder->reference_file,
                'editUrl' => $this->Url->build(['action' => 'edit', $apprenticeOrder->id])
            ]) ?>
        <?php else: ?>
            <span style="color: #999; font-style: italic;">
                No file - 
                <?= $this->Html->link('Upload', ['action' => 'edit', $apprenticeOrder->id]) ?>
            </span>
        <?php endif; ?>
    </td>
</tr>
```

### Pattern 2: Candidates View (Photo)
```php
<!-- Photo -->
<tr>
    <th><?= __('Photo') ?></th>
    <td>
        <?php if (!empty($candidate->image_photo)): ?>
            <?= $this->element('file_viewer', [
                'filePath' => 'img/uploads/' . $candidate->image_photo,
                'editUrl' => $this->Url->build(['action' => 'edit', $candidate->id])
            ]) ?>
        <?php else: ?>
            <div style="padding: 20px; text-align: center; background: #f6f8fa; border: 1px dashed #d0d7de; border-radius: 4px;">
                <i class="fas fa-user" style="font-size: 48px; color: #d0d7de;"></i>
                <p style="margin: 10px 0 0 0; color: #57606a;">
                    No photo - 
                    <?= $this->Html->link('Upload photo', ['action' => 'edit', $candidate->id]) ?>
                </p>
            </div>
        <?php endif; ?>
    </td>
</tr>
```

### Pattern 3: Multiple Files
```php
<!-- Documents Section -->
<div class="documents-section">
    <h4>Supporting Documents</h4>
    
    <!-- CV/Resume -->
    <div class="doc-item">
        <strong>CV/Resume:</strong>
        <?php if (!empty($candidate->file_cv)): ?>
            <?= $this->element('file_viewer', [
                'filePath' => 'files/uploads/cv/' . $candidate->file_cv,
                'editUrl' => $this->Url->build(['action' => 'edit', $candidate->id])
            ]) ?>
        <?php else: ?>
            <span style="color: #999;">Not uploaded - 
                <?= $this->Html->link('Upload', ['action' => 'edit', $candidate->id]) ?>
            </span>
        <?php endif; ?>
    </div>
    
    <!-- Certificate -->
    <div class="doc-item">
        <strong>Certificate:</strong>
        <?php if (!empty($candidate->file_certificate)): ?>
            <?= $this->element('file_viewer', [
                'filePath' => 'files/uploads/certificates/' . $candidate->file_certificate,
                'editUrl' => $this->Url->build(['action' => 'edit', $candidate->id])
            ]) ?>
        <?php else: ?>
            <span style="color: #999;">Not uploaded - 
                <?= $this->Html->link('Upload', ['action' => 'edit', $candidate->id]) ?>
            </span>
        <?php endif; ?>
    </div>
</div>
```

## Auto-Detection Logic

### Image Extensions
The element automatically detects image files based on extension:
- jpg, jpeg, png, gif, bmp, svg, webp

**Shows:** "Image not found"

### Document Extensions
All other extensions:
- pdf, doc, docx, xls, xlsx, txt, zip, rar, etc.

**Shows:** "File not found"

## CSS Styling

The warning message uses these classes:
- `.file-not-found-msg` - Main container (yellow box)
- `.file-error-blink` - Blinking animation for icon
- `.edit-link` - Styled edit link (blue, underline)

You can customize in your CSS:
```css
.file-not-found-msg {
    background-color: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
}

.file-not-found-msg .edit-link {
    color: #0969da;
    text-decoration: underline;
}
```

## Tips

1. **Always check if field is empty first**
   ```php
   <?php if (!empty($entity->file_field)): ?>
       <?= $this->element('file_viewer', [...]) ?>
   <?php else: ?>
       <!-- Show "No file" message -->
   <?php endif; ?>
   ```

2. **Provide edit URL for better UX**
   ```php
   'editUrl' => $this->Url->build(['action' => 'edit', $entity->id])
   ```

3. **Use correct path from webroot**
   - ✅ `files/uploads/document.pdf`
   - ✅ `img/uploads/photo.jpg`
   - ❌ `/var/www/html/webroot/files/...` (absolute path)

4. **Handle multiple missing files gracefully**
   ```php
   $missingFiles = [];
   if (!empty($entity->file1) && !file_exists(WWW_ROOT . $entity->file1)) {
       $missingFiles[] = 'File 1';
   }
   // Show warning for missing files
   ```

## Troubleshooting

### Issue: Edit link not showing
**Solution:** Pass `editUrl` parameter:
```php
'editUrl' => $this->Url->build(['action' => 'edit', $entity->id])
```

### Issue: File shows as "not found" but exists
**Solution:** Check file permissions:
```bash
# Windows (PowerShell as admin)
icacls "D:\xampp\htdocs\project_tmm\webroot\files" /grant "IIS_IUSRS:(OI)(CI)R"

# Or check path is correct (relative to webroot)
```

### Issue: Modal not opening
**Solution:** Ensure Bootstrap 4+ and jQuery are loaded:
```html
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
```

## See Also
- `FILE_VIEWER_USAGE.md` - Complete file viewer documentation
- `FILE_VIEWER_QUICK_REF.md` - Quick reference card
- `src/Template/Element/file_viewer.ctp` - Element source code
- `src/View/Helper/FileViewerHelper.php` - Helper class

---

**Last Updated:** 2025-11-16  
**Status:** ✅ Production Ready
