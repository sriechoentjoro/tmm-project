<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\ApprenticeDocumentManagementDashboard|null $apprenticeDocumentManagementDashboard */
use Cake\Utility\Inflector;

// Dynamic host detection for static assets (CORS-friendly)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$staticAssetsUrl = $protocol . '://' . $host . '/static-assets';
?>
<!-- Load CSS/JS from static location (workaround for .htaccess issue) -->
<script src="<?= $staticAssetsUrl ?>/js/form-confirm.js?v=2.0"></script>
<?= $this->Html->css('datepicker-fix.css') ?>

<!-- Mobile CSS now loaded globally from layout - mobile-responsive.css -->

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Apprentice Document Management Dashboards'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Candidates'), ['controller' => 'Candidates', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New Candidate'), ['controller' => 'Candidates', 'action' => 'add'], ['escape' => false]) ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="apprenticeDocumentManagementDashboards form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' Apprentice Document Management Dashboard') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($apprenticeDocumentManagementDashboard, ['data-confirm' => 'true', 'id' => 'apprenticeDocumentManagementDashboardForm']) ?>
            <fieldset>
                <legend><?= __('Enter Apprentice Document Management Dashboard Information') ?></legend>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Candidate Id') ?></label>
                        <?= $this->Form->control('candidate_id', [
                            'type' => 'text',
                            'class' => 'form-control datepicker',
                            'placeholder' => __('Select Candidate Id'),
                            'label' => false,
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Total Documents') ?></label>
                        <?= $this->Form->control('total_documents', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Total Documents'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Total Ready') ?></label>
                        <?= $this->Form->control('total_ready', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Total Ready'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Total Pending') ?></label>
                        <?= $this->Form->control('total_pending', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Total Pending'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Total Missing') ?></label>
                        <?= $this->Form->control('total_missing', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Total Missing'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Last Updated') ?></label>
                        <?= $this->Form->control('last_updated', [
                            'type' => 'text',
                            'class' => 'form-control datepicker',
                            'placeholder' => __('Select Last Updated'),
                            'label' => false,
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Apprentice Document Management Dashboard'), [
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
<script>
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
            }, 1);
    });
    
    // Auto-uppercase for text inputs (except email, password, url)
    $('input[type="text"], textarea').not('[type="email"], [type="password"], [type="url"], .no-uppercase', .datepicker).on('input', function() {
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
                $(this).after('<div class="invalid-feedback">Please enter a valid email address</div>');`n            }`n        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
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
            $(this).after('<div class="password-strength mt-1"><small></small><div class="progress" style="height: 5px;"><div class="progress-bar"></div></div></div>');`n        }`n        `n        var strengthDiv = $(this).next('.password-strength');
        strengthDiv.find('small').text(strengthText[strength - 1] || '').css('color', strengthColor[strength - 1] || '#6c757d');
        strengthDiv.find('.progress-bar').css({
            'width': (strength * 20) + '%',
            'background-color': strengthColor[strength - 1] || '#6c757d'
        });
    });
});
</script>
<?php $this->end(); ?>

