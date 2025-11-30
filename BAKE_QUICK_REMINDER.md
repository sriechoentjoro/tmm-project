# 🎯 BAKE QUICK REMINDER
**Print this and keep it visible while baking!**

---

## ⚠️ BEFORE EVERY BAKE

1. **Open MASTER_BAKE_GUIDE.md** - It's the single source of truth
2. **Check connection** - Which database does this table belong to?
3. **Verify table location** - Is it already in the database?

---

## 🚀 STANDARD BAKE WORKFLOW

```powershell
# Step 1: Bake with correct connection
bin\cake bake all TableName --connection <connection> --force

# Step 2: IMMEDIATELY run automation (THIS IS CRITICAL!)
powershell -ExecutionPolicy Bypass -File post_bake_fix.ps1

# Step 3: Test in browser
# http://localhost/asahi_v3/table-names

# Step 4: Hard reload (Ctrl+Shift+R)
```

---

## 📊 CONNECTION QUICK REFERENCE

| Connection | Database | Common Tables |
|------------|----------|---------------|
| `default` | asahi_inventories | Inventories, Storages, PurchaseReceipts, StockOutgoings |
| `personnel` | asahi_common_personnels | Companies, Departments, Positions |
| `common` | asahi_commons | **Personnels**, Users, Sections, Suppliers |
| `maintenance` | asahi_maintenance | MaintenanceCards, PlannedJobs, Actions |
| `vehicle` | asahi_vehicles | Vehicles, Drivers, DeliveryOrders |
| `approvals` | asahi_online_approvals | Approvals, ApprovalWorkflows |
| `district` | asahi_common_districts | Propinsis, Kabupatens, Kecamatans |

---

## ⭐ CRITICAL RULES (MEMORIZE THESE!)

### Rule 1: No Table Duplication!
- ❌ **NEVER** create `personnels` in multiple databases
- ✅ **ALWAYS** keep each table in ONE database only
- ✅ Use `'strategy' => 'select'` for cross-database references

### Rule 2: Personnels Location
- ⚠️ `personnels` table is in **`common`** database
- ⚠️ NOT in `personnel` database (common mistake!)
- ⚠️ NOT in `default` database

### Rule 3: Always Run Automation
- ✅ `post_bake_fix.ps1` runs **5 critical scripts**:
  1. fix_all_cross_db_associations.ps1 (adds 'strategy' => 'select')
  2. fix_approval_connections.ps1
  3. fix_approval_aliases.ps1
  4. fix_personnel_aliases_simple.ps1 (adds 'className')
  5. Clear all caches

### Rule 4: Test Immediately
- ✅ Browse to the baked controller
- ✅ Hard reload (Ctrl+Shift+R) to clear cache
- ✅ Check for "Table not found" errors
- ✅ Verify associations load correctly

---

## 🔧 COMMON ERRORS & QUICK FIXES

### Error: "Table 'asahi_inventories.personnels' doesn't exist"

**Full Error:**
```
Error: SQLSTATE[42S02]: Base table or view not found: 1146 
Table 'asahi_inventories.personnels' doesn't exist

If you are using SQL keywords as table column names, you can enable 
identifier quoting for your database connection in config/app.php.
```

**Fix:**
```powershell
# Run automation (adds 'strategy' => 'select')
powershell -ExecutionPolicy Bypass -File fix_all_cross_db_associations.ps1

# OR run complete post-bake workflow
powershell -ExecutionPolicy Bypass -File post_bake_fix.ps1
```

**Why:** `personnels` table is in `common` database (NOT `default`). Needs `'strategy' => 'select'` for cross-database reference.

**Note:** The "enable identifier quoting" message is misleading - real issue is cross-DB association.

### Error: "Table 'asahi_inventories.approved_by_personnels' doesn't exist"
```powershell
# Fix: Run personnel alias fix (adds 'className' => 'Personnels')
powershell -ExecutionPolicy Bypass -File fix_personnel_aliases_simple.ps1
```

### Templates not updating after bake
```powershell
# Fix: Clear all caches
bin\cake cache clear_all

# Then hard reload browser (Ctrl+Shift+R)
```

---

## 📖 DETAILED DOCUMENTATION

**For complete information, ALWAYS refer to:**
- **MASTER_BAKE_GUIDE.md** - Complete guide (1800+ lines)
  - Section 1: Quick Start Workflow
  - Section 2: Multi-Database Architecture
  - Section 3: Bake Template Reference
  - Section 4: Post-Bake Automation Scripts
  - Section 5: **Cross-Database Reference Principle** ⭐
  - Section 6: Common Issues & Solutions
  - Section 12: FAQ (CakePHP 2.x vs 3.x)

---

## ✅ POST-BAKE CHECKLIST

After running `post_bake_fix.ps1`, verify:

- [ ] Table file has correct connection: `ConnectionManager::get('<connection>')`
- [ ] Cross-DB associations have `'strategy' => 'select'`
- [ ] Personnel aliases have `'className' => 'Personnels'`
- [ ] Index template has purple gradient thead (matches button colors)
- [ ] Index template has Export buttons (CSV/Excel/PDF/Print) with btn-export-light
- [ ] Action column sticky (position: sticky; left: 0) - stays visible when scrolling
- [ ] Action column transparent (no borders, invisible background)
- [ ] Action column background matches row stripe colors (odd/even)
- [ ] Action buttons hidden by default, appear on row hover (opacity transition)
- [ ] Sticky column has 2px shadow separator
- [ ] Buttons use btn-action-icon class (dark background, orange/blue hover)
- [ ] Delete button NOT present on index pages (safer UI)
- [ ] Hover works with AJAX-generated table rows
- [ ] Z-index: header (10), body (5) for proper stacking
- [ ] View template has GitHub-style tabs
- [ ] Forms have btn-export-light buttons
- [ ] Controller has export methods (exportCsv, exportExcel, exportPdf)
- [ ] Browser shows no errors after hard reload
- [ ] Color consistency: thead uses same purple gradient as buttons (15% opacity)

---

## 🎯 MANTRA

**"One table, one database, cross-reference with strategy."**

**Never duplicate. Always automate. Test immediately.**

---

**Last Updated:** November 11, 2025  
**See:** MASTER_BAKE_GUIDE.md for complete documentation
