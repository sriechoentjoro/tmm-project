# ============================================
# Deploy NGINX Configuration to Server
# ============================================

$serverIP = "103.214.112.58"
$serverUser = "root"
$localConfig = "nginx_complete_config.conf"
$remoteConfig = "/etc/nginx/sites-available/ip-projects.conf"

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  DEPLOY NGINX CONFIGURATION" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# ===== STEP 1: Backup existing config =====
Write-Host "[STEP 1] Backing up existing configuration..." -ForegroundColor Yellow
$backupName = "ip-projects.conf.backup." + (Get-Date -Format 'yyyyMMdd_HHmmss')
ssh ${serverUser}@${serverIP} "cp $remoteConfig /etc/nginx/sites-available/$backupName"
Write-Host "  ✓ Backup created: $backupName" -ForegroundColor Green
Write-Host ""

# ===== STEP 2: Upload new config =====
Write-Host "[STEP 2] Uploading new configuration..." -ForegroundColor Yellow
scp $localConfig ${serverUser}@${serverIP}:$remoteConfig
Write-Host "  ✓ Configuration uploaded" -ForegroundColor Green
Write-Host ""

# ===== STEP 3: Remove conflicting configs =====
Write-Host "[STEP 3] Removing conflicting configurations..." -ForegroundColor Yellow
ssh ${serverUser}@${serverIP} "rm -f /etc/nginx/sites-enabled/asahifamily.*.conf"
Write-Host "  ✓ Conflicts removed" -ForegroundColor Green
Write-Host ""

# ===== STEP 4: Ensure ip-projects.conf is enabled =====
Write-Host "[STEP 4] Ensuring configuration is enabled..." -ForegroundColor Yellow
ssh ${serverUser}@${serverIP} "ln -sf /etc/nginx/sites-available/ip-projects.conf /etc/nginx/sites-enabled/"
Write-Host "  ✓ Configuration enabled" -ForegroundColor Green
Write-Host ""

# ===== STEP 5: Test NGINX configuration =====
Write-Host "[STEP 5] Testing NGINX configuration..." -ForegroundColor Yellow
$testResult = ssh ${serverUser}@${serverIP} "nginx -t 2>&1"
Write-Host $testResult

if ($testResult -match "syntax is ok" -and $testResult -match "test is successful") {
    Write-Host "  ✓ NGINX configuration is valid!" -ForegroundColor Green
    
    # ===== STEP 6: Restart NGINX =====
    Write-Host ""
    Write-Host "[STEP 6] Restarting NGINX..." -ForegroundColor Yellow
    
    ssh ${serverUser}@${serverIP} "systemctl restart nginx"
    
    Start-Sleep -Seconds 2
    
    $nginxStatus = ssh ${serverUser}@${serverIP} "systemctl status nginx | grep Active"
    Write-Host "  $nginxStatus" -ForegroundColor White
    
    Write-Host "  ✓ NGINX restarted successfully!" -ForegroundColor Green
} else {
    Write-Host "  ✗ NGINX configuration has errors!" -ForegroundColor Red
    Write-Host ""
    Write-Host "  Rolling back to backup..." -ForegroundColor Yellow
    ssh ${serverUser}@${serverIP} "cp /etc/nginx/sites-available/$backupName $remoteConfig"
    Write-Host "  ✓ Rollback completed" -ForegroundColor Green
    exit 1
}

# ===== STEP 7: Create missing folders =====
Write-Host ""
Write-Host "[STEP 7] Ensuring project folders exist..." -ForegroundColor Yellow

$folders = @(
    "/var/www/tmm",
    "/var/www/asahifamily.co.id"
)

foreach ($folder in $folders) {
    $cmd = "mkdir -p $folder/webroot; chown -R www-data:www-data $folder; chmod -R 755 $folder"
    ssh ${serverUser}@${serverIP} $cmd
    Write-Host "  ✓ $folder" -ForegroundColor Green
}

Write-Host ""

# ===== STEP 8: Summary =====
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  DEPLOYMENT COMPLETED SUCCESSFULLY!" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Access URLs:" -ForegroundColor Yellow
Write-Host ""
Write-Host "  1. phpMyAdmin" -ForegroundColor White
Write-Host "     URL: http://103.214.112.58/phpmyadmin" -ForegroundColor Cyan
Write-Host ""
Write-Host "  2. TMM Project" -ForegroundColor White
Write-Host "     URL: http://103.214.112.58/tmm" -ForegroundColor Cyan
Write-Host "     Folder: /var/www/tmm/webroot" -ForegroundColor Gray
Write-Host ""
Write-Host "  3. Asahi Family .co.id" -ForegroundColor White
Write-Host "     URL (IP): http://103.214.112.58/asahifamily.co.id" -ForegroundColor Cyan
Write-Host "     URL (Domain): http://asahifamily.co.id" -ForegroundColor Cyan
Write-Host "     Folder: /var/www/asahifamily.co.id/webroot" -ForegroundColor Gray
Write-Host ""
Write-Host "  4. Asahi Family .id" -ForegroundColor White
Write-Host "     URL (IP): http://103.214.112.58/asahifamily.id" -ForegroundColor Cyan
Write-Host "     URL (Domain): http://asahifamily.id" -ForegroundColor Cyan
Write-Host "     Folder: /var/www/asahifamily.id/webroot" -ForegroundColor Gray
Write-Host ""
Write-Host "  5. Default (HTML)" -ForegroundColor White
Write-Host "     URL: http://103.214.112.58/" -ForegroundColor Cyan
Write-Host "     Folder: /var/www/html" -ForegroundColor Gray
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  - Upload your project files to respective folders" -ForegroundColor White
Write-Host "  - Configure database in config/app_datasources.php" -ForegroundColor White
Write-Host "  - Test access via browser" -ForegroundColor White
Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
