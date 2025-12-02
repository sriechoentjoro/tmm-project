# PowerShell Script: Generate Default Process Flow Templates for All Controllers
# Purpose: Auto-create process_flow.ctp templates to prevent MissingTemplateException

Write-Host "Starting to generate process flow templates..." -ForegroundColor Green
Write-Host ""

$controllersPath = "src\Controller"
$templatesPath = "src\Template"
$created = 0
$skipped = 0

# Get all controllers
$controllers = Get-ChildItem -Path $controllersPath -Filter "*Controller.php" -Recurse

foreach ($controller in $controllers) {
    # Skip AppController and Component folder
    if ($controller.Name -eq "AppController.php" -or $controller.FullName -like "*\Component\*") {
        continue
    }
    
    # Read controller content
    $content = Get-Content -Path $controller.FullName -Raw -Encoding UTF8
    
    # Check if processFlow method exists
    if ($content -notlike "*function processFlow()*") {
        continue
    }
    
    # Extract controller name (remove "Controller.php")
    $controllerName = $controller.Name -replace "Controller\.php$", ""
    
    # Determine template path
    $templatePath = ""
    if ($controller.FullName -like "*\Controller\Admin\*") {
        # Admin namespace controller
        $templatePath = Join-Path $templatesPath "Admin\$controllerName"
    } else {
        # Regular controller
        $templatePath = Join-Path $templatesPath $controllerName
    }
    
    # Create directory if not exists
    if (-not (Test-Path $templatePath)) {
        New-Item -ItemType Directory -Path $templatePath -Force | Out-Null
    }
    
    # Check if process_flow.ctp already exists
    $templateFile = Join-Path $templatePath "process_flow.ctp"
    if (Test-Path $templateFile) {
        Write-Host "Skipped: $controllerName (template already exists)" -ForegroundColor Gray
        $skipped++
        continue
    }
    
    # Generate humanized controller title
    $controllerTitle = $controllerName -creplace '([a-z])([A-Z])', '$1 $2'
    
    # Create default template content (without __pf() function calls)
    $templateContent = @"
<?php
/**
 * Process Flow Documentation - $controllerTitle
 * Multi-language support: Indonesian, English, Japanese
 */

// Get current language from session (same as layout)
`$currentLang = `$this->request->getSession()->read('Config.language') ?: 'ind';
?>

<!-- Process Overview Section -->
<div class="flow-section">
    <h2>
        <i class="fas fa-clipboard-list"></i> 
        <?php if (`$currentLang === 'ind'): ?>
            Ringkasan Proses
        <?php elseif (`$currentLang === 'eng'): ?>
            Process Overview
        <?php else: ?>
            プロセス概要
        <?php endif; ?>
    </h2>
    
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        <?php if (`$currentLang === 'ind'): ?>
            <strong>$controllerTitle</strong> adalah modul untuk mengelola data terkait $controllerTitle dalam sistem TMM.
        <?php elseif (`$currentLang === 'eng'): ?>
            <strong>$controllerTitle</strong> is a module for managing $controllerTitle data in the TMM system.
        <?php else: ?>
            <strong>$controllerTitle</strong>は、TMMシステムで$controllerTitle データを管理するモジュールです。
        <?php endif; ?>
    </div>
    
    <!-- Workflow Steps -->
    <div class="workflow-steps">
        <div class="workflow-step">
            <span class="step-number">1</span>
            <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                <div class="step-title">
                    <?php if (`$currentLang === 'ind'): ?>
                        Tahap 1: Input Data
                    <?php elseif (`$currentLang === 'eng'): ?>
                        Step 1: Data Input
                    <?php else: ?>
                        ステップ1：データ入力
                    <?php endif; ?>
                    <span class="database-indicator">$($controllerName.ToLower())</span>
                </div>
                <div class="step-description">
                    <strong>
                        <?php if (`$currentLang === 'ind'): ?>
                            Siapa
                        <?php elseif (`$currentLang === 'eng'): ?>
                            Who
                        <?php else: ?>
                            誰が
                        <?php endif; ?>:
                    </strong> 
                    <?php if (`$currentLang === 'ind'): ?>
                        Administrator atau Operator
                    <?php elseif (`$currentLang === 'eng'): ?>
                        Administrator or Operator
                    <?php else: ?>
                        管理者またはオペレーター
                    <?php endif; ?>
                    <br>
                    
                    <strong>
                        <?php if (`$currentLang === 'ind'): ?>
                            Aksi
                        <?php elseif (`$currentLang === 'eng'): ?>
                            Action
                        <?php else: ?>
                            アクション
                        <?php endif; ?>:
                    </strong>
                    <?php if (`$currentLang === 'ind'): ?>
                        Mengisi formulir data $controllerTitle
                    <?php elseif (`$currentLang === 'eng'): ?>
                        Fill out $controllerTitle data form
                    <?php else: ?>
                        $controllerTitle データフォームに記入する
                    <?php endif; ?>
                    <br>
                    
                    <strong>
                        <?php if (`$currentLang === 'ind'): ?>
                            Hasil
                        <?php elseif (`$currentLang === 'eng'): ?>
                            Result
                        <?php else: ?>
                            結果
                        <?php endif; ?>:
                    </strong>
                    <?php if (`$currentLang === 'ind'): ?>
                        Data tersimpan ke database
                    <?php elseif (`$currentLang === 'eng'): ?>
                        Data saved to database
                    <?php else: ?>
                        データがデータベースに保存される
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visual Process Flow Diagram -->
<div class="flow-section">
    <h2>
        <i class="fas fa-project-diagram"></i>
        <?php if (`$currentLang === 'ind'): ?>
            Diagram Alur Visual
        <?php elseif (`$currentLang === 'eng'): ?>
            Visual Process Flow
        <?php else: ?>
            ビジュアルフロー図
        <?php endif; ?>
    </h2>
    
    <div class="mermaid">
graph TD
    A[<?php echo `$currentLang === 'ind' ? 'Input Data' : (`$currentLang === 'eng' ? 'Data Input' : 'データ入力'); ?>] --> B[<?php echo `$currentLang === 'ind' ? 'Validasi' : (`$currentLang === 'eng' ? 'Validation' : '検証'); ?>]
    B --> C{<?php echo `$currentLang === 'ind' ? 'Valid?' : (`$currentLang === 'eng' ? 'Valid?' : '有効？'); ?>}
    C -->|<?php echo `$currentLang === 'ind' ? 'Ya' : (`$currentLang === 'eng' ? 'Yes' : 'はい'); ?>| D[<?php echo `$currentLang === 'ind' ? 'Simpan ke Database' : (`$currentLang === 'eng' ? 'Save to Database' : 'データベースに保存'); ?>]
    C -->|<?php echo `$currentLang === 'ind' ? 'Tidak' : (`$currentLang === 'eng' ? 'No' : 'いいえ'); ?>| A
    D --> E[<?php echo `$currentLang === 'ind' ? 'Selesai' : (`$currentLang === 'eng' ? 'Done' : '完了'); ?>]
    
    style A fill:#e3f2fd
    style E fill:#c8e6c9
    </div>
</div>

<!-- Important Guidelines -->
<div class="flow-section">
    <h2>
        <i class="fas fa-exclamation-triangle"></i>
        <?php if (`$currentLang === 'ind'): ?>
            Panduan Penting
        <?php elseif (`$currentLang === 'eng'): ?>
            Important Guidelines
        <?php else: ?>
            重要なガイドライン
        <?php endif; ?>
    </h2>
    
    <div class="alert-info-custom">
        <?php if (`$currentLang === 'ind'): ?>
            <h4>Catatan Penting:</h4>
            <ul>
                <li>Pastikan semua field wajib telah diisi dengan benar</li>
                <li>Periksa validasi data sebelum menyimpan</li>
                <li>Hubungi administrator jika mengalami kendala</li>
            </ul>
        <?php elseif (`$currentLang === 'eng'): ?>
            <h4>Important Notes:</h4>
            <ul>
                <li>Ensure all required fields are filled correctly</li>
                <li>Check data validation before saving</li>
                <li>Contact administrator if you encounter any issues</li>
            </ul>
        <?php else: ?>
            <h4>重要な注意事項：</h4>
            <ul>
                <li>すべての必須フィールドが正しく入力されていることを確認してください</li>
                <li>保存する前にデータ検証を確認してください</li>
                <li>問題が発生した場合は管理者に連絡してください</li>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- TODO: Customize this template with specific process flow for $controllerTitle -->
<!-- See src/Template/Candidates/process_flow.ctp for a complete example -->
"@
    
    # Write template file (UTF-8 without BOM)
    $utf8 = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($templateFile, $templateContent, $utf8)
    
    Write-Host "Created: $controllerName -> $templateFile" -ForegroundColor Green
    $created++
}

Write-Host ""
Write-Host "Template generation complete!" -ForegroundColor Green
Write-Host "Templates created: $created" -ForegroundColor Cyan
Write-Host "Templates skipped: $skipped" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Magenta
Write-Host "1. Test any process flow page (e.g., /candidates/process-flow)" -ForegroundColor White
Write-Host "2. Customize templates with specific workflow for each module" -ForegroundColor White
Write-Host "3. Add more detailed diagrams and database relationships" -ForegroundColor White
Write-Host "4. Translate content to Indonesian, English, and Japanese" -ForegroundColor White
