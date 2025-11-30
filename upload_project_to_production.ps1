# Upload TMM Project to Production Server
# Target: root@103.214.112.58:/var/www/tmm

$ErrorActionPreference = "Stop"

# Configuration
$serverUser = "root"
$serverHost = "103.214.112.58"
$remotePath = "/var/www/tmm"
$localPath = "d:\xampp\htdocs\tmm"

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "TMM Project Upload to Production" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Server: $serverUser@$serverHost" -ForegroundColor Yellow
Write-Host "Remote Path: $remotePath" -ForegroundColor Yellow
Write-Host "Local Path: $localPath" -ForegroundColor Yellow
Write-Host ""

# Exclude patterns for rsync
$excludePatterns = @(
    ".git/",
    ".vscode/",
    "node_modules/",
    "tmp/cache/",
    "tmp/sessions/",
    "tmp/tests/",
    "logs/*.log",
    "webroot/img/uploads/",
    "webroot/files/uploads/",
    "*.ps1",
    "*.md",
    "*.txt",
    "*.bat",
    ".gitignore",
    "composer.lock",
    "config/app_local.php"
)

# Build rsync exclude arguments
$excludeArgs = @()
foreach ($pattern in $excludePatterns) {
    $excludeArgs += "--exclude=$pattern"
}

Write-Host "Step 1: Checking SSH connection..." -ForegroundColor Green
try {
    ssh -o ConnectTimeout=5 "$serverUser@$serverHost" "echo 'SSH connection successful'"
    Write-Host "[OK] SSH connection verified" -ForegroundColor Green
} catch {
    Write-Host "[ERROR] SSH connection failed. Please check:" -ForegroundColor Red
    Write-Host "  - Server is accessible" -ForegroundColor Yellow
    Write-Host "  - SSH key is configured" -ForegroundColor Yellow
    Write-Host "  - Port 22 is open" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "Step 2: Creating remote directory if not exists..." -ForegroundColor Green
ssh "$serverUser@$serverHost" "mkdir -p $remotePath"
Write-Host "[OK] Remote directory ready" -ForegroundColor Green

Write-Host ""
Write-Host "Step 3: Uploading project files via archive..." -ForegroundColor Green
Write-Host "This may take a few minutes..." -ForegroundColor Yellow
Write-Host ""

# Create temporary tar archive
$tempArchive = Join-Path $env:TEMP "tmm_upload.tar.gz"
Write-Host "Creating archive: $tempArchive" -ForegroundColor Cyan

# Use tar (available in Windows 10+)
Push-Location $localPath
tar -czf $tempArchive `
    --exclude=.git `
    --exclude=.vscode `
    --exclude=node_modules `
    --exclude=tmp/cache/* `
    --exclude=tmp/sessions/* `
    --exclude=logs/*.log `
    --exclude=webroot/img/uploads/* `
    --exclude=webroot/files/uploads/* `
    --exclude=*.ps1 `
    --exclude=*.md `
    --exclude=*.txt `
    --exclude=*.bat `
    .
Pop-Location

Write-Host "[OK] Archive created: $([math]::Round((Get-Item $tempArchive).Length / 1MB, 2)) MB" -ForegroundColor Green

# Upload archive
Write-Host "Uploading archive to server..." -ForegroundColor Cyan
scp $tempArchive "$serverUser@${serverHost}:/tmp/tmm_upload.tar.gz"

# Extract on server
Write-Host "Extracting archive on server..." -ForegroundColor Cyan
ssh "$serverUser@$serverHost" @"
    mkdir -p $remotePath
    cd $remotePath
    tar -xzf /tmp/tmm_upload.tar.gz
    rm /tmp/tmm_upload.tar.gz
    echo 'Extraction complete'
"@

# Clean up local archive
Remove-Item $tempArchive -Force
Write-Host "[OK] Upload complete" -ForegroundColor Green

Write-Host ""
Write-Host "Step 4: Setting proper permissions..." -ForegroundColor Green
ssh "$serverUser@$serverHost" @"
    cd $remotePath
    
    # Set ownership (adjust www-data if needed for your server)
    chown -R www-data:www-data .
    
    # Set directory permissions
    find . -type d -exec chmod 755 {} \;
    
    # Set file permissions
    find . -type f -exec chmod 644 {} \;
    
    # Make bin/cake executable
    chmod +x bin/cake
    
    # Set writable directories
    chmod -R 775 tmp logs webroot/img/uploads webroot/files/uploads
    chown -R www-data:www-data tmp logs webroot/img/uploads webroot/files/uploads
    
    echo 'Permissions set'
"@
Write-Host "[OK] Permissions configured" -ForegroundColor Green

Write-Host ""
Write-Host "Step 5: Verifying upload..." -ForegroundColor Green
$remoteFileCount = ssh "$serverUser@$serverHost" "find $remotePath -type f | wc -l"
Write-Host "Remote files: $remoteFileCount" -ForegroundColor Cyan

Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "Upload Complete!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Update database configuration:" -ForegroundColor White
Write-Host "   ssh $serverUser@$serverHost" -ForegroundColor Cyan
Write-Host "   nano $remotePath/config/app.php" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Clear CakePHP cache:" -ForegroundColor White
Write-Host "   cd $remotePath && bin/cake cache clear_all" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Verify web server configuration (nginx/apache)" -ForegroundColor White
Write-Host ""
Write-Host "4. Test the application:" -ForegroundColor White
Write-Host "   http://103.214.112.58" -ForegroundColor Cyan
Write-Host ""
