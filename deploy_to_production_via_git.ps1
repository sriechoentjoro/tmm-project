# Automated Git Deploy Script to Production Server
# Created: December 3, 2025

$ErrorActionPreference = "Stop"

$serverIP = "103.214.112.58"
$serverUser = "root"
$projectPath = "/var/www/tmm"
$branch = "main"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Git Deploy to Production Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Commit and Push Local Changes
Write-Host "[1/5] Checking local changes..." -ForegroundColor Yellow

$gitStatus = git status --porcelain
if ($gitStatus) {
    Write-Host "Found uncommitted changes. Committing..." -ForegroundColor Yellow
    
    # Show changes
    git status --short
    Write-Host ""
    
    # Ask for commit message
    $commitMsg = Read-Host "Enter commit message (or press Enter for default)"
    if ([string]::IsNullOrWhiteSpace($commitMsg)) {
        $commitMsg = "Update: $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
    }
    
    # Commit and push
    git add .
    git commit -m $commitMsg
    git push origin $branch
    
    Write-Host "OK Changes committed and pushed to GitHub!" -ForegroundColor Green
} else {
    Write-Host "OK No local changes to commit" -ForegroundColor Green
}
Write-Host ""

# Step 2: Pull on Production Server
Write-Host "[2/5] Pulling from GitHub on production server..." -ForegroundColor Yellow

$pullScript = @"
cd $projectPath
echo '=== Current branch ==='
git branch
echo ''
echo '=== Pulling from GitHub ==='
git pull origin $branch
echo ''
echo 'OK Pull completed!'
"@

ssh ${serverUser}@${serverIP} "$pullScript"
Write-Host ""

# Step 3: Set Permissions
Write-Host "[3/5] Setting file permissions..." -ForegroundColor Yellow

$permScript = @"
cd $projectPath
chown -R www-data:www-data tmp/ logs/ webroot/img/uploads/ webroot/files/uploads/
chmod -R 775 tmp/cache/ logs/ webroot/img/uploads/ webroot/files/uploads/
echo 'OK Permissions set!'
"@

ssh ${serverUser}@${serverIP} "$permScript"
Write-Host ""

# Step 4: Clear Cache
Write-Host "[4/5] Clearing cache..." -ForegroundColor Yellow

$cacheScript = @"
cd $projectPath
rm -rf tmp/cache/*
echo 'OK Cache cleared!'
"@

ssh ${serverUser}@${serverIP} "$cacheScript"
Write-Host ""

# Step 5: Restart Services
Write-Host "[5/5] Restarting services..." -ForegroundColor Yellow

$restartScript = @"
systemctl restart php7.4-fpm
systemctl reload nginx
echo 'OK Services restarted!'
"@

ssh ${serverUser}@${serverIP} "$restartScript"
Write-Host ""

# Verify Deployment
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Deployment Verification" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

$verifyScript = @"
cd $projectPath
echo 'Current commit:'
git log -1 --oneline
echo ''
echo 'Service status:'
systemctl is-active php7.4-fpm
systemctl is-active nginx
"@

ssh ${serverUser}@${serverIP} "$verifyScript"
Write-Host ""

Write-Host "========================================" -ForegroundColor Green
Write-Host "Deployment Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Test the deployment at:" -ForegroundColor Cyan
Write-Host "  http://103.214.112.58/tmm/" -ForegroundColor White
Write-Host "  https://asahifamily.id/tmm/" -ForegroundColor White
Write-Host ""
