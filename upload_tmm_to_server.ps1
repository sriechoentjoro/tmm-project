# ============================================
# Upload TMM Project to Production Server
# ============================================

$serverIP = "103.214.112.58"
$serverUser = "root"
$localPath = "D:\xampp\htdocs\project_tmm"
$remotePath = "/var/www/tmm"

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  UPLOAD TMM PROJECT TO SERVER" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Create directories
Write-Host "[STEP 1] Creating directories..." -ForegroundColor Yellow
ssh ${serverUser}@${serverIP} "mkdir -p $remotePath/src $remotePath/config $remotePath/webroot $remotePath/tmp $remotePath/logs $remotePath/bin $remotePath/vendor $remotePath/webroot/img/uploads $remotePath/webroot/files/uploads"
Write-Host "  Done" -ForegroundColor Green
Write-Host ""

# Upload folders
Write-Host "[STEP 2] Uploading project files..." -ForegroundColor Yellow

Write-Host "  Uploading: src/" -ForegroundColor White
scp -r "$localPath\src" "${serverUser}@${serverIP}:${remotePath}/"

Write-Host "  Uploading: config/" -ForegroundColor White
scp -r "$localPath\config" "${serverUser}@${serverIP}:${remotePath}/"

Write-Host "  Uploading: webroot/" -ForegroundColor White
scp -r "$localPath\webroot" "${serverUser}@${serverIP}:${remotePath}/"

Write-Host "  Uploading: bin/" -ForegroundColor White
scp -r "$localPath\bin" "${serverUser}@${serverIP}:${remotePath}/"

if (Test-Path "$localPath\plugins") {
    Write-Host "  Uploading: plugins/" -ForegroundColor White
    scp -r "$localPath\plugins" "${serverUser}@${serverIP}:${remotePath}/"
}

Write-Host "  Uploading root files..." -ForegroundColor White
scp "$localPath\composer.json" "${serverUser}@${serverIP}:${remotePath}/"
if (Test-Path "$localPath\composer.lock") {
    scp "$localPath\composer.lock" "${serverUser}@${serverIP}:${remotePath}/"
}
if (Test-Path "$localPath\.htaccess") {
    scp "$localPath\.htaccess" "${serverUser}@${serverIP}:${remotePath}/"
}
if (Test-Path "$localPath\index.php") {
    scp "$localPath\index.php" "${serverUser}@${serverIP}:${remotePath}/"
}

Write-Host "  Done" -ForegroundColor Green
Write-Host ""

# Set permissions
Write-Host "[STEP 3] Setting permissions..." -ForegroundColor Yellow
ssh ${serverUser}@${serverIP} "chown -R www-data:www-data $remotePath"
ssh ${serverUser}@${serverIP} "chmod -R 755 $remotePath"
ssh ${serverUser}@${serverIP} "chmod -R 777 $remotePath/tmp"
ssh ${serverUser}@${serverIP} "chmod -R 777 $remotePath/logs"
ssh ${serverUser}@${serverIP} "chmod -R 777 $remotePath/webroot/img/uploads"
ssh ${serverUser}@${serverIP} "chmod -R 777 $remotePath/webroot/files/uploads"
Write-Host "  Done" -ForegroundColor Green
Write-Host ""

# Clear cache
Write-Host "[STEP 4] Clearing cache..." -ForegroundColor Yellow
ssh ${serverUser}@${serverIP} "rm -rf $remotePath/tmp/cache/models/*"
ssh ${serverUser}@${serverIP} "rm -rf $remotePath/tmp/cache/persistent/*"
ssh ${serverUser}@${serverIP} "rm -rf $remotePath/tmp/cache/views/*"
Write-Host "  Done" -ForegroundColor Green
Write-Host ""

# Verify
Write-Host "[STEP 5] Verifying upload..." -ForegroundColor Yellow
$fileCount = ssh ${serverUser}@${serverIP} "find $remotePath -type f | wc -l"
Write-Host "  Total files: $fileCount" -ForegroundColor White
Write-Host ""

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  UPLOAD COMPLETED!" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Access URL: http://103.214.112.58/tmm" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  1. Check database config: /var/www/tmm/config/app_datasources.php" -ForegroundColor White
Write-Host "  2. Install composer: ssh root@103.214.112.58 'cd /var/www/tmm; composer install'" -ForegroundColor White
Write-Host "  3. Test in browser: http://103.214.112.58/tmm" -ForegroundColor White
Write-Host ""
