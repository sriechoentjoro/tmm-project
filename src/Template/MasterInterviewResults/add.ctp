<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\MasterInterviewResult|null $masterInterviewResult */
use Cake\Utility\Inflector;
?>
<?= $this->Html->css('datepicker-fix.css') ?>
<?= $this->Html->script('form-confirm.js') ?>

<!-- Mobile CSS now loaded globally from layout - mobile-responsive.css -->

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Master Interview Results'), ['action' => 'index'], ['escape' => false]) ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="masterInterviewResults form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' Master Interview Result') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($masterInterviewResult, ['data-confirm' => 'true', 'id' => 'masterInterviewResultForm']) ?>
            <fieldset>
                <legend><?= __('Enter Master Interview Result Information') ?></legend>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Title Jpg') ?>
                            <small class="text-muted">(Optional - Image filename - Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('title_jpg', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter image filename (e.g., result_icon.jpg)'),
                            'label' => false,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-image"></i> Optional image filename associated with this interview result.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Details') ?>
                            <small class="text-muted">(Optional)</small>
                        </label>
                        <?= $this->Form->control('details', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => __('Enter detailed description of this interview result'),
                            'label' => false
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-align-left"></i> Provide additional details or criteria for this interview result category.
                        </small>
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Master Interview Result'), [
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
    
    // Auto-uppercase for text inputs (except email, password, url)
    $('input[type="text"], textarea').not('[type="email"], [type="password"], [type="url"], .no-uppercase, .datepicker').on('input', function() {
        var start = this.selectionStart;
        var end = this.selectionEnd;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(start, end);
    });
    
    // CRITICAL: Prevent form submit on Enter key (except textarea)
    $('#masterInterviewResultForm').on('keydown', function(e) {
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
    $('#masterInterviewResultForm').on('submit', function(e) {
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
                $('#masterInterviewResultForm').find('input, select, textarea, button').prop('disabled', false);
                $('.form-overlay').remove();
        }, 10000);
    });
    
    // Re-enable form if user navigates back
    $(window).on('pageshow', function(event) {
        if (event.originalEvent.persisted) {
            isSubmitting = false;
            $('#submitBtn').prop('disabled', false).text('<?= __('Save Master Interview Result') ?>').css('opacity', '1');
            $('#masterInterviewResultForm').find('input, select, textarea, button').prop('disabled', false);
            $('.form-overlay').remove();
    });
});
</script>
<?php $this->end(); ?>

