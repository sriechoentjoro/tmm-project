# ============================================
# UPDATE ALL VIEW TEMPLATES - ADD AJAX TABS
# ============================================
# Purpose: Add AJAX lazy-loading tabs to all view templates
# for related records (hasMany associations)
# ============================================

$ErrorActionPreference = "Continue"
$rootPath = "D:\xampp\htdocs\project_tmm"
$templatesPath = "$rootPath\src\Template"
$logFile = "$rootPath\update_view_tabs_log.txt"

# Initialize log
"" | Out-File $logFile
"Update View Tabs Script - Started: $(Get-Date)" | Out-File $logFile -Append
"=" * 80 | Out-File $logFile -Append

# Function to check if file has related_records_table element
function Has-AjaxTabs($filePath) {
    $content = Get-Content $filePath -Raw
    return $content -match "element\('related_records_table'"
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
    
    $content = Get-Content $tableFile -Raw
    $associations = @()
    
    # Match hasMany associations
    $matches = [regex]::Matches($content, "\-\>hasMany\('([^']+)'")
    foreach ($match in $matches) {
        $assocName = $match.Groups[1].Value
        $associations += $assocName
    }
    
    return $associations
}

# Function to get controller name from table name
function Get-ControllerName($tableName) {
    # Convert from PascalCase to dash-case for URL
    $controller = $tableName -creplace '([A-Z])', '-$1'
    $controller = $controller.TrimStart('-').ToLower()
    return $controller
}

# Function to get primary key field name
function Get-PrimaryKeyField($tableName) {
    # Most tables use id, but some might use custom primary key
    # For now, default to id
    return "id"
}

# Function to determine important columns for related table
function Get-ImportantColumns($tableName) {
    # Common column patterns
    $columns = @()
    
    # Always include id first
    $columns += @{
        name = "id"
        label = "ID"
        type = "number"
        sortable = $true
    }
    
    # Add common name/title columns
    $commonNames = @('name', 'title', 'fullname', 'code', 'number')
    foreach ($name in $commonNames) {
        $columns += @{
            name = $name
            label = ($name -replace '_', ' ').ToUpper()
            type = "text"
            sortable = $true
        }
    }
    
    # Add date columns
    $columns += @{
        name = "created"
        label = "Created"
        type = "datetime"
        sortable = $true
    }
    
    return $columns
}

# Function to add AJAX tabs to view template
function Add-AjaxTabsToView($filePath, $tableName) {
    $content = Get-Content $filePath -Raw
    
    # Check if already has AJAX tabs
    if (Has-AjaxTabs $filePath) {
        "  ✓ Already has AJAX tabs: $filePath" | Out-File $logFile -Append
        return $false
    }
    
    # Get hasMany associations
    $associations = Get-HasManyAssociations $tableName
    
    if ($associations.Count -eq 0) {
        "  - No hasMany associations found for: $tableName" | Out-File $logFile -Append
        return $false
    }
    
    # Find the end of the main content (before closing tags)
    $insertPoint = $content.LastIndexOf('</div>')
    if ($insertPoint -eq -1) {
        "  ✗ Could not find insertion point in: $filePath" | Out-File $logFile -Append
        return $false
    }
    
    # Build tabs HTML
    $tabsHtml = @"

    <!-- Related Records Tabs with AJAX Lazy Loading -->
    <div class="related-section mt-4">
        <h3><i class="fas fa-link"></i> <?= __('Related Records') ?></h3>
        
        <ul class="nav nav-tabs" id="relatedTabs" role="tablist">

"@
    
    $tabContent = @"
        </ul>
        
        <div class="tab-content" id="relatedTabsContent">

"@
    
    $index = 0
    foreach ($assoc in $associations) {
        $active = if ($index -eq 0) { "active" } else { "" }
        $selected = if ($index -eq 0) { "true" } else { "false" }
        $tabId = $assoc.ToLower() -replace '[^a-z0-9]', '-'
        $controllerName = Get-ControllerName $assoc
        $foreignKey = "${tableName}_id".ToLower()
        
        # Tab header
        $tabsHtml += @"
            <li class="nav-item" role="presentation">
                <button class="nav-link $active" id="$tabId-tab" data-bs-toggle="tab" 
                        data-bs-target="#$tabId-pane" type="button" role="tab" 
                        aria-controls="$tabId-pane" aria-selected="$selected">
                    <?= __('$assoc') ?>
                </button>
            </li>

"@
        
        # Tab content with AJAX element
        $columns = Get-ImportantColumns $assoc
        $columnsJson = $columns | ConvertTo-Json -Compress
        
        $tabContent += @"
            <div class="tab-pane fade $active show" id="$tabId-pane" role="tabpanel" 
                 aria-labelledby="$tabId-tab" tabindex="0">
                <?= `$this->element('related_records_table', [
                    'tabId' => '$tabId',
                    'title' => __('$assoc'),
                    'filterField' => '$foreignKey',
                    'filterValue' => `$entity->id,
                    'ajaxUrl' => `$this->Url->build(['controller' => '$assoc', 'action' => 'getRelated$(($tableName))s']),
                    'controller' => '$controllerName',
                    'columns' => $columnsJson
                ]) ?>
            </div>

"@
        
        $index++
    }
    
    $tabsHtml += $tabContent
    $tabsHtml += @"
        </div>
    </div>

"@
    
    # Insert tabs before closing div
    $newContent = $content.Substring(0, $insertPoint) + $tabsHtml + $content.Substring($insertPoint)
    
    # Write back to file
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($filePath, $newContent, $utf8NoBom)
    
    "  ✓ Added AJAX tabs ($($associations.Count) tabs) to: $filePath" | Out-File $logFile -Append
    return $true
}

# Main execution
Write-Host "Starting to update all view templates with AJAX tabs..." -ForegroundColor Cyan
Write-Host ""

$totalFiles = 0
$updatedFiles = 0
$skippedFiles = 0
$errorFiles = 0

# Find all view.ctp files
$viewFiles = Get-ChildItem -Path $templatesPath -Recurse -Filter "view.ctp" | Where-Object {
    $_.FullName -notmatch '\\Element\\' -and 
    $_.FullName -notmatch '\\Email\\' -and
    $_.FullName -notmatch '\\Layout\\'
}

foreach ($file in $viewFiles) {
    $totalFiles++
    
    # Extract table name from path (e.g., Candidates from Candidates\view.ctp)
    $tableName = $file.Directory.Name
    
    Write-Host "Processing: $tableName\view.ctp" -ForegroundColor Yellow
    "Processing: $($file.FullName)" | Out-File $logFile -Append
    
    try {
        $updated = Add-AjaxTabsToView $file.FullName $tableName
        
        if ($updated) {
            $updatedFiles++
            Write-Host "  ✓ Updated with AJAX tabs" -ForegroundColor Green
        } else {
            $skippedFiles++
            Write-Host "  - Skipped (already has tabs or no associations)" -ForegroundColor Gray
        }
    }
    catch {
        $errorFiles++
        Write-Host "  ✗ Error: $_" -ForegroundColor Red
        "  ✗ Error: $_" | Out-File $logFile -Append
    }
    
    Write-Host ""
}

# Summary
Write-Host "=" * 80 -ForegroundColor Cyan
Write-Host "UPDATE COMPLETE" -ForegroundColor Green
Write-Host "=" * 80 -ForegroundColor Cyan
Write-Host "Total files processed: $totalFiles" -ForegroundColor White
Write-Host "Files updated: $updatedFiles" -ForegroundColor Green
Write-Host "Files skipped: $skippedFiles" -ForegroundColor Yellow
Write-Host "Files with errors: $errorFiles" -ForegroundColor Red
Write-Host ""
Write-Host "Log file: $logFile" -ForegroundColor Cyan

# Write summary to log
"" | Out-File $logFile -Append
"=" * 80 | Out-File $logFile -Append
"SUMMARY" | Out-File $logFile -Append
"=" * 80 | Out-File $logFile -Append
"Total files processed: $totalFiles" | Out-File $logFile -Append
"Files updated: $updatedFiles" | Out-File $logFile -Append
"Files skipped: $skippedFiles" | Out-File $logFile -Append
"Files with errors: $errorFiles" | Out-File $logFile -Append
"Completed: $(Get-Date)" | Out-File $logFile -Append

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
