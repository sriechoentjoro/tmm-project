<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trainee[]|\Cake\Collection\CollectionInterface $trainees
 */
?>
<!-- Page Header with Hamburger Menu -->
<div class="index-header" style="margin-bottom: 20px;">
    <!-- Title Row with Hamburger Dropdown -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 6px 8px; font-size: 18px; color: #24292f; background: transparent; border: none; text-decoration: none; margin: 0;">
                <i class="fas fa-bars"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Trainees'),
                    ['action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Trainee'),
                    ['action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Candidates'),
                    ['controller' => 'Candidates', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Candidate'),
                    ['controller' => 'Candidates', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Apprentice Orders'),
                    ['controller' => 'ApprenticeOrders', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Apprentice Order'),
                    ['controller' => 'ApprenticeOrders', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Vocational Training Institutions'),
                    ['controller' => 'VocationalTrainingInstitutions', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Vocational Training Institution'),
                    ['controller' => 'VocationalTrainingInstitutions', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Acceptance Organizations'),
                    ['controller' => 'AcceptanceOrganizations', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Acceptance Organization'),
                    ['controller' => 'AcceptanceOrganizations', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Genders'),
                    ['controller' => 'MasterGenders', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Gender'),
                    ['controller' => 'MasterGenders', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Religions'),
                    ['controller' => 'MasterReligions', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Religion'),
                    ['controller' => 'MasterReligions', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Marriage Statuses'),
                    ['controller' => 'MasterMarriageStatuses', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Marriage Status'),
                    ['controller' => 'MasterMarriageStatuses', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Propinsis'),
                    ['controller' => 'MasterPropinsis', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Propinsi'),
                    ['controller' => 'MasterPropinsis', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Kabupatens'),
                    ['controller' => 'MasterKabupatens', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Kabupaten'),
                    ['controller' => 'MasterKabupatens', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Kecamatans'),
                    ['controller' => 'MasterKecamatans', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Kecamatan'),
                    ['controller' => 'MasterKecamatans', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Kelurahans'),
                    ['controller' => 'MasterKelurahans', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Kelurahan'),
                    ['controller' => 'MasterKelurahans', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Blood Types'),
                    ['controller' => 'MasterBloodTypes', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Blood Type'),
                    ['controller' => 'MasterBloodTypes', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <div class="dropdown-divider"></div>
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Rejected Reasons'),
                    ['controller' => 'MasterRejectedReasons', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Rejected Reason'),
                    ['controller' => 'MasterRejectedReasons', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
            </div>
            
            <h2 style="margin: 0; display: inline-block;"><?= __('Trainees') ?></h2>
        </div>
        
        <div style="display: flex; align-items: center; gap: 10px;">
            <?= $this->Html->link(
                '<i class="fas fa-plus"></i> ' . __('Add New'),
                ['action' => 'add'],
                ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Add New Record']
            ) ?>
        </div>
    </div>
    
    <!-- Export Buttons Row -->
    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 5px;">
        <?= $this->Html->link(
            '<i class="fas fa-print"></i> Print',
            ['action' => 'printReport', '?' => $this->request->getQueryParams()],
            ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Print Report', 'target' => '_blank']
        ) ?>
        <?= $this->Html->link(
            '<i class="fas fa-download"></i> CSV',
            ['action' => 'exportCsv', '?' => $this->request->getQueryParams()],
            ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to CSV']
        ) ?>
        <?= $this->Html->link(
            '<i class="fas fa-file-excel"></i> Excel',
            ['action' => 'exportExcel', '?' => $this->request->getQueryParams()],
            ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to Excel']
        ) ?>
    </div>
</div>

<!-- Table Container with Horizontal Scroll -->
<div class="table-scroll-wrapper" style="overflow-x: auto; cursor: grab; -webkit-overflow-scrolling: touch; user-select: none;">
    <div class="trainees index content">
        <table class="table" style="border-collapse: collapse; width: 100%; min-width: 800px;">
            <!-- Purple Gradient Header -->
            <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" class="actions"><?= __('Actions') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('candidate_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('apprentice_order_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('training_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('applicant_code') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('tmm_code') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('vocational_training_institution_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('acceptance_organization_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('identity_number') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('name') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('name_katakana') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_gender_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_religion_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_marriage_status_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('birth_place') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('birth_place_katakana') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('birth_date') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('telephone_mobile') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('telephone_emergency') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('email') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_propinsi_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_kabupaten_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_kecamatan_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_kelurahan_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('post_code') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('address') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('image_photo') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('strengths') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('weaknesses') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('hobby') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('last_salary_amount') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('application_reasons') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_ever_went_to_japan') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('will_go_to_japan_after_finished') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('expected_work_upon_returning_to_japan') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_holding_passport') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('saving_goal_amount') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('blood_type_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('body_weight') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('body_height') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_wear_eye_glasses') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('explain_eye_condition') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_color_blind') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('explain_color_blind') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_right_handed') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_smoking') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_drinking_alcohol') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_tattooed') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('link_whatsapp') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('link_line') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('link_instagram') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('link_facebook') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('link_tiktok') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_interview_result_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_training_pass') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('is_apprenticeship_pass') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_rejected_reason_id') ?></th>
                </tr>
            
            <!-- Filter Row (INSIDE thead) -->
            <tr class="filter-row" style="background-color: #f8f9fa;">
                <td style="padding: 5px;"><!-- Actions column - no filter --></td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="id" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">â‰¤</option>
                        <option value=">=">â‰¥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="number" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="id" style="font-size: 0.85rem; padding: 4px;">
                    <input type="number" class="filter-input-range form-control form-control-sm" placeholder="To..." data-column="id" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="candidate_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Candidates</option>
                        <?php foreach ($candidates as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="apprentice_order_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Apprenticeship_orders</option>
                        <?php foreach ($apprenticeship_orders as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="training_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Trainings</option>
                        <?php foreach ($trainings as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="applicant_code" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="applicant_code" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="tmm_code" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="tmm_code" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="vocational_training_institution_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Vocational_training_institutions</option>
                        <?php foreach ($vocational_training_institutions as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="acceptance_organization_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Acceptance_organizations</option>
                        <?php foreach ($acceptance_organizations as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="identity_number" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="identity_number" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="name" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="name" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="name_katakana" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="name_katakana" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_gender_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_genders</option>
                        <?php foreach ($master_genders as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_religion_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_religions</option>
                        <?php foreach ($master_religions as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_marriage_status_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_marriage_statuss</option>
                        <?php foreach ($master_marriage_statuss as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="birth_place" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="birth_place" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="birth_place_katakana" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="birth_place_katakana" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="birth_date" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">â‰¤</option>
                        <option value=">=">â‰¥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="date" class="filter-input form-control form-control-sm" data-column="birth_date" style="font-size: 0.85rem; padding: 4px;">
                    <input type="date" class="filter-input-range form-control form-control-sm" data-column="birth_date" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="telephone_mobile" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="telephone_mobile" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="telephone_emergency" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="telephone_emergency" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="email" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="email" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_propinsi_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_propinsis</option>
                        <?php foreach ($master_propinsis as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_kabupaten_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_kabupatens</option>
                        <?php foreach ($master_kabupatens as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_kecamatan_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_kecamatans</option>
                        <?php foreach ($master_kecamatans as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_kelurahan_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_kelurahans</option>
                        <?php foreach ($master_kelurahans as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="post_code" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">â‰¤</option>
                        <option value=">=">â‰¥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="number" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="post_code" style="font-size: 0.85rem; padding: 4px;">
                    <input type="number" class="filter-input-range form-control form-control-sm" placeholder="To..." data-column="post_code" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="address" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="address" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="image_photo" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="image_photo" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="strengths" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="strengths" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="weaknesses" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="weaknesses" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="hobby" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="hobby" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="last_salary_amount" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">â‰¤</option>
                        <option value=">=">â‰¥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="number" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="last_salary_amount" style="font-size: 0.85rem; padding: 4px;">
                    <input type="number" class="filter-input-range form-control form-control-sm" placeholder="To..." data-column="last_salary_amount" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="application_reasons" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="application_reasons" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_ever_went_to_japan" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_ever_went_to_japan" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="will_go_to_japan_after_finished" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="will_go_to_japan_after_finished" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="expected_work_upon_returning_to_japan" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="expected_work_upon_returning_to_japan" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_holding_passport" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_holding_passport" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="saving_goal_amount" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">â‰¤</option>
                        <option value=">=">â‰¥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="number" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="saving_goal_amount" style="font-size: 0.85rem; padding: 4px;">
                    <input type="number" class="filter-input-range form-control form-control-sm" placeholder="To..." data-column="saving_goal_amount" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="blood_type_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Blood_types</option>
                        <?php foreach ($blood_types as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="body_weight" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">â‰¤</option>
                        <option value=">=">â‰¥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="number" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="body_weight" style="font-size: 0.85rem; padding: 4px;">
                    <input type="number" class="filter-input-range form-control form-control-sm" placeholder="To..." data-column="body_weight" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="body_height" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">â‰¤</option>
                        <option value=">=">â‰¥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="number" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="body_height" style="font-size: 0.85rem; padding: 4px;">
                    <input type="number" class="filter-input-range form-control form-control-sm" placeholder="To..." data-column="body_height" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_wear_eye_glasses" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_wear_eye_glasses" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="explain_eye_condition" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="explain_eye_condition" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_color_blind" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_color_blind" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="explain_color_blind" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="explain_color_blind" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_right_handed" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_right_handed" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_smoking" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_smoking" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_drinking_alcohol" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_drinking_alcohol" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_tattooed" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_tattooed" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="link_whatsapp" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="link_whatsapp" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="link_line" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="link_line" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="link_instagram" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="link_instagram" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="link_facebook" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="link_facebook" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="link_tiktok" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="link_tiktok" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_interview_result_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_interview_results</option>
                        <?php foreach ($master_interview_results as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_training_pass" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_training_pass" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="is_apprenticeship_pass" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="is_apprenticeship_pass" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-input form-control form-control-sm" data-column="master_rejected_reason_id" data-type="select" style="font-size: 0.85rem; padding: 4px;">
                        <option value="">All Master_rejected_reasons</option>
                        <?php foreach ($master_rejected_reasons as $id => $name): ?>
                            <option value="<?= $id ?>"><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            </thead>
            
            <tbody>
                <?php foreach ($trainees as $trainee): ?>
                <tr>
                    <!-- Action Buttons with Icons -->
                    <td class="actions" style="white-space: nowrap; padding: 8px;">
                        <div class="action-buttons-hover">
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $trainee->id],
                                ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => 'View']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $trainee->id],
                                ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => 'Edit']
                            ) ?>
                        </div>
                    </td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($trainee->id) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('candidate') ? $this->Html->link($trainee->candidate->name, ['controller' => 'Candidates', 'action' => 'view', $trainee->candidate->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('apprentice_order') ? $this->Html->link($trainee->apprentice_order->title, ['controller' => 'ApprenticeOrders', 'action' => 'view', $trainee->apprentice_order->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($trainee->training_id) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->applicant_code) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->tmm_code) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('vocational_training_institution') ? $this->Html->link($trainee->vocational_training_institution->name, ['controller' => 'VocationalTrainingInstitutions', 'action' => 'view', $trainee->vocational_training_institution->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('acceptance_organization') ? $this->Html->link($trainee->acceptance_organization->title, ['controller' => 'AcceptanceOrganizations', 'action' => 'view', $trainee->acceptance_organization->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->identity_number) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->name) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->name_katakana) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_gender') ? $this->Html->link($trainee->master_gender->title, ['controller' => 'MasterGenders', 'action' => 'view', $trainee->master_gender->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_religion') ? $this->Html->link($trainee->master_religion->title, ['controller' => 'MasterReligions', 'action' => 'view', $trainee->master_religion->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_marriage_status') ? $this->Html->link($trainee->master_marriage_status->title, ['controller' => 'MasterMarriageStatuses', 'action' => 'view', $trainee->master_marriage_status->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->birth_place) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->birth_place_katakana) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->birth_date) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->telephone_mobile) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->telephone_emergency) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->email) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_propinsi') ? $this->Html->link($trainee->master_propinsi->title, ['controller' => 'MasterPropinsis', 'action' => 'view', $trainee->master_propinsi->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_kabupaten') ? $this->Html->link($trainee->master_kabupaten->title, ['controller' => 'MasterKabupatens', 'action' => 'view', $trainee->master_kabupaten->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_kecamatan') ? $this->Html->link($trainee->master_kecamatan->title, ['controller' => 'MasterKecamatans', 'action' => 'view', $trainee->master_kecamatan->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_kelurahan') ? $this->Html->link($trainee->master_kelurahan->title, ['controller' => 'MasterKelurahans', 'action' => 'view', $trainee->master_kelurahan->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($trainee->post_code) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->address) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->element('file_viewer', ['filePath' => $trainee->image_photo]) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->strengths) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->weaknesses) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->hobby) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($trainee->last_salary_amount) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->application_reasons) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_ever_went_to_japan) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->will_go_to_japan_after_finished) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->expected_work_upon_returning_to_japan) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_holding_passport) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($trainee->saving_goal_amount) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_blood_type') ? $this->Html->link($trainee->master_blood_type->title, ['controller' => 'MasterBloodTypes', 'action' => 'view', $trainee->master_blood_type->id]) : '' ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($trainee->body_weight) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($trainee->body_height) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_wear_eye_glasses) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->explain_eye_condition) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_color_blind) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->explain_color_blind) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_right_handed) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_smoking) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_drinking_alcohol) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_tattooed) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->link_whatsapp) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->link_line) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->link_instagram) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->link_facebook) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->link_tiktok) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($trainee->master_interview_result_id) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_training_pass) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($trainee->is_apprenticeship_pass) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $trainee->has('master_rejected_reason') ? $this->Html->link($trainee->master_rejected_reason->title, ['controller' => 'MasterRejectedReasons', 'action' => 'view', $trainee->master_rejected_reason->id]) : '' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="paginator" role="navigation" aria-label="Pagination" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
    <ul class="pagination" style="margin: 10px 0; display: flex; list-style: none; gap: 5px;">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p style="margin: 10px 0;"><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
</div>

<style>
/* Dropdown Menu Styles */
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 200px;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    font-size: 1rem;
    color: #212529;
    text-align: left;
    list-style: none;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);

.dropdown-menu.show {
    display: block;

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.5rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;

.dropdown-item:hover {
    color: #16181b;
    background-color: #f8f9fa;

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid #e9ecef;

/* Action Buttons with Hover Effect */
.action-buttons-hover {
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s ease-in-out;

tr:hover .action-buttons-hover {
    opacity: 1;

.btn-action-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    padding: 0;
    font-size: 12px;
    border-radius: 4px;
    background-color: #2c3e50;
    color: #fff;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;

.btn-action-icon:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);

.btn-view-icon:hover { background-color: #3498db; color: #fff; }
.btn-edit-icon:hover { background-color: #f39c12; color: #fff; }
.btn-delete-icon:hover { background-color: #e74c3c; color: #fff; }

/* Export Buttons */
.btn-export-light {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #24292f;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s ease;

.btn-export-light:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
    border-color: rgba(102, 126, 234, 0.5);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #24292f;

/* Drag to Scroll */
.table-scroll-wrapper.dragging {
    cursor: grabbing;
    user-select: none;

/* Filter Input Styles */
.filter-input, .filter-operator, .filter-input-range {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 4px;

.filter-input:focus, .filter-operator:focus, .filter-input-range:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hamburger Dropdown Menu
    const dropdownButton = document.getElementById('dropdownMenuButton');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownButton && dropdownMenu) {
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
        });
        
        dropdownMenu.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                setTimeout(() => dropdownMenu.classList.remove('show'), 100);
        });
    
    // Drag to Scroll
    const scrollContainer = document.querySelector('.table-scroll-wrapper');
    let isDown = false;
    let startX;
    let scrollLeft;

    scrollContainer.addEventListener('mousedown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.tagName === 'SELECT') {
            return;
        isDown = true;
        scrollContainer.classList.add('dragging');
        startX = e.pageX - scrollContainer.offsetLeft;
        scrollLeft = scrollContainer.scrollLeft;
    });

    scrollContainer.addEventListener('mouseleave', function() {
        isDown = false;
        scrollContainer.classList.remove('dragging');
    });

    scrollContainer.addEventListener('mouseup', function() {
        isDown = false;
        scrollContainer.classList.remove('dragging');
    });

    scrollContainer.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - scrollContainer.offsetLeft;
        const walk = (x - startX) * 2;
        scrollContainer.scrollLeft = scrollLeft - walk;
    });
    
    // Filter Operators
    const filterOperators = document.querySelectorAll('.filter-operator');
    const filterInputs = document.querySelectorAll('.filter-input');
    const rangeInputs = document.querySelectorAll('.filter-input-range');
    
    // Handle operator change to show/hide range input for "between"
    filterOperators.forEach(function(select) {
        select.addEventListener('change', function() {
            const column = this.getAttribute('data-column');
            const operator = this.value;
            
            const rangeInput = document.querySelector('.filter-input-range[data-column="' + column + '"]');
            
            if (operator === 'between' && rangeInput) {
                rangeInput.style.display = 'block';
            } else if (rangeInput) {
                rangeInput.style.display = 'none';
            
            filterTable();
        });
    });
    
    // Add event listeners to filter inputs
    filterInputs.forEach(function(input) {
        input.addEventListener('input', filterTable);
    });
    
    // Add event listeners to range inputs for "between" operator
    rangeInputs.forEach(function(input) {
        input.addEventListener('input', filterTable);
    });
    
    function filterTable() {
        const table = document.querySelector('.table tbody');
        const rows = table.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(function(row) {
            let showRow = true;
            
            filterInputs.forEach(function(input) {
                if (!showRow) return;
                
                const filterValue = input.value.trim();
                if (!filterValue) return;
                
                const column = input.getAttribute('data-column');
                const columnIndex = getColumnIndex(column);
                
                if (columnIndex === -1) return;
                
                const cell = row.cells[columnIndex];
                if (!cell) return;
                
                const cellText = cell.textContent || cell.innerText || '';
                
                const operatorSelect = document.querySelector('.filter-operator[data-column="' + column + '"]');
                const operator = operatorSelect ? operatorSelect.value : 'like';
                
                // For "between", collect range value
                if (operator === 'between') {
                    const rangeInput = document.querySelector('.filter-input-range[data-column="' + column + '"]');
                    const filterValue2 = rangeInput ? rangeInput.value.trim() : '';
                    showRow = applyFilter(cellText, filterValue, operator, filterValue2);
                } else {
                    showRow = applyFilter(cellText, filterValue, operator);
            });
            
            if (showRow) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
        });
        
        console.log('Filtered: ' + visibleCount + ' / ' + rows.length + ' rows visible');
    
    function applyFilter(cellText, filterValue, operator, filterValue2) {
        const cellVal = cellText.trim().toLowerCase();
        const filterVal = filterValue.trim().toLowerCase();
        
        if (filterVal === '') return true;
        
        const cellNum = parseFloat(cellVal.replace(/[^0-9.-]/g, ''));
        const filterNum = parseFloat(filterVal);
        const isNumeric = !isNaN(cellNum) && !isNaN(filterNum);
        
        switch(operator) {
            case '=':
                return isNumeric ? cellNum === filterNum : cellVal === filterVal;
            case '!=':
                return isNumeric ? cellNum !== filterNum : cellVal !== filterVal;
            case '<':
                return isNumeric ? cellNum < filterNum : cellVal < filterVal;
            case '>':
                return isNumeric ? cellNum > filterNum : cellVal > filterVal;
            case '<=':
                return isNumeric ? cellNum <= filterNum : cellVal <= filterVal;
            case '>=':
                return isNumeric ? cellNum >= filterNum : cellVal >= filterVal;
            case 'between':
                if (filterValue2 && isNumeric) {
                    const filterNum2 = parseFloat(filterValue2);
                    if (!isNaN(filterNum2)) {
                        return cellNum >= filterNum && cellNum <= filterNum2;
                return cellVal.includes(filterVal);
            case 'like':
                return cellVal.includes(filterVal);
            case 'not_like':
                return !cellVal.includes(filterVal);
            case 'starts_with':
                return cellVal.startsWith(filterVal);
            case 'ends_with':
                return cellVal.endsWith(filterVal);
            default:
                return cellVal.includes(filterVal);
    
    function getColumnIndex(columnName) {
        const headerCells = document.querySelectorAll('.table thead th');
        for (let i = 0; i < headerCells.length; i++) {
            const sortLink = headerCells[i].querySelector('a');
            if (sortLink) {
                const href = sortLink.getAttribute('href');
                if (href && href.includes('sort=' + columnName)) {
                    return i;
        return -1;
});
</script>


<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
