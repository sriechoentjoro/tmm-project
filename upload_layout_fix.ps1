# Upload fixed layout file and UsersController
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Uploading Fixed Files..." -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Upload UsersController
Write-Host "1. Uploading UsersController.php..." -ForegroundColor Yellow
Get-Content "src\Controller\UsersController.php" | ssh root@103.214.112.58 "cat > /var/www/tmm/src/Controller/UsersController.php"

if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ UsersController uploaded!" -ForegroundColor Green
} else {
    Write-Host "   ✗ UsersController upload failed!" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Upload Layout
Write-Host "2. Uploading default.ctp..." -ForegroundColor Yellow
Get-Content "src\Template\Layout\default.ctp" | ssh root@103.214.112.58 "cat > /var/www/tmm/src/Template/Layout/default.ctp"

if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Layout uploaded!" -ForegroundColor Green
} else {
    Write-Host "   ✗ Layout upload failed!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "3. Clearing cache..." -ForegroundColor Yellow

ssh root@103.214.112.58 "cd /var/www/tmm && bin/cake cache clear_all"

if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Cache cleared!" -ForegroundColor Green
    Write-Host ""
    Write-Host "================================================" -ForegroundColor Green
    Write-Host "ALL FILES UPLOADED SUCCESSFULLY!" -ForegroundColor Green
    Write-Host "================================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Test now with Ctrl+Shift+R!" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Fixed Issues:" -ForegroundColor Yellow
    Write-Host "  ✓ URL double path fixed (hardcoded <a href>)" -ForegroundColor White
    Write-Host "  ✓ Profile 500 error fixed (removed invalid association)" -ForegroundColor White
} else {
    Write-Host "   ✗ Cache clear failed!" -ForegroundColor Red
}
