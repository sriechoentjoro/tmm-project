# PowerShell Script to Fix All Association Displays in Index and View Templates
# This script replaces ID displays with association name/title displays

$projectRoot = "d:\xampp\htdocs\project_tmm"
$templatesDir = "$projectRoot\src\Template"

Write-Host "=== Fixing Association Displays in All Templates ===" -ForegroundColor Cyan
Write-Host ""

# Common association patterns to fix
$associationPatterns = @(
    # Cooperative Associations
    @{
        TablePattern = "CooperativeAssociations"
        IdField = "cooperative_association_id"
        DisplayField = "name"
        HeaderText = "Cooperative Association"
    },
    # Acceptance Organizations
    @{
        TablePattern = "AcceptanceOrganizations"
        IdField = "acceptance_organization_id"
        DisplayField = "title"
        HeaderText = "Acceptance Organization"
    },
    # Job Categories
    @{
        TablePattern = "MasterJobCategories"
        IdField = "job_category_id"
        DisplayField = "title"
        HeaderText = "Job Category"
    },
    @{
        TablePattern = "MasterJobCategories"
        IdField = "master_job_category_id"
        DisplayField = "title"
        HeaderText = "Job Category"
    },
    # Vocational Training Institutions
    @{
        TablePattern = "VocationalTrainingInstitutions"
        IdField = "vocational_training_institution_id"
        DisplayField = "name"
        HeaderText = "Vocational Training Institution"
    },
    # Candidates
    @{
        TablePattern = "Candidates"
        IdField = "candidate_id"
        DisplayField = "fullname"
        HeaderText = "Candidate"
    },
    # Trainees
    @{
        TablePattern = "Trainees"
        IdField = "trainee_id"
        DisplayField = "fullname"
        HeaderText = "Trainee"
    },
    # Users
    @{
        TablePattern = "Users"
        IdField = "user_id"
        DisplayField = "fullname"
        HeaderText = "User"
    },
    @{
        TablePattern = "Users"
        IdField = "created_by"
        DisplayField = "fullname"
        HeaderText = "Created By"
    },
    @{
        TablePattern = "Users"
        IdField = "modified_by"
        DisplayField = "fullname"
        HeaderText = "Modified By"
    },
    # Geographic fields
    @{
        TablePattern = "MasterPropinsis"
        IdField = "master_propinsi_id"
        DisplayField = "title"
        HeaderText = "Province"
    },
    @{
        TablePattern = "MasterKabupatens"
        IdField = "master_kabupaten_id"
        DisplayField = "title"
        HeaderText = "City/District"
    },
    @{
        TablePattern = "MasterKecamatans"
        IdField = "master_kecamatan_id"
        DisplayField = "title"
        HeaderText = "Subdistrict"
    },
    @{
        TablePattern = "MasterKelurahans"
        IdField = "master_kelurahan_id"
        DisplayField = "title"
        HeaderText = "Village"
    }
)

function Fix-IndexTemplate {
    param(
        [string]$filePath,
        [string]$entityVar,
        [hashtable]$pattern
    )
    
    $content = Get-Content $filePath -Raw -Encoding UTF8
    $modified = $false
    
    # Fix table header
    $oldHeader = "Paginator->sort('$($pattern.IdField)')"
    $newHeader = "Paginator->sort('$($pattern.TablePattern).$($pattern.DisplayField)', '$($pattern.HeaderText)')"
    if ($content -match [regex]::Escape($oldHeader)) {
        $content = $content -replace [regex]::Escape($oldHeader), $newHeader
        $modified = $true
        Write-Host "  ✓ Fixed header: $($pattern.IdField) -> $($pattern.HeaderText)" -ForegroundColor Green
    }
    
    # Fix data display - Pattern 1: Number->format
    $oldDisplay1 = "Number->format(`$$entityVar->$($pattern.IdField))"
    $newDisplay1 = "$entityVar->has('$($pattern.TablePattern.ToLower().Substring(0,1).ToLower() + $pattern.TablePattern.Substring(1))') ? `n                            h(`$$entityVar->$($pattern.TablePattern.ToLower().Substring(0,1).ToLower() + $pattern.TablePattern.Substring(1))->$($pattern.DisplayField)) : ''"
    
    if ($content -match [regex]::Escape($oldDisplay1)) {
        $content = $content -replace [regex]::Escape($oldDisplay1), $newDisplay1
        $modified = $true
        Write-Host "  ✓ Fixed display: Number->format($($pattern.IdField))" -ForegroundColor Green
    }
    
    # Fix data display - Pattern 2: h() function
    $oldDisplay2 = "h(`$$entityVar->$($pattern.IdField))"
    $newDisplay2 = "$entityVar->has('$($pattern.TablePattern.ToLower().Substring(0,1).ToLower() + $pattern.TablePattern.Substring(1))') ? `n                            h(`$$entityVar->$($pattern.TablePattern.ToLower().Substring(0,1).ToLower() + $pattern.TablePattern.Substring(1))->$($pattern.DisplayField)) : ''"
    
    if ($content -match [regex]::Escape($oldDisplay2)) {
        $content = $content -replace [regex]::Escape($oldDisplay2), $newDisplay2
        $modified = $true
        Write-Host "  ✓ Fixed display: h($($pattern.IdField))" -ForegroundColor Green
    }
    
    if ($modified) {
        $content | Set-Content $filePath -Encoding UTF8 -NoNewline
    }
    
    return $modified
}

function Fix-ViewTemplate {
    param(
        [string]$filePath,
        [string]$entityVar,
        [hashtable]$pattern
    )
    
    $content = Get-Content $filePath -Raw -Encoding UTF8
    $modified = $false
    
    # Fix view header label - use variable to avoid < operator issue
    $thOpen = '<th>'
    $thClose = '</th>'
    $labelText = $pattern.IdField.Replace('_', ' ').Replace('id', 'Id')
    $oldLabel = [regex]::Escape($thOpen + $labelText + $thClose)
    $newLabel = $thOpen + $pattern.HeaderText + $thClose
    if ($content -match $oldLabel) {
        $content = $content -replace $oldLabel, $newLabel
        $modified = $true
        Write-Host "  ✓ Fixed label: $($pattern.IdField) -> $($pattern.HeaderText)" -ForegroundColor Green
    }
    
    # Fix data display - Pattern 1: Number->format
    $oldDisplay1 = "Number->format(`$$entityVar->$($pattern.IdField))"
    $assocVar = $pattern.TablePattern.Substring(0,1).ToLower() + $pattern.TablePattern.Substring(1)
    $newDisplay1 = "$entityVar->has('$assocVar') ? `n            h(`$$entityVar->$assocVar->$($pattern.DisplayField)) : ''"
    
    if ($content -match [regex]::Escape($oldDisplay1)) {
        $content = $content -replace [regex]::Escape($oldDisplay1), $newDisplay1
        $modified = $true
        Write-Host "  ✓ Fixed display: Number->format($($pattern.IdField))" -ForegroundColor Green
    }
    
    # Fix data display - Pattern 2: h() function
    $oldDisplay2 = "h(`$$entityVar->$($pattern.IdField))"
    $newDisplay2 = "$entityVar->has('$assocVar') ? `n            h(`$$entityVar->$assocVar->$($pattern.DisplayField)) : ''"
    
    if ($content -match [regex]::Escape($oldDisplay2)) {
        $content = $content -replace [regex]::Escape($oldDisplay2), $newDisplay2
        $modified = $true
        Write-Host "  ✓ Fixed display: h($($pattern.IdField))" -ForegroundColor Green
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
    if ($entityVar -like "*s") {
        $entityVar = $entityVar.Substring(0, $entityVar.Length - 1)
    }
    
    Write-Host "Processing: $folderName (entity: `$$entityVar)" -ForegroundColor Yellow
    
    # Process index.ctp
    $indexPath = Join-Path $folder.FullName "index.ctp"
    if (Test-Path $indexPath) {
        $totalFiles++
        $fileModified = $false
        Write-Host "  Checking index.ctp..." -ForegroundColor Gray
        
        foreach ($pattern in $associationPatterns) {
            if (Fix-IndexTemplate -filePath $indexPath -entityVar $entityVar -pattern $pattern) {
                $fileModified = $true
            }
        }
        
        if ($fileModified) {
            $modifiedFiles++
            Write-Host "  ✓ index.ctp modified" -ForegroundColor Green
        }
    }
    
    # Process view.ctp
    $viewPath = Join-Path $folder.FullName "view.ctp"
    if (Test-Path $viewPath) {
        $totalFiles++
        $fileModified = $false
        Write-Host "  Checking view.ctp..." -ForegroundColor Gray
        
        foreach ($pattern in $associationPatterns) {
            if (Fix-ViewTemplate -filePath $viewPath -entityVar $entityVar -pattern $pattern) {
                $fileModified = $true
            }
        }
        
        if ($fileModified) {
            $modifiedFiles++
            Write-Host "  ✓ view.ctp modified" -ForegroundColor Green
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
