# Update static assets URL in project_tmm
cd d:\xampp\htdocs\project_tmm

Write-Host "Updating static assets URL..." -ForegroundColor Yellow

# Update elegant.ctp layout
$layoutFile = "src\Template\Layout\elegant.ctp"
if (Test-Path $layoutFile) {
    $content = Get-Content $layoutFile -Raw -Encoding UTF8
    $content = $content -replace "'/static-assets'", "'/project_tmm'"
    $content | Out-File $layoutFile -Encoding UTF8 -NoNewline
    Write-Host "✓ Layout updated" -ForegroundColor Green
}

# Update bake templates
$bakeFiles = @(
    "src\Template\Bake\Template\index.ctp",
    "src\Template\Bake\Template\form.ctp",
    "src\Template\Bake\Template\add.ctp",
    "src\Template\Bake\Template\edit.ctp"
)

foreach ($file in $bakeFiles) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw -Encoding UTF8
        $content = $content -replace "'/static-assets'", "'/project_tmm'"
        $content | Out-File $file -Encoding UTF8 -NoNewline
        Write-Host "✓ Updated $(Split-Path $file -Leaf)" -ForegroundColor Green
    }
}

Write-Host "`nStatic asset URLs updated to /project_tmm/" -ForegroundColor Cyan
Write-Host "Done!" -ForegroundColor Green
