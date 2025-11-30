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
    $originalContent = $content
    $changed = $false
    
    # Pattern 1: Remove delete button from action column
    # Matches: $this->Form->postLink(...'delete'...)
    $deletePattern1 = [regex]'(\s+)' + [regex]::Escape('<?= $this->Form->postLink(') + '[\s\S]*?' + [regex]::Escape("'action' => 'delete'") + '[\s\S]*?' + [regex]::Escape('?>') + '\s*'
    
    if ($content -match $deletePattern1) {
        # Count occurrences
        $matches = [regex]::Matches($content, $deletePattern1)
        $deleteCount = $matches.Count
        
        # Remove all delete postLink occurrences
        $content = [regex]::Replace($content, $deletePattern1, '')
        $changed = $true
        $changesLog += "$($file.FullName) - Removed $deleteCount delete button(s)"
        Write-Host "  ✓ Removed $deleteCount delete button(s)" -ForegroundColor Green
    }
    
    # Pattern 2: Alternative pattern with btn-delete-icon class
    $deletePattern2 = [regex]'(\s+)' + [regex]::Escape('<?= $this->Form->postLink(') + '[\s\S]*?btn-delete-icon[\s\S]*?' + [regex]::Escape('?>') + '\s*'
    
    if ($content -match $deletePattern2) {
        $matches = [regex]::Matches($content, $deletePattern2)
        $deleteCount = $matches.Count
        
        $content = [regex]::Replace($content, $deletePattern2, '')
        
        if (-not $changed) {
            $changed = $true
            $changesLog += "$($file.FullName) - Removed $deleteCount delete button(s) (pattern 2)"
        }
        Write-Host "  ✓ Removed $deleteCount delete button(s) (pattern 2)" -ForegroundColor Green
    }
    
    # Pattern 3: Remove any remaining __('Delete') links in action column
    $deletePattern3 = [regex]'(\s+)' + [regex]::Escape('<?=') + '[\s\S]*?' + [regex]::Escape("__('Delete')") + '[\s\S]*?' + [regex]::Escape('?>') + '\s*'
    
    if ($content -match $deletePattern3) {
        $matches = [regex]::Matches($content, $deletePattern3)
        $deleteCount = $matches.Count
        
        # Only remove if it's in the actions area (check for nearby View/Edit buttons)
        if ($content -match "('View'|'Edit').*__\('Delete'\)|__\('Delete'\).*('View'|'Edit')") {
            $content = [regex]::Replace($content, $deletePattern3, '')
            
            if (-not $changed) {
                $changed = $true
                $changesLog += "$($file.FullName) - Removed $deleteCount delete link(s) (pattern 3)"
            }
            Write-Host "  ✓ Removed $deleteCount delete link(s) (pattern 3)" -ForegroundColor Green
        }
    }
    
    # Save if changed
    if ($changed) {
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
        $processedFiles++
        Write-Host "  ✓ File updated successfully" -ForegroundColor Green
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
