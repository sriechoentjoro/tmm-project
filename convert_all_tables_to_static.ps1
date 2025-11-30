# Convert ALL view templates from old related_records_table to new related_records_table_static
# Adds AJAX filtering, pagination, and modern styling

Write-Host "=== Converting All View Templates to Modern Table with AJAX Filtering ===" -ForegroundColor Cyan
Write-Host ""

$templatePath = "src/Template"
$viewFiles = Get-ChildItem -Path $templatePath -Filter "view.ctp" -Recurse -File | Where-Object { $_.FullName -notmatch "\\template_backups\\" }

$updatedCount = 0
$skippedCount = 0
$errorCount = 0

foreach ($file in $viewFiles) {
    $relativePath = $file.FullName.Replace((Get-Location).Path, "").TrimStart("\")
    Write-Host "Processing: $relativePath" -ForegroundColor Yellow
    
    try {
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        $originalContent = $content
        
        # Check if using OLD element
        if ($content -notmatch "related_records_table'") {
            Write-Host "  ℹ Not using related_records_table - SKIP" -ForegroundColor Gray
            $skippedCount++
            continue
        }
        
        # Check if already converted
        if ($content -match "related_records_table_static") {
            Write-Host "  ✓ Already converted - SKIP" -ForegroundColor Green
            $skippedCount++
            continue
        }
        
        Write-Host "  → Converting element name..." -ForegroundColor Cyan
        
        # Simple replacement: related_records_table' -> related_records_table_static'
        $content = $content -replace "related_records_table'", "related_records_table_static'"
        
        if ($content -ne $originalContent) {
            # Save with UTF8 without BOM
            $utf8NoBom = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
            
            Write-Host "  ✓ Converted successfully" -ForegroundColor Green
            $updatedCount++
        } else {
            Write-Host "  ℹ No changes needed" -ForegroundColor Gray
            $skippedCount++
        }
        
    } catch {
        Write-Host "  ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
        $errorCount++
    }
}

Write-Host ""
Write-Host "=== Summary ===" -ForegroundColor Cyan
Write-Host "Converted: $updatedCount files" -ForegroundColor Green
Write-Host "Skipped: $skippedCount files" -ForegroundColor Yellow
Write-Host "Errors: $errorCount files" -ForegroundColor Red
Write-Host ""

if ($updatedCount -gt 0) {
    Write-Host "✓ Done! Run: bin\cake cache clear_all" -ForegroundColor Green
}
