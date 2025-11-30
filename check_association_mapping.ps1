# Comprehensive Association Mapping Check Script
# Checks for common association naming errors in Table files
# Date: November 16, 2025

$projectRoot = "d:\xampp\htdocs\project_tmm"
$logFile = "$projectRoot\association_mapping_check.log"

# Initialize log
"=== Association Mapping Check ===" | Out-File $logFile
"Start Time: $(Get-Date)" | Out-File $logFile -Append
"" | Out-File $logFile -Append

Write-Host "`n=== Checking Association Mapping Issues ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append

$issuesFound = 0

# Common naming issues to check
$namingIssues = @(
    @{ Wrong = "BloodTypes"; Correct = "MasterBloodTypes"; Field = "blood_type_id" },
    @{ Wrong = "ApprenticeshipOrders"; Correct = "ApprenticeOrders"; Field = "apprenticeship_order_id|apprentice_order_id" },
    @{ Wrong = "JobCategories"; Correct = "MasterJobCategories"; Field = "job_category_id|master_job_category_id" },
    @{ Wrong = "InterviewResults"; Correct = "MasterInterviewResults"; Field = "interview_result_id|master_interview_result_id" },
    @{ Wrong = "RejectedReasons"; Correct = "MasterRejectedReasons"; Field = "rejected_reason_id|master_rejected_reason_id" }
)

# Get all Table files
$tableFiles = Get-ChildItem "$projectRoot\src\Model\Table" -Filter "*Table.php" | 
    Where-Object { $_.Name -ne "AppTable.php" }

Write-Host "Scanning $($tableFiles.Count) Table files..." -ForegroundColor White
"Scanning $($tableFiles.Count) Table files..." | Out-File $logFile -Append
"" | Out-File $logFile -Append

foreach ($file in $tableFiles) {
    $content = Get-Content $file.FullName -Raw
    $relativePath = $file.Name
    
    foreach ($issue in $namingIssues) {
        # Check in belongsTo definitions
        if ($content -match "belongsTo\('$($issue.Wrong)'") {
            $issuesFound++
            Write-Host "[ISSUE] $relativePath - Uses '$($issue.Wrong)' instead of '$($issue.Correct)' in belongsTo" -ForegroundColor Yellow
            "[ISSUE] $relativePath - belongsTo('$($issue.Wrong)') should be belongsTo('$($issue.Correct)')" | Out-File $logFile -Append
        }
        
        # Check in hasMany definitions
        if ($content -match "hasMany\('$($issue.Wrong)'") {
            $issuesFound++
            Write-Host "[ISSUE] $relativePath - Uses '$($issue.Wrong)' instead of '$($issue.Correct)' in hasMany" -ForegroundColor Yellow
            "[ISSUE] $relativePath - hasMany('$($issue.Wrong)') should be hasMany('$($issue.Correct)')" | Out-File $logFile -Append
        }
        
        # Check in existsIn validation rules
        if ($content -match "existsIn\(\[.*\],\s*'$($issue.Wrong)'\)") {
            $issuesFound++
            Write-Host "[ISSUE] $relativePath - Uses '$($issue.Wrong)' instead of '$($issue.Correct)' in existsIn" -ForegroundColor Yellow
            "[ISSUE] $relativePath - existsIn validation for '$($issue.Wrong)' should use '$($issue.Correct)'" | Out-File $logFile -Append
        }
        
        # Check in @property annotations
        if ($content -match "@property.*\\\$($issue.Wrong)") {
            $issuesFound++
            Write-Host "[ISSUE] $relativePath - Uses '\$$($issue.Wrong)' instead of '\$$($issue.Correct)' in @property" -ForegroundColor Yellow
            "[ISSUE] $relativePath - @property annotation '\$$($issue.Wrong)' should be '\$$($issue.Correct)'" | Out-File $logFile -Append
        }
    }
}

# Check for missing 'strategy' => 'select' in cross-database associations
Write-Host "`nChecking for missing 'strategy' => 'select' in cross-database associations..." -ForegroundColor Cyan
"" | Out-File $logFile -Append
"Checking for missing 'strategy' => 'select'..." | Out-File $logFile -Append

$crossDbIssues = 0

foreach ($file in $tableFiles) {
    $content = Get-Content $file.FullName -Raw
    $lines = Get-Content $file.FullName
    
    # List of associations that require cross-database strategy
    $crossDbAssociations = @(
        "MasterGenders", "MasterReligions", "MasterMarriageStatuses",
        "MasterPropinsis", "MasterKabupatens", "MasterKecamatans", "MasterKelurahans",
        "MasterBloodTypes", "MasterJobCategories", "MasterInterviewResults", "MasterRejectedReasons",
        "VocationalTrainingInstitutions", "AcceptanceOrganizations", "CooperativeAssociations"
    )
    
    foreach ($assoc in $crossDbAssociations) {
        if ($content -match "belongsTo\('$assoc'") {
            # Check if 'strategy' => 'select' is present
            $assocPattern = "belongsTo\('$assoc',\s*\[([\s\S]*?)\]\)"
            if ($content -match $assocPattern) {
                $assocBlock = $Matches[1]
                if ($assocBlock -notmatch "'strategy'\s*=>\s*'select'") {
                    $crossDbIssues++
                    Write-Host "[WARNING] $($file.Name) - Association '$assoc' might need 'strategy' => 'select'" -ForegroundColor Yellow
                    "[WARNING] $($file.Name) - Association '$assoc' might need 'strategy' => 'select'" | Out-File $logFile -Append
                }
            }
        }
    }
}

# Summary
Write-Host "`n=== Summary ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append
"=== Summary ===" | Out-File $logFile -Append
"Total Table files scanned: $($tableFiles.Count)" | Out-File $logFile -Append
"Association naming issues found: $issuesFound" | Out-File $logFile -Append
"Potential cross-database strategy issues: $crossDbIssues" | Out-File $logFile -Append
"End Time: $(Get-Date)" | Out-File $logFile -Append

Write-Host "`nTotal files scanned: $($tableFiles.Count)" -ForegroundColor White
Write-Host "Association naming issues: $issuesFound" -ForegroundColor $(if ($issuesFound -eq 0) { "Green" } else { "Yellow" })
Write-Host "Potential cross-db issues: $crossDbIssues" -ForegroundColor $(if ($crossDbIssues -eq 0) { "Green" } else { "Yellow" })

if ($issuesFound -eq 0 -and $crossDbIssues -eq 0) {
    Write-Host "`n[OK] No association mapping issues found!" -ForegroundColor Green
} else {
    Write-Host "`n[ACTION NEEDED] Please review the issues above" -ForegroundColor Yellow
    Write-Host "See log file for details: $logFile" -ForegroundColor Cyan
}

Write-Host "`nLog saved to: $logFile" -ForegroundColor Cyan
