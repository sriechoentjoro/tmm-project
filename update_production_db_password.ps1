# Update Production Database Credentials
# This script updates all database passwords in app_datasources.php

param(
    [Parameter(Mandatory=$true)]
    [string]$NewPassword
)

$ErrorActionPreference = "Stop"

$serverUser = "root"
$serverHost = "103.214.112.58"
$configFile = "/var/www/tmm/config/app_datasources.php"

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Update Production Database Credentials" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Server: $serverUser@$serverHost" -ForegroundColor Yellow
Write-Host "Config: $configFile" -ForegroundColor Yellow
Write-Host "New Password: " -NoNewline -ForegroundColor Yellow
Write-Host "********" -ForegroundColor Yellow
Write-Host ""

# Download current config
Write-Host "Step 1: Downloading current configuration..." -ForegroundColor Green
$tempFile = [System.IO.Path]::GetTempFileName()
scp "${serverUser}@${serverHost}:${configFile}" $tempFile

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Failed to download config file" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Config downloaded" -ForegroundColor Green

# Read and update content
Write-Host ""
Write-Host "Step 2: Updating password in all datasources..." -ForegroundColor Green
$content = Get-Content $tempFile -Raw

# Count occurrences before
$oldPasswordPattern = "'password' => '62xe6zyr'"
$occurrences = ([regex]::Matches($content, [regex]::Escape($oldPasswordPattern))).Count
Write-Host "Found $occurrences password entries to update" -ForegroundColor Cyan

# Replace all password occurrences
$newContent = $content -replace [regex]::Escape("'password' => '62xe6zyr'"), "'password' => '$NewPassword'"

# Save updated content
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($tempFile, $newContent, $utf8NoBom)

Write-Host "[OK] Password updated in local copy" -ForegroundColor Green

# Upload updated config
Write-Host ""
Write-Host "Step 3: Uploading updated configuration..." -ForegroundColor Green

# Backup original
ssh "$serverUser@$serverHost" "cp $configFile ${configFile}.backup_$(date +%Y%m%d_%H%M%S)"
Write-Host "[OK] Original backed up" -ForegroundColor Green

# Upload new version
scp $tempFile "${serverUser}@${serverHost}:${configFile}"

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Failed to upload config file" -ForegroundColor Red
    Write-Host "Original file is backed up" -ForegroundColor Yellow
    exit 1
}

Write-Host "[OK] Config uploaded" -ForegroundColor Green

# Set permissions
ssh "$serverUser@$serverHost" "chmod 644 $configFile && chown www-data:www-data $configFile"
Write-Host "[OK] Permissions set" -ForegroundColor Green

# Clean up
Remove-Item $tempFile -Force

# Test database connection
Write-Host ""
Write-Host "Step 4: Testing database connection..." -ForegroundColor Green
$testResult = ssh "$serverUser@$serverHost" "mysql -u root -p'$NewPassword' -e 'SELECT 1' 2>&1"

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Database connection successful!" -ForegroundColor Green
} else {
    Write-Host "[WARNING] Database connection test failed" -ForegroundColor Yellow
    Write-Host "Error: $testResult" -ForegroundColor Red
    Write-Host "You may need to check MySQL credentials" -ForegroundColor Yellow
}

# Clear cache
Write-Host ""
Write-Host "Step 5: Clearing CakePHP cache..." -ForegroundColor Green
ssh "$serverUser@$serverHost" "cd /var/www/tmm && rm -rf tmp/cache/*/*"
Write-Host "[OK] Cache cleared" -ForegroundColor Green

Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "Update Complete!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""
Write-Host "Database credentials updated for all datasources:" -ForegroundColor White
Write-Host "  - default" -ForegroundColor Gray
Write-Host "  - cms_masters" -ForegroundColor Gray
Write-Host "  - cms_lpk_candidates" -ForegroundColor Gray
Write-Host "  - cms_lpk_candidate_documents" -ForegroundColor Gray
Write-Host "  - cms_tmm_apprentices" -ForegroundColor Gray
Write-Host "  - cms_tmm_apprentice_documents" -ForegroundColor Gray
Write-Host "  - cms_tmm_apprentice_document_ticketings" -ForegroundColor Gray
Write-Host "  - cms_tmm_stakeholders" -ForegroundColor Gray
Write-Host "  - cms_tmm_trainees" -ForegroundColor Gray
Write-Host "  - cms_tmm_trainee_accountings" -ForegroundColor Gray
Write-Host "  - cms_tmm_trainee_trainings" -ForegroundColor Gray
Write-Host "  - cms_tmm_trainee_training_scorings" -ForegroundColor Gray
Write-Host "  - cms_authentication_authorization" -ForegroundColor Gray
Write-Host ""
Write-Host "Next: Configure web server (nginx/apache)" -ForegroundColor Yellow
Write-Host ""
