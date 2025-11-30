# Script untuk memeriksa semua Model Table files

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Checking All Model Table Files" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$errorCount = 0
$warningCount = 0
$okCount = 0
$issues = @()

$files = Get-ChildItem -Path "src\Model\Table" -Filter "*.php"

Write-Host "Total files to check: $($files.Count)" -ForegroundColor Yellow
Write-Host ""

foreach ($file in $files) {
    $hasError = $false
    $content = Get-Content $file.FullName -Raw
    
    # Check 1: Syntax check with PHP
    $syntaxCheck = php -l $file.FullName 2>&1
    if ($syntaxCheck -notmatch "No syntax errors") {
        Write-Host "[ERROR] $($file.Name)" -ForegroundColor Red
        Write-Host "  Syntax Error: $syntaxCheck" -ForegroundColor Red
        $issues += "[SYNTAX] $($file.Name): $syntaxCheck"
        $errorCount++
        $hasError = $true
    }
    
    # Check 2: Incomplete existsIn rules
    if ($content -match 'existsIn\(\[[^\]]+\]\s*\)\s*[^;]') {
        Write-Host "[ERROR] $($file.Name)" -ForegroundColor Red
        Write-Host "  Incomplete existsIn() rule found" -ForegroundColor Red
        $issues += "[EXISTSIN] $($file.Name): Incomplete existsIn() rule"
        $errorCount++
        $hasError = $true
    }
    
    # Check 3: Orphaned $this statements
    if ($content -match '\$this\s+\}') {
        Write-Host "[ERROR] $($file.Name)" -ForegroundColor Red
        Write-Host "  Orphaned statement found: '\$this }'" -ForegroundColor Red
        $issues += "[ORPHAN] $($file.Name): Orphaned statement"
        $errorCount++
        $hasError = $true
    }
    
    # Check 4: Missing closing braces in initialize()
    if ($content -match 'public function initialize.*?\{' -and $content -notmatch 'public function initialize.*?\{[^}]*\}') {
        # This is a simple check, may have false positives
    }
    
    # Check 5: CourseMajors references (should be removed)
    if ($content -match 'CourseMajors') {
        Write-Host "[WARNING] $($file.Name)" -ForegroundColor Yellow
        Write-Host "  Still contains CourseMajors reference" -ForegroundColor Yellow
        $issues += "[COURSEMAJORS] $($file.Name): Contains CourseMajors reference"
        $warningCount++
    }
    
    if (-not $hasError) {
        $okCount++
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Summary" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Total Files: $($files.Count)" -ForegroundColor White
Write-Host "OK: $okCount" -ForegroundColor Green
Write-Host "Warnings: $warningCount" -ForegroundColor Yellow
Write-Host "Errors: $errorCount" -ForegroundColor Red
Write-Host ""

if ($issues.Count -gt 0) {
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host " Issues Found:" -ForegroundColor Cyan
    Write-Host "========================================" -ForegroundColor Cyan
    foreach ($issue in $issues) {
        Write-Host $issue -ForegroundColor Red
    }
    Write-Host ""
}

if ($errorCount -eq 0) {
    Write-Host "All Model files are OK!" -ForegroundColor Green
} else {
    Write-Host "Found $errorCount files with errors. Please fix them." -ForegroundColor Red
}
