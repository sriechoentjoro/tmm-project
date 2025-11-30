# Script to remove BOM (Byte Order Mark) from all PHP files
# BOM causes "Namespace declaration must be first statement" error
# Date: November 28, 2025

$projectRoot = $PSScriptRoot
$logFile = "$projectRoot\fix_bom.log"

# Initialize log
"=== Remove BOM from PHP Files ===" | Out-File $logFile
"Start Time: $(Get-Date)" | Out-File $logFile -Append
"" | Out-File $logFile -Append

$totalFixed = 0
$totalChecked = 0

Write-Host "`n=== Scanning PHP Files for BOM ===" -ForegroundColor Cyan

# Get all PHP files in src directory (excluding vendor and template_backups)
$phpFiles = Get-ChildItem -Path "$projectRoot\src" -Filter "*.php" -Recurse | 
    Where-Object { $_.FullName -notlike "*\vendor\*" -and $_.FullName -notlike "*\template_backups\*" }

foreach ($file in $phpFiles) {
    $totalChecked++
    
    try {
        # Read file as bytes
        $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
        
        # Check for UTF-8 BOM (EF BB BF)
        if ($bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
            Write-Host "  [BOM FOUND] $($file.FullName)" -ForegroundColor Yellow
            "BOM found in: $($file.FullName)" | Out-File $logFile -Append
            
            # Remove BOM (skip first 3 bytes)
            $content = [System.IO.File]::ReadAllText($file.FullName)
            
            # Write back without BOM using UTF8Encoding without BOM
            $utf8NoBom = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
            
            Write-Host "  [FIXED] BOM removed" -ForegroundColor Green
            "  -> Fixed" | Out-File $logFile -Append
            $totalFixed++
        }
    }
    catch {
        Write-Host "  [ERROR] Failed to process $($file.FullName): $_" -ForegroundColor Red
        "ERROR processing $($file.FullName): $_" | Out-File $logFile -Append
    }
}

"" | Out-File $logFile -Append
"=== Summary ===" | Out-File $logFile -Append
"Total files checked: $totalChecked" | Out-File $logFile -Append
"Files with BOM fixed: $totalFixed" | Out-File $logFile -Append
"End Time: $(Get-Date)" | Out-File $logFile -Append

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Total files checked: $totalChecked" -ForegroundColor White
Write-Host "Files with BOM fixed: $totalFixed" -ForegroundColor White

if ($totalFixed -eq 0) {
    Write-Host "`n[OK] No files with BOM found. All clean!" -ForegroundColor Green
} else {
    Write-Host "`n[OK] Fixed $totalFixed file(s). BOM removed successfully!" -ForegroundColor Green
}

Write-Host "`nLog saved to: $logFile" -ForegroundColor Cyan

# Also scan config directory
Write-Host "`n=== Scanning Config Files ===" -ForegroundColor Cyan
$configFiles = Get-ChildItem -Path "$projectRoot\config" -Filter "*.php" -Recurse

foreach ($file in $configFiles) {
    try {
        $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
        
        if ($bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
            Write-Host "  [BOM FOUND] $($file.FullName)" -ForegroundColor Yellow
            
            $content = [System.IO.File]::ReadAllText($file.FullName)
            $utf8NoBom = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
            
            Write-Host "  [FIXED] BOM removed from config" -ForegroundColor Green
            $totalFixed++
        }
    }
    catch {
        Write-Host "  [ERROR] $_" -ForegroundColor Red
    }
}

Write-Host "`n=== All Done ===" -ForegroundColor Green
Write-Host "Total BOM fixes: $totalFixed" -ForegroundColor White
