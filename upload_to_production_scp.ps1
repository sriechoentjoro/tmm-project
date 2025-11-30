# Upload TMM Project to Production using SCP
# Server: root@103.214.112.58
# Target: /var/www/tmm
# Date: 2025-11-30

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "TMM Project - Upload to Production (SCP)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$serverUser = "root"
$serverHost = "103.214.112.58"
$targetPath = "/var/www/tmm"
$localPath = "D:\xampp\htdocs\tmm"

Write-Host "Creating temporary archive..." -ForegroundColor Cyan

# Create temp directory
$tempDir = Join-Path $env:TEMP "tmm_upload"
if (Test-Path $tempDir) {
    Remove-Item -Path $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir -Force | Out-Null

$archivePath = Join-Path $tempDir "tmm_project.zip"

# Folders/files to exclude
$excludePatterns = @(
    "vendor",
    "tmp",
    ".git",
    ".gitignore",
    ".env",
    "logs\debug.log",
    "logs\error.log",
    "logs\cli-debug.log",
    "logs\cli-error.log",
    "composer.lock",
    ".DS_Store",
    "Thumbs.db",
    "node_modules",
    ".vscode",
    ".idea"
)

Write-Host "Creating ZIP archive (excluding vendor, tmp, .git, logs)..." -ForegroundColor Yellow
Write-Host ""

# Get all files except excluded
$files = Get-ChildItem -Path $localPath -Recurse -File | Where-Object {
    $file = $_
    $relativePath = $file.FullName.Substring($localPath.Length + 1)
    
    # Check if file matches any exclude pattern
    $exclude = $false
    foreach ($pattern in $excludePatterns) {
        if ($relativePath -like "$pattern*" -or $relativePath -like "*.$pattern" -or $file.Name -like "*.log" -or $file.Name -like "*.tmp" -or $file.Name -like "*.bak" -or $file.Extension -eq ".ps1" -or $file.Extension -eq ".bat" -or $file.Extension -eq ".md") {
            $exclude = $true
            break
        }
    }
    
    -not $exclude
}

Write-Host "Found $($files.Count) files to upload" -ForegroundColor Green
Write-Host ""

# Create ZIP
Add-Type -Assembly System.IO.Compression.FileSystem
$compressionLevel = [System.IO.Compression.CompressionLevel]::Optimal

try {
    $zip = [System.IO.Compression.ZipFile]::Open($archivePath, 'Create')
    
    $count = 0
    foreach ($file in $files) {
        $count++
        $relativePath = $file.FullName.Substring($localPath.Length + 1)
        
        if ($count % 100 -eq 0) {
            Write-Host "Compressed $count / $($files.Count) files..." -ForegroundColor Gray
        }
        
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $file.FullName, $relativePath, $compressionLevel) | Out-Null
    }
    
    $zip.Dispose()
    
    Write-Host "Archive created: $('{0:N2}' -f ((Get-Item $archivePath).Length / 1MB)) MB" -ForegroundColor Green
    Write-Host ""
    
} catch {
    Write-Host "ERROR creating archive: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Upload via SCP
Write-Host "Uploading to server..." -ForegroundColor Cyan
Write-Host ""

$scpTarget = "${serverUser}@${serverHost}:/tmp/tmm_project.zip"

try {
    # Use scp command
    $psi = New-Object System.Diagnostics.ProcessStartInfo
    $psi.FileName = "scp"
    $psi.Arguments = "`"$archivePath`" $scpTarget"
    $psi.UseShellExecute = $false
    $psi.RedirectStandardOutput = $true
    $psi.RedirectStandardError = $true
    
    $process = New-Object System.Diagnostics.Process
    $process.StartInfo = $psi
    $process.Start() | Out-Null
    
    $output = $process.StandardOutput.ReadToEnd()
    $error = $process.StandardError.ReadToEnd()
    $process.WaitForExit()
    
    if ($process.ExitCode -ne 0) {
        Write-Host "ERROR uploading file: $error" -ForegroundColor Red
        exit 1
    }
    
    Write-Host "Upload completed!" -ForegroundColor Green
    Write-Host ""
    
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Extract on server
Write-Host "Extracting on server..." -ForegroundColor Cyan
Write-Host ""

$sshCommands = @"
# Backup current installation
if [ -d '$targetPath' ]; then
    echo 'Creating backup...'
    mv $targetPath ${targetPath}_backup_$(date +%Y%m%d_%H%M%S)
fi

# Create target directory
mkdir -p $targetPath

# Extract archive
echo 'Extracting files...'
cd $targetPath
unzip -q /tmp/tmm_project.zip

# Set permissions
echo 'Setting permissions...'
chmod -R 755 $targetPath
mkdir -p $targetPath/tmp $targetPath/logs
chmod -R 777 $targetPath/tmp $targetPath/logs
chmod -R 775 $targetPath/webroot

# Install composer dependencies
echo 'Installing composer dependencies...'
cd $targetPath
composer install --no-dev --optimize-autoloader

# Set ownership
echo 'Setting ownership...'
chown -R www-data:www-data $targetPath

# Clean up
rm -f /tmp/tmm_project.zip

echo 'Deployment completed!'
"@

try {
    $sshCommands | ssh "${serverUser}@${serverHost}" "bash -s"
    
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "Deployment completed successfully!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Final steps:" -ForegroundColor Yellow
    Write-Host "1. SSH to server: ssh root@103.214.112.58" -ForegroundColor White
    Write-Host "2. Restart web server: systemctl restart nginx" -ForegroundColor White
    Write-Host "3. Check logs: tail -f /var/www/tmm/logs/error.log" -ForegroundColor White
    Write-Host ""
    
} catch {
    Write-Host "ERROR during server setup: $($_.Exception.Message)" -ForegroundColor Red
}

# Clean up local temp
Remove-Item -Path $tempDir -Recurse -Force -ErrorAction SilentlyContinue
