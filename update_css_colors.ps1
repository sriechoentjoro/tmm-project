# Update CSS files with teal mobile app colors
Set-Location d:\xampp\htdocs\project_tmm\webroot\css

Write-Host "Updating CSS files with teal gradient..." -ForegroundColor Yellow

$cssFiles = Get-ChildItem "*.css"

foreach ($file in $cssFiles) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    
    # Replace purple colors with teal
    $content = $content -replace '#667eea', '#00BCD4'
    $content = $content -replace '#764ba2', '#0097A7'
    $content = $content -replace 'rgba\(102,\s*126,\s*234', 'rgba(0, 188, 212'
    $content = $content -replace 'rgba\(102, 126, 234', 'rgba(0, 188, 212'
    
    $content | Out-File $file.FullName -Encoding UTF8 -NoNewline
    Write-Host "✓ Updated $($file.Name)" -ForegroundColor Green
}

Write-Host "`nAll CSS files updated to teal gradient!" -ForegroundColor Cyan
