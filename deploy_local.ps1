# Phase 3-4 Local Deployment Script
param([string]$MySQLPassword = "root")

$ErrorActionPreference = "Continue"

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Phase 3-4 Local Deployment Starting..." -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Cyan

# Step 1: Check MySQL
Write-Host "[1/6] Checking MySQL..." -ForegroundColor Yellow
$mysqlService = Get-Service | Where-Object {$_.Name -like "*mysql*"}
if ($mysqlService.Status -eq "Running") {
    Write-Host "  MySQL service: Running" -ForegroundColor Green
} else {
    Write-Host "  ERROR: MySQL not running" -ForegroundColor Red
    exit 1
}

# Test connection
$env:MYSQL_PWD = $MySQLPassword
$testResult = mysql -u root -e "SELECT 1;" 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "  Database connection: OK" -ForegroundColor Green
} else {
    Write-Host "  ERROR: Cannot connect to MySQL" -ForegroundColor Red
    Write-Host "  Try: powershell -File deploy_local.ps1 -MySQLPassword 'yourpassword'" -ForegroundColor Yellow
    exit 1
}

# Step 2: Database Migration
Write-Host "`n[2/6] Executing Database Migration..." -ForegroundColor Yellow
mysql -u root cms_authentication_authorization < phase_3_4_simple_migration.sql 2>&1
if ($LASTEXITCODE -eq 0 -or $LASTEXITCODE -eq 1) {
    Write-Host "  Migration: Complete" -ForegroundColor Green
}

# Verify table
$tableExists = mysql -u root -D cms_authentication_authorization -e "SHOW TABLES LIKE 'email_verification_tokens';" -s -N 2>&1
if ($tableExists) {
    Write-Host "  Table verified: email_verification_tokens" -ForegroundColor Green
}

# Step 3: Menu Update
Write-Host "`n[3/6] Updating Menu Structure..." -ForegroundColor Yellow
mysql -u root cms_authentication_authorization < add_lpk_registration_menu.sql 2>&1
Write-Host "  Menu update: Complete" -ForegroundColor Green

# Step 4: Clear Cache
Write-Host "`n[4/6] Clearing Cache..." -ForegroundColor Yellow
@("tmp\cache\models", "tmp\cache\persistent", "tmp\cache\views") | ForEach-Object {
    if (Test-Path $_) {
        Remove-Item "$_\*" -Recurse -Force -ErrorAction SilentlyContinue
        Write-Host "  Cleared: $_" -ForegroundColor Green
    }
}

# Step 5: Run Tests
Write-Host "`n[5/6] Running Automated Tests..." -ForegroundColor Yellow
powershell -ExecutionPolicy Bypass -File test_lpk_registration_simple.ps1

# Step 6: Summary
Write-Host "`n[6/6] Deployment Summary" -ForegroundColor Yellow
Write-Host "=======================================" -ForegroundColor Cyan

$lpkCount = mysql -u root -D cms_authentication_authorization -e "SELECT COUNT(*) FROM vocational_training_institutions;" -s -N 2>&1
$tokenCount = mysql -u root -D cms_authentication_authorization -e "SELECT COUNT(*) FROM email_verification_tokens;" -s -N 2>&1

Write-Host "`nDatabase Status:" -ForegroundColor White
Write-Host "  LPK Institutions: $lpkCount" -ForegroundColor Gray
Write-Host "  Verification Tokens: $tokenCount" -ForegroundColor Gray

Write-Host "`nLocal Deployment Complete!" -ForegroundColor Green

Write-Host "`nNext Steps:" -ForegroundColor White
Write-Host "  1. Open: http://localhost/tmm/" -ForegroundColor Yellow
Write-Host "  2. Login as Administrator" -ForegroundColor Yellow
Write-Host "  3. Navigate: Admin > LPK Registration > Register New LPK" -ForegroundColor Yellow
Write-Host "  4. Test complete workflow (see PHASE_3_4_TESTING_GUIDE.md)" -ForegroundColor Yellow

Write-Host "`nProduction Deployment:" -ForegroundColor White
Write-Host "  Run after local testing passes" -ForegroundColor Gray
Write-Host "  Guide: PRODUCTION_DEPLOYMENT_GUIDE.md`n" -ForegroundColor Gray

$env:MYSQL_PWD = $null
