<%
/**
 * Custom Bake Template for Forms with Smart Field Detection
 * Features:
 * - Auto-detect file/image fields -> multipart form
 * - Date fields -> datePicker
 * - Email fields -> email validation
 * - Katakana/Hiragana fields -> kana.js
 * - Address fields -> custom address selector
 */
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return !in_array($schema->getColumnType($field), ['binary']);
    });

// Auto-detect if form needs multipart/form-data
$hasFileUpload = false;
foreach ($fields as $field) {
    if (preg_match('/(file|image)/i', $field)) {
        $hasFileUpload = true;
        break;
$formOptions = $hasFileUpload ? ", ['type' => 'file']" : '';
%>
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\<%= $entityClass %>|null $<%= $singularVar %>
 */
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
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List <%= $pluralHumanName %>'), ['action' => 'index'], ['escape' => false]) ?></li>
<%
    $done = [];
    foreach ($associations as $type => $data):
        foreach ($data as $alias => $details):
            if (!empty($details['navLink']) && $details['controller'] !== $this->name && !in_array($details['controller'], $done)):
                $controller = $details['controller'];
                $singularController = Inflector::humanize(Inflector::singularize($controller));
%>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List <%= $controller %>'), ['controller' => '<%= $controller %>', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New <%= $singularController %>'), ['controller' => '<%= $controller %>', 'action' => 'add'], ['escape' => false]) ?></li>
<%
                $done[] = $controller;
            endif;
        endforeach;
    endforeach;
%>
    </ul>
</nav>

<!-- Main Content -->
<div class="<%= $pluralVar %> form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' <%= $singularHumanName %>') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($<%= $singularVar %><% if ($hasFileUpload): %>, ['type' => 'file', 'data-confirm' => 'true', 'id' => '<%= $singularVar %>Form']<% else: %>, ['data-confirm' => 'true', 'id' => '<%= $singularVar %>Form']<% endif; %>) ?>
            <fieldset>
                <legend><?= __('Enter <%= $singularHumanName %> Information') ?></legend>
                <div class="row">
<%
    foreach ($fields as $field):
        if (in_array($field, $primaryKey)) {
            continue;
        
        $fieldData = $schema->getColumn($field);
        $fieldType = $fieldData['type'];
        $humanize = Inflector::humanize($field);
        
        // DATE FIELD DETECTION
        if (preg_match('/(date|tanggal)/i', $field)):
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'type' => 'text',
                            'class' => 'form-control datepicker',
                            'placeholder' => __('Select <%= $humanize %>'),
                            'label' => false,
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>
<%
        // FILE/IMAGE FIELD DETECTION
        elseif (preg_match('/(file|image|photo|attachment|gambar|foto)/i', $field)):
            $isImage = preg_match('/(image|photo|gambar|foto)/i', $field);
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'type' => 'file',
                            'class' => 'form-control',
                            'label' => false,
<% if ($isImage): %>
                            'accept' => 'image/*',
<% else: %>
                            'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar',
<% endif; %>
                        ]) ?>
                        <?php if (!empty($<%= $singularVar %>-><%= $field %>)): ?>
                            <div class="mt-2">
<% if ($isImage): %>
                                <img src="<?= $this->Url->build('/' . $<%= $singularVar %>-><%= $field %>, ['fullBase' => true]) ?>" 
                                     alt="Current <%= $humanize %>" 
                                     class="img-thumbnail" 
                                     style="max-height: 150px;">
<% else: %>
                                <a href="<?= $this->Url->build('/' . $<%= $singularVar %>-><%= $field %>, ['fullBase' => true]) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Download Current File
                                </a>
<% endif; %>
                            </div>
                        <?php endif; ?>
                    </div>
<%
        // EMAIL FIELD DETECTION
        elseif (preg_match('/(email|e_mail)/i', $field)):
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'type' => 'email',
                            'class' => 'form-control email-validate',
                            'placeholder' => __('Enter <%= $humanize %>'),
                            'label' => false
                        ]) ?>
                        <small class="text-muted">Format: user@example.com</small>
                    </div>
                    <script>
                    $(document).ready(function() {
                        $('.email-validate').on('keyup blur', function() {
                            var email = $(this).val();
                            var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                            if (email && !regex.test(email)) {
                                $(this).css('border-color', 'red');
                            } else {
                                $(this).css('border-color', '#ced4da');
                        });
                    });
                    </script>
<%
        // PASSWORD FIELD DETECTION
        elseif (preg_match('/(password|pass|pwd|kata_sandi)/i', $field)):
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'type' => 'password',
                            'class' => 'form-control',
                            'placeholder' => __('Enter <%= $humanize %>'),
                            'label' => false,
                            'autocomplete' => 'new-password'
                        ]) ?>
                        <small class="text-muted">Minimum 8 characters, include uppercase, lowercase, number & symbol</small>
                    </div>
<%
        // KATAKANA FIELD DETECTION
        elseif (preg_match('/katakana/i', $field)):
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'class' => 'form-control katakana-input',
                            'placeholder' => __('Enter <%= $humanize %> (Katakana)'),
                            'label' => false
                        ]) ?>
                    </div>
<%
        // HIRAGANA FIELD DETECTION
        elseif (preg_match('/hiragana/i', $field)):
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'class' => 'form-control hiragana-input',
                            'placeholder' => __('Enter <%= $humanize %> (Hiragana)'),
                            'label' => false
                        ]) ?>
                    </div>
<%
        // ADDRESS FIELD DETECTION (Propinsi/Province)
        elseif (preg_match('/(propinsi|province)/i', $field)):
%>
                    <div class="col-md-12 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-map-marker-alt"></i> <?= __('Address Information') ?></h5>
                                <p class="text-muted small mb-3">Select Province first, then City, District, and Village will be populated automatically</p>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label required"><?= __('Province') ?> <span class="text-danger">*</span></label>
                                        <?= $this->Form->control('propinsi_id', [
                                            'options' => isset($propinsis) ? $propinsis : [],
                                            'class' => 'form-control address-select',
                                            'id' => '<%= Inflector::classify($singularVar) %>PropinsiId',
                                            'label' => false,
                                            'empty' => __('-- Select Province --'),
                                            'required' => false
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label required"><?= __('Kabupaten/City') ?> <span class="text-danger">*</span></label>
                                        <?= $this->Form->control('kabupaten_id', [
                                            'options' => isset($kabupatens) ? $kabupatens : [],
                                            'class' => 'form-control address-select',
                                            'id' => '<%= Inflector::classify($singularVar) %>KabupatenId',
                                            'label' => false,
                                            'empty' => __('-- Select Kabupaten --'),
                                            'required' => false
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label required"><?= __('Kecamatan/District') ?> <span class="text-danger">*</span></label>
                                        <?= $this->Form->control('kecamatan_id', [
                                            'options' => isset($kecamatans) ? $kecamatans : [],
                                            'class' => 'form-control address-select',
                                            'id' => '<%= Inflector::classify($singularVar) %>KecamatanId',
                                            'label' => false,
                                            'empty' => __('-- Select Kecamatan --'),
                                            'required' => false
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label required"><?= __('Kelurahan/Village') ?> <span class="text-danger">*</span></label>
                                        <?= $this->Form->control('kelurahan_id', [
                                            'options' => isset($kelurahans) ? $kelurahans : [],
                                            'class' => 'form-control address-select',
                                            'id' => '<%= Inflector::classify($singularVar) %>KelurahanId',
                                            'label' => false,
                                            'empty' => __('-- Select Kelurahan --'),
                                            'required' => false
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="address-loading" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Loading options...
                                </div>
                            </div>
                        </div>
                    </div>
<%
        // FOREIGN KEY DETECTION
        elseif (isset($keyFields[$field])):
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'options' => $<%= $keyFields[$field] %>,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select <%= $humanize %> --')
                        ]) ?>
                    </div>
<%
        // TEXTAREA DETECTION (text/longtext type)
        elseif (in_array($fieldType, ['text', 'longtext'])):
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => __('Enter <%= $humanize %>'),
                            'label' => false
                        ]) ?>
                    </div>
<%
        // BOOLEAN/CHECKBOX DETECTION
        elseif ($fieldType === 'boolean'):
%>
                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('<%= $field %>', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('<%= $humanize %>'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
<%
        // SKIP created, modified, updated
        elseif (!in_array($field, ['created', 'modified', 'updated'])):
            // DEFAULT TEXT INPUT
%>
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><?= __('<%= $humanize %>') ?></label>
                        <?= $this->Form->control('<%= $field %>', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter <%= $humanize %>'),
                            'label' => false
                        ]) ?>
                    </div>
<%
        endif;
    endforeach;
%>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save <%= $singularHumanName %>'), [
                    'class' => 'btn btn-primary',
                    'id' => 'submitBtn'
                ]) ?>
                <?= $this->Html->link(__('Cancel'), ['action' => 'index'], [
                    'class' => 'btn btn-secondary'
                ]) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<?php $this->append('script'); ?>
<?= ->Html->script('image-preview.js') ?>
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
                $(this).after('<div class="invalid-feedback">Please enter a valid email address</div>');
        } else {
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
            $(this).after('<div class="password-strength mt-1"><small></small><div class="progress" style="height: 5px;"><div class="progress-bar"></div></div></div>');
        
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

