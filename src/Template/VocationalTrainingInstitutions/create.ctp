<?php
/**
 * Create a new Vocational Training Institution.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VocationalTrainingInstitution $vocationalTrainingInstitution
 * @var \Cake\Collection\CollectionInterface $masterPropinsis
 * @var \Cake\Collection\CollectionInterface $masterKabupatens
 * @var \Cake\Collection\CollectionInterface $masterKecamatans
 * @var \Cake\Collection\CollectionInterface $masterKelurahans
 */

// Dynamic host detection for static assets (CORS-friendly)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$staticAssetsUrl = $protocol . '://' . $host . '/static-assets';
?>
<?= $this->Html->script('form-confirm.js?v=2.0') ?>

<style>
/* Sectioned create form -------------------------------------------------- */
.vti-create .form-section {
    border: 1px solid #d0d7de;
    border-radius: 8px;
    margin-bottom: 18px;
    background: #fff;
    overflow: hidden;
}
.vti-create .form-section > .section-head {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #f6f8fa;
    border-bottom: 1px solid #d0d7de;
    font-weight: 600;
    font-size: 14px;
    color: #24292f;
}
.vti-create .form-section > .section-head .step {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #0969da;
    color: #fff;
    font-size: 12px;
    flex: 0 0 auto;
}
.vti-create .form-section > .section-body { padding: 16px; }
.vti-create .form-label { font-weight: 600; font-size: 13px; margin-bottom: 4px; }
.vti-create .req { color: #cf222e; font-weight: 700; }
.vti-create .hint { display: block; margin-top: 4px; color: #57606a; font-size: 12px; }
.vti-create .required-note {
    margin: 0 0 16px;
    padding: 8px 12px;
    border-left: 3px solid #0969da;
    background: #ddf4ff;
    border-radius: 4px;
    font-size: 13px;
    color: #24292f;
}
</style>

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Vocational Training Institutions'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterPropinsis'), ['controller' => 'MasterPropinsis', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKabupatens'), ['controller' => 'MasterKabupatens', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKecamatans'), ['controller' => 'MasterKecamatans', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKelurahans'), ['controller' => 'MasterKelurahans', 'action' => 'index'], ['escape' => false]) ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="vocationalTrainingInstitutions form content vti-create">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-building"></i>
                <?= __('New Vocational Training Institution') ?>
            </h3>
        </div>

        <div class="card-body">
            <p class="required-note">
                <i class="fas fa-info-circle"></i>
                <?= __('Fields marked with {0} are required. After saving, a registration link is generated and emailed to the address below.', '<span class="req">*</span>') ?>
            </p>

            <?= $this->Form->create($vocationalTrainingInstitution, [
                'type' => 'file',
                'data-confirm' => 'true',
                'id' => 'vocationalTrainingInstitutionForm'
            ]) ?>

            <!-- 1. Institution details ------------------------------------ -->
            <div class="form-section">
                <div class="section-head">
                    <span class="step">1</span> <i class="fas fa-building"></i> <?= __('Institution Details') ?>
                </div>
                <div class="section-body">
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
                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="name">
                                <?= __('Name') ?> <span class="req">*</span>
                            </label>
                            <?= $this->Form->control('name', [
                                'class' => 'form-control',
                                'placeholder' => __('Enter the full official name'),
                                'label' => false,
                                'required' => true,
                                'maxlength' => 256
                            ]) ?>
                            <small class="hint">
                                <i class="fas fa-building"></i> <?= __('Full official name of the institution.') ?>
                            </small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="abbreviation"><?= __('Abbreviation') ?></label>
                            <?= $this->Form->control('abbreviation', [
                                'class' => 'form-control',
                                'placeholder' => __('e.g. LPK ABC'),
                                'label' => false,
                                'maxlength' => 11
                            ]) ?>
                            <small class="hint">
                                <i class="fas fa-info-circle"></i> <?= __('Short code, max 11 characters.') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Address ------------------------------------------------ -->
            <div class="form-section">
                <div class="section-head">
                    <span class="step">2</span> <i class="fas fa-map-marker-alt"></i> <?= __('Address') ?>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-3">
                        <?= __('Select Province first — City, District and Village are populated automatically.') ?>
                    </p>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label"><?= __('Province') ?> <span class="req">*</span></label>
                            <?= $this->Form->control('master_propinsi_id', [
                                'options' => isset($masterPropinsis) ? $masterPropinsis : [],
                                'class' => 'form-control address-select',
                                'id' => 'VocationalTrainingInstitutionPropinsiId',
                                'label' => false,
                                'empty' => __('-- Select Province --'),
                                'required' => false
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label"><?= __('Kabupaten/City') ?> <span class="req">*</span></label>
                            <?= $this->Form->control('master_kabupaten_id', [
                                'options' => isset($masterKabupatens) ? $masterKabupatens : [],
                                'class' => 'form-control address-select',
                                'id' => 'VocationalTrainingInstitutionKabupatenId',
                                'label' => false,
                                'empty' => __('-- Select Kabupaten --'),
                                'required' => false
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label"><?= __('Kecamatan/District') ?> <span class="req">*</span></label>
                            <?= $this->Form->control('master_kecamatan_id', [
                                'options' => isset($masterKecamatans) ? $masterKecamatans : [],
                                'class' => 'form-control address-select',
                                'id' => 'VocationalTrainingInstitutionKecamatanId',
                                'label' => false,
                                'empty' => __('-- Select Kecamatan --'),
                                'required' => false
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label"><?= __('Kelurahan/Village') ?> <span class="req">*</span></label>
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
                        <i class="fas fa-spinner fa-spin"></i> <?= __('Loading options...') ?>
                    </div>
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label class="form-label" for="address"><?= __('Street Address') ?> <span class="req">*</span></label>
                            <?= $this->Form->control('address', [
                                'class' => 'form-control',
                                'placeholder' => __('Building name/number, street'),
                                'label' => false,
                                'required' => true,
                                'maxlength' => 256
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="post-code"><?= __('Post Code') ?></label>
                            <?= $this->Form->control('post_code', [
                                'class' => 'form-control',
                                'placeholder' => __('e.g. 123456'),
                                'label' => false,
                                'maxlength' => 6
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Director ----------------------------------------------- -->
            <div class="form-section">
                <div class="section-head">
                    <span class="step">3</span> <i class="fas fa-user-tie"></i> <?= __('Director') ?>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="director"><?= __('Director Name') ?></label>
                            <?= $this->Form->control('director', [
                                'class' => 'form-control',
                                'placeholder' => __('Full name of the director'),
                                'label' => false,
                                'maxlength' => 256
                            ]) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="director-katakana"><?= __('Director Name (Katakana)') ?></label>
                            <?= $this->Form->control('director_katakana', [
                                'class' => 'form-control katakana-input no-uppercase',
                                'placeholder' => 'ヤマダ ハナコ',
                                'label' => false,
                                'maxlength' => 256
                            ]) ?>
                            <small class="hint">
                                <i class="fas fa-info-circle"></i> <?= __('Katakana only. Example: ヤマダ ハナコ') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Login account ------------------------------------------ -->
            <div class="form-section">
                <div class="section-head">
                    <span class="step">4</span> <i class="fas fa-user-lock"></i> <?= __('Login Account') ?>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-3">
                        <?= __('The institution uses these to complete its registration. The password is set by the institution itself via the emailed link.') ?>
                    </p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="email"><?= __('Email') ?> <span class="req">*</span></label>
                            <?= $this->Form->control('email', [
                                'type' => 'email',
                                'class' => 'form-control',
                                'placeholder' => __('institution@example.com'),
                                'label' => false,
                                'required' => true,
                                'maxlength' => 100
                            ]) ?>
                            <small class="hint">
                                <i class="fas fa-envelope"></i> <?= __('The registration link is sent here. Must be unique.') ?>
                            </small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="username"><?= __('Username') ?> <span class="req">*</span></label>
                            <?= $this->Form->control('username', [
                                'class' => 'form-control no-uppercase',
                                'placeholder' => __('e.g. lpkabc2026'),
                                'label' => false,
                                'required' => true,
                                'maxlength' => 50,
                                'pattern' => '[A-Za-z0-9]+',
                                'title' => __('Letters and numbers only, no spaces or symbols.')
                            ]) ?>
                            <small class="hint">
                                <i class="fas fa-user"></i> <?= __('Letters and numbers only. Must be unique.') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Documents ---------------------------------------------- -->
            <div class="form-section">
                <div class="section-head">
                    <span class="step">5</span> <i class="fas fa-file-signature"></i> <?= __('Documents') ?>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="mou-file"><?= __('MoU File') ?> <span class="req">*</span></label>
                            <?= $this->Form->control('mou_file', [
                                'type' => 'file',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar',
                            ]) ?>
                            <small class="hint">
                                <i class="fas fa-paperclip"></i> <?= __('Accepted: PDF, Word, Excel, ZIP, RAR.') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Create Institution'), [
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
// Date pickers are initialised globally by webroot/js/datepicker-init.js,
// so this template does not bind them itself.
$(document).ready(function () {
    // Auto-uppercase for plain text inputs. Email, password, url, date pickers
    // and anything marked .no-uppercase are excluded - uppercasing a username
    // or katakana field would corrupt it.
    $('input[type="text"], textarea')
        .not('[type="email"], [type="password"], [type="url"], .datepicker, .no-uppercase')
        .on('input', function () {
            var start = this.selectionStart;
            var end = this.selectionEnd;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(start, end);
        });
});
</script>
<?php $this->end(); ?>

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
