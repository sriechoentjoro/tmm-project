#!/usr/bin/env pwsh
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "TMM SUBPATH CONFIGURATION COMPLETE" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

Write-Host "Application URL:" -ForegroundColor Cyan
Write-Host "  http://103.214.112.58/tmm/" -ForegroundColor White
Write-Host ""

Write-Host "Wizard URL:" -ForegroundColor Cyan
Write-Host "  http://103.214.112.58/tmm/candidates/wizard" -ForegroundColor White
Write-Host ""

Write-Host "Status Verification:" -ForegroundColor Cyan
try {
    $r1 = Invoke-WebRequest -Uri "http://103.214.112.58/tmm/" -Method Head -ErrorAction Stop
    Write-Host "  [OK] Homepage: HTTP $($r1.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  [FAIL] Homepage: Failed" -ForegroundColor Red
}

try {
    $r2 = Invoke-WebRequest -Uri "http://103.214.112.58/tmm/candidates/wizard" -Method Head -ErrorAction Stop
    Write-Host "  [OK] Wizard: HTTP $($r2.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  [FAIL] Wizard: Failed" -ForegroundColor Red
}

try {
    $r3 = Invoke-WebRequest -Uri "http://103.214.112.58/tmm/css/style.css" -Method Head -ErrorAction Stop
    Write-Host "  [OK] Static Files: HTTP $($r3.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  [FAIL] Static Files: Failed" -ForegroundColor Red
}

Write-Host ""
Write-Host "Configuration Details:" -ForegroundColor Cyan
Write-Host "  Nginx Config: /etc/nginx/sites-available/tmm" -ForegroundColor Gray
Write-Host "  CakePHP Base: /tmm" -ForegroundColor Gray
Write-Host "  Document Root: /var/www/tmm/webroot" -ForegroundColor Gray
Write-Host ""
