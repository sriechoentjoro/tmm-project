# Test TMM Application Deployment
# This script tests the deployed application functionality

$ErrorActionPreference = "Stop"

$serverUser = "root"
$serverHost = "103.214.112.58"
$appUrl = "http://103.214.112.58"
$appPath = "/var/www/tmm"

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "TMM Application Deployment Test" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Server: $serverHost" -ForegroundColor Yellow
Write-Host "App URL: $appUrl" -ForegroundColor Yellow
Write-Host ""

$testResults = @()

# Test 1: Check if files exist
Write-Host "Test 1: Checking application files..." -ForegroundColor Green
$filesExist = ssh "$serverUser@$serverHost" "test -f $appPath/webroot/index.php && echo 'OK' || echo 'FAIL'"

if ($filesExist -match "OK") {
    Write-Host "  [PASS] Application files exist" -ForegroundColor Green
    $testResults += "Application Files: PASS"
} else {
    Write-Host "  [FAIL] Application files missing" -ForegroundColor Red
    $testResults += "Application Files: FAIL"
}

# Test 2: Check file permissions
Write-Host ""
Write-Host "Test 2: Checking file permissions..." -ForegroundColor Green
$permissions = ssh "$serverUser@$serverHost" "ls -ld $appPath/tmp $appPath/logs"
Write-Host $permissions -ForegroundColor Gray

if ($permissions -match "drwx") {
    Write-Host "  [PASS] Directories have correct permissions" -ForegroundColor Green
    $testResults += "File Permissions: PASS"
} else {
    Write-Host "  [WARN] Check permissions manually" -ForegroundColor Yellow
    $testResults += "File Permissions: WARN"
}

# Test 3: Check database connectivity
Write-Host ""
Write-Host "Test 3: Testing database connectivity..." -ForegroundColor Green
$databases = @("cms_masters", "cms_lpk_candidates", "cms_tmm_trainees", "cms_authentication_authorization")

$dbTestPass = $true
foreach ($db in $databases) {
    $dbTest = ssh "$serverUser@$serverHost" "mysql -u root -e 'USE $db; SELECT 1;' 2>&1"
    if ($LASTEXITCODE -eq 0) {
        Write-Host "  [PASS] $db - Connected" -ForegroundColor Green
    } else {
        Write-Host "  [FAIL] $db - Connection failed" -ForegroundColor Red
        $dbTestPass = $false
    }
}

if ($dbTestPass) {
    $testResults += "Database Connectivity: PASS"
} else {
    $testResults += "Database Connectivity: FAIL"
}

# Test 4: Check nginx status
Write-Host ""
Write-Host "Test 4: Checking nginx status..." -ForegroundColor Green
$nginxStatus = ssh "$serverUser@$serverHost" "systemctl is-active nginx"

if ($nginxStatus -match "active") {
    Write-Host "  [PASS] Nginx is running" -ForegroundColor Green
    $testResults += "Nginx Status: PASS"
} else {
    Write-Host "  [FAIL] Nginx is not running" -ForegroundColor Red
    $testResults += "Nginx Status: FAIL"
}

# Test 5: Check PHP-FPM
Write-Host ""
Write-Host "Test 5: Checking PHP-FPM..." -ForegroundColor Green
$phpFpmStatus = ssh "$serverUser@$serverHost" "systemctl is-active php7.4-fpm 2>&1 || systemctl is-active php-fpm 2>&1"

if ($phpFpmStatus -match "active") {
    Write-Host "  [PASS] PHP-FPM is running" -ForegroundColor Green
    $testResults += "PHP-FPM Status: PASS"
} else {
    Write-Host "  [FAIL] PHP-FPM is not running" -ForegroundColor Red
    $testResults += "PHP-FPM Status: FAIL"
}

# Test 6: Test HTTP response
Write-Host ""
Write-Host "Test 6: Testing HTTP response..." -ForegroundColor Green

try {
    $response = Invoke-WebRequest -Uri $appUrl -TimeoutSec 10 -UseBasicParsing
    $statusCode = $response.StatusCode
    
    Write-Host "  Status Code: $statusCode" -ForegroundColor Cyan
    
    if ($statusCode -eq 200) {
        Write-Host "  [PASS] Application is responding" -ForegroundColor Green
        $testResults += "HTTP Response: PASS (200)"
        
        # Check for CakePHP indicators
        if ($response.Content -match "CakePHP") {
            Write-Host "  [INFO] CakePHP detected in response" -ForegroundColor Cyan
        }
    } else {
        Write-Host "  [WARN] Unexpected status code: $statusCode" -ForegroundColor Yellow
        $testResults += "HTTP Response: WARN ($statusCode)"
    }
} catch {
    Write-Host "  [FAIL] Cannot connect to application" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    $testResults += "HTTP Response: FAIL"
}

# Test 7: Check error logs
Write-Host ""
Write-Host "Test 7: Checking recent error logs..." -ForegroundColor Green
$errorLogs = ssh "$serverUser@$serverHost" "tail -20 $appPath/logs/error.log 2>&1"

if ($errorLogs -match "No such file") {
    Write-Host "  [INFO] No error log file yet (clean installation)" -ForegroundColor Cyan
    $testResults += "Error Logs: OK (empty)"
} elseif ([string]::IsNullOrWhiteSpace($errorLogs)) {
    Write-Host "  [PASS] No recent errors" -ForegroundColor Green
    $testResults += "Error Logs: OK (no errors)"
} else {
    Write-Host "  [WARN] Recent errors found:" -ForegroundColor Yellow
    Write-Host $errorLogs -ForegroundColor Gray
    $testResults += "Error Logs: WARN (has errors)"
}

# Test 8: Check nginx error logs
Write-Host ""
Write-Host "Test 8: Checking nginx error logs..." -ForegroundColor Green
$nginxErrors = ssh "$serverUser@$serverHost" "tail -10 /var/log/nginx/tmm-error.log 2>&1"

if ($nginxErrors -match "No such file") {
    Write-Host "  [INFO] No nginx error log yet" -ForegroundColor Cyan
    $testResults += "Nginx Errors: OK (no log)"
} elseif ([string]::IsNullOrWhiteSpace($nginxErrors)) {
    Write-Host "  [PASS] No nginx errors" -ForegroundColor Green
    $testResults += "Nginx Errors: OK"
} else {
    Write-Host "  [WARN] Nginx errors found:" -ForegroundColor Yellow
    Write-Host $nginxErrors -ForegroundColor Gray
    $testResults += "Nginx Errors: WARN"
}

# Test 9: Check cache directories
Write-Host ""
Write-Host "Test 9: Checking cache directories..." -ForegroundColor Green
$cacheDirs = ssh "$serverUser@$serverHost" "ls -la $appPath/tmp/cache/ 2>&1"

if ($cacheDirs -match "models" -and $cacheDirs -match "persistent") {
    Write-Host "  [PASS] Cache directories exist" -ForegroundColor Green
    $testResults += "Cache Directories: PASS"
} else {
    Write-Host "  [WARN] Cache directories may be missing" -ForegroundColor Yellow
    $testResults += "Cache Directories: WARN"
}

# Summary
Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Test Summary" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

$passCount = ($testResults | Where-Object { $_ -match "PASS" }).Count
$failCount = ($testResults | Where-Object { $_ -match "FAIL" }).Count
$warnCount = ($testResults | Where-Object { $_ -match "WARN" }).Count

foreach ($result in $testResults) {
    if ($result -match "PASS") {
        Write-Host "  [OK] $result" -ForegroundColor Green
    } elseif ($result -match "FAIL") {
        Write-Host "  [X] $result" -ForegroundColor Red
    } else {
        Write-Host "  [!] $result" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "Results: $passCount passed, $failCount failed, $warnCount warnings" -ForegroundColor $(
    if ($failCount -gt 0) { "Red" }
    elseif ($warnCount -gt 0) { "Yellow" }
    else { "Green" }
)

Write-Host ""
Write-Host "Quick Access Commands:" -ForegroundColor Yellow
Write-Host "  View app in browser: $appUrl" -ForegroundColor Cyan
Write-Host "  View error log: ssh $serverUser@$serverHost 'tail -f $appPath/logs/error.log'" -ForegroundColor Cyan
Write-Host "  View nginx log: ssh $serverUser@$serverHost 'tail -f /var/log/nginx/tmm-error.log'" -ForegroundColor Cyan
Write-Host "  Clear cache: ssh $serverUser@$serverHost 'cd $appPath && rm -rf tmp/cache/*/*'" -ForegroundColor Cyan
Write-Host ""

if ($failCount -eq 0) {
    Write-Host "[SUCCESS] All critical tests passed! Application should be working." -ForegroundColor Green
} elseif ($failCount -gt 0) {
    Write-Host "[ATTENTION] Some tests failed. Please review the output above." -ForegroundColor Red
} else {
    Write-Host "[WARNING] Some warnings detected. Application may work but review recommended." -ForegroundColor Yellow
}

Write-Host ""
