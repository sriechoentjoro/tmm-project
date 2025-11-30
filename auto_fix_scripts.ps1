# Auto-fix script tags and stray braces in Bake-generated templates
# ---------------------------------------------------------------
# Place this file in the project root (d:\xampp\htdocs\tmm) and run it with PowerShell:
#   .\auto_fix_scripts.ps1
# The script will:
#   1. Scan all *.ctp files under src\Template\
#   2. Replace raw <script src=...> tags for image-preview.js and form-confirm.js
#      with CakePHP's Html->script helper (ensures correct URL and cache busting).
#   3. Remove stray solitary '}' lines that often appear in generated add.ctp files.
#   4. After all modifications, run the CakePHP test suite (bin\cake test).
#   5. Write a concise report to script_fix_report.md.

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$templateDir = Join-Path $projectRoot "src\\Template"
$reportPath = Join-Path $projectRoot "script_fix_report.md"

# Initialise report
"# Auto‑Fix Script Tags & Brace Cleanup Report`nGenerated on $(Get-Date)`n" | Set-Content $reportPath

function Replace-ScriptTags([string]$content) {
    # Replace image-preview.js tag
    $content = $content -replace '(?i)<script\s+src=\"[^\"]*image-preview\.js\"[^>]*>\s*</script>', "<?= $this->Html->script('image-preview.js') ?>"
    # Replace form-confirm.js tag
    $content = $content -replace '(?i)<script\s+src=\"[^\"]*form-confirm\.js\"[^>]*>\s*</script>', "<?= $this->Html->script('form-confirm.js') ?>"
    return $content
}

function Remove-StrayBrace([string]$content) {
    $lines = $content -split "`n"
    $newLines = @()
    foreach ($line in $lines) {
        if ($line -match "^\s*}\s*$") { continue }
        $newLines += $line
    }
    return ($newLines -join "`n")
}

$files = Get-ChildItem -Path $templateDir -Recurse -Filter "*.ctp"
foreach ($file in $files) {
    $original = Get-Content $file.FullName -Raw
    $modified = $original
    $modified = Replace-ScriptTags $modified
    $modified = Remove-StrayBrace $modified
    if ($modified -ne $original) {
        Set-Content -Path $file.FullName -Value $modified -Encoding UTF8
        "Modified $($file.FullName)" | Out-File -Append $reportPath
    }
}

# Run CakePHP test suite after modifications
$testCmd = "bin\\cake test"
$proc = Start-Process -FilePath "cmd.exe" -ArgumentList "/c $testCmd" -NoNewWindow -RedirectStandardOutput "test_output.txt" -RedirectStandardError "test_error.txt" -PassThru -Wait
$exitCode = $proc.ExitCode
$output = Get-Content "test_output.txt" -Raw
$testError = Get-Content "test_error.txt" -Raw
"`n--- Test Suite Result ---`nExit Code: $exitCode`n`nOutput:`n$output`n`nErrors:`n$testError" | Out-File -Append $reportPath

"# Finished`nReport written to $reportPath" | Out-File -Append $reportPath
