<!-- GitHub Style View Template -->
<div class="github-container">
    <!-- Page Header -->
    <div class="github-page-header">
        <div class="github-header-content">
            <div class="github-title-row">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Hamburger Dropdown Menu -->
                    <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" 
                            data-bs-toggle="dropdown" aria-expanded="false" 
                            style="padding: 6px 8px; font-size: 18px; color: #24292f; 
                                   background: transparent; border: none; text-decoration: none; margin: 0;">
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
                            '<i class="fas fa-list"></i> ' . __('List ApprenticeOrders'),
                            ['controller' => 'ApprenticeOrders', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New ApprenticeOrder'),
                            ['controller' => 'ApprenticeOrders', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List VocationalTrainingInstitutions'),
                            ['controller' => 'VocationalTrainingInstitutions', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New VocationalTrainingInstitution'),
                            ['controller' => 'VocationalTrainingInstitutions', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List AcceptanceOrganizations'),
                            ['controller' => 'AcceptanceOrganizations', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New AcceptanceOrganization'),
                            ['controller' => 'AcceptanceOrganizations', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterGenders'),
                            ['controller' => 'MasterGenders', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterGender'),
                            ['controller' => 'MasterGenders', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterReligions'),
                            ['controller' => 'MasterReligions', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterReligion'),
                            ['controller' => 'MasterReligions', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterMarriageStatuses'),
                            ['controller' => 'MasterMarriageStatuses', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterMarriageStatus'),
                            ['controller' => 'MasterMarriageStatuses', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterPropinsis'),
                            ['controller' => 'MasterPropinsis', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterPropinsi'),
                            ['controller' => 'MasterPropinsis', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterKabupatens'),
                            ['controller' => 'MasterKabupatens', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterKabupaten'),
                            ['controller' => 'MasterKabupatens', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterKecamatans'),
                            ['controller' => 'MasterKecamatans', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterKecamatan'),
                            ['controller' => 'MasterKecamatans', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterKelurahans'),
                            ['controller' => 'MasterKelurahans', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterKelurahan'),
                            ['controller' => 'MasterKelurahans', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterBloodTypes'),
                            ['controller' => 'MasterBloodTypes', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterBloodType'),
                            ['controller' => 'MasterBloodTypes', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List MasterRejectedReasons'),
                            ['controller' => 'MasterRejectedReasons', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterRejectedReason'),
                            ['controller' => 'MasterRejectedReasons', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                    </div>
                    
                    <h1 class="github-page-title" style="margin: 0;">
                        <?= __('Trainee') ?>: <?= h($trainee->name) ?>
                    </h1>
                </div>
                
                <div class="github-header-actions">
                    <?= $this->Html->link(
                        '<i class="fas fa-file-csv"></i> ' . __('CSV'),
                        ['action' => 'exportCsv'],
                        ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to CSV']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-file-excel"></i> ' . __('Excel'),
                        ['action' => 'exportExcel'],
                        ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to Excel']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-file-pdf"></i> ' . __('PDF'),
                        ['action' => 'exportPdf'],
                        ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to PDF']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-print"></i> ' . __('Print'),
                        'javascript:window.print()',
                        ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Print this page']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-edit"></i> ' . __('Edit'),
                        ['action' => 'edit', $trainee->id],
                        ['class' => 'btn-export-light', 'escape' => false]
                    ) ?>
                    <?= $this->Form->postLink(
                        '<i class="fas fa-trash"></i> ' . __('Delete'),
                        ['action' => 'delete', $trainee->id],
                        [
                            'class' => 'btn-export-light',
                            'escape' => false,
                            'confirm' => __('Are you sure you want to delete # {0}?', $trainee->id)
                        ]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-list"></i> ' . __('List'),
                        ['action' => 'index'],
                        ['class' => 'btn-export-light', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- View Content Wrapper - Modal Safe -->
    <div class="view-content-wrapper" data-view-template="true">
    
    <!-- Tab Navigation -->
    <div class="view-tabs-container">
        <ul class="view-tabs-nav" role="tablist">
            <li class="view-tab-item">
                <a href="#tab-home" class="view-tab-link active" data-tab="tab-home">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M0 1.75C0 .784.784 0 1.75 0h12.5C15.216 0 16 .784 16 1.75v12.5A1.75 1.75 0 0 1 14.25 16H1.75A1.75 1.75 0 0 1 0 14.25V1.75zm1.75-.25a.25.25 0 0 0-.25.25v12.5c0 .138.112.25.25.25h12.5a.25.25 0 0 0 .25-.25V1.75a.25.25 0 0 0-.25-.25H1.75zM3.5 6.75A.75.75 0 0 1 4.25 6h7a.75.75 0 0 1 0 1.5h-7a.75.75 0 0 1-.75-.75zm.75 2.25a.75.75 0 0 0 0 1.5h4a.75.75 0 0 0 0-1.5h-4z"></path>
                    </svg>
                    <?= __('Detail') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-candidates" class="view-tab-link" data-tab="tab-candidates">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('Candidates') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-apprentice_orders" class="view-tab-link" data-tab="tab-apprentice_orders">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('ApprenticeOrders') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-vocational_training_institutions" class="view-tab-link" data-tab="tab-vocational_training_institutions">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('VocationalTrainingInstitutions') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-acceptance_organizations" class="view-tab-link" data-tab="tab-acceptance_organizations">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('AcceptanceOrganizations') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_genders" class="view-tab-link" data-tab="tab-master_genders">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterGenders') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_religions" class="view-tab-link" data-tab="tab-master_religions">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterReligions') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_marriage_statuses" class="view-tab-link" data-tab="tab-master_marriage_statuses">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterMarriageStatuses') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_propinsis" class="view-tab-link" data-tab="tab-master_propinsis">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterPropinsis') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_kabupatens" class="view-tab-link" data-tab="tab-master_kabupatens">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterKabupatens') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_kecamatans" class="view-tab-link" data-tab="tab-master_kecamatans">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterKecamatans') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_kelurahans" class="view-tab-link" data-tab="tab-master_kelurahans">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterKelurahans') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_blood_types" class="view-tab-link" data-tab="tab-master_blood_types">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterBloodTypes') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-master_rejected_reasons" class="view-tab-link" data-tab="tab-master_rejected_reasons">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                    </svg>
                    <?= __('MasterRejectedReasons') ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-trainee_certifications" class="view-tab-link" data-tab="tab-trainee_certifications">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('TraineeCertifications') ?>
                    <?php if (!empty($trainee->trainee_certifications)): ?>
                    <span class="tab-badge"><?= count($trainee->trainee_certifications) ?></span>
                    <?php else: ?>
                    <span class="tab-badge">0</span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-trainee_courses" class="view-tab-link" data-tab="tab-trainee_courses">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('TraineeCourses') ?>
                    <?php if (!empty($trainee->trainee_courses)): ?>
                    <span class="tab-badge"><?= count($trainee->trainee_courses) ?></span>
                    <?php else: ?>
                    <span class="tab-badge">0</span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-trainee_educations" class="view-tab-link" data-tab="tab-trainee_educations">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('TraineeEducations') ?>
                    <?php if (!empty($trainee->trainee_educations)): ?>
                    <span class="tab-badge"><?= count($trainee->trainee_educations) ?></span>
                    <?php else: ?>
                    <span class="tab-badge">0</span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-trainee_experiences" class="view-tab-link" data-tab="tab-trainee_experiences">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('TraineeExperiences') ?>
                    <?php if (!empty($trainee->trainee_experiences)): ?>
                    <span class="tab-badge"><?= count($trainee->trainee_experiences) ?></span>
                    <?php else: ?>
                    <span class="tab-badge">0</span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-trainee_families" class="view-tab-link" data-tab="tab-trainee_families">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('TraineeFamilies') ?>
                    <?php if (!empty($trainee->trainee_families)): ?>
                    <span class="tab-badge"><?= count($trainee->trainee_families) ?></span>
                    <?php else: ?>
                    <span class="tab-badge">0</span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-trainee_family_stories" class="view-tab-link" data-tab="tab-trainee_family_stories">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('TraineeFamilyStories') ?>
                    <?php if (!empty($trainee->trainee_family_stories)): ?>
                    <span class="tab-badge"><?= count($trainee->trainee_family_stories) ?></span>
                    <?php else: ?>
                    <span class="tab-badge">0</span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="view-tabs-content">
            <!-- Home Tab -->
            <div id="tab-home" class="view-tab-pane active">
                <!-- Media Preview Section -->
                <div class="media-preview-section" style="margin-bottom: 24px;">
                    <div class="image-preview-container" style="text-align: center; margin-bottom: 20px; background: #f6f8fa; padding: 20px; border-radius: 8px; border: 1px solid #d0d7de;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: #24292f;">
                            <i class="fas fa-image"></i> <?= __('Image Photo') ?>
                        </h4>
                        <?= $this->element('file_viewer', [
                            'filePath' => $trainee->image_photo,
                            'showPreview' => true,
                            'editUrl' => $this->Url->build(['action' => 'edit', $trainee->id])
                        ]) ?>
                    </div>
                </div>

                <!-- Slideshow JavaScript -->
                <script>
                function changeSlide(containerId, direction) {
                    const container = document.getElementById(containerId);
                    const slides = container.getElementsByClassName('slide');
                    let currentIndex = -1;
                    
                    for (let i = 0; i < slides.length; i++) {
                        if (slides[i].style.display === 'block') {
                            currentIndex = i;
                            slides[i].style.display = 'none';
                            break;
                    
                    currentIndex += direction;
                    if (currentIndex >= slides.length) currentIndex = 0;
                    if (currentIndex < 0) currentIndex = slides.length - 1;
                    
                    slides[currentIndex].style.display = 'block';
                
                function showSlide(containerId, index) {
                    const container = document.getElementById(containerId);
                    const slides = container.getElementsByClassName('slide');
                    
                    for (let i = 0; i < slides.length; i++) {
                        slides[i].style.display = i === index ? 'block' : 'none';
                
                // Touch swipe support for mobile
                document.querySelectorAll('.slideshow-container').forEach(container => {
                    let touchStartX = 0;
                    let touchEndX = 0;
                    
                    container.addEventListener('touchstart', e => {
                        touchStartX = e.changedTouches[0].screenX;
                    });
                    
                    container.addEventListener('touchend', e => {
                        touchEndX = e.changedTouches[0].screenX;
                        const diff = touchStartX - touchEndX;
                        if (Math.abs(diff) > 50) {
                            changeSlide(container.id, diff > 0 ? 1 : -1);
                    });
                });
                </script>

                <!-- Mobile Responsive Styles -->
                <style>
                @media (max-width: 768px) {
                    .image-preview-container img,
                    .slideshow-container img {
                        max-height: 300px !important;
                    
                    .file-preview-container iframe {
                        height: 400px !important;
                    
                    .slideshow-container button {
                        padding: 8px 12px !important;
                        font-size: 16px !important;
                    
                    .media-preview-section {
                        margin-left: -10px;
                        margin-right: -10px;
                    
                    .image-preview-container,
                    .file-preview-container,
                    .image-gallery-container {
                        padding: 15px !important;
                        border-radius: 0 !important;
                </style>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M0 1.75C0 .784.784 0 1.75 0h12.5C15.216 0 16 .784 16 1.75v12.5A1.75 1.75 0 0 1 14.25 16H1.75A1.75 1.75 0 0 1 0 14.25V1.75z"></path>
                            </svg>
                            <?= __('Details') ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                <table class="github-details-table">
                    <tbody>
                        <tr>
                            <th class="github-detail-label"><?= __('Id') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($trainee->id) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Training Id') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($trainee->training_id) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Post Code') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($trainee->post_code) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Last Salary Amount') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($trainee->last_salary_amount) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Saving Goal Amount') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($trainee->saving_goal_amount) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Body Weight') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($trainee->body_weight) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Body Height') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($trainee->body_height) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Interview Result Id') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($trainee->master_interview_result_id) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Candidate Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('candidate') ? $this->Html->link($trainee->candidate->name, ['controller' => 'Candidates', 'action' => 'view', $trainee->candidate->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Apprenticeship Order Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('apprentice_order') ? $this->Html->link($trainee->apprentice_order->title, ['controller' => 'ApprenticeOrders', 'action' => 'view', $trainee->apprentice_order->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Applicant Code') ?></th>
                            <td class="github-detail-value"><?= h($trainee->applicant_code) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Tmm Code') ?></th>
                            <td class="github-detail-value"><?= h($trainee->tmm_code) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Vocational Training Institution Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('vocational_training_institution') ? $this->Html->link($trainee->vocational_training_institution->name, ['controller' => 'VocationalTrainingInstitutions', 'action' => 'view', $trainee->vocational_training_institution->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Acceptance Organization Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('acceptance_organization') ? $this->Html->link($trainee->acceptance_organization->title, ['controller' => 'AcceptanceOrganizations', 'action' => 'view', $trainee->acceptance_organization->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Identity Number') ?></th>
                            <td class="github-detail-value"><?= h($trainee->identity_number) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Name') ?></th>
                            <td class="github-detail-value"><?= h($trainee->name) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Name Katakana') ?></th>
                            <td class="github-detail-value"><?= h($trainee->name_katakana) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Gender Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_gender') ? $this->Html->link($trainee->master_gender->title, ['controller' => 'MasterGenders', 'action' => 'view', $trainee->master_gender->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Religion Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_religion') ? $this->Html->link($trainee->master_religion->title, ['controller' => 'MasterReligions', 'action' => 'view', $trainee->master_religion->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Marriage Status Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_marriage_status') ? $this->Html->link($trainee->master_marriage_status->title, ['controller' => 'MasterMarriageStatuses', 'action' => 'view', $trainee->master_marriage_status->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Birth Place') ?></th>
                            <td class="github-detail-value"><?= h($trainee->birth_place) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Birth Place Katakana') ?></th>
                            <td class="github-detail-value"><?= h($trainee->birth_place_katakana) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Telephone Mobile') ?></th>
                            <td class="github-detail-value"><?= h($trainee->telephone_mobile) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Telephone Emergency') ?></th>
                            <td class="github-detail-value"><?= h($trainee->telephone_emergency) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Email') ?></th>
                            <td class="github-detail-value"><?= h($trainee->email) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Propinsi Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_propinsi') ? $this->Html->link($trainee->master_propinsi->title, ['controller' => 'MasterPropinsis', 'action' => 'view', $trainee->master_propinsi->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Kabupaten Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_kabupaten') ? $this->Html->link($trainee->master_kabupaten->title, ['controller' => 'MasterKabupatens', 'action' => 'view', $trainee->master_kabupaten->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Kecamatan Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_kecamatan') ? $this->Html->link($trainee->master_kecamatan->title, ['controller' => 'MasterKecamatans', 'action' => 'view', $trainee->master_kecamatan->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Kelurahan Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_kelurahan') ? $this->Html->link($trainee->master_kelurahan->title, ['controller' => 'MasterKelurahans', 'action' => 'view', $trainee->master_kelurahan->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Address') ?></th>
                            <td class="github-detail-value"><?= h($trainee->address) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Strengths') ?></th>
                            <td class="github-detail-value"><?= h($trainee->strengths) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Weaknesses') ?></th>
                            <td class="github-detail-value"><?= h($trainee->weaknesses) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Hobby') ?></th>
                            <td class="github-detail-value"><?= h($trainee->hobby) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Application Reasons') ?></th>
                            <td class="github-detail-value"><?= h($trainee->application_reasons) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Expected Work Upon Returning To Japan') ?></th>
                            <td class="github-detail-value"><?= h($trainee->expected_work_upon_returning_to_japan) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Blood Type Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_blood_type') ? $this->Html->link($trainee->master_blood_type->title, ['controller' => 'MasterBloodTypes', 'action' => 'view', $trainee->master_blood_type->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Explain Eye Condition') ?></th>
                            <td class="github-detail-value"><?= h($trainee->explain_eye_condition) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Explain Color Blind') ?></th>
                            <td class="github-detail-value"><?= h($trainee->explain_color_blind) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Whatsapp') ?></th>
                            <td class="github-detail-value"><?= h($trainee->link_whatsapp) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Line') ?></th>
                            <td class="github-detail-value"><?= h($trainee->link_line) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Instagram') ?></th>
                            <td class="github-detail-value"><?= h($trainee->link_instagram) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Facebook') ?></th>
                            <td class="github-detail-value"><?= h($trainee->link_facebook) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Tiktok') ?></th>
                            <td class="github-detail-value"><?= h($trainee->link_tiktok) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Rejected Reason Id') ?></th>
                            <td class="github-detail-value">
                                <?= $trainee->has('master_rejected_reason') ? $this->Html->link($trainee->master_rejected_reason->title, ['controller' => 'MasterRejectedReasons', 'action' => 'view', $trainee->master_rejected_reason->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Specific Rejected Reason') ?></th>
                            <td class="github-detail-value"><?= h($trainee->specific_rejected_reason) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Birth Date') ?></th>
                            <td class="github-detail-value"><?= h($trainee->birth_date) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Ever Went To Japan') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_ever_went_to_japan ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_ever_went_to_japan ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Will Go To Japan After Finished') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->will_go_to_japan_after_finished ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->will_go_to_japan_after_finished ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Holding Passport') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_holding_passport ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_holding_passport ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Wear Eye Glasses') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_wear_eye_glasses ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_wear_eye_glasses ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Color Blind') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_color_blind ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_color_blind ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Right Handed') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_right_handed ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_right_handed ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Smoking') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_smoking ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_smoking ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Drinking Alcohol') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_drinking_alcohol ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_drinking_alcohol ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Tattooed') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_tattooed ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_tattooed ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Training Pass') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_training_pass ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_training_pass ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Apprenticeship Pass') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $trainee->is_apprenticeship_pass ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $trainee->is_apprenticeship_pass ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            </div>
            <!-- End Home Tab -->

            <!-- TraineeCertifications Tab -->
            <div id="tab-trainee_certifications" class="view-tab-pane">
                <?php if (!empty($trainee->trainee_certifications)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related TraineeCertifications') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New TraineeCertification'),
                                ['controller' => 'TraineeCertifications', 'action' => 'add'],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-related-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover github-related-table" data-ajax-filter="true">
                                <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                                    <tr>
                                        <th class="actions-column-header" style="width: 15px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th><?= __('Id') ?></th>
                                        <th><?= __('Trainee Id') ?></th>
                                        <th><?= __('Title') ?></th>
                                        <th><?= __('Institution Name') ?></th>
                                        <th><?= __('Certification Date') ?></th>
                                    </tr>
                                    <!-- Filter Row -->
                                    <tr class="filter-row" style="background-color: #f8f9fa;">
                                        <th class="actions-column-header" style="width: 15px; padding: 4px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Id') ?>" 
                                                       data-column="id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="trainee_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Trainee Id') ?>" 
                                                       data-column="trainee_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="title" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Title') ?>" 
                                                       data-column="title"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="institution_name" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Institution Name') ?>" 
                                                       data-column="institution_name"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="certification_date" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Certification Date') ?>" 
                                                       data-column="certification_date"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trainee->trainee_certifications as $traineeCertifications): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'TraineeCertifications', 'action' => 'edit', $traineeCertifications->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'TraineeCertifications', 'action' => 'view', $traineeCertifications->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($traineeCertifications->id) ?></td>
                                        <td>
                                            <?php if ($traineeCertifications->has('trainee')): ?>
                                                <?php
                                                    $displayValue = $traineeCertifications->trainee->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeCertifications->trainee->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeCertifications->trainee_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Trainees', 'action' => 'view', $traineeCertifications->trainee_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeCertifications->trainee_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($traineeCertifications->title) ?></td>
                                        <td><?= h($traineeCertifications->institution_name) ?></td>
                                        <td><?= h($traineeCertifications->certification_date) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                    </svg>
                    <h3><?= __('No Related TraineeCertifications') ?></h3>
                    <p><?= __('There are no related TraineeCertifications for this Trainee.') ?></p>
                    <?= $this->Html->link(
                        __('Add First TraineeCertification'),
                        ['controller' => 'TraineeCertifications', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- TraineeCourses Tab -->
            <div id="tab-trainee_courses" class="view-tab-pane">
                <?php if (!empty($trainee->trainee_courses)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related TraineeCourses') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New TraineeCourse'),
                                ['controller' => 'TraineeCourses', 'action' => 'add'],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-related-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover github-related-table" data-ajax-filter="true">
                                <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                                    <tr>
                                        <th class="actions-column-header" style="width: 15px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th><?= __('Id') ?></th>
                                        <th><?= __('Trainee Id') ?></th>
                                        <th><?= __('Vocational Training Institution Id') ?></th>
                                        <th><?= __('Course Major Id') ?></th>
                                        <th><?= __('Course Year') ?></th>
                                    </tr>
                                    <!-- Filter Row -->
                                    <tr class="filter-row" style="background-color: #f8f9fa;">
                                        <th class="actions-column-header" style="width: 15px; padding: 4px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Id') ?>" 
                                                       data-column="id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="trainee_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Trainee Id') ?>" 
                                                       data-column="trainee_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="vocational_training_institution_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Vocational Training Institution Id') ?>" 
                                                       data-column="vocational_training_institution_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="course_major_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Course Major Id') ?>" 
                                                       data-column="course_major_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="course_year" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Course Year') ?>" 
                                                       data-column="course_year"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trainee->trainee_courses as $traineeCourses): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'TraineeCourses', 'action' => 'edit', $traineeCourses->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'TraineeCourses', 'action' => 'view', $traineeCourses->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($traineeCourses->id) ?></td>
                                        <td>
                                            <?php if ($traineeCourses->has('trainee')): ?>
                                                <?php
                                                    $displayValue = $traineeCourses->trainee->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeCourses->trainee->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeCourses->trainee_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Trainees', 'action' => 'view', $traineeCourses->trainee_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeCourses->trainee_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($traineeCourses->has('vocationalTrainingInstitution')): ?>
                                                <?php
                                                    $displayValue = $traineeCourses->vocationalTrainingInstitution->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeCourses->vocationalTrainingInstitution->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeCourses->vocational_training_institution_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'VocationalTrainingInstitutions', 'action' => 'view', $traineeCourses->vocational_training_institution_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeCourses->vocational_training_institution_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($traineeCourses->has('courseMajor')): ?>
                                                <?php
                                                    $displayValue = $traineeCourses->courseMajor->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeCourses->courseMajor->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeCourses->course_major_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'CourseMajors', 'action' => 'view', $traineeCourses->course_major_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeCourses->course_major_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($traineeCourses->course_year) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                    </svg>
                    <h3><?= __('No Related TraineeCourses') ?></h3>
                    <p><?= __('There are no related TraineeCourses for this Trainee.') ?></p>
                    <?= $this->Html->link(
                        __('Add First TraineeCourse'),
                        ['controller' => 'TraineeCourses', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- TraineeEducations Tab -->
            <div id="tab-trainee_educations" class="view-tab-pane">
                <?php if (!empty($trainee->trainee_educations)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related TraineeEducations') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New TraineeEducation'),
                                ['controller' => 'TraineeEducations', 'action' => 'add'],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-related-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover github-related-table" data-ajax-filter="true">
                                <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                                    <tr>
                                        <th class="actions-column-header" style="width: 15px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th><?= __('Id') ?></th>
                                        <th><?= __('Trainee Id') ?></th>
                                        <th><?= __('Master Strata Id') ?></th>
                                        <th><?= __('Master Propinsi Id') ?></th>
                                        <th><?= __('Master Kabupaten Id') ?></th>
                                    </tr>
                                    <!-- Filter Row -->
                                    <tr class="filter-row" style="background-color: #f8f9fa;">
                                        <th class="actions-column-header" style="width: 15px; padding: 4px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Id') ?>" 
                                                       data-column="id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="trainee_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Trainee Id') ?>" 
                                                       data-column="trainee_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="master_strata_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Master Strata Id') ?>" 
                                                       data-column="master_strata_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="master_propinsi_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Master Propinsi Id') ?>" 
                                                       data-column="master_propinsi_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="master_kabupaten_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Master Kabupaten Id') ?>" 
                                                       data-column="master_kabupaten_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trainee->trainee_educations as $traineeEducations): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'TraineeEducations', 'action' => 'edit', $traineeEducations->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'TraineeEducations', 'action' => 'view', $traineeEducations->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($traineeEducations->id) ?></td>
                                        <td>
                                            <?php if ($traineeEducations->has('trainee')): ?>
                                                <?php
                                                    $displayValue = $traineeEducations->trainee->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeEducations->trainee->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeEducations->trainee_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Trainees', 'action' => 'view', $traineeEducations->trainee_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeEducations->trainee_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($traineeEducations->has('masterStrata')): ?>
                                                <?php
                                                    $displayValue = $traineeEducations->masterStrata->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeEducations->masterStrata->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeEducations->master_strata_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterStratas', 'action' => 'view', $traineeEducations->master_strata_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeEducations->master_strata_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($traineeEducations->has('masterPropinsi')): ?>
                                                <?php
                                                    $displayValue = $traineeEducations->masterPropinsi->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeEducations->masterPropinsi->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeEducations->master_propinsi_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterPropinsis', 'action' => 'view', $traineeEducations->master_propinsi_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeEducations->master_propinsi_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($traineeEducations->has('masterKabupaten')): ?>
                                                <?php
                                                    $displayValue = $traineeEducations->masterKabupaten->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeEducations->masterKabupaten->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeEducations->master_kabupaten_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterKabupatens', 'action' => 'view', $traineeEducations->master_kabupaten_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeEducations->master_kabupaten_id) ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                    </svg>
                    <h3><?= __('No Related TraineeEducations') ?></h3>
                    <p><?= __('There are no related TraineeEducations for this Trainee.') ?></p>
                    <?= $this->Html->link(
                        __('Add First TraineeEducation'),
                        ['controller' => 'TraineeEducations', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- TraineeExperiences Tab -->
            <div id="tab-trainee_experiences" class="view-tab-pane">
                <?php if (!empty($trainee->trainee_experiences)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related TraineeExperiences') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New TraineeExperience'),
                                ['controller' => 'TraineeExperiences', 'action' => 'add'],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-related-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover github-related-table" data-ajax-filter="true">
                                <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                                    <tr>
                                        <th class="actions-column-header" style="width: 15px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th><?= __('Id') ?></th>
                                        <th><?= __('Trainee Id') ?></th>
                                        <th><?= __('Employment Start Year') ?></th>
                                        <th><?= __('Employment End Year') ?></th>
                                        <th><?= __('Company Name') ?></th>
                                    </tr>
                                    <!-- Filter Row -->
                                    <tr class="filter-row" style="background-color: #f8f9fa;">
                                        <th class="actions-column-header" style="width: 15px; padding: 4px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Id') ?>" 
                                                       data-column="id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="trainee_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Trainee Id') ?>" 
                                                       data-column="trainee_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="employment_start_year" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Employment Start Year') ?>" 
                                                       data-column="employment_start_year"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="employment_end_year" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Employment End Year') ?>" 
                                                       data-column="employment_end_year"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="company_name" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Company Name') ?>" 
                                                       data-column="company_name"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trainee->trainee_experiences as $traineeExperiences): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'TraineeExperiences', 'action' => 'edit', $traineeExperiences->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'TraineeExperiences', 'action' => 'view', $traineeExperiences->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($traineeExperiences->id) ?></td>
                                        <td>
                                            <?php if ($traineeExperiences->has('trainee')): ?>
                                                <?php
                                                    $displayValue = $traineeExperiences->trainee->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeExperiences->trainee->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeExperiences->trainee_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Trainees', 'action' => 'view', $traineeExperiences->trainee_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeExperiences->trainee_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($traineeExperiences->employment_start_year) ?></td>
                                        <td><?= h($traineeExperiences->employment_end_year) ?></td>
                                        <td><?= h($traineeExperiences->company_name) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                    </svg>
                    <h3><?= __('No Related TraineeExperiences') ?></h3>
                    <p><?= __('There are no related TraineeExperiences for this Trainee.') ?></p>
                    <?= $this->Html->link(
                        __('Add First TraineeExperience'),
                        ['controller' => 'TraineeExperiences', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- TraineeFamilies Tab -->
            <div id="tab-trainee_families" class="view-tab-pane">
                <?php if (!empty($trainee->trainee_families)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related TraineeFamilies') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New TraineeFamily'),
                                ['controller' => 'TraineeFamilies', 'action' => 'add'],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-related-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover github-related-table" data-ajax-filter="true">
                                <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                                    <tr>
                                        <th class="actions-column-header" style="width: 15px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th><?= __('Id') ?></th>
                                        <th><?= __('Trainee Id') ?></th>
                                        <th><?= __('Master Family Connection Id') ?></th>
                                        <th><?= __('Name') ?></th>
                                        <th><?= __('Age') ?></th>
                                    </tr>
                                    <!-- Filter Row -->
                                    <tr class="filter-row" style="background-color: #f8f9fa;">
                                        <th class="actions-column-header" style="width: 15px; padding: 4px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Id') ?>" 
                                                       data-column="id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="trainee_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Trainee Id') ?>" 
                                                       data-column="trainee_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="master_family_connection_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Master Family Connection Id') ?>" 
                                                       data-column="master_family_connection_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="name" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Name') ?>" 
                                                       data-column="name"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="age" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Age') ?>" 
                                                       data-column="age"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trainee->trainee_families as $traineeFamilies): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'TraineeFamilies', 'action' => 'edit', $traineeFamilies->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'TraineeFamilies', 'action' => 'view', $traineeFamilies->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($traineeFamilies->id) ?></td>
                                        <td>
                                            <?php if ($traineeFamilies->has('trainee')): ?>
                                                <?php
                                                    $displayValue = $traineeFamilies->trainee->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeFamilies->trainee->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeFamilies->trainee_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Trainees', 'action' => 'view', $traineeFamilies->trainee_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeFamilies->trainee_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($traineeFamilies->has('masterFamilyConnection')): ?>
                                                <?php
                                                    $displayValue = $traineeFamilies->masterFamilyConnection->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeFamilies->masterFamilyConnection->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeFamilies->master_family_connection_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterFamilyConnections', 'action' => 'view', $traineeFamilies->master_family_connection_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeFamilies->master_family_connection_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($traineeFamilies->name) ?></td>
                                        <td><?= h($traineeFamilies->age) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                    </svg>
                    <h3><?= __('No Related TraineeFamilies') ?></h3>
                    <p><?= __('There are no related TraineeFamilies for this Trainee.') ?></p>
                    <?= $this->Html->link(
                        __('Add First TraineeFamily'),
                        ['controller' => 'TraineeFamilies', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- TraineeFamilyStories Tab -->
            <div id="tab-trainee_family_stories" class="view-tab-pane">
                <?php if (!empty($trainee->trainee_family_stories)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related TraineeFamilyStories') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New TraineeFamilyStory'),
                                ['controller' => 'TraineeFamilyStories', 'action' => 'add'],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-related-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover github-related-table" data-ajax-filter="true">
                                <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                                    <tr>
                                        <th class="actions-column-header" style="width: 15px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th><?= __('Id') ?></th>
                                        <th><?= __('Trainee Id') ?></th>
                                        <th><?= __('Title') ?></th>
                                        <th><?= __('Date Occurrence') ?></th>
                                        <th><?= __('Problem Contents') ?></th>
                                    </tr>
                                    <!-- Filter Row -->
                                    <tr class="filter-row" style="background-color: #f8f9fa;">
                                        <th class="actions-column-header" style="width: 15px; padding: 4px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Id') ?>" 
                                                       data-column="id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="trainee_id" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Trainee Id') ?>" 
                                                       data-column="trainee_id"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="title" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Title') ?>" 
                                                       data-column="title"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="date_occurrence" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Date Occurrence') ?>" 
                                                       data-column="date_occurrence"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="problem_contents" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter Problem Contents') ?>" 
                                                       data-column="problem_contents"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trainee->trainee_family_stories as $traineeFamilyStories): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'TraineeFamilyStories', 'action' => 'edit', $traineeFamilyStories->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'TraineeFamilyStories', 'action' => 'view', $traineeFamilyStories->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($traineeFamilyStories->id) ?></td>
                                        <td>
                                            <?php if ($traineeFamilyStories->has('trainee')): ?>
                                                <?php
                                                    $displayValue = $traineeFamilyStories->trainee->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeFamilyStories->trainee->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $traineeFamilyStories->trainee_id;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Trainees', 'action' => 'view', $traineeFamilyStories->trainee_id]) ?>
                                            <?php else: ?>
                                                <?= h($traineeFamilyStories->trainee_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($traineeFamilyStories->title) ?></td>
                                        <td><?= h($traineeFamilyStories->date_occurrence) ?></td>
                                        <td><?= h($traineeFamilyStories->problem_contents) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                    </svg>
                    <h3><?= __('No Related TraineeFamilyStories') ?></h3>
                    <p><?= __('There are no related TraineeFamilyStories for this Trainee.') ?></p>
                    <?= $this->Html->link(
                        __('Add First TraineeFamilyStory'),
                        ['controller' => 'TraineeFamilyStories', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- Candidate Tab (BelongsTo) -->
            <div id="tab-candidates" class="view-tab-pane">
                <?php if ($trainee->has('candidate')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('Candidate Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'Candidates', 'action' => 'view', $trainee->candidate->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'Candidates', 'action' => 'edit', $trainee->candidate->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Apprenticeship Order Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->apprentice_order_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Candidate Code') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->candidate_code) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Vocational Training Institution Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->vocational_training_institution_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Acceptance Organization Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->acceptance_organization_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Identity Number') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->identity_number) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Name') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->name) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Name Katakana') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->name_katakana) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Master Gender Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->master_gender_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Master Religion Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->candidate->master_religion_id) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No Candidate Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any Candidate.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- ApprenticeOrder Tab (BelongsTo) -->
            <div id="tab-apprentice_orders" class="view-tab-pane">
                <?php if ($trainee->has('apprentice_order')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('ApprenticeOrder Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'ApprenticeOrders', 'action' => 'view', $trainee->apprentice_order->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'ApprenticeOrders', 'action' => 'edit', $trainee->apprentice_order->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Reference File') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->reference_file) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Cooperative Association Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->cooperative_association_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Acceptance Organization Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->acceptance_organization_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->title) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Male Trainee Number') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->male_trainee_number) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Female Trainee Number') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->female_trainee_number) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Master Job Category Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->master_job_category_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Departure Year') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->departure_year) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Departure Month') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->apprentice_order->departure_month) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No ApprenticeOrder Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any ApprenticeOrder.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- VocationalTrainingInstitution Tab (BelongsTo) -->
            <div id="tab-vocational_training_institutions" class="view-tab-pane">
                <?php if ($trainee->has('vocational_training_institution')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('VocationalTrainingInstitution Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'VocationalTrainingInstitutions', 'action' => 'view', $trainee->vocational_training_institution->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'VocationalTrainingInstitutions', 'action' => 'edit', $trainee->vocational_training_institution->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Is Special Skill Support Institution') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->is_special_skill_support_institution) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Abbreviation') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->abbreviation) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Name') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->name) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Master Propinsi Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->master_propinsi_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Master Kabupaten Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->master_kabupaten_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Master Kecamatan Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->master_kecamatan_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Master Kelurahan Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->master_kelurahan_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Address') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->address) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Post Code') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->vocational_training_institution->post_code) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No VocationalTrainingInstitution Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any VocationalTrainingInstitution.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- AcceptanceOrganization Tab (BelongsTo) -->
            <div id="tab-acceptance_organizations" class="view-tab-pane">
                <?php if ($trainee->has('acceptance_organization')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('AcceptanceOrganization Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'AcceptanceOrganizations', 'action' => 'view', $trainee->acceptance_organization->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'AcceptanceOrganizations', 'action' => 'edit', $trainee->acceptance_organization->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->acceptance_organization->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->acceptance_organization->title) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Master Japan Prefecture Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->acceptance_organization->master_japan_prefecture_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Post Code') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->acceptance_organization->post_code) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Address') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->acceptance_organization->address) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Director') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->acceptance_organization->director) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Director Hiragana') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->acceptance_organization->director_hiragana) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No AcceptanceOrganization Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any AcceptanceOrganization.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterGender Tab (BelongsTo) -->
            <div id="tab-master_genders" class="view-tab-pane">
                <?php if ($trainee->has('master_gender')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterGender Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterGenders', 'action' => 'view', $trainee->master_gender->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterGenders', 'action' => 'edit', $trainee->master_gender->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_gender->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_gender->title) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title Jpg') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_gender->title_jpg) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterGender Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterGender.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterReligion Tab (BelongsTo) -->
            <div id="tab-master_religions" class="view-tab-pane">
                <?php if ($trainee->has('master_religion')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterReligion Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterReligions', 'action' => 'view', $trainee->master_religion->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterReligions', 'action' => 'edit', $trainee->master_religion->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_religion->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Slug') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_religion->slug) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_religion->title) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title Jpg') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_religion->title_jpg) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterReligion Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterReligion.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterMarriageStatus Tab (BelongsTo) -->
            <div id="tab-master_marriage_statuses" class="view-tab-pane">
                <?php if ($trainee->has('master_marriage_status')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterMarriageStatus Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterMarriageStatuses', 'action' => 'view', $trainee->master_marriage_status->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterMarriageStatuses', 'action' => 'edit', $trainee->master_marriage_status->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_marriage_status->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Slug') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_marriage_status->slug) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_marriage_status->title) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title Jpg') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_marriage_status->title_jpg) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterMarriageStatus Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterMarriageStatus.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterPropinsi Tab (BelongsTo) -->
            <div id="tab-master_propinsis" class="view-tab-pane">
                <?php if ($trainee->has('master_propinsi')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterPropinsi Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterPropinsis', 'action' => 'view', $trainee->master_propinsi->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterPropinsis', 'action' => 'edit', $trainee->master_propinsi->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_propinsi->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Propinsi') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_propinsi->kode_propinsi) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_propinsi->title) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterPropinsi Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterPropinsi.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterKabupaten Tab (BelongsTo) -->
            <div id="tab-master_kabupatens" class="view-tab-pane">
                <?php if ($trainee->has('master_kabupaten')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterKabupaten Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterKabupatens', 'action' => 'view', $trainee->master_kabupaten->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterKabupatens', 'action' => 'edit', $trainee->master_kabupaten->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kabupaten->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Propinsi Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kabupaten->propinsi_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Propinsi') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kabupaten->kode_propinsi) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Kabupaten') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kabupaten->kode_kabupaten) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kabupaten->title) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterKabupaten Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterKabupaten.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterKecamatan Tab (BelongsTo) -->
            <div id="tab-master_kecamatans" class="view-tab-pane">
                <?php if ($trainee->has('master_kecamatan')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterKecamatan Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterKecamatans', 'action' => 'view', $trainee->master_kecamatan->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterKecamatans', 'action' => 'edit', $trainee->master_kecamatan->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kecamatan->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Propinsi Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kecamatan->propinsi_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kabupaten Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kecamatan->kabupaten_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Propinsi') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kecamatan->kode_propinsi) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Kabupaten') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kecamatan->kode_kabupaten) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Kecamatan') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kecamatan->kode_kecamatan) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kecamatan->title) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterKecamatan Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterKecamatan.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterKelurahan Tab (BelongsTo) -->
            <div id="tab-master_kelurahans" class="view-tab-pane">
                <?php if ($trainee->has('master_kelurahan')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterKelurahan Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterKelurahans', 'action' => 'view', $trainee->master_kelurahan->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterKelurahans', 'action' => 'edit', $trainee->master_kelurahan->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Propinsi Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->propinsi_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kabupaten Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->kabupaten_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kecamatan Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->kecamatan_id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Propinsi') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->kode_propinsi) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Kabupaten') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->kode_kabupaten) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Kecamatan') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->kode_kecamatan) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Kelurahan') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->kode_kelurahan) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Kode Pos') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->kode_pos) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_kelurahan->title) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterKelurahan Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterKelurahan.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterBloodType Tab (BelongsTo) -->
            <div id="tab-master_blood_types" class="view-tab-pane">
                <?php if ($trainee->has('master_blood_type')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterBloodType Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterBloodTypes', 'action' => 'view', $trainee->master_blood_type->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterBloodTypes', 'action' => 'edit', $trainee->master_blood_type->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_blood_type->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_blood_type->title) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterBloodType Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterBloodType.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <!-- MasterRejectedReason Tab (BelongsTo) -->
            <div id="tab-master_rejected_reasons" class="view-tab-pane">
                <?php if ($trainee->has('master_rejected_reason')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('MasterRejectedReason Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => 'MasterRejectedReasons', 'action' => 'view', $trainee->master_rejected_reason->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => 'MasterRejectedReasons', 'action' => 'edit', $trainee->master_rejected_reason->id],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
                                <tr>
                                    <th class="github-detail-label"><?= __('Id') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_rejected_reason->id) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_rejected_reason->title) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Title Jpg') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_rejected_reason->title_jpg) ?></td>
                                </tr>
                                <tr>
                                    <th class="github-detail-label"><?= __('Details') ?></th>
                                    <td class="github-detail-value"><?= h($trainee->master_rejected_reason->details) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No MasterRejectedReason Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any MasterRejectedReason.') ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- End Tab Contents -->
    </div>
    <!-- End Tabs Container -->
</div>
<!-- End GitHub Container -->

<script>
// Tab Switching JavaScript - Modal Safe
document.addEventListener('DOMContentLoaded', function() {
    initializeTabSwitching();
});

// Also initialize when content is dynamically loaded (e.g., in modals)
if (typeof window.initializeTabSwitching === 'undefined') {
    window.initializeTabSwitching = function(container) {
        // Find container (for modal context or specific view container)
        const viewContainer = container || document.querySelector('.github-container') || document;
        
        // Use data-view-template attribute to target only the primary view template tabs
        // This prevents selecting nested tabs in child associate data
        const isDocument = (viewContainer === document);
        
        let tabLinks, tabPanes;
        
        if (isDocument) {
            // When querying whole document, find tabs within view-content-wrapper that has data-view-template
            const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
            if (!viewWrapper) {
                console.warn('No view-content-wrapper with data-view-template found');
                return;
            tabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
            tabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
        } else {
            // When container is specified, search within it for the wrapper
            const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
            if (viewWrapper) {
                tabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
                tabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
            } else {
                // Fallback: container might BE the wrapper already
                tabLinks = viewContainer.querySelectorAll('.view-tab-link');
                tabPanes = viewContainer.querySelectorAll('.view-tab-pane');
        
        if (tabLinks.length === 0 || tabPanes.length === 0) {
            console.error('Ã¢ÂÅ’ No tabs found! TabLinks:', tabLinks.length, 'TabPanes:', tabPanes.length);
            console.error('Container:', viewContainer);
            if (isDocument) {
                console.error('View wrapper:', document.querySelector('.view-content-wrapper[data-view-template="true"]'));
            return;
        
        console.log('Ã¢Å“â€¦ SUCCESS: Initializing', tabLinks.length, 'tab links and', tabPanes.length, 'tab panes');
        console.log('Ã°Å¸â€œÂ Container:', isDocument ? 'document (full page)' : 'element (modal/container)');
        
        // Remove existing click handlers to avoid duplicates
        tabLinks.forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
        });
        
        // Re-query after cloning (use same wrapper logic)
        let freshTabLinks, freshTabPanes;
        
        if (isDocument) {
            const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
            freshTabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
            freshTabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
        } else {
            const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
            if (viewWrapper) {
                freshTabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
                freshTabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
            } else {
                freshTabLinks = viewContainer.querySelectorAll('.view-tab-link');
                freshTabPanes = viewContainer.querySelectorAll('.view-tab-pane');
        
        freshTabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Ã°Å¸â€“Â±Ã¯Â¸Â  Tab clicked:', this.getAttribute('data-tab'));
                
                // Get target tab
                const targetTab = this.getAttribute('data-tab');
                
                if (!targetTab) {
                    console.error('Ã¢ÂÅ’ No data-tab attribute found on clicked element');
                    return;
                
                // Remove active class from all tabs and panes in this container
                freshTabLinks.forEach(l => {
                    l.classList.remove('active');
                });
                freshTabPanes.forEach(p => {
                    p.classList.remove('active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Find and activate corresponding pane (search within wrapper to avoid nested tabs)
                let targetPane;
                if (isDocument) {
                    const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
                    targetPane = viewWrapper ? viewWrapper.querySelector('#' + targetTab) : null;
                } else {
                    const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
                    targetPane = viewWrapper ? viewWrapper.querySelector('#' + targetTab) : viewContainer.querySelector('#' + targetTab);
                
                if (targetPane) {
                    targetPane.classList.add('active');
                    console.log('Ã¢Å“â€¦ Activated pane:', targetTab);
                } else {
                    console.error('Ã¢ÂÅ’ Target pane not found:', targetTab);
                    console.error('Available panes:', freshTabPanes ? Array.from(freshTabPanes).map(p => p.id) : 'none');
                
                // Store active tab in sessionStorage (optional)
                try {
                    sessionStorage.setItem('activeViewTab', targetTab);
                } catch (e) {
                    console.warn('sessionStorage not available:', e);
            });
        });
        
        // Restore active tab from sessionStorage
        try {
            const savedTab = sessionStorage.getItem('activeViewTab');
            if (savedTab) {
                // Search within wrapper to find saved tab
                let targetPane, savedLink;
                
                if (isDocument) {
                    const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
                    if (viewWrapper) {
                        targetPane = viewWrapper.querySelector('#' + savedTab);
                        savedLink = viewWrapper.querySelector('[data-tab="' + savedTab + '"]');
                } else {
                    const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
                    if (viewWrapper) {
                        targetPane = viewWrapper.querySelector('#' + savedTab);
                        savedLink = viewWrapper.querySelector('[data-tab="' + savedTab + '"]');
                    } else {
                        targetPane = viewContainer.querySelector('#' + savedTab);
                        savedLink = viewContainer.querySelector('[data-tab="' + savedTab + '"]');
                
                if (targetPane && savedLink) {
                    savedLink.click();
        } catch (e) {
            console.warn('Could not restore tab from sessionStorage:', e);
    };

// Drag to scroll for tabs navigation
document.addEventListener('DOMContentLoaded', function() {
    // Only target view template tabs, not main navigation
    const tabsNav = document.querySelector('.github-container .view-tabs-nav');
    if (!tabsNav) return;
    
    let isDown = false;
    let startX;
    let scrollLeft;
    
    tabsNav.addEventListener('mousedown', (e) => {
        // Only activate drag on empty areas, not on links
        if (e.target.closest('.view-tab-link')) return;
        
        isDown = true;
        tabsNav.classList.add('dragging');
        startX = e.pageX - tabsNav.offsetLeft;
        scrollLeft = tabsNav.scrollLeft;
        tabsNav.style.cursor = 'grabbing';
    });
    
    tabsNav.addEventListener('mouseleave', () => {
        isDown = false;
        tabsNav.classList.remove('dragging');
        tabsNav.style.cursor = 'grab';
    });
    
    tabsNav.addEventListener('mouseup', () => {
        isDown = false;
        tabsNav.classList.remove('dragging');
        tabsNav.style.cursor = 'grab';
    });
    
    tabsNav.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - tabsNav.offsetLeft;
        const walk = (x - startX) * 2; // Scroll speed multiplier
        tabsNav.scrollLeft = scrollLeft - walk;
    });
    
    // Initialize tab switching after DOM is ready
    window.initializeTabSwitching();
});

// Auto-initialize tabs in Bootstrap modals - Multiple detection methods
// Method 1: Bootstrap modal event (jQuery)
if (typeof jQuery !== 'undefined') {
    jQuery(document).on('shown.bs.modal', '.modal', function(e) {
        console.log('Modal opened (jQuery event), checking for tabs...');
        const modal = this;
        
        // Wait a bit for content to settle (especially for AJAX-loaded modals)
        setTimeout(function() {
            const modalBody = modal.querySelector('.modal-body') || modal.querySelector('.modal-content');
            if (modalBody) {
                // Look for any tabs, not just those with data-view-template
                const tabLinks = modalBody.querySelectorAll('.view-tab-link');
                if (tabLinks.length > 0) {
                    console.log('Found', tabLinks.length, 'tab links in modal, initializing...');
                    window.initializeTabSwitching(modalBody);
                } else {
                    console.log('No tab links found in modal');
        }, 100);
    });
    
    // Also listen for show event (before animation)
    jQuery(document).on('show.bs.modal', '.modal', function(e) {
        console.log('Modal showing (before animation)...');
    });

// Method 2: Vanilla JavaScript modal event (Bootstrap 5)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up modal observers...');
    
    // Listen for all modals
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            console.log('Modal opened (vanilla event), checking for tabs...');
            const modalBody = this.querySelector('.modal-body') || this.querySelector('.modal-content');
            if (modalBody) {
                const tabLinks = modalBody.querySelectorAll('.view-tab-link');
                if (tabLinks.length > 0) {
                    console.log('Found', tabLinks.length, 'tab links in modal, initializing...');
                    window.initializeTabSwitching(modalBody);
        });
    });
    
    // Method 3: MutationObserver for dynamically added modals or content
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    // Check if the added node is a modal or contains a modal
                    let modals = [];
                    if (node.classList && node.classList.contains('modal')) {
                        modals.push(node);
                    modals.push(...node.querySelectorAll('.modal'));
                    
                    modals.forEach(function(modal) {
                        // Check if modal is visible and has tabs
                        if (modal.classList.contains('show') || modal.style.display !== 'none') {
                            const modalBody = modal.querySelector('.modal-body') || modal.querySelector('.modal-content');
                            if (modalBody) {
                                const tabLinks = modalBody.querySelectorAll('.view-tab-link');
                                if (tabLinks.length > 0) {
                                    console.log('MutationObserver: Found modal with', tabLinks.length, 'tabs, initializing...');
                                    window.initializeTabSwitching(modalBody);
                    });
                    
                    // Also check if tabs were added to an existing modal
                    if (node.closest('.modal-body')) {
                        const tabLinks = node.querySelectorAll('.view-tab-link');
                        if (tabLinks.length > 0) {
                            console.log('MutationObserver: Tabs added to modal, initializing...');
                            const modalBody = node.closest('.modal-body');
                            window.initializeTabSwitching(modalBody);
            });
        });
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    console.log('MutationObserver active for modal content detection');
});

// Method 4: Global function for manual initialization
window.initializeModalTabs = function(modalSelector) {
    const modal = typeof modalSelector === 'string' 
        ? document.querySelector(modalSelector) 
        : modalSelector;
    
    if (modal) {
        const modalBody = modal.querySelector('.modal-body') || modal.querySelector('.modal-content');
        if (modalBody) {
            console.log('Manual initialization requested for modal');
            window.initializeTabSwitching(modalBody);
};

// Usage examples in comments:
// After loading modal content via AJAX: window.initializeModalTabs('#myModal');
// Or pass the element directly: window.initializeModalTabs(document.getElementById('myModal'));
</script>

<style>
/* View Content Wrapper - Modal Safe Isolation */
.view-content-wrapper {
    position: relative !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;

/* Ensure tabs work in modals - override Bootstrap modal styles */
.modal .view-content-wrapper,
.modal-body .view-content-wrapper {
    overflow: visible !important;
    transform: none !important;

.modal .view-tabs-container,
.modal-body .view-tabs-container {
    overflow: visible !important;

.modal .view-tabs-nav,
.modal-body .view-tabs-nav {
    flex-wrap: nowrap !important;
    transform: none !important;

/* Fix Bootstrap modal conflicts */
.modal a.view-tab-link,
.modal-body a.view-tab-link {
    color: #586069 !important;

.modal a.view-tab-link.active,
.modal-body a.view-tab-link.active {
    color: #0969da !important;

.modal a.view-tab-link:hover,
.modal-body a.view-tab-link:hover {
    color: #24292f !important;
    text-decoration: none !important;

/* Button spacing for view actions */
.github-header-actions .btn {
    margin-right: 8px;

.github-header-actions .btn:last-child {
    margin-right: 0;

/* Tab Navigation Styles - Modal Safe with !important overrides */
.view-tabs-container {
    margin: 20px 0 !important;
    position: relative !important;
    z-index: 1 !important;
    background: #ffffff !important;
    border-radius: 6px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12) !important;
    border: 1px solid #d1d5da !important;
    overflow: hidden !important;
    display: block !important;

/* Override any modal/page styles that might affect tabs */
.modal .view-tabs-container,
.modal-body .view-tabs-container {
    margin: 20px 0 !important;
    width: 100% !important;
    max-width: 100% !important;

.view-tabs-nav {
    display: flex !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    border-bottom: 2px solid #e1e4e8 !important;
    background: #fafbfc !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    scroll-behavior: smooth !important;
    position: relative !important;
    z-index: 1 !important;
    flex-wrap: nowrap !important;

.view-tabs-nav.dragging {
    cursor: grabbing !important;
    scroll-behavior: auto !important;

/* Transparent scrollbar */
.view-tabs-nav::-webkit-scrollbar {
    height: 8px !important;

.view-tabs-nav::-webkit-scrollbar-track {
    background: transparent !important;

.view-tabs-nav::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1) !important;
    border-radius: 4px !important;

.view-tabs-nav::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.2) !important;

/* Firefox */
.view-tabs-nav {
    scrollbar-width: thin !important;
    scrollbar-color: rgba(0, 0, 0, 0.1) transparent !important;

.view-tab-item {
    margin: 0 !important;
    flex-shrink: 0 !important;
    display: block !important;

.view-tab-link {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 14px 24px !important;
    color: #586069 !important;
    text-decoration: none !important;
    border-bottom: 3px solid transparent !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    font-weight: 500 !important;
    background: transparent !important;
    border-right: 1px solid rgba(225, 228, 232, 0.5) !important;
    position: relative !important;
    width: auto !important;
    height: auto !important;
    line-height: 1.5 !important;

.view-tab-link:last-child {
    border-right: none !important;

.view-tab-link:hover {
    color: #24292f !important;
    background: rgba(175, 184, 193, 0.2) !important;
    border-bottom-color: #959da5 !important;
    text-decoration: none !important;

.view-tab-link.active {
    color: #0969da !important;
    border-bottom-color: #0969da !important;
    font-weight: 600 !important;
    background: #ffffff !important;
    z-index: 2 !important;

.view-tab-link.active::after {
    content: '' !important;
    position: absolute !important;
    bottom: -2px !important;
    left: 0 !important;
    right: 0 !important;
    height: 2px !important;
    background: #ffffff !important;

.tab-icon {
    flex-shrink: 0 !important;
    display: inline-block !important;

.tab-badge {
    display: inline-block !important;
    padding: 3px 10px !important;
    background: #e1e4e8 !important;
    border-radius: 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #586069 !important;
    min-width: 20px !important;
    text-align: center !important;

.view-tab-link.active .tab-badge {
    background: #0969da !important;
    color: #ffffff !important;

.view-tabs-content {
    margin-top: 20px !important;
    display: block !important;
    width: 100% !important;

.view-tab-pane {
    display: none !important;
    width: 100% !important;

.view-tab-pane.active {
    display: block !important;

/* Empty State */
.github-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--github-canvas-default, #ffffff);
    border: 1px solid var(--github-border-default, #d0d7de);
    border-radius: 12px;

.github-empty-state .empty-icon {
    opacity: 0.3;
    margin-bottom: 16px;

.github-empty-state h3 {
    margin: 0 0 8px;
    font-size: 18px;
    color: var(--github-fg-default, #24292f);

.github-empty-state p {
    margin: 0 0 20px;
    color: var(--github-fg-muted, #656d76);

/* GitHub Style View CSS */
.github-view-container {
    display: flex;
    flex-direction: column;
    gap: 20px;

.github-details-card,
.github-related-card {
    background: var(--github-canvas-default, #ffffff);
    border: 1px solid var(--github-border-default, #d0d7de);
    border-radius: 12px;
    overflow: hidden;

.github-details-header,
.github-related-header {
    padding: 16px 20px;
    background: var(--github-canvas-subtle, #f6f8fa);
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);
    display: flex;
    align-items: center;
    justify-content: space-between;

.github-details-title,
.github-related-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--github-fg-default, #24292f);
    width: 100%;
    overflow: hidden;

.github-related-title::after {
    content: "";
    display: block;
    clear: both;

.github-details-body,
.github-related-body {
    padding: 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;

.github-details-table {
    width: 100%;
    border-collapse: collapse;

.github-details-table tr {
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);

.github-details-table tr:last-child {
    border-bottom: none;

.github-detail-label {
    width: 200px;
    padding: 16px 20px;
    font-weight: 600;
    color: var(--github-fg-default, #24292f);
    background: var(--github-canvas-subtle, #f6f8fa);
    text-align: left;
    vertical-align: top;

.github-detail-value {
    padding: 16px 20px;
    color: var(--github-fg-default, #24292f);

.github-link {
    color: var(--github-accent-fg, #0969da);
    text-decoration: none;

.github-link:hover {
    text-decoration: underline;

.github-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;

.badge-success {
    background: rgba(26, 127, 55, 0.1);
    color: #1a7f37;

.badge-secondary {
    background: var(--github-canvas-subtle, #f6f8fa);
    color: var(--github-fg-muted, #656d76);

.github-related-table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;

.github-related-table thead th {
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);
    white-space: nowrap;
    background: transparent;
    position: sticky;
    top: 0;
    z-index: 10;

.github-related-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);

.github-related-table tbody tr:last-child td {
    border-bottom: none;

.github-related-actions {
    display: flex;
    gap: 4px;

/* Mobile Full Width Optimization */
@media (max-width: 768px) {
    .content-wrapper,
    .github-details-card,
    .github-related-card,
    .github-form-card,
    .inventories.index.content {
        margin: 0 8px;
        width: calc(100% - 16px);
        max-width: 100%;

    body {
        padding: 0;
        margin: 0;

    .container {
        padding: 0 8px;
    
    /* Mobile Tab Styles */
    .view-tab-link {
        padding: 10px 16px;
        font-size: 14px;
    
    .tab-icon {
        width: 14px;
        height: 14px;
    
    .tab-badge {
        font-size: 11px;
        padding: 1px 6px;
    
    .github-detail-label {
        width: 120px;
        padding: 12px 16px;
        font-size: 14px;
    
    .github-detail-value {
        padding: 12px 16px;
        font-size: 14px;
    
    .github-related-table {
        font-size: 14px;
    
    .github-related-actions {
        flex-direction: column;
        gap: 4px;
    
    .github-related-actions .github-btn {
        width: 100%;
        text-align: center;

@media (max-width: 480px) {
    .view-tab-link {
        padding: 8px 12px;
        font-size: 13px;
    
    .github-detail-label {
        width: 100px;
        padding: 10px 12px;
        font-size: 13px;
    
    .github-detail-value {
        padding: 10px 12px;
        font-size: 13px;

/* Action Buttons Styles (same as index page) */
.action-buttons-hover {
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
    display: flex;
    gap: 2px;

.table-row-with-actions:hover .action-buttons-hover {
    opacity: 1;

.actions-column {
    background-color: #fff !important;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);

.table-striped tbody tr:nth-of-type(odd) .actions-column {
    background-color: rgba(102, 126, 234, 0.05) !important;

.table-striped tbody tr:nth-of-type(even) .actions-column {
    background-color: #fff !important;

.table-row-with-actions:hover .actions-column {
    background-color: rgba(102, 126, 234, 0.08) !important;

.btn-action-icon {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 8px;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 6px;
    font-size: 11px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    margin: 0 2px;

.btn-action-icon:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    text-decoration: none;

/* Filter Row Styles */
.filter-row input.filter-input {
    width: 100%;
    padding: 4px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 12px;

.filter-row input.filter-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);

/* Drag to scroll for tables */
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;

.table-responsive:active {
    cursor: grabbing;

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
</style>

<script>
// Drag to scroll functionality for tables
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Don't activate drag on form inputs or buttons
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON' || e.target.closest('a')) {
                return;
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // Scroll speed multiplier
            container.scrollLeft = scrollLeft - walk;
        });
    });
});

// Table Filter JavaScript with Operator Support (client-side filtering for related tables)
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('table[data-ajax-filter="true"]');
    
    tables.forEach(table => {
        const filterInputs = table.querySelectorAll('.filter-input');
        const filterOperators = table.querySelectorAll('.filter-operator');
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr.table-row-with-actions');
        
        // Function to apply filter with operator
        function applyFilter(cellText, filterValue, operator) {
            const cellVal = cellText.trim().toLowerCase();
            const filterVal = filterValue.trim().toLowerCase();
            
            if (filterVal === '') return true;
            
            // Try to parse as numbers for numeric comparisons
            const cellNum = parseFloat(cellVal);
            const filterNum = parseFloat(filterVal);
            const isNumeric = !isNaN(cellNum) && !isNaN(filterNum);
            
            switch(operator) {
                case 'equals':
                    return cellVal === filterVal;
                case 'contains':
                    return cellVal.includes(filterVal);
                case 'starts':
                    return cellVal.startsWith(filterVal);
                case 'ends':
                    return cellVal.endsWith(filterVal);
                case 'gt':
                    return isNumeric ? cellNum > filterNum : cellVal > filterVal;
                case 'lt':
                    return isNumeric ? cellNum < filterNum : cellVal < filterVal;
                case 'gte':
                    return isNumeric ? cellNum >= filterNum : cellVal >= filterVal;
                case 'lte':
                    return isNumeric ? cellNum <= filterNum : cellVal <= filterVal;
                case 'between':
                    // Format: "min,max" or "min-max"
                    const parts = filterVal.split(/[,\-]/);
                    if (parts.length === 2 && isNumeric) {
                        const min = parseFloat(parts[0]);
                        const max = parseFloat(parts[1]);
                        return cellNum >= min && cellNum <= max;
                    return cellVal.includes(filterVal);
                default:
                    return cellVal.includes(filterVal);
        
        // Function to filter all rows
        function filterRows() {
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let shouldShow = true;
                
                // Check all active filters
                filterInputs.forEach(filterInput => {
                    const filterVal = filterInput.value;
                    if (filterVal === '') return;
                    
                    const filterColumn = filterInput.getAttribute('data-column');
                    
                    // Find corresponding operator
                    let operator = 'contains'; // default
                    filterOperators.forEach(op => {
                        if (op.getAttribute('data-column') === filterColumn) {
                            operator = op.value;
                    });
                    
                    // Get column index
                    const headers = Array.from(table.querySelectorAll('thead tr:first-child th'));
                    let filterColIndex = -1;
                    headers.forEach((th, idx) => {
                        if (th.textContent.toLowerCase().includes(filterColumn.replace('_', ' '))) {
                            filterColIndex = idx;
                    });
                    
                    if (filterColIndex >= 0 && cells[filterColIndex]) {
                        const cellText = cells[filterColIndex].textContent;
                        if (!applyFilter(cellText, filterVal, operator)) {
                            shouldShow = false;
                });
                
                row.style.display = shouldShow ? '' : 'none';
            });
        
        // Attach event listeners to inputs and operators
        filterInputs.forEach(input => {
            input.addEventListener('input', filterRows);
        });
        
        filterOperators.forEach(select => {
            select.addEventListener('change', filterRows);
        });
    });
});

// Dropdown Menu Functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.getElementById('dropdownMenuButton');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownButton && dropdownMenu) {
        // Toggle dropdown on button click
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
        });
        
        // Close dropdown after clicking a link (with small delay for navigation)
        dropdownMenu.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                setTimeout(function() {
                    dropdownMenu.classList.remove('show');
                }, 100);
        });
        
        console.log('Ã¢Å“â€¦ Dropdown menu initialized on view page');
});
</script>

<!-- Dropdown Menu CSS -->
<style>
/* Dropdown Menu Styling */
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 200px;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);

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
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    text-decoration: none;

.dropdown-item:hover {
    color: #16181b;
    background-color: #f8f9fa;
    text-decoration: none;

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid #e9ecef;

.dropdown-toggle::after {
    display: none; /* Remove default Bootstrap caret */

/* Position dropdown container relatively */
.github-title-row > div:first-child {
    position: relative;
</style>


    <!-- Related Records Tabs with AJAX Lazy Loading -->
    <div class="related-section mt-4">
        <h3><i class="fas fa-link"></i> <?= __('Related Records') ?></h3>
        
        <ul class="nav nav-tabs" id="relatedTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="traineecertifications-tab" data-bs-toggle="tab" 
                        data-bs-target="#traineecertifications-pane" type="button" role="tab" 
                        aria-controls="traineecertifications-pane" aria-selected="true">
                    <?= __('TraineeCertifications') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="traineecourses-tab" data-bs-toggle="tab" 
                        data-bs-target="#traineecourses-pane" type="button" role="tab" 
                        aria-controls="traineecourses-pane" aria-selected="false">
                    <?= __('TraineeCourses') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="traineeeducations-tab" data-bs-toggle="tab" 
                        data-bs-target="#traineeeducations-pane" type="button" role="tab" 
                        aria-controls="traineeeducations-pane" aria-selected="false">
                    <?= __('TraineeEducations') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="traineeexperiences-tab" data-bs-toggle="tab" 
                        data-bs-target="#traineeexperiences-pane" type="button" role="tab" 
                        aria-controls="traineeexperiences-pane" aria-selected="false">
                    <?= __('TraineeExperiences') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="traineefamilies-tab" data-bs-toggle="tab" 
                        data-bs-target="#traineefamilies-pane" type="button" role="tab" 
                        aria-controls="traineefamilies-pane" aria-selected="false">
                    <?= __('TraineeFamilies') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="traineefamilystories-tab" data-bs-toggle="tab" 
                        data-bs-target="#traineefamilystories-pane" type="button" role="tab" 
                        aria-controls="traineefamilystories-pane" aria-selected="false">
                    <?= __('TraineeFamilyStories') ?>
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="relatedTabsContent">
            <div class="tab-pane fade active show" id="traineecertifications-pane" role="tabpanel" 
                 aria-labelledby="traineecertifications-tab" tabindex="0">
                <?= $this->element('related_records_table_static', [
                    'tabId' => 'traineecertifications',
                    'title' => __('TraineeCertifications'),
                    'filterField' => 'trainees_id',
                    'filterValue' => $trainee->id,
                    'ajaxUrl' => $this->Url->build(['controller' => 'TraineeCertifications', 'action' => 'getRelated']),
                    'controller' => 'TraineeCertifications',
                    'columns' => [
                        ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'sortable' => true],
                        ['name' => 'created', 'label' => 'Created', 'type' => 'datetime', 'sortable' => true]
                    ]
                ]) ?>
            </div>
            <div class="tab-pane fade  " id="traineecourses-pane" role="tabpanel" 
                 aria-labelledby="traineecourses-tab" tabindex="0">
                <?= $this->element('related_records_table_static', [
                    'tabId' => 'traineecourses',
                    'title' => __('TraineeCourses'),
                    'filterField' => 'trainees_id',
                    'filterValue' => $trainee->id,
                    'ajaxUrl' => $this->Url->build(['controller' => 'TraineeCourses', 'action' => 'getRelated']),
                    'controller' => 'TraineeCourses',
                    'columns' => [
                        ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'sortable' => true],
                        ['name' => 'created', 'label' => 'Created', 'type' => 'datetime', 'sortable' => true]
                    ]
                ]) ?>
            </div>
            <div class="tab-pane fade  " id="traineeeducations-pane" role="tabpanel" 
                 aria-labelledby="traineeeducations-tab" tabindex="0">
                <?= $this->element('related_records_table_static', [
                    'tabId' => 'traineeeducations',
                    'title' => __('TraineeEducations'),
                    'filterField' => 'trainees_id',
                    'filterValue' => $trainee->id,
                    'ajaxUrl' => $this->Url->build(['controller' => 'TraineeEducations', 'action' => 'getRelated']),
                    'controller' => 'TraineeEducations',
                    'columns' => [
                        ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'sortable' => true],
                        ['name' => 'created', 'label' => 'Created', 'type' => 'datetime', 'sortable' => true]
                    ]
                ]) ?>
            </div>
            <div class="tab-pane fade  " id="traineeexperiences-pane" role="tabpanel" 
                 aria-labelledby="traineeexperiences-tab" tabindex="0">
                <?= $this->element('related_records_table_static', [
                    'tabId' => 'traineeexperiences',
                    'title' => __('TraineeExperiences'),
                    'filterField' => 'trainees_id',
                    'filterValue' => $trainee->id,
                    'ajaxUrl' => $this->Url->build(['controller' => 'TraineeExperiences', 'action' => 'getRelated']),
                    'controller' => 'TraineeExperiences',
                    'columns' => [
                        ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'sortable' => true],
                        ['name' => 'created', 'label' => 'Created', 'type' => 'datetime', 'sortable' => true]
                    ]
                ]) ?>
            </div>
            <div class="tab-pane fade  " id="traineefamilies-pane" role="tabpanel" 
                 aria-labelledby="traineefamilies-tab" tabindex="0">
                <?= $this->element('related_records_table_static', [
                    'tabId' => 'traineefamilies',
                    'title' => __('TraineeFamilies'),
                    'filterField' => 'trainees_id',
                    'filterValue' => $trainee->id,
                    'ajaxUrl' => $this->Url->build(['controller' => 'TraineeFamilies', 'action' => 'getRelated']),
                    'controller' => 'TraineeFamilies',
                    'columns' => [
                        ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'sortable' => true],
                        ['name' => 'created', 'label' => 'Created', 'type' => 'datetime', 'sortable' => true]
                    ]
                ]) ?>
            </div>
            <div class="tab-pane fade  " id="traineefamilystories-pane" role="tabpanel" 
                 aria-labelledby="traineefamilystories-tab" tabindex="0">
                <?= $this->element('related_records_table_static', [
                    'tabId' => 'traineefamilystories',
                    'title' => __('TraineeFamilyStories'),
                    'filterField' => 'trainees_id',
                    'filterValue' => $trainee->id,
                    'ajaxUrl' => $this->Url->build(['controller' => 'TraineeFamilyStories', 'action' => 'getRelated']),
                    'controller' => 'TraineeFamilyStories',
                    'columns' => [
                        ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'sortable' => true],
                        ['name' => 'created', 'label' => 'Created', 'type' => 'datetime', 'sortable' => true]
                    ]
                ]) ?>
            </div>
        </div>
    </div>

</div><!-- .view-content-wrapper -->
