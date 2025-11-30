# ============================================
# Fix Datepicker Format Across All Templates
# ============================================
# Purpose: Update all datepicker initializations to use yyyy-mm-dd format
# Database: All date fields expect YYYY-MM-DD format
# ============================================

$ErrorActionPreference = "Stop"
$utf8NoBom = New-Object System.Text.UTF8Encoding $false

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Datepicker Format Fix - All Templates" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Define template directories to scan
$templateDirs = @(
    "src\Template\Candidates",
    "src\Template\Trainees",
    "src\Template\Apprentices",
    "src\Template\ApprenticeOrders",
    "src\Template\AcceptanceOrganizations",
    "src\Template\VocationalTrainingInstitutions",
    "src\Template\Users",
    "src\Template\MasterGenders",
    "src\Template\MasterReligions",
    "src\Template\MasterMarriageStatuses",
    "src\Template\MasterBloodTypes",
    "src\Template\MasterJobCategories",
    "src\Template\MasterEducationLevels",
    "src\Template\CooperativeAssociations"
)

$filesProcessed = 0
$filesUpdated = 0
$errors = @()

# Old datepicker patterns to replace
$oldPatterns = @(
    # jQuery UI Datepicker pattern
    @{
        Pattern = '(?s)\$\(''.datepicker''\)\.datepicker\(\{[^}]*changeYear:\s*true[^}]*\}\);'
        Name = "jQuery UI Datepicker"
    },
    # Bootstrap Datepicker without proper format
    @{
        Pattern = '(?s)\$\(''.datepicker''\)\.datepicker\(\{[^}]*format:\s*''[^'']+''[^}]*\}\);'
        Name = "Bootstrap Datepicker (old format)"
    }
)

# New standardized datepicker initialization
$newDatepickerInit = @'
    // Initialize Bootstrap Datepicker with correct format
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',  // MySQL date format
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        container: 'body',
        showOnFocus: true,
        zIndexOffset: 1050
    });
    
    // Fix datepicker CSS conflicts
    $('.datepicker-dropdown').css({
        'z-index': '1060',
        'display': 'block'
    });
    
    // Ensure datepicker opens below input
    $('.datepicker').on('show', function(e) {
        $('.datepicker-dropdown').css({
            'top': $(this).offset().top + $(this).outerHeight(),
            'left': $(this).offset().left
        });
    });
'@

# Function to check if file needs CSS include
function Add-DatepickerCSS {
    param (
        [string]$FilePath,
        [string]$Content
    )
    
    # Check if datepicker-fix.css is already included
    if ($Content -notmatch 'datepicker-fix\.css') {
        # Find the position to insert CSS (after form-confirm.js or at beginning)
        if ($Content -match '(<script src=.*?form-confirm\.js.*?</script>)') {
            $insertAfter = $matches[1]
            $newContent = $Content -replace [regex]::Escape($insertAfter), "$insertAfter`n<?= `$this->Html->css('datepicker-fix.css') ?>"
            return $newContent
        }
        elseif ($Content -match '(<\?php[\s\S]*?\?>)') {
            # Insert after PHP opening block
            $phpBlock = $matches[1]
            $newContent = $Content -replace [regex]::Escape($phpBlock), "$phpBlock`n<?= `$this->Html->css('datepicker-fix.css') ?>"
            return $newContent
        }
    }
    
    return $Content
}

# Process each template directory
foreach ($dir in $templateDirs) {
    $fullPath = Join-Path $PSScriptRoot $dir
    
    if (-not (Test-Path $fullPath)) {
        Write-Host "[-] Skipping $dir (not found)" -ForegroundColor Yellow
        continue
    }
    
    Write-Host "`nProcessing: $dir" -ForegroundColor Cyan
    Write-Host "----------------------------------------" -ForegroundColor Gray
    
    # Find all add.ctp and edit.ctp files
    $files = Get-ChildItem -Path $fullPath -Filter "*.ctp" -Recurse | Where-Object {
        $_.Name -match '(add|edit)\.ctp$'
    }
    
    foreach ($file in $files) {
        $filesProcessed++
        $relativePath = $file.FullName.Replace($PSScriptRoot, "").TrimStart('\')
        
        try {
            # Read file content
            $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
            $originalContent = $content
            $updated = $false
            
            # Check if file has datepicker
            if ($content -match '\.datepicker\(') {
                Write-Host "  [>] $($file.Name)" -ForegroundColor White -NoNewline
                
                # Replace old datepicker patterns
                if ($content -match '(?s)\$\(''.datepicker''\)\.datepicker\(\{[^}]*\}\);') {
                    # Find the entire datepicker block (including surrounding context)
                    $datepickerBlock = [regex]::Match($content, '(?s)(// .*?Datepicker.*?\n)?\$\(''.datepicker''\)\.datepicker\(\{[^}]*\}\);')
                    
                    if ($datepickerBlock.Success) {
                        # Replace the entire block
                        $content = $content.Replace($datepickerBlock.Value, $newDatepickerInit)
                        $updated = $true
                    }
                }
                
                # Add datepicker CSS if not present
                $contentWithCSS = Add-DatepickerCSS -FilePath $file.FullName -Content $content
                if ($contentWithCSS -ne $content) {
                    $content = $contentWithCSS
                    $updated = $true
                }
                
                # Ensure datepicker fields are excluded from uppercase transform
                if ($content -match '\$\(''input\[type="text"\], textarea''\)\.not\(.*?\)\.on\(''input''') {
                    if ($content -notmatch '\.not\(.*?\.datepicker.*?\)') {
                        # Add .datepicker to exclusion list
                        $content = $content -replace '(\.not\([^)]+)(,\s*\.no-uppercase)', '$1, .datepicker$2'
                        if ($content -notmatch ', \.datepicker') {
                            $content = $content -replace '(\.not\([^)]+)(\)\.on\(''input'')', '$1, .datepicker$2'
                        }
                        $updated = $true
                    }
                }
                
                # Save if updated
                if ($updated -and $content -ne $originalContent) {
                    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
                    Write-Host " - UPDATED" -ForegroundColor Green
                    $filesUpdated++
                }
                else {
                    Write-Host " - Already correct" -ForegroundColor Gray
                }
            }
        }
        catch {
            $errorMsg = "Error processing $($file.Name): $_"
            Write-Host "  [!] $($file.Name) - ERROR" -ForegroundColor Red
            $errors += $errorMsg
        }
    }
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Summary" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Files processed: $filesProcessed" -ForegroundColor White
Write-Host "Files updated: $filesUpdated" -ForegroundColor Green

if ($errors.Count -gt 0) {
    Write-Host "`nErrors encountered:" -ForegroundColor Red
    foreach ($error in $errors) {
        Write-Host "  - $error" -ForegroundColor Red
    }
}

Write-Host "`n[NEXT STEPS]" -ForegroundColor Yellow
Write-Host "1. Clear CakePHP cache: bin\cake cache clear_all" -ForegroundColor White
Write-Host "2. Test datepicker in forms" -ForegroundColor White
Write-Host "3. Verify date format: YYYY-MM-DD" -ForegroundColor White
Write-Host "4. Test date save to database" -ForegroundColor White

Write-Host "`nDone!" -ForegroundColor Green
