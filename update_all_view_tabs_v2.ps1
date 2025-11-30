# ============================================
# UPDATE ALL VIEW TEMPLATES - ADD AJAX TABS
# ============================================

$ErrorActionPreference = "Continue"
$rootPath = "D:\xampp\htdocs\project_tmm"
$templatesPath = "$rootPath\src\Template"
$logFile = "$rootPath\update_view_tabs_log.txt"

# Initialize log
"Update View Tabs Script - Started: $(Get-Date)" | Out-File $logFile

# Function to check if file has related_records_table element
function Has-AjaxTabs($filePath) {
    $content = Get-Content $filePath -Raw -ErrorAction SilentlyContinue
    if ($content) {
        return $content -match "element\('related_records_table'"
    }
    return $false
}

# Function to find hasMany associations in Table file
function Get-HasManyAssociations($tableName) {
    $tableFile = "$rootPath\src\Model\Table\${tableName}sTable.php"
    if (-not (Test-Path $tableFile)) {
        $tableFile = "$rootPath\src\Model\Table\${tableName}Table.php"
    }
    
    if (-not (Test-Path $tableFile)) {
        return @()
    }
    
    $content = Get-Content $tableFile -Raw -ErrorAction SilentlyContinue
    if (-not $content) {
        return @()
    }
    
    $associations = @()
    
    # Match hasMany associations
    $matches = [regex]::Matches($content, "\-\>hasMany\('([^']+)'")
    foreach ($match in $matches) {
        $assocName = $match.Groups[1].Value
        $associations += $assocName
    }
    
    return $associations
}

# Function to create tabs HTML for a view template
function Create-TabsHtml($tableName, $associations) {
    $html = "`n    <!-- Related Records Tabs with AJAX Lazy Loading -->`n"
    $html += "    <div class=`"related-section mt-4`">`n"
    $html += "        <h3><i class=`"fas fa-link`"></i> <?= __('Related Records') ?></h3>`n"
    $html += "        `n"
    $html += "        <ul class=`"nav nav-tabs`" id=`"relatedTabs`" role=`"tablist`">`n"
    
    $index = 0
    $tabPanes = ""
    
    foreach ($assoc in $associations) {
        $active = ""
        $selected = "false"
        $show = ""
        
        if ($index -eq 0) {
            $active = "active"
            $selected = "true"
            $show = "show"
        }
        
        $tabId = $assoc.ToLower() -replace '[^a-z0-9]', '-'
        $foreignKey = $tableName.ToLower() + "_id"
        
        # Tab button
        $html += "            <li class=`"nav-item`" role=`"presentation`">`n"
        $html += "                <button class=`"nav-link $active`" id=`"$tabId-tab`" data-bs-toggle=`"tab`" `n"
        $html += "                        data-bs-target=`"#$tabId-pane`" type=`"button`" role=`"tab`" `n"
        $html += "                        aria-controls=`"$tabId-pane`" aria-selected=`"$selected`">`n"
        $html += "                    <?= __('$assoc') ?>`n"
        $html += "                </button>`n"
        $html += "            </li>`n"
        
        # Tab pane with AJAX element
        $tabPanes += "            <div class=`"tab-pane fade $active $show`" id=`"$tabId-pane`" role=`"tabpanel`" `n"
        $tabPanes += "                 aria-labelledby=`"$tabId-tab`" tabindex=`"0`">`n"
        $tabPanes += "                <?= `$this->element('related_records_table', [`n"
        $tabPanes += "                    'tabId' => '$tabId',`n"
        $tabPanes += "                    'title' => __('$assoc'),`n"
        $tabPanes += "                    'filterField' => '$foreignKey',`n"
        $tabPanes += "                    'filterValue' => `$entity->id,`n"
        $tabPanes += "                    'ajaxUrl' => `$this->Url->build(['controller' => '$assoc', 'action' => 'getRelated']),`n"
        $tabPanes += "                    'controller' => '$assoc',`n"
        $tabPanes += "                    'columns' => [`n"
        $tabPanes += "                        ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],`n"
        $tabPanes += "                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'sortable' => true],`n"
        $tabPanes += "                        ['name' => 'created', 'label' => 'Created', 'type' => 'datetime', 'sortable' => true]`n"
        $tabPanes += "                    ]`n"
        $tabPanes += "                ]) ?>`n"
        $tabPanes += "            </div>`n"
        
        $index++
    }
    
    $html += "        </ul>`n"
    $html += "        `n"
    $html += "        <div class=`"tab-content`" id=`"relatedTabsContent`">`n"
    $html += $tabPanes
    $html += "        </div>`n"
    $html += "    </div>`n"
    
    return $html
}

# Function to add AJAX tabs to view template
function Add-AjaxTabsToView($filePath, $tableName) {
    $content = Get-Content $filePath -Raw -ErrorAction SilentlyContinue
    
    if (-not $content) {
        "  ERROR: Could not read file: $filePath" | Out-File $logFile -Append
        return $false
    }
    
    # Check if already has AJAX tabs
    if (Has-AjaxTabs $filePath) {
        "  SKIP: Already has AJAX tabs: $filePath" | Out-File $logFile -Append
        return $false
    }
    
    # Get hasMany associations
    $associations = Get-HasManyAssociations $tableName
    
    if ($associations.Count -eq 0) {
        "  SKIP: No hasMany associations found for: $tableName" | Out-File $logFile -Append
        return $false
    }
    
    # Find the end of the main content (before closing php tag or last </div>)
    $insertPoint = $content.LastIndexOf('<?php $this->end(); ?>')
    if ($insertPoint -eq -1) {
        $insertPoint = $content.LastIndexOf('</div>')
        if ($insertPoint -eq -1) {
            "  ERROR: Could not find insertion point in: $filePath" | Out-File $logFile -Append
            return $false
        }
    }
    
    # Build tabs HTML
    $tabsHtml = Create-TabsHtml $tableName $associations
    
    # Insert tabs before closing
    $newContent = $content.Substring(0, $insertPoint) + $tabsHtml + "`n" + $content.Substring($insertPoint)
    
    # Write back to file
    try {
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($filePath, $newContent, $utf8NoBom)
        
        "  SUCCESS: Added AJAX tabs ($($associations.Count) tabs) to: $filePath" | Out-File $logFile -Append
        return $true
    }
    catch {
        "  ERROR: Failed to write file: $filePath - $_" | Out-File $logFile -Append
        return $false
    }
}

# Main execution
Write-Host "Starting to update all view templates with AJAX tabs..." -ForegroundColor Cyan
Write-Host ""

$totalFiles = 0
$updatedFiles = 0
$skippedFiles = 0
$errorFiles = 0

# Find all view.ctp files
$viewFiles = Get-ChildItem -Path $templatesPath -Recurse -Filter "view.ctp" -ErrorAction SilentlyContinue | Where-Object {
    $_.FullName -notmatch '\\Element\\' -and 
    $_.FullName -notmatch '\\Email\\' -and
    $_.FullName -notmatch '\\Layout\\'
}

foreach ($file in $viewFiles) {
    $totalFiles++
    
    # Extract table name from path
    $tableName = $file.Directory.Name
    
    Write-Host "Processing: $tableName\view.ctp" -ForegroundColor Yellow
    "Processing: $($file.FullName)" | Out-File $logFile -Append
    
    try {
        $updated = Add-AjaxTabsToView $file.FullName $tableName
        
        if ($updated) {
            $updatedFiles++
            Write-Host "  SUCCESS: Updated with AJAX tabs" -ForegroundColor Green
        } else {
            $skippedFiles++
            Write-Host "  SKIP: Skipped (already has tabs or no associations)" -ForegroundColor Gray
        }
    }
    catch {
        $errorFiles++
        Write-Host "  ERROR: $_" -ForegroundColor Red
        "  ERROR: $_" | Out-File $logFile -Append
    }
    
    Write-Host ""
}

# Summary
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host "UPDATE COMPLETE" -ForegroundColor Green
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host "Total files processed: $totalFiles" -ForegroundColor White
Write-Host "Files updated: $updatedFiles" -ForegroundColor Green
Write-Host "Files skipped: $skippedFiles" -ForegroundColor Yellow
Write-Host "Files with errors: $errorFiles" -ForegroundColor Red
Write-Host ""
Write-Host "Log file: $logFile" -ForegroundColor Cyan
Write-Host ""

# Write summary to log
"" | Out-File $logFile -Append
"================================================================================" | Out-File $logFile -Append
"SUMMARY" | Out-File $logFile -Append
"================================================================================" | Out-File $logFile -Append
"Total files processed: $totalFiles" | Out-File $logFile -Append
"Files updated: $updatedFiles" | Out-File $logFile -Append
"Files skipped: $skippedFiles" | Out-File $logFile -Append
"Files with errors: $errorFiles" | Out-File $logFile -Append
"Completed: $(Get-Date)" | Out-File $logFile -Append
