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

Write-Host "`n╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║         COMPREHENSIVE BOM REMOVAL TOOL                    ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan

# Function to check and fix BOM
function Fix-BOM {
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
            
            # Write back without BOM using UTF8Encoding without BOM
            $utf8NoBom = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllText($FilePath, $content, $utf8NoBom)
            
            Write-Host "  [FIXED] ✓" -ForegroundColor Green
            "  -> Fixed successfully" | Out-File $logFile -Append
            
            return $true
        }
        return $false
    }
    catch {
        Write-Host "  [ERROR] Failed to process: $_" -ForegroundColor Red
        "ERROR: $FilePath - $_" | Out-File $logFile -Append
        return $false
    }
}

# 1. Scan PHP Files
Write-Host "`n[1/5] Scanning PHP Files..." -ForegroundColor Cyan
$phpFiles = Get-ChildItem -Path "$projectRoot\src" -Filter "*.php" -Recurse -ErrorAction SilentlyContinue | 
    Where-Object { $_.FullName -notlike "*\vendor\*" }

$phpFixed = 0
foreach ($file in $phpFiles) {
    $totalChecked++
    if (Fix-BOM -FilePath $file.FullName -FileType "PHP") {
        $phpFixed++
        $totalFixed++
    }
}
$filesByType["PHP"] = @{ Checked = $phpFiles.Count; Fixed = $phpFixed }
Write-Host "  Checked: $($phpFiles.Count) files | Fixed: $phpFixed files" -ForegroundColor White

# 2. Scan CTP Template Files
Write-Host "`n[2/5] Scanning CTP Template Files..." -ForegroundColor Cyan
$ctpFiles = Get-ChildItem -Path "$projectRoot\src\Template" -Filter "*.ctp" -Recurse -ErrorAction SilentlyContinue

$ctpFixed = 0
foreach ($file in $ctpFiles) {
    $totalChecked++
    if (Fix-BOM -FilePath $file.FullName -FileType "CTP") {
        $ctpFixed++
        $totalFixed++
    }
}
$filesByType["CTP"] = @{ Checked = $ctpFiles.Count; Fixed = $ctpFixed }
Write-Host "  Checked: $($ctpFiles.Count) files | Fixed: $ctpFixed files" -ForegroundColor White

# 3. Scan Bake Template Files
Write-Host "`n[3/5] Scanning Bake Template Files..." -ForegroundColor Cyan
$bakeFiles = @()
if (Test-Path "$projectRoot\src\Template\Bake") {
    $bakeFiles = Get-ChildItem -Path "$projectRoot\src\Template\Bake" -Filter "*.ctp" -Recurse -ErrorAction SilentlyContinue
}

$bakeFixed = 0
foreach ($file in $bakeFiles) {
    $totalChecked++
    if (Fix-BOM -FilePath $file.FullName -FileType "BAKE") {
        $bakeFixed++
        $totalFixed++
    }
}
$filesByType["BAKE"] = @{ Checked = $bakeFiles.Count; Fixed = $bakeFixed }
Write-Host "  Checked: $($bakeFiles.Count) files | Fixed: $bakeFixed files" -ForegroundColor White

# 4. Scan CSS Files
Write-Host "`n[4/5] Scanning CSS Files..." -ForegroundColor Cyan
$cssFiles = @()
if (Test-Path "$projectRoot\webroot\css") {
    $cssFiles = Get-ChildItem -Path "$projectRoot\webroot\css" -Filter "*.css" -Recurse -ErrorAction SilentlyContinue
}

$cssFixed = 0
foreach ($file in $cssFiles) {
    $totalChecked++
    if (Fix-BOM -FilePath $file.FullName -FileType "CSS") {
        $cssFixed++
        $totalFixed++
    }
}
$filesByType["CSS"] = @{ Checked = $cssFiles.Count; Fixed = $cssFixed }
Write-Host "  Checked: $($cssFiles.Count) files | Fixed: $cssFixed files" -ForegroundColor White

# 5. Scan JavaScript Files
Write-Host "`n[5/5] Scanning JavaScript Files..." -ForegroundColor Cyan
$jsFiles = @()
if (Test-Path "$projectRoot\webroot\js") {
    $jsFiles = Get-ChildItem -Path "$projectRoot\webroot\js" -Filter "*.js" -Recurse -ErrorAction SilentlyContinue
}

$jsFixed = 0
foreach ($file in $jsFiles) {
    $totalChecked++
    if (Fix-BOM -FilePath $file.FullName -FileType "JS") {
        $jsFixed++
        $totalFixed++
    }
}
$filesByType["JS"] = @{ Checked = $jsFiles.Count; Fixed = $jsFixed }
Write-Host "  Checked: $($jsFiles.Count) files | Fixed: $jsFixed files" -ForegroundColor White

# 6. Scan Config Files
Write-Host "`n[6/6] Scanning Config Files..." -ForegroundColor Cyan
$configFiles = @()
if (Test-Path "$projectRoot\config") {
    $configFiles = Get-ChildItem -Path "$projectRoot\config" -Filter "*.php" -Recurse -ErrorAction SilentlyContinue
}

$configFixed = 0
foreach ($file in $configFiles) {
    $totalChecked++
    if (Fix-BOM -FilePath $file.FullName -FileType "CONFIG") {
        $configFixed++
        $totalFixed++
    }
}
$filesByType["CONFIG"] = @{ Checked = $configFiles.Count; Fixed = $configFixed }
Write-Host "  Checked: $($configFiles.Count) files | Fixed: $configFixed files" -ForegroundColor White

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
Write-Host "`n╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                    SUMMARY REPORT                          ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan

Write-Host "`nBy File Type:" -ForegroundColor White
Write-Host "┌─────────────┬──────────────┬──────────────┐" -ForegroundColor Gray
Write-Host "│  File Type  │   Checked    │    Fixed     │" -ForegroundColor Gray
Write-Host "├─────────────┼──────────────┼──────────────┤" -ForegroundColor Gray

foreach ($type in $filesByType.Keys | Sort-Object) {
    $stats = $filesByType[$type]
    $typeFormatted = $type.PadRight(11)
    $checkedFormatted = $stats.Checked.ToString().PadLeft(12)
    $fixedFormatted = $stats.Fixed.ToString().PadLeft(12)
    
    if ($stats.Fixed -gt 0) {
        Write-Host "│ $typeFormatted │$checkedFormatted │$fixedFormatted │" -ForegroundColor Yellow
    } else {
        Write-Host "│ $typeFormatted │$checkedFormatted │$fixedFormatted │" -ForegroundColor White
    }
}

Write-Host "└─────────────┴──────────────┴──────────────┘" -ForegroundColor Gray

Write-Host "`nGrand Total:" -ForegroundColor White
Write-Host "  Total files checked: " -NoNewline -ForegroundColor White
Write-Host "$totalChecked" -ForegroundColor Cyan
Write-Host "  Files with BOM fixed: " -NoNewline -ForegroundColor White
if ($totalFixed -gt 0) {
    Write-Host "$totalFixed" -ForegroundColor Yellow
} else {
    Write-Host "$totalFixed" -ForegroundColor Green
}

if ($totalFixed -eq 0) {
    Write-Host "`n✓ SUCCESS: No files with BOM found. All clean!" -ForegroundColor Green
} else {
    Write-Host "`n✓ SUCCESS: Fixed $totalFixed file(s). BOM removed successfully!" -ForegroundColor Green
    Write-Host "`nℹ️  IMPORTANT: Run 'bin\cake cache clear_all' to apply changes" -ForegroundColor Yellow
}

Write-Host "`n📄 Log saved to: $logFile" -ForegroundColor Cyan
Write-Host "`n" -ForegroundColor White
