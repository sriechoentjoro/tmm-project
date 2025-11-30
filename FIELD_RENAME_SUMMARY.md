# Field Rename Summary: apprenticeship_order_id → apprentice_order_id

**Date:** November 16, 2025  
**Status:** ✅ COMPLETED

---

## Overview

This document summarizes the complete field rename from `apprenticeship_order_id` to `apprentice_order_id` throughout the project.

## Changes Made

### 1. Database Schema Changes (SQL)

**File:** `rename_apprenticeship_order_id_in_database.sql`

Tables updated:
- ✅ `cms_lpk_candidates.candidates` - Column renamed
- ✅ `cms_tmm_trainees.trainees` - Column renamed  
- ✅ `cms_tmm_apprentices.apprentices` - Column renamed

**SQL Commands:**
```sql
-- candidates table
ALTER TABLE candidates 
CHANGE COLUMN apprenticeship_order_id apprentice_order_id INT(11) NULL DEFAULT NULL;

-- trainees table
ALTER TABLE trainees 
CHANGE COLUMN apprenticeship_order_id apprentice_order_id INT(11) NULL DEFAULT NULL;

-- apprentices table
ALTER TABLE apprentices 
CHANGE COLUMN apprenticeship_order_id apprentice_order_id INT(11) NULL DEFAULT NULL;
```

### 2. Model Files Updated

#### Table Classes (4 files)
- ✅ `src/Model/Table/CandidatesTable.php`
  - Foreign key: `'foreignKey' => 'apprentice_order_id'`
  - Validation: `existsIn(['apprentice_order_id'], 'ApprenticeOrders')`

- ✅ `src/Model/Table/TraineesTable.php`
  - Foreign key: `'foreignKey' => 'apprentice_order_id'`
  - Validation: `existsIn(['apprentice_order_id'], 'ApprenticeOrders')`

- ✅ `src/Model/Table/ApprenticesTable.php`
  - Foreign key: `'foreignKey' => 'apprentice_order_id'`
  - Validation: `existsIn(['apprentice_order_id'], 'ApprenticeOrders')`

- ✅ `src/Model/Table/ApprenticeOrdersTable.php`
  - Foreign key: `'foreignKey' => 'apprentice_order_id'`

#### Entity Classes (3 files)
- ✅ `src/Model/Entity/Candidate.php`
  - Property: `@property int|null $apprentice_order_id`
  - Accessible: `'apprentice_order_id' => true`

- ✅ `src/Model/Entity/Trainee.php`
  - Property: `@property int|null $apprentice_order_id`
  - Accessible: `'apprentice_order_id' => true`

- ✅ `src/Model/Entity/Apprentice.php`
  - Property: `@property int|null $apprentice_order_id`
  - Accessible: `'apprentice_order_id' => true`

### 3. Template Files Updated (13 files)

#### Candidates Templates
- ✅ `src/Template/Candidates/add.ctp`
- ✅ `src/Template/Candidates/edit.ctp`
- ✅ `src/Template/Candidates/index.ctp`

#### Trainees Templates
- ✅ `src/Template/Trainees/add.ctp`
- ✅ `src/Template/Trainees/edit.ctp`
- ✅ `src/Template/Trainees/index.ctp`
- ✅ `src/Template/Trainees/view.ctp`

#### Apprentices Templates
- ✅ `src/Template/Apprentices/add.ctp`
- ✅ `src/Template/Apprentices/edit.ctp`
- ✅ `src/Template/Apprentices/index.ctp`
- ✅ `src/Template/Apprentices/view.ctp`

#### Other Templates
- ✅ `src/Template/ApprenticeOrders/view.ctp`
- ✅ `src/Template/MasterCandidateInterviewResults/view.ctp`

### 4. Files with "Apprenticeship" in Name

**Result:** ✅ No files found with "Apprenticeship" in their filename

### 5. Cache Cleared

✅ All CakePHP caches cleared:
- default cache
- _cake_core_ cache
- _cake_model_ cache
- _cake_routes_ cache

---

## Automation Scripts Created

1. **PowerShell Script:** `rename_apprenticeship_to_apprentice_order_id.ps1`
   - Automatically updates all PHP files
   - Searches for files with "Apprenticeship" in name
   - Generates detailed log file

2. **SQL Script:** `rename_apprenticeship_order_id_in_database.sql`
   - Updates all database tables
   - Includes verification queries
   - Ready to run in phpMyAdmin

3. **Log File:** `rename_apprenticeship_order_id.log`
   - Complete log of all changes made
   - Timestamp and file paths recorded

---

## Testing Checklist

### Database Testing
- [ ] Run SQL script in phpMyAdmin
- [ ] Verify column names changed in all 3 tables
- [ ] Check foreign key constraints are intact
- [ ] Test data integrity

### Application Testing
- [ ] Candidates add/edit forms work correctly
- [ ] Trainees add/edit forms work correctly
- [ ] Apprentices add/edit forms work correctly
- [ ] Index pages display correct data
- [ ] View pages show association links
- [ ] Filter/search functionality works
- [ ] No console errors in browser
- [ ] Association tabs load correctly

### Code Verification
- [ ] No references to `apprenticeship_order_id` remain
- [ ] All references use `apprentice_order_id`
- [ ] No files with "Apprenticeship" in name exist
- [ ] Cache cleared successfully

---

## Rollback Plan (If Needed)

If issues occur, rollback using these steps:

1. **Database Rollback:**
   ```sql
   ALTER TABLE candidates CHANGE COLUMN apprentice_order_id apprenticeship_order_id INT(11);
   ALTER TABLE trainees CHANGE COLUMN apprentice_order_id apprenticeship_order_id INT(11);
   ALTER TABLE apprentices CHANGE COLUMN apprentice_order_id apprenticeship_order_id INT(11);
   ```

2. **Code Rollback:**
   - Restore from Git: `git checkout -- .`
   - Or use backup if available

3. **Clear Cache:**
   ```bash
   bin\cake cache clear_all
   ```

---

## Next Steps

1. ✅ **Run SQL script** in phpMyAdmin:
   - File: `rename_apprenticeship_order_id_in_database.sql`
   - Execute in each database (cms_lpk_candidates, cms_tmm_trainees, cms_tmm_apprentices)

2. ✅ **Test in browser:**
   - Navigate to Candidates pages
   - Navigate to Trainees pages
   - Navigate to Apprentices pages
   - Navigate to ApprenticeOrders pages
   - Verify all CRUD operations work

3. ✅ **Verify associations:**
   - Check belongsTo associations load correctly
   - Check hasMany associations display in tabs
   - Verify sorting and filtering work

4. ✅ **Update documentation:**
   - Update DATABASE_MAPPING_REFERENCE.md if needed
   - Update any API documentation
   - Update developer guides

---

## Related Documents

- `DATABASE_MAPPING_REFERENCE.md` - Database connection mapping
- `.github/copilot-instructions.md` - Development guidelines
- `ASSOCIATION_DISPLAY_GUIDE.md` - Association display patterns
- `rename_apprenticeship_order_id.log` - Detailed change log

---

## Contact

For questions or issues related to this change, refer to:
- This summary document
- Log file: `rename_apprenticeship_order_id.log`
- Project documentation in `.github/` folder

---

**End of Summary**
