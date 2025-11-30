# Upload Complete TMM Project to Production Server
# Server: root@103.214.112.58
# Target: /var/www/tmm
# Date: 2025-11-30

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "TMM Project - Upload to Production" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$serverUser = "root"
$serverHost = "103.214.112.58"
$targetPath = "/var/www/tmm"
$localPath = "D:\xampp\htdocs\tmm"

# Check if rsync is available
$rsyncPath = "C:\Program Files\Git\usr\bin\rsync.exe"
if (-not (Test-Path $rsyncPath)) {
    Write-Host "ERROR: rsync not found at $rsyncPath" -ForegroundColor Red
    Write-Host "Please install Git for Windows or install rsync" -ForegroundColor Yellow
    exit 1
}

Write-Host "Source: $localPath" -ForegroundColor Green
Write-Host "Target: ${serverUser}@${serverHost}:${targetPath}" -ForegroundColor Green
Write-Host ""

# Confirm before upload
Write-Host "This will upload ALL project files to production server." -ForegroundColor Yellow
Write-Host "Excluded: vendor/, tmp/, .git/, .env" -ForegroundColor Yellow
Write-Host ""
$confirm = Read-Host "Continue? (yes/no)"
if ($confirm -ne "yes") {
    Write-Host "Upload cancelled." -ForegroundColor Red
    exit 0
}

Write-Host ""
Write-Host "Starting upload..." -ForegroundColor Cyan
Write-Host ""

# Create exclude file
$excludeFile = Join-Path $env:TEMP "tmm_rsync_exclude.txt"
@"
vendor/
tmp/
.git/
.gitignore
.env
*.log
logs/debug.log
logs/error.log
logs/cli-debug.log
logs/cli-error.log
composer.lock
.DS_Store
Thumbs.db
*.tmp
*.bak
node_modules/
.vscode/
.idea/
*.ps1
*.bat
*.md
README*
CHANGELOG*
LICENSE*
"@ | Out-File -FilePath $excludeFile -Encoding UTF8

# Build rsync command
$rsyncArgs = @(
    "-avz"                          # Archive, verbose, compress
    "--progress"                    # Show progress
    "--delete"                      # Delete files on server that don't exist locally
    "--exclude-from=`"$excludeFile`""  # Exclude file
    "$localPath/"                   # Source (trailing slash important!)
    "${serverUser}@${serverHost}:${targetPath}/"  # Destination
)

Write-Host "Executing rsync..." -ForegroundColor Cyan
Write-Host "Command: $rsyncPath $rsyncArgs" -ForegroundColor Gray
Write-Host ""

# Execute rsync
try {
    & $rsyncPath $rsyncArgs
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "========================================" -ForegroundColor Green
        Write-Host "Upload completed successfully!" -ForegroundColor Green
        Write-Host "========================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "Next steps on server:" -ForegroundColor Yellow
        Write-Host "1. SSH to server: ssh root@103.214.112.58" -ForegroundColor White
        Write-Host "2. cd /var/www/tmm" -ForegroundColor White
        Write-Host "3. composer install --no-dev --optimize-autoloader" -ForegroundColor White
        Write-Host "4. chmod -R 777 tmp/" -ForegroundColor White
        Write-Host "5. chmod -R 777 logs/" -ForegroundColor White
        Write-Host "6. chown -R www-data:www-data /var/www/tmm" -ForegroundColor White
        Write-Host "7. systemctl restart nginx" -ForegroundColor White
        Write-Host ""
    } else {
        Write-Host ""
        Write-Host "ERROR: Upload failed with exit code $LASTEXITCODE" -ForegroundColor Red
        Write-Host ""
    }
} catch {
    Write-Host ""
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
} finally {
    # Clean up temp file
    Remove-Item -Path $excludeFile -ErrorAction SilentlyContinue
}
