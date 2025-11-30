# Simple Cross-Database Association Audit
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Cross-Database Association Audit" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# Database mapping
$tableToDatabase = @{
    # cms_masters
    'master_blood_types' = 'cms_masters'
    'master_departments' = 'cms_masters'
    'master_educational_levels' = 'cms_masters'
    'master_genders' = 'cms_masters'
    'master_job_categories' = 'cms_masters'
    'master_kabupatens' = 'cms_masters'
    'master_kecamatans' = 'cms_masters'
    'master_kelurahans' = 'cms_masters'
    'master_marriage_statuses' = 'cms_masters'
    'master_propinsis' = 'cms_masters'
    'master_religions' = 'cms_masters'
    'menus' = 'cms_masters'
    
    # cms_tmm_apprentices
    'apprentices' = 'cms_tmm_apprentices'
    'apprentice_certifications' = 'cms_tmm_apprentices'
    'apprentice_courses' = 'cms_tmm_apprentices'
    'apprentice_educations' = 'cms_tmm_apprentices'
    'apprentice_experiences' = 'cms_tmm_apprentices'
    'apprentice_families' = 'cms_tmm_apprentices'
    'apprentice_family_stories' = 'cms_tmm_apprentices'
    
    # cms_tmm_organizations
    'acceptance_organizations' = 'cms_tmm_organizations'
    'cooperative_associations' = 'cms_tmm_organizations'
    'vocational_training_institutions' = 'cms_tmm_organizations'
    
    # cms_tmm_stakeholders
    'candidates' = 'cms_tmm_stakeholders'
    'candidate_documents' = 'cms_tmm_stakeholders'
    'candidate_educations' = 'cms_tmm_stakeholders'
    'candidate_experiences' = 'cms_tmm_stakeholders'
    'candidate_families' = 'cms_tmm_stakeholders'
    
    # cms_tmm_trainees
    'apprentice_orders' = 'cms_tmm_trainees'
    'departure_orders' = 'cms_tmm_trainees'
    'trainees' = 'cms_tmm_trainees'
    
    # cms_tmm_trainee_accountings
    'trainee_accountings' = 'cms_tmm_trainee_accountings'
    'trainee_accounting_details' = 'cms_tmm_trainee_accountings'
}

$tableFiles = Get-ChildItem -Path "src\Model\Table" -Filter "*Table.php" -File
$issuesFound = @()
$totalChecked = 0

foreach ($file in $tableFiles) {
    $totalChecked++
    $fileName = $file.Name
    $content = Get-Content $file.FullName -Raw
    
    # Get table database
    $tableDb = 'cms_masters' # default
    if ($content -match "defaultConnectionName\(\)[\s\S]*?return '([^']+)'") {
        $tableDb = $Matches[1]
    }
    
    Write-Host "Checking: $fileName (DB: $tableDb)" -ForegroundColor Gray
    
    # Find associations without 'strategy' => 'select'
    $lines = Get-Content $file.FullName
    $inAssociation = $false
    $associationName = ''
    $associationType = ''
    $lineNum = 0
    $associationStart = 0
    $hasStrategy = $false
    
    foreach ($line in $lines) {
        $lineNum++
        
        # Start of association
        if ($line -match '\$this->(belongsTo|hasMany|hasOne|belongsToMany)\(''([^'']+)''') {
            $associationType = $Matches[1]
            $associationName = $Matches[2]
            $inAssociation = $true
            $associationStart = $lineNum
            $hasStrategy = $false
        }
        
        # Check for strategy
        if ($inAssociation -and $line -match "'strategy'\s*=>\s*'select'") {
            $hasStrategy = $true
        }
        
        # End of association
        if ($inAssociation -and $line -match '\]\s*\);') {
            $inAssociation = $false
            
            # Check if cross-database (simple heuristic)
            $needsStrategy = $false
            
            # If association name starts with Master, Candidate, Trainee, Apprentice, etc
            # and current table is not in cms_masters, likely cross-db
            if (($associationName -match '^Master' -or $associationName -match '^Candidate' -or 
                 $associationName -match '^Trainee' -or $associationName -match '^Apprentice' -or
                 $associationName -match 'Organization' -or $associationName -match 'Institution') -and
                $tableDb -ne 'cms_masters') {
                $needsStrategy = $true
            }
            
            if ($needsStrategy -and -not $hasStrategy) {
                $issue = "  Line $associationStart : $associationType('$associationName') - MISSING 'strategy' => 'select'"
                Write-Host $issue -ForegroundColor Yellow
                $issuesFound += "$fileName`n$issue"
            }
        }
    }
}

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "SUMMARY" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Files checked: $totalChecked" -ForegroundColor White
Write-Host "Issues found: $($issuesFound.Count)" -ForegroundColor $(if ($issuesFound.Count -gt 0) { 'Yellow' } else { 'Green' })

if ($issuesFound.Count -gt 0) {
    Write-Host ""
    Write-Host "DETAILS:" -ForegroundColor Yellow
    foreach ($issue in $issuesFound) {
        Write-Host $issue -ForegroundColor Yellow
        Write-Host ""
    }
    
    $reportContent = "# Cross-Database Association Issues`n`n"
    $reportContent += "Total files checked: $totalChecked`n"
    $reportContent += "Issues found: $($issuesFound.Count)`n`n"
    $reportContent += "## Issues:`n`n"
    $reportContent += $($issuesFound -join "`n`n")
    
    Set-Content -Path "CROSS_DB_AUDIT_SIMPLE.md" -Value $reportContent -Encoding UTF8
    Write-Host "Report saved to: CROSS_DB_AUDIT_SIMPLE.md" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "Audit complete!" -ForegroundColor Green
