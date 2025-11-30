<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\Trainee|null $trainee */
use Cake\Utility\Inflector;

?>
<!-- Load CSS/JS -->
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
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Trainees'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Candidates'), ['controller' => 'Candidates', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New Candidate'), ['controller' => 'Candidates', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List ApprenticeOrders'), ['controller' => 'ApprenticeOrders', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New ApprenticeOrder'), ['controller' => 'ApprenticeOrders', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List VocationalTrainingInstitutions'), ['controller' => 'VocationalTrainingInstitutions', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New VocationalTrainingInstitution'), ['controller' => 'VocationalTrainingInstitutions', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List AcceptanceOrganizations'), ['controller' => 'AcceptanceOrganizations', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New AcceptanceOrganization'), ['controller' => 'AcceptanceOrganizations', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterGenders'), ['controller' => 'MasterGenders', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterGender'), ['controller' => 'MasterGenders', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterReligions'), ['controller' => 'MasterReligions', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterReligion'), ['controller' => 'MasterReligions', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterMarriageStatuses'), ['controller' => 'MasterMarriageStatuses', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterMarriageStatus'), ['controller' => 'MasterMarriageStatuses', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterPropinsis'), ['controller' => 'MasterPropinsis', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterPropinsi'), ['controller' => 'MasterPropinsis', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKabupatens'), ['controller' => 'MasterKabupatens', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterKabupaten'), ['controller' => 'MasterKabupatens', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKecamatans'), ['controller' => 'MasterKecamatans', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterKecamatan'), ['controller' => 'MasterKecamatans', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterKelurahans'), ['controller' => 'MasterKelurahans', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterKelurahan'), ['controller' => 'MasterKelurahans', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterBloodTypes'), ['controller' => 'MasterBloodTypes', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterBloodType'), ['controller' => 'MasterBloodTypes', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List MasterRejectedReasons'), ['controller' => 'MasterRejectedReasons', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New MasterRejectedReason'), ['controller' => 'MasterRejectedReasons', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List TraineeCertifications'), ['controller' => 'TraineeCertifications', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New TraineeCertification'), ['controller' => 'TraineeCertifications', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List TraineeCourses'), ['controller' => 'TraineeCourses', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New TraineeCourse'), ['controller' => 'TraineeCourses', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List TraineeEducations'), ['controller' => 'TraineeEducations', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New TraineeEducation'), ['controller' => 'TraineeEducations', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List TraineeExperiences'), ['controller' => 'TraineeExperiences', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New TraineeExperience'), ['controller' => 'TraineeExperiences', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List TraineeFamilies'), ['controller' => 'TraineeFamilies', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New TraineeFamily'), ['controller' => 'TraineeFamilies', 'action' => 'add'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List TraineeFamilyStories'), ['controller' => 'TraineeFamilyStories', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New TraineeFamilyStory'), ['controller' => 'TraineeFamilyStories', 'action' => 'add'], ['escape' => false]) ?></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="trainees form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' Trainee') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($trainee, ['type' => 'file', 'data-confirm' => 'true', 'id' => 'traineeForm']) ?>
            <?php if (!empty($trainee->id)): ?>
                <?= $this->Form->hidden('id') ?>
            <?php endif; ?>
            <fieldset>
                <legend><?= __('Enter Trainee Information') ?></legend>
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
                        <label class="form-label"><?= __('Apprenticeship Order Id') ?></label>
                        <?= $this->Form->control('apprentice_order_id', [
                            'options' => $apprenticeOrders,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Apprenticeship Order Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Training Id') ?></label>
                        <?= $this->Form->control('training_id', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Training Id'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Applicant Code') ?></label>
                        <?= $this->Form->control('applicant_code', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Applicant Code'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Tmm Code') ?></label>
                        <?= $this->Form->control('tmm_code', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Tmm Code'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Vocational Training Institution Id') ?></label>
                        <?= $this->Form->control('vocational_training_institution_id', [
                            'options' => $vocationalTrainingInstitutions,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Vocational Training Institution Id --')
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
                        <label class="form-label"><?= __('Identity Number') ?></label>
                        <?= $this->Form->control('identity_number', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Identity Number'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Name') ?></label>
                        <?= $this->Form->control('name', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Name'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Name Katakana') ?>
                            <small class="text-muted">(Optional - Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('name_katakana', [
                            'class' => 'form-control katakana-input',
                            'placeholder' => __('カタカナで名前を入力してください'),
                            'label' => false,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Enter your name in Katakana (カタカナ). Example: タナカ タロウ
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Gender Id') ?></label>
                        <?= $this->Form->control('master_gender_id', [
                            'options' => $masterGenders,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Master Gender Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Religion Id') ?></label>
                        <?= $this->Form->control('master_religion_id', [
                            'options' => $masterReligions,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Master Religion Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Marriage Status Id') ?></label>
                        <?= $this->Form->control('master_marriage_status_id', [
                            'options' => $masterMarriageStatuses,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Master Marriage Status Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Birth Place') ?></label>
                        <?= $this->Form->control('birth_place', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Birth Place'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <?= __('Birth Place Katakana') ?>
                            <small class="text-muted">(Optional - Max 256 characters)</small>
                        </label>
                        <?= $this->Form->control('birth_place_katakana', [
                            'class' => 'form-control katakana-input',
                            'placeholder' => __('カタカナで出生地を入力してください'),
                            'label' => false,
                            'maxlength' => 256
                        ]) ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Enter birth place in Katakana. Example: トウキョウ
                        </small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Birth Date') ?></label>
                        <?= $this->Form->control('birth_date', [
                            'type' => 'text',
                            'class' => 'form-control datepicker',
                            'placeholder' => __('Select Birth Date'),
                            'label' => false,
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Telephone Mobile') ?></label>
                        <?= $this->Form->control('telephone_mobile', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Telephone Mobile'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Telephone Emergency') ?></label>
                        <?= $this->Form->control('telephone_emergency', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Telephone Emergency'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Email') ?></label>
                        <?= $this->Form->control('email', [
                            'type' => 'email',
                            'class' => 'form-control email-validate',
                            'placeholder' => __('Enter Email'),
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
                                            'id' => 'TraineePropinsiId',
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
                                            'id' => 'TraineeKabupatenId',
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
                                            'id' => 'TraineeKecamatanId',
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
                                            'id' => 'TraineeKelurahanId',
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
                        <label class="form-label"><?= __('Master Kabupaten Id') ?></label>
                        <?= $this->Form->control('master_kabupaten_id', [
                            'options' => $masterKabupatens,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Master Kabupaten Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Kecamatan Id') ?></label>
                        <?= $this->Form->control('master_kecamatan_id', [
                            'options' => $masterKecamatans,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Master Kecamatan Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Kelurahan Id') ?></label>
                        <?= $this->Form->control('master_kelurahan_id', [
                            'options' => $masterKelurahans,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Master Kelurahan Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Post Code') ?></label>
                        <?= $this->Form->control('post_code', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Post Code'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Address') ?></label>
                        <?= $this->Form->control('address', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Address'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Image Photo') ?></label>
                        <?= $this->Form->control('image_photo', [
                            'type' => 'file',
                            'class' => 'form-control',
                            'label' => false,
                            'accept' => 'image/*',
                        ]) ?>
                        <?php if (!empty($trainee->image_photo)): ?>
                            <div class="mt-2">
                                <img src="<?= $this->Url->build('/' . $trainee->image_photo, ['fullBase' => true]) ?>" 
                                     alt="Current Image Photo" 
                                     class="img-thumbnail" 
                                     style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Strengths') ?></label>
                        <?= $this->Form->control('strengths', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Strengths'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Weaknesses') ?></label>
                        <?= $this->Form->control('weaknesses', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Weaknesses'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Hobby') ?></label>
                        <?= $this->Form->control('hobby', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Hobby'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Last Salary Amount') ?></label>
                        <?= $this->Form->control('last_salary_amount', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Last Salary Amount'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Application Reasons') ?></label>
                        <?= $this->Form->control('application_reasons', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Application Reasons'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_ever_went_to_japan', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Ever Went To Japan'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('will_go_to_japan_after_finished', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Will Go To Japan After Finished'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Expected Work Upon Returning To Japan') ?></label>
                        <?= $this->Form->control('expected_work_upon_returning_to_japan', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Expected Work Upon Returning To Japan'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Is Holding Passport') ?></label>
                        <?= $this->Form->control('is_holding_passport', [
                            'type' => 'password',
                            'class' => 'form-control',
                            'placeholder' => __('Enter Is Holding Passport'),
                            'label' => false,
                            'autocomplete' => 'new-password'
                        ]) ?>
                        <small class="text-muted">Minimum 8 characters, include uppercase, lowercase, number & symbol</small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Saving Goal Amount') ?></label>
                        <?= $this->Form->control('saving_goal_amount', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Saving Goal Amount'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Blood Type Id') ?></label>
                        <?= $this->Form->control('blood_type_id', [
                            'options' => $masterBloodTypes,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Blood Type Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Body Weight') ?></label>
                        <?= $this->Form->control('body_weight', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Body Weight'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Body Height') ?></label>
                        <?= $this->Form->control('body_height', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Body Height'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_wear_eye_glasses', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Wear Eye Glasses'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Explain Eye Condition') ?></label>
                        <?= $this->Form->control('explain_eye_condition', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Explain Eye Condition'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_color_blind', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Color Blind'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Explain Color Blind') ?></label>
                        <?= $this->Form->control('explain_color_blind', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Explain Color Blind'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_right_handed', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Right Handed'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_smoking', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Smoking'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_drinking_alcohol', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Drinking Alcohol'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('is_tattooed', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => ['text' => __('Is Tattooed'), 'class' => 'form-check-label']
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Link Whatsapp') ?></label>
                        <?= $this->Form->control('link_whatsapp', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Whatsapp'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Link Line') ?></label>
                        <?= $this->Form->control('link_line', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Line'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Link Instagram') ?></label>
                        <?= $this->Form->control('link_instagram', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Instagram'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Link Facebook') ?></label>
                        <?= $this->Form->control('link_facebook', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Facebook'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Link Tiktok') ?></label>
                        <?= $this->Form->control('link_tiktok', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Tiktok'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Interview Result Id') ?></label>
                        <?= $this->Form->control('master_interview_result_id', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Master Interview Result Id'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Is Training Pass') ?></label>
                        <?= $this->Form->control('is_training_pass', [
                            'type' => 'password',
                            'class' => 'form-control',
                            'placeholder' => __('Enter Is Training Pass'),
                            'label' => false,
                            'autocomplete' => 'new-password'
                        ]) ?>
                        <small class="text-muted">Minimum 8 characters, include uppercase, lowercase, number & symbol</small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Is Apprenticeship Pass') ?></label>
                        <?= $this->Form->control('is_apprenticeship_pass', [
                            'type' => 'password',
                            'class' => 'form-control',
                            'placeholder' => __('Enter Is Apprenticeship Pass'),
                            'label' => false,
                            'autocomplete' => 'new-password'
                        ]) ?>
                        <small class="text-muted">Minimum 8 characters, include uppercase, lowercase, number & symbol</small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Rejected Reason Id') ?></label>
                        <?= $this->Form->control('master_rejected_reason_id', [
                            'options' => $masterRejectedReasons,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Master Rejected Reason Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Specific Rejected Reason') ?></label>
                        <?= $this->Form->control('specific_rejected_reason', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => __('Enter Specific Rejected Reason'),
                            'label' => false
                        ]) ?>
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Trainee'), [
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
    $('#traineeForm').on('keydown', function(e) {
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
                        <label class="form-label"><?= __('Link Line') ?></label>
                        <?= $this->Form->control('link_line', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Line'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Link Instagram') ?></label>
                        <?= $this->Form->control('link_instagram', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Instagram'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Link Facebook') ?></label>
                        <?= $this->Form->control('link_facebook', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Facebook'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Link Tiktok') ?></label>
                        <?= $this->Form->control('link_tiktok', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Link Tiktok'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Interview Result Id') ?></label>
                        <?= $this->Form->control('master_interview_result_id', [
                            'class' => 'form-control',
                            'placeholder' => __('Enter Master Interview Result Id'),
                            'label' => false
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Is Training Pass') ?></label>
                        <?= $this->Form->control('is_training_pass', [
                            'type' => 'password',
                            'class' => 'form-control',
                            'placeholder' => __('Enter Is Training Pass'),
                            'label' => false,
                            'autocomplete' => 'new-password'
                        ]) ?>
                        <small class="text-muted">Minimum 8 characters, include uppercase, lowercase, number & symbol</small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Is Apprenticeship Pass') ?></label>
                        <?= $this->Form->control('is_apprenticeship_pass', [
                            'type' => 'password',
                            'class' => 'form-control',
                            'placeholder' => __('Enter Is Apprenticeship Pass'),
                            'label' => false,
                            'autocomplete' => 'new-password'
                        ]) ?>
                        <small class="text-muted">Minimum 8 characters, include uppercase, lowercase, number & symbol</small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Master Rejected Reason Id') ?></label>
                        <?= $this->Form->control('master_rejected_reason_id', [
                            'options' => $masterRejectedReasons,
                            'class' => 'form-control',
                            'label' => false,
                            'empty' => __('-- Select Master Rejected Reason Id --')
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Specific Rejected Reason') ?></label>
                        <?= $this->Form->control('specific_rejected_reason', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => __('Enter Specific Rejected Reason'),
                            'label' => false
                        ]) ?>
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions mt-4">
                <?= $this->Form->button(__('Save Trainee'), [
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
    $('#traineeForm').on('keydown', function(e) {
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
    
    // Form validation
    $('#traineeForm').on('submit', function(e) {
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
    });
    
    // Re-enable form if user navigates back
    $(window).on('pageshow', function(event) {
        if (event.originalEvent.persisted) {
            $('.form-overlay').remove();
    });
});
</script>
<?php $this->end(); ?>

