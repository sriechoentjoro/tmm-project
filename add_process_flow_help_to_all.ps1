# ========================================
# Add Process Flow Help Button to All Templates
# ========================================
# 
# This script adds the floating help button to all index, view, add, and edit templates
# The button opens an interactive process flow documentation for each controller
#
# Author: TMM Development Team
# Date: December 1, 2025
# ========================================

Write-Host "`n╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Add Process Flow Help Button to All Templates        ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

$templatePath = "src\Template"
$elementCode = "<!-- Process Flow Help Button -->`n<?= `$this->element('process_flow_help') ?>"

$stats = @{
    Total = 0
    Success = 0
    Skipped = 0
    Failed = 0
}

# Function to add help button if not exists
function Add-HelpButton {
    param(
        [string]$FilePath
    )
    
    $stats.Total++
    
    try {
        $content = Get-Content -Path $FilePath -Raw -Encoding UTF8
        
        # Check if already has help button
        if ($content -match 'process_flow_help') {
            Write-Host "  ⊘ SKIPPED: $FilePath (already exists)" -ForegroundColor Yellow
            $stats.Skipped++
            return
        }
        
        # Add help button before closing </div> or at end of file
        if ($content -match '(?s)(.*)(</div>\s*</div>\s*</div>\s*)$') {
            $newContent = $content -replace '(?s)(</div>\s*</div>\s*</div>\s*)$', "`n$elementCode`n`$1"
        }
        elseif ($content -match '(?s)(.*)(</style>\s*)$') {
            $newContent = $content -replace '(?s)(</style>\s*)$', "`$1`n$elementCode"
        }
        else {
            # Append at end
            $newContent = $content + "`n`n$elementCode"
        }
        
        # Save with UTF-8 NO BOM
        $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
        [System.IO.File]::WriteAllText($FilePath, $newContent, $utf8NoBom)
        
        Write-Host "  ✓ ADDED: $FilePath" -ForegroundColor Green
        $stats.Success++
    }
    catch {
        Write-Host "  ✗ FAILED: $FilePath - $($_.Exception.Message)" -ForegroundColor Red
        $stats.Failed++
    }
}

# Find all index, view, add, edit templates
Write-Host "Scanning templates..." -ForegroundColor Cyan

$templates = @(
    Get-ChildItem -Path $templatePath -Filter "index.ctp" -Recurse
    Get-ChildItem -Path $templatePath -Filter "view.ctp" -Recurse
    Get-ChildItem -Path $templatePath -Filter "add.ctp" -Recurse
    Get-ChildItem -Path $templatePath -Filter "edit.ctp" -Recurse
)

Write-Host "Found $($templates.Count) templates to process`n" -ForegroundColor White

foreach ($template in $templates) {
    # Skip Element and Email folders
    if ($template.FullName -match '\\Element\\|\\Email\\') {
        continue
    }
    
    Add-HelpButton -FilePath $template.FullName
}

# Summary
Write-Host "`n╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Summary                                               ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "Total Templates: $($stats.Total)" -ForegroundColor White
Write-Host "✓ Successfully Added: $($stats.Success)" -ForegroundColor Green
Write-Host "⊘ Skipped (Already Exists): $($stats.Skipped)" -ForegroundColor Yellow
Write-Host "✗ Failed: $($stats.Failed)" -ForegroundColor Red

if ($stats.Success -gt 0) {
    Write-Host "`n`nProcess flow help buttons added successfully!" -ForegroundColor Green
    Write-Host "Next step: Create processFlow() method in each controller" -ForegroundColor Cyan
}

Write-Host "`nPress any key to continue..."
$null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
