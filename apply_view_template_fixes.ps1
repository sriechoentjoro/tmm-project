# Apply View Template Fixes - File Preview & Association Display
# This script fixes all view.ctp files to:
# 1. Use showPreview=true for file_viewer element
# 2. Display association names/titles instead of IDs

Write-Host "=== Applying View Template Fixes ===" -ForegroundColor Cyan
Write-Host ""

$projectRoot = "d:\xampp\htdocs\project_tmm"
$viewFiles = Get-ChildItem -Path "$projectRoot\src\Template" -Recurse -Filter "view.ctp"

$stats = @{
    Total = 0
    Updated = 0
    Skipped = 0
    Errors = 0
}

# Common association patterns to fix
$associationPatterns = @(
    # Pattern: ID display → Name/Title display
    @{
        Search = "cooperative_association_id"
        IDPattern = '\$this->Number->format\(\$\w+->cooperative_association_id\)'
        Replacement = '$apprenticeOrder->has(''cooperative_association'') ? h($apprenticeOrder->cooperative_association->name) : ''''
        Label = 'Cooperative Association'
    },
    @{
        Search = "acceptance_organization_id"
        IDPattern = '\$this->Number->format\(\$\w+->acceptance_organization_id\)'
        Replacement = '$apprenticeOrder->has(''acceptance_organization'') ? h($apprenticeOrder->acceptance_organization->title) : ''''
        Label = 'Acceptance Organization'
    },
    @{
        Search = "job_category_id"
        IDPattern = '\$this->Number->format\(\$\w+->job_category_id\)'
        Replacement = '$apprenticeOrder->has(''master_job_category'') ? h($apprenticeOrder->master_job_category->title) : ''''
        Label = 'Job Category'
    },
    @{
        Search = "candidate_id"
        IDPattern = '\$this->Number->format\(\$\w+->candidate_id\)'
        Replacement = '$entity->has(''candidate'') ? h($entity->candidate->fullname) : ''''
        Label = 'Candidate'
    },
    @{
        Search = "trainee_id"
        IDPattern = '\$this->Number->format\(\$\w+->trainee_id\)'
        Replacement = '$entity->has(''trainee'') ? h($entity->trainee->fullname) : ''''
        Label = 'Trainee'
    },
    @{
        Search = "user_id"
        IDPattern = '\$this->Number->format\(\$\w+->user_id\)'
        Replacement = '$entity->has(''user'') ? h($entity->user->fullname) : ''''
        Label = 'User'
    }
)

foreach ($file in $viewFiles) {
    $stats.Total++
    $relativePath = $file.FullName.Replace($projectRoot, "").TrimStart("\")
    
    Write-Host "Processing: $relativePath" -ForegroundColor Yellow
    
    try {
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        $originalContent = $content
        $updated = $false
        
        # Fix 1: Update file_viewer element to use showPreview
        if ($content -match "element\('file_viewer'") {
            # Check if already has showPreview
            if ($content -notmatch "showPreview.*=>.*true") {
                # Add showPreview => true to existing file_viewer calls
                $content = $content -replace "(element\('file_viewer',\s*\[)([^\]]+)(\])", {
                    param($match)
                    $start = $match.Groups[1].Value
                    $params = $match.Groups[2].Value
                    $end = $match.Groups[3].Value
                    
                    # Add showPreview if not exists
                    if ($params -notmatch "showPreview") {
                        if ($params.Trim() -ne "") {
                            return "$start$params,`n                        'showPreview' => true$end"
                        } else {
                            return "$start'showPreview' => true$end"
                        }
                    }
                    return $match.Value
                }
                
                if ($content -ne $originalContent) {
                    Write-Host "  [✓] Added showPreview => true to file_viewer" -ForegroundColor Green
                    $updated = $true
                }
            }
        }
        
        # Fix 2: Replace association ID displays with name/title
        foreach ($pattern in $associationPatterns) {
            if ($content -match $pattern.Search) {
                # Replace ID display with association name/title
                $newContent = $content -replace "<th[^>]*>__\('$($pattern.Search.Replace('_', ' '))'.*?Id'\)</th>\s*<td[^>]*>\s*" + $pattern.IDPattern + "\s*</td>",
                    "<th class=`"github-detail-label`"><?= __('$($pattern.Label)') ?></th>`n                            <td class=`"github-detail-value`">`n                                <?= $($pattern.Replacement) ?>`n                            </td>"
                
                if ($newContent -ne $content) {
                    $content = $newContent
                    Write-Host "  [✓] Fixed $($pattern.Label) display" -ForegroundColor Green
                    $updated = $true
                }
            }
        }
        
        # Fix 3: Remove duplicate Reference File rows in table (keep only preview section)
        if ($content -match "Reference File.*?file_viewer") {
            # Count occurrences
            $matches = [regex]::Matches($content, "Reference File.*?file_viewer")
            if ($matches.Count -gt 1) {
                # Remove table row occurrences, keep preview section
                $content = $content -replace "<tr>\s*<th[^>]*>__\('Reference File'\)</th>\s*<td[^>]*>.*?element\('file_viewer'[^\)]+\).*?</td>\s*</tr>", ""
                Write-Host "  [✓] Removed duplicate Reference File table row" -ForegroundColor Green
                $updated = $true
            }
        }
        
        if ($updated) {
            Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
            $stats.Updated++
            Write-Host "  [✓] File updated successfully" -ForegroundColor Green
        } else {
            Write-Host "  [→] No changes needed" -ForegroundColor Gray
            $stats.Skipped++
        }
        
    } catch {
        Write-Host "  [✗] Error: $_" -ForegroundColor Red
        $stats.Errors++
    }
    
    Write-Host ""
}

# Summary
Write-Host "=== Summary ===" -ForegroundColor Cyan
Write-Host "Total files processed: $($stats.Total)" -ForegroundColor White
Write-Host "Files updated: $($stats.Updated)" -ForegroundColor Green
Write-Host "Files skipped: $($stats.Skipped)" -ForegroundColor Gray
Write-Host "Errors: $($stats.Errors)" -ForegroundColor Red
Write-Host ""
Write-Host "Done! Run 'bin\cake cache clear_all' to apply changes." -ForegroundColor Yellow
