# PowerShell Script: Add AJAX Tabs to All View Templates
# This script integrates AJAX lazy-loading tabs into existing view templates

$ErrorActionPreference = "Stop"

# Define controllers with AJAX endpoints and their associations
$controllers = @{
    'AcceptanceOrganizations' = @{
        entityVar = 'acceptanceOrganization'
        associations = @(
            @{
                name = 'AcceptanceOrganizationStories'
                method = 'getRelatedStories'
                tabId = 'acceptanceorganizationstories'
                title = 'Stories'
                filterField = 'acceptance_organization_id'
                controller = 'acceptance-organization-stories'
                columns = @(
                    @{name='id'; label='ID'; type='number'; sortable=$true}
                    @{name='date_occurrence'; label='Date'; type='date'; sortable=$true}
                    @{name='problem_classification'; label='Classification'; type='text'; sortable=$true}
                    @{name='problem_contents'; label='Problem'; type='text'; sortable=$true}
                    @{name='problem_solution'; label='Solution'; type='text'; sortable=$true}
                    @{name='image_path'; label='Image'; type='image'; sortable=$false}
                )
            }
        )
    }
    'ApprenticeOrders' = @{
        entityVar = 'apprenticeOrder'
        associations = @()  # To be filled by checking Table file
    }
    'Apprentices' = @{
        entityVar = 'apprentice'
        associations = @()
    }
    'Trainees' = @{
        entityVar = 'trainee'
        associations = @()
    }
    'CooperativeAssociations' = @{
        entityVar = 'cooperativeAssociation'
        associations = @()
    }
    'MasterAuthorizationRoles' = @{
        entityVar = 'masterAuthorizationRole'
        associations = @()
    }
    'MasterCandidateInterviewResults' = @{
        entityVar = 'masterCandidateInterviewResult'
        associations = @()
    }
    'MasterCandidateInterviewTypes' = @{
        entityVar = 'masterCandidateInterviewType'
        associations = @()
    }
    'MasterJapanPrefectures' = @{
        entityVar = 'masterJapanPrefecture'
        associations = @()
    }
    'Users' = @{
        entityVar = 'user'
        associations = @()
    }
    'VocationalTrainingInstitutions' = @{
        entityVar = 'vocationalTrainingInstitution'
        associations = @()
    }
}

Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "  AJAX Tabs Integration Script - All View Templates" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

# Function to read hasMany associations from Table file
function Get-HasManyAssociations($controllerName) {
    $tableFile = "src\Model\Table\${controllerName}Table.php"
    if (-not (Test-Path $tableFile)) {
        Write-Host "  ⚠️  Table file not found: $tableFile" -ForegroundColor Yellow
        return @()
    }
    
    $content = Get-Content $tableFile -Raw
    $associations = @()
    
    # Regex to find hasMany associations
    if ($content -match '(?s)\$this->hasMany\(''([^'']+)''') {
        $matches = [regex]::Matches($content, '\$this->hasMany\(''([^'']+)''[^;]+\);')
        foreach ($match in $matches) {
            $assocName = $match.Groups[1].Value
            $associations += $assocName
        }
    }
    
    return $associations
}

# Function to check if view template has existing tab structure
function Test-HasTabStructure($viewFile) {
    if (-not (Test-Path $viewFile)) {
        return $false
    }
    $content = Get-Content $viewFile -Raw
    return $content -match 'view-tabs-nav' -or $content -match 'view-tabs-container'
}

# Function to add AJAX tab to existing structure
function Add-AjaxTabToView($controllerName, $viewFile, $entityVar, $associations) {
    Write-Host "  📝 Processing: $viewFile" -ForegroundColor White
    
    if (-not (Test-Path $viewFile)) {
        Write-Host "    ❌ View file not found" -ForegroundColor Red
        return
    }
    
    $content = Get-Content $viewFile -Raw
    
    # Check if already has tab structure
    if (-not ($content -match 'view-tabs-nav')) {
        Write-Host "    ⚠️  No existing tab structure found - skipping" -ForegroundColor Yellow
        return
    }
    
    # Check if AJAX tabs already exist
    if ($content -match 'related_records_table') {
        Write-Host "    ℹ️  AJAX tabs already exist - skipping" -ForegroundColor Cyan
        return
    }
    
    if ($associations.Count -eq 0) {
        Write-Host "    ⚠️  No associations configured - skipping" -ForegroundColor Yellow
        return
    }
    
    # Build tab links HTML
    $tabLinks = ""
    foreach ($assoc in $associations) {
        $tabLinks += @"

        <li class="view-tab-item">
            <a href="#tab-$($assoc.tabId)" class="view-tab-link" data-tab="tab-$($assoc.tabId)">
                <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M1.75 0A1.75 1.75 0 000 1.75v12.5C0 15.216.784 16 1.75 16h12.5A1.75 1.75 0 0016 14.25V1.75A1.75 1.75 0 0014.25 0H1.75z"></path>
                </svg>
                <?= __('$($assoc.title)') ?>
            </a>
        </li>
"@
    }
    
    # Build tab panes HTML
    $tabPanes = ""
    foreach ($assoc in $associations) {
        # Convert columns array to JSON format
        $columnsJson = ($assoc.columns | ForEach-Object {
            $sortableStr = if ($_.sortable) { "true" } else { "false" }
            "['name' => '$($_.name)', 'label' => '$($_.label)', 'type' => '$($_.type)', 'sortable' => $sortableStr]"
        }) -join ",`n                                "
        
        $tabPanes += @"

            <!-- $($assoc.title) Tab with AJAX -->
            <div id="tab-$($assoc.tabId)" class="view-tab-pane">
                <div id="$($assoc.tabId)-pane">
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.75 0A1.75 1.75 0 000 1.75v12.5C0 15.216.784 16 1.75 16h12.5A1.75 1.75 0 0016 14.25V1.75A1.75 1.75 0 0014.25 0H1.75z"></path>
                            </svg>
                            <?= __('$($assoc.name)') ?>
                        </h3>
                    </div>
                    <div class="github-details-body">
                        <?= `$this->element('related_records_table', [
                            'tabId' => '$($assoc.tabId)',
                            'title' => __('$($assoc.title)'),
                            'filterField' => '$($assoc.filterField)',
                            'filterValue' => `$$entityVar->id,
                            'ajaxUrl' => `$this->Url->build(['controller' => '$(($controllerName -replace '([A-Z])', '-$1').TrimStart('-').ToLower())', 'action' => '$($assoc.method)']),
                            'controller' => '$($assoc.controller)',
                            'columns' => [
                                $columnsJson
                            ]
                        ]) ?>
                    </div>
                </div>
                </div>
            </div>
            <!-- End $($assoc.title) Tab -->
"@
    }
    
    # Insert tab links before </ul> of view-tabs-nav
    if ($content -match '(\s+)</ul>\s*<!-- End Tabs Nav -->') {
        $content = $content -replace '(\s+)</ul>\s*<!-- End Tabs Nav -->', "$tabLinks`r`n`$1</ul>`r`n        <!-- End Tabs Nav -->"
        Write-Host "    ✅ Added $($associations.Count) tab link(s)" -ForegroundColor Green
    }
    
    # Insert tab panes before closing </div> of view-tabs-content
    if ($content -match '(\s+)</div>\s*<!-- End Tab Contents -->') {
        $content = $content -replace '(\s+)</div>\s*<!-- End Tab Contents -->', "$tabPanes`r`n`$1</div>`r`n        <!-- End Tab Contents -->"
        Write-Host "    ✅ Added $($associations.Count) tab pane(s)" -ForegroundColor Green
    }
    
    # Save file without BOM
    [System.IO.File]::WriteAllText($viewFile, $content, (New-Object System.Text.UTF8Encoding $false))
    Write-Host "    ✅ File saved successfully" -ForegroundColor Green
}

# Main execution
$totalProcessed = 0
$totalUpdated = 0

foreach ($controllerName in $controllers.Keys) {
    Write-Host ""
    Write-Host "Processing: $controllerName" -ForegroundColor Yellow
    Write-Host "----------------------------------------" -ForegroundColor Gray
    
    $config = $controllers[$controllerName]
    $viewFile = "src\Template\$controllerName\view.ctp"
    
    # If no associations configured, try to detect from Table file
    if ($config.associations.Count -eq 0) {
        Write-Host "  ℹ️  No associations configured - would need manual setup" -ForegroundColor Cyan
        $totalProcessed++
        continue
    }
    
    # Add tabs to view
    Add-AjaxTabToView $controllerName $viewFile $config.entityVar $config.associations
    
    $totalProcessed++
    if (Test-Path $viewFile) {
        $content = Get-Content $viewFile -Raw
        if ($content -match 'related_records_table') {
            $totalUpdated++
        }
    }
}

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "  Summary" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "  Total controllers processed: $totalProcessed" -ForegroundColor White
Write-Host "  Total views updated: $totalUpdated" -ForegroundColor Green
Write-Host ""
Write-Host "✅ Script completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Clear cache: bin\cake cache clear_all" -ForegroundColor White
Write-Host "  2. Test each view page in browser" -ForegroundColor White
Write-Host "  3. Check console for any errors" -ForegroundColor White
Write-Host ""
