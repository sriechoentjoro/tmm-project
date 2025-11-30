Write-Host "=== Comprehensive BOM Fix ===" -ForegroundColor Cyan

$paths = @(
    'src\Template',
    'src\Controller', 
    'src\View',
    'src\Model',
    'config'
)

$fixed = 0
$clean = 0
$errors = 0

foreach($p in $paths) {
    $fullPath = Join-Path 'd:\xampp\htdocs\project_tmm' $p
    if(Test-Path $fullPath) {
        Write-Host "`nScanning $p..." -ForegroundColor Yellow
        
        Get-ChildItem -Path $fullPath -Include '*.php','*.ctp' -Recurse -File | ForEach-Object {
            try {
                $bytes = Get-Content $_.FullName -Encoding Byte -TotalCount 3 -ErrorAction Stop
                
                if($bytes -and ($bytes -join '' -eq '239187191')) {
                    $content = Get-Content $_.FullName -Raw
                    $utf8 = New-Object System.Text.UTF8Encoding $false
                    [System.IO.File]::WriteAllText($_.FullName, $content, $utf8)
                    Write-Host "  FIXED: $($_.Name)" -ForegroundColor Green
                    $fixed++
                } else {
                    $clean++
                }
            } catch {
                Write-Host "  ERROR: $($_.Name) - $($_.Exception.Message)" -ForegroundColor Red
                $errors++
            }
        }
    }
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Fixed: $fixed" -ForegroundColor Green
Write-Host "Clean: $clean" -ForegroundColor Gray
Write-Host "Errors: $errors" -ForegroundColor Red
Write-Host "`nDone!" -ForegroundColor Cyan
