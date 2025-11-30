# PowerShell Script: Move Delete button from header to bottom of view templates
# Add "Danger Zone" section at the bottom of tab container

$templateDir = "src\Template"
$backupDir = "template_backups\move_delete_to_bottom_" + (Get-Date -Format "yyyyMMdd_HHmmss")

# Create backup directory
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null
Write-Host "Backup directory created: $backupDir" -ForegroundColor Green

# Find all view.ctp files (exclude Bake templates)
$viewFiles = Get-ChildItem -Path $templateDir -Filter "view.ctp" -Recurse | 
    Where-Object { $_.FullName -notmatch "\\Bake\\" }

Write-Host "`nFound $($viewFiles.Count) view.ctp files to process" -ForegroundColor Cyan
Write-Host "================================================`n" -ForegroundColor Cyan

$successCount = 0
$skipCount = 0
$errorCount = 0

foreach ($file in $viewFiles) {
    $relativePath = $file.FullName.Replace($PWD.Path + "\", "")
    $entityName = $file.Directory.Name
    Write-Host "Processing: $relativePath" -ForegroundColor Yellow
    
    try {
        # Backup original file
        $backupPath = Join-Path $backupDir "${entityName}_view.ctp"
        Copy-Item $file.FullName -Destination $backupPath -Force
        
        # Read file content
        $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.UTF8Encoding]::new($false))
        $originalContent = $content
        
        # Check if file has delete button in header OR sidebar
        if ($content -notmatch "Form->postLink.*fas fa-trash") {
            Write-Host "  [SKIP] No delete button found`n" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Check if already has Danger Zone section
        if ($content -match "Danger Zone") {
            Write-Host "  [SKIP] Already has Danger Zone section`n" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Extract entity variable name from foreach loop or first usage
        $entityVar = ""
        if ($content -match '\$(\w+)->id') {
            $entityVar = $Matches[1]
        }
        
        if ([string]::IsNullOrEmpty($entityVar)) {
            Write-Host "  [ERROR] Could not determine entity variable name`n" -ForegroundColor Red
            $errorCount++
            continue
        }
        
        # Create entity title (singular, capitalized)
        $entityTitle = $entityName -replace 's$', '' # Remove trailing 's' for plural
        
        # Step 1: Remove Delete button from anywhere in the file (header or sidebar)
        # Pattern to match the delete button and surrounding whitespace
        $deletePattern = '\s*<\?=\s*\$this->Form->postLink\(\s*' +
                        '''<i class="fas fa-trash"></i>''[^?]*?' +
                        '\)\s*\?>'
        
        $content = $content -replace $deletePattern, ''
        
        # Step 2: Add Danger Zone section before closing </div><!-- .view-content-wrapper -->
        $dangerZoneSection = @"

<!-- Delete Button Section - Bottom of Tab Container -->
<div style="margin-top: 30px; padding: 20px; border-top: 2px solid #e1e4e8; background-color: #f6f8fa;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h5 style="margin: 0; color: #d73a49; font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i> Danger Zone
            </h5>
            <p style="margin: 5px 0 0 0; color: #586069; font-size: 0.9rem;">
                Once you delete this $entityTitle, there is no going back. Please be certain.
            </p>
        </div>
        <?= `$this->Form->postLink(
            '<i class="fas fa-trash"></i> ' . __('Delete This $entityTitle'),
            ['action' => 'delete', `$$entityVar->id],
            [
                'class' => 'btn btn-danger',
                'escape' => false,
                'confirm' => __('Are you sure you want to permanently delete this $entityTitle?'),
                'style' => 'padding: 10px 20px; font-weight: 500;'
            ]
        ) ?>
    </div>
</div>

</div><!-- .view-content-wrapper -->
"@
        
        # Replace the closing view-content-wrapper div with danger zone + closing div
        $content = $content -replace '</div><!-- \.view-content-wrapper -->', $dangerZoneSection
        
        # Check if any changes were made
        if ($content -eq $originalContent) {
            Write-Host "  [SKIP] No changes applied`n" -ForegroundColor Gray
            $skipCount++
            continue
        }
        
        # Write modified content back
        [System.IO.File]::WriteAllText($file.FullName, $content, [System.Text.UTF8Encoding]::new($false))
        
        Write-Host "  [SUCCESS] Moved delete button to Danger Zone section`n" -ForegroundColor Green
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
    Write-Host "Delete buttons moved to 'Danger Zone' section at bottom of view templates." -ForegroundColor Yellow
    Write-Host "Index pages only show: [Edit Icon] [View Icon]" -ForegroundColor Yellow
} else {
    Write-Host "`nNo files were updated." -ForegroundColor Gray
}
