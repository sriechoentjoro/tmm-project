# Hide File/Image Fields in All View Templates - Simple Version
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Hide File Fields in View Templates" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# File patterns
$filePatterns = 'image|file|photo|document|foto|gambar|dokumen|attachment|pdf|scan|upload|media'

# Get all view.ctp files
$viewFiles = Get-ChildItem -Path "src\Template" -Filter "view.ctp" -Recurse -File

$totalFiles = $viewFiles.Count
$updatedFiles = 0

Write-Host "Found $totalFiles view.ctp files..." -ForegroundColor Yellow
Write-Host ""

foreach ($file in $viewFiles) {
    $relativePath = $file.FullName -replace [regex]::Escape($PWD.Path), ''
    Write-Host "Processing: $relativePath" -ForegroundColor Gray
    
    $lines = Get-Content $file.FullName
    $newLines = @()
    $modified = $false
    $i = 0
    
    while ($i -lt $lines.Count) {
        $line = $lines[$i]
        
        # Check if line contains github-detail-label with file field
        if ($line -match 'github-detail-label' -and $line -match $filePatterns) {
            # Found a file field, skip this row
            $fieldMatch = $null
            if ($line -match "__\('([^']+)'\)") {
                $fieldMatch = $Matches[1]
            }
            
            if ($fieldMatch) {
                Write-Host "  → Removing: $fieldMatch" -ForegroundColor Yellow
                $modified = $true
                
                # Find the start of <tr>
                $j = $i
                while ($j -gt 0 -and $lines[$j] -notmatch '<tr>') {
                    $j--
                }
                
                # Remove lines from <tr> to current position
                if ($j -ge 0) {
                    # Remove previously added lines back to <tr>
                    $lineCount = $i - $j + 1
                    $newLines = $newLines[0..($newLines.Count - $lineCount)]
                    
                    # Add comment
                    $indent = ' ' * 24
                    $newLines += "$indent<!-- $fieldMatch removed - already shown in preview section above -->"
                }
                
                # Skip until </tr>
                while ($i -lt $lines.Count -and $lines[$i] -notmatch '</tr>') {
                    $i++
                }
                $i++ # Skip </tr> line
                continue
            }
        }
        
        $newLines += $line
        $i++
    }
    
    if ($modified) {
        Set-Content -Path $file.FullName -Value $newLines -Encoding UTF8
        $updatedFiles++
        Write-Host "  ✓ Updated!" -ForegroundColor Green
    }
    
    Write-Host ""
}

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Files updated: $updatedFiles / $totalFiles" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Cyan
