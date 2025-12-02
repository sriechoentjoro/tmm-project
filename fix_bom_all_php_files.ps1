# Script to remove BOM (Byte Order Mark) from all PHP files
# BOM causes "Namespace declaration must be first statement" error
# Date: November 16, 2025

$projectRoot = "d:\xampp\htdocs\tmm"
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
        # Read file as bytes to check for BOM
        $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
        
        # Check for UTF-8 BOM (EF BB BF)
        $hasBOM = ($bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF)
        
        if ($hasBOM) {
            # Read content and write without BOM
            $content = Get-Content $file.FullName -Raw
            $utf8NoBom = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
            
            $totalFixed++
            $relativePath = $file.FullName.Replace($projectRoot, "")
            Write-Host "[FIXED] $relativePath" -ForegroundColor Green
            "[FIXED] $($file.FullName)" | Out-File $logFile -Append
        }
    } catch {
        $relativePath = $file.FullName.Replace($projectRoot, "")
        Write-Host "[ERROR] $relativePath : $_" -ForegroundColor Red
        "[ERROR] $($file.FullName) : $_" | Out-File $logFile -Append
    }
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Total files checked: $totalChecked" -ForegroundColor White
Write-Host "Files with BOM fixed: $totalFixed" -ForegroundColor Green
Write-Host "Files without BOM: $($totalChecked - $totalFixed)" -ForegroundColor White

"" | Out-File $logFile -Append
"=== Summary ===" | Out-File $logFile -Append
"Total files checked: $totalChecked" | Out-File $logFile -Append
"Files with BOM fixed: $totalFixed" | Out-File $logFile -Append
"End Time: $(Get-Date)" | Out-File $logFile -Append

if ($totalFixed -gt 0) {
    Write-Host "`n[OK] BOM removed from $totalFixed file(s)" -ForegroundColor Green
    Write-Host "Run: bin\cake cache clear_all" -ForegroundColor Yellow
} else {
    Write-Host "`n[OK] No files with BOM found. All clean!" -ForegroundColor Green
}

Write-Host "`nLog saved to: $logFile" -ForegroundColor Cyan
