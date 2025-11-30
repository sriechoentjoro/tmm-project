# Remove delete postLink blocks from all index.ctp files
$root = "src\Template"
$files = Get-ChildItem -Path $root -Filter "index.ctp" -Recurse
$backupDir = "template_backups\remove_delete_index_" + (Get-Date -Format "yyyyMMdd_HHmmss")
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

$pattern = '(?s)\s*<\?=\s*\$this->Form->postLink\([\s\S]*?fa-trash[\s\S]*?\)\s*\?>'

$success=0; $skipped=0; $errors=0
foreach ($f in $files) {
    $content = Get-Content $f.FullName -Raw -Encoding UTF8
    if ($content -match 'fas fa-trash') {
        try {
            Copy-Item $f.FullName -Destination (Join-Path $backupDir ($f.Directory.Name + "_" + $f.Name)) -Force
            $new = [regex]::Replace($content, $pattern, "", [System.Text.RegularExpressions.RegexOptions]::Singleline)
            if ($new -ne $content) {
                Set-Content -Path $f.FullName -Value $new -Encoding UTF8
                Write-Host "Updated: $($f.FullName)" -ForegroundColor Green
                $success++
            } else {
                Write-Host "No change (pattern not matched precisely): $($f.FullName)" -ForegroundColor Yellow
                $skipped++
            }
        } catch {
            Write-Host "Error processing $($f.FullName): $_" -ForegroundColor Red
            $errors++
        }
    } else {
        $skipped++
    }
}

Write-Host "\nSummary: Updated $success files, Skipped $skipped, Errors $errors" -ForegroundColor Cyan

# Clear cache
& ".\bin\cake.bat" cache clear_all
