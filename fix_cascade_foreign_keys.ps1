# Fix all foreign key field names in cascade filters
# master_propinsi_id → propinsi_id
# master_kabupaten_id → kabupaten_id  
# master_kecamatan_id → kecamatan_id

$files = @(
    "src\Controller\CandidatesController.php",
    "src\Template\Candidates\add.ctp",
    "src\Template\Candidates\edit.ctp"
)

$rootPath = "d:\xampp\htdocs\project_tmm"

foreach ($file in $files) {
    $fullPath = Join-Path $rootPath $file
    
    if (Test-Path $fullPath) {
        Write-Host "Processing: $file" -ForegroundColor Cyan
        
        $content = Get-Content $fullPath -Raw
        
        # Fix cascade filter select queries in controllers
        $content = $content -replace "select\(\['id', 'title', 'master_propinsi_id'\]\)", "select(['id', 'title', 'propinsi_id'])"
        $content = $content -replace "select\(\['id', 'title', 'master_propinsi_id', 'master_kabupaten_id'\]\)", "select(['id', 'title', 'kabupaten_id'])"
        $content = $content -replace "select\(\['id', 'title', 'master_propinsi_id', 'master_kabupaten_id', 'master_kecamatan_id'\]\)", "select(['id', 'title', 'kecamatan_id'])"
        
        # Fix cascade filter in templates (PHP array syntax)
        $content = $content -replace "'master_propinsi_id' => \`$k->master_propinsi_id, 'master_kabupaten_id' => \`$k->master_kabupaten_id, 'master_kecamatan_id' => \`$k->master_kecamatan_id", "'propinsi_id' => `$k->propinsi_id, 'kabupaten_id' => `$k->kabupaten_id, 'kecamatan_id' => `$k->kecamatan_id"
        $content = $content -replace "'master_propinsi_id' => \`$k->master_propinsi_id, 'master_kabupaten_id' => \`$k->master_kabupaten_id", "'propinsi_id' => `$k->propinsi_id, 'kabupaten_id' => `$k->kabupaten_id"
        
        Set-Content $fullPath -Value $content -NoNewline
        Write-Host "  ✓ Fixed" -ForegroundColor Green
    } else {
        Write-Host "  ✗ Not found: $fullPath" -ForegroundColor Red
    }
}

Write-Host "`nDone! All cascade filter foreign keys fixed." -ForegroundColor Green
Write-Host "Changes made:" -ForegroundColor Yellow
Write-Host "  - master_propinsi_id → propinsi_id" -ForegroundColor White
Write-Host "  - master_kabupaten_id → kabupaten_id" -ForegroundColor White
Write-Host "  - master_kecamatan_id → kecamatan_id" -ForegroundColor White
