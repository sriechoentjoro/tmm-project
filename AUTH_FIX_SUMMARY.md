# Auth Component Error Fix - November 24, 2025

## Problem
Error when accessing `/tmm/candidates/wizard`:
```
Notice (1024): Undefined property: CandidatesController::$Auth in /var/www/tmm/src/Controller/CandidatesController.php on line 23
Error: Call to a member function allow() on bool
```

## Root Cause
In `CandidatesController.php`, the `initialize()` method was calling `$this->Auth->allow()` **before** calling `parent::initialize()`. This meant the Auth component hadn't been loaded yet from AppController.

### Original Code (WRONG):
```php
class CandidatesController extends AppController
{
    public function initialize()
    {
        parent::initialize();  // Auth loaded here
        $this->Auth->allow(['getKabupaten', 'getKecamatan', 'getKelurahan']);
    }
    use \App\Controller\ExportTrait;
    use \App\Controller\LpkDataFilterTrait;
    // ...
}
```

**Issue**: `use` statements were placed AFTER the `initialize()` method, which is incorrect PHP syntax order.

## Solution

### Fixed Code:
```php
class CandidatesController extends AppController
{
    use \App\Controller\ExportTrait;
    use \App\Controller\LpkDataFilterTrait;
    
    public function initialize()
    {
        parent::initialize();  // Auth loaded here first
        $this->Auth->allow(['wizard', 'getKabupaten', 'getKecamatan', 'getKelurahan']);
    }
    // ...
}
```

**Changes**:
1. ✅ Moved `use` trait statements to top of class (correct PHP syntax)
2. ✅ Added `'wizard'` to the allowed actions list (public access)
3. ✅ Kept `parent::initialize()` at the beginning so Auth loads first

## Deployment Steps

1. **Fixed local file**: `d:\xampp\htdocs\tmm\src\Controller\CandidatesController.php`
2. **Uploaded to production**:
   ```bash
   scp CandidatesController.php root@103.214.112.58:/var/www/tmm/src/Controller/
   ```
3. **Cleared cache**:
   ```bash
   ssh root@103.214.112.58 "cd /var/www/tmm && rm -rf tmp/cache/models/* tmp/cache/persistent/* tmp/cache/views/*"
   ```
4. **Fixed permissions**:
   ```bash
   ssh root@103.214.112.58 "chown www-data:www-data /var/www/tmm/src/Controller/CandidatesController.php"
   ```

## Verification

### Test Results:
```
✅ Homepage: HTTP 200
✅ Wizard Page: HTTP 200  
✅ Static Files: HTTP 200
```

### Test Commands:
```bash
# Test wizard page
curl -I http://103.214.112.58/tmm/candidates/wizard

# Or use PowerShell script
powershell -ExecutionPolicy Bypass -File verify_tmm_subpath.ps1
```

## Why This Matters

### Auth Component Lifecycle:
1. `CandidatesController::initialize()` is called
2. `parent::initialize()` loads AppController's `initialize()`
3. AppController loads the Auth component via `$this->loadComponent('Auth', [...])`
4. **ONLY THEN** can we call `$this->Auth->allow()`

### Public Access Actions:
The wizard needs to be publicly accessible (no login required) for candidate self-registration:
- `wizard` - Main wizard page (9-step registration)
- `getKabupaten` - AJAX endpoint for location cascade
- `getKecamatan` - AJAX endpoint for location cascade
- `getKelurahan` - AJAX endpoint for location cascade

## Status
✅ **FIXED** - All pages working correctly at http://103.214.112.58/tmm/

**Fixed By**: AI Assistant  
**Date**: November 24, 2025  
**Time**: ~05:00 UTC
