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
<!-- Load CSS/JS from static location (workaround for .htaccess issue) -->
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
                        <?php if (!empty($candidateDocument->file_name)): ?>
                            <div class="mt-2">
                                <a href="<?= $this->Url->build('/' . $candidateDocument->file_name, ['fullBase' => true]) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Download Current File
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- File Preview Container -->
                    <div id="file-preview-container" class="col-12 mb-3" style="display: none;">
                        <div class="card" style="border: 2px solid #667eea;">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <i class="fas fa-eye"></i> File Preview
                            </div>
                            <div class="card-body">
                                <div id="file-preview-content"></div>
                                <div id="file-info" class="mt-2" style="font-size: 0.9em; color: #666;"></div>
                            </div>
                        </div>
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
<?= $this->Html->script('image-preview.js') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
// Set PDF.js worker
if (typeof pdfjsLib !== 'undefined') {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
}

// File preview function
window.previewFile = function(input) {
    console.log('previewFile called', input);
    const previewContainer = document.getElementById('file-preview-container');
    const previewContent = document.getElementById('file-preview-content');
    const fileInfo = document.getElementById('file-info');
    
    console.log('Elements found:', {
        previewContainer: previewContainer,
        previewContent: previewContent,
        fileInfo: fileInfo
    });
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024).toFixed(2); // KB
        const fileExt = fileName.split('.').pop().toLowerCase();
        
        console.log('File selected:', { fileName, fileSize, fileExt });
        
        // Show preview container
        previewContainer.style.display = 'block';
        
        // Update file info
        fileInfo.innerHTML = `<strong>File:</strong> ${fileName} | <strong>Size:</strong> ${fileSize} KB | <strong>Type:</strong> ${fileExt.toUpperCase()}`;
        
        const reader = new FileReader();
        
        // Image preview
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExt)) {
            reader.onload = function(e) {
                previewContent.innerHTML = `
                    <div style="text-align: center;">
                        <img src="${e.target.result}" style="max-width: 100%; max-height: 500px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
        // PDF preview
        else if (fileExt === 'pdf') {
            reader.onload = function(e) {
                const loadingTask = pdfjsLib.getDocument({data: e.target.result});
                
                previewContent.innerHTML = `
                    <div style="text-align: center;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading PDF...</span>
                        </div>
                        <p class="mt-2">Rendering first page...</p>
                    </div>
                `;
                
                loadingTask.promise.then(function(pdf) {
                    pdf.getPage(1).then(function(page) {
                        const scale = 1.5;
                        const viewport = page.getViewport({scale: scale});
                        
                        const canvas = document.createElement('canvas');
                        canvas.id = 'pdf-preview-canvas';
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.style.border = '1px solid #ddd';
                        canvas.style.borderRadius = '4px';
                        
                        previewContent.innerHTML = '';
                        previewContent.appendChild(canvas);
                        
                        const renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        
                        page.render(renderContext);
                        
                        fileInfo.innerHTML += ` | <strong>Pages:</strong> ${pdf.numPages}`;
                    });
                }).catch(function(error) {
                    previewContent.innerHTML = `<div class="alert alert-danger">Error loading PDF: ${error.message}</div>`;
                });
            };
            reader.readAsArrayBuffer(file);
        }
        // Text file preview
        else if (['txt', 'log', 'csv', 'json', 'xml', 'md'].includes(fileExt)) {
            reader.onload = function(e) {
                const textContent = e.target.result;
                const lines = textContent.split('\n').length;
                const preview = textContent.substring(0, 1000);
                const truncated = textContent.length > 1000 ? '... (truncated)' : '';
                
                previewContent.innerHTML = `
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; max-height: 400px; overflow-y: auto;">
                        <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word; font-size: 0.85em;">${preview}${truncated}</pre>
                    </div>
                `;
                
                fileInfo.innerHTML += ` | <strong>Lines:</strong> ~${lines} | <strong>Characters:</strong> ${textContent.length}`;
            };
            reader.readAsText(file);
        }
        // Other files (show icon only)
        else {
            let icon = 'fa-file';
            let color = '#6c757d';
            
            if (['doc', 'docx'].includes(fileExt)) {
                icon = 'fa-file-word';
                color = '#2b579a';
            } else if (['xls', 'xlsx'].includes(fileExt)) {
                icon = 'fa-file-excel';
                color = '#217346';
            } else if (['zip', 'rar', '7z'].includes(fileExt)) {
                icon = 'fa-file-archive';
                color = '#f39c12';
            }
            
            previewContent.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <i class="fas ${icon}" style="font-size: 80px; color: ${color};"></i>
                    <p class="mt-3">Preview not available for this file type</p>
                    <p class="text-muted">File will be uploaded when you save</p>
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

