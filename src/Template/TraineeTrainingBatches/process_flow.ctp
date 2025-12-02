<?php
/**
 * Process Flow Documentation - Trainee Training Batches
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
            ðŸ‡®ðŸ‡© Indonesian
        </a>
        <a href="?lang=eng" class="lang-btn <?= $currentLang === 'eng' ? 'active' : '' ?>">
            ðŸ‡¬ðŸ‡§ English
        </a>
        <a href="?lang=jpn" class="lang-btn <?= $currentLang === 'jpn' ? 'active' : '' ?>">
            ðŸ‡¯ðŸ‡µ æ—¥æœ¬èªž
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
            ãƒ—ãƒ­ã‚»ã‚¹æ¦‚è¦
        <?php endif; ?>
    </h2>
    
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        <?php if ($currentLang === 'ind'): ?>
            <strong>Trainee Training Batches</strong> adalah modul untuk mengelola data terkait Trainee Training Batches dalam sistem TMM.
        <?php elseif ($currentLang === 'eng'): ?>
            <strong>Trainee Training Batches</strong> is a module for managing Trainee Training Batches data in the TMM system.
        <?php else: ?>
            <strong>Trainee Training Batches</strong>ã¯ã€TMMã‚·ã‚¹ãƒ†ãƒ ã§Trainee Training Batches ãƒ‡ãƒ¼ã‚¿ã‚’ç®¡ç†ã™ã‚‹ãƒ¢ã‚¸ãƒ¥ãƒ¼ãƒ«ã§ã™ã€‚
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
                        ã‚¹ãƒ†ãƒƒãƒ—1ï¼šãƒ‡ãƒ¼ã‚¿å…¥åŠ›
                    <?php endif; ?>
                    <span class="database-indicator">traineetrainingbatches</span>
                </div>
                <div class="step-description">
                    <strong>
                        <?php if ($currentLang === 'ind'): ?>
                            Siapa
                        <?php elseif ($currentLang === 'eng'): ?>
                            Who
                        <?php else: ?>
                            èª°ãŒ
                        <?php endif; ?>:
                    </strong> 
                    <?php if ($currentLang === 'ind'): ?>
                        Administrator atau Operator
                    <?php elseif ($currentLang === 'eng'): ?>
                        Administrator or Operator
                    <?php else: ?>
                        ç®¡ç†è€…ã¾ãŸã¯ã‚ªãƒšãƒ¬ãƒ¼ã‚¿ãƒ¼
                    <?php endif; ?>
                    <br>
                    
                    <strong>
                        <?php if ($currentLang === 'ind'): ?>
                            Aksi
                        <?php elseif ($currentLang === 'eng'): ?>
                            Action
                        <?php else: ?>
                            ã‚¢ã‚¯ã‚·ãƒ§ãƒ³
                        <?php endif; ?>:
                    </strong>
                    <?php if ($currentLang === 'ind'): ?>
                        Mengisi formulir data Trainee Training Batches
                    <?php elseif ($currentLang === 'eng'): ?>
                        Fill out Trainee Training Batches data form
                    <?php else: ?>
                        Trainee Training Batches ãƒ‡ãƒ¼ã‚¿ãƒ•ã‚©ãƒ¼ãƒ ã«è¨˜å…¥ã™ã‚‹
                    <?php endif; ?>
                    <br>
                    
                    <strong>
                        <?php if ($currentLang === 'ind'): ?>
                            Hasil
                        <?php elseif ($currentLang === 'eng'): ?>
                            Result
                        <?php else: ?>
                            çµæžœ
                        <?php endif; ?>:
                    </strong>
                    <?php if ($currentLang === 'ind'): ?>
                        Data tersimpan ke database
                    <?php elseif ($currentLang === 'eng'): ?>
                        Data saved to database
                    <?php else: ?>
                        ãƒ‡ãƒ¼ã‚¿ãŒãƒ‡ãƒ¼ã‚¿ãƒ™ãƒ¼ã‚¹ã«ä¿å­˜ã•ã‚Œã‚‹
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
            ãƒ“ã‚¸ãƒ¥ã‚¢ãƒ«ãƒ•ãƒ­ãƒ¼å›³
        <?php endif; ?>
    </h2>
    
    <div class="mermaid">
graph TD
    A[<?php echo $currentLang === 'ind' ? 'Input Data' : ($currentLang === 'eng' ? 'Data Input' : 'ãƒ‡ãƒ¼ã‚¿å…¥åŠ›'); ?>] --> B[<?php echo $currentLang === 'ind' ? 'Validasi' : ($currentLang === 'eng' ? 'Validation' : 'æ¤œè¨¼'); ?>]
    B --> C{<?php echo $currentLang === 'ind' ? 'Valid?' : ($currentLang === 'eng' ? 'Valid?' : 'æœ‰åŠ¹ï¼Ÿ'); ?>}
    C -->|<?php echo $currentLang === 'ind' ? 'Ya' : ($currentLang === 'eng' ? 'Yes' : 'ã¯ã„'); ?>| D[<?php echo $currentLang === 'ind' ? 'Simpan ke Database' : ($currentLang === 'eng' ? 'Save to Database' : 'ãƒ‡ãƒ¼ã‚¿ãƒ™ãƒ¼ã‚¹ã«ä¿å­˜'); ?>]
    C -->|<?php echo $currentLang === 'ind' ? 'Tidak' : ($currentLang === 'eng' ? 'No' : 'ã„ã„ãˆ'); ?>| A
    D --> E[<?php echo $currentLang === 'ind' ? 'Selesai' : ($currentLang === 'eng' ? 'Done' : 'å®Œäº†'); ?>]
    
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
            é‡è¦ãªã‚¬ã‚¤ãƒ‰ãƒ©ã‚¤ãƒ³
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
            <h4>é‡è¦ãªæ³¨æ„äº‹é …ï¼š</h4>
            <ul>
                <li>ã™ã¹ã¦ã®å¿…é ˆãƒ•ã‚£ãƒ¼ãƒ«ãƒ‰ãŒæ­£ã—ãå…¥åŠ›ã•ã‚Œã¦ã„ã‚‹ã“ã¨ã‚’ç¢ºèªã—ã¦ãã ã•ã„</li>
                <li>ä¿å­˜ã™ã‚‹å‰ã«ãƒ‡ãƒ¼ã‚¿æ¤œè¨¼ã‚’ç¢ºèªã—ã¦ãã ã•ã„</li>
                <li>å•é¡ŒãŒç™ºç”Ÿã—ãŸå ´åˆã¯ç®¡ç†è€…ã«é€£çµ¡ã—ã¦ãã ã•ã„</li>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- TODO: Customize this template with specific process flow for Trainee Training Batches -->
<!-- See src/Template/Candidates/process_flow.ctp for a complete example -->

</div><!-- End .content-wrapper -->
