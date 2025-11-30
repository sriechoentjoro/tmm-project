# Script to rename apprenticeship_order_id to apprentice_order_id throughout the project
# Date: November 16, 2025

$projectRoot = "d:\xampp\htdocs\project_tmm"
$logFile = "$projectRoot\rename_apprenticeship_order_id.log"

# Initialize log
"=== Rename apprenticeship_order_id to apprentice_order_id ===" | Out-File $logFile
"Start Time: $(Get-Date)" | Out-File $logFile -Append
"" | Out-File $logFile -Append

# Function to replace in file
function Replace-InFile {
    param (
        [string]$FilePath,
        [string]$OldText,
        [string]$NewText
    )
    
    try {
        $content = Get-Content $FilePath -Raw -Encoding UTF8
        if ($content -match [regex]::Escape($OldText)) {
            $newContent = $content -replace [regex]::Escape($OldText), $NewText
            
            # Use UTF-8 without BOM to prevent "Namespace declaration must be first" error
            $utf8NoBom = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllText($FilePath, $newContent, $utf8NoBom)
            
            "[OK] Updated: $FilePath" | Out-File $logFile -Append
            Write-Host "[OK] Updated: $FilePath" -ForegroundColor Green
            return $true
        }
        return $false
    } catch {
        "[ERROR] Error in $FilePath : $_" | Out-File $logFile -Append
        Write-Host "[ERROR] Error in $FilePath : $_" -ForegroundColor Red
        return $false
    }
}

Write-Host "`n=== Step 1: Update Model Table Files ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append
"=== Step 1: Update Model Table Files ===" | Out-File $logFile -Append

$tableFiles = @(
    "$projectRoot\src\Model\Table\CandidatesTable.php",
    "$projectRoot\src\Model\Table\TraineesTable.php",
    "$projectRoot\src\Model\Table\ApprenticesTable.php",
    "$projectRoot\src\Model\Table\ApprenticeOrdersTable.php"
)

foreach ($file in $tableFiles) {
    if (Test-Path $file) {
        Replace-InFile -FilePath $file -OldText "apprenticeship_order_id" -NewText "apprentice_order_id"
    }
}

Write-Host "`n=== Step 2: Update Model Entity Files ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append
"=== Step 2: Update Model Entity Files ===" | Out-File $logFile -Append

$entityFiles = @(
    "$projectRoot\src\Model\Entity\Candidate.php",
    "$projectRoot\src\Model\Entity\Trainee.php",
    "$projectRoot\src\Model\Entity\Apprentice.php"
)

foreach ($file in $entityFiles) {
    if (Test-Path $file) {
        Replace-InFile -FilePath $file -OldText "apprenticeship_order_id" -NewText "apprentice_order_id"
    }
}

Write-Host "`n=== Step 3: Update Controller Files ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append
"=== Step 3: Update Controller Files ===" | Out-File $logFile -Append

$controllerFiles = Get-ChildItem "$projectRoot\src\Controller\*.php" -Recurse | Where-Object { $_.FullName -notlike "*\template_backups\*" }

foreach ($file in $controllerFiles) {
    Replace-InFile -FilePath $file.FullName -OldText "apprenticeship_order_id" -NewText "apprentice_order_id"
}

Write-Host "`n=== Step 4: Update Template Files ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append
"=== Step 4: Update Template Files ===" | Out-File $logFile -Append

$templateFiles = Get-ChildItem "$projectRoot\src\Template\*.ctp" -Recurse | Where-Object { $_.FullName -notlike "*\template_backups\*" }

foreach ($file in $templateFiles) {
    Replace-InFile -FilePath $file.FullName -OldText "apprenticeship_order_id" -NewText "apprentice_order_id"
}

Write-Host "`n=== Step 5: Update Bake Template Files ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append
"=== Step 5: Update Bake Template Files ===" | Out-File $logFile -Append

$bakeTemplateFiles = Get-ChildItem "$projectRoot\src\Template\Bake\*.ctp" -Recurse

foreach ($file in $bakeTemplateFiles) {
    Replace-InFile -FilePath $file.FullName -OldText "apprenticeship_order_id" -NewText "apprentice_order_id"
}

Write-Host "`n=== Step 6: Find Files with 'Apprenticeship' in Name ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append
"=== Step 6: Files with 'Apprenticeship' in Name ===" | Out-File $logFile -Append

$apprenticeshipFiles = Get-ChildItem "$projectRoot\*Apprenticeship*.php" -Recurse | Where-Object { $_.FullName -notlike "*\vendor\*" -and $_.FullName -notlike "*\template_backups\*" }

if ($apprenticeshipFiles.Count -gt 0) {
    Write-Host "`nFiles with 'Apprenticeship' in name:" -ForegroundColor Yellow
    foreach ($file in $apprenticeshipFiles) {
        $relativePath = $file.FullName.Replace($projectRoot, "")
        Write-Host "  - $relativePath" -ForegroundColor Yellow
        "  Found: $relativePath" | Out-File $logFile -Append
    }
    
    Write-Host "`nDo you want to DELETE these files? (Y/N): " -ForegroundColor Yellow -NoNewline
    $response = Read-Host
    
    if ($response -eq "Y" -or $response -eq "y") {
        foreach ($file in $apprenticeshipFiles) {
            Remove-Item $file.FullName -Force
            Write-Host "[OK] Deleted: $($file.Name)" -ForegroundColor Green
            "[OK] Deleted: $($file.FullName)" | Out-File $logFile -Append
        }
    } else {
        Write-Host "Skipped deletion." -ForegroundColor Yellow
        "User skipped deletion of Apprenticeship files" | Out-File $logFile -Append
    }
} else {
    Write-Host "No files with 'Apprenticeship' in name found." -ForegroundColor Green
    "No files with 'Apprenticeship' in name found." | Out-File $logFile -Append
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
"" | Out-File $logFile -Append
"=== Summary ===" | Out-File $logFile -Append
"End Time: $(Get-Date)" | Out-File $logFile -Append

Write-Host "`n[OK] All files have been updated!" -ForegroundColor Green
Write-Host "[OK] Log file saved to: $logFile" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Update database column names in phpMyAdmin:" -ForegroundColor White
Write-Host "   - candidates table: apprenticeship_order_id → apprentice_order_id" -ForegroundColor White
Write-Host "   - trainees table: apprenticeship_order_id → apprentice_order_id" -ForegroundColor White
Write-Host "   - apprentices table: apprenticeship_order_id → apprentice_order_id" -ForegroundColor White
Write-Host "2. Clear CakePHP cache: bin\cake cache clear_all" -ForegroundColor White
Write-Host "3. Test in browser" -ForegroundColor White

# Pause to show results
Read-Host "`nPress Enter to exit"
