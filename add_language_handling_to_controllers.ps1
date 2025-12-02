# PowerShell Script: Add Language Handling to All Controller processFlow Methods
# Purpose: Update processFlow() methods to handle language switching via URL parameter

Write-Host "Starting to add language handling to controllers..." -ForegroundColor Green
Write-Host ""

$controllersPath = "src\Controller"
$controllers = Get-ChildItem -Path $controllersPath -Filter "*Controller.php" -Recurse
$updated = 0
$skipped = 0

foreach ($controller in $controllers) {
    # Skip AppController and Component folder
    if ($controller.Name -eq "AppController.php" -or $controller.FullName -like "*\Component\*") {
        Write-Host "Skipped: $($controller.Name) (excluded)" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    # Read file content
    $content = Get-Content -Path $controller.FullName -Raw -Encoding UTF8
    
    # Check if processFlow method exists
    if ($content -notlike "*function processFlow()*") {
        Write-Host "Skipped: $($controller.Name) (no processFlow method)" -ForegroundColor Gray
        $skipped++
        continue
    }
    
    # Check if already has language handling
    if ($content -like "*getQuery('lang')*") {
        Write-Host "Skipped: $($controller.Name) (already has language handling)" -ForegroundColor Gray
        $skipped++
        continue
    }
    
    # Define old method pattern (simple version)
    $oldMethod = @"
    public function processFlow()
    {
        `$this->viewBuilder()->setLayout('process_flow');
    }
"@
    
    # Define new method with language handling
    $newMethod = @"
    public function processFlow()
    {
        // Handle language switching
        if (`$lang = `$this->request->getQuery('lang')) {
            if (in_array(`$lang, ['ind', 'eng', 'jpn'])) {
                `$this->request->getSession()->write('Config.language', `$lang);
                return `$this->redirect(['action' => 'processFlow']);
            }
        }
        
        `$this->viewBuilder()->setLayout('process_flow');
    }
"@
    
    # Replace old method with new method
    $newContent = $content -replace [regex]::Escape($oldMethod), $newMethod
    
    # Check if replacement was successful
    if ($newContent -ne $content) {
        # Write updated content back to file (UTF-8 without BOM)
        $utf8 = New-Object System.Text.UTF8Encoding($false)
        [System.IO.File]::WriteAllText($controller.FullName, $newContent, $utf8)
        
        Write-Host "Updated: $($controller.Name)" -ForegroundColor Green
        $updated++
    } else {
        Write-Host "Skipped: $($controller.Name) (pattern not matched)" -ForegroundColor Yellow
        $skipped++
    }
}

Write-Host ""
Write-Host "Language handling update complete!" -ForegroundColor Green
Write-Host "Controllers updated: $updated" -ForegroundColor Cyan
Write-Host "Controllers skipped: $skipped" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Magenta
Write-Host "1. Test language switching on any process flow page" -ForegroundColor White
Write-Host "2. Verify language preference persists across sessions" -ForegroundColor White
Write-Host "3. Check for any errors in browser console" -ForegroundColor White
