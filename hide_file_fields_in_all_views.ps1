# Hide File/Image Fields in All View Templates
# This script removes file/image field rows from detail sections in all view.ctp files

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Hide File Fields in View Templates" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# File/image field patterns to detect
$filePatterns = @(
    'image', 'file', 'photo', 'document', 'foto', 'gambar', 
    'dokumen', 'attachment', 'pdf', 'doc', 'excel', 'picture',
    'scan', 'upload', 'media', 'asset'
)

# Get all view.ctp files
$viewFiles = Get-ChildItem -Path "src\Template" -Filter "view.ctp" -Recurse -File

$totalFiles = $viewFiles.Count
$processedFiles = 0
$updatedFiles = 0
$fieldPatternRegex = '(' + ($filePatterns -join '|') + ')'

Write-Host "Found $totalFiles view.ctp files to check..." -ForegroundColor Yellow
Write-Host ""

foreach ($file in $viewFiles) {
    $processedFiles++
    $filePath = $file.FullName
    $relativePath = $filePath -replace [regex]::Escape($PWD.Path + '\'), ''
    
    Write-Host "[$processedFiles/$totalFiles] Checking: $relativePath" -ForegroundColor Gray
    
    $content = Get-Content $filePath -Raw
    $originalContent = $content
    $modified = $false
    
    # Pattern 1: Remove entire <tr> blocks for file fields
    # Match pattern: <tr>\s*<th...>Field Name</th>\s*<td...>...</td>\s*</tr>
    $lines = Get-Content $filePath
    $newLines = @()
    $skipNext = 0
    $inFileFieldRow = $false
    
    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i]
        
        # Check if this line starts a table row with a file field
        if ($line -match '<tr>' -and $i + 1 -lt $lines.Count) {
            $nextLine = $lines[$i + 1]
            
            # Check if next line contains a file-related field label
            if ($nextLine -match "github-detail-label.*?__\('([^']+)'\)" -or 
                $nextLine -match "github-detail-label.*?>([^<]+)<") {
                $fieldName = $Matches[1]
                
                # Check if field name matches file patterns
                if ($fieldName -match $fieldPatternRegex) {
                    $inFileFieldRow = $true
                    $modified = $true
                    Write-Host "  → Removing field: $fieldName" -ForegroundColor Yellow
                    
                    # Add comment instead of the row
                    $newLines += "                        <!-- $fieldName removed - already shown in preview section above -->"
                    
                    # Skip lines until we find </tr>
                    while ($i -lt $lines.Count -and $lines[$i] -notmatch '</tr>') {
                        $i++
                    }
                    $inFileFieldRow = $false
                    continue
                }
            }
        }
        
        # Add line if not skipped
        $newLines += $line
    }
    
    if ($modified) {
        # Write updated content
        Set-Content -Path $filePath -Value $newLines -Encoding UTF8
        $updatedFiles++
        Write-Host "  ✓ Updated!" -ForegroundColor Green
    } else {
        Write-Host "  - No file fields found" -ForegroundColor DarkGray
    }
    
    Write-Host ""
}

# Summary
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "SUMMARY" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Files checked: $totalFiles" -ForegroundColor White
Write-Host "Files updated: $updatedFiles" -ForegroundColor Green
Write-Host ""

if ($updatedFiles -gt 0) {
    Write-Host "✓ All file/image fields hidden from detail sections!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Files are still shown in:" -ForegroundColor Yellow
    Write-Host "  - Media Preview section (at top of view pages)" -ForegroundColor Gray
    Write-Host "  - With full preview functionality (PDF iframe, image display, etc.)" -ForegroundColor Gray
} else {
    Write-Host "No updates needed - all views are clean!" -ForegroundColor Green
}

Write-Host ""
Write-Host "Script complete!" -ForegroundColor Cyan
