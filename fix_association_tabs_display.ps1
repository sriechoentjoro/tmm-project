# PowerShell script to fix association display in view template tabs
# Fix the display of foreign keys in related tables to show title/name instead of ID

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupDir = "template_backups\fix_tabs_associations_$timestamp"
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

Write-Host "Backup directory: $backupDir"

# Find all view.ctp files
$viewFiles = Get-ChildItem -Path "src\Template" -Recurse -Filter "view.ctp" | Where-Object { $_.FullName -notlike "*\Bake\*" }
Write-Host "Found $($viewFiles.Count) view.ctp files to process`n"

$successCount = 0
$skipCount = 0
$errorCount = 0

foreach ($file in $viewFiles) {
    try {
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        $originalContent = $content
        $modified = $false
        
        # Pattern 1: Fix candidate display (should use fullname, not title/name fallback)
        if ($content -match '\$displayValue = \$\w+->candidate->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->candidate->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->candidate->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->candidate->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->candidate_id;\s+\}', '$1$2->candidate->fullname;$3if (empty($displayValue)) { $displayValue = $2->candidate_id; }'
            $modified = $true
        }
        
        # Pattern 2: Fix trainee display (should use fullname)
        if ($content -match '\$displayValue = \$\w+->trainee->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->trainee->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->trainee->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->trainee->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->trainee_id;\s+\}', '$1$2->trainee->fullname;$3if (empty($displayValue)) { $displayValue = $2->trainee_id; }'
            $modified = $true
        }
        
        # Pattern 3: Fix apprentice display (should use fullname)
        if ($content -match '\$displayValue = \$\w+->apprentice->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->apprentice->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->apprentice->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->apprentice->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->apprentice_id;\s+\}', '$1$2->apprentice->fullname;$3if (empty($displayValue)) { $displayValue = $2->apprentice_id; }'
            $modified = $true
        }
        
        # Pattern 4: Fix user display (should use fullname)
        if ($content -match '\$displayValue = \$\w+->user->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->user->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->user->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->user->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->user_id;\s+\}', '$1$2->user->fullname;$3if (empty($displayValue)) { $displayValue = $2->user_id; }'
            $modified = $true
        }
        
        # Pattern 5: Fix vocational_training_institution display (should use name only)
        if ($content -match '\$displayValue = \$\w+->vocationalTrainingInstitution->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->vocationalTrainingInstitution->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->vocationalTrainingInstitution->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->vocationalTrainingInstitution->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->vocational_training_institution_id;\s+\}', '$1$2->vocationalTrainingInstitution->name;$3if (empty($displayValue)) { $displayValue = $2->vocational_training_institution_id; }'
            $modified = $true
        }
        
        # Pattern 6: Fix acceptance_organization display (should use name only)
        if ($content -match '\$displayValue = \$\w+->acceptanceOrganization->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->acceptanceOrganization->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->acceptanceOrganization->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->acceptanceOrganization->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->acceptance_organization_id;\s+\}', '$1$2->acceptanceOrganization->name;$3if (empty($displayValue)) { $displayValue = $2->acceptance_organization_id; }'
            $modified = $true
        }
        
        # Pattern 7: Fix cooperative_association display (should use name only)
        if ($content -match '\$displayValue = \$\w+->cooperativeAssociation->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->cooperativeAssociation->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->cooperativeAssociation->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->cooperativeAssociation->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->cooperative_association_id;\s+\}', '$1$2->cooperativeAssociation->name;$3if (empty($displayValue)) { $displayValue = $2->cooperative_association_id; }'
            $modified = $true
        }
        
        # Pattern 8: Fix special_skill_support_institution display (should use name only)
        if ($content -match '\$displayValue = \$\w+->specialSkillSupportInstitution->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->specialSkillSupportInstitution->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->specialSkillSupportInstitution->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->specialSkillSupportInstitution->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->special_skill_support_institution_id;\s+\}', '$1$2->specialSkillSupportInstitution->name;$3if (empty($displayValue)) { $displayValue = $2->special_skill_support_institution_id; }'
            $modified = $true
        }
        
        # Pattern 9: Fix apprentice_order display (should use title only)
        if ($content -match '\$displayValue = \$\w+->apprenticeOrder->title;[\s\S]{0,300}if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \$\w+->apprenticeOrder->name;') {
            $content = $content -replace '(\$displayValue = )(\$\w+)->apprenticeOrder->title;([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->apprenticeOrder->name;\s+\}([\s\S]+?)if \(empty\(\$displayValue\)\) \{\s+\$displayValue = \2->apprenticeship_order_id;\s+\}', '$1$2->apprenticeOrder->title;$3if (empty($displayValue)) { $displayValue = $2->apprenticeship_order_id; }'
            $modified = $true
        }
        
        if ($modified) {
            # Create backup
            $relativePath = $file.FullName -replace [regex]::Escape((Get-Location).Path + '\'), ''
            $backupPath = Join-Path $backupDir $relativePath
            $backupFolder = Split-Path $backupPath -Parent
            New-Item -ItemType Directory -Path $backupFolder -Force | Out-Null
            Set-Content -Path $backupPath -Value $originalContent -Encoding UTF8 -NoNewline
            
            # Save modified content
            Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
            
            Write-Host "[SUCCESS] Fixed tab associations: $relativePath" -ForegroundColor Green
            $successCount++
        }
        else {
            $skipCount++
        }
    }
    catch {
        Write-Host "[ERROR] Failed to process: $($file.FullName)" -ForegroundColor Red
        Write-Host "Error: $_" -ForegroundColor Red
        $errorCount++
    }
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Successfully updated: $successCount files" -ForegroundColor Green
Write-Host "Skipped: $skipCount files" -ForegroundColor Yellow
Write-Host "Errors: $errorCount files" -ForegroundColor $(if($errorCount -gt 0){'Red'}else{'Green'})
Write-Host "Backups: $backupDir" -ForegroundColor Cyan

# Clear cache
Write-Host "`nClearing cache..." -ForegroundColor Cyan
& "bin\cake" cache clear_all

Write-Host "`n--- Tab association displays fixed" -ForegroundColor Green
Write-Host "Related tables in tabs now show correct title/name/fullname" -ForegroundColor Green
