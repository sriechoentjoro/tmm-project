# Add Process Flow Help Button to All Templates
# Simple version

Write-Host "`nAdding Process Flow Help Button to All Templates..." -ForegroundColor Cyan

$templatePath = "src\Template"
$helpButton = "`n<!-- Process Flow Help Button -->`n<?= `$this->element('process_flow_help') ?>`n"

$added = 0
$skipped = 0

# Find templates
$templates = Get-ChildItem -Path $templatePath -Include "index.ctp","view.ctp","add.ctp","edit.ctp" -Recurse

foreach ($file in $templates) {
    # Skip Element and Email folders
    if ($file.FullName -like "*\Element\*" -or $file.FullName -like "*\Email\*") {
        continue
    }
    
    $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
    
    # Check if already exists
    if ($content -like "*process_flow_help*") {
        Write-Host "SKIP: $($file.FullName)" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    # Add at end of file
    $newContent = $content + $helpButton
    
    # Save
    $utf8 = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($file.FullName, $newContent, $utf8)
    
    Write-Host "ADD:  $($file.FullName)" -ForegroundColor Green
    $added++
}

Write-Host "`nSummary:" -ForegroundColor Cyan
Write-Host "Added: $added" -ForegroundColor Green
Write-Host "Skipped: $skipped" -ForegroundColor Yellow
Write-Host "`nDone!" -ForegroundColor Green
