<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\VocationalTrainingInstitution|null $vocationalTrainingInstitution */
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
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Vocational Training Institutions'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterPropinsis'), ['controller' => 'MasterPropinsis', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterPropinsi'), ['controller' => 'MasterPropinsis', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKabupatens'), ['controller' => 'MasterKabupatens', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterKabupaten'), ['controller' => 'MasterKabupatens', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKecamatans'), ['controller' => 'MasterKecamatans', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterKecamatan'), ['controller' => 'MasterKecamatans', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKelurahans'), ['controller' => 'MasterKelurahans', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterKelurahan'), ['controller' => 'MasterKelurahans', 'action' => 'add'], ['escape' => false]) ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="vocationalTrainingInstitutions form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' Vocational Training Institution') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($vocationalTrainingInstitution, ['type' => 'file', 'data-confirm' => 'true', 'id' => 'vocationalTrainingInstitutionForm']) ?>
            <fieldset>
                <legend><?= __('Enter Vocational Training Institution Information') ?></legend>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_special_skill_support_institution', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Special Skill Support Institution'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Abbreviation') ?>
                            <small class="text-muted">(Max 50 characters)</small>
                        </label>
                        <?= $this->Form->control('abbreviation', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Abbreviation'),
                            'label' => false,
                            'maxlength' => 50
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Short code or abbreviation for the institution.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Name') ?>
                            <span class="text-danger">*</span>
                            <small class="text-muted">(Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('name', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Name'),
                            'label' => false,
                            'required' => true,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-building"></i> Full official name of the vocational training institution.
                        </small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-map-marker-alt"></i> <?= __('Address Information') ?></h5>
                                <p class="text-muted small mb-3">Select Province first, then City, District, and Village will be populated automatically</p>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label"><?= __('Province') ?></label>
                                        <?= $this->Form->control('master_propinsi_id', [
                                            'options' => isset($masterPropinsis) ? $masterPropinsis : [],
                                            'class' => 'form-control address-select',
                                            'id' => 'VocationalTrainingInstitutionPropinsiId',
                                            'label' => false,
                                            'empty' => __('-- Select Province --'),
                                            'required' => false
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label"><?= __('Kabupaten/City') ?></label>
                                        <?= $this->Form->control('master_kabupaten_id', [
                                            'options' => isset($masterKabupatens) ? $masterKabupatens : [],
                                            'class' => 'form-control address-select',
                                            'id' => 'VocationalTrainingInstitutionKabupatenId',
                                            'label' => false,
                                            'empty' => __('-- Select Kabupaten --'),
                                            'required' => false
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label"><?= __('Kecamatan/District') ?></label>
                                        <?= $this->Form->control('master_kecamatan_id', [
                                            'options' => isset($masterKecamatans) ? $masterKecamatans : [],
                                            'class' => 'form-control address-select',
                                            'id' => 'VocationalTrainingInstitutionKecamatanId',
                                            'label' => false,
                                            'empty' => __('-- Select Kecamatan --'),
                                            'required' => false
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label"><?= __('Kelurahan/Village') ?></label>
                                        <?= $this->Form->control('master_kelurahan_id', [
                                            'options' => isset($masterKelurahans) ? $masterKelurahans : [],
                                            'class' => 'form-control address-select',
                                            'id' => 'VocationalTrainingInstitutionKelurahanId',
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
                        <label class="form-label">
                            <?= __('Address') ?>
                            <small class="text-muted">(Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('address', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Address'),
                            'label' => false,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-map-marked-alt"></i> Full street address including building name/number.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Post Code') ?>
                            <small class="text-muted">(Max 10 characters)</small>
                        </label>
                        <?= $this->Form->control('post_code', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Post Code'),
                            'label' => false,
                            'maxlength' => 10
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-mail-bulk"></i> Postal code for the address.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Director') ?>
                            <small class="text-muted">(Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('director', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Director Name'),
                            'label' => false,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-user-tie"></i> Full name of the institution's director/head.
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Director Katakana') ?>
                            <small class="text-muted">(Optional - Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('director_katakana', [
                            'class' => 'form-control katakana-input',
                            'placeholder' => __('ã‚«ã‚¿ã‚«ãƒŠã§åå‰ã‚’å…¥åŠ›ã—ã¦ãã ã•ã„'),
                            'label' => false,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Enter director's name in Katakana (ã‚«ã‚¿ã‚«ãƒŠ). Example: ãƒ¤ãƒžãƒ€ ãƒãƒŠã‚³
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Mou File') ?></label>
                        <?= $this->Form->control('mou_file', [
                            'type' => 'file',
                            'class' => 'form-control',
                            'label' => false,
                            'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar',
                        ]) ?>
                        <?php if (!empty($vocationalTrainingInstitution->mou_file)): ?>
                            <div class="mt-2">
                                <a href="<?= $this->Url->build('/' . $vocationalTrainingInstitution->mou_file, ['fullBase' => true]) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Download Current File
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Vocational Training Institution'), [
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
    const APP_BASE_URL = "<?= $this->Url->build('/') ?>";
</script>
<?= $this->Html->script('address-cascade') ?>
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
    $('input[type="text"], textarea').not('[type="email"], [type="password"], [type="url"], .datepicker, .no-uppercase').on('input', function() {
        var start = this.selectionStart;
        var end = this.selectionEnd;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(start, end);
    });
});
</script>
<?php $this->end(); ?>

