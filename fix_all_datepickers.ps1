# ============================================
# FIX ALL DATEPICKERS - YYYY-MM-DD FORMAT
# ============================================
# Purpose: Update all template files to use Bootstrap Datepicker
#          with MySQL-compatible date format (YYYY-MM-DD)
# ============================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "FIXING ALL DATEPICKERS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Counter for statistics
$filesProcessed = 0
$filesUpdated = 0
$datepickerInstancesFixed = 0

# Find all .ctp files with datepicker
$templateFiles = Get-ChildItem -Path "src\Template" -Filter "*.ctp" -Recurse | Where-Object {
    $content = Get-Content $_.FullName -Raw -ErrorAction SilentlyContinue
    $content -match '\.datepicker\('
}

Write-Host "Found $($templateFiles.Count) template files with datepicker" -ForegroundColor Yellow
Write-Host ""

foreach ($file in $templateFiles) {
    $filesProcessed++
    $relativePath = $file.FullName.Replace((Get-Location).Path + "\", "")
    Write-Host "Processing: $relativePath" -ForegroundColor White
    
    $content = Get-Content $file.FullName -Raw
    $originalContent = $content
    
    # Pattern 1: jQuery UI Datepicker with changeYear, changeMonth, etc.
    $oldPattern1 = @'
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        changeYear: true,
        changeMonth: true,
        yearRange: '1900:+10',
        showButtonPanel: true,
        beforeShow: function(input, inst) {
            setTimeout(function() {
                var buttonPane = $(inst.dpDiv).find('.ui-datepicker-buttonpane');
                $('<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all">Today</button>')
                    .appendTo(buttonPane)
                    .on('click', function() {
                        $.datepicker._selectToday(input);
                    });
            }, 1);
        }
    });
'@

    $newPattern1 = @'
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

    if ($content -match [regex]::Escape($oldPattern1)) {
        $content = $content -replace [regex]::Escape($oldPattern1), $newPattern1
        $datepickerInstancesFixed++
        Write-Host "  ✓ Fixed jQuery UI datepicker" -ForegroundColor Green
    }
    
    # Pattern 2: Simple datepicker without options
    $pattern2 = '\$\(''.datepicker''\)\.datepicker\(\);'
    if ($content -match $pattern2) {
        $content = $content -replace $pattern2, @'
$('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        container: 'body'
    });
'@
        $datepickerInstancesFixed++
        Write-Host "  ✓ Fixed simple datepicker" -ForegroundColor Green
    }
    
    # Pattern 3: Datepicker with wrong format (mm/dd/yyyy, dd-mm-yyyy, etc.)
    $wrongFormats = @('mm/dd/yyyy', 'dd/mm/yyyy', 'dd-mm-yyyy', 'mm-dd-yyyy', 'd/m/Y', 'm/d/Y')
    foreach ($wrongFormat in $wrongFormats) {
        if ($content -match "format:\s*['""]$wrongFormat['""]") {
            $content = $content -replace "format:\s*['""]$wrongFormat['""]", "format: 'yyyy-mm-dd'"
            $datepickerInstancesFixed++
            Write-Host "  ✓ Fixed wrong format: $wrongFormat → yyyy-mm-dd" -ForegroundColor Green
        }
    }
    
    # Pattern 4: Fix auto-uppercase to exclude datepicker fields
    $uppercasePattern1 = '\$\(''input\[type="text"\], textarea''\)\.not\(''\[type="email"\], \[type="password"\], \[type="url"\], \.no-uppercase''\)'
    if ($content -match $uppercasePattern1) {
        $content = $content -replace $uppercasePattern1, '$$(''input[type="text"], textarea'').not(''[type="email"], [type="password"], [type="url"], .no-uppercase, .datepicker'')'
        Write-Host "  ✓ Fixed auto-uppercase to exclude datepicker" -ForegroundColor Green
    }
    
    # Pattern 5: Add datepicker CSS if not present
    if ($content -match '\.datepicker\(' -and $content -notmatch 'datepicker-fix\.css') {
        # Find the CSS loading section
        if ($content -match '<!-- Mobile CSS now loaded globally from layout - mobile-responsive\.css -->') {
            $content = $content -replace '(<!-- Mobile CSS now loaded globally from layout - mobile-responsive\.css -->)', 
                '<?= $$this->Html->css(''datepicker-fix.css'') ?>' + "`r`n`r`n" + '$$1'
            Write-Host "  ✓ Added datepicker-fix.css reference" -ForegroundColor Green
        }
    }
    
    # Save if changed
    if ($content -ne $originalContent) {
        # Use UTF-8 WITHOUT BOM
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
        $filesUpdated++
        Write-Host "  ✓ File updated successfully" -ForegroundColor Green
    } else {
        Write-Host "  - No changes needed" -ForegroundColor Gray
    }
    
    Write-Host ""
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "SUMMARY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Files processed: $filesProcessed" -ForegroundColor White
Write-Host "Files updated: $filesUpdated" -ForegroundColor Green
Write-Host "Datepicker instances fixed: $datepickerInstancesFixed" -ForegroundColor Green
Write-Host ""
Write-Host "✓ All datepickers now use YYYY-MM-DD format!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Clear cache: bin\cake cache clear_all" -ForegroundColor White
Write-Host "2. Test forms in browser" -ForegroundColor White
Write-Host "3. Verify dates save correctly to database" -ForegroundColor White
