# ============================================
# Fix Datepicker Format - ALL Templates (Complete)
# ============================================
# Purpose: Update ALL datepicker initializations to use yyyy-mm-dd format
# Database: All date fields expect YYYY-MM-DD format
# Scope: Scan entire src/Template directory recursively
# ============================================

$ErrorActionPreference = "Stop"
$utf8NoBom = New-Object System.Text.UTF8Encoding $false

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Datepicker Format Fix - ALL Templates" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$templateRoot = Join-Path $PSScriptRoot "src\Template"
$filesProcessed = 0
$filesUpdated = 0
$filesSkipped = 0
$errors = @()

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
        [string]$Content
    )
    
    # Check if datepicker-fix.css is already included
    if ($Content -notmatch 'datepicker-fix\.css') {
        # Find the position to insert CSS
        if ($Content -match '(<script src=.*?form-confirm\.js.*?</script>)') {
            $insertAfter = $matches[1]
            $newContent = $Content -replace [regex]::Escape($insertAfter), "$insertAfter`n<?= `$this->Html->css('datepicker-fix.css') ?>"
            return @{ Updated = $true; Content = $newContent }
        }
        elseif ($Content -match '(<\?php[\s\S]{1,500}?\?>)') {
            # Insert after PHP opening block (within first 500 chars)
            $phpBlock = $matches[1]
            $newContent = $Content -replace [regex]::Escape($phpBlock), "$phpBlock`n<?= `$this->Html->css('datepicker-fix.css') ?>"
            return @{ Updated = $true; Content = $newContent }
        }
    }
    
    return @{ Updated = $false; Content = $Content }
}

Write-Host "Scanning template directory: $templateRoot" -ForegroundColor White
Write-Host ""

# Find ALL .ctp files recursively
$allFiles = Get-ChildItem -Path $templateRoot -Filter "*.ctp" -Recurse

Write-Host "Found $($allFiles.Count) template files" -ForegroundColor Cyan
Write-Host "Processing files with datepicker..." -ForegroundColor Cyan
Write-Host "----------------------------------------" -ForegroundColor Gray

foreach ($file in $allFiles) {
    try {
        # Read file content
        $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
        
        # Check if file has datepicker
        if ($content -match '\.datepicker\(|class=["\'].*datepicker') {
            $filesProcessed++
            $relativePath = $file.FullName.Replace($PSScriptRoot, "").TrimStart('\')
            $originalContent = $content
            $updated = $false
            
            Write-Host "  [>] $relativePath" -ForegroundColor White -NoNewline
            
            # Replace old datepicker patterns (jQuery UI style with changeYear, changeMonth, etc.)
            if ($content -match '(?s)\$\(''.datepicker''\)\.datepicker\(\{[^}]*changeYear:\s*true[^}]*\}\);') {
                # Find the entire jQuery UI datepicker block
                $datepickerBlock = [regex]::Match($content, '(?s)(// .*?Datepicker.*?\n)?\$\(''.datepicker''\)\.datepicker\(\{[^}]*changeYear:\s*true[^}]*\}\);')
                
                if ($datepickerBlock.Success) {
                    $content = $content.Replace($datepickerBlock.Value, $newDatepickerInit)
                    $updated = $true
                }
            }
            # Replace Bootstrap Datepicker with old format or missing options
            elseif ($content -match '(?s)\$\(''.datepicker''\)\.datepicker\(\{[^}]*format:\s*''yyyy-mm-dd''[^}]*\}\);' -and 
                    $content -notmatch 'orientation:\s*''bottom auto''') {
                # Has format but missing other options
                $datepickerBlock = [regex]::Match($content, '(?s)(// .*?Datepicker.*?\n)?\$\(''.datepicker''\)\.datepicker\(\{[^}]*\}\);')
                
                if ($datepickerBlock.Success) {
                    $content = $content.Replace($datepickerBlock.Value, $newDatepickerInit)
                    $updated = $true
                }
            }
            # Any other datepicker initialization that needs updating
            elseif ($content -match '\$\(''.datepicker''\)\.datepicker\(' -and 
                    $content -notmatch 'orientation:\s*''bottom auto''') {
                # Has datepicker but not updated yet
                $datepickerBlock = [regex]::Match($content, '(?s)(// .*?Datepicker.*?\n)?\$\(''.datepicker''\)\.datepicker\(\{[^}]*\}\);')
                
                if ($datepickerBlock.Success) {
                    $content = $content.Replace($datepickerBlock.Value, $newDatepickerInit)
                    $updated = $true
                }
            }
            
            # Add datepicker CSS if not present
            $cssResult = Add-DatepickerCSS -Content $content
            if ($cssResult.Updated) {
                $content = $cssResult.Content
                $updated = $true
            }
            
            # Ensure datepicker fields are excluded from uppercase transform
            if ($content -match '\$\(''input\[type="text"\], textarea''\)\.not\(.*?\)\.on\(''input''') {
                if ($content -notmatch '\.not\(.*?\.datepicker.*?\)') {
                    # Add .datepicker to exclusion list
                    $content = $content -replace '(\.not\([^\)]+)(\)\.on\(''input'')', '$1, .datepicker$2'
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
                $filesSkipped++
            }
        }
    }
    catch {
        $errorMsg = "Error processing $($file.Name): $_"
        Write-Host "  [!] $($file.FullName) - ERROR" -ForegroundColor Red
        $errors += $errorMsg
    }
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Summary" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Total template files scanned: $($allFiles.Count)" -ForegroundColor White
Write-Host "Files with datepicker: $filesProcessed" -ForegroundColor White
Write-Host "Files updated: $filesUpdated" -ForegroundColor Green
Write-Host "Files already correct: $filesSkipped" -ForegroundColor Gray

if ($errors.Count -gt 0) {
    Write-Host "`nErrors encountered:" -ForegroundColor Red
    foreach ($error in $errors) {
        Write-Host "  - $error" -ForegroundColor Red
    }
}

Write-Host "`n[NEXT STEPS]" -ForegroundColor Yellow
Write-Host "1. Clear CakePHP cache: bin\cake cache clear_all" -ForegroundColor White
Write-Host "2. Test datepicker in various forms" -ForegroundColor White
Write-Host "3. Verify date format: YYYY-MM-DD" -ForegroundColor White
Write-Host "4. Test date save to database" -ForegroundColor White

Write-Host "`nDone!" -ForegroundColor Green
