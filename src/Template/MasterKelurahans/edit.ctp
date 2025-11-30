<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\MasterKelurahan|null $masterKelurahan */
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
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Master Kelurahans'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterPropinsis'), ['controller' => 'MasterPropinsis', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterPropinsi'), ['controller' => 'MasterPropinsis', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKabupatens'), ['controller' => 'MasterKabupatens', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterKabupaten'), ['controller' => 'MasterKabupatens', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKecamatans'), ['controller' => 'MasterKecamatans', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterKecamatan'), ['controller' => 'MasterKecamatans', 'action' => 'add'], ['escape' => false]) ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="masterKelurahans form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' Master Kelurahan') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($masterKelurahan, ['data-confirm' => 'true', 'id' => 'masterKelurahanForm']) ?>
            <?php if (!empty($masterKelurahan->id)): ?>
                <?= $this->Form->hidden('id') ?>
            <?php endif; ?>
            <fieldset>
                <legend><?= __('Enter Master Kelurahan Information') ?></legend>
                <div class="row">
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
                                            'id' => 'MasterKelurahanPropinsiId',
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
                                            'id' => 'MasterKelurahanKabupatenId',
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
                                            'id' => 'MasterKelurahanKecamatanId',
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
                                            'id' => 'MasterKelurahanKelurahanId',
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
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Kabupaten Id') ?></label>
                        <?= $this->Form->control('kabupaten_id', [
                            'options' => $masterKabupatens,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Kabupaten Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Kecamatan Id') ?></label>
                        <?= $this->Form->control('kecamatan_id', [
                            'options' => $masterKecamatans,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Kecamatan Id --')
                        ]) ?>
                    </div>
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
                                            'id' => 'MasterKelurahanPropinsiId',
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
                                            'id' => 'MasterKelurahanKabupatenId',
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
                                            'id' => 'MasterKelurahanKecamatanId',
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
                                            'id' => 'MasterKelurahanKelurahanId',
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
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Kode Kabupaten') ?></label>
                        <?= $this->Form->control('kode_kabupaten', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Kode Kabupaten'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Kode Kecamatan') ?></label>
                        <?= $this->Form->control('kode_kecamatan', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Kode Kecamatan'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Kode Kelurahan') ?></label>
                        <?= $this->Form->control('kode_kelurahan', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Kode Kelurahan'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Kode Pos') ?></label>
                        <?= $this->Form->control('kode_pos', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Kode Pos'),
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
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Master Kelurahan'), [
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

