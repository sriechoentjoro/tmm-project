# View Template - Hide File/Image Fields in Detail Section

**Date:** November 16, 2025  
**Status:** ✅ **COMPLETE**

## Purpose

Remove duplicate display of file/image fields in the Detail tab since they are already shown in the Media Preview section at the top of the view page.

## Changes Made

### 1. Bake Template Updated (`src/Template/Bake/Template/view.ctp`)

**Added filter to skip file/image fields in detail section:**

```php
// BEFORE
if ($isKey !== true):
    if (!in_array($field, ['created', 'modified', 'updated'])):
        if ($type === 'number'):

// AFTER
if ($isKey !== true):
    // Skip image/file fields as they are already shown in preview section
    $isFileField = preg_match('/(image|file|photo|document|foto|gambar|dokumen|attachment)/i', $field);
    if (!in_array($field, ['created', 'modified', 'updated']) && !$isFileField):
        if ($type === 'number'):
```

**Pattern detection:**
- Automatically detects fields containing: `image`, `file`, `photo`, `document`, `foto`, `gambar`, `dokumen`, `attachment`
- Case-insensitive matching
- Skips these fields in the detail table

### 2. ApprenticeOrders View Updated (`src/Template/ApprenticeOrders/view.ctp`)

**Removed `Reference File` row from detail table:**

```php
<!-- BEFORE -->
<tr>
    <th class="github-detail-label"><?= __('Reference File') ?></th>
    <td class="github-detail-value"><?= h($apprenticeOrder->reference_file) ?></td>
</tr>

<!-- AFTER -->
<!-- Reference File removed - already shown in preview section above -->
```

## Benefits

1. ✅ **No duplication** - File paths not shown twice
2. ✅ **Cleaner UI** - Detail section only shows relevant data
3. ✅ **Better UX** - File preview section is more prominent and functional
4. ✅ **Future-proof** - All future baked tables will auto-hide file fields

## Field Types Affected

The following field name patterns are automatically hidden from detail section:

| Pattern | Example Fields |
|---------|---------------|
| `*image*` | `profile_image`, `photo_image`, `cover_image` |
| `*file*` | `reference_file`, `attachment_file`, `document_file` |
| `*photo*` | `profile_photo`, `user_photo` |
| `*document*` | `submission_document`, `contract_document` |
| `*foto*` | `foto_profile`, `foto_ktp` (Indonesian) |
| `*gambar*` | `gambar_profile` (Indonesian) |
| `*dokumen*` | `dokumen_kontrak` (Indonesian) |
| `*attachment*` | `email_attachment`, `message_attachment` |

## Preview Section Features

File/image fields are displayed in the Media Preview Section with:

- **PDF Files:** Inline iframe preview (600px height on desktop, 400px on mobile)
- **Images:** Full image preview with responsive sizing
- **Documents:** File icon with download link
- **Multiple Files:** Slideshow with prev/next navigation
- **Download Button:** Direct download link for all file types
- **Open in New Tab:** Opens file in separate browser tab

## For Future Bakes

When you bake new tables with `bin\cake bake all TableName`:

1. ✅ File/image fields will automatically appear in Media Preview section
2. ✅ File/image fields will automatically be hidden from Detail table
3. ✅ No manual editing required

## Example Output

**Before (with duplicate Reference File):**

```
┌─ Detail Tab ─────────────────────┐
│ ID: 4                             │
│ Male Trainee: 67                  │
│ Female Trainee: 16                │
│ Reference File: files/...pdf  ← DUPLICATE
│ Title: YUGEN KAISHA...            │
└───────────────────────────────────┘
```

**After (clean detail section):**

```
┌─ Detail Tab ─────────────────────┐
│ ID: 4                             │
│ Male Trainee: 67                  │
│ Female Trainee: 16                │
│ Title: YUGEN KAISHA...            │
│ Departure Year: 2026              │
└───────────────────────────────────┘

[File already shown in preview section above ↑]
```

## Testing

✅ **Verified on:**
- ApprenticeOrders view page
- Reference File shown in preview section (with PDF iframe)
- Reference File NOT shown in detail table
- Other fields display normally

## Notes

- This change only affects the **Detail tab** in view templates
- The **Media Preview section** (at the top) still shows all files/images with full preview functionality
- Association tabs (BelongsTo, HasMany, etc.) are not affected
- Add/Edit forms still show file upload inputs normally

## Related Files

- `src/Template/Bake/Template/view.ctp` - Master bake template (updated)
- `src/Template/ApprenticeOrders/view.ctp` - Manually updated for existing template
- All future baked view templates will inherit this behavior

## Conclusion

✅ View templates now have cleaner detail sections without duplicate file path displays. File preview section remains fully functional at the top of the page.
