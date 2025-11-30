# PROJECT TMM - FINAL SETUP
Write-Host "PROJECT TMM - FINAL SETUP" -ForegroundColor Cyan
Write-Host ""

Set-Location d:\xampp\htdocs\project_tmm

# Step 1
Write-Host "[1/4] Updating URLs..." -ForegroundColor Yellow
$layoutFile = "src\Template\Layout\elegant.ctp"
if (Test-Path $layoutFile) {
    $content = Get-Content $layoutFile -Raw -Encoding UTF8
    $content = $content -replace "'/static-assets'", "'/project_tmm'"
    $content | Out-File $layoutFile -Encoding UTF8 -NoNewline
    Write-Host "      URLs updated" -ForegroundColor Green
}

# Step 2  
Write-Host "[2/4] Updating CSS..." -ForegroundColor Yellow
$cssFiles = Get-ChildItem "webroot\css\*.css" -ErrorAction SilentlyContinue
foreach ($cssFile in $cssFiles) {
    $content = Get-Content $cssFile.FullName -Raw -Encoding UTF8
    $content = $content -replace '#667eea', '#00BCD4'
    $content = $content -replace '#764ba2', '#0097A7'
    $content | Out-File $cssFile.FullName -Encoding UTF8 -NoNewline
}
Write-Host "      CSS updated" -ForegroundColor Green

# Step 3
Write-Host "[3/4] Copying controllers..." -ForegroundColor Yellow
$controllers = @("PropinsisController.php","KabupatensController.php","KecamatansController.php","KelurahansController.php")
foreach ($ctrl in $controllers) {
    $src = "..\asahi_v3\src\Controller\$ctrl"
    if (Test-Path $src) {
        Copy-Item $src "src\Controller\" -Force
    }
}
Write-Host "      Controllers copied" -ForegroundColor Green

# Step 4
Write-Host "[4/4] Copying components..." -ForegroundColor Yellow
if (Test-Path "..\asahi_v3\src\Controller\Component") {
    Copy-Item "..\asahi_v3\src\Controller\Component\*.php" "src\Controller\Component\" -Force -ErrorAction SilentlyContinue
    Write-Host "      Components copied" -ForegroundColor Green
}

Write-Host ""
Write-Host "SETUP COMPLETE!" -ForegroundColor Green
Write-Host "Color: Teal Gradient (#00BCD4 -> #00838F)" -ForegroundColor White
Write-Host "Static Assets: webroot/" -ForegroundColor White
Write-Host "Controllers: $((Get-ChildItem 'src\Controller\*.php').Count) files" -ForegroundColor White
