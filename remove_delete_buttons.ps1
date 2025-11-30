# PowerShell script to:
# 1. Remove Delete buttons from all index.ctp files
# 2. Add Delete buttons to view.ctp files at the bottom (after tab-container)
# NOTE: Tests are NOT run automatically - run manually after reviewing changes

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$templateDir = Join-Path $projectRoot "src\Template"
$reportPath = Join-Path $projectRoot "delete_buttons_report.md"

# Initialize report
"# Delete Buttons Modification Report`nGenerated on $(Get-Date)`n" | Set-Content $reportPath

# ============================================
# PART 1: Remove Delete buttons from index.ctp
# ============================================
"`n## Part 1: Removed Delete Buttons from index.ctp Files`n" | Add-Content $reportPath

$indexFiles = Get-ChildItem -Path $templateDir -Recurse -Filter "index.ctp" | Where-Object {
    $_.FullName -notmatch "\\Bake\\" -and $_.FullName -notmatch "\\Backup\\"
}

$indexFilesModified = 0
$indexLinesRemoved = 0

foreach ($file in $indexFiles) {
    $lines = Get-Content $file.FullName
    $newLines = @()
    $removedInThisFile = 0
    
    foreach ($line in $lines) {
        # Skip lines that contain Form->postLink with 'Delete'
        if ($line -match "Form->postLink.*__\('Delete" -or 
            $line -match "Form->postLink.*'Delete'" -or
            $line -match 'Form->postLink.*"Delete"' -or
            $line -match "Form->postLink.*<i class=.*fa-trash") {
            $removedInThisFile++
            continue
        }
        $newLines += $line
    }
    
    if ($removedInThisFile -gt 0) {
        $newLines | Set-Content -Path $file.FullName -Encoding UTF8
        $indexFilesModified++
        $indexLinesRemoved += $removedInThisFile
        
        $relativePath = $file.FullName.Replace($projectRoot, "").TrimStart('\')
        "- **$relativePath** - Removed $removedInThisFile Delete button(s)" | Add-Content $reportPath
    }
}

# ============================================
# PART 2: Add Delete buttons to view.ctp files
# ============================================
"`n## Part 2: Added Delete Buttons to view.ctp Files`n" | Add-Content $reportPath

$viewFiles = Get-ChildItem -Path $templateDir -Recurse -Filter "view.ctp" | Where-Object {
    $_.FullName -notmatch "\\Bake\\" -and $_.FullName -notmatch "\\Backup\\"
}

$viewFilesModified = 0
$viewButtonsAdded = 0

foreach ($file in $viewFiles) {
    $content = Get-Content $file.FullName -Raw
    
    # Check if Delete button already exists
    if ($content -match "Form->postLink.*__\('Delete" -or 
        $content -match "Form->postLink.*'Delete'" -or
        $content -match 'Form->postLink.*"Delete"') {
        continue  # Skip if already has Delete button
    }
    
    # Try to find the entity name from the file
    $folderName = Split-Path (Split-Path $file.FullName -Parent) -Leaf
    $entityName = $folderName.TrimEnd('s')  # Simple singularization
    $entityVarName = $entityName.Substring(0,1).ToLower() + $entityName.Substring(1)
    
    # Convert CamelCase to Title Case for display
    $displayName = $entityName -creplace '([A-Z])', ' $1'
    $displayName = $displayName.Trim()
    
    # Delete button HTML to add
    $deleteButton = @"
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="float-right">
                <?= `$this->Form->postLink(
                    '<i class="fas fa-trash"></i> ' . __('Delete $displayName'),
                    ['action' => 'delete', `$$entityVarName->id],
                    [
                        'confirm' => __('Are you sure you want to delete this $displayName?'),
                        'class' => 'btn btn-danger',
                        'escape' => false
                    ]
                ) ?>
            </div>
        </div>
    </div>
"@
    
    # Find the closing </div> tags near the end (after tab-container)
    # Look for the pattern that closes the main content div
    if ($content -match '(?s)(.*</div>\s*</div>\s*)$') {
        # Insert the delete button before the final closing divs
        $insertPosition = $content.LastIndexOf('</div>')
        if ($insertPosition -gt 0) {
            # Find the second-to-last </div>
            $tempContent = $content.Substring(0, $insertPosition)
            $secondLastDiv = $tempContent.LastIndexOf('</div>')
            
            if ($secondLastDiv -gt 0) {
                $newContent = $content.Substring(0, $secondLastDiv) + 
                              "`n" + $deleteButton + "`n" + 
                              $content.Substring($secondLastDiv)
                
                Set-Content -Path $file.FullName -Value $newContent -Encoding UTF8
                $viewFilesModified++
                $viewButtonsAdded++
                
                $relativePath = $file.FullName.Replace($projectRoot, "").TrimStart('\')
                "- **$relativePath** - Added Delete button for $displayName" | Add-Content $reportPath
            }
        }
    }
}

# ============================================
# Summary
# ============================================
"`n## Summary`n" | Add-Content $reportPath
"### Index Files (Delete buttons removed)" | Add-Content $reportPath
"- **Files modified:** $indexFilesModified" | Add-Content $reportPath
"- **Delete buttons removed:** $indexLinesRemoved" | Add-Content $reportPath
"`n### View Files (Delete buttons added)" | Add-Content $reportPath
"- **Files modified:** $viewFilesModified" | Add-Content $reportPath
"- **Delete buttons added:** $viewButtonsAdded" | Add-Content $reportPath

"`n---`n" | Add-Content $reportPath
"**Note:** Tests were NOT run automatically. Please run ``bin\cake test`` manually to verify changes." | Add-Content $reportPath

Write-Host "========================================="
Write-Host "Delete Buttons Modification Complete!"
Write-Host "========================================="
Write-Host "Index files modified: $indexFilesModified (removed $indexLinesRemoved buttons)"
Write-Host "View files modified: $viewFilesModified (added $viewButtonsAdded buttons)"
Write-Host "Report saved to: $reportPath"
Write-Host ""
Write-Host "IMPORTANT: Run 'bin\cake test' to verify changes"
Write-Host "========================================="
