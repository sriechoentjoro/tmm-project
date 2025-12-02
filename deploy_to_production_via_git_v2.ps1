# TMM Production Deployment Script via Git - Version 2.0
# Fixed CRLF issues and improved error handling
# Created: December 3, 2025

$ErrorActionPreference = "Stop"

$serverIP = "103.214.112.58"
$serverUser = "root"
$projectPath = "/var/www/tmm"
$branch = "main"

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "   TMM Git Deploy to Production v2.0" -ForegroundColor Cyan
Write-Host "   (CRLF-Fixed & Error-Handled)" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Step 1: Check Local Changes
Write-Host "[1/6] Checking local changes..." -ForegroundColor Yellow

$gitStatus = git status --porcelain
if ($gitStatus) {
    Write-Host "WARNING: You have uncommitted changes:" -ForegroundColor Yellow
    Write-Host $gitStatus
    $continue = Read-Host "`nCommit and push these changes? (y/n)"
    
    if ($continue -eq 'y') {
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
        
        Write-Host "✓ Changes committed and pushed to GitHub!" -ForegroundColor Green
    } elseif ($continue -ne 'n') {
        Write-Host "Deployment cancelled." -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "✓ No local changes" -ForegroundColor Green
}
Write-Host ""

# Step 2: Pull on Production Server (Single command approach)
Write-Host "[2/6] Pulling from GitHub on production server..." -ForegroundColor Yellow

try {
    $pullOutput = ssh ${serverUser}@${serverIP} "cd $projectPath && git pull origin $branch 2>&1"
    Write-Host $pullOutput -ForegroundColor Gray
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Git pull successful" -ForegroundColor Green
    } else {
        Write-Host "✗ Git pull failed with exit code: $LASTEXITCODE" -ForegroundColor Red
        Write-Host "Output: $pullOutput" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "✗ Git pull error: $_" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Step 3: Set Permissions
Write-Host "[3/6] Setting file permissions..." -ForegroundColor Yellow

try {
    ssh ${serverUser}@${serverIP} "cd $projectPath && chown -R www-data:www-data tmp/ logs/ webroot/img/uploads/ webroot/files/uploads/ 2>&1"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Ownership set to www-data:www-data" -ForegroundColor Green
    } else {
        Write-Host "⚠ Warning: chown failed (may need sudo)" -ForegroundColor Yellow
    }
    
    ssh ${serverUser}@${serverIP} "cd $projectPath && chmod -R 775 tmp/cache/ logs/ webroot/img/uploads/ webroot/files/uploads/ 2>&1"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Permissions set to 775" -ForegroundColor Green
    } else {
        Write-Host "⚠ Warning: chmod failed" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠ Permissions error: $_" -ForegroundColor Yellow
    Write-Host "   (Continuing anyway...)" -ForegroundColor Gray
}
Write-Host ""

# Step 4: Clear Cache
Write-Host "[4/6] Clearing CakePHP cache..." -ForegroundColor Yellow

try {
    ssh ${serverUser}@${serverIP} "cd $projectPath && rm -rf tmp/cache/models/* tmp/cache/persistent/* tmp/cache/views/* 2>&1"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Cache cleared (models, persistent, views)" -ForegroundColor Green
    } else {
        Write-Host "⚠ Warning: Cache clear failed" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠ Cache clear error: $_" -ForegroundColor Yellow
}
Write-Host ""

# Step 5: Restart PHP-FPM
Write-Host "[5/6] Restarting PHP-FPM..." -ForegroundColor Yellow

try {
    $phpOutput = ssh ${serverUser}@${serverIP} "systemctl restart php7.4-fpm 2>&1 && echo OK"
    
    if ($phpOutput -match "OK") {
        Write-Host "✓ PHP 7.4-FPM restarted" -ForegroundColor Green
    } else {
        Write-Host "✗ PHP-FPM restart failed: $phpOutput" -ForegroundColor Red
        Write-Host "   (Trying to continue...)" -ForegroundColor Gray
    }
} catch {
    Write-Host "✗ PHP-FPM restart error: $_" -ForegroundColor Red
}
Write-Host ""

# Step 6: Reload Nginx
Write-Host "[6/6] Reloading nginx..." -ForegroundColor Yellow

try {
    $nginxOutput = ssh ${serverUser}@${serverIP} "systemctl reload nginx 2>&1 && echo OK"
    
    if ($nginxOutput -match "OK") {
        Write-Host "✓ nginx reloaded" -ForegroundColor Green
    } else {
        Write-Host "✗ nginx reload failed: $nginxOutput" -ForegroundColor Red
    }
} catch {
    Write-Host "✗ nginx reload error: $_" -ForegroundColor Red
}
Write-Host ""

# Verify Deployment
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   Deployment Verification" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

try {
    Write-Host "Current commit on server:" -ForegroundColor Yellow
    $commitInfo = ssh ${serverUser}@${serverIP} "cd $projectPath && git log -1 --oneline"
    Write-Host "  $commitInfo" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Service status:" -ForegroundColor Yellow
    $phpStatus = ssh ${serverUser}@${serverIP} "systemctl is-active php7.4-fpm 2>&1"
    $nginxStatus = ssh ${serverUser}@${serverIP} "systemctl is-active nginx 2>&1"
    
    if ($phpStatus -match "active") {
        Write-Host "  ✓ PHP 7.4-FPM: $phpStatus" -ForegroundColor Green
    } else {
        Write-Host "  ✗ PHP 7.4-FPM: $phpStatus" -ForegroundColor Red
    }
    
    if ($nginxStatus -match "active") {
        Write-Host "  ✓ nginx: $nginxStatus" -ForegroundColor Green
    } else {
        Write-Host "  ✗ nginx: $nginxStatus" -ForegroundColor Red
    }
} catch {
    Write-Host "⚠ Verification error: $_" -ForegroundColor Yellow
}

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "   Deployment Complete!" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "Test the deployment at:" -ForegroundColor Cyan
Write-Host "  • http://103.214.112.58/tmm/" -ForegroundColor White
Write-Host "  • https://asahifamily.id/tmm/" -ForegroundColor White
Write-Host ""
Write-Host "Check process flow pages:" -ForegroundColor Cyan
Write-Host "  • http://103.214.112.58/tmm/candidates/process-flow" -ForegroundColor White
Write-Host "  • http://103.214.112.58/tmm/dashboard/process-flow" -ForegroundColor White
Write-Host ""
