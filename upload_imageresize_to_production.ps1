# Upload ImageResize library to production server
# Fix: Warning require_once ImageResize.php failed to open stream

$ErrorActionPreference = "Stop"

# Configuration
$serverUser = "root"
$serverHost = "103.214.112.58"
$serverPath = "/var/www/tmm/vendor/"
$localPath = "vendor\ImageResize"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Upload ImageResize Library to Production" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if local ImageResize folder exists
if (-not (Test-Path $localPath)) {
    Write-Host "ERROR: Local ImageResize folder not found at: $localPath" -ForegroundColor Red
    exit 1
}

Write-Host "Step 1: Checking local ImageResize folder..." -ForegroundColor Yellow
$files = Get-ChildItem -Path $localPath -Recurse -File
Write-Host "Found $($files.Count) files in ImageResize folder" -ForegroundColor Green

# Create temporary zip file
$tempZip = "ImageResize_temp.zip"
Write-Host ""
Write-Host "Step 2: Creating temporary zip file..." -ForegroundColor Yellow

# Remove old zip if exists
if (Test-Path $tempZip) {
    Remove-Item $tempZip -Force
}

# Create zip using PowerShell Compress-Archive
Compress-Archive -Path "$localPath\*" -DestinationPath $tempZip -Force
Write-Host "Created: $tempZip" -ForegroundColor Green

# Upload zip to server
Write-Host ""
Write-Host "Step 3: Uploading to production server..." -ForegroundColor Yellow
Write-Host "Target: ${serverUser}@${serverHost}:${serverPath}" -ForegroundColor Cyan

scp $tempZip "${serverUser}@${serverHost}:/tmp/"
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Failed to upload zip file" -ForegroundColor Red
    Remove-Item $tempZip -Force
    exit 1
}
Write-Host "Upload successful!" -ForegroundColor Green

# SSH commands to extract and set permissions
Write-Host ""
Write-Host "Step 4: Extracting on server..." -ForegroundColor Yellow

$sshCommands = @"
cd /var/www/tmm/vendor/ && \
echo 'Creating ImageResize directory...' && \
mkdir -p ImageResize && \
echo 'Extracting files...' && \
unzip -o /tmp/ImageResize_temp.zip -d ImageResize/ && \
echo 'Setting permissions...' && \
chown -R www-data:www-data ImageResize && \
chmod -R 755 ImageResize && \
echo 'Cleaning up...' && \
rm /tmp/ImageResize_temp.zip && \
echo 'Verifying installation...' && \
ls -la ImageResize/ && \
echo '' && \
echo 'SUCCESS: ImageResize library installed!' && \
echo 'Files in ImageResize folder:' && \
find ImageResize -type f -name '*.php'
"@

ssh "${serverUser}@${serverHost}" $sshCommands
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Failed to extract or set permissions" -ForegroundColor Red
    Remove-Item $tempZip -Force
    exit 1
}

# Clean up local zip
Write-Host ""
Write-Host "Step 5: Cleaning up local files..." -ForegroundColor Yellow
Remove-Item $tempZip -Force
Write-Host "Removed temporary zip file" -ForegroundColor Green

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "ImageResize Upload Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Verify the library is working by accessing your app" -ForegroundColor White
Write-Host "2. Check error logs: ssh root@103.214.112.58 'tail -f /var/www/tmm/logs/error.log'" -ForegroundColor White
Write-Host ""
Write-Host "If issues persist, check:" -ForegroundColor Yellow
Write-Host "- File permissions (should be www-data:www-data)" -ForegroundColor White
Write-Host "- Path in AppController.php matches: /var/www/tmm/vendor/ImageResize/ImageResize.php" -ForegroundColor White
Write-Host ""
