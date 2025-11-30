# ImageResize Library Fix - Production Server

## Problem
```
Warning (2): require_once(/var/www/tmm/vendor/ImageResize/ImageResize.php): 
failed to open stream: No such file or directory 
[APP/Controller/AppController.php, line 21]

Fatal error: require_once() [function.require]: Failed opening required 
'/var/www/tmm/vendor/ImageResize/ImageResize.php'
```

## Root Cause
The `ImageResize` library folder was missing from the production server's vendor directory at `/var/www/tmm/vendor/ImageResize/`.

## Solution Applied

### 1. Created Upload Script
**File:** `upload_imageresize_to_production.ps1`

The script:
- ✅ Compresses local `vendor\ImageResize` folder to zip
- ✅ Uploads zip to production server via SCP
- ✅ Extracts files to `/var/www/tmm/vendor/ImageResize/`
- ✅ Sets correct permissions (www-data:www-data, 755)
- ✅ Verifies installation
- ✅ Cleans up temporary files

### 2. Executed Upload
```powershell
powershell -ExecutionPolicy Bypass -File upload_imageresize_to_production.ps1
```

**Result:**
- Successfully uploaded `ImageResize.php` (21 KB)
- File location: `/var/www/tmm/vendor/ImageResize/ImageResize.php`
- Permissions: `rwxr-xr-x www-data:www-data`

### 3. Restarted PHP-FPM
```bash
ssh root@103.214.112.58 "systemctl restart php7.4-fpm"
```

Cleared cached states and applied the new library.

## Verification

### File Exists Check
```bash
test -f /var/www/tmm/vendor/ImageResize/ImageResize.php
# Output: File exists!
```

### Directory Structure
```
/var/www/tmm/vendor/ImageResize/
├── ImageResize.php (21,435 bytes)
```

### Permissions
```
-rwxr-xr-x 1 www-data www-data 21435 Nov  7 16:13 ImageResize.php
```

## Status
✅ **RESOLVED** - ImageResize library is now available on production server

## Usage in Code
The library is loaded in `src/Controller/AppController.php`:
```php
// Line 21
require_once ROOT . DS . 'vendor' . DS . 'ImageResize' . DS . 'ImageResize.php';
use ImageResize\ImageResize;
```

This is used throughout the application for:
- Image uploads
- Thumbnail generation
- Watermarking
- Image resizing in various controllers

## Notes
- The ImageResize library is a custom/manual library (not managed by Composer)
- It's stored in `vendor/ImageResize/` but not part of `composer.json`
- Future deployments should include this folder in the upload process
- Consider adding to deployment checklist or automation scripts

## Related Files
- `src/Controller/AppController.php` - Requires the library
- `upload_imageresize_to_production.ps1` - Upload script (reusable)
- Image upload controllers use `ImageResize` class for processing

## Prevention
To avoid this issue in future deployments:
1. ✅ Include `vendor/ImageResize/` in deployment scripts
2. ✅ Add to pre-deployment checklist
3. ✅ Consider adding to `composer.json` as custom repository
4. ✅ Document in deployment guide

## Timestamp
**Fixed:** November 19, 2025, 12:31 PM (UTC+7)
**By:** Automated deployment script
