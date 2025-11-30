# PowerShell Script: Reorder ALL action buttons - Edit (Left) | View (Right), Remove Delete
# Purpose: Systematically change button order in all index.ctp files

$templateDir = "src\Template"
$backupDir = "template_backups\action_buttons_final_" + (Get-Date -Format "yyyyMMdd_HHmmss")

# Create backup directory
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null
Write-Host "Backup directory created: $backupDir" -ForegroundColor Green

# Find all index.ctp files
$indexFiles = Get-ChildItem -Path $templateDir -Filter "index.ctp" -Recurse | Where-Object { $_.FullName -notmatch "\\Bake\\" }

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
        if ($content -notmatch 'fas fa-eye' -or $content -notmatch 'fas fa-edit') {
            Write-Host "  [SKIP] No complete action buttons found" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Check if already in correct order (Edit before View)
        $viewIndex = $content.IndexOf("'<i class=`"fas fa-eye`"></i>'")
        $editIndex = $content.IndexOf("'<i class=`"fas fa-edit`"></i>'")
        
        if ($editIndex -lt $viewIndex -and $editIndex -gt 0) {
            Write-Host "  [SKIP] Already in correct order (Edit before View)" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        $modified = $content
        
        # Step 1: Remove all Delete buttons
        $deletePattern = '(?s)\s+<\?=\s*\$this->Form->postLink\(\s*' +
                        '''<i class="fas fa-trash"></i>'',[^)]+' +
                        'confirm[^)]+\)\s*\?>'
        $modified = $modified -replace $deletePattern, ''
        
        # Step 2: Find and swap View and Edit buttons
        # This regex captures the entire action cell structure
        $swapPattern = '(<td[^>]*class="actions"[^>]*>.*?<div[^>]*>\s*)' +
                      '(<\?=\s*\$this->Html->link\(\s*' +
                      '''<i class="fas fa-eye"></i>''[^)]+\)\s*\?>\s*)' +
                      '(<\?=\s*\$this->Html->link\(\s*' +
                      '''<i class="fas fa-edit"></i>''[^)]+\)\s*\?>)'
        
        # Swap: Put Edit ($3) before View ($2)
        $swapReplacement = '$1$3' + "`n" + '                            $2'
        $modified = $modified -replace $swapPattern, $swapReplacement
        
        # Check if any changes were made
        if ($modified -eq $content) {
            Write-Host "  [SKIP] No changes applied" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Write modified content back
        [System.IO.File]::WriteAllText($file.FullName, $modified, [System.Text.UTF8Encoding]::new($false))
        
        Write-Host "  [SUCCESS] Reordered: Edit → View (Delete removed)" -ForegroundColor Green
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

if ($successCount -gt 0) {
    # Clear cache after updates
    Write-Host "`nClearing CakePHP cache..." -ForegroundColor Cyan
    & ".\bin\cake.bat" cache clear_all
}

Write-Host "`n=== Action Button Reorder Complete! ===" -ForegroundColor Green
Write-Host "All index pages now show: [Edit Icon (Left)] [View Icon (Right)]" -ForegroundColor Yellow
Write-Host "Delete buttons have been removed." -ForegroundColor Yellow
