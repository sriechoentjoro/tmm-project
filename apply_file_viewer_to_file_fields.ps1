# Apply Modal File Viewer to All File Fields
# Scans all templates and applies file_viewer_modal to fields containing "file"

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$templatesDir = Join-Path $scriptDir "src\Template"

# UTF-8 without BOM
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)

$filesUpdated = 0
$fieldsUpdated = 0
$updatedFiles = @()

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "APPLYING MODAL FILE VIEWER TO FILE FIELDS" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host ""

# Find all index.ctp and view.ctp files
$templateFiles = Get-ChildItem -Path $templatesDir -Include "index.ctp","view.ctp" -Recurse

foreach ($file in $templateFiles) {
    $relativePath = $file.FullName.Replace($scriptDir + "\", "")
    $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.Encoding]::UTF8)
    $updated = $false
    $fieldsInFile = 0
    
    # INDEX templates - find file fields in table cells
    if ($file.Name -eq "index.ctp") {
        # Pattern: <td><?= h($entity->some_file_field) ?></td>
        $pattern = '<td>\s*<\?=\s*h\(\$(\w+)->(\w*file\w*)\)\s*\?>\s*</td>'
        $regex = [regex]::new($pattern)
        
        $newContent = $regex.Replace($content, {
            param($match)
            $entityVar = $match.Groups[1].Value
            $fieldName = $match.Groups[2].Value
            $script:fieldsInFile++
            
            Write-Host "  [INDEX] Found field: $fieldName" -ForegroundColor Green
            
            return "<td>
                `<?= `$this->element('file_viewer_modal', [
                    'filePath' => `$$entityVar->$fieldName,
                    'fieldName' => '$fieldName'
                ]) ?>
            </td>"
        })
        
        if ($fieldsInFile -gt 0) {
            $content = $newContent
            $updated = $true
        }
    }
    
    # VIEW templates - find file fields in table rows
    if ($file.Name -eq "view.ctp") {
        # Pattern: <tr><th>Label</th><td><?= h($entity->some_file_field) ?></td></tr>
        $pattern = '<tr>\s*<th>\s*<\?=\s*__\([''"]([^''"]+)[''"]\)\s*\?>\s*</th>\s*<td>\s*<\?=\s*h\(\$(\w+)->(\w*file\w*)\)\s*\?>\s*</td>\s*</tr>'
        $regex = [regex]::new($pattern)
        
        $newContent = $regex.Replace($content, {
            param($match)
            $label = $match.Groups[1].Value
            $entityVar = $match.Groups[2].Value
            $fieldName = $match.Groups[3].Value
            $script:fieldsInFile++
            
            Write-Host "  [VIEW] Found field: $fieldName (label: $label)" -ForegroundColor Green
            
            return "<tr>
        <th>`<?= __('$label') ?></th>
        <td>
            `<?= `$this->element('file_viewer_modal', [
                'filePath' => `$$entityVar->$fieldName,
                'label' => h(`$$entityVar->$fieldName),
                'fieldName' => '$fieldName'
            ]) ?>
        </td>
    </tr>"
        })
        
        if ($fieldsInFile -gt 0) {
            $content = $newContent
            $updated = $true
        }
    }
    
    # Write back if updated
    if ($updated) {
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
        $filesUpdated++
        $fieldsUpdated += $fieldsInFile
        $updatedFiles += $relativePath
        Write-Host "  Updated: $relativePath ($fieldsInFile field(s))" -ForegroundColor Cyan
        Write-Host ""
    }
}

Write-Host ""
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "SUMMARY" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "Files updated: $filesUpdated" -ForegroundColor Green
Write-Host "Fields updated: $fieldsUpdated" -ForegroundColor Green
Write-Host ""

if ($updatedFiles.Count -gt 0) {
    Write-Host "Updated files:" -ForegroundColor Yellow
    foreach ($file in $updatedFiles) {
        Write-Host "  - $file" -ForegroundColor Gray
    }
    Write-Host ""
}

Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host "1. Clear cache: bin\cake cache clear_all" -ForegroundColor White
Write-Host "2. Refresh browser (Ctrl+F5)" -ForegroundColor White
Write-Host "3. Test file fields - click icon to see modal popup" -ForegroundColor White
Write-Host ""
Write-Host "Done!" -ForegroundColor Cyan
