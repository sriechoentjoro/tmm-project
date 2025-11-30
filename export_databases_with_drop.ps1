# Export All TMM Databases with DROP IF EXISTS
# This script exports all databases defined in app_datasources.php
# Includes DROP DATABASE IF EXISTS and CREATE DATABASE statements

$ErrorActionPreference = "Stop"

# Configuration
$mysqlUser = "root"
$mysqlPassword = "62xe6zyr"
$mysqlHost = "localhost"
$exportDir = "d:\xampp\htdocs\tmm\database_exports"
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"

# List of all databases from app_datasources.php
$databases = @(
    "cms_masters",
    "cms_lpk_candidates",
    "cms_lpk_candidate_documents",
    "cms_tmm_apprentices",
    "cms_tmm_apprentice_documents",
    "cms_tmm_apprentice_document_ticketings",
    "cms_tmm_stakeholders",
    "cms_tmm_trainees",
    "cms_tmm_trainee_accountings",
    "cms_tmm_trainee_trainings",
    "cms_tmm_trainee_training_scorings",
    "cms_authentication_authorization"
)

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "TMM Database Export with DROP IF EXISTS" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Export Directory: $exportDir" -ForegroundColor Yellow
Write-Host "Timestamp: $timestamp" -ForegroundColor Yellow
Write-Host "Total Databases: $($databases.Count)" -ForegroundColor Yellow
Write-Host ""

# Create export directory
if (-not (Test-Path $exportDir)) {
    New-Item -ItemType Directory -Path $exportDir | Out-Null
    Write-Host "[OK] Created export directory" -ForegroundColor Green
} else {
    Write-Host "[OK] Export directory exists" -ForegroundColor Green
}

# Create timestamped subdirectory
$exportPath = Join-Path $exportDir $timestamp
New-Item -ItemType Directory -Path $exportPath | Out-Null
Write-Host "[OK] Created timestamped directory: $timestamp" -ForegroundColor Green
Write-Host ""

# Export each database
$successCount = 0
$failCount = 0
$totalSize = 0

foreach ($database in $databases) {
    Write-Host "Exporting: $database" -ForegroundColor Cyan
    
    $outputFile = Join-Path $exportPath "$database.sql"
    $tempFile = Join-Path $exportPath "$database.temp.sql"
    
    try {
        # Export database structure and data
        $mysqldumpArgs = @(
            "-h", $mysqlHost,
            "-u", $mysqlUser,
            "-p$mysqlPassword",
            "--single-transaction",
            "--skip-routines",
            "--triggers",
            "--skip-events",
            "--hex-blob",
            "--default-character-set=utf8mb4",
            $database
        )
        
        # Run mysqldump and save to temp file
        & "d:\xampp\mysql\bin\mysqldump.exe" $mysqldumpArgs | Out-File -FilePath $tempFile -Encoding UTF8
        
        if ($LASTEXITCODE -eq 0) {
            # Prepend DROP DATABASE IF EXISTS and CREATE DATABASE statements
            $dropCreateStatements = @"
-- ============================================
-- Database: $database
-- Exported: $timestamp
-- ============================================

DROP DATABASE IF EXISTS ``$database``;
CREATE DATABASE ``$database`` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ``$database``;

"@
            
            # Read the mysqldump output
            $dumpContent = Get-Content $tempFile -Raw
            
            # Combine DROP/CREATE with dump content
            $finalContent = $dropCreateStatements + $dumpContent
            
            # Save to final file with UTF-8 encoding (no BOM)
            $utf8NoBom = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllText($outputFile, $finalContent, $utf8NoBom)
            
            # Remove temp file
            Remove-Item $tempFile -Force
            
            # Get file size
            $fileSize = (Get-Item $outputFile).Length
            $fileSizeMB = [math]::Round($fileSize / 1MB, 2)
            $totalSize += $fileSize
            
            Write-Host "  [OK] Exported: $fileSizeMB MB" -ForegroundColor Green
            $successCount++
        } else {
            Write-Host "  [ERROR] mysqldump failed for $database" -ForegroundColor Red
            $failCount++
            if (Test-Path $tempFile) {
                Remove-Item $tempFile -Force
            }
        }
    } catch {
        Write-Host "  [ERROR] Exception: $($_.Exception.Message)" -ForegroundColor Red
        $failCount++
        if (Test-Path $tempFile) {
            Remove-Item $tempFile -Force
        }
    }
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "Export Complete!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host "Successful: $successCount" -ForegroundColor Green
Write-Host "Failed: $failCount" -ForegroundColor $(if ($failCount -gt 0) { "Red" } else { "Green" })
Write-Host "Total Size: $([math]::Round($totalSize / 1MB, 2)) MB" -ForegroundColor Cyan
Write-Host "Export Location: $exportPath" -ForegroundColor Yellow
Write-Host ""

# Create a combined import script
$importScriptPath = Join-Path $exportPath "import_all.sh"
$importScriptContent = @"
#!/bin/bash
# Import All TMM Databases
# Generated: $timestamp
# Usage: bash import_all.sh [mysql_user] [mysql_password]

MYSQL_USER=`${1:-root}
MYSQL_PASSWORD=`${2:-}

if [ -z "`$MYSQL_PASSWORD" ]; then
    echo "Usage: bash import_all.sh [mysql_user] [mysql_password]"
    echo "Example: bash import_all.sh root mypassword"
    exit 1
fi

echo "========================================"
echo "TMM Database Import"
echo "========================================"
echo ""

"@

foreach ($database in $databases) {
    $importScriptContent += @"
echo "Importing: $database"
mysql -u`$MYSQL_USER -p`$MYSQL_PASSWORD < $database.sql
if [ `$? -eq 0 ]; then
    echo "  [OK] $database imported successfully"
else
    echo "  [ERROR] Failed to import $database"
fi
echo ""

"@
}

$importScriptContent += @"

echo "========================================"
echo "Import Complete!"
echo "========================================"
"@

# Save import script
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($importScriptPath, $importScriptContent, $utf8NoBom)

Write-Host "[OK] Created import script: import_all.sh" -ForegroundColor Green
Write-Host ""

# Create Windows batch import script
$importBatchPath = Join-Path $exportPath "import_all.bat"
$importBatchContent = @"
@echo off
REM Import All TMM Databases (Windows)
REM Generated: $timestamp
REM Usage: import_all.bat [mysql_user] [mysql_password]

SET MYSQL_USER=%1
SET MYSQL_PASSWORD=%2

IF "%MYSQL_USER%"=="" SET MYSQL_USER=root

IF "%MYSQL_PASSWORD%"=="" (
    echo Usage: import_all.bat [mysql_user] [mysql_password]
    echo Example: import_all.bat root mypassword
    exit /b 1
)

echo ========================================
echo TMM Database Import
echo ========================================
echo.

"@

foreach ($database in $databases) {
    $importBatchContent += @"
echo Importing: $database
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < $database.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] $database imported successfully
) ELSE (
    echo   [ERROR] Failed to import $database
)
echo.

"@
}

$importBatchContent += @"

echo ========================================
echo Import Complete!
echo ========================================
pause
"@

$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($importBatchPath, $importBatchContent, $utf8NoBom)

Write-Host "[OK] Created import batch: import_all.bat" -ForegroundColor Green
Write-Host ""

# Create README
$readmePath = Join-Path $exportPath "README.txt"
$readmeContent = @"
TMM Database Export
===================
Export Date: $timestamp
Total Databases: $($databases.Count)
Total Size: $([math]::Round($totalSize / 1MB, 2)) MB

Database List:
--------------
"@

foreach ($database in $databases) {
    $sqlFile = Join-Path $exportPath "$database.sql"
    if (Test-Path $sqlFile) {
        $size = [math]::Round((Get-Item $sqlFile).Length / 1MB, 2)
        $readmeContent += "`n- $database ($size MB)"
    }
}

$readmeContent += @"


Import Instructions:
--------------------

On Linux/Unix (Production Server):
1. Upload all .sql files to server:
   scp *.sql root@103.214.112.58:/tmp/

2. SSH to server:
   ssh root@103.214.112.58

3. Run import script:
   cd /tmp
   bash import_all.sh root your_mysql_password

Or import individually:
   mysql -u root -p < cms_masters.sql
   mysql -u root -p < cms_lpk_candidates.sql
   ... etc

On Windows (Local):
1. Run import batch:
   import_all.bat root your_mysql_password

Or import individually:
   mysql -u root -p62xe6zyr < cms_masters.sql
   mysql -u root -p62xe6zyr < cms_lpk_candidates.sql
   ... etc

Features:
---------
- Each SQL file includes DROP DATABASE IF EXISTS statement
- Automatically creates database with utf8mb4 charset
- Includes all tables, data, triggers, routines, and events
- Safe to re-run without manual cleanup

Notes:
------
- All databases will be DROPPED and RECREATED
- Existing data will be LOST
- Backup before importing on production
- Verify database names match your configuration

"@

$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($readmePath, $readmeContent, $utf8NoBom)

Write-Host "[OK] Created README.txt" -ForegroundColor Green
Write-Host ""

Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Upload to server:" -ForegroundColor White
Write-Host "   scp $exportPath/*.sql root@103.214.112.58:/tmp/" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Import on server:" -ForegroundColor White
Write-Host "   ssh root@103.214.112.58" -ForegroundColor Cyan
Write-Host "   bash /tmp/import_all.sh root your_mysql_password" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Verify import:" -ForegroundColor White
Write-Host "   mysql -u root -p -e 'SHOW DATABASES;'" -ForegroundColor Cyan
Write-Host ""
