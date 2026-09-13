<?php
/**
 * Process Flow Documentation - Candidate Experiences
 * Multi-language support: Indonesian, English, Japanese
 */

// Get current language from session (same as layout)
$currentLang = $this->request->getSession()->read('Config.language') ?: 'ind';
?>
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


<!-- Process Overview Section -->
<div class="flow-section">
    <h2>
        <i class="fas fa-clipboard-list"></i> 
        <?php if ($currentLang === 'ind'): ?>
            Ringkasan Proses
        <?php elseif ($currentLang === 'eng'): ?>
            Process Overview
        <?php else: ?>
            プロセス概要
        <?php endif; ?>
    </h2>
    
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        <?php if ($currentLang === 'ind'): ?>
            <strong>Candidate Experiences</strong> adalah modul untuk mengelola data terkait Candidate Experiences dalam sistem TMM.
        <?php elseif ($currentLang === 'eng'): ?>
            <strong>Candidate Experiences</strong> is a module for managing Candidate Experiences data in the TMM system.
        <?php else: ?>
            <strong>Candidate Experiences</strong>は、TMMシステムでCandidate Experiences データを管理するモジュールです。
        <?php endif; ?>
    </div>
    
    <!-- Workflow Steps -->
    <div class="workflow-steps">
        <div class="workflow-step">
            <span class="step-number">1</span>
            <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                <div class="step-title">
                    <?php if ($currentLang === 'ind'): ?>
                        Tahap 1: Input Data
                    <?php elseif ($currentLang === 'eng'): ?>
                        Step 1: Data Input
                    <?php else: ?>
                        ステップ1：データ入力
                    <?php endif; ?>
                    <span class="database-indicator">candidateexperiences</span>
                </div>
                <div class="step-description">
                    <strong>
                        <?php if ($currentLang === 'ind'): ?>
                            Siapa
                        <?php elseif ($currentLang === 'eng'): ?>
                            Who
                        <?php else: ?>
                            誰が
                        <?php endif; ?>:
                    </strong> 
                    <?php if ($currentLang === 'ind'): ?>
                        Administrator atau Operator
                    <?php elseif ($currentLang === 'eng'): ?>
                        Administrator or Operator
                    <?php else: ?>
                        管理者またはオペレーター
                    <?php endif; ?>
                    <br>
                    
                    <strong>
                        <?php if ($currentLang === 'ind'): ?>
                            Aksi
                        <?php elseif ($currentLang === 'eng'): ?>
                            Action
                        <?php else: ?>
                            アクション
                        <?php endif; ?>:
                    </strong>
                    <?php if ($currentLang === 'ind'): ?>
                        Mengisi formulir data Candidate Experiences
                    <?php elseif ($currentLang === 'eng'): ?>
                        Fill out Candidate Experiences data form
                    <?php else: ?>
                        Candidate Experiences データフォームに記入する
                    <?php endif; ?>
                    <br>
                    
                    <strong>
                        <?php if ($currentLang === 'ind'): ?>
                            Hasil
                        <?php elseif ($currentLang === 'eng'): ?>
                            Result
                        <?php else: ?>
                            結果
                        <?php endif; ?>:
                    </strong>
                    <?php if ($currentLang === 'ind'): ?>
                        Data tersimpan ke database
                    <?php elseif ($currentLang === 'eng'): ?>
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
        <?php if ($currentLang === 'ind'): ?>
            Diagram Alur Visual
        <?php elseif ($currentLang === 'eng'): ?>
            Visual Process Flow
        <?php else: ?>
            ビジュアルフロー図
        <?php endif; ?>
    </h2>
    
    <div class="mermaid">
graph TD
    A[<?php echo $currentLang === 'ind' ? 'Input Data' : ($currentLang === 'eng' ? 'Data Input' : 'データ入力'); ?>] --> B[<?php echo $currentLang === 'ind' ? 'Validasi' : ($currentLang === 'eng' ? 'Validation' : '検証'); ?>]
    B --> C{<?php echo $currentLang === 'ind' ? 'Valid?' : ($currentLang === 'eng' ? 'Valid?' : '有効？'); ?>}
    C -->|<?php echo $currentLang === 'ind' ? 'Ya' : ($currentLang === 'eng' ? 'Yes' : 'はい'); ?>| D[<?php echo $currentLang === 'ind' ? 'Simpan ke Database' : ($currentLang === 'eng' ? 'Save to Database' : 'データベースに保存'); ?>]
    C -->|<?php echo $currentLang === 'ind' ? 'Tidak' : ($currentLang === 'eng' ? 'No' : 'いいえ'); ?>| A
    D --> E[<?php echo $currentLang === 'ind' ? 'Selesai' : ($currentLang === 'eng' ? 'Done' : '完了'); ?>]
    
    style A fill:#e3f2fd
    style E fill:#c8e6c9
    </div>
</div>

<!-- Important Guidelines -->
<div class="flow-section">
    <h2>
        <i class="fas fa-exclamation-triangle"></i>
        <?php if ($currentLang === 'ind'): ?>
            Panduan Penting
        <?php elseif ($currentLang === 'eng'): ?>
            Important Guidelines
        <?php else: ?>
            重要なガイドライン
        <?php endif; ?>
    </h2>
    
    <div class="alert-info-custom">
        <?php if ($currentLang === 'ind'): ?>
            <h4>Catatan Penting:</h4>
            <ul>
                <li>Pastikan semua field wajib telah diisi dengan benar</li>
                <li>Periksa validasi data sebelum menyimpan</li>
                <li>Hubungi administrator jika mengalami kendala</li>
            </ul>
        <?php elseif ($currentLang === 'eng'): ?>
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

<!-- TODO: Customize this template with specific process flow for Candidate Experiences -->
<!-- See src/Template/Candidates/process_flow.ctp for a complete example -->

</div><!-- End .content-wrapper -->
