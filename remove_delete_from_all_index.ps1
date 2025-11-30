# PowerShell Script: Remove Delete buttons from ALL index.ctp files
# Keep only Edit (Left) and View (Right) buttons
# Delete button will only exist in view.ctp templates

$templateDir = "src\Template"
$backupDir = "template_backups\remove_delete_" + (Get-Date -Format "yyyyMMdd_HHmmss")

# Create backup directory
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null
Write-Host "Backup directory created: $backupDir" -ForegroundColor Green

# Find all index.ctp files (exclude Bake templates)
$indexFiles = Get-ChildItem -Path $templateDir -Filter "index.ctp" -Recurse | 
    Where-Object { $_.FullName -notmatch "\\Bake\\" }

Write-Host "`nFound $($indexFiles.Count) index.ctp files to process" -ForegroundColor Cyan
Write-Host "================================================`n" -ForegroundColor Cyan

$successCount = 0
$skipCount = 0
$errorCount = 0

foreach ($file in $indexFiles) {
    $relativePath = $file.FullName.Replace($PWD.Path + "\", "")
    Write-Host "Processing: $relativePath" -ForegroundColor Yellow
    
    try {
        # Backup original file
        $dir = $file.Directory.Name
        $backupPath = Join-Path $backupDir "${dir}_index.ctp"
        Copy-Item $file.FullName -Destination $backupPath -Force
        
        # Read file content
        $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.UTF8Encoding]::new($false))
        $originalContent = $content
        
        # Check if file has delete button
        if ($content -notmatch "fas fa-trash") {
            Write-Host "  [SKIP] No delete button found`n" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Remove Delete button (postLink with trash icon)
        # Pattern matches the entire postLink block including spacing
        $deletePattern = '\s*<\?=\s*\$this->Form->postLink\(\s*' +
                        '''<i class="fas fa-trash"></i>''[^?]*?' +
                        '\)\s*\?>'
        
        $content = $content -replace $deletePattern, ''
        
        # Also ensure Edit comes before View
        # Find and swap if View comes before Edit
        $viewPattern = '(<\?=\s*\$this->Html->link\(\s*)' +
                      '''<i class="fas fa-eye"></i>''([^?]+\?>\s*)' +
                      '(<\?=\s*\$this->Html->link\(\s*)' +
                      '''<i class="fas fa-edit"></i>''([^?]+\?>\s*)'
        
        # Check if View comes before Edit
        if ($content -match $viewPattern) {
            # Swap them: put Edit before View
            $content = $content -replace $viewPattern, '$3''<i class="fas fa-edit"></i>''$4$1''<i class="fas fa-eye"></i>''$2'
        }
        
        # Update comment
        $content = $content -replace '<!-- Action Buttons with Icons -->', '<!-- Action Buttons: Edit | View -->'
        $content = $content -replace '<!-- Action Buttons with Icons: Edit \(Left\) \| View \(Right\) -->', '<!-- Action Buttons: Edit | View -->'
        
        # Check if any changes were made
        if ($content -eq $originalContent) {
            Write-Host "  [SKIP] No changes needed`n" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Write modified content back
        [System.IO.File]::WriteAllText($file.FullName, $content, [System.Text.UTF8Encoding]::new($false))
        
        Write-Host "  [SUCCESS] Updated: Edit | View (Delete removed)`n" -ForegroundColor Green
        $successCount++
        
    } catch {
        Write-Host "  [ERROR] $($_.Exception.Message)`n" -ForegroundColor Red
        $errorCount++
    }
}

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "=== Summary ===" -ForegroundColor Cyan
Write-Host "Successfully updated: $successCount files" -ForegroundColor Green
Write-Host "Skipped: $skipCount files" -ForegroundColor Gray
Write-Host "Errors: $errorCount files" -ForegroundColor $(if($errorCount -gt 0){"Red"}else{"Gray"})
Write-Host "Backups saved to: $backupDir" -ForegroundColor Yellow

if ($successCount -gt 0) {
    # Clear cache after updates
    Write-Host "`nClearing CakePHP cache..." -ForegroundColor Cyan
    & ".\bin\cake.bat" cache clear_all
    
    Write-Host "`n=== Complete! ===" -ForegroundColor Green
    Write-Host "All index pages now show: [Edit Icon] [View Icon]" -ForegroundColor Yellow
    Write-Host "Delete buttons removed from index pages." -ForegroundColor Yellow
    Write-Host "Delete functionality is now only available in view templates." -ForegroundColor Yellow
} else {
    Write-Host "`nNo files were updated." -ForegroundColor Gray
}
