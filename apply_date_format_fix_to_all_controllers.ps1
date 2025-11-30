# Apply Date Format Fix to All Controllers
# Adds automatic DD-MM-YYYY to YYYY-MM-DD conversion in edit/add methods

$controllersPath = "d:\xampp\htdocs\tmm\src\Controller"
$controllers = Get-ChildItem -Path $controllersPath -Filter "*Controller.php" -Exclude "AppController.php","Component"

$dateConversionCode = @'
            // Fix date format: Convert DD-MM-YYYY or DD/MM/YYYY to YYYY-MM-DD
            foreach ($data as $fieldName => $value) {
                if (is_string($value) && !empty($value) && preg_match('/_date$|^date_/', $fieldName)) {
                    // Try DD-MM-YYYY format
                    if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $value, $matches)) {
                        $data[$fieldName] = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                    }
                    // Try DD/MM/YYYY format
                    elseif (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $value, $matches)) {
                        $data[$fieldName] = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                    }
                }
            }
            
'@

Write-Host "=== Applying Date Format Fix to All Controllers ===" -ForegroundColor Cyan
Write-Host ""

$updated = 0
$skipped = 0

foreach ($controller in $controllers) {
    $filePath = $controller.FullName
    $fileName = $controller.Name
    
    Write-Host "Processing: $fileName" -ForegroundColor Yellow
    
    $content = Get-Content $filePath -Raw -Encoding UTF8
    
    # Check if already has the fix
    if ($content -match "Fix date format: Convert DD-MM-YYYY") {
        Write-Host "  [SKIP] Already has date format fix" -ForegroundColor Gray
        $skipped++
        continue
    }
    
    # Find edit/add methods and add date conversion after getData()
    $pattern = '(\$data = \$this->request->getData\(\);)'
    $replacement = "`$1`n`n$dateConversionCode"
    
    $matches = [regex]::Matches($content, $pattern)
    if ($matches.Count -gt 0) {
        $newContent = $content -replace $pattern, $replacement
        [System.IO.File]::WriteAllText($filePath, $newContent, (New-Object System.Text.UTF8Encoding $false))
        Write-Host "  [OK] Date format fix applied ($($matches.Count) locations)" -ForegroundColor Green
        $updated++
    } else {
        Write-Host "  [SKIP] No getData() pattern found" -ForegroundColor Gray
        $skipped++
    }
}

Write-Host ""
Write-Host "=== Summary ===" -ForegroundColor Cyan
Write-Host "Updated: $updated controllers" -ForegroundColor Green
Write-Host "Skipped: $skipped controllers" -ForegroundColor Gray
Write-Host ""
Write-Host "Clearing cache..." -ForegroundColor Yellow
& "d:\xampp\htdocs\tmm\bin\cake" cache clear_all
Write-Host ""
Write-Host "Done! All date fields will now auto-convert DD-MM-YYYY to YYYY-MM-DD" -ForegroundColor Green
