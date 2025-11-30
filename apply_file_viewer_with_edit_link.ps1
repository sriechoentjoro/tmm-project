# Apply file_viewer element with editUrl to all view templates
# Detects fields with file|image|photo|document keywords and wraps with proper element call

$root = "src\Template"
$viewFiles = Get-ChildItem -Path $root -Filter "view.ctp" -Recurse | Where-Object { $_.FullName -notmatch "\\Bake\\" }
$backupDir = "template_backups\apply_file_viewer_edit_" + (Get-Date -Format "yyyyMMdd_HHmmss")
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

Write-Host "Backup directory: $backupDir" -ForegroundColor Green
Write-Host "Found $($viewFiles.Count) view.ctp files to process`n" -ForegroundColor Cyan

$success = 0
$skipped = 0
$errors = 0

# Patterns to detect file/image display
$fileFieldPatterns = @(
    'reference_file',
    'image_photo',
    'image_',
    'file_',
    'photo_',
    'document_',
    'certificate_',
    'attachment_'
)

foreach ($file in $viewFiles) {
    $relativePath = $file.FullName.Replace($PWD.Path + "\", "")
    
    try {
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        
        # Check if file has potential file/image fields
        $hasFileFields = $false
        foreach ($pattern in $fileFieldPatterns) {
            if ($content -match $pattern) {
                $hasFileFields = $true
                break
            }
        }
        
        if (-not $hasFileFields) {
            Write-Host "[SKIP] No file fields detected: $relativePath" -ForegroundColor Gray
            $skipped++
            continue
        }
        
        # Extract entity variable name (e.g., $candidate, $apprenticeOrder, etc.)
        if ($content -match '\$this->request->params\[''pass''\]\[0\]') {
            # Found entity ID reference
            $entityPattern = '\$([a-z]+[A-Z][a-zA-Z]*)->id'
            if ($content -match $entityPattern) {
                $entityVar = $matches[1]
            } else {
                Write-Host "[SKIP] Could not detect entity variable: $relativePath" -ForegroundColor Yellow
                $skipped++
                continue
            }
        } else {
            Write-Host "[SKIP] No entity ID found: $relativePath" -ForegroundColor Gray
            $skipped++
            continue
        }
        
        # Backup file
        Copy-Item $file.FullName -Destination (Join-Path $backupDir ($file.Directory.Name + "_view.ctp")) -Force
        
        $modified = $false
        $newContent = $content
        
        # Pattern 1: Direct file path display without element
        # Example: <td><?= h($entity->reference_file) ?></td>
        $directDisplayPattern = '(<td[^>]*>)\s*<\?=\s*h\(\$' + $entityVar + '->([a-z_]*(?:file|image|photo|document|certificate|attachment)[a-z_]*)\)\s*\?>\s*(</td>)'
        
        if ($newContent -match $directDisplayPattern) {
            $newContent = $newContent -replace $directDisplayPattern, {
                param($match)
                $fieldName = $match.Groups[2].Value
                @"
`$1
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
        `$3
"@
            }
            $modified = $true
        }
        
        # Pattern 2: Image with WWW_ROOT check
        # Example: <?php if (!empty($entity->image_photo) && file_exists(WWW_ROOT . 'img' . DS . 'uploads' . DS . $entity->image_photo)): ?>
        $imagePattern = '<\?php if \(!empty\(\$' + $entityVar + '->([a-z_]*(?:image|photo)[a-z_]*)\)[^:]*\): \?>\s*<\?=.*?src=.*?\?>\s*<\?php else: \?>\s*.*?\s*<\?php endif; \?>'
        
        if ($newContent -match $imagePattern) {
            # Replace with file_viewer element
            $newContent = $newContent -replace $imagePattern, {
                param($match)
                $fieldName = $match.Groups[1].Value
                @"
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
            }
            $modified = $true
        }
        
        if ($modified) {
            Set-Content -Path $file.FullName -Value $newContent -Encoding UTF8
            Write-Host "[SUCCESS] Applied file_viewer: $relativePath" -ForegroundColor Green
            $success++
        } else {
            Write-Host "[SKIP] No applicable patterns: $relativePath" -ForegroundColor Yellow
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

Write-Host "`n✓ File viewer with edit link applied to view templates" -ForegroundColor Green
