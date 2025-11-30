# Master Post-Bake Fix Script
# Run this after baking any tables to apply all necessary fixes

Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║         Post-Bake Fix - Asahi Inventory System            ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

$startTime = Get-Date

# 1. Fix cross-database associations
Write-Host "[1/4] Fixing cross-database associations..." -ForegroundColor Yellow
if (Test-Path "fix_all_cross_db_associations.ps1") {
    & ".\fix_all_cross_db_associations.ps1"
} else {
    Write-Host "  Warning: fix_all_cross_db_associations.ps1 not found" -ForegroundColor Red
}

Write-Host ""

# 2. Fix approval table connections
Write-Host "[2/4] Fixing approval table connections..." -ForegroundColor Yellow
if (Test-Path "fix_approval_connections.ps1") {
    & ".\fix_approval_connections.ps1"
} else {
    Write-Host "  Warning: fix_approval_connections.ps1 not found" -ForegroundColor Red
}

Write-Host ""

# 3. Fix approval association aliases
Write-Host "[3/5] Fixing approval association aliases..." -ForegroundColor Yellow
if (Test-Path "fix_approval_aliases.ps1") {
    & ".\fix_approval_aliases.ps1"
} else {
    Write-Host "  Warning: fix_approval_aliases.ps1 not found" -ForegroundColor Red
}

Write-Host ""

# 4. Fix ALL personnel alias associations (auto-detect)
Write-Host "[4/5] Fixing personnel alias associations..." -ForegroundColor Yellow
if (Test-Path "fix_personnel_aliases_simple.ps1") {
    & ".\fix_personnel_aliases_simple.ps1"
} else {
    Write-Host "  Warning: fix_personnel_aliases_simple.ps1 not found" -ForegroundColor Red
}

Write-Host ""

# 5. Clear all caches
Write-Host "[5/5] Clearing caches..." -ForegroundColor Yellow
& "bin\cake" cache clear_all

Write-Host ""

$endTime = Get-Date
$duration = $endTime - $startTime

Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║                    All Fixes Applied!                      ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
Write-Host "  Duration: $($duration.TotalSeconds) seconds" -ForegroundColor Gray
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "  1. Test the baked controllers in browser" -ForegroundColor White
Write-Host "  2. Hard reload (Ctrl+Shift+R) to clear browser cache" -ForegroundColor White
Write-Host "  3. Check error.log if any issues occur" -ForegroundColor White
Write-Host ""
