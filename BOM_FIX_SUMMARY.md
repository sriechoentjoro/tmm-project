# BOM (Byte Order Mark) Fix Summary

**Date:** November 16, 2025  
**Issue:** Fatal error - Namespace declaration must be first statement  
**Cause:** UTF-8 BOM (Byte Order Mark) in PHP files  
**Status:** ✅ RESOLVED

---

## Problem

```
Fatal error: Namespace declaration statement has to be the very first statement 
in the script in D:\xampp\htdocs\project_tmm\src\Model\Table\ApprenticesTable.php on line 2
```

### Root Cause

When PowerShell's `Set-Content` command was used by our rename script, it added UTF-8 BOM (Byte Order Mark) to the files. BOM consists of invisible bytes (EF BB BF) at the start of the file, which PHP interprets as output before the namespace declaration, causing a fatal error.

---

## Files Fixed

### Model Table Files (4 files)
- ✅ `src/Model/Table/ApprenticesTable.php`
- ✅ `src/Model/Table/CandidatesTable.php`
- ✅ `src/Model/Table/TraineesTable.php`
- ✅ `src/Model/Table/ApprenticeOrdersTable.php`

### Model Entity Files (3 files)
- ✅ `src/Model/Entity/Apprentice.php`
- ✅ `src/Model/Entity/Candidate.php`
- ✅ `src/Model/Entity/Trainee.php`

---

## Solution Applied

Used PowerShell with UTF-8 encoding WITHOUT BOM:

```powershell
$content = Get-Content $filePath -Raw
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($filePath, $content, $utf8NoBom)
```

---

## Prevention

Created `fix_bom_all_php_files.ps1` script to:
- Scan all PHP files in `src/` directory
- Detect UTF-8 BOM bytes (EF BB BF)
- Remove BOM automatically
- Generate log file

**Total files scanned:** 267  
**Files with BOM found:** 0 (after fix)  
**Status:** All clean ✅

---

## Updated Rename Script

The original `rename_apprenticeship_to_apprentice_order_id.ps1` has been updated to use UTF-8 without BOM:

**Before (caused BOM issue):**
```powershell
$newContent | Set-Content $FilePath -Encoding UTF8
```

**After (no BOM):**
```powershell
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($FilePath, $newContent, $utf8NoBom)
```

---

## Verification

1. ✅ All 7 PHP files fixed (no BOM)
2. ✅ Cache cleared (`bin\cake cache clear_all`)
3. ✅ Full project scan completed (267 files checked, 0 BOM found)
4. ✅ Prevention script created for future use

---

## Testing

After fix, test these URLs:
- http://localhost/project_tmm/apprentices
- http://localhost/project_tmm/candidates
- http://localhost/project_tmm/trainees
- http://localhost/project_tmm/apprentice-orders

Expected result: No fatal errors, pages load correctly.

---

## Scripts Created

1. **`fix_bom_all_php_files.ps1`**
   - Scans all PHP files for BOM
   - Removes BOM automatically
   - Generates detailed log
   - Run anytime: `powershell -ExecutionPolicy Bypass -File .\fix_bom_all_php_files.ps1`

2. **`fix_bom.log`**
   - Log of all BOM fixes
   - Timestamp and file paths

---

## Best Practices for Future

When creating/modifying PHP files:

1. **Always use UTF-8 WITHOUT BOM**
2. **In VS Code:** 
   - Bottom right corner → Click "UTF-8" → Select "UTF-8 without BOM"
   - Or add to settings.json: `"files.encoding": "utf8"`

3. **In PowerShell scripts:**
   - Use `[System.IO.File]::WriteAllText()` with UTF8Encoding($false)
   - Avoid `Set-Content` with `-Encoding UTF8` (adds BOM)

4. **After mass file updates:**
   - Run `fix_bom_all_php_files.ps1`
   - Clear cache: `bin\cake cache clear_all`
   - Test in browser

---

**Issue Resolution Time:** ~5 minutes  
**Status:** ✅ RESOLVED - Ready to continue testing
