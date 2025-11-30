# PowerShell script to fix association display in view templates
# Replace ID display with title/name display for related tables

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupDir = "template_backups\fix_associations_$timestamp"
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

Write-Host "Backup directory: $backupDir"

# Find all view.ctp files
$viewFiles = Get-ChildItem -Path "src\Template" -Recurse -Filter "view.ctp"
Write-Host "Found $($viewFiles.Count) view.ctp files to process`n"

$successCount = 0
$skipCount = 0
$errorCount = 0

# Define patterns to fix:
# 1. Master tables in related records (hasMany tables)
$patterns = @(
    # Master tables - use 'title' field
    @{
        Match = 'h\(\$(\w+)->master_(\w+)_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            $table = $match.Groups[2].Value
            
            # Convert to PascalCase
            $pascalTable = ($table -split '_' | ForEach-Object { $_.Substring(0,1).ToUpper() + $_.Substring(1) }) -join ''
            
            # Build association name
            $assocName = "master_$table"
            
            return "$entity->has('$assocName') ? `$this->Html->link(`$$entity->$assocName->title, ['controller' => 'Master${pascalTable}s', 'action' => 'view', `$$entity->$assocName->id]) : '' ?>"
        }
    },
    # Vocational Training Institution
    @{
        Match = 'h\(\$(\w+)->vocational_training_institution_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('vocational_training_institution') ? `$this->Html->link(`$$entity->vocational_training_institution->name, ['controller' => 'VocationalTrainingInstitutions', 'action' => 'view', `$$entity->vocational_training_institution->id]) : '' ?>"
        }
    },
    # Acceptance Organization
    @{
        Match = 'h\(\$(\w+)->acceptance_organization_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('acceptance_organization') ? `$this->Html->link(`$$entity->acceptance_organization->name, ['controller' => 'AcceptanceOrganizations', 'action' => 'view', `$$entity->acceptance_organization->id]) : '' ?>"
        }
    },
    # Cooperative Association
    @{
        Match = 'h\(\$(\w+)->cooperative_association_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('cooperative_association') ? `$this->Html->link(`$$entity->cooperative_association->name, ['controller' => 'CooperativeAssociations', 'action' => 'view', `$$entity->cooperative_association->id]) : '' ?>"
        }
    },
    # Special Skill Support Institution
    @{
        Match = 'h\(\$(\w+)->special_skill_support_institution_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('special_skill_support_institution') ? `$this->Html->link(`$$entity->special_skill_support_institution->name, ['controller' => 'SpecialSkillSupportInstitutions', 'action' => 'view', `$$entity->special_skill_support_institution->id]) : '' ?>"
        }
    },
    # Candidate
    @{
        Match = 'h\(\$(\w+)->candidate_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('candidate') ? `$this->Html->link(`$$entity->candidate->fullname, ['controller' => 'Candidates', 'action' => 'view', `$$entity->candidate->id]) : '' ?>"
        }
    },
    # Trainee
    @{
        Match = 'h\(\$(\w+)->trainee_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('trainee') ? `$this->Html->link(`$$entity->trainee->fullname, ['controller' => 'Trainees', 'action' => 'view', `$$entity->trainee->id]) : '' ?>"
        }
    },
    # Apprentice
    @{
        Match = 'h\(\$(\w+)->apprentice_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('apprentice') ? `$this->Html->link(`$$entity->apprentice->fullname, ['controller' => 'Apprentices', 'action' => 'view', `$$entity->apprentice->id]) : '' ?>"
        }
    },
    # User
    @{
        Match = 'h\(\$(\w+)->user_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('user') ? `$this->Html->link(`$$entity->user->fullname, ['controller' => 'Users', 'action' => 'view', `$$entity->user->id]) : '' ?>"
        }
    },
    # Apprenticeship Order (special case - uses 'title')
    @{
        Match = 'h\(\$(\w+)->apprenticeship_order_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            return "$entity->has('apprentice_order') ? `$this->Html->link(`$$entity->apprentice_order->title, ['controller' => 'ApprenticeOrders', 'action' => 'view', `$$entity->apprentice_order->id]) : '' ?>"
        }
    },
    # Applicant (special handling - can be candidate or trainee)
    @{
        Match = 'h\(\$(\w+)->applicant_id\)\s*\?>'
        Replace = {
            param($match)
            $entity = $match.Groups[1].Value
            # Try to detect if it's candidate or trainee from context
            # For now, assume candidate (can be improved based on table name)
            return "$entity->has('candidate') ? `$this->Html->link(`$$entity->candidate->fullname, ['controller' => 'Candidates', 'action' => 'view', `$$entity->candidate->id]) : '' ?>"
        }
    }
)

foreach ($file in $viewFiles) {
    try {
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        $originalContent = $content
        $modified = $false
        
        foreach ($pattern in $patterns) {
            if ($content -match $pattern.Match) {
                $content = [regex]::Replace($content, $pattern.Match, $pattern.Replace)
                $modified = $true
            }
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
            
            Write-Host "[SUCCESS] Fixed associations: $relativePath" -ForegroundColor Green
            $successCount++
        }
        else {
            Write-Host "[SKIP] No association ID display: $($file.FullName -replace [regex]::Escape((Get-Location).Path + '\'), '')"
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

Write-Host "`n--- Association displays fixed in view templates" -ForegroundColor Green
Write-Host "Related tables will now show title/name instead of ID" -ForegroundColor Green
