# Fix $entity variable to use correct variable name in AJAX tabs
$rootPath = "D:\xampp\htdocs\project_tmm"
$templatesPath = "$rootPath\src\Template"
$logFile = "$rootPath\fix_entity_log.txt"

"Fix Entity Variable Script - Started: $(Get-Date)" | Out-File $logFile

# Mapping of table names to their entity variable names
$entityVarMap = @{
    'AcceptanceOrganizations' = 'acceptanceOrganization'
    'ApprenticeOrders' = 'apprenticeOrder'
    'Apprentices' = 'apprentice'
    'CooperativeAssociations' = 'cooperativeAssociation'
    'MasterAuthorizationRoles' = 'masterAuthorizationRole'
    'MasterCandidateInterviewResults' = 'masterCandidateInterviewResult'
    'MasterCandidateInterviewTypes' = 'masterCandidateInterviewType'
    'MasterJapanPrefectures' = 'masterJapanPrefecture'
    'Trainees' = 'trainee'
    'Users' = 'user'
    'VocationalTrainingInstitutions' = 'vocationalTrainingInstitution'
}

$updated = 0
$skipped = 0

foreach ($tableName in $entityVarMap.Keys) {
    $viewFile = "$templatesPath\$tableName\view.ctp"
    
    if (-not (Test-Path $viewFile)) {
        Write-Host "SKIP: File not found - $tableName" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    $content = Get-Content $viewFile -Raw -ErrorAction SilentlyContinue
    if (-not $content) {
        Write-Host "SKIP: Could not read - $tableName" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    # Check if it has the error pattern
    if ($content -notmatch '\$entity->id') {
        Write-Host "SKIP: No \$entity->id found - $tableName" -ForegroundColor Gray
        $skipped++
        continue
    }
    
    Write-Host "Processing: $tableName" -ForegroundColor Yellow
    
    $varName = $entityVarMap[$tableName]
    
    # Replace $entity->id with correct variable
    $newContent = $content -replace '\$entity->id', "`$$varName->id"
    
    # Write back
    try {
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($viewFile, $newContent, $utf8NoBom)
        
        Write-Host "  SUCCESS: Fixed \$entity->id to \$$varName->id" -ForegroundColor Green
        "SUCCESS: $tableName - Fixed to use \$$varName->id" | Out-File $logFile -Append
        $updated++
    }
    catch {
        Write-Host "  ERROR: $_" -ForegroundColor Red
        "ERROR: $tableName - $_" | Out-File $logFile -Append
    }
}

Write-Host ""
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host "FIX COMPLETE" -ForegroundColor Green
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host "Files updated: $updated" -ForegroundColor Green
Write-Host "Files skipped: $skipped" -ForegroundColor Yellow
Write-Host ""
Write-Host "Log file: $logFile" -ForegroundColor Cyan

"" | Out-File $logFile -Append
"Files updated: $updated" | Out-File $logFile -Append
"Files skipped: $skipped" | Out-File $logFile -Append
"Completed: $(Get-Date)" | Out-File $logFile -Append
