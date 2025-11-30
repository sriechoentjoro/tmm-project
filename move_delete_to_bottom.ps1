# Move delete Form->postLink from header to bottom of view container for all view.ctp files
$root = "src\Template"
$files = Get-ChildItem -Path $root -Filter "view.ctp" -Recurse
$backupDir = "template_backups\move_delete_view_" + (Get-Date -Format "yyyyMMdd_HHmmss")
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

$removePattern = '(?s)\s*<\?=\s*\$this->Form->postLink\([\s\S]*?fa-trash[\s\S]*?\)\s*\?>'
$success=0; $skipped=0; $errors=0

foreach ($f in $files) {
    $content = Get-Content $f.FullName -Raw -Encoding UTF8
    if ($content -match 'fa-trash') {
        # If already has bottom delete section marker, skip
        if ($content -match 'Delete Button Section - Bottom' -or $content -match 'Danger Zone') {
            Write-Host "Already placed bottom delete: $($f.FullName)" -ForegroundColor Gray
            $skipped++
            continue
        }
        try {
            Copy-Item $f.FullName -Destination (Join-Path $backupDir ($f.Directory.Name + "_" + $f.Name)) -Force
            # Remove the first occurrence of delete postLink
            $new = [regex]::Replace($content, $removePattern, "", [System.Text.RegularExpressions.RegexOptions]::Singleline, 1)
            if ($new -eq $content) {
                Write-Host "No change (pattern not matched precisely): $($f.FullName)" -ForegroundColor Yellow
                $skipped++
                continue
            }
            # Insert bottom delete block before closing wrapper
            $deleteBlock = @'
    <!-- Delete Button Section - Bottom of Tab Container -->
    <div style="margin-top: 30px; padding: 20px; border-top: 2px solid #e1e4e8; background-color: #f6f8fa;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h5 style="margin: 0; color: #d73a49; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </h5>
                <p style="margin: 5px 0 0 0; color: #586069; font-size: 0.9rem;">
                    Once you delete this record, there is no going back. Please be certain.
                </p>
            </div>
            <?= $this->Form->postLink(
                '<i class="fas fa-trash"></i> ' . __('Delete'),
                ['action' => 'delete', $this->request->params['pass'][0]],
                [
                    'class' => 'btn btn-danger',
                    'escape' => false,
                    'confirm' => __('Are you sure you want to delete this item?')
                ]
            ) ?>
        </div>
    </div>
'@

            if ($new -match "</div><!-- \.view-content-wrapper -->") {
                $new = $new -replace "</div><!-- \\\.view-content-wrapper -->$", $deleteBlock + "`n</div><!-- .view-content-wrapper -->"
            } else {
                # fallback append at end
                $new = $new + $deleteBlock
            }

            Set-Content -Path $f.FullName -Value $new -Encoding UTF8
            Write-Host "Moved delete to bottom: $($f.FullName)" -ForegroundColor Green
            $success++
        } catch {
            Write-Host "Error processing $($f.FullName): $_" -ForegroundColor Red
            $errors++
        }
    } else {
        $skipped++
    }
}

Write-Host "`nSummary: Moved delete in $success files, Skipped $skipped, Errors $errors" -ForegroundColor Cyan

# Clear cache
& ".\bin\cake.bat" cache clear_all
