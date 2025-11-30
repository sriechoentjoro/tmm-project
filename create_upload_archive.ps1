# Simple Upload TMM Project to Production
# Creates ZIP and uploads via SCP
# Manual extraction on server

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "TMM Project - Create Upload Archive" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$localPath = "D:\xampp\htdocs\tmm"
$outputPath = "D:\tmm_production_upload.zip"

# Remove old archive if exists
if (Test-Path $outputPath) {
    Remove-Item $outputPath -Force
    Write-Host "Removed old archive" -ForegroundColor Gray
}

Write-Host "Creating ZIP archive..." -ForegroundColor Yellow
Write-Host "Source: $localPath" -ForegroundColor White
Write-Host "Output: $outputPath" -ForegroundColor White
Write-Host ""

# Exclude patterns
$excludePatterns = @(
    "*\vendor\*",
    "*\tmp\*",
    "*\.git\*",
    "*\.gitignore",
    "*\.env",
    "*\logs\*.log",
    "*\.DS_Store",
    "*\Thumbs.db",
    "*\node_modules\*",
    "*\.vscode\*",
    "*\.idea\*",
    "*.ps1",
    "*.bat",
    "*.md",
    "*.tmp",
    "*.bak"
)

# Get files to include
$allFiles = Get-ChildItem -Path $localPath -Recurse -File
$filesToInclude = $allFiles | Where-Object {
    $file = $_
    $include = $true
    
    foreach ($pattern in $excludePatterns) {
        if ($file.FullName -like $pattern) {
            $include = $false
            break
        }
    }
    
    $include
}

Write-Host "Files to upload: $($filesToInclude.Count)" -ForegroundColor Green
Write-Host "Excluded files: $($allFiles.Count - $filesToInclude.Count)" -ForegroundColor Yellow
Write-Host ""
Write-Host "Creating archive (this may take a few minutes)..." -ForegroundColor Cyan

# Create ZIP
Add-Type -Assembly System.IO.Compression.FileSystem
$compressionLevel = [System.IO.Compression.CompressionLevel]::Optimal

$zip = [System.IO.Compression.ZipFile]::Open($outputPath, 'Create')

$count = 0
$total = $filesToInclude.Count

foreach ($file in $filesToInclude) {
    $count++
    $relativePath = $file.FullName.Substring($localPath.Length + 1)
    
    if ($count % 100 -eq 0 -or $count -eq $total) {
        $percent = [math]::Round(($count / $total) * 100)
        Write-Progress -Activity "Creating Archive" -Status "$count / $total files ($percent%)" -PercentComplete $percent
    }
    
    try {
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $file.FullName, $relativePath, $compressionLevel) | Out-Null
    } catch {
        Write-Host "Warning: Could not add $relativePath - $($_.Exception.Message)" -ForegroundColor Yellow
    }
}

$zip.Dispose()
Write-Progress -Activity "Creating Archive" -Completed

$archiveSize = (Get-Item $outputPath).Length / 1MB
Write-Host ""
Write-Host "Archive created successfully!" -ForegroundColor Green
Write-Host "Size: $('{0:N2}' -f $archiveSize) MB" -ForegroundColor Green
Write-Host "Location: $outputPath" -ForegroundColor White
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Next Steps - Manual Upload" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Option 1: Using WinSCP or FileZilla" -ForegroundColor Yellow
Write-Host "  1. Open WinSCP/FileZilla" -ForegroundColor White
Write-Host "  2. Connect to: root@103.214.112.58" -ForegroundColor White
Write-Host "  3. Upload: $outputPath" -ForegroundColor White
Write-Host "  4. To: /tmp/" -ForegroundColor White
Write-Host ""
Write-Host "Option 2: Using SCP command" -ForegroundColor Yellow
Write-Host "  scp `"$outputPath`" root@103.214.112.58:/tmp/tmm_production.zip" -ForegroundColor White
Write-Host ""
Write-Host "After upload, SSH to server and run:" -ForegroundColor Yellow
Write-Host ""
Write-Host "# Backup current installation" -ForegroundColor Gray
Write-Host "mv /var/www/tmm /var/www/tmm_backup_`$(date +%Y%m%d_%H%M%S)" -ForegroundColor White
Write-Host ""
Write-Host "# Extract new files" -ForegroundColor Gray
Write-Host "mkdir -p /var/www/tmm" -ForegroundColor White
Write-Host "cd /var/www/tmm" -ForegroundColor White
Write-Host "unzip /tmp/tmm_production.zip" -ForegroundColor White
Write-Host ""
Write-Host "# Install dependencies" -ForegroundColor Gray
Write-Host "composer install --no-dev --optimize-autoloader" -ForegroundColor White
Write-Host ""
Write-Host "# Set permissions" -ForegroundColor Gray
Write-Host "chmod -R 755 /var/www/tmm" -ForegroundColor White
Write-Host "mkdir -p tmp logs" -ForegroundColor White
Write-Host "chmod -R 777 tmp logs" -ForegroundColor White
Write-Host "chown -R www-data:www-data /var/www/tmm" -ForegroundColor White
Write-Host ""
Write-Host "# Restart web server" -ForegroundColor Gray
Write-Host "systemctl restart nginx" -ForegroundColor White
Write-Host ""
Write-Host "# Clean up" -ForegroundColor Gray
Write-Host "rm /tmp/tmm_production.zip" -ForegroundColor White
Write-Host ""
