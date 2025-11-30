# Remove Delete Buttons from All Index Templates
# Following GitHub Copilot instructions: Delete buttons ONLY in view templates, NOT in index row actions

$ErrorActionPreference = "Stop"
$utf8NoBom = New-Object System.Text.UTF8Encoding $false

$indexTemplates = Get-ChildItem -Path "src\Template" -Recurse -Filter "index.ctp"
$changesLog = @()
$processedFiles = 0

Write-Host "=== Removing Delete Buttons from Index Templates ===" -ForegroundColor Cyan
Write-Host "Policy: Delete buttons ONLY in view templates (bottom), NOT in index row actions`n" -ForegroundColor Yellow

foreach ($file in $indexTemplates) {
    Write-Host "Processing: $($file.FullName)" -ForegroundColor Yellow
    
    $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.Encoding]::UTF8)
    $changed = $false
    
    # Simple pattern: Find and remove delete button lines
    # Pattern matches the entire Form->postLink for delete action
    $lines = $content -split "`n"
    $newLines = @()
    $skipNext = $false
    $deleteCount = 0
    
    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i]
        
        # Check if this line contains delete button
        if ($line -match "Form->postLink.*'delete'" -or 
            $line -match "btn-delete-icon" -or
            ($line -match "__\('Delete'\)" -and $line -match "Form->postLink")) {
            
            # Skip this line and check if next line is closing
            $deleteCount++
            $changed = $true
            
            # Check if delete spans multiple lines (look ahead)
            $j = $i
            while ($j -lt $lines.Count -and $lines[$j] -notmatch '\?>\s*$') {
                $j++
            }
            # Skip all lines in the delete button block
            $i = $j
            continue
        }
        
        $newLines += $line
    }
    
    if ($changed) {
        $content = $newLines -join "`n"
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
        $processedFiles++
        $changesLog += "$($file.FullName) - Removed $deleteCount delete button(s)"
        Write-Host "  ✓ Removed $deleteCount delete button(s)" -ForegroundColor Green
    } else {
        Write-Host "  - No delete buttons found (already compliant)" -ForegroundColor Gray
    }
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Total index templates scanned: $($indexTemplates.Count)" -ForegroundColor White
Write-Host "Files modified: $processedFiles" -ForegroundColor Green
Write-Host "Changes applied: $($changesLog.Count)" -ForegroundColor Green

if ($changesLog.Count -gt 0) {
    Write-Host "`nDetailed Changes:" -ForegroundColor Yellow
    foreach ($log in $changesLog) {
        Write-Host "  - $log" -ForegroundColor Gray
    }
}

Write-Host "`n✓ Script completed successfully!" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "  1. Run: bin\cake cache clear_all" -ForegroundColor White
Write-Host "  2. Refresh browser - Index pages show only View/Edit buttons" -ForegroundColor White
Write-Host "  3. Delete buttons remain in View templates (bottom section)" -ForegroundColor White

Write-Host "`nPolicy Applied:" -ForegroundColor Cyan
Write-Host "  ✓ Index templates: View + Edit buttons only (no delete)" -ForegroundColor Green
Write-Host "  ✓ View templates: Delete button at bottom (unchanged)" -ForegroundColor Green
Write-Host "  ✓ Reason: Data safety - prevent accidental deletion from list view" -ForegroundColor Yellow
