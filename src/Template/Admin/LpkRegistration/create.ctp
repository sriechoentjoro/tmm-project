<?php
/**
 * LPK Registration - Step 1: Create Registration
 * Admin creates new LPK record and system sends verification email
 */
?>
<div class="lpk-registration-create">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap"></i> <?= __('Register New LPK (Vocational Training Institution)') ?>
                    </h3>
                    <div class="card-tools">
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('Back to List'),
                            ['action' => 'index'],
                            ['class' => 'btn btn-secondary btn-sm', 'escape' => false]
                        ) ?>
                    </div>
                </div>

                <?= $this->Form->create($institution, ['type' => 'file', 'id' => 'lpkRegistrationForm']) ?>
                <div class="card-body">
                    
                    <!-- Alert Box -->
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong><?= __('Registration Process:') ?></strong><br>
                        1. Fill in LPK information below<br>
                        2. System will send verification email to the provided email address<br>
                        3. LPK director clicks verification link (valid for 24 hours)<br>
                        4. LPK sets password to activate account
                    </div>

                    <!-- Basic Information Section -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-building"></i> <?= __('Basic Information') ?>
                            </h4>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Institution Name') ?> 
                                            <span class="text-danger">*</span>
                                            <small class="text-muted">(Max 256 characters)</small>
                                        </label>
                                        <?= $this->Form->control('name', [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter LPK Name (e.g., LPK Karya Mandiri)'),
                                            'label' => false,
                                            'required' => true,
                                            'maxlength' => 256
                                        ]) ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Full official name of the vocational training institution
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Registration Number') ?> 
                                            <span class="text-danger">*</span>
                                            <small class="text-muted">(Max 50 characters)</small>
                                        </label>
                                        <?= $this->Form->control('registration_number', [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter Registration Number'),
                                            'label' => false,
                                            'required' => true,
                                            'maxlength' => 50
                                        ]) ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Official registration number from government authority
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('License Number') ?>
                                            <small class="text-muted">(Optional)</small>
                                        </label>
                                        <?= $this->Form->control('license_number', [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter License Number'),
                                            'label' => false,
                                            'maxlength' => 50
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('License Expiry Date') ?>
                                            <small class="text-muted">(Format: YYYY-MM-DD)</small>
                                        </label>
                                        <?= $this->Form->control('license_expiry_date', [
                                            'type' => 'date',
                                            'class' => 'form-control',
                                            'label' => false
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Establishment Date') ?>
                                            <small class="text-muted">(Format: YYYY-MM-DD)</small>
                                        </label>
                                        <?= $this->Form->control('establishment_date', [
                                            'type' => 'date',
                                            'class' => 'form-control',
                                            'label' => false
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-address-card"></i> <?= __('Contact Information') ?>
                            </h4>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Director Name') ?> 
                                            <span class="text-danger">*</span>
                                            <small class="text-muted">(Max 256 characters)</small>
                                        </label>
                                        <?= $this->Form->control('director_name', [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter Director Full Name'),
                                            'label' => false,
                                            'required' => true,
                                            'maxlength' => 256
                                        ]) ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-user"></i> Full name of the institution director
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Email Address') ?> 
                                            <span class="text-danger">*</span>
                                            <small class="text-muted">(Valid email format)</small>
                                        </label>
                                        <?= $this->Form->control('email', [
                                            'type' => 'email',
                                            'class' => 'form-control',
                                            'placeholder' => __('director@lpk-example.com'),
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'lpk-email'
                                        ]) ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-envelope"></i> Verification email will be sent to this address
                                        </small>
                                        <div id="email-check-result"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Phone Number') ?>
                                            <small class="text-muted">(Optional)</small>
                                        </label>
                                        <?= $this->Form->control('phone', [
                                            'type' => 'tel',
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter Phone Number'),
                                            'label' => false,
                                            'maxlength' => 20
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Website') ?>
                                            <small class="text-muted">(Optional)</small>
                                        </label>
                                        <?= $this->Form->control('website', [
                                            'type' => 'url',
                                            'class' => 'form-control',
                                            'placeholder' => __('https://www.lpk-example.com'),
                                            'label' => false,
                                            'maxlength' => 256
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Address') ?> 
                                            <span class="text-danger">*</span>
                                            <small class="text-muted">(Max 500 characters)</small>
                                        </label>
                                        <?= $this->Form->control('address', [
                                            'type' => 'textarea',
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter Complete Address'),
                                            'label' => false,
                                            'required' => true,
                                            'rows' => 3,
                                            'maxlength' => 500
                                        ]) ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-home"></i> Complete address including street name, number, RT/RW
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Geographic Location Section -->
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-map-marked-alt"></i> <?= __('Geographic Location') ?>
                            </h4>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Province') ?>
                                            <small class="text-muted">(Optional)</small>
                                        </label>
                                        <?= $this->Form->control('master_propinsi_id', [
                                            'options' => $masterPropinsis,
                                            'empty' => __('-- Select Province --'),
                                            'class' => 'form-control',
                                            'label' => false,
                                            'id' => 'master-propinsi-id'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('City/District') ?>
                                            <small class="text-muted">(Optional)</small>
                                        </label>
                                        <?= $this->Form->control('master_kabupaten_id', [
                                            'options' => $masterKabupatens,
                                            'empty' => __('-- Select City/District --'),
                                            'class' => 'form-control',
                                            'label' => false,
                                            'id' => 'master-kabupaten-id'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Subdistrict') ?>
                                            <small class="text-muted">(Optional)</small>
                                        </label>
                                        <?= $this->Form->control('master_kecamatan_id', [
                                            'options' => $masterKecamatans,
                                            'empty' => __('-- Select Subdistrict --'),
                                            'class' => 'form-control',
                                            'label' => false,
                                            'id' => 'master-kecamatan-id'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Village') ?>
                                            <small class="text-muted">(Optional)</small>
                                        </label>
                                        <?= $this->Form->control('master_kelurahan_id', [
                                            'options' => $masterKelurahans,
                                            'empty' => __('-- Select Village --'),
                                            'class' => 'form-control',
                                            'label' => false,
                                            'id' => 'master-kelurahan-id'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <?= __('Postal Code') ?>
                                            <small class="text-muted">(5 digits)</small>
                                        </label>
                                        <?= $this->Form->control('postal_code', [
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'placeholder' => __('e.g., 12345'),
                                            'label' => false,
                                            'maxlength' => 5,
                                            'pattern' => '[0-9]{5}'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Html->link(
                                '<i class="fas fa-times"></i> ' . __('Cancel'),
                                ['action' => 'index'],
                                ['class' => 'btn btn-secondary', 'escape' => false]
                            ) ?>
                        </div>
                        <div class="col-md-6 text-right">
                            <?= $this->Form->button(
                                '<i class="fas fa-paper-plane"></i> ' . __('Register LPK & Send Verification Email'),
                                [
                                    'class' => 'btn btn-primary btn-lg',
                                    'escape' => false,
                                    'id' => 'submit-btn'
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<!-- Cascade Dropdown JavaScript -->
<script>
$(document).ready(function() {
    // Cascade dropdown data from controller
    var kabupatensData = <?= json_encode($masterKabupatensData) ?>;
    var kecamatansData = <?= json_encode($masterKecamatansData) ?>;
    var kelurahansData = <?= json_encode($masterKelurahansData) ?>;
    
    // Province change - filter cities
    $('#master-propinsi-id').on('change', function() {
        var propinsiId = $(this).val();
        var kabupatenSelect = $('#master-kabupaten-id');
        var kecamatanSelect = $('#master-kecamatan-id');
        var kelurahanSelect = $('#master-kelurahan-id');
        
        // Clear and reset dependent dropdowns
        kabupatenSelect.empty().append('<option value="">-- Select City/District --</option>');
        kecamatanSelect.empty().append('<option value="">-- Select Subdistrict --</option>');
        kelurahanSelect.empty().append('<option value="">-- Select Village --</option>');
        
        if (propinsiId) {
            // Populate cities for selected province
            kabupatensData.forEach(function(kab) {
                if (kab.propinsi_id == propinsiId) {
                    kabupatenSelect.append('<option value="' + kab.id + '">' + kab.title + '</option>');
                }
            });
        }
    });
    
    // City change - filter subdistricts
    $('#master-kabupaten-id').on('change', function() {
        var kabupatenId = $(this).val();
        var kecamatanSelect = $('#master-kecamatan-id');
        var kelurahanSelect = $('#master-kelurahan-id');
        
        // Clear and reset dependent dropdowns
        kecamatanSelect.empty().append('<option value="">-- Select Subdistrict --</option>');
        kelurahanSelect.empty().append('<option value="">-- Select Village --</option>');
        
        if (kabupatenId) {
            // Populate subdistricts for selected city
            kecamatansData.forEach(function(kec) {
                if (kec.kabupaten_id == kabupatenId) {
                    kecamatanSelect.append('<option value="' + kec.id + '">' + kec.title + '</option>');
                }
            });
        }
    });
    
    // Subdistrict change - filter villages
    $('#master-kecamatan-id').on('change', function() {
        var kecamatanId = $(this).val();
        var kelurahanSelect = $('#master-kelurahan-id');
        
        // Clear dropdown
        kelurahanSelect.empty().append('<option value="">-- Select Village --</option>');
        
        if (kecamatanId) {
            // Populate villages for selected subdistrict
            kelurahansData.forEach(function(kel) {
                if (kel.kecamatan_id == kecamatanId) {
                    kelurahanSelect.append('<option value="' + kel.id + '">' + kel.title + '</option>');
                }
            });
        }
    });
    
    // Real-time email validation
    var emailCheckTimeout;
    $('#lpk-email').on('keyup', function() {
        clearTimeout(emailCheckTimeout);
        var email = $(this).val();
        var resultDiv = $('#email-check-result');
        
        if (email.length > 0 && email.includes('@')) {
            emailCheckTimeout = setTimeout(function() {
                resultDiv.html('<small class="text-muted"><i class="fas fa-spinner fa-spin"></i> Checking email...</small>');
                
                // TODO: AJAX call to check if email exists
                // For now, just show validation message
                setTimeout(function() {
                    resultDiv.html('<small class="text-success"><i class="fas fa-check-circle"></i> Email format is valid</small>');
                }, 500);
            }, 500);
        } else {
            resultDiv.html('');
        }
    });
    
    // Form submission confirmation
    $('#lpkRegistrationForm').on('submit', function(e) {
        var btn = $('#submit-btn');
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Sending verification email...');
    });
});
</script>

<style>
.card-header {
    background-color: #f4f6f9;
}
.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.text-danger {
    font-weight: bold;
}
.card-tools .btn {
    padding: 0.25rem 0.5rem;
}
</style>
