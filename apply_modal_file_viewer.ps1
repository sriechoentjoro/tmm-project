# Apply Modal File Viewer to All Templates
# This script finds all fields with names containing "file" and replaces them with file_viewer_modal element

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$templatesDir = Join-Path $scriptDir "src\Template"

# Encoding without BOM
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)

# Counter
$filesUpdated = 0
$fieldsUpdated = 0

# Find all index.ctp and view.ctp files
$templateFiles = Get-ChildItem -Path $templatesDir -Include "index.ctp","view.ctp" -Recurse

Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "APPLYING MODAL FILE VIEWER TO ALL TEMPLATES" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host ""

foreach ($file in $templateFiles) {
    $relativePath = $file.FullName.Replace($scriptDir + "\", "")
    $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.Encoding]::UTF8)
    $originalContent = $content
    $updated = $false
    
    # Pattern 1: Find file fields in index templates
    # Example: <td><?= h($entity->reference_file) ?></td>
    if ($file.Name -eq "index.ctp") {
        $pattern = '<td>\s*<\?=\s*h\(\$\w+->(\w*file\w*)\)\s*\?>\s*</td>'
        $matches = [regex]::Matches($content, $pattern)
        
        foreach ($match in $matches) {
            $fieldName = $match.Groups[1].Value
            $oldCode = $match.Value
            $newCode = @"
<td>
                <?= `$this->element('file_viewer_modal', [
                    'filePath' => `$entity->$fieldName,
                    'fieldName' => '$fieldName'
                ]) ?>
            </td>
"@
            $content = $content.Replace($oldCode, $newCode)
            $updated = $true
            $fieldsUpdated++
            Write-Host "  [INDEX] Updated field: $fieldName" -ForegroundColor Green
        }
    }
    
    # Pattern 2: Find file fields in view templates - table rows
    # Example: <tr><th><?= __('Reference File') ?></th><td><?= h($entity->reference_file) ?></td></tr>
    if ($file.Name -eq "view.ctp") {
        # Match table rows with file fields
        $pattern = '<tr>\s*<th>\s*<\?=\s*__\([^)]+\)\s*\?>\s*</th>\s*<td>\s*<\?=\s*h\(\$\w+->(\w*file\w*)\)\s*\?>\s*</td>\s*</tr>'
        $matches = [regex]::Matches($content, $pattern)
        
        foreach ($match in $matches) {
            $fieldName = $match.Groups[1].Value
            $oldCode = $match.Value
            
            # Extract the label from the __() function
            $labelMatch = [regex]::Match($oldCode, '__\([''"]([^''"]+)[''"]\)')
            $label = if ($labelMatch.Success) { $labelMatch.Groups[1].Value } else { $fieldName }
            
            $newCode = @"
<tr>
        <th><?= __('$label') ?></th>
        <td>
            <?= `$this->element('file_viewer_modal', [
                'filePath' => `$entity->$fieldName,
                'label' => h(`$entity->$fieldName),
                'fieldName' => '$fieldName'
            ]) ?>
        </td>
    </tr>
"@
            $content = $content.Replace($oldCode, $newCode)
            $updated = $true
            $fieldsUpdated++
            Write-Host "  [VIEW] Updated field: $fieldName" -ForegroundColor Green
        }
        
        # Pattern 3: File fields without table structure
        $pattern = '<\?=\s*h\(\$\w+->(\w*file\w*)\)\s*\?>'
        $matches = [regex]::Matches($content, $pattern)
        
        foreach ($match in $matches) {
            # Skip if already inside file_viewer_modal element
            if ($content.Substring([Math]::Max(0, $match.Index - 200), [Math]::Min(200, $match.Index)) -match "file_viewer_modal") {
                continue
            }
            
            $fieldName = $match.Groups[1].Value
            $oldCode = $match.Value
            $newCode = "<?= `$this->element('file_viewer_modal', ['filePath' => `$entity->$fieldName, 'fieldName' => '$fieldName']) ?>"
            
            $content = $content.Replace($oldCode, $newCode)
            $updated = $true
            $fieldsUpdated++
            Write-Host "  [VIEW] Updated standalone field: $fieldName" -ForegroundColor Green
        }
    }
    
    # Write back if updated
    if ($updated) {
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
        $filesUpdated++
        Write-Host "✓ Updated: $relativePath" -ForegroundColor Cyan
        Write-Host ""
    }
}

Write-Host ""
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "SUMMARY" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "Files updated: $filesUpdated" -ForegroundColor Green
Write-Host "Fields updated: $fieldsUpdated" -ForegroundColor Green
Write-Host ""
Write-Host "USAGE IN TEMPLATES:" -ForegroundColor Yellow
Write-Host "Use file_viewer_modal element for all file fields" -ForegroundColor Gray
Write-Host ""
Write-Host "FEATURES:" -ForegroundColor Yellow
Write-Host "  * Click file icon to open modal popup viewer" -ForegroundColor Green
Write-Host "  * Preview images JPG PNG GIF in modal" -ForegroundColor Green
Write-Host "  * Preview PDF files in iframe" -ForegroundColor Green
Write-Host "  * Download button for all file types" -ForegroundColor Green
Write-Host "  * Color-coded file type icons" -ForegroundColor Green
Write-Host "  * Close with ESC key or click outside" -ForegroundColor Green
Write-Host ""
Write-Host "Done! Refresh your browser to see the changes." -ForegroundColor Cyan
