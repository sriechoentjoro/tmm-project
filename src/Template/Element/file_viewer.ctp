<?php
/**
 * File Viewer Element - Simplified Version (No Modal)
 * Display file with icon and inline preview only
 * 
 * @var string $filePath - Path to the file
 * @var string $label - Optional label (default: filename)
 * @var bool $showPreview - Show inline preview (for view templates, default: false)
 * @var string $editUrl - Optional edit URL for upload link
 */

// CRITICAL: If filePath is not provided or empty, don't display anything
if (!isset($filePath) || empty($filePath) || trim($filePath) === '') {
    return;
}

// Default values
$label = isset($label) ? $label : basename($filePath);
$showPreview = isset($showPreview) ? $showPreview : false;

// Get file extension
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

// File extension to icon mapping (SVG paths)
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

// Build full path and check accessibility
$fullPath = WWW_ROOT . $filePath;
$fileExists = (file_exists($fullPath) && is_readable($fullPath));
?>

<style>
    @keyframes blink-file-error {
        0%, 50%, 100% { opacity: 1; }
        25%, 75% { opacity: 0.3; }
    .file-error-blink {
        animation: blink-file-error 1.5s infinite;
    .file-not-found-compact {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 4px;
        color: #856404;
        font-size: 13px;
    .file-not-found-compact .icon {
        color: #E74C3C;
        flex-shrink: 0;
    .file-not-found-compact .edit-link {
        color: #0969da;
        text-decoration: underline;
        font-weight: 500;
        margin-left: 4px;
    .file-not-found-compact .edit-link:hover {
        color: #0550ae;
    .file-icon-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #495057;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
    .file-icon-link:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        text-decoration: none;
</style>

<?php if (!$fileExists): ?>
    <!-- File not found - Show compact warning -->
    <div class="file-not-found-compact">
        <span class="icon file-error-blink">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </span>
        <span>
            <?php 
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
                echo $isImage ? __('Image not found') : __('File not found');
            ?>
        </span>
        <?php if (isset($editUrl) && !empty($editUrl)): ?>
            <a href="<?= $editUrl ?>" class="edit-link">
                <?= __('Upload') ?>
            </a>
        <?php endif; ?>
    </div>
<?php elseif ($showPreview): ?>
    <!-- Show inline preview (for view templates) -->
    <div class="file-preview-inline" style="border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden; margin: 10px 0; background: #f8f9fa;">
        <div class="file-preview-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="color: white;"><?= $icon ?></span>
                <strong style="font-size: 14px;"><?= h($label) ?></strong>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="<?= $fileUrl ?>" 
                   target="_blank" 
                   style="background: rgba(255,255,255,0.2); color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    Open
                </a>
                <a href="<?= $fileUrl ?>" 
                   download 
                   style="background: rgba(255,255,255,0.2); color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download
                </a>
            </div>
        </div>
        <div class="file-preview-body" style="padding: 0; height: 300px; overflow-y: auto; overflow-x: auto; background: #fff;">
            <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
                <div style="padding: 10px; text-align: center; background: #f8f9fa; min-height: 300px; display: flex; align-items: center; justify-content: center;">
                    <img src="<?= $fileUrl ?>" 
                         alt="<?= h($label) ?>" 
                         style="max-width: 100%; max-height: 280px; height: auto; display: block; border-radius: 4px;">
                </div>
            <?php elseif ($canPreview): ?>
                <iframe src="<?= $fileUrl ?>" 
                        style="width: 100%; height: 100%; border: none; display: block;" 
                        frameborder="0">
                </iframe>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; color: #6c757d;">
                    <div style="font-size: 48px; margin-bottom: 15px; color: <?= $color ?>;">
                        <?= $icon ?>
                    </div>
                    <p style="margin: 10px 0; font-size: 14px;">
                        <strong><?= h($label) ?></strong>
                    </p>
                    <p style="margin: 5px 0; font-size: 13px; color: #999;">
                        File type: .<?= strtoupper($ext) ?>
                    </p>
                    <div style="margin-top: 20px;">
                        <a href="<?= $fileUrl ?>" 
                           download 
                           style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-size: 14px; display: inline-block;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Download <?= strtoupper($ext) ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <!-- Show icon only (for index templates) -->
    <a href="<?= $fileUrl ?>" 
       target="_blank" 
       class="file-icon-link" 
       title="<?= h($label) ?>">
        <span style="color: <?= $color ?>; display: flex;">
            <?= $icon ?>
        </span>
        <span><?= h($label) ?></span>
    </a>
<?php endif; ?>

