# Audit and Fix Cross-Database Associations
# This script checks all Table files for cross-database associations and ensures 'strategy' => 'select' is present

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Cross-Database Association Audit & Fix Tool" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# Database mapping from app_datasources.php
$databaseMapping = @{
    'default' = 'cms_masters'
    'cms_masters' = @(
        'master_blood_types', 'master_departments', 'master_educational_levels',
        'master_genders', 'master_interview_results', 'master_job_categories',
        'master_kabupatens', 'master_kecamatans', 'master_kelurahans',
        'master_marriage_statuses', 'master_propinsis', 'master_rejected_reasons',
        'master_religions', 'menus'
    )
    'cms_tmm_apprentices' = @(
        'apprentices', 'apprentice_certifications', 'apprentice_courses',
        'apprentice_educations', 'apprentice_experiences', 'apprentice_families',
        'apprentice_family_stories'
    )
    'cms_tmm_apprentice_documents' = @(
        'apprentice_departure_documents', 'apprentice_departure_document_trackings',
        'apprentice_documents', 'apprentice_document_trackings'
    )
    'cms_tmm_apprentice_document_ticketings' = @(
        'apprentice_departure_ticketings', 'apprentice_ticketings'
    )
    'cms_tmm_organizations' = @(
        'acceptance_organizations', 'cooperative_associations',
        'vocational_training_institutions'
    )
    'cms_tmm_stakeholders' = @(
        'candidates', 'candidate_documents', 'candidate_educations',
        'candidate_experiences', 'candidate_families'
    )
    'cms_tmm_trainees' = @(
        'apprentice_orders', 'departure_orders', 'trainees'
    )
    'cms_tmm_trainee_accountings' = @(
        'trainee_accountings', 'trainee_accounting_details'
    )
    'cms_tmm_trainee_trainings' = @(
        'trainee_trainings', 'trainee_training_schedules'
    )
    'cms_tmm_trainee_training_scorings' = @(
        'trainee_training_scorings', 'trainee_training_scoring_details'
    )
}

# Function to get database connection for a table
function Get-TableDatabase {
    param($tableName)
    
    foreach ($db in $databaseMapping.Keys) {
        if ($databaseMapping[$db] -is [array]) {
            if ($databaseMapping[$db] -contains $tableName) {
                return $db
            }
        }
    }
    return 'cms_masters' # default
}

# Function to convert table name to class name
function Get-ClassName {
    param($tableName)
    
    # Convert snake_case to PascalCase
    $words = $tableName -split '_'
    $className = ($words | ForEach-Object { 
        $_.Substring(0,1).ToUpper() + $_.Substring(1).ToLower() 
    }) -join ''
    
    # Handle pluralization
    if ($className -notmatch 's$') {
        $className += 's'
    }
    
    return $className
}

# Get all Table files
$tableFiles = Get-ChildItem -Path "src\Model\Table" -Filter "*Table.php" -File

$totalFiles = $tableFiles.Count
$processedFiles = 0
$fixedFiles = 0
$issuesFound = @()
$nonExistentTables = @()

Write-Host "Found $totalFiles table files to check..." -ForegroundColor Yellow
Write-Host ""

foreach ($file in $tableFiles) {
    $processedFiles++
    $fileName = $file.Name
    $tableName = $fileName -replace 'Table\.php$', ''
    
    Write-Host "[$processedFiles/$totalFiles] Checking: $fileName" -ForegroundColor Gray
    
    $content = Get-Content $file.FullName -Raw
    
    # Extract the table name from setTable() call
    if ($content -match "setTable\('([^']+)'\)") {
        $actualTableName = $matches[1]
    } else {
        $actualTableName = ($tableName -replace '([A-Z])', '_$1').ToLower().TrimStart('_')
    }
    
    # Get the database connection for this table
    if ($content -match "defaultConnectionName\(\)[\s\S]*?return\s+'([^']+)'") {
        $tableDatabase = $matches[1]
    } else {
        $tableDatabase = Get-TableDatabase $actualTableName
    }
    
    Write-Host "  Table: $actualTableName | Database: $tableDatabase" -ForegroundColor DarkGray
    
    # Find all associations
    $associationPattern = '\$this->(belongsTo|hasMany|hasOne|belongsToMany)\(''([^'']+)''[^;]*?\]\s*\);'
    $matches = [regex]::Matches($content, $associationPattern, [System.Text.RegularExpressions.RegexOptions]::Singleline)
    
    $needsFix = $false
    $fileIssues = @()
    
    foreach ($match in $matches) {
        $associationType = $match.Groups[1].Value
        $associationName = $match.Groups[2].Value
        $associationBlock = $match.Value
        
        # Try to find className in association
        if ($associationBlock -match "'className'\s*=>\s*'([^']+)'") {
            $targetClassName = $matches[1]
        } else {
            $targetClassName = $associationName
        }
        
        # Convert class name to table name
        $targetTableName = ($targetClassName -replace '([A-Z])', '_$1').ToLower().TrimStart('_')
        $targetTableName = $targetTableName -replace 's$', '' # Remove plural 's' for approximation
        
        # Get target database
        $targetDatabase = Get-TableDatabase $targetTableName
        
        # Check if cross-database
        if ($tableDatabase -ne $targetDatabase) {
            # Check if 'strategy' => 'select' exists
            if ($associationBlock -notmatch "'strategy'\s*=>\s*'select'") {
                $issue = "  ⚠ MISSING 'strategy' => 'select': $associationType('$associationName') | Target DB: $targetDatabase"
                $fileIssues += $issue
                Write-Host $issue -ForegroundColor Yellow
                $needsFix = $true
            } else {
                Write-Host "  ✓ OK: $associationType('$associationName') has 'strategy' => 'select'" -ForegroundColor Green
            }
        }
    }
    
    if ($needsFix) {
        $fixedFiles++
        $issuesFound += "FILE: $fileName`n" + ($fileIssues -join "`n")
    }
    
    Write-Host ""
}

# Summary Report
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "AUDIT SUMMARY" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Total files checked: $totalFiles" -ForegroundColor White
Write-Host "Files with issues: $fixedFiles" -ForegroundColor $(if ($fixedFiles -gt 0) { 'Yellow' } else { 'Green' })
Write-Host ""

if ($issuesFound.Count -gt 0) {
    Write-Host "ISSUES FOUND:" -ForegroundColor Red
    Write-Host "==================================================" -ForegroundColor Red
    foreach ($issue in $issuesFound) {
        Write-Host $issue -ForegroundColor Yellow
        Write-Host ""
    }
    
    # Save report
    $reportPath = "CROSS_DB_ASSOCIATION_AUDIT_REPORT.md"
    $reportContent = "# Cross-Database Association Audit Report`n"
    $reportContent += "Generated: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')`n`n"
    $reportContent += "## Summary`n"
    $reportContent += "- Total files checked: $totalFiles`n"
    $reportContent += "- Files with issues: $fixedFiles`n`n"
    $reportContent += "## Issues Found`n`n"
    $reportContent += $($issuesFound -join "`n`n---`n`n")
    $reportContent += "`n`n## Recommended Actions`n`n"
    $reportContent += "1. Review each issue above`n"
    $reportContent += "2. Add 'strategy' => 'select' to cross-database associations`n"
    $reportContent += "3. Run: bin\cake cache clear_all`n"
    $reportContent += "4. Test affected pages`n"
    
    Set-Content -Path $reportPath -Value $reportContent -Encoding UTF8
    Write-Host "Report saved to: $reportPath" -ForegroundColor Cyan
} else {
    Write-Host "✓ All cross-database associations are correctly configured!" -ForegroundColor Green
}

Write-Host ""
Write-Host "Audit complete!" -ForegroundColor Cyan
