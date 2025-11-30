# Apply File Viewer to image_photo fields in Trainees, Candidates, and Apprentices templates
# Following GitHub Copilot instructions for image/photo handling

$ErrorActionPreference = "Stop"
$utf8NoBom = New-Object System.Text.UTF8Encoding $false

$tables = @('Trainees', 'Candidates', 'Apprentices')
$changesLog = @()

foreach ($table in $tables) {
    Write-Host "`n=== Processing $table ===" -ForegroundColor Cyan
    
    # Process INDEX template
    $indexPath = "src\Template\$table\index.ctp"
    if (Test-Path $indexPath) {
        Write-Host "Processing $indexPath..." -ForegroundColor Yellow
        $content = [System.IO.File]::ReadAllText($indexPath, [System.Text.Encoding]::UTF8)
        
        # Pattern 1: Replace plain h() display with file_viewer
        # OLD: <?= h($entity->image_photo) ?>
        # NEW: <?= $this->element('file_viewer', ['filePath' => $entity->image_photo]) ?>
        $oldPattern1 = [regex]::Escape('<?= h($' + $table.ToLower().TrimEnd('s') + '->image_photo) ?>')
        $newPattern1 = "<?= `$this->element('file_viewer', ['filePath' => `$" + $table.ToLower().TrimEnd('s') + "->image_photo]) ?>"
        
        if ($content -match $oldPattern1) {
            $content = $content -replace $oldPattern1, $newPattern1
            $changesLog += "$indexPath - Replaced h() display with file_viewer"
            Write-Host "  ✓ Replaced h() with file_viewer" -ForegroundColor Green
        }
        
        # Pattern 2: Check if already has file_viewer (skip if already applied)
        if ($content -match "file_viewer.*image_photo") {
            Write-Host "  ✓ File viewer already applied" -ForegroundColor Green
        }
        
        [System.IO.File]::WriteAllText($indexPath, $content, $utf8NoBom)
    } else {
        Write-Host "  ! File not found: $indexPath" -ForegroundColor Red
    }
    
    # Process VIEW template
    $viewPath = "src\Template\$table\view.ctp"
    if (Test-Path $viewPath) {
        Write-Host "Processing $viewPath..." -ForegroundColor Yellow
        $content = [System.IO.File]::ReadAllText($viewPath, [System.Text.Encoding]::UTF8)
        
        # Pattern for VIEW: Replace with showPreview and editUrl
        # OLD: <?= h($entity->image_photo) ?>
        # NEW: <?= $this->element('file_viewer', [
        #        'filePath' => $entity->image_photo,
        #        'showPreview' => true,
        #        'editUrl' => $this->Url->build(['action' => 'edit', $entity->id])
        #      ]) ?>
        
        $entityName = $table.ToLower().TrimEnd('s')
        $oldPattern2 = [regex]::Escape('<?= h($' + $entityName + '->image_photo) ?>')
        $newPattern2 = @"
<?= `$this->element('file_viewer', [
                'filePath' => `$$entityName->image_photo,
                'showPreview' => true,
                'editUrl' => `$this->Url->build(['action' => 'edit', `$$entityName->id])
            ]) ?>
"@
        
        if ($content -match $oldPattern2) {
            $content = $content -replace $oldPattern2, $newPattern2
            $changesLog += "$viewPath - Replaced h() with file_viewer (with showPreview)"
            Write-Host "  ✓ Replaced h() with file_viewer (showPreview enabled)" -ForegroundColor Green
        }
        
        # Check if already applied
        if ($content -match "file_viewer.*image_photo.*showPreview") {
            Write-Host "  ✓ File viewer with showPreview already applied" -ForegroundColor Green
        }
        
        [System.IO.File]::WriteAllText($viewPath, $content, $utf8NoBom)
    } else {
        Write-Host "  ! File not found: $viewPath" -ForegroundColor Red
    }
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Total changes: $($changesLog.Count)" -ForegroundColor Green
foreach ($log in $changesLog) {
    Write-Host "  - $log" -ForegroundColor Gray
}

Write-Host "`n✓ Script completed successfully!" -ForegroundColor Green
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Run: bin\cake cache clear_all" -ForegroundColor White
Write-Host "  2. Refresh browser to see image_photo displayed with file viewer" -ForegroundColor White
