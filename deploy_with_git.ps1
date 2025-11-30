#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Git-based deployment script for TMM application
    
.DESCRIPTION
    This script manages deployment from local development to production server using Git.
    It commits changes, pushes to GitHub, then pulls on production server.
    
.PARAMETER Message
    Commit message (default: auto-generated with timestamp)
    
.PARAMETER SkipTests
    Skip pre-deployment validation tests
    
.EXAMPLE
    .\deploy_with_git.ps1 -Message "Fixed katakana templates"
    
.EXAMPLE
    .\deploy_with_git.ps1 -SkipTests
#>

param(
    [string]$Message = "",
    [switch]$SkipTests = $false
)

$ErrorActionPreference = "Stop"
$ServerIP = "103.214.112.58"
$ServerUser = "root"
$ServerPath = "/var/www/tmm"
$GitRemote = "origin"
$GitBranch = "main"

# Colors for output
function Write-ColorOutput($ForegroundColor) {
    $fc = $host.UI.RawUI.ForegroundColor
    $host.UI.RawUI.ForegroundColor = $ForegroundColor
    if ($args) {
        Write-Output $args
    }
    $host.UI.RawUI.ForegroundColor = $fc
}

function Write-Step($message) {
    Write-ColorOutput Cyan "`n========================================`n$message`n========================================"
}

function Write-Success($message) {
    Write-ColorOutput Green " $message"
}

function Write-Error($message) {
    Write-ColorOutput Red " $message"
}

function Write-Warning($message) {
    Write-ColorOutput Yellow "  $message"
}

# Step 1: Pre-deployment checks
Write-Step "Step 1: Pre-deployment Checks"

# Check if we're in the correct directory
if (-not (Test-Path "config/app.php")) {
    Write-Error "Not in TMM root directory. Please run from d:\xampp\htdocs\tmm"
    exit 1
}

# Check Git status
Write-Host "Checking Git status..."
$gitStatus = git status --porcelain
if (-not $gitStatus) {
    Write-Warning "No changes to deploy. Working directory is clean."
    $continue = Read-Host "Continue anyway? (y/N)"
    if ($continue -ne "y") {
        exit 0
    }
}

# Step 2: Syntax validation (unless skipped)
if (-not $SkipTests) {
    Write-Step "Step 2: Syntax Validation"
    
    Write-Host "Validating PHP syntax in critical files..."
    $criticalFiles = @(
        "config/app.php",
        "config/app_datasources.php",
        "src/Controller/AppController.php"
    )
    
    foreach ($file in $criticalFiles) {
        if (Test-Path $file) {
            $result = php -l $file 2>&1
            if ($LASTEXITCODE -ne 0) {
                Write-Error "PHP syntax error in $file"
                Write-Host $result
                exit 1
            }
        }
    }
    Write-Success "All critical files pass syntax validation"
} else {
    Write-Warning "Skipping syntax validation (--SkipTests flag)"
}

# Step 3: Commit changes
Write-Step "Step 3: Committing Changes to Git"

# Generate commit message if not provided
if ([string]::IsNullOrWhiteSpace($Message)) {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $Message = "Deployment update - $timestamp"
    Write-Host "Using auto-generated commit message: $Message"
}

# Show what will be committed
Write-Host "`nFiles to be committed:"
git status --short

$confirm = Read-Host "`nProceed with commit? (Y/n)"
if ($confirm -eq "n") {
    Write-Warning "Deployment cancelled by user"
    exit 0
}

# Stage all changes
Write-Host "Staging changes..."
git add -A

# Commit
Write-Host "Creating commit..."
git commit -m $Message
if ($LASTEXITCODE -ne 0) {
    Write-Warning "Commit failed (may be nothing to commit)"
}

Write-Success "Changes committed locally"

# Step 4: Push to GitHub
Write-Step "Step 4: Pushing to GitHub"

Write-Host "Pushing to $GitRemote/$GitBranch..."
git push $GitRemote $GitBranch

if ($LASTEXITCODE -ne 0) {
    Write-Error "Failed to push to GitHub"
    Write-Host "Please resolve Git issues manually and try again"
    exit 1
}

Write-Success "Changes pushed to GitHub"

# Step 5: Deploy to production server
Write-Step "Step 5: Deploying to Production Server ($ServerIP)"

Write-Host "Connecting to production server..."

# Create deployment commands
$deployCommands = @"
echo '======================================'
echo 'TMM Git Deployment'
echo '======================================'
echo ''

# Navigate to application directory
cd $ServerPath || exit 1

# Show current state
echo ' Current branch and status:'
git branch --show-current
git log -1 --oneline
echo ''

# Stash any local changes (like logs)
echo ' Stashing local changes...'
git stash

# Pull latest changes from GitHub
echo ' Pulling latest changes from GitHub...'
git pull $GitRemote $GitBranch

if [ \$? -ne 0 ]; then
    echo ' Git pull failed!'
    exit 1
fi

echo ' Code updated successfully'
echo ''

# Set correct permissions
echo ' Setting permissions...'
chown -R www-data:www-data tmp/ logs/ webroot/img/uploads/ webroot/files/uploads/
chmod -R 775 tmp/ logs/ webroot/img/uploads/ webroot/files/uploads/

# Clear cache
echo '  Clearing cache...'
rm -rf tmp/cache/models/*
rm -rf tmp/cache/persistent/*
rm -rf tmp/cache/views/*

# Restart services
echo '  Restarting services...'
systemctl restart php7.4-fpm
systemctl reload nginx

if [ \$? -eq 0 ]; then
    echo ' Services restarted successfully'
else
    echo ' Service restart failed'
    exit 1
fi

echo ''
echo '======================================'
echo 'Deployment Complete!'
echo '======================================'
echo ''
echo ' Deployment Summary:'
git log -1 --pretty=format:'Commit: %h%nAuthor: %an%nDate: %ad%nMessage: %s' --date=short
echo ''
echo ''
echo ' Application URL: http://$ServerIP/tmm/'
echo ''
"@

# Execute deployment on server
ssh ${ServerUser}@${ServerIP} $deployCommands

if ($LASTEXITCODE -ne 0) {
    Write-Error "Deployment to production server failed"
    exit 1
}

Write-Success "Deployment to production completed"

# Step 6: Post-deployment verification
Write-Step "Step 6: Post-deployment Verification"

Write-Host "Testing application endpoints..."

# Test main page
try {
    $response = Invoke-WebRequest -Uri "http://$ServerIP/tmm/" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Success "Main page responding (HTTP 200)"
    } else {
        Write-Warning "Main page returned HTTP $($response.StatusCode)"
    }
} catch {
    Write-Error "Failed to connect to application: $_"
}

# Check error logs
Write-Host "`nChecking recent error logs..."
ssh ${ServerUser}@${ServerIP} "tail -20 $ServerPath/logs/error.log 2>/dev/null | grep -v '^$' | tail -5 || echo 'No recent errors'"

# Final summary
Write-Step "Deployment Summary"
Write-Success "Local changes committed: $Message"
Write-Success "Changes pushed to GitHub: $GitRemote/$GitBranch"
Write-Success "Production server updated: $ServerIP"
Write-Success "Services restarted: php7.4-fpm, nginx"
Write-Success "Cache cleared"

Write-Host "`n Deployment completed successfully!"
Write-Host " Application: http://$ServerIP/tmm/"
Write-Host " GitHub: https://github.com/sriechoentjoro/tmm-project"
Write-Host ""

