<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\Training|null $training */
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
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Trainings'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Form->postLink(
                '<i class="fas fa-trash"></i> ' . __('Delete'),
                ['action' => 'delete', $training->id],
                ['escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $training->id)]
            )
        ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="trainings form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' Training') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($training, ['data-confirm' => 'true', 'id' => 'trainingForm']) ?>
            <fieldset>
                <legend><?= __('Enter Training Information') ?></legend>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Title') ?> 
                            <span class="text-danger">*</span>
                            <small class="text-muted">(Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('title', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Training Title'),
                            'label' => false,
                            'required' => true,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Enter a descriptive title for this training program.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Description') ?>
                            <small class="text-muted">(Optional)</small>
                        </label>
                        <?= $this->Form->control('description', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => __('Enter detailed description of the training program'),
                            'label' => false
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-align-left"></i> Provide details about the training content, objectives, and requirements.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Start Date') ?>
                            <small class="text-muted">(Optional - Format: YYYY-MM-DD)</small>
                        </label>
                        <?= $this->Form->control('start_date', [
                            'type' => 'text',
                            'class' => 'form-control datepicker',
                            'placeholder' => __('Select Start Date (YYYY-MM-DD)'),
                            'label' => false,
                            'autocomplete' => 'off',
                            'value' => !empty($training->start_date) ? $training->start_date->format('Y-m-d') : ''
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-calendar-alt"></i> Click to select date from calendar. Format: Year-Month-Day (e.g., 2024-01-15)
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('End Date') ?>
                            <small class="text-muted">(Optional - Format: YYYY-MM-DD)</small>
                        </label>
                        <?= $this->Form->control('end_date', [
                            'type' => 'text',
                            'class' => 'form-control datepicker',
                            'placeholder' => __('Select End Date (YYYY-MM-DD)'),
                            'label' => false,
                            'autocomplete' => 'off',
                            'value' => !empty($training->end_date) ? $training->end_date->format('Y-m-d') : ''
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-calendar-alt"></i> Click to select date from calendar. Format: Year-Month-Day (e.g., 2024-03-15)
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Location') ?>
                            <small class="text-muted">(Optional - Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('location', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter training location (e.g., Training Center Jakarta)'),
                            'label' => false,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-map-marker-alt"></i> Specify the physical location or venue where the training will be held.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Instructor') ?>
                            <small class="text-muted">(Optional - Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('instructor', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter instructor name'),
                            'label' => false,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-chalkboard-teacher"></i> Name of the instructor or trainer conducting this training.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Max Participants') ?>
                            <small class="text-muted">(Optional - Number only)</small>
                        </label>
                        <?= $this->Form->control('max_participants', [
                            'type' => 'number',
                            'class' => 'form-control',
                            'placeholder' => __('Enter maximum number of participants'),
                            'label' => false,
                            'min' => 1
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-users"></i> Maximum number of participants allowed in this training session.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Status') ?>
                            <small class="text-muted">(Optional - Max 50 characters)</small>
                        </label>
                        <?= $this->Form->control('status', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter status (e.g., Planned, Ongoing, Completed)'),
                            'label' => false,
                            'maxlength' => 50
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-flag"></i> Current status of the training (e.g., Planned, Ongoing, Completed, Cancelled).
                        </small>
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Training'), [
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
    $('#trainingForm').on('keydown', function(e) {
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
    $('#trainingForm').on('submit', function(e) {
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
                $('#trainingForm').find('input, select, textarea, button').prop('disabled', false);
                $('.form-overlay').remove();
        }, 10000);
    });
    
    // Re-enable form if user navigates back
    $(window).on('pageshow', function(event) {
        if (event.originalEvent.persisted) {
            isSubmitting = false;
            $('#submitBtn').prop('disabled', false).text('<?= __('Save Training') ?>').css('opacity', '1');
            $('#trainingForm').find('input, select, textarea, button').prop('disabled', false);
            $('.form-overlay').remove();
    });
});
</script>
<?php $this->end(); ?>

