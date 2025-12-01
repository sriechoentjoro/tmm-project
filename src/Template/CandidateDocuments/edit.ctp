<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\CandidateDocument|null $candidateDocument */
use Cake\Utility\Inflector;

// Dynamic host detection for static assets (CORS-friendly)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$staticAssetsUrl = $protocol . '://' . $host . '/static-assets';
?>
<?= $this->Html->css('datepicker-fix.css') ?>

<!-- Mobile CSS now loaded globally from layout - mobile-responsive.css -->

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Candidate Documents'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Candidates'), ['controller' => 'Candidates', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New Candidate'), ['controller' => 'Candidates', 'action' => 'add'], ['escape' => false]) ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="candidateDocuments form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' Candidate Document') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($candidateDocument, ['type' => 'file', 'id' => 'candidateDocumentForm']) ?>
            <?php if (!empty($candidateDocument->id)): ?>
                <?= $this->Form->hidden('id') ?>
            <?php endif; ?>
            <fieldset>
                <legend><?= __('Enter Candidate Document Information') ?></legend>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Candidate') ?></label>
                        <?= $this->Form->control('candidate_id', [
                            'options' => $candidates,
                            'empty' => __('-- Select Candidate --'),
                            'class' => 'form-control',
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Title') ?></label>
                        <?= $this->Form->control('title', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Title'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('File Name') ?></label>
                        <?= $this->Form->control('file_name', [
                            'type' => 'file',
                            'class' => 'form-control',
                            'label' => false,
                            'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.jpg,.jpeg,.png,.gif,.txt,.log,.csv,.json,.xml,.md',
                            'id' => 'file-name-input',
                            'onchange' => 'previewFile(this)'
                        ]) ?>
                        
                        <!-- File Preview Container -->
                        <div id="file-preview-container" class="mt-3" style="display: none;">
                            <div class="card" style="border: 2px solid #667eea; border-radius: 8px;">
                                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 15px;">
                                    <strong><i class="fas fa-eye"></i> File Preview</strong>
                                </div>
                                <div class="card-body" style="padding: 15px;">
                                    <div id="file-preview-content" style="text-align: center;"></div>
                                    <div id="file-info" class="mt-2" style="font-size: 12px; color: #6c757d;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($candidateDocument->file_name)): ?>
                            <div class="mt-2">
                                <strong class="text-muted d-block mb-2"><i class="fas fa-file"></i> Current File:</strong>
                                <?= $this->element('file_viewer', [
                                    'filePath' => $candidateDocument->file_name,
                                    'showPreview' => false
                                ]) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Comments') ?></label>
                        <?= $this->Form->control('comments', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Comments'),
                            'label' => false
                        ]) ?>
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Candidate Document'), [
                    'class' => 'btn-export-light',
                    'id' => 'submitBtn'
                ]) ?>
                <?= $this->Html->link(__('Cancel'), ['action' => 'index'], [
                    'class' => 'btn-export-light'
                ]) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<?php $this->append('script'); ?>
<script>
// File Preview Function (Global scope - defined before jQuery ready)
window.previewFile = function(input) {
    console.log('previewFile called', input);
    console.log('Files:', input.files);
    
    const previewContainer = document.getElementById('file-preview-container');
    const previewContent = document.getElementById('file-preview-content');
    const fileInfo = document.getElementById('file-info');
    
    console.log('Elements found:', {
        previewContainer: !!previewContainer,
        previewContent: !!previewContent,
        fileInfo: !!fileInfo
    });
    
    if (!previewContainer || !previewContent || !fileInfo) {
        console.error('Required elements not found!');
        return;
    }
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024).toFixed(2); // KB
        const fileType = file.type;
        const fileExt = fileName.split('.').pop().toLowerCase();
        
        // Show preview container
        previewContainer.style.display = 'block';
        
        // Display file info
        fileInfo.innerHTML = `
            <div style="display: flex; justify-content: space-between; padding: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 10px;">
                <span><strong>Name:</strong> ${fileName}</span>
                <span><strong>Size:</strong> ${fileSize} KB</span>
                <span><strong>Type:</strong> ${fileExt.toUpperCase()}</span>
            </div>
        `;
        
        // Preview based on file type
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExt)) {
            // Image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContent.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Preview" 
                         style="max-width: 100%; max-height: 400px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                `;
            };
            reader.readAsDataURL(file);
        } else if (fileExt === 'pdf') {
            // PDF preview with PDF.js
            const reader = new FileReader();
            reader.onload = function(e) {
                const pdfUrl = e.target.result;
                previewContent.innerHTML = `
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                        <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <i class="fas fa-file-pdf" style="font-size: 48px; color: #E74C3C; vertical-align: middle;"></i>
                                <strong style="margin-left: 10px; font-size: 16px;">PDF Preview</strong>
                            </div>
                            <span style="font-size: 12px; color: #6c757d;">First page preview</span>
                        </div>
                        <canvas id="pdf-preview-canvas" style="border: 1px solid #dee2e6; border-radius: 4px; max-width: 100%; background: white;"></canvas>
                        <div id="pdf-loading" style="text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 32px;"></i>
                            <p style="margin-top: 10px;">Loading PDF preview...</p>
                        </div>
                    </div>
                `;
                
                // Load PDF.js if not already loaded
                if (typeof pdfjsLib === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                    script.onload = function() {
                        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                        renderPDF(pdfUrl);
                    };
                    document.head.appendChild(script);
                } else {
                    renderPDF(pdfUrl);
                }
                
                function renderPDF(url) {
                    const loadingDiv = document.getElementById('pdf-loading');
                    const canvas = document.getElementById('pdf-preview-canvas');
                    
                    pdfjsLib.getDocument(url).promise.then(function(pdf) {
                        pdf.getPage(1).then(function(page) {
                            const viewport = page.getViewport({ scale: 1.5 });
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            
                            page.render({
                                canvasContext: context,
                                viewport: viewport
                            }).promise.then(function() {
                                loadingDiv.style.display = 'none';
                                canvas.style.display = 'block';
                            });
                        });
                    }).catch(function(error) {
                        loadingDiv.innerHTML = '<p style="color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Could not preview PDF</p>';
                    });
                }
            };
            reader.readAsDataURL(file);
        } else if (['doc', 'docx'].includes(fileExt)) {
            previewContent.innerHTML = `
                <div style="background: #f8f9fa; padding: 30px; border-radius: 8px;">
                    <i class="fas fa-file-word" style="font-size: 64px; color: #2980B9;"></i>
                    <p style="margin-top: 15px; color: #495057;"><strong>Word Document</strong></p>
                    <p style="font-size: 12px; color: #6c757d;">Preview will be available after upload</p>
                </div>
            `;
        } else if (['xls', 'xlsx'].includes(fileExt)) {
            previewContent.innerHTML = `
                <div style="background: #f8f9fa; padding: 30px; border-radius: 8px;">
                    <i class="fas fa-file-excel" style="font-size: 64px; color: #27AE60;"></i>
                    <p style="margin-top: 15px; color: #495057;"><strong>Excel Document</strong></p>
                    <p style="font-size: 12px; color: #6c757d;">Preview will be available after upload</p>
                </div>
            `;
        } else if (['zip', 'rar'].includes(fileExt)) {
            previewContent.innerHTML = `
                <div style="background: #f8f9fa; padding: 30px; border-radius: 8px;">
                    <i class="fas fa-file-archive" style="font-size: 64px; color: #95A5A6;"></i>
                    <p style="margin-top: 15px; color: #495057;"><strong>Archive File</strong></p>
                    <p style="font-size: 12px; color: #6c757d;">Compressed file ready for upload</p>
                </div>
            `;
        } else if (['txt', 'log', 'csv', 'json', 'xml', 'md'].includes(fileExt)) {
            // Text file preview with content
            const reader = new FileReader();
            reader.onload = function(e) {
                const textContent = e.target.result;
                const preview = textContent.substring(0, 1000); // First 1000 chars
                const lineCount = textContent.split('\n').length;
                
                previewContent.innerHTML = `
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                        <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <i class="fas fa-file-alt" style="font-size: 48px; color: #7F8C8D; vertical-align: middle;"></i>
                                <strong style="margin-left: 10px; font-size: 16px;">Text File Preview</strong>
                            </div>
                            <span style="font-size: 12px; color: #6c757d;">${lineCount} lines</span>
                        </div>
                        <div style="background: white; padding: 15px; border-radius: 4px; max-height: 300px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 13px; line-height: 1.5; color: #333; white-space: pre-wrap; word-break: break-word; border: 1px solid #dee2e6;">
${preview}${textContent.length > 1000 ? '\n\n... (truncated - showing first 1000 characters)' : ''}
                        </div>
                        <div style="margin-top: 10px; text-align: center;">
                            <small style="color: #6c757d;">
                                <i class="fas fa-info-circle"></i> 
                                Full content: ${textContent.length} characters, ${lineCount} lines
                            </small>
                        </div>
                    </div>
                `;
            };
            reader.readAsText(file);
        } else {
            previewContent.innerHTML = `
                <div style="background: #f8f9fa; padding: 30px; border-radius: 8px;">
                    <i class="fas fa-file" style="font-size: 64px; color: #34495E;"></i>
                    <p style="margin-top: 15px; color: #495057;"><strong>File Selected</strong></p>
                    <p style="font-size: 12px; color: #6c757d;">Ready for upload</p>
                </div>
            `;
        }
    } else {
        previewContainer.style.display = 'none';
    }
};

// Enhanced Datepicker with easy year selection
$(document).ready(function() {
        // Initialize Bootstrap Datepicker with correct format
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',  // MySQL date format
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        container: 'body',
        showOnFocus: true,
        zIndexOffset: 1050
    });
    
    // Fix datepicker CSS conflicts
    $('.datepicker-dropdown').css({
        'z-index': '1060',
        'display': 'block'
    });
    
    // Ensure datepicker opens below input
    $('.datepicker').on('show', function(e) {
        $('.datepicker-dropdown').css({
            'top': $(this).offset().top + $(this).outerHeight(),
            'left': $(this).offset().left
        });
    });
    
    // Auto-uppercase for text inputs (except email, password, url)
    $('input[type="text"], textarea').not('[type="email"], [type="password"], [type="url"], .no-uppercase, .datepicker').on('input', function() {
        var start = this.selectionStart;
        var end = this.selectionEnd;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(start, end);
    });
    
    // Email validation enhancement
    $('input[type="email"]').on('blur', function() {
        var email = $(this).val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Please enter a valid email address</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    // Password strength indicator
    $('input[type="password"]').on('input', function() {
        var password = $(this).val();
        var strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        
        var strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        var strengthColor = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997'];
        
        if (!$(this).next('.password-strength').length) {
            $(this).after('<div class="password-strength mt-1"><small></small><div class="progress" style="height: 5px;"><div class="progress-bar"></div></div></div>');
        }
        
        var strengthDiv = $(this).next('.password-strength');
        strengthDiv.find('small').text(strengthText[strength - 1] || '').css('color', strengthColor[strength - 1] || '#6c757d');
        strengthDiv.find('.progress-bar').css({
            'width': (strength * 20) + '%',
            'background-color': strengthColor[strength - 1] || '#6c757d'
        });
    });
});
</script>
<?php $this->end(); ?>


<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
