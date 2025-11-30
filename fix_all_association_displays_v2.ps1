# PowerShell Script to Fix All Association Displays in Index and View Templates
# Version 2: Clean version without errors

$projectRoot = "d:\xampp\htdocs\project_tmm"
$templatesDir = "$projectRoot\src\Template"

Write-Host "=== Fixing Association Displays in All Templates ===" -ForegroundColor Cyan
Write-Host ""

# Common association patterns to fix
$associationPatterns = @(
    @{Table = "CooperativeAssociations"; IdField = "cooperative_association_id"; DisplayField = "name"; HeaderText = "Cooperative Association"},
    @{Table = "AcceptanceOrganizations"; IdField = "acceptance_organization_id"; DisplayField = "title"; HeaderText = "Acceptance Organization"},
    @{Table = "MasterJobCategories"; IdField = "job_category_id"; DisplayField = "title"; HeaderText = "Job Category"},
    @{Table = "MasterJobCategories"; IdField = "master_job_category_id"; DisplayField = "title"; HeaderText = "Job Category"},
    @{Table = "VocationalTrainingInstitutions"; IdField = "vocational_training_institution_id"; DisplayField = "name"; HeaderText = "Vocational Training Institution"},
    @{Table = "Candidates"; IdField = "candidate_id"; DisplayField = "fullname"; HeaderText = "Candidate"},
    @{Table = "Trainees"; IdField = "trainee_id"; DisplayField = "fullname"; HeaderText = "Trainee"},
    @{Table = "Users"; IdField = "user_id"; DisplayField = "fullname"; HeaderText = "User"},
    @{Table = "Users"; IdField = "created_by"; DisplayField = "fullname"; HeaderText = "Created By"},
    @{Table = "Users"; IdField = "modified_by"; DisplayField = "fullname"; HeaderText = "Modified By"},
    @{Table = "MasterPropinsis"; IdField = "master_propinsi_id"; DisplayField = "title"; HeaderText = "Province"},
    @{Table = "MasterKabupatens"; IdField = "master_kabupaten_id"; DisplayField = "title"; HeaderText = "City/District"},
    @{Table = "MasterKecamatans"; IdField = "master_kecamatan_id"; DisplayField = "title"; HeaderText = "Subdistrict"},
    @{Table = "MasterKelurahans"; IdField = "master_kelurahan_id"; DisplayField = "title"; HeaderText = "Village"}
)

function Get-AssocVariableName {
    param([string]$tableName)
    return $tableName.Substring(0,1).ToLower() + $tableName.Substring(1)
}

function Fix-TemplateFile {
    param(
        [string]$filePath,
        [string]$entityVar,
        [array]$patterns,
        [bool]$isIndexTemplate
    )
    
    $content = Get-Content $filePath -Raw -Encoding UTF8
    $originalContent = $content
    $modified = $false
    
    foreach ($pattern in $patterns) {
        $dollarSign = '$'
        $assocVar = Get-AssocVariableName -tableName $pattern.Table
        
        # Fix header (index only)
        if ($isIndexTemplate) {
            $oldHeader = "Paginator->sort('$($pattern.IdField)')"
            $newHeader = "Paginator->sort('$($pattern.Table).$($pattern.DisplayField)', '$($pattern.HeaderText)')"
            if ($content -like "*$oldHeader*") {
                $content = $content -replace [regex]::Escape($oldHeader), $newHeader
                $modified = $true
                Write-Host "    ✓ Fixed header: $($pattern.IdField) -> $($pattern.HeaderText)" -ForegroundColor Green
            }
        }
        
        # Fix label (view only)
        if (-not $isIndexTemplate) {
            $labelText = $pattern.IdField -replace '_', ' ' -replace 'id', 'Id'
            $thOpen = '<th>'
            $thClose = '</th>'
            $oldLabelPattern = [regex]::Escape($thOpen + $labelText + $thClose)
            $newLabel = $thOpen + $pattern.HeaderText + $thClose
            if ($content -match $oldLabelPattern) {
                $content = $content -replace $oldLabelPattern, [regex]::Escape($newLabel)
                $modified = $true
                Write-Host "    ✓ Fixed label: $labelText -> $($pattern.HeaderText)" -ForegroundColor Green
            }
        }
        
        # Fix Number->format display
        $oldDisplayPattern1 = [regex]::Escape("Number->format($dollarSign$entityVar->$($pattern.IdField))")
        $newDisplay1 = "$entityVar->has('$assocVar') ? `n                            h($dollarSign$entityVar->$assocVar->$($pattern.DisplayField)) : ''"
        if ($content -match $oldDisplayPattern1) {
            $content = $content -replace $oldDisplayPattern1, $newDisplay1
            $modified = $true
            Write-Host "    ✓ Fixed display: Number->format($($pattern.IdField))" -ForegroundColor Green
        }
        
        # Fix h() display
        $oldDisplayPattern2 = [regex]::Escape("h($dollarSign$entityVar->$($pattern.IdField))")
        $newDisplay2 = "$entityVar->has('$assocVar') ? `n            h($dollarSign$entityVar->$assocVar->$($pattern.DisplayField)) : ''"
        if ($content -match $oldDisplayPattern2) {
            $content = $content -replace $oldDisplayPattern2, $newDisplay2
            $modified = $true
            Write-Host "    ✓ Fixed display: h($($pattern.IdField))" -ForegroundColor Green
        }
    }
    
    if ($modified) {
        $content | Set-Content $filePath -Encoding UTF8 -NoNewline
    }
    
    return $modified
}

# Get all controller folders
$controllerFolders = Get-ChildItem -Path $templatesDir -Directory | Where-Object { 
    $_.Name -notlike "Element" -and 
    $_.Name -notlike "Email" -and 
    $_.Name -notlike "Error" -and
    $_.Name -notlike "Layout" -and 
    $_.Name -notlike "Plugin" -and
    $_.Name -notlike "Bake"
}

$totalFiles = 0
$modifiedFiles = 0

foreach ($folder in $controllerFolders) {
    $folderName = $folder.Name
    $entityVar = $folderName.Substring(0,1).ToLower() + $folderName.Substring(1)
    if ($entityVar.EndsWith("s")) {
        $entityVar = $entityVar.Substring(0, $entityVar.Length - 1)
    }
    
    Write-Host "Processing: $folderName (entity: `$$entityVar)" -ForegroundColor Yellow
    
    # Process index.ctp
    $indexPath = Join-Path $folder.FullName "index.ctp"
    if (Test-Path $indexPath) {
        $totalFiles++
        Write-Host "  Checking index.ctp..." -ForegroundColor Gray
        
        if (Fix-TemplateFile -filePath $indexPath -entityVar $entityVar -patterns $associationPatterns -isIndexTemplate $true) {
            $modifiedFiles++
            Write-Host "  ✓ index.ctp modified" -ForegroundColor Green
        } else {
            Write-Host "  - No changes needed" -ForegroundColor DarkGray
        }
    }
    
    # Process view.ctp
    $viewPath = Join-Path $folder.FullName "view.ctp"
    if (Test-Path $viewPath) {
        $totalFiles++
        Write-Host "  Checking view.ctp..." -ForegroundColor Gray
        
        if (Fix-TemplateFile -filePath $viewPath -entityVar $entityVar -patterns $associationPatterns -isIndexTemplate $false) {
            $modifiedFiles++
            Write-Host "  ✓ view.ctp modified" -ForegroundColor Green
        } else {
            Write-Host "  - No changes needed" -ForegroundColor DarkGray
        }
    }
    
    Write-Host ""
}

Write-Host "=== Summary ===" -ForegroundColor Cyan
Write-Host "Total files checked: $totalFiles" -ForegroundColor White
Write-Host "Files modified: $modifiedFiles" -ForegroundColor Green
Write-Host ""
Write-Host "=== Clearing Cache ===" -ForegroundColor Cyan
Set-Location $projectRoot
& "bin\cake" cache clear_all
Write-Host ""
Write-Host "Done! All association displays have been fixed." -ForegroundColor Green
