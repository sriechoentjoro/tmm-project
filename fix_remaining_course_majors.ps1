# Fix remaining CourseMajors references manually

$files = @(
    "src\Controller\ApprenticeCoursesController.php",
    "src\Controller\TraineeCoursesController.php"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        Write-Host "Fixing: $file" -ForegroundColor Yellow
        
        $content = Get-Content $file -Raw
        
        # Remove CourseMajors find() lines
        $content = $content -replace '\s*\$coursemajors\s*=\s*\$this->[^\n]+->CourseMajors->find[^\n]+\n', ''
        $content = $content -replace '\s*\$courseMajors\s*=\s*\$this->[^\n]+->CourseMajors->find[^\n]+\n', ''
        
        # Remove CourseMajors from contain arrays
        $content = $content -replace "\$contain\[\]\s*=\s*'CourseMajors';\s*\n", ""
        
        # Remove coursemajors/courseMajors from compact() calls
        $content = $content -replace ", 'coursemajors'", ""
        $content = $content -replace ", 'courseMajors'", ""
        $content = $content -replace "'coursemajors', ", ""
        $content = $content -replace "'courseMajors', ", ""
        
        Set-Content $file -Value $content -NoNewline
        Write-Host "  Fixed!" -ForegroundColor Green
    }
}

Write-Host "Done!" -ForegroundColor Green
