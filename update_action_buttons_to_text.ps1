# PowerShell Script: Reorder action buttons - Edit (Left) | View (Right)
# Purpose: Change button order from View-Edit-Delete to Edit-View only

$templateDir = "src\Template"
$backupDir = "template_backups\action_buttons_reorder_" + (Get-Date -Format "yyyyMMdd_HHmmss")

# Create backup directory
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null
Write-Host "Backup directory created: $backupDir" -ForegroundColor Green

# Find all index.ctp files
$indexFiles = Get-ChildItem -Path $templateDir -Filter "index.ctp" -Recurse

Write-Host "`nFound $($indexFiles.Count) index.ctp files to process" -ForegroundColor Cyan

$successCount = 0
$skipCount = 0
$errorCount = 0

foreach ($file in $indexFiles) {
    $relativePath = $file.FullName.Replace($PWD.Path + "\", "")
    Write-Host "`nProcessing: $relativePath" -ForegroundColor Yellow
    
    try {
        # Backup original file
        $backupPath = Join-Path $backupDir ($file.Directory.Name + "_" + $file.Name)
        Copy-Item $file.FullName -Destination $backupPath
        
        # Read file content
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        
        # Check if file has icon buttons
        if ($content -notmatch 'fas fa-eye' -and $content -notmatch 'fas fa-edit') {
            Write-Host "  [SKIP] No icon buttons found" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Find the action buttons section and reorder
        # Pattern: Find the entire action buttons block
        $pattern = '(?s)(<td[^>]*class="actions"[^>]*>.*?<div[^>]*>)\s*' +
                   '(<\?=\s*\$this->Html->link\(\s*' +
                   '''<i class="fas fa-eye"></i>''[^)]+\)\s*\?>)\s*' +
                   '(<\?=\s*\$this->Html->link\(\s*' +
                   '''<i class="fas fa-edit"></i>''[^)]+\)\s*\?>)\s*' +
                   '(?:<\?=\s*\$this->Form->postLink\([^)]+\)\s*\?>)?\s*' +
                   '(</div>\s*</td>)'
        
        # Replacement: Edit first, then View (swap $2 and $3)
        $replacement = '$1' + "`n" + 
                      '                            $3' + "`n" + 
                      '                            $2' + "`n" + 
                      '                        $4'
        
        # Apply replacement
        $modified = $content -replace $pattern, $replacement
        
        # Check if any changes were made
        if ($modified -eq $content) {
            Write-Host "  [SKIP] No changes needed (already in correct order)" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Write modified content back
        $modified | Set-Content $file.FullName -Encoding UTF8 -NoNewline
        
        Write-Host "  [SUCCESS] Reordered: Edit (Left) | View (Right)" -ForegroundColor Green
        $successCount++
        
    } catch {
        Write-Host "  [ERROR] $($_.Exception.Message)" -ForegroundColor Red
        $errorCount++
    }
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Successfully updated: $successCount files" -ForegroundColor Green
Write-Host "Skipped: $skipCount files" -ForegroundColor Gray
Write-Host "Errors: $errorCount files" -ForegroundColor Red
Write-Host "Backups saved to: $backupDir" -ForegroundColor Yellow

# Clear cache after updates
Write-Host "`nClearing CakePHP cache..." -ForegroundColor Cyan
& ".\bin\cake.bat" cache clear_all

Write-Host "`nAction button reorder complete!" -ForegroundColor Green
Write-Host "All index pages now show: [Edit Icon] [View Icon] (Delete removed)" -ForegroundColor Yellow
