# Project TMM - Complete Setup Script
# Run this to finish the configuration

Write-Host "=== PROJECT TMM SETUP ===" -ForegroundColor Cyan
Write-Host ""

# 1. Update static asset URLs in layout
Write-Host "1. Updating static asset URLs to webroot..." -ForegroundColor Yellow
$layoutPath = "src\Template\Layout\elegant.ctp"
$menuPath = "src\Template\Element\elegant_menu.ctp"

if (Test-Path $layoutPath) {
    $content = Get-Content $layoutPath -Raw -Encoding UTF8
    $content = $content -replace 'http://localhost/static-assets/', '/project_tmm/'
    $content | Set-Content $layoutPath -Encoding UTF8 -NoNewline
    Write-Host "   ✓ Layout URLs updated" -ForegroundColor Green
}

# 2. Update bake template URLs
Write-Host "2. Updating bake template URLs..." -ForegroundColor Yellow
$indexTemplate = "src\Template\Bake\Template\index.ctp"
if (Test-Path $indexTemplate) {
    $content = Get-Content $indexTemplate -Raw -Encoding UTF8
    $content = $content -replace 'http://localhost/static-assets/', '/project_tmm/'
    $content | Set-Content $indexTemplate -Encoding UTF8 -NoNewline
    Write-Host "   ✓ Index template URLs updated" -ForegroundColor Green
}

# 3. Copy Helper files
Write-Host "3. Copying Helper classes..." -ForegroundColor Yellow
if (Test-Path "..\asahi_v3\src\View\Helper") {
    xcopy /E /I /Y /Q "..\asahi_v3\src\View\Helper" "src\View\Helper" >$null 2>&1
    Write-Host "   ✓ Helper files copied" -ForegroundColor Green
} else {
    Write-Host "   ! Helper folder not found in asahi_v3" -ForegroundColor Red
}

# 4. Copy district-related controllers
Write-Host "4. Checking for district controllers..." -ForegroundColor Yellow
$districtControllers = @(
    "PropinsisController.php",
    "KabupatensController.php",
    "KecamatansController.php",
    "KelurahansController.php"
)

foreach ($controller in $districtControllers) {
    $sourcePath = "..\asahi_v3\src\Controller\$controller"
    if (Test-Path $sourcePath) {
        Copy-Item $sourcePath "src\Controller\" -Force
        Write-Host "   ✓ Copied $controller" -ForegroundColor Green
    }
}

# 5. Check Component folder
Write-Host "5. Ensuring Component folder exists..." -ForegroundColor Yellow
if (!(Test-Path "src\Controller\Component")) {
    New-Item -ItemType Directory -Path "src\Controller\Component" -Force | Out-Null
}
if (Test-Path "..\asahi_v3\src\Controller\Component") {
    xcopy /E /I /Y /Q "..\asahi_v3\src\Controller\Component\*.php" "src\Controller\Component\" >$null 2>&1
    Write-Host "   ✓ Components copied" -ForegroundColor Green
}

# 6. Update CSS files with new colors
Write-Host "6. Updating CSS files with teal colors..." -ForegroundColor Yellow
$cssFiles = Get-ChildItem "webroot\css\*.css" -ErrorAction SilentlyContinue
foreach ($file in $cssFiles) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $content = $content -replace '#667eea', '#00BCD4'
    $content = $content -replace '#764ba2', '#0097A7'
    $content | Set-Content $file.FullName -Encoding UTF8 -NoNewline
}
Write-Host "   ✓ CSS colors updated to teal gradient" -ForegroundColor Green

# 7. Verify structure
Write-Host ""
Write-Host "=== VERIFICATION ===" -ForegroundColor Cyan
Write-Host "Controllers:" -ForegroundColor Yellow
Get-ChildItem "src\Controller\*.php" | Select-Object -ExpandProperty Name | ForEach-Object {
    Write-Host "   $_" -ForegroundColor Gray
}

Write-Host "`nHelpers:" -ForegroundColor Yellow
if (Test-Path "src\View\Helper") {
    Get-ChildItem "src\View\Helper\*.php" | Select-Object -ExpandProperty Name | ForEach-Object {
        Write-Host "   $_" -ForegroundColor Gray
    }
} else {
    Write-Host "   (none)" -ForegroundColor Gray
}

Write-Host "`nStatic Assets in webroot:" -ForegroundColor Yellow
Write-Host "   CSS files: $((Get-ChildItem 'webroot\css\*.css' -ErrorAction SilentlyContinue).Count)" -ForegroundColor Gray
Write-Host "   JS files: $((Get-ChildItem 'webroot\js\*.js' -ErrorAction SilentlyContinue).Count)" -ForegroundColor Gray
Write-Host "   Webfonts: $((Get-ChildItem 'webroot\webfonts\*' -ErrorAction SilentlyContinue).Count)" -ForegroundColor Gray

Write-Host ""
Write-Host "=== SETUP COMPLETE ===" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Configure database in config\app_datasources.php"
Write-Host "2. Run: bin\cake server -p 8765"
Write-Host "3. Start baking tables with: bin\cake bake all TableName --force"
Write-Host ""
