# ========================================
# Add processFlow Method to All Controllers
# ========================================
# 
# This script adds processFlow() method to all controllers
# that don't already have it
#
# Author: TMM Development Team
# Date: December 1, 2025
# ========================================

Write-Host "`n╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Add processFlow Method to All Controllers            ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

$controllerPath = "src\Controller"

$methodCode = "

    /**
     * Process Flow Documentation
     *
     * Displays interactive process flow diagram with database relationships
     * Helps users understand data flow and table associations
     *
     * @return \Cake\Http\Response|null|void
     */
    public function processFlow()
    {
        // This action renders the process flow documentation
        // Layout: src/Template/Layout/process_flow.ctp
        
        `$this->viewBuilder()->setLayout('process_flow');
    }
"

$stats = @{
    Total = 0
    Success = 0
    Skipped = 0
    Failed = 0
}

# Function to add method if not exists
function Add-ProcessFlowMethod {
    param(
        [string]$FilePath
    )
    
    $stats.Total++
    
    try {
        $content = Get-Content -Path $FilePath -Raw -Encoding UTF8
        
        # Check if already has processFlow method
        if ($content -match 'function processFlow\(\)') {
            Write-Host "  ⊘ SKIPPED: $FilePath (already exists)" -ForegroundColor Yellow
            $stats.Skipped++
            return
        }
        
        # Find closing brace of class
        if ($content -match '(?s)(.*)(}\s*)$') {
            $newContent = $content -replace '(?s)(}\s*)$', "$methodCode`n}"
        }
        else {
            Write-Host "  ✗ FAILED: $FilePath (cannot find class closing brace)" -ForegroundColor Red
            $stats.Failed++
            return
        }
        
        # Save with UTF-8 NO BOM
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($FilePath, $newContent, $utf8NoBom)
        
        Write-Host "  ✓ ADDED: $FilePath" -ForegroundColor Green
        $stats.Success++
    }
    catch {
        Write-Host "  ✗ FAILED: $FilePath - $($_.Exception.Message)" -ForegroundColor Red
        $stats.Failed++
    }
}

# Find all controllers
Write-Host "Scanning controllers..." -ForegroundColor Cyan

$controllers = Get-ChildItem -Path $controllerPath -Filter "*Controller.php" -Recurse

Write-Host "Found $($controllers.Count) controllers to process`n" -ForegroundColor White

foreach ($controller in $controllers) {
    # Skip AppController and Component folders
    if ($controller.Name -eq "AppController.php" -or $controller.FullName -match '\\Component\\') {
        continue
    }
    
    Add-ProcessFlowMethod -FilePath $controller.FullName
}

# Summary
Write-Host "`n╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Summary                                               ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "Total Controllers: $($stats.Total)" -ForegroundColor White
Write-Host "✓ Successfully Added: $($stats.Success)" -ForegroundColor Green
Write-Host "⊘ Skipped (Already Exists): $($stats.Skipped)" -ForegroundColor Yellow
Write-Host "✗ Failed: $($stats.Failed)" -ForegroundColor Red

if ($stats.Success -gt 0) {
    Write-Host "`nprocessFlow methods added successfully!" -ForegroundColor Green
    Write-Host "Next step: Create process_flow.ctp template for each controller" -ForegroundColor Cyan
}

Write-Host "`nPress any key to continue..."
$null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
