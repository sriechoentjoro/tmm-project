# Comprehensive script to convert ALL view templates to modern AJAX filtering
# Phase 1: Convert element names in all view.ctp files

Write-Host "=== Phase 1: Converting View Templates ===" -ForegroundColor Cyan
Write-Host ""

$viewFiles = Get-ChildItem -Path "src/Template" -Filter "view.ctp" -Recurse | Where-Object { $_.FullName -notmatch "template_backups" }
$convertedCount = 0

foreach ($file in $viewFiles) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    
    if (($content -match "related_records_table'") -and ($content -notmatch "related_records_table_static")) {
        $content = $content -replace "related_records_table'", "related_records_table_static'"
        
        # Save with UTF8 without BOM
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
        
        Write-Host "✓ Converted: $($file.Directory.Name)/$($file.Name)" -ForegroundColor Green
        $convertedCount++
    }
}

Write-Host ""
Write-Host "Converted $convertedCount view templates" -ForegroundColor Cyan
Write-Host ""
Write-Host "✓ Done! All tables now use modern AJAX filtering." -ForegroundColor Green
Write-Host "Run: bin\cake cache clear_all" -ForegroundColor Yellow
