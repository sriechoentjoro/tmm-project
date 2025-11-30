#!/usr/bin/env pwsh
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Testing TMM at /tmm Subpath" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: Homepage
Write-Host "Test 1: Testing homepage..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://103.214.112.58/tmm/" -Method Head -ErrorAction Stop
    Write-Host "  [PASS] Homepage: HTTP $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  [FAIL] Homepage: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Wizard page
Write-Host "Test 2: Testing wizard page..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://103.214.112.58/tmm/candidates/wizard" -Method Head -ErrorAction Stop
    Write-Host "  [PASS] Wizard: HTTP $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  [FAIL] Wizard: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 3: Users page
Write-Host "Test 3: Testing users page..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://103.214.112.58/tmm/users" -Method Head -ErrorAction Stop
    Write-Host "  [PASS] Users: HTTP $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  [FAIL] Users: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Static asset (CSS)
Write-Host "Test 4: Testing static CSS..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://103.214.112.58/tmm/css/bootstrap.min.css" -Method Head -ErrorAction Stop
    Write-Host "  [PASS] CSS: HTTP $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  [FAIL] CSS: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Testing Complete!" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Application URL: http://103.214.112.58/tmm/" -ForegroundColor Green
Write-Host "Wizard URL: http://103.214.112.58/tmm/candidates/wizard" -ForegroundColor Green
