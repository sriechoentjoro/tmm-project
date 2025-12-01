# Add processFlow Method to All Controllers
# Simple version

Write-Host "`nAdding processFlow Method to All Controllers..." -ForegroundColor Cyan

$controllerPath = "src\Controller"

$method = @'

    /**
     * Process Flow Documentation
     */
    public function processFlow()
    {
        $this->viewBuilder()->setLayout('process_flow');
    }
'@

$added = 0
$skipped = 0

# Find controllers
$controllers = Get-ChildItem -Path $controllerPath -Filter "*Controller.php" -Recurse

foreach ($file in $controllers) {
    # Skip AppController and Component
    if ($file.Name -eq "AppController.php" -or $file.FullName -like "*\Component\*") {
        continue
    }
    
    $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
    
    # Check if already exists
    if ($content -like "*function processFlow*") {
        Write-Host "SKIP: $($file.Name)" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    # Add before closing brace
    $newContent = $content -replace '(}\s*)$', "$method`n}"
    
    # Save
    $utf8 = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($file.FullName, $newContent, $utf8)
    
    Write-Host "ADD:  $($file.Name)" -ForegroundColor Green
    $added++
}

Write-Host "`nSummary:" -ForegroundColor Cyan
Write-Host "Added: $added" -ForegroundColor Green
Write-Host "Skipped: $skipped" -ForegroundColor Yellow
Write-Host "`nDone!" -ForegroundColor Green
