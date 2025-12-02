# Script to apply language switcher with light glossy theme to all process-flow templates
# Created: December 3, 2025

$ErrorActionPreference = "Continue"
$projectRoot = "d:\xampp\htdocs\tmm"
$templatesPath = "$projectRoot\src\Template"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Apply Language Switcher to All Process Flow Pages" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Get all controller directories
$controllerDirs = Get-ChildItem -Path $templatesPath -Directory | Where-Object { 
    $_.Name -ne "Layout" -and $_.Name -ne "Element" -and $_.Name -ne "Email" -and $_.Name -ne "Error" -and $_.Name -ne "Pages"
}

$totalFiles = 0
$updatedFiles = 0
$skippedFiles = 0
$errorFiles = 0

# The style and switcher code to insert
$styleAndSwitcherCode = @'

<!-- Styling -->
<style>
.content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.language-switcher {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 50%, #80deea 100%);
    border-radius: 15px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    border: 1px solid rgba(255, 255, 255, 0.6);
    position: relative;
    overflow: hidden;
}

.language-switcher::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    transform: rotate(45deg);
    animation: glossy-shine 3s infinite;
}

@keyframes glossy-shine {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
    }
    100% {
        transform: translateX(100%) translateY(100%) rotate(45deg);
    }
}

.lang-btn {
    display: inline-block;
    padding: 10px 25px;
    margin: 0 8px;
    border: 2px solid rgba(0, 188, 212, 0.5);
    background: rgba(255, 255, 255, 0.7);
    color: #00796b;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    box-shadow: 0 4px 15px rgba(0, 188, 212, 0.2);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    position: relative;
    z-index: 1;
}

.lang-btn:hover {
    background: rgba(255, 255, 255, 0.95);
    color: #00695c;
    text-decoration: none;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 188, 212, 0.4);
    border-color: #00bcd4;
}

.lang-btn.active {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(224, 247, 250, 0.95) 100%);
    color: #00796b;
    border-color: #00bcd4;
    font-weight: 700;
    box-shadow: 0 6px 25px rgba(0, 188, 212, 0.5);
}

@media (max-width: 768px) {
    .language-switcher {
        padding: 15px 10px;
    }
    
    .lang-btn {
        padding: 8px 15px;
        margin: 5px 4px;
        font-size: 13px;
    }
}
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Language Switcher Buttons -->
    <div class="language-switcher">
        <a href="?lang=ind" class="lang-btn <?= $currentLang === 'ind' ? 'active' : '' ?>">
            🇮🇩 Indonesian
        </a>
        <a href="?lang=eng" class="lang-btn <?= $currentLang === 'eng' ? 'active' : '' ?>">
            🇬🇧 English
        </a>
        <a href="?lang=jpn" class="lang-btn <?= $currentLang === 'jpn' ? 'active' : '' ?>">
            🇯🇵 日本語
        </a>
    </div>

'@

$closingWrapperTag = "`n</div><!-- End .content-wrapper -->"

foreach ($dir in $controllerDirs) {
    $processFlowFile = Join-Path $dir.FullName "process_flow.ctp"
    
    if (Test-Path $processFlowFile) {
        $totalFiles++
        Write-Host "Processing: $($dir.Name)/process_flow.ctp" -ForegroundColor Yellow
        
        try {
            # Read the file content
            $content = Get-Content $processFlowFile -Raw -Encoding UTF8
            
            # Check if already has language switcher
            if ($content -match 'class="language-switcher"') {
                Write-Host "  ⏭️  Already has language switcher - Skipping" -ForegroundColor Gray
                $skippedFiles++
                continue
            }
            
            # Check if file has the expected structure
            if ($content -notmatch '\$currentLang = \$this->request->getSession\(\)->read\(''Config\.language''\)') {
                Write-Host "  ⚠️  Missing language variable - Skipping" -ForegroundColor Magenta
                $skippedFiles++
                continue
            }
            
            # Find the position after the PHP block that sets $currentLang
            $pattern = '(\$currentLang = \$this->request->getSession\(\)->read\(''Config\.language''\) \?: ''ind'';[\r\n]+\?>)'
            
            if ($content -match $pattern) {
                # Insert style and switcher after the PHP block
                $content = $content -replace $pattern, "`$1$styleAndSwitcherCode"
                
                # Add closing wrapper tag before the end of file
                # Find the last closing </div> and add wrapper close before it
                if ($content -match '(<!-- TODO: Customize this template.*)$') {
                    $content = $content -replace '(<!-- TODO: Customize this template.*)', "$closingWrapperTag`n`n`$1"
                } else {
                    # If no TODO comment, add at the very end
                    $content = $content.TrimEnd() + "`n$closingWrapperTag`n"
                }
                
                # Save the modified content (UTF-8 without BOM)
                $utf8NoBom = New-Object System.Text.UTF8Encoding $false
                [System.IO.File]::WriteAllText($processFlowFile, $content, $utf8NoBom)
                
                Write-Host "  ✅ Successfully updated!" -ForegroundColor Green
                $updatedFiles++
            } else {
                Write-Host "  ⚠️  Pattern not found - Skipping" -ForegroundColor Magenta
                $skippedFiles++
            }
            
        } catch {
            Write-Host "  ❌ Error: $($_.Exception.Message)" -ForegroundColor Red
            $errorFiles++
        }
        
        Write-Host ""
    }
}

# Summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Summary" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Total files found:    $totalFiles" -ForegroundColor White
Write-Host "Successfully updated: $updatedFiles" -ForegroundColor Green
Write-Host "Skipped:              $skippedFiles" -ForegroundColor Yellow
Write-Host "Errors:               $errorFiles" -ForegroundColor Red
Write-Host ""

if ($updatedFiles -gt 0) {
    Write-Host "Language switcher with light glossy theme has been applied!" -ForegroundColor Green
    Write-Host "All process-flow pages now have:" -ForegroundColor Cyan
    Write-Host "   - Light cyan glossy background" -ForegroundColor White
    Write-Host "   - 3 language buttons (Indonesian, English, Japanese)" -ForegroundColor White
    Write-Host "   - Centered content wrapper" -ForegroundColor White
    Write-Host "   - Glossy shine animation" -ForegroundColor White
    Write-Host ""
    Write-Host "Next step: Clear cache with:" -ForegroundColor Yellow
    Write-Host "   bin\cake cache clear_all" -ForegroundColor White
}

Write-Host ""
