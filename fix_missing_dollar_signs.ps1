# Fix missing $ signs in view templates after association fix

$viewFiles = @(
    "src\Template\Candidates\view.ctp",
    "src\Template\Trainees\view.ctp",
    "src\Template\MasterAuthorizationRoles\view.ctp",
    "src\Template\MasterCandidateInterviewResults\view.ctp",
    "src\Template\MasterCandidateInterviewTypes\view.ctp",
    "src\Template\MasterJapanPrefectures\view.ctp",
    "src\Template\Users\view.ctp"
)

$fixedCount = 0

foreach ($file in $viewFiles) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw -Encoding UTF8
        $originalContent = $content
        
        # Fix missing $ before variable names in generated code
        # Pattern: word->has('...') without $ at the beginning
        $content = $content -replace '(?<=\s)([a-z]\w+)->has\(', '$$$1->has('
        
        # Fix in ternary expressions too
        $content = $content -replace '\?\s*([a-z]\w+)->Html', '? $$$1->Html'
        
        if ($content -ne $originalContent) {
            Set-Content -Path $file -Value $content -Encoding UTF8 -NoNewline
            Write-Host "[FIXED] $file" -ForegroundColor Green
            $fixedCount++
        }
    }
}

Write-Host "`nFixed $fixedCount files" -ForegroundColor Cyan

# Clear cache
Write-Host "`nClearing cache..." -ForegroundColor Cyan
& "bin\cake" cache clear_all
