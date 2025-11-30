#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Setup Git repository on production server
    
.DESCRIPTION
    This script initializes Git on the production server and connects it to GitHub.
    Run this ONCE to set up the server for Git-based deployments.
    
.EXAMPLE
    .\setup_git_on_server.ps1
#>

$ErrorActionPreference = "Stop"
$ServerIP = "103.214.112.58"
$ServerUser = "root"
$ServerPath = "/var/www/tmm"
$GitRemote = "https://github.com/sriechoentjoro/tmm-project.git"
$GitBranch = "main"

function Write-Step($message) {
    Write-Host "`n========================================" -ForegroundColor Cyan
    Write-Host $message -ForegroundColor Cyan
    Write-Host "========================================`n" -ForegroundColor Cyan
}

function Write-Success($message) {
    Write-Host " $message" -ForegroundColor Green
}

Write-Step "TMM Production Server - Git Setup"

Write-Host "This script will:"
Write-Host "  1. Install Git on production server (if needed)"
Write-Host "  2. Initialize Git repository in $ServerPath"
Write-Host "  3. Connect to GitHub repository"
Write-Host "  4. Pull latest code from GitHub"
Write-Host "  5. Configure Git to ignore local files"
Write-Host ""

$confirm = Read-Host "Continue? (Y/n)"
if ($confirm -eq "n") {
    Write-Host "Setup cancelled"
    exit 0
}

Write-Step "Step 1: Installing Git"

$setupCommands = @"
echo ' Checking Git installation...'
if ! command -v git &> /dev/null; then
    echo 'Installing Git...'
    apt-get update -qq
    apt-get install -y git
    echo ' Git installed'
else
    echo ' Git already installed'
    git --version
fi
echo ''

echo ' Configuring Git...'
git config --global user.name "TMM Production Server"
git config --global user.email "admin@asahifamily.id"
git config --global init.defaultBranch main
echo ' Git configured'
echo ''
"@

ssh ${ServerUser}@${ServerIP} $setupCommands

Write-Success "Git installed and configured on server"

Write-Step "Step 2: Initializing Git Repository"

$initCommands = @"
cd $ServerPath || exit 1

# Check if already a Git repository
if [ -d .git ]; then
    echo '  Git repository already exists'
    echo 'Current remote:'
    git remote -v
    echo ''
    echo 'Skipping initialization'
else
    echo ' Initializing Git repository...'
    git init
    echo ' Git repository initialized'
fi
echo ''
"@

ssh ${ServerUser}@${ServerIP} $initCommands

Write-Success "Git repository initialized"

Write-Step "Step 3: Connecting to GitHub"

$remoteCommands = @"
cd $ServerPath || exit 1

# Remove existing remote if any
git remote remove origin 2>/dev/null || true

# Add GitHub remote
echo ' Adding GitHub remote...'
git remote add origin $GitRemote
git remote -v
echo ' GitHub remote added'
echo ''
"@

ssh ${ServerUser}@${ServerIP} $remoteCommands

Write-Success "Connected to GitHub repository"

Write-Step "Step 4: Pulling Latest Code"

$pullCommands = @"
cd $ServerPath || exit 1

echo ' Fetching from GitHub...'
git fetch origin

# Check if we have local commits
if git rev-parse --verify HEAD >/dev/null 2>&1; then
    echo '  Local commits detected'
    echo 'Creating backup branch...'
    git branch backup-before-git-setup 2>/dev/null || true
fi

echo ' Pulling latest code from GitHub...'
git checkout $GitBranch 2>/dev/null || git checkout -b $GitBranch
git pull origin $GitBranch --allow-unrelated-histories

if [ \$? -eq 0 ]; then
    echo ' Code pulled successfully'
else
    echo ' Pull failed - may need manual merge'
    exit 1
fi
echo ''

echo ' Current status:'
git log -1 --oneline
echo ''
"@

ssh ${ServerUser}@${ServerIP} $pullCommands

Write-Success "Latest code pulled from GitHub"

Write-Step "Step 5: Configuring Git Ignore"

$gitignoreCommands = @"
cd $ServerPath || exit 1

# Create server-specific gitignore
cat > .git/info/exclude <<'EOF'
# Server-specific files (never commit these)
/config/app_local.php
/logs/*.log
/tmp/cache/**
/tmp/sessions/**
/webroot/img/uploads/**
/webroot/files/uploads/**
/backups/*.sql
EOF

echo ' Git ignore configured for server'
echo ''

# Set correct permissions
echo ' Setting permissions...'
chown -R www-data:www-data tmp/ logs/ webroot/img/uploads/ webroot/files/uploads/
chmod -R 775 tmp/ logs/ webroot/img/uploads/ webroot/files/uploads/
echo ' Permissions set'
echo ''
"@

ssh ${ServerUser}@${ServerIP} $gitignoreCommands

Write-Success "Git configuration completed"

Write-Step "Setup Complete!"

Write-Host " Git is now set up on production server" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Make changes to your local code"
Write-Host "  2. Run: .\deploy_with_git.ps1 -Message 'Your commit message'"
Write-Host "  3. Changes will automatically deploy to production"
Write-Host ""
Write-Host "Production URL: http://$ServerIP/tmm/"
Write-Host "GitHub: $GitRemote"
Write-Host ""


