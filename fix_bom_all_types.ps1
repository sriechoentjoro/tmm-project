# Comprehensive BOM Removal Script
# Removes UTF-8 BOM from PHP, CTP, CSS, JS, and Bake template files
# Date: November 28, 2025

$projectRoot = $PSScriptRoot
$logFile = "$projectRoot\fix_bom_comprehensive.log"

# Initialize log
"=== Comprehensive BOM Removal ===" | Out-File $logFile
"Start Time: $(Get-Date)" | Out-File $logFile -Append
"" | Out-File $logFile -Append

$totalFixed = 0
$totalChecked = 0
$filesByType = @{}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  COMPREHENSIVE BOM REMOVAL TOOL" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# Function to check and fix BOM
function Remove-BOMFromFile {
    param (
        [string]$FilePath,
        [string]$FileType
    )
    
    try {
        # Read file as bytes
        $bytes = [System.IO.File]::ReadAllBytes($FilePath)
        
        # Check for UTF-8 BOM (EF BB BF)
        if ($bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
            $relativePath = $FilePath.Replace($projectRoot, ".")
            Write-Host "  [BOM FOUND] $relativePath" -ForegroundColor Yellow
            "[$FileType] BOM found: $relativePath" | Out-File $logFile -Append
            
            # Remove BOM
            $content = [System.IO.File]::ReadAllText($FilePath)
            
            # Write back without BOM
            $utf8NoBom = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllText($FilePath, $content, $utf8NoBom)
            
            Write-Host "  [FIXED]" -ForegroundColor Green
            "  -> Fixed successfully" | Out-File $logFile -Append
            
            return $true
        }
        return $false
    }
    catch {
        Write-Host "  [ERROR] Failed: $_" -ForegroundColor Red
        "ERROR: $FilePath - $_" | Out-File $logFile -Append
        return $false
    }
}

# 1. Scan PHP Files
Write-Host ""
Write-Host "[1/6] Scanning PHP Files..." -ForegroundColor Cyan
$phpFiles = Get-ChildItem -Path "$projectRoot\src" -Filter "*.php" -Recurse -ErrorAction SilentlyContinue | 
    Where-Object { $_.FullName -notlike "*\vendor\*" }

$phpFixed = 0
foreach ($file in $phpFiles) {
    $totalChecked++
    if (Remove-BOMFromFile -FilePath $file.FullName -FileType "PHP") {
        $phpFixed++
        $totalFixed++
    }
}
$filesByType["PHP"] = @{ Checked = $phpFiles.Count; Fixed = $phpFixed }
Write-Host "  Checked: $($phpFiles.Count) | Fixed: $phpFixed" -ForegroundColor White

# 2. Scan CTP Template Files
Write-Host ""
Write-Host "[2/6] Scanning CTP Template Files..." -ForegroundColor Cyan
$ctpFiles = Get-ChildItem -Path "$projectRoot\src\Template" -Filter "*.ctp" -Recurse -ErrorAction SilentlyContinue

$ctpFixed = 0
foreach ($file in $ctpFiles) {
    $totalChecked++
    if (Remove-BOMFromFile -FilePath $file.FullName -FileType "CTP") {
        $ctpFixed++
        $totalFixed++
    }
}
$filesByType["CTP"] = @{ Checked = $ctpFiles.Count; Fixed = $ctpFixed }
Write-Host "  Checked: $($ctpFiles.Count) | Fixed: $ctpFixed" -ForegroundColor White

# 3. Scan Bake Template Files
Write-Host ""
Write-Host "[3/6] Scanning Bake Template Files..." -ForegroundColor Cyan
$bakeFiles = @()
if (Test-Path "$projectRoot\src\Template\Bake") {
    $bakeFiles = Get-ChildItem -Path "$projectRoot\src\Template\Bake" -Filter "*.ctp" -Recurse -ErrorAction SilentlyContinue
}

$bakeFixed = 0
foreach ($file in $bakeFiles) {
    $totalChecked++
    if (Remove-BOMFromFile -FilePath $file.FullName -FileType "BAKE") {
        $bakeFixed++
        $totalFixed++
    }
}
$filesByType["BAKE"] = @{ Checked = $bakeFiles.Count; Fixed = $bakeFixed }
Write-Host "  Checked: $($bakeFiles.Count) | Fixed: $bakeFixed" -ForegroundColor White

# 4. Scan CSS Files
Write-Host ""
Write-Host "[4/6] Scanning CSS Files..." -ForegroundColor Cyan
$cssFiles = @()
if (Test-Path "$projectRoot\webroot\css") {
    $cssFiles = Get-ChildItem -Path "$projectRoot\webroot\css" -Filter "*.css" -Recurse -ErrorAction SilentlyContinue
}

$cssFixed = 0
foreach ($file in $cssFiles) {
    $totalChecked++
    if (Remove-BOMFromFile -FilePath $file.FullName -FileType "CSS") {
        $cssFixed++
        $totalFixed++
    }
}
$filesByType["CSS"] = @{ Checked = $cssFiles.Count; Fixed = $cssFixed }
Write-Host "  Checked: $($cssFiles.Count) | Fixed: $cssFixed" -ForegroundColor White

# 5. Scan JavaScript Files
Write-Host ""
Write-Host "[5/6] Scanning JavaScript Files..." -ForegroundColor Cyan
$jsFiles = @()
if (Test-Path "$projectRoot\webroot\js") {
    $jsFiles = Get-ChildItem -Path "$projectRoot\webroot\js" -Filter "*.js" -Recurse -ErrorAction SilentlyContinue
}

$jsFixed = 0
foreach ($file in $jsFiles) {
    $totalChecked++
    if (Remove-BOMFromFile -FilePath $file.FullName -FileType "JS") {
        $jsFixed++
        $totalFixed++
    }
}
$filesByType["JS"] = @{ Checked = $jsFiles.Count; Fixed = $jsFixed }
Write-Host "  Checked: $($jsFiles.Count) | Fixed: $jsFixed" -ForegroundColor White

# 6. Scan Config Files
Write-Host ""
Write-Host "[6/6] Scanning Config Files..." -ForegroundColor Cyan
$configFiles = @()
if (Test-Path "$projectRoot\config") {
    $configFiles = Get-ChildItem -Path "$projectRoot\config" -Filter "*.php" -Recurse -ErrorAction SilentlyContinue
}

$configFixed = 0
foreach ($file in $configFiles) {
    $totalChecked++
    if (Remove-BOMFromFile -FilePath $file.FullName -FileType "CONFIG") {
        $configFixed++
        $totalFixed++
    }
}
$filesByType["CONFIG"] = @{ Checked = $configFiles.Count; Fixed = $configFixed }
Write-Host "  Checked: $($configFiles.Count) | Fixed: $configFixed" -ForegroundColor White

# Write summary to log
"" | Out-File $logFile -Append
"=== SUMMARY BY FILE TYPE ===" | Out-File $logFile -Append
foreach ($type in $filesByType.Keys) {
    $stats = $filesByType[$type]
    "$type Files - Checked: $($stats.Checked), Fixed: $($stats.Fixed)" | Out-File $logFile -Append
}
"" | Out-File $logFile -Append
"=== GRAND TOTAL ===" | Out-File $logFile -Append
"Total files checked: $totalChecked" | Out-File $logFile -Append
"Total files with BOM fixed: $totalFixed" | Out-File $logFile -Append
"End Time: $(Get-Date)" | Out-File $logFile -Append

# Display final summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "           SUMMARY REPORT" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "By File Type:" -ForegroundColor White
Write-Host "----------------------------------------" -ForegroundColor Gray

foreach ($type in ($filesByType.Keys | Sort-Object)) {
    $stats = $filesByType[$type]
    $line = "  $type : $($stats.Checked) checked, $($stats.Fixed) fixed"
    
    if ($stats.Fixed -gt 0) {
        Write-Host $line -ForegroundColor Yellow
    } else {
        Write-Host $line -ForegroundColor White
    }
}

Write-Host "----------------------------------------" -ForegroundColor Gray
Write-Host ""
Write-Host "Grand Total:" -ForegroundColor White
Write-Host "  Total files checked: $totalChecked" -ForegroundColor Cyan
if ($totalFixed -gt 0) {
    Write-Host "  Files with BOM fixed: $totalFixed" -ForegroundColor Yellow
} else {
    Write-Host "  Files with BOM fixed: $totalFixed" -ForegroundColor Green
}

Write-Host ""
if ($totalFixed -eq 0) {
    Write-Host "[OK] No files with BOM found. All clean!" -ForegroundColor Green
} else {
    Write-Host "[OK] Fixed $totalFixed file(s). BOM removed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "IMPORTANT: Run 'bin\cake cache clear_all' now" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Log saved to: $logFile" -ForegroundColor Cyan
Write-Host ""
