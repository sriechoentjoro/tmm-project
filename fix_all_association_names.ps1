# Fix All Association Name Mismatches
Write-Host "Fixing association name mismatches..." -ForegroundColor Cyan

# Mapping of incorrect association names to correct ones
$fixes = @{
    # ApprenticeshipOrders -> ApprenticeOrders
    "'ApprenticeshipOrders'" = "'ApprenticeOrders'"
    
    # Plural to Master prefix corrections
    "'Propinsis'" = "'MasterPropinsis'"
    "'Kabupatens'" = "'MasterKabupatens'"
    "'Kecamatans'" = "'MasterKecamatans'"
    "'BloodTypes'" = "'MasterBloodTypes'"
    
    # Other missing Master prefix
    "'OccupationCategories'" = "'MasterOccupationCategories'"
    "'MedicalCheckUpResults'" = "'MasterMedicalCheckUpResults'"
    
    # Submission document categories
    "'MasterApprenticeshipSubmissionDocumentCategories'" = "'MasterApprenticeSubmissionDocumentCategories'"
    "'ApprenticeshipSubmissionDocuments'" = "'MasterApprenticeSubmissionDocuments'"
    
    # COE Types
    "'MasterCoeTypes'" = "'MasterApprenticeCoeTypes'"
    
    # Generic names that need more specific names
    "'Applicants'" = "'Candidates'"
    "'Documents'" = "'CandidateDocuments'"
    "'Categories'" = "'CandidateDocumentCategories'"
    "'Dashboards'" = "'CandidateDocumentManagementDashboards'"  
    
    # Job/Position related
    "'MasterPositions'" = "'MasterEmploymentPositions'"
    "'JobCategories'" = "'MasterJobCategories'"
}

$count = 0
$files = Get-ChildItem -Path "src\Model\Table" -Filter "*Table.php" -Recurse
$files += Get-ChildItem -Path "src\Controller" -Filter "*Controller.php" -Recurse

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $originalContent = $content
    
    foreach ($wrong in $fixes.Keys) {
        $correct = $fixes[$wrong]
        if ($content -match [regex]::Escape($wrong)) {
            $content = $content -replace [regex]::Escape($wrong), $correct
        }
    }
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        $count++
        Write-Host "  Fixed: $($file.Name)" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Fixed $count files" -ForegroundColor Yellow
Write-Host "Clearing cache..." -ForegroundColor Cyan
bin\cake cache clear_all
Write-Host "Done!" -ForegroundColor Green
