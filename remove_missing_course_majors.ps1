# PowerShell script to remove all references to missing CourseMajors tables
# Tables that don't exist: master_course_majors, course_majors

Write-Host "Removing references to missing CourseMajors tables..." -ForegroundColor Cyan

# Files to fix
$filesToFix = @(
    "src\Model\Table\CandidateCoursesTable.php",
    "src\Model\Table\ApprenticeCoursesTable.php",
    "src\Model\Table\TraineeCoursesTable.php",
    "src\Controller\CandidateCoursesController.php",
    "src\Controller\ApprenticeCoursesController.php",
    "src\Controller\TraineeCoursesController.php"
)

foreach ($file in $filesToFix) {
    if (Test-Path $file) {
        Write-Host "Processing: $file" -ForegroundColor Yellow
        
        $content = Get-Content $file -Raw
        $originalContent = $content
        
        # Remove @property lines for CourseMajors
        $content = $content -replace '\s*\*\s*@property\s+\\App\\Model\\Table\\MasterCourseMajorsTable.*?\n', ''
        $content = $content -replace '\s*\*\s*@property\s+\\App\\Model\\Table\\CourseMajorsTable.*?\n', ''
        
        # Remove belongsTo associations
        $content = $content -replace "\s*\$this->belongsTo\('MasterCourseMajors',[^\)]*\)\s*;\s*\n", ""
        $content = $content -replace "\s*\$this->belongsTo\('CourseMajors',[^\)]*\)\s*;\s*\n", ""
        
        # Remove validation rules
        $content = $content -replace "\s*\$rules->add\(\$rules->existsIn\(\['master_course_major_id'\],\s*'MasterCourseMajors'\)\);\s*\n", ""
        $content = $content -replace "\s*\$rules->add\(\$rules->existsIn\(\['course_major_id'\],\s*'CourseMajors'\)\);\s*\n", ""
        
        # Remove from contain arrays in controllers
        $content = $content -replace ",\s*'MasterCourseMajors'", ""
        $content = $content -replace ",\s*'CourseMajors'", ""
        $content = $content -replace "'MasterCourseMajors',\s*", ""
        $content = $content -replace "'CourseMajors',\s*", ""
        
        # Remove variable assignments in controllers
        $content = $content -replace "\s*\$mastercoursemajors\s*=\s*\$this->CandidateCourses->MasterCourseMajors->find\('list'\)->limit\(200\)->toArray\(\);\s*\n", ""
        $content = $content -replace "\s*\$coursemajors\s*=\s*\$this->ApprenticeCourses->CourseMajors->find\('list'\)->limit\(200\)->toArray\(\);\s*\n", ""
        $content = $content -replace "\s*\$coursemajors\s*=\s*\$this->TraineeCourses->CourseMajors->find\('list'\)->limit\(200\)->toArray\(\);\s*\n", ""
        $content = $content -replace "\s*\$masterCourseMajors\s*=\s*\$this->CandidateCourses->MasterCourseMajors->find\('list',\s*\['limit'\s*=>\s*200\]\);\s*\n", ""
        $content = $content -replace "\s*\$courseMajors\s*=\s*\$this->ApprenticeCourses->CourseMajors->find\('list',\s*\['limit'\s*=>\s*200\]\);\s*\n", ""
        $content = $content -replace "\s*\$courseMajors\s*=\s*\$this->TraineeCourses->CourseMajors->find\('list',\s*\['limit'\s*=>\s*200\]\);\s*\n", ""
        
        # Remove from compact() calls
        $content = $content -replace ",\s*'mastercoursemajors'", ""
        $content = $content -replace ",\s*'coursemajors'", ""
        $content = $content -replace ",\s*'masterCourseMajors'", ""
        $content = $content -replace ",\s*'courseMajors'", ""
        $content = $content -replace "'mastercoursemajors',\s*", ""
        $content = $content -replace "'coursemajors',\s*", ""
        $content = $content -replace "'masterCourseMajors',\s*", ""
        $content = $content -replace "'courseMajors',\s*", ""
        
        # Remove from $contain[] assignments
        $content = $content -replace "\s*\$contain\[\]\s*=\s*'MasterCourseMajors';\s*\n", ""
        $content = $content -replace "\s*\$contain\[\]\s*=\s*'CourseMajors';\s*\n", ""
        
        if ($content -ne $originalContent) {
            Set-Content $file -Value $content -NoNewline
            Write-Host "  ✓ Fixed $file" -ForegroundColor Green
        } else {
            Write-Host "  - No changes needed" -ForegroundColor Gray
        }
    } else {
        Write-Host "  ✗ File not found: $file" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Done! All CourseMajors references removed." -ForegroundColor Green
Write-Host "Run: bin/cake cache clear_all" -ForegroundColor Yellow
