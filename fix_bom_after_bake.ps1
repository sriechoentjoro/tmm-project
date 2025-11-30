# Fix UTF-8 BOM Issue After Baking
# Run this script after baking new tables to remove BOM from generated files

$projectRoot = "d:\xampp\htdocs\project_tmm"

Write-Host "Fixing UTF-8 BOM in generated files..." -ForegroundColor Cyan

# Fix Controllers
$controllers = Get-ChildItem "$projectRoot\src\Controller\*.php"
foreach ($file in $controllers) {
    $content = Get-Content $file.FullName -Raw
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
}
Write-Host "✓ Fixed $($controllers.Count) Controller files" -ForegroundColor Green

# Fix Table files
$tables = @(Get-ChildItem "$projectRoot\src\Model\Table\*.php" -ErrorAction SilentlyContinue)
foreach ($file in $tables) {
    $content = Get-Content $file.FullName -Raw
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
}
Write-Host "✓ Fixed $($tables.Count) Table files" -ForegroundColor Green

# Fix Entity files
$entities = @(Get-ChildItem "$projectRoot\src\Model\Entity\*.php" -ErrorAction SilentlyContinue)
foreach ($file in $entities) {
    $content = Get-Content $file.FullName -Raw
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
}
Write-Host "✓ Fixed $($entities.Count) Entity files" -ForegroundColor Green

# Fix Template files
$templates = @(Get-ChildItem "$projectRoot\src\Template" -Recurse -Filter "*.ctp" -ErrorAction SilentlyContinue)
foreach ($file in $templates) {
    $content = Get-Content $file.FullName -Raw
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
}
Write-Host "✓ Fixed $($templates.Count) Template files" -ForegroundColor Green

Write-Host "`nAll files fixed! BOM removed successfully." -ForegroundColor Yellow
Write-Host "Run this script after every bake to prevent namespace errors." -ForegroundColor Gray
