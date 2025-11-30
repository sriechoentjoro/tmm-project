# Auto-fix Filters and Delete Support for CakePHP Controllers
# ---------------------------------------------------------------
# This script runs on Windows PowerShell. Place it in the project root (d:\xampp\htdocs\tmm)
# and execute:  .\auto_fix_filters.ps1
# It will:
#   1. Scan all PHP controller files under src\Controller\
#   2. Ensure each controller uses SearchableTrait and AjaxFilterTrait
#   3. Update or create an index() method that applies the search filter and handles AJAX requests
#   4. Ensure a delete($id = null) action exists
#   5. After each file modification, run the CakePHP test suite (bin\cake test)
#   6. Log successes and failures to auto_fix_report.md

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$controllerDir = Join-Path $projectRoot "src\Controller"
$reportPath = Join-Path $projectRoot "auto_fix_report.md"

# Initialise report
"# Auto‑Fix Report`nGenerated on $(Get-Date)`n" | Set-Content $reportPath

function Add-Traits([string]$filePath) {
    $content = Get-Content $filePath -Raw
    $namespacePattern = "(?m)^(namespace\s+[^;]+;)"
    if ($content -notmatch "use\s+App\\Controller\\Traits\\SearchableTrait;") {
        $content = $content -replace $namespacePattern, "`$1`nuse App\\Controller\\Traits\\SearchableTrait;"
    }
    if ($content -notmatch "use\s+App\\Controller\\AjaxFilterTrait;") {
        $content = $content -replace $namespacePattern, "`$1`nuse App\\Controller\\AjaxFilterTrait;"
    }
    Set-Content $filePath $content
}

function Update-IndexMethod([string]$filePath) {
    $content = Get-Content $filePath -Raw
    $className = [System.IO.Path]::GetFileNameWithoutExtension($filePath)
    $modelVar = $className -replace 'Controller$', ''
    $modelVar = $modelVar.Substring(0,1).ToLower() + $modelVar.Substring(1)
    $indexPattern = "(?s)function\s+index\s*\([^)]*\)\s*\{.*?\}"
    $newIndex = @"
    public function index()
    {
        $${modelVar} = $this->${modelVar};
        $query = $${modelVar}->find();
        $query = $this->applySearchFilter($query);
        if ($this->request->is('ajax')) {
            return $this->handleAjaxFilter();
        }
        $this->set('${modelVar}s', $this->paginate($query));
    }
"@.Trim()
    if ($content -match $indexPattern) {
        $content = $content -replace $indexPattern, $newIndex
    } else {
        # Append new index method before the last closing brace of the class
        $content = $content -replace "(?m)\}\s*$", "    $newIndex`n}\n"
    }
    Set-Content $filePath $content
}

function Ensure-DeleteMethod([string]$filePath) {
    $content = Get-Content $filePath -Raw
    if ($content -notmatch "function\s+delete\s*\(") {
        $className = [System.IO.Path]::GetFileNameWithoutExtension($filePath)
        $modelVar = $className -replace 'Controller$', ''
        $modelVar = $modelVar.Substring(0,1).ToLower() + $modelVar.Substring(1)
        $deleteMethod = @"
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $${modelVar} = $this->${modelVar}->get($id);
        if ($this->${modelVar}->delete($${modelVar})) {
            $this->Flash->success(__('The ${modelVar} has been deleted.'));
        } else {
            $this->Flash->error(__('The ${modelVar} could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
"@.Trim()
        # Insert before the final closing brace of the class
        $content = $content -replace "(?m)\}\s*$", "    $deleteMethod`n}\n"
        Set-Content $filePath $content
    }
}

function Run-Tests() {
    $testCmd = "bin\cake test"
    $proc = Start-Process -FilePath "cmd.exe" -ArgumentList "/c $testCmd" -NoNewWindow -RedirectStandardOutput "test_output.txt" -RedirectStandardError "test_error.txt" -PassThru -Wait
    $exitCode = $proc.ExitCode
    $output = Get-Content "test_output.txt" -Raw
    $error = Get-Content "test_error.txt" -Raw
    return @{code=$exitCode; out=$output; err=$error}
}

# Main loop
Get-ChildItem -Path $controllerDir -Filter "*Controller.php" -Recurse | ForEach-Object {
    $file = $_.FullName
    "Processing $file..." | Out-File -Append $reportPath
    try {
        Add-Traits $file
        Update-IndexMethod $file
        Ensure-DeleteMethod $file
        $testResult = Run-Tests
        if ($testResult.code -eq 0) {
            "✅ Tests passed after modifying $($_.Name)" | Out-File -Append $reportPath
        } else {
            "❌ Tests FAILED after modifying $($_.Name)" | Out-File -Append $reportPath
            "Error output:`n$($testResult.err)" | Out-File -Append $reportPath
        }
    } catch {
        "⚠️ Exception while processing $($_.Name): $_" | Out-File -Append $reportPath
    }
}

"# Finished`nReport written to $reportPath" | Out-File -Append $reportPath
