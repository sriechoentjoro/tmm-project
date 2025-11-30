# PowerShell Script to Apply File Viewer Element to All Templates
# This script will scan all .ctp templates and replace file/document field displays with file_viewer element

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Apply File Viewer to All Templates" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Define patterns to search for file/document fields
$filePatterns = @(
    "*file*",
    "*document*",
    "*attachment*",
    "*upload*"
)

# Get all index.ctp and view.ctp files
$indexFiles = Get-ChildItem -Path "src\Template" -Recurse -Filter "index.ctp"
$viewFiles = Get-ChildItem -Path "src\Template" -Recurse -Filter "view.ctp"
$allFiles = $indexFiles + $viewFiles

Write-Host "Found $($allFiles.Count) template files to process" -ForegroundColor Yellow
Write-Host ""

$processedCount = 0
$modifiedCount = 0

foreach ($file in $allFiles) {
    $processedCount++
    $content = Get-Content $file.FullName -Raw
    $modified = $false
    
    Write-Host "[$processedCount/$($allFiles.Count)] Processing: $($file.FullName.Replace($PWD, '.'))" -ForegroundColor Gray
    
    # Pattern 1: Simple field display in index.ctp
    # Example: <td><?= h($entity->some_file) ?></td>
    $pattern1 = '(<td[^>]*>)\s*<\?=\s*h\(\$\w+->(\w*(?:file|document|attachment)\w*)\)\s*\?>\s*(</td>)'
    if ($content -match $pattern1) {
        $newContent = $content -replace $pattern1, {
            param($match)
            $td = $match.Groups[1].Value
            $fieldName = $match.Groups[2].Value
            $closeTd = $match.Groups[3].Value
            
            $entityVar = if ($match.Value -match '\$(\w+)->') { $matches[1] } else { 'entity' }
            
            @"
$td
                        <?php if (!empty(`$$entityVar->$fieldName)): ?>
                            <?= `$this->element('file_viewer', [
                                'filePath' => `$$entityVar->$fieldName,
                                'label' => basename(`$$entityVar->$fieldName)
                            ]) ?>
                        <?php else: ?>
                            <span style="color: #999; font-style: italic;">No file</span>
                        <?php endif; ?>
                    $closeTd
"@
        }
        $content = $newContent
        $modified = $true
        Write-Host "  - Applied file_viewer to index table cells" -ForegroundColor Green
    }
    
    # Pattern 2: View detail display
    # Example: <td class="github-detail-value"><?= h($entity->some_file) ?></td>
    $pattern2 = '(<td\s+class="github-detail-value">)\s*<\?=\s*h\(\$\w+->(\w*(?:file|document|attachment)\w*)\)\s*\?>\s*(</td>)'
    if ($content -match $pattern2) {
        $newContent = $content -replace $pattern2, {
            param($match)
            $td = $match.Groups[1].Value
            $fieldName = $match.Groups[2].Value
            $closeTd = $match.Groups[3].Value
            
            $entityVar = if ($match.Value -match '\$(\w+)->') { $matches[1] } else { 'entity' }
            
            @"
$td
                                <?php if (!empty(`$$entityVar->$fieldName)): ?>
                                    <?= `$this->element('file_viewer', [
                                        'filePath' => `$$entityVar->$fieldName,
                                        'label' => basename(`$$entityVar->$fieldName)
                                    ]) ?>
                                <?php else: ?>
                                    <span style="color: #999; font-style: italic;">No file</span>
                                <?php endif; ?>
                            $closeTd
"@
        }
        $content = $newContent
        $modified = $true
        Write-Host "  - Applied file_viewer to view detail rows" -ForegroundColor Green
    }
    
    if ($modified) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        $modifiedCount++
        Write-Host "  ✓ File modified successfully" -ForegroundColor Cyan
    } else {
        Write-Host "  - No file/document fields found" -ForegroundColor DarkGray
    }
    Write-Host ""
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Summary:" -ForegroundColor Cyan
Write-Host "  Total files processed: $processedCount" -ForegroundColor White
Write-Host "  Files modified: $modifiedCount" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Done! Please clear cache:" -ForegroundColor Yellow
Write-Host "  .\bin\cake.bat cache clear_all" -ForegroundColor White
