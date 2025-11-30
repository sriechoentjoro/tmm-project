# Convert All HasMany Tabs to AJAX Lazy-Loading
# This script converts eager-loaded hasMany association tabs to AJAX tabs

$projectRoot = "d:\xampp\htdocs\project_tmm"

# List of controllers with complex views (many hasMany associations)
$complexViews = @(
    @{
        Controller = "Candidates"
        Associations = @(
            @{Name = "CandidateCertifications"; FilterField = "candidate_id"; Columns = @(
                @{name = "id"; label = "ID"; type = "number"; sortable = $true}
                @{name = "candidate_id"; label = "Candidate"; type = "association"; association = "Candidate"; displayField = "name"; sortable = $true}
                @{name = "certificate_type"; label = "Certificate Type"; type = "text"; sortable = $true}
                @{name = "certificate_name"; label = "Certificate Name"; type = "text"; sortable = $true}
                @{name = "issued_date"; label = "Issued Date"; type = "date"; sortable = $true}
            )}
            @{Name = "CandidateCourses"; FilterField = "candidate_id"; Columns = @(
                @{name = "id"; label = "ID"; type = "number"; sortable = $true}
                @{name = "candidate_id"; label = "Candidate"; type = "association"; association = "Candidate"; displayField = "name"; sortable = $true}
                @{name = "course_name"; label = "Course Name"; type = "text"; sortable = $true}
                @{name = "institution"; label = "Institution"; type = "text"; sortable = $true}
                @{name = "start_date"; label = "Start Date"; type = "date"; sortable = $true}
                @{name = "end_date"; label = "End Date"; type = "date"; sortable = $true}
            )}
            @{Name = "CandidateDocuments"; FilterField = "candidate_id"; Columns = @(
                @{name = "id"; label = "ID"; type = "number"; sortable = $true}
                @{name = "candidate_id"; label = "Candidate"; type = "association"; association = "Candidate"; displayField = "name"; sortable = $true}
                @{name = "document_name"; label = "Document Name"; type = "text"; sortable = $true}
                @{name = "file_path"; label = "File"; type = "file"; sortable = $false}
                @{name = "uploaded_date"; label = "Uploaded"; type = "datetime"; sortable = $true}
            )}
            @{Name = "CandidateEducations"; FilterField = "candidate_id"; Columns = @(
                @{name = "id"; label = "ID"; type = "number"; sortable = $true}
                @{name = "candidate_id"; label = "Candidate"; type = "association"; association = "Candidate"; displayField = "name"; sortable = $true}
                @{name = "education_level"; label = "Level"; type = "text"; sortable = $true}
                @{name = "institution_name"; label = "Institution"; type = "text"; sortable = $true}
                @{name = "graduation_year"; label = "Year"; type = "number"; sortable = $true}
            )}
            @{Name = "CandidateExperiences"; FilterField = "candidate_id"; Columns = @(
                @{name = "id"; label = "ID"; type = "number"; sortable = $true}
                @{name = "candidate_id"; label = "Candidate"; type = "association"; association = "Candidate"; displayField = "name"; sortable = $true}
                @{name = "company_name"; label = "Company"; type = "text"; sortable = $true}
                @{name = "position"; label = "Position"; type = "text"; sortable = $true}
                @{name = "start_date"; label = "From"; type = "date"; sortable = $true}
                @{name = "end_date"; label = "To"; type = "date"; sortable = $true}
            )}
            @{Name = "CandidateFamilies"; FilterField = "candidate_id"; Columns = @(
                @{name = "id"; label = "ID"; type = "number"; sortable = $true}
                @{name = "candidate_id"; label = "Candidate"; type = "association"; association = "Candidate"; displayField = "name"; sortable = $true}
                @{name = "family_member_name"; label = "Name"; type = "text"; sortable = $true}
                @{name = "relationship"; label = "Relationship"; type = "text"; sortable = $true}
                @{name = "age"; label = "Age"; type = "number"; sortable = $true}
            )}
        )
    }
    @{
        Controller = "Apprentices"
        Associations = @(
            @{Name = "ApprenticeCertifications"; FilterField = "apprentice_id"}
            @{Name = "ApprenticeCourses"; FilterField = "apprentice_id"}
            @{Name = "ApprenticeEducations"; FilterField = "apprentice_id"}
            @{Name = "ApprenticeExperiences"; FilterField = "apprentice_id"}
            @{Name = "ApprenticeFamilies"; FilterField = "apprentice_id"}
            @{Name = "ApprenticeFamilyStories"; FilterField = "apprentice_id"}
        )
    }
    @{
        Controller = "Trainees"
        Associations = @(
            @{Name = "TraineeCertifications"; FilterField = "trainee_id"}
            @{Name = "TraineeCourses"; FilterField = "trainee_id"}
            @{Name = "TraineeEducations"; FilterField = "trainee_id"}
            @{Name = "TraineeExperiences"; FilterField = "trainee_id"}
            @{Name = "TraineeFamilies"; FilterField = "trainee_id"}
            @{Name = "TraineeFamilyStories"; FilterField = "trainee_id"}
        )
    }
)

Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "AJAX Tabs Conversion Script" -ForegroundColor Cyan
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""

foreach ($viewConfig in $complexViews) {
    $controller = $viewConfig.Controller
    $viewPath = "$projectRoot\src\Template\$controller\view.ctp"
    
    Write-Host "Processing: $controller..." -ForegroundColor Yellow
    
    if (-not (Test-Path $viewPath)) {
        Write-Host "  ✗ View file not found: $viewPath" -ForegroundColor Red
        continue
    }
    
    $content = Get-Content $viewPath -Raw
    
    # Backup original
    $backupPath = "$projectRoot\template_backups\ajax_conversion_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    if (-not (Test-Path $backupPath)) {
        New-Item -ItemType Directory -Path $backupPath -Force | Out-Null
    }
    Copy-Item $viewPath "$backupPath\$controller-view.ctp.bak"
    Write-Host "  ✓ Backed up to: $backupPath" -ForegroundColor Green
    
    # For each hasMany association, convert tab content to AJAX
    foreach ($assoc in $viewConfig.Associations) {
        $assocName = $assoc.Name
        $tabId = ($assocName -replace '([A-Z])', '_$1').ToLower().Trim('_')
        $filterField = $assoc.FilterField
        
        Write-Host "    Converting: $assocName to AJAX..." -ForegroundColor Gray
        
        # Generate columns JSON
        if ($assoc.Columns) {
            $columnsJson = $assoc.Columns | ForEach-Object {
                $col = $_
                $props = @()
                foreach ($key in $col.Keys) {
                    $value = $col[$key]
                    if ($value -is [bool]) {
                        $valueStr = if ($value) { "true" } else { "false" }
                    } elseif ($value -is [string]) {
                        $valueStr = "'$($value -replace "'", "\'")'"
                    } else {
                        $valueStr = $value
                    }
                    $props += "'$key' => $valueStr"
                }
                "['$($props -join "', '")']"
            }
            $columnsArray = "[$($columnsJson -join ', ')]"
        } else {
            $columnsArray = "[]"
        }
        
        # Pattern to find the tab pane content (simplified - just replace whole pane)
        $tabPanePattern = "(?s)<div id=`"tab-$tabId`" class=`"view-tab-pane`">.*?</div>\s*<!-- End $assocName Tab -->"
        
        $ajaxTabContent = @"
<div id="tab-$tabId" class="view-tab-pane">
                <?= `$this->element('related_records_table', [
                    'tabId' => '$tabId',
                    'title' => __('$assocName'),
                    'filterField' => '$filterField',
                    'filterValue' => `$$($controller.ToLower())->id,
                    'ajaxUrl' => `$this->Url->build(['controller' => '$controller', 'action' => 'getRelated', '?' => ['association' => '$assocName']]),
                    'controller' => '$assocName',
                    'columns' => $columnsArray,
                    'addUrl' => ['controller' => '$assocName', 'action' => 'add', '?' => ['$filterField' => `$$($controller.ToLower())->id]]
                ]) ?>
            </div>
            <!-- End $assocName Tab -->
"@
        
        if ($content -match $tabPanePattern) {
            $content = $content -replace $tabPanePattern, $ajaxTabContent
            Write-Host "      ✓ Converted $assocName tab" -ForegroundColor Green
        } else {
            Write-Host "      ✗ Could not find tab pane for $assocName" -ForegroundColor Red
        }
    }
    
    # Save updated content
    [System.IO.File]::WriteAllText($viewPath, $content, (New-Object System.Text.UTF8Encoding $false))
    Write-Host "  ✓ Saved: $viewPath" -ForegroundColor Green
    Write-Host ""
}

Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "Conversion Complete!" -ForegroundColor Cyan
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Clear cache: bin\cake cache clear_all" -ForegroundColor Gray
Write-Host "2. Test each view in browser" -ForegroundColor Gray
Write-Host "3. Check console for AJAX errors" -ForegroundColor Gray
