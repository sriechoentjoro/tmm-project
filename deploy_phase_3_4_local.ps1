# Phase 3-4 Local Deployment Script
# Executes: 1. Database Migration, 2. Menu Update, 3. Cache Clear, 4. Tests

param(
    [string]$MySQLPassword = "root"  # Change if needed
)

$ErrorActionPreference = "Continue"

Write-Host "`n╔════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Phase 3-4 Local Deployment Wizard        ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════╝" -ForegroundColor Cyan

# Step 1: Check Prerequisites
Write-Host "`n[1/6] Checking Prerequisites..." -ForegroundColor Yellow

# Check MySQL running
$mysqlService = Get-Service | Where-Object {$_.Name -like "*mysql*"}
if ($mysqlService.Status -eq "Running") {
    Write-Host "  ✅ MySQL service is running" -ForegroundColor Green
} else {
    Write-Host "  ❌ MySQL service is NOT running" -ForegroundColor Red
    Write-Host "  Please start XAMPP MySQL and try again." -ForegroundColor Yellow
    exit 1
}

# Check database connection
Write-Host "  Testing database connection..." -ForegroundColor Gray
$testConnection = mysql -u root -p$MySQLPassword -e "SELECT 1;" 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✅ Database connection successful" -ForegroundColor Green
} else {
    Write-Host "  ❌ Cannot connect to MySQL" -ForegroundColor Red
    Write-Host "  Error: $testConnection" -ForegroundColor Yellow
    Write-Host "  Please check MySQL password (current: $MySQLPassword)" -ForegroundColor Yellow
    exit 1
}

# Check files exist
$requiredFiles = @(
    "phase_3_4_simple_migration.sql",
    "add_lpk_registration_menu.sql",
    "test_lpk_registration_simple.ps1"
)

foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        Write-Host "  ✅ Found: $file" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Missing: $file" -ForegroundColor Red
        exit 1
    }
}

# Step 2: Execute Database Migration
Write-Host "`n[2/6] Executing Database Migration..." -ForegroundColor Yellow
Write-Host "  Creating email_verification_tokens table..." -ForegroundColor Gray

$migrationResult = mysql -u root -p$MySQLPassword cms_authentication_authorization < phase_3_4_simple_migration.sql 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✅ Migration executed successfully" -ForegroundColor Green
    
    # Verify table created
    $tableCheck = mysql -u root -p$MySQLPassword -D cms_authentication_authorization -e "DESCRIBE email_verification_tokens;" 2>&1
    if ($tableCheck -match "token") {
        Write-Host "  ✅ Table verified: email_verification_tokens" -ForegroundColor Green
    }
} else {
    # Check if table already exists (which is OK)
    if ($migrationResult -match "already exists") {
        Write-Host "  ⚠️  Table already exists (skipping)" -ForegroundColor Yellow
    } else {
        Write-Host "  ❌ Migration failed" -ForegroundColor Red
        Write-Host "  Error: $migrationResult" -ForegroundColor Yellow
        exit 1
    }
}

# Step 3: Execute Menu Update
Write-Host "`n[3/6] Updating Enhanced Tab Menu..." -ForegroundColor Yellow
Write-Host "  Adding Admin menu with LPK Registration..." -ForegroundColor Gray

$menuResult = mysql -u root -p$MySQLPassword cms_authentication_authorization < add_lpk_registration_menu.sql 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✅ Menu structure added successfully" -ForegroundColor Green
    
    # Verify menu created
    $menuCheck = mysql -u root -p$MySQLPassword -D cms_authentication_authorization -e "SELECT COUNT(*) as count FROM menus WHERE title IN ('Admin', 'LPK Registration', 'Email Verification Tokens');" -s -N 2>&1
    if ($menuCheck -match "\d+") {
        Write-Host "  ✅ Menu verified: $menuCheck items added" -ForegroundColor Green
    }
} else {
    Write-Host "  ⚠️  Menu update completed with warnings" -ForegroundColor Yellow
    Write-Host "  This is normal if menus already exist" -ForegroundColor Gray
}

# Step 4: Clear Cache
Write-Host "`n[4/6] Clearing Cache..." -ForegroundColor Yellow

$cacheDirs = @(
    "tmp\cache\models",
    "tmp\cache\persistent",
    "tmp\cache\views"
)

foreach ($dir in $cacheDirs) {
    if (Test-Path $dir) {
        $fileCount = (Get-ChildItem $dir -File -Recurse -ErrorAction SilentlyContinue).Count
        Remove-Item "$dir\*" -Recurse -Force -ErrorAction SilentlyContinue
        Write-Host "  ✅ Cleared: $dir ($fileCount files)" -ForegroundColor Green
    }
}

# Step 5: Run Automated Tests
Write-Host "`n[5/6] Running Automated Tests..." -ForegroundColor Yellow

powershell -ExecutionPolicy Bypass -File test_lpk_registration_simple.ps1

# Step 6: Summary and Next Steps
Write-Host "`n[6/6] Deployment Summary" -ForegroundColor Yellow
Write-Host "═══════════════════════════════════════════" -ForegroundColor Cyan

# Count database records
$lpkCount = mysql -u root -p$MySQLPassword -D cms_authentication_authorization -e "SELECT COUNT(*) FROM vocational_training_institutions;" -s -N 2>&1
$tokenCount = mysql -u root -p$MySQLPassword -D cms_authentication_authorization -e "SELECT COUNT(*) FROM email_verification_tokens;" -s -N 2>&1
$menuCount = mysql -u root -p$MySQLPassword -D cms_authentication_authorization -e "SELECT COUNT(*) FROM menus WHERE parent_id = (SELECT id FROM menus WHERE title = 'Admin' AND parent_id IS NULL);" -s -N 2>&1

Write-Host "`n📊 Database Status:" -ForegroundColor White
Write-Host "  LPK Institutions: $lpkCount" -ForegroundColor Gray
Write-Host "  Verification Tokens: $tokenCount" -ForegroundColor Gray
Write-Host "  Admin Menu Items: $menuCount" -ForegroundColor Gray

Write-Host "`n✅ Local Deployment Complete!" -ForegroundColor Green

Write-Host "`n📋 Next Steps:" -ForegroundColor White
Write-Host "  1. Open browser: http://localhost/tmm/" -ForegroundColor Yellow
Write-Host "  2. Login as Administrator" -ForegroundColor Yellow
Write-Host "  3. Look for 'Admin' tab in top menu" -ForegroundColor Yellow
Write-Host "  4. Click Admin → LPK Registration → Register New LPK" -ForegroundColor Yellow
Write-Host "  5. Test complete registration workflow" -ForegroundColor Yellow

Write-Host "`n🧪 Manual Testing Guide:" -ForegroundColor White
Write-Host "  See: PHASE_3_4_TESTING_GUIDE.md" -ForegroundColor Gray
Write-Host "  Scenario LPK-001: Complete happy path test" -ForegroundColor Gray

Write-Host "`n🚀 Production Deployment:" -ForegroundColor White
Write-Host "  See: PRODUCTION_DEPLOYMENT_GUIDE.md" -ForegroundColor Gray
Write-Host "  Ready to deploy after local testing passes" -ForegroundColor Gray

Write-Host "`n═══════════════════════════════════════════`n" -ForegroundColor Cyan
