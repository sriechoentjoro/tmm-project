<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\ApprenticeOrder|null $apprenticeOrder */
use Cake\Utility\Inflector;

// Dynamic host detection for static assets (CORS-friendly)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$staticAssetsUrl = $protocol . '://' . $host . '/static-assets';
?>
<!-- Load CSS/JS from static location (workaround for .htaccess issue) -->
<?= $this->Html->script('form-confirm.js?v=2.0') ?>
<?= $this->Html->css('datepicker-fix.css') ?>

<!-- Mobile CSS now loaded globally from layout - mobile-responsive.css -->

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Apprentice Orders'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List CooperativeAssociations'), ['controller' => 'CooperativeAssociations', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New CooperativeAssociation'), ['controller' => 'CooperativeAssociations', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List AcceptanceOrganizations'), ['controller' => 'AcceptanceOrganizations', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New AcceptanceOrganization'), ['controller' => 'AcceptanceOrganizations', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterJobCategories'), ['controller' => 'MasterJobCategories', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterJobCategory'), ['controller' => 'MasterJobCategories', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Apprentices'), ['controller' => 'Apprentices', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New Apprentice'), ['controller' => 'Apprentices', 'action' => 'add'], ['escape' => false]) ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="apprenticeOrders form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' Apprentice Order') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($apprenticeOrder, ['type' => 'file', 'data-confirm' => 'true', 'id' => 'apprenticeOrderForm']) ?>
            <fieldset>
                <legend><?= __('Enter Apprentice Order Information') ?></legend>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Reference File') ?></label>
                        <?= $this->Form->control('reference_file', [
                            'type' => 'file',
                            'class' => 'form-control',
                            'label' => false,
                            'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar',
                        ]) ?>
                        <?php if (!empty($apprenticeOrder->reference_file)): ?>
                            <div class="mt-2">
                                <a href="<?= $this->Url->build('/' . $apprenticeOrder->reference_file, ['fullBase' => true]) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Download Current File
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Cooperative Association Id') ?></label>
                        <?= $this->Form->control('cooperative_association_id', [
                            'options' => $cooperativeAssociations,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Cooperative Association Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Acceptance Organization Id') ?></label>
                        <?= $this->Form->control('acceptance_organization_id', [
                            'options' => $acceptanceOrganizations,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Acceptance Organization Id --')
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
                        <label class="form-label"><?= __('Male Trainee Number') ?></label>
                        <?= $this->Form->control('male_trainee_number', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Male Trainee Number'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Female Trainee Number') ?></label>
                        <?= $this->Form->control('female_trainee_number', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Female Trainee Number'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Job Category Id') ?></label>
                        <?= $this->Form->control('job_category_id', [
                            'options' => $masterJobCategories,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Job Category Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Departure Year') ?></label>
                        <?= $this->Form->control('departure_year', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Departure Year'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Departure Month') ?></label>
                        <?= $this->Form->control('departure_month', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Departure Month'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_practical_test_required', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Practical Test Required'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Other Requirements') ?></label>
                        <?= $this->Form->control('other_requirements', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Other Requirements'),
                            'label' => false
                        ]) ?>
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Apprentice Order'), [
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
    $('input[type="text"], textarea').not('[type="email"], [type="password"], [type="url"], .datepicker, .no-uppercase').on('input', function() {
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
    
    // CRITICAL: Prevent form submit on Enter key (except textarea)
    $('#apprenticeOrderForm').on('keydown', function(e) {
        // Allow Enter in textarea for multiline input
        if (e.target.tagName.toLowerCase() === 'textarea') {
            return true;
        
        // Prevent Enter key from submitting form
        if (e.keyCode === 13 || e.which === 13) {
            e.preventDefault();
            
            // Move to next input field instead
            var inputs = $(this).find('input, select, textarea').not('[type="hidden"]');
            var currentIndex = inputs.index(e.target);
            if (currentIndex < inputs.length - 1) {
                inputs.eq(currentIndex + 1).focus();
            
            return false;
    });
    
    // CRITICAL: Prevent double-submit and show loading state
    var isSubmitting = false;
    $('#apprenticeOrderForm').on('submit', function(e) {
        // Check if already submitting
        if (isSubmitting) {
            e.preventDefault();
            return false;
        
        // Validate required fields
        var hasError = false;
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                hasError = true;
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">This field is required</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
        });
        
        if (hasError) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        
        // Set submitting flag
        isSubmitting = true;
        
        // Disable submit button and show loading
        var $submitBtn = $('#submitBtn');
        var originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true)
                  .html('<i class="fas fa-spinner fa-spin"></i> Saving...')
                  .css('opacity', '0.6');
        
        // Disable all form inputs to prevent changes
        $(this).find('input, select, textarea, button').not('#submitBtn').prop('disabled', true);
        
        // Show overlay to prevent clicking
        if (!$('.form-overlay').length) {
            $('body').append('<div class="form-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><div class="mt-2">Saving data, please wait...</div></div></div>');
        
        // If form validation fails or submission takes too long, re-enable after 10 seconds
        setTimeout(function() {
            if (isSubmitting) {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).text(originalText).css('opacity', '1');
                $('#apprenticeOrderForm').find('input, select, textarea, button').prop('disabled', false);
                $('.form-overlay').remove();
        }, 10000);
    });
    
    // Re-enable form if user navigates back
    $(window).on('pageshow', function(event) {
        if (event.originalEvent.persisted) {
            isSubmitting = false;
            $('#submitBtn').prop('disabled', false).text('<?= __('Save Apprentice Order') ?>').css('opacity', '1');
            $('#apprenticeOrderForm').find('input, select, textarea, button').prop('disabled', false);
            $('.form-overlay').remove();
    });
});
</script>
<?php $this->end(); ?>

