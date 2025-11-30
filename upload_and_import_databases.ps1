# Upload and Import Databases to Production Server
# This script uploads SQL files and imports them to production

param(
    [string]$ExportFolder = ""
)

$ErrorActionPreference = "Stop"

# Configuration
$serverUser = "root"
$serverHost = "103.214.112.58"
$mysqlUser = "root"
$mysqlPassword = ""  # Will prompt if not set

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "TMM Database Upload & Import" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Find the latest export folder if not specified
if ([string]::IsNullOrEmpty($ExportFolder)) {
    $exportDir = "d:\xampp\htdocs\tmm\database_exports"
    
    if (-not (Test-Path $exportDir)) {
        Write-Host "[ERROR] Export directory not found: $exportDir" -ForegroundColor Red
        Write-Host "Please run export_databases_with_drop.ps1 first" -ForegroundColor Yellow
        exit 1
    }
    
    $latestExport = Get-ChildItem $exportDir -Directory | Sort-Object Name -Descending | Select-Object -First 1
    
    if ($null -eq $latestExport) {
        Write-Host "[ERROR] No export folders found in $exportDir" -ForegroundColor Red
        Write-Host "Please run export_databases_with_drop.ps1 first" -ForegroundColor Yellow
        exit 1
    }
    
    $ExportFolder = $latestExport.FullName
}

if (-not (Test-Path $ExportFolder)) {
    Write-Host "[ERROR] Export folder not found: $ExportFolder" -ForegroundColor Red
    exit 1
}

Write-Host "Export Folder: $ExportFolder" -ForegroundColor Yellow
Write-Host "Server: $serverUser@$serverHost" -ForegroundColor Yellow
Write-Host ""

# Get list of SQL files
$sqlFiles = Get-ChildItem $ExportFolder -Filter "*.sql" | Where-Object { $_.Name -ne "import_all.sql" }

if ($sqlFiles.Count -eq 0) {
    Write-Host "[ERROR] No SQL files found in export folder" -ForegroundColor Red
    exit 1
}

Write-Host "Found $($sqlFiles.Count) database(s) to import" -ForegroundColor Cyan
foreach ($file in $sqlFiles) {
    $sizeMB = [math]::Round($file.Length / 1MB, 2)
    Write-Host "  - $($file.Name) ($sizeMB MB)" -ForegroundColor Gray
}
Write-Host ""

# Prompt for MySQL password if not set
if ([string]::IsNullOrEmpty($mysqlPassword)) {
    $securePassword = Read-Host "Enter MySQL password for production server" -AsSecureString
    $BSTR = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
    $mysqlPassword = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($BSTR)
}

# Step 1: Upload SQL files to server
Write-Host "Step 1: Uploading SQL files to server..." -ForegroundColor Green
Write-Host ""

$uploadSuccess = $true
foreach ($file in $sqlFiles) {
    Write-Host "  Uploading: $($file.Name)" -ForegroundColor Cyan
    
    try {
        scp $file.FullName "$serverUser@${serverHost}:/tmp/"
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "    [OK] Uploaded" -ForegroundColor Green
        } else {
            Write-Host "    [ERROR] Upload failed" -ForegroundColor Red
            $uploadSuccess = $false
        }
    } catch {
        Write-Host "    [ERROR] $($_.Exception.Message)" -ForegroundColor Red
        $uploadSuccess = $false
    }
}

if (-not $uploadSuccess) {
    Write-Host ""
    Write-Host "[ERROR] Some files failed to upload" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "[OK] All files uploaded successfully" -ForegroundColor Green
Write-Host ""

# Step 2: Import databases on server
Write-Host "Step 2: Importing databases on server..." -ForegroundColor Green
Write-Host ""

$importSuccess = $true
foreach ($file in $sqlFiles) {
    $database = [System.IO.Path]::GetFileNameWithoutExtension($file.Name)
    Write-Host "  Importing: $database" -ForegroundColor Cyan
    
    try {
        # Import database
        $importCommand = "mysql -u$mysqlUser -p'$mysqlPassword' < /tmp/$($file.Name) 2>&1"
        $result = ssh "$serverUser@$serverHost" $importCommand
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "    [OK] Imported successfully" -ForegroundColor Green
            
            # Verify database exists
            $verifyCommand = "mysql -u$mysqlUser -p'$mysqlPassword' -e 'USE $database; SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=''$database'';' -s -N 2>&1"
            $tableCount = ssh "$serverUser@$serverHost" $verifyCommand
            
            if ($tableCount -match '^\d+$') {
                Write-Host "    [INFO] Database contains $tableCount table(s)" -ForegroundColor Gray
            }
        } else {
            Write-Host "    [ERROR] Import failed: $result" -ForegroundColor Red
            $importSuccess = $false
        }
    } catch {
        Write-Host "    [ERROR] $($_.Exception.Message)" -ForegroundColor Red
        $importSuccess = $false
    }
}

Write-Host ""

# Step 3: Cleanup temp files
Write-Host "Step 3: Cleaning up temporary files..." -ForegroundColor Green
foreach ($file in $sqlFiles) {
    ssh "$serverUser@$serverHost" "rm -f /tmp/$($file.Name)"
}
Write-Host "[OK] Cleanup complete" -ForegroundColor Green
Write-Host ""

# Summary
Write-Host "======================================" -ForegroundColor Green
Write-Host "Import Complete!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green

if ($importSuccess) {
    Write-Host "All databases imported successfully" -ForegroundColor Green
} else {
    Write-Host "Some databases failed to import" -ForegroundColor Red
    Write-Host "Check the output above for details" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Verify database configuration in /var/www/tmm/config/app.php" -ForegroundColor White
Write-Host "2. Clear CakePHP cache:" -ForegroundColor White
Write-Host "   ssh $serverUser@$serverHost" -ForegroundColor Cyan
Write-Host "   cd /var/www/tmm && rm -rf tmp/cache/*/*" -ForegroundColor Cyan
Write-Host "3. Test the application" -ForegroundColor White
Write-Host ""

# Show database list
Write-Host "Verifying databases on server..." -ForegroundColor Cyan
$dbListCommand = "mysql -u$mysqlUser -p'$mysqlPassword' -e 'SHOW DATABASES;' 2>&1 | grep cms_"
$dbList = ssh "$serverUser@$serverHost" $dbListCommand

if ($dbList) {
    Write-Host "Production Databases:" -ForegroundColor Green
    Write-Host $dbList -ForegroundColor Gray
}

Write-Host ""
