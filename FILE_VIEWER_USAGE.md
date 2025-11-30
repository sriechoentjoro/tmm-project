# File Viewer - Usage Guide

## Overview
File Viewer adalah fitur untuk menampilkan file dengan icon SVG berdasarkan extension dan modal preview dengan iframe.

## Features
✅ Auto-detect file extension
✅ Display file icon (PDF, DOC, XLS, Images, ZIP, etc.)
✅ Color-coded by file type
✅ Modal preview untuk file yang bisa di-preview (PDF, Images)
✅ Direct download untuk file yang tidak bisa di-preview (DOC, XLS, ZIP)
✅ Responsive dan mobile-friendly

## Supported File Types

### Previewable (Modal dengan Iframe)
- **PDF** - Red color (#E74C3C)
- **Images** (JPG, JPEG, PNG, GIF) - Purple color (#9B59B6)
- **Text** (TXT, HTML) - Gray color (#7F8C8D)

### Downloadable (Direct Link)
- **Word** (DOC, DOCX) - Blue color (#2980B9)
- **Excel** (XLS, XLSX) - Green color (#27AE60)
- **Archive** (ZIP, RAR) - Gray color (#95A5A6)

## Usage

### Method 1: Using Element (Recommended)

```php
<?php if (!empty($entity->reference_file)): ?>
    <?= $this->element('file_viewer', [
        'filePath' => $entity->reference_file,
        'label' => basename($entity->reference_file),  // Optional
        'showIcon' => true,                             // Optional, default: true
        'showModal' => true                             // Optional, default: true
    ]) ?>
<?php else: ?>
    <span style="color: #999; font-style: italic;">No file</span>
<?php endif; ?>
```

### Method 2: Using Helper

```php
<?php if (!empty($entity->document_path)): ?>
    <?= $this->FileViewer->display($entity->document_path, [
        'label' => 'View Document',
        'showIcon' => true,
        'showModal' => true
    ]) ?>
<?php endif; ?>
```

## Examples

### Example 1: In Index Table (index.ctp)

```php
<td style="padding: 8px;">
    <?php if (!empty($apprenticeOrder->reference_file)): ?>
        <?= $this->element('file_viewer', [
            'filePath' => $apprenticeOrder->reference_file,
            'label' => basename($apprenticeOrder->reference_file)
        ]) ?>
    <?php else: ?>
        <span style="color: #999; font-style: italic;">No file</span>
    <?php endif; ?>
</td>
```

### Example 2: In View Details (view.ctp)

```php
<tr>
    <th class="github-detail-label"><?= __('Reference File') ?></th>
    <td class="github-detail-value">
        <?php if (!empty($apprenticeOrder->reference_file)): ?>
            <?= $this->element('file_viewer', [
                'filePath' => $apprenticeOrder->reference_file,
                'label' => basename($apprenticeOrder->reference_file)
            ]) ?>
        <?php else: ?>
            <span style="color: #999; font-style: italic;">No file</span>
        <?php endif; ?>
    </td>
</tr>
```

### Example 3: Multiple Files

```php
<?php foreach ($entity->documents as $document): ?>
    <div class="document-item" style="margin-bottom: 8px;">
        <?= $this->element('file_viewer', [
            'filePath' => $document->file_path,
            'label' => $document->name
        ]) ?>
    </div>
<?php endforeach; ?>
```

### Example 4: Custom Label and No Icon

```php
<?= $this->element('file_viewer', [
    'filePath' => $entity->certificate,
    'label' => 'Download Certificate',
    'showIcon' => false,
    'showModal' => true
]) ?>
```

## Auto-Apply to All Templates

Run this PowerShell script to automatically apply file_viewer to all existing templates:

```powershell
.\apply_file_viewer_to_all_templates.ps1
```

This script will:
1. Scan all `index.ctp` and `view.ctp` files
2. Find fields containing `file`, `document`, or `attachment`
3. Replace simple `<?= h($entity->field) ?>` with file_viewer element
4. Backup original files before modification

## Modal Features

When a file is previewed in modal:
- **Full screen view** (80vh height, 90% width)
- **Gradient header** with file icon and name
- **Preview area** with iframe or image display
- **Action buttons**:
  - Open in New Tab
  - Download
  - Close

## Customization

### Custom Colors

Edit `src/Template/Element/file_viewer.ctp` and modify the `$colorMap` array:

```php
$colorMap = [
    'pdf' => '#E74C3C',     // Red
    'doc' => '#2980B9',     // Blue
    'xls' => '#27AE60',     // Green
    // Add your custom colors here
];
```

### Custom Icons

Edit `src/Template/Element/file_viewer.ctp` and modify the `$iconMap` array with your SVG icons.

### Add New File Types

Add to `$previewableTypes` array in element:

```php
$previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt', 'html', 'csv'];
```

## Troubleshooting

### Modal doesn't show
- Ensure Bootstrap 4+ is loaded
- Check browser console for JavaScript errors
- Verify jQuery is loaded before Bootstrap

### File not displaying
- Check file path is correct (relative to webroot)
- Verify file exists in `webroot/` directory
- Check file permissions

### Preview not working
- Ensure file extension is in `$previewableTypes` array
- Some file types may be blocked by browser security
- PDF preview requires browser PDF support

## File Structure

```
src/
├── Template/
│   └── Element/
│       └── file_viewer.ctp          # Main element file
├── View/
│   ├── AppView.php                  # Register helper
│   └── Helper/
│       └── FileViewerHelper.php     # Helper class
└── ...

apply_file_viewer_to_all_templates.ps1   # Auto-apply script
FILE_VIEWER_USAGE.md                     # This documentation
```

## Best Practices

1. **Always check if file exists** before displaying
2. **Use basename()** for labels to show only filename
3. **Provide fallback** for empty files
4. **Test on mobile devices** for responsiveness
5. **Clear cache** after modifications: `.\bin\cake.bat cache clear_all`

## Integration Examples

### With HasMany Relationship

```php
<?php if (!empty($candidate->candidate_documents)): ?>
    <h4>Documents</h4>
    <?php foreach ($candidate->candidate_documents as $doc): ?>
        <div class="document-row">
            <?= $this->element('file_viewer', [
                'filePath' => $doc->file_path,
                'label' => $doc->document_type . ' - ' . $doc->name
            ]) ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
```

### With File Upload Form

After upload, display the uploaded file:

```php
<?php if (!empty($entity->uploaded_file)): ?>
    <div class="alert alert-success">
        File uploaded successfully: 
        <?= $this->element('file_viewer', [
            'filePath' => $entity->uploaded_file,
            'label' => 'View Uploaded File'
        ]) ?>
    </div>
<?php endif; ?>
```

## Performance Notes

- Modal content loaded on-demand (when clicked)
- SVG icons are inline (no additional HTTP requests)
- File preview uses native browser iframe (no external libraries)
- Minimal CSS and JavaScript footprint

## Browser Compatibility

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Credits

Built with:
- Bootstrap 4+ (Modal and styling)
- Feather Icons (SVG icons)
- CakePHP 3.9 (Element and Helper system)
