# Import SQL files from /var/www/html/projects/sql to MySQL
# Server: 103.214.112.58
# Runs on server via SSH

param(
    [string]$Server = "root@103.214.112.58",
    [string]$SqlDir = "/var/www/html/projects/sql"
)

Write-Host "=== Import SQL Files from Server ===" -ForegroundColor Cyan
Write-Host "Server: $Server" -ForegroundColor Yellow
Write-Host "SQL Directory: $SqlDir`n" -ForegroundColor Yellow

# Test SSH Connection
Write-Host "=== Step 1: Test SSH Connection ===" -ForegroundColor Cyan
Write-Host "Testing connection..." -ForegroundColor Yellow

try {
    $testResult = ssh -o ConnectTimeout=10 $Server "echo 'Connection OK'" 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "SSH connection failed"
    }
    Write-Host "Success: SSH connection OK`n" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Cannot connect to server!" -ForegroundColor Red
    Write-Host "Please check:" -ForegroundColor Yellow
    Write-Host "  1. Server is online: ping 103.214.112.58" -ForegroundColor Gray
    Write-Host "  2. SSH service is running" -ForegroundColor Gray
    Write-Host "  3. SSH keys are configured" -ForegroundColor Gray
    exit 1
}

# List SQL Files
Write-Host "=== Step 2: List SQL Files ===" -ForegroundColor Cyan
Write-Host "Scanning $SqlDir for SQL files...`n" -ForegroundColor Yellow

$sqlFilesList = ssh $Server "find $SqlDir -type f -name '*.sql' 2>/dev/null | sort"

if ([string]::IsNullOrWhiteSpace($sqlFilesList)) {
    Write-Host "ERROR: No SQL files found in $SqlDir" -ForegroundColor Red
    Write-Host "`nTroubleshooting:" -ForegroundColor Yellow
    Write-Host "  ssh $Server 'ls -la /var/www/html/projects/'" -ForegroundColor Gray
    Write-Host "  ssh $Server 'ls -la $SqlDir'" -ForegroundColor Gray
    exit 1
}

$fileArray = $sqlFilesList -split "`n" | Where-Object { $_ -ne "" }
Write-Host "Found $($fileArray.Count) SQL file(s):" -ForegroundColor Green
foreach ($file in $fileArray) {
    $fileName = Split-Path $file -Leaf
    Write-Host "  - $fileName" -ForegroundColor Gray
}

# Create bash import script
Write-Host "`n=== Step 3: Prepare Import Script ===" -ForegroundColor Cyan

$bashScript = @'
#!/bin/bash
MYSQL_USER="root"
SQL_DIR="/var/www/html/projects/sql"
LOG_FILE="/tmp/sql_import_$(date +%Y%m%d_%H%M%S).log"

echo "=== SQL Import Started ===" | tee $LOG_FILE
echo "" | tee -a $LOG_FILE

read -s -p "Enter MySQL root password: " MYSQL_PASS
echo ""
echo "" | tee -a $LOG_FILE

echo "Testing MySQL connection..." | tee -a $LOG_FILE
mysql -u$MYSQL_USER -p$MYSQL_PASS -e "SELECT 1;" > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "ERROR: Cannot connect to MySQL!" | tee -a $LOG_FILE
    exit 1
fi
echo "MySQL connection OK" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

SQL_FILES=$(find $SQL_DIR -type f -name "*.sql" | sort)
TOTAL=$(echo "$SQL_FILES" | wc -l)
echo "Found $TOTAL SQL files" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

SUCCESS=0
FAILED=0

for SQL_FILE in $SQL_FILES; do
    FILENAME=$(basename $SQL_FILE)
    echo "========================================" | tee -a $LOG_FILE
    echo "Processing: $FILENAME" | tee -a $LOG_FILE
    
    DB_NAME=$(basename $SQL_FILE .sql | sed 's/_[0-9]\{8\}.*//' | sed 's/_backup.*//')
    echo "Target database: $DB_NAME" | tee -a $LOG_FILE
    
    DB_EXISTS=$(mysql -u$MYSQL_USER -p$MYSQL_PASS -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null | grep -c "$DB_NAME")
    
    if [ $DB_EXISTS -eq 0 ]; then
        echo "Creating database $DB_NAME..." | tee -a $LOG_FILE
        mysql -u$MYSQL_USER -p$MYSQL_PASS -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | tee -a $LOG_FILE
    fi
    
    echo "Importing..." | tee -a $LOG_FILE
    START=$(date +%s)
    
    mysql -u$MYSQL_USER -p$MYSQL_PASS $DB_NAME < "$SQL_FILE" 2>&1 | tee -a $LOG_FILE
    
    if [ ${PIPESTATUS[0]} -eq 0 ]; then
        END=$(date +%s)
        ELAPSED=$((END - START))
        echo "SUCCESS: Imported in $ELAPSED seconds" | tee -a $LOG_FILE
        ((SUCCESS++))
    else
        echo "FAILED: Import error" | tee -a $LOG_FILE
        ((FAILED++))
    fi
    echo "" | tee -a $LOG_FILE
done

echo "========================================" | tee -a $LOG_FILE
echo "SUMMARY:" | tee -a $LOG_FILE
echo "Total: $TOTAL" | tee -a $LOG_FILE
echo "Success: $SUCCESS" | tee -a $LOG_FILE
echo "Failed: $FAILED" | tee -a $LOG_FILE
echo "Log: $LOG_FILE" | tee -a $LOG_FILE

if [ $FAILED -eq 0 ]; then
    echo "" | tee -a $LOG_FILE
    echo "All imports completed successfully!" | tee -a $LOG_FILE
    exit 0
else
    echo "" | tee -a $LOG_FILE
    echo "Some imports failed - check log" | tee -a $LOG_FILE
    exit 1
fi
'@

# Save to temp file
$tempScript = [System.IO.Path]::GetTempFileName()
$tempScript = $tempScript -replace '\.tmp$', '.sh'
[System.IO.File]::WriteAllText($tempScript, $bashScript, [System.Text.Encoding]::UTF8)

Write-Host "Uploading import script..." -ForegroundColor Yellow
scp $tempScript "${Server}:/tmp/import_sql.sh" 2>&1 | Out-Null

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Failed to upload script" -ForegroundColor Red
    Remove-Item $tempScript -Force
    exit 1
}

Write-Host "Success: Script uploaded`n" -ForegroundColor Green

# Cleanup local temp
Remove-Item $tempScript -Force

# Execute import
Write-Host "=== Step 4: Execute Import ===" -ForegroundColor Cyan
Write-Host "Starting import process..." -ForegroundColor Yellow
Write-Host "(You will be prompted for MySQL password)`n" -ForegroundColor Cyan

ssh -t $Server "chmod +x /tmp/import_sql.sh && /tmp/import_sql.sh"

$exitCode = $LASTEXITCODE

Write-Host "`n=== Import Complete ===" -ForegroundColor Cyan

if ($exitCode -eq 0) {
    Write-Host "Success: All databases imported!" -ForegroundColor Green
} else {
    Write-Host "Warning: Some imports may have failed" -ForegroundColor Yellow
}

Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "1. View log: ssh $Server 'cat /tmp/sql_import_*.log | tail -50'" -ForegroundColor Gray
Write-Host "2. Check databases: ssh $Server 'mysql -u root -p -e ""SHOW DATABASES;""'" -ForegroundColor Gray
Write-Host "3. Access phpMyAdmin: http://103.214.112.58/phpmyadmin" -ForegroundColor Gray

Write-Host "`nDone!" -ForegroundColor Green
