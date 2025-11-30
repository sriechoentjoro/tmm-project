<?php
/**
 * File Viewer Element - Modal Version
 * Display file with icon and modal preview popup
 * 
 * @var string $filePath - Path to the file
 * @var string $label - Optional label (default: filename)
 * @var string $fieldName - Field name for unique modal ID
 */

// CRITICAL: If filePath is not provided or empty, don't display anything
if (!isset($filePath) || empty($filePath) || trim($filePath) === '') {
    return;
}

// Default values
$label = isset($label) ? $label : basename($filePath);
$fieldName = isset($fieldName) ? $fieldName : 'file_' . uniqid();
$modalId = 'fileModal_' . $fieldName;

// Get file extension
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

// File extension to icon mapping
$iconMap = [
    'pdf' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M10 12h4"></path><path d="M10 16h4"></path></svg>',
    'doc' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M16 13H8"></path><path d="M16 17H8"></path><path d="M10 9H8"></path></svg>',
    'docx' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M16 13H8"></path><path d="M16 17H8"></path><path d="M10 9H8"></path></svg>',
    'xls' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><line x1="12" y1="13" x2="12" y2="17"></line></svg>',
    'xlsx' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><line x1="12" y1="13" x2="12" y2="17"></line></svg>',
    'jpg' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
    'jpeg' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
    'png' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
    'gif' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
    'zip' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>',
    'rar' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>',
    'txt' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>',
    'default' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>'
];

// Color mapping for file types
$colorMap = [
    'pdf' => '#E74C3C',
    'doc' => '#2980B9',
    'docx' => '#2980B9',
    'xls' => '#27AE60',
    'xlsx' => '#27AE60',
    'jpg' => '#9B59B6',
    'jpeg' => '#9B59B6',
    'png' => '#9B59B6',
    'gif' => '#9B59B6',
    'zip' => '#95A5A6',
    'rar' => '#95A5A6',
    'txt' => '#7F8C8D',
    'default' => '#34495E'
];

$icon = isset($iconMap[$ext]) ? $iconMap[$ext] : $iconMap['default'];
$color = isset($colorMap[$ext]) ? $colorMap[$ext] : $colorMap['default'];

// Check if file can be previewed in iframe
$previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt', 'html'];
$canPreview = in_array($ext, $previewableTypes);

// Build file URL
$fileUrl = $this->Url->build('/' . $filePath, ['fullBase' => false]);

// Check if file exists
$fullPath = WWW_ROOT . $filePath;
$fileExists = (file_exists($fullPath) && is_readable($fullPath));
?>

<style>
    .file-modal-trigger {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #495057;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .file-modal-trigger:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .file-viewer-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.75);
        overflow: auto;
    }
    .file-viewer-modal.show {
        display: block;
    }
    .file-modal-content {
        position: relative;
        background-color: #fefefe;
        margin: 2% auto;
        padding: 0;
        width: 90%;
        max-width: 1200px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        animation: modalSlideIn 0.3s ease-out;
    }
    @keyframes modalSlideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    .file-modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .file-modal-header h3 {
        margin: 0;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .file-modal-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        padding: 0 10px;
        border-radius: 4px;
        transition: all 0.2s;
        line-height: 1;
    }
    .file-modal-close:hover {
        background: rgba(255,255,255,0.3);
    }
    .file-modal-body {
        padding: 20px;
        min-height: 500px;
        max-height: 70vh;
        overflow-y: auto;
    }
    .file-modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8f9fa;
        border-radius: 0 0 8px 8px;
    }
    .file-modal-footer .btn {
        padding: 8px 16px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .file-modal-footer .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .file-modal-footer .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
    }
    .file-modal-footer .btn-secondary {
        background: #6c757d;
        color: white;
    }
    .file-modal-footer .btn-secondary:hover {
        background: #5a6268;
    }
    .file-preview-container {
        text-align: center;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 4px;
        padding: 20px;
    }
    .file-preview-container img {
        max-width: 100%;
        max-height: 500px;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .file-preview-container iframe {
        width: 100%;
        height: 500px;
        border: none;
        border-radius: 4px;
    }
</style>

<?php if ($fileExists): ?>
    <!-- Trigger Button -->
    <button type="button" 
            class="file-modal-trigger" 
            onclick="openFileModal('<?= $modalId ?>')"
            title="Click to preview <?= h($label) ?>">
        <span style="color: <?= $color ?>; display: flex;">
            <?= $icon ?>
        </span>
        <span><?= h($label) ?></span>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
        </svg>
    </button>

    <!-- Modal -->
    <div id="<?= $modalId ?>" class="file-viewer-modal">
        <div class="file-modal-content">
            <div class="file-modal-header">
                <h3>
                    <span style="color: white; display: flex;">
                        <?= $icon ?>
                    </span>
                    <?= h($label) ?>
                </h3>
                <button class="file-modal-close" onclick="closeFileModal('<?= $modalId ?>')">&times;</button>
            </div>
            <div class="file-modal-body">
                <div class="file-preview-container">
                    <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
                        <img src="<?= $fileUrl ?>" alt="<?= h($label) ?>">
                    <?php elseif ($canPreview): ?>
                        <iframe src="<?= $fileUrl ?>"></iframe>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px;">
                            <div style="font-size: 64px; margin-bottom: 20px; color: <?= $color ?>;">
                                <?= $icon ?>
                            </div>
                            <p style="font-size: 16px; margin: 10px 0;">
                                <strong><?= h($label) ?></strong>
                            </p>
                            <p style="color: #999; margin: 5px 0;">
                                File type: .<?= strtoupper($ext) ?>
                            </p>
                            <p style="color: #6c757d; margin: 15px 0;">
                                This file type cannot be previewed in the browser.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="file-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeFileModal('<?= $modalId ?>')">
                    Close
                </button>
                <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    Open in New Tab
                </a>
                <a href="<?= $fileUrl ?>" download class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download
                </a>
            </div>
        </div>
    </div>

    <script>
    function openFileModal(modalId) {
        document.getElementById(modalId).classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeFileModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('file-viewer-modal')) {
            closeFileModal(event.target.id);
        }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModals = document.querySelectorAll('.file-viewer-modal.show');
            openModals.forEach(function(modal) {
                closeFileModal(modal.id);
            });
        }
    });
    </script>
<?php else: ?>
    <span style="color: #dc3545; font-style: italic;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        File not found
    </span>
<?php endif; ?>
