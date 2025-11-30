# Replace iframe file displays with file_viewer element in all view.ctp files
# This prevents "Missing Controller" errors for missing files

$root = "src\Template"
$viewFiles = Get-ChildItem -Path $root -Filter "view.ctp" -Recurse | Where-Object { $_.FullName -notmatch "\\Bake\\" }
$backupDir = "template_backups\replace_iframe_" + (Get-Date -Format "yyyyMMdd_HHmmss")
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

Write-Host "Backup directory: $backupDir" -ForegroundColor Green
Write-Host "Found $($viewFiles.Count) view.ctp files to process`n" -ForegroundColor Cyan

$success = 0
$skipped = 0
$errors = 0

foreach ($file in $viewFiles) {
    $relativePath = $file.FullName.Replace($PWD.Path + "\", "")
    
    try {
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        
        # Check if file has iframe with file display
        if ($content -notmatch '<iframe.*?src=.*?\$.*?->.*?(file|image|photo|document)') {
            Write-Host "[SKIP] No iframe file display: $relativePath" -ForegroundColor Gray
            $skipped++
            continue
        }
        
        # Backup file
        Copy-Item $file.FullName -Destination (Join-Path $backupDir ($file.Directory.Name + "_view.ctp")) -Force
        
        $modified = $false
        $newContent = $content
        
        # Pattern 1: Remove large iframe blocks with file preview
        # Match: Media Preview Section with iframe
        $iframePattern = '(?s)<!-- Media Preview Section -->.*?<div class="media-preview-section"[^>]*>.*?<\?php if \(!empty\(\$([a-zA-Z]+)->([a-z_]+)\)\): \?>.*?<iframe.*?</iframe>.*?</div>.*?<\?php endif; \?>.*?</div>'
        
        if ($newContent -match $iframePattern) {
            $entityVar = $matches[1]
            $fieldName = $matches[2]
            
            $replacement = @"
<!-- Reference File Section -->
                <?php if (!empty(`$$entityVar->$fieldName)): ?>
                <div class="file-preview-container" style="margin-bottom: 20px; background: #f6f8fa; padding: 20px; border-radius: 8px; border: 1px solid #d0d7de;">
                    <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: #24292f;">
                        <i class="fas fa-file"></i> <?= __('Reference File') ?>
                    </h4>
                    <?= `$this->element('file_viewer', [
                        'filePath' => `$$entityVar->$fieldName,
                        'editUrl' => `$this->Url->build(['action' => 'edit', `$$entityVar->id]),
                        'showModal' => true
                    ]) ?>
                </div>
                <?php endif; ?>
"@
            
            $newContent = $newContent -replace $iframePattern, $replacement
            $modified = $true
        }
        
        # Pattern 2: Simple iframe in table rows
        $simpleIframePattern = '(?s)<tr>\s*<th[^>]*>.*?(File|Document|Reference).*?</th>\s*<td[^>]*>.*?<iframe.*?src="[^"]*\$([a-zA-Z]+)->([a-z_]+)[^"]*".*?</iframe>.*?</td>\s*</tr>'
        
        if ($newContent -match $simpleIframePattern) {
            $entityVar = $matches[2]
            $fieldName = $matches[3]
            
            $simpleReplacement = @"
<tr>
                    <th><?= __('Reference File') ?></th>
                    <td>
                        <?php if (!empty(`$$entityVar->$fieldName)): ?>
                            <?= `$this->element('file_viewer', [
                                'filePath' => `$$entityVar->$fieldName,
                                'editUrl' => `$this->Url->build(['action' => 'edit', `$$entityVar->id])
                            ]) ?>
                        <?php else: ?>
                            <span style="color: #999; font-style: italic;">
                                No file - <?= `$this->Html->link('Upload', ['action' => 'edit', `$$entityVar->id]) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
"@
            
            $newContent = $newContent -replace $simpleIframePattern, $simpleReplacement
            $modified = $true
        }
        
        # Pattern 3: Image iframe (for photo previews)
        $imageIframePattern = '(?s)<iframe.*?src="[^"]*uploads[^"]*\$([a-zA-Z]+)->(image_[a-z_]+|photo)[^"]*".*?</iframe>'
        
        if ($newContent -match $imageIframePattern) {
            $entityVar = $matches[1]
            $fieldName = $matches[2]
            
            $imageReplacement = @"
<?php if (!empty(`$$entityVar->$fieldName)): ?>
                            <?= `$this->element('file_viewer', [
                                'filePath' => 'img/uploads/' . `$$entityVar->$fieldName,
                                'editUrl' => `$this->Url->build(['action' => 'edit', `$$entityVar->id])
                            ]) ?>
                        <?php else: ?>
                            <div style="padding: 20px; text-align: center; background: #f6f8fa; border: 1px dashed #d0d7de; border-radius: 4px;">
                                <i class="fas fa-user" style="font-size: 48px; color: #d0d7de;"></i>
                                <p style="margin: 10px 0 0 0; color: #57606a;">
                                    No photo - <?= `$this->Html->link('Upload', ['action' => 'edit', `$$entityVar->id]) ?>
                                </p>
                            </div>
                        <?php endif; ?>
"@
            
            $newContent = $newContent -replace $imageIframePattern, $imageReplacement
            $modified = $true
        }
        
        if ($modified) {
            Set-Content -Path $file.FullName -Value $newContent -Encoding UTF8
            Write-Host "[SUCCESS] Replaced iframe with file_viewer: $relativePath" -ForegroundColor Green
            $success++
        } else {
            Write-Host "[SKIP] No matching iframe pattern: $relativePath" -ForegroundColor Yellow
            $skipped++
        }
        
    } catch {
        Write-Host "[ERROR] $relativePath : $_" -ForegroundColor Red
        $errors++
    }
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Successfully updated: $success files" -ForegroundColor Green
Write-Host "Skipped: $skipped files" -ForegroundColor Gray
Write-Host "Errors: $errors files" -ForegroundColor Red
Write-Host "Backups: $backupDir" -ForegroundColor Yellow

if ($success -gt 0) {
    Write-Host "`nClearing cache..." -ForegroundColor Cyan
    & ".\bin\cake.bat" cache clear_all
}

Write-Host "`n--- Iframe displays replaced with file_viewer element" -ForegroundColor Green
Write-Host "Files not found will now show compact warning instead of iframe error" -ForegroundColor Yellow
