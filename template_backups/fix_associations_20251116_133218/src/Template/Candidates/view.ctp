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
                            '<i class="fas fa-list"></i> ' . __('List Candidates'),
                            ['action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New Candidate'),
                            ['action' => 'add'],
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
                            '<i class="fas fa-list"></i> ' . __('List MasterCandidateInterviewResults'),
                            ['controller' => 'MasterCandidateInterviewResults', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New MasterCandidateInterviewResult'),
                            ['controller' => 'MasterCandidateInterviewResults', 'action' => 'add'],
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
                        <?= __('Candidate') ?>: <?= h($candidate->name) ?>
                    </h1>
                </div>
                
                <div class="github-header-actions" style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                    <!-- Export Buttons Row -->
                    <div style="display: flex; gap: 8px;">
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
                    </div>
                    
                    <!-- Action Buttons Row -->
                    <div style="display: flex; gap: 8px;">
                        <?= $this->Html->link(
                            '<i class="fas fa-edit"></i> ' . __('Edit'),
                            ['action' => 'edit', $candidate->id],
                            ['class' => 'btn-export-light', 'escape' => false]
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
                <a href="#tab-candidate_certifications" class="view-tab-link" data-tab="tab-candidate_certifications">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('CandidateCertifications') ?>
                    <?php if (!empty($candidate->candidate_certifications)): ?>
                    <span class="tab-badge"><?= count($candidate->candidate_certifications) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-candidate_courses" class="view-tab-link" data-tab="tab-candidate_courses">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('CandidateCourses') ?>
                    <?php if (!empty($candidate->candidate_courses)): ?>
                    <span class="tab-badge"><?= count($candidate->candidate_courses) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-candidate_documents" class="view-tab-link" data-tab="tab-candidate_documents">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('CandidateDocuments') ?>
                    <?php if (!empty($candidate->candidate_documents)): ?>
                    <span class="tab-badge"><?= count($candidate->candidate_documents) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-candidate_educations" class="view-tab-link" data-tab="tab-candidate_educations">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('CandidateEducations') ?>
                    <?php if (!empty($candidate->candidate_educations)): ?>
                    <span class="tab-badge"><?= count($candidate->candidate_educations) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-candidate_experiences" class="view-tab-link" data-tab="tab-candidate_experiences">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('CandidateExperiences') ?>
                    <?php if (!empty($candidate->candidate_experiences)): ?>
                    <span class="tab-badge"><?= count($candidate->candidate_experiences) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="view-tab-item">
                <a href="#tab-candidate_families" class="view-tab-link" data-tab="tab-candidate_families">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('CandidateFamilies') ?>
                    <?php if (!empty($candidate->candidate_families)): ?>
                    <span class="tab-badge"><?= count($candidate->candidate_families) ?></span>
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
                    <?php if (!empty($candidate->image_photo)): ?>
                    <div class="image-preview-container" style="text-align: center; margin-bottom: 20px; background: #f6f8fa; padding: 20px; border-radius: 8px; border: 1px solid #d0d7de;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: #24292f;">
                            <i class="fas fa-image"></i> <?= __('Image Photo') ?>
                        </h4>
                        <div style="max-width: 800px; margin: 0 auto;">
                            <?php 
                            // Build proper image path
                            $imagePath = $candidate->image_photo;
                            if (strpos($imagePath, 'img/') !== 0 && strpos($imagePath, '/img/') !== 0) {
                                $imagePath = 'img/uploads/' . $imagePath;
                            }
                            ?>
                            <img src="<?= $this->Url->build('/' . h($imagePath)) ?>" 
                                 alt="<?= __('Image Photo') ?>"
                                 style="max-width: 100%; height: auto; max-height: 500px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: pointer;"
                                 onclick="window.open(this.src, '_blank')"
                                 onerror="this.parentElement.parentElement.style.display='none'">
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="<?= $this->Url->build('/' . h($imagePath)) ?>" 
                               download 
                               class="btn-export-light"
                               style="display: inline-block;">
                                <i class="fas fa-download"></i> <?= __('Download Image') ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
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
                        }
                    }
                    
                    currentIndex += direction;
                    if (currentIndex >= slides.length) currentIndex = 0;
                    if (currentIndex < 0) currentIndex = slides.length - 1;
                    
                    slides[currentIndex].style.display = 'block';
                }
                
                function showSlide(containerId, index) {
                    const container = document.getElementById(containerId);
                    const slides = container.getElementsByClassName('slide');
                    
                    for (let i = 0; i < slides.length; i++) {
                        slides[i].style.display = i === index ? 'block' : 'none';
                    }
                }
                
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
                        }
                    });
                });
                </script>

                <!-- Mobile Responsive Styles -->
                <style>
                @media (max-width: 768px) {
                    .image-preview-container img,
                    .slideshow-container img {
                        max-height: 300px !important;
                    }
                    
                    .file-preview-container iframe {
                        height: 400px !important;
                    }
                    
                    .slideshow-container button {
                        padding: 8px 12px !important;
                        font-size: 16px !important;
                    }
                    
                    .media-preview-section {
                        margin-left: -10px;
                        margin-right: -10px;
                    }
                    
                    .image-preview-container,
                    .file-preview-container,
                    .image-gallery-container {
                        padding: 15px !important;
                        border-radius: 0 !important;
                    }
                }
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
                            <td class="github-detail-value"><?= $this->Number->format($candidate->id) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Apprenticeship Order Id') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($candidate->apprenticeship_order_id) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Vocational Training Institution Id') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($candidate->vocational_training_institution_id) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Acceptance Organization Id') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($candidate->acceptance_organization_id) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Post Code') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($candidate->post_code) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Last Salary Amount') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($candidate->last_salary_amount) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Saving Goal Amount') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($candidate->saving_goal_amount) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Body Weight') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($candidate->body_weight) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Body Height') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($candidate->body_height) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Candidate Code') ?></th>
                            <td class="github-detail-value"><?= h($candidate->candidate_code) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Identity Number') ?></th>
                            <td class="github-detail-value"><?= h($candidate->identity_number) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Name') ?></th>
                            <td class="github-detail-value"><?= h($candidate->name) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Name Katakana') ?></th>
                            <td class="github-detail-value"><?= h($candidate->name_katakana) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Gender Id') ?></th>
                            <td class="github-detail-value">
                                <?= $candidate->has('master_gender') ? $this->Html->link($candidate->master_gender->title, ['controller' => 'MasterGenders', 'action' => 'view', $candidate->master_gender->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Religion Id') ?></th>
                            <td class="github-detail-value">
                                <?= $candidate->has('master_religion') ? $this->Html->link($candidate->master_religion->title, ['controller' => 'MasterReligions', 'action' => 'view', $candidate->master_religion->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Marriage Status Id') ?></th>
                            <td class="github-detail-value">
                                <?= $candidate->has('master_marriage_status') ? $this->Html->link($candidate->master_marriage_status->title, ['controller' => 'MasterMarriageStatuses', 'action' => 'view', $candidate->master_marriage_status->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Birth Place') ?></th>
                            <td class="github-detail-value"><?= h($candidate->birth_place) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Birth Place Katakana') ?></th>
                            <td class="github-detail-value"><?= h($candidate->birth_place_katakana) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Telephone Mobile') ?></th>
                            <td class="github-detail-value"><?= h($candidate->telephone_mobile) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Telephone Emergency') ?></th>
                            <td class="github-detail-value"><?= h($candidate->telephone_emergency) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Email') ?></th>
                            <td class="github-detail-value"><?= h($candidate->email) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Propinsi Id') ?></th>
                            <td class="github-detail-value">
                                <?= $candidate->has('master_propinsi') ? $this->Html->link($candidate->master_propinsi->title, ['controller' => 'MasterPropinsis', 'action' => 'view', $candidate->master_propinsi->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Kabupaten Id') ?></th>
                            <td class="github-detail-value">
                                <?= $candidate->has('master_kabupaten') ? $this->Html->link($candidate->master_kabupaten->title, ['controller' => 'MasterKabupatens', 'action' => 'view', $candidate->master_kabupaten->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Kecamatan') ?></th>
                            <td class="github-detail-value">
                                <?php if ($candidate->has('master_kecamatan')): ?>
                                    <?= $this->Html->link(
                                        h($candidate->master_kecamatan->title), 
                                        ['controller' => 'MasterKecamatans', 'action' => 'view', $candidate->master_kecamatan->id], 
                                        ['class' => 'github-link']
                                    ) ?>
                                    <small class="text-muted">(ID: <?= h($candidate->master_kecamatan_id) ?>)</small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Kelurahan') ?></th>
                            <td class="github-detail-value">
                                <?php if ($candidate->has('master_kelurahan')): ?>
                                    <?= $this->Html->link(
                                        h($candidate->master_kelurahan->title), 
                                        ['controller' => 'MasterKelurahans', 'action' => 'view', $candidate->master_kelurahan->id], 
                                        ['class' => 'github-link']
                                    ) ?>
                                    <small class="text-muted">(ID: <?= h($candidate->master_kelurahan_id) ?>)</small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Address') ?></th>
                            <td class="github-detail-value"><?= h($candidate->address) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Image Photo') ?></th>
                            <td class="github-detail-value"><?= h($candidate->image_photo) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Strengths') ?></th>
                            <td class="github-detail-value"><?= h($candidate->strengths) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Weaknesses') ?></th>
                            <td class="github-detail-value"><?= h($candidate->weaknesses) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Hobby') ?></th>
                            <td class="github-detail-value"><?= h($candidate->hobby) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Application Reasons') ?></th>
                            <td class="github-detail-value"><?= h($candidate->application_reasons) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Expected Work Upon Returning To Japan') ?></th>
                            <td class="github-detail-value"><?= h($candidate->expected_work_upon_returning_to_japan) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Blood Type Id') ?></th>
                            <td class="github-detail-value">
                                <?= $candidate->has('master_blood_type') ? $this->Html->link($candidate->master_blood_type->title, ['controller' => 'MasterBloodTypes', 'action' => 'view', $candidate->master_blood_type->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Explain Eye Condition') ?></th>
                            <td class="github-detail-value"><?= h($candidate->explain_eye_condition) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Explain Color Blind') ?></th>
                            <td class="github-detail-value"><?= h($candidate->explain_color_blind) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Whatsapp') ?></th>
                            <td class="github-detail-value"><?= h($candidate->link_whatsapp) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Line') ?></th>
                            <td class="github-detail-value"><?= h($candidate->link_line) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Instagram') ?></th>
                            <td class="github-detail-value"><?= h($candidate->link_instagram) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Facebook') ?></th>
                            <td class="github-detail-value"><?= h($candidate->link_facebook) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Link Tiktok') ?></th>
                            <td class="github-detail-value"><?= h($candidate->link_tiktok) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Candidate Interview Result Id') ?></th>
                            <td class="github-detail-value">
                                <?= $candidate->has('master_candidate_interview_result') ? $this->Html->link($candidate->master_candidate_interview_result->title, ['controller' => 'MasterCandidateInterviewResults', 'action' => 'view', $candidate->master_candidate_interview_result->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Master Rejected Reason Id') ?></th>
                            <td class="github-detail-value">
                                <?= $candidate->has('master_rejected_reason') ? $this->Html->link($candidate->master_rejected_reason->title, ['controller' => 'MasterRejectedReasons', 'action' => 'view', $candidate->master_rejected_reason->id], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Specific Rejected Reason') ?></th>
                            <td class="github-detail-value"><?= h($candidate->specific_rejected_reason) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Birth Date') ?></th>
                            <td class="github-detail-value"><?= h($candidate->birth_date) ?></td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Ever Went To Japan') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_ever_went_to_japan ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_ever_went_to_japan ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Will Go To Japan After Finished') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->will_go_to_japan_after_finished ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->will_go_to_japan_after_finished ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Holding Passport') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_holding_passport ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_holding_passport ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Wear Eye Glasses') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_wear_eye_glasses ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_wear_eye_glasses ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Color Blind') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_color_blind ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_color_blind ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Right Handed') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_right_handed ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_right_handed ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Smoking') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_smoking ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_smoking ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Drinking Alcohol') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_drinking_alcohol ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_drinking_alcohol ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Tattooed') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_tattooed ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_tattooed ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Training Pass') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_training_pass ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_training_pass ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="github-detail-label"><?= __('Is Apprenticeship Pass') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $candidate->is_apprenticeship_pass ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $candidate->is_apprenticeship_pass ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            </div>
            <!-- End Home Tab -->

            <!-- CandidateCertifications Tab -->
            <div id="tab-candidate_certifications" class="view-tab-pane">
                <?php if (!empty($candidate->candidate_certifications)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related CandidateCertifications') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New CandidateCertification'),
                                ['controller' => 'CandidateCertifications', 'action' => 'add'],
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
                                        <th><?= __('Candidate Id') ?></th>
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
                                                        data-column="candidate_id" 
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
                                                       placeholder="<?= __('Filter Candidate Id') ?>" 
                                                       data-column="candidate_id"
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
                                    <?php foreach ($candidate->candidate_certifications as $candidateCertifications): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'CandidateCertifications', 'action' => 'edit', $candidateCertifications->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'CandidateCertifications', 'action' => 'view', $candidateCertifications->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($candidateCertifications->id) ?></td>
                                        <td>
                                            <?php if ($candidateCertifications->has('candidate')): ?>
                                                <?php
                                                    $displayValue = $candidateCertifications->candidate->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateCertifications->candidate->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateCertifications->candidate_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Candidates', 'action' => 'view', $candidateCertifications->candidate_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateCertifications->candidate_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($candidateCertifications->title) ?></td>
                                        <td><?= h($candidateCertifications->institution_name) ?></td>
                                        <td><?= h($candidateCertifications->certification_date) ?></td>
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
                    <h3><?= __('No Related CandidateCertifications') ?></h3>
                    <p><?= __('There are no related CandidateCertifications for this Candidate.') ?></p>
                    <?= $this->Html->link(
                        __('Add First CandidateCertification'),
                        ['controller' => 'CandidateCertifications', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- CandidateCourses Tab -->
            <div id="tab-candidate_courses" class="view-tab-pane">
                <?php if (!empty($candidate->candidate_courses)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related CandidateCourses') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New CandidateCourse'),
                                ['controller' => 'CandidateCourses', 'action' => 'add'],
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
                                        <th><?= __('Candidate Id') ?></th>
                                        <th><?= __('Vocational Training Institution Id') ?></th>
                                        <th><?= __('Master Course Major Id') ?></th>
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
                                                        data-column="candidate_id" 
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
                                                       placeholder="<?= __('Filter Candidate Id') ?>" 
                                                       data-column="candidate_id"
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
                                                        data-column="master_course_major_id" 
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
                                                       placeholder="<?= __('Filter Master Course Major Id') ?>" 
                                                       data-column="master_course_major_id"
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
                                    <?php foreach ($candidate->candidate_courses as $candidateCourses): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'CandidateCourses', 'action' => 'edit', $candidateCourses->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'CandidateCourses', 'action' => 'view', $candidateCourses->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($candidateCourses->id) ?></td>
                                        <td>
                                            <?php if ($candidateCourses->has('candidate')): ?>
                                                <?php
                                                    $displayValue = $candidateCourses->candidate->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateCourses->candidate->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateCourses->candidate_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Candidates', 'action' => 'view', $candidateCourses->candidate_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateCourses->candidate_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($candidateCourses->has('vocationalTrainingInstitution')): ?>
                                                <?php
                                                    $displayValue = $candidateCourses->vocationalTrainingInstitution->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateCourses->vocationalTrainingInstitution->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateCourses->vocational_training_institution_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'VocationalTrainingInstitutions', 'action' => 'view', $candidateCourses->vocational_training_institution_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateCourses->vocational_training_institution_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($candidateCourses->has('masterCourseMajor')): ?>
                                                <?php
                                                    $displayValue = $candidateCourses->masterCourseMajor->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateCourses->masterCourseMajor->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateCourses->master_course_major_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterCourseMajors', 'action' => 'view', $candidateCourses->master_course_major_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateCourses->master_course_major_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($candidateCourses->course_year) ?></td>
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
                    <h3><?= __('No Related CandidateCourses') ?></h3>
                    <p><?= __('There are no related CandidateCourses for this Candidate.') ?></p>
                    <?= $this->Html->link(
                        __('Add First CandidateCourse'),
                        ['controller' => 'CandidateCourses', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- CandidateDocuments Tab -->
            <div id="tab-candidate_documents" class="view-tab-pane">
                <?php if (!empty($candidate->candidate_documents)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related CandidateDocuments') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New CandidateDocument'),
                                ['controller' => 'CandidateDocuments', 'action' => 'add'],
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
                                        <th><?= __('Candidate Id') ?></th>
                                        <th><?= __('Title') ?></th>
                                        <th><?= __('File Name') ?></th>
                                        <th><?= __('Comments') ?></th>
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
                                                        data-column="candidate_id" 
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
                                                       placeholder="<?= __('Filter Candidate Id') ?>" 
                                                       data-column="candidate_id"
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
                                                        data-column="file_name" 
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
                                                       placeholder="<?= __('Filter File Name') ?>" 
                                                       data-column="file_name"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="comments" 
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
                                                       placeholder="<?= __('Filter Comments') ?>" 
                                                       data-column="comments"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($candidate->candidate_documents as $candidateDocuments): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'CandidateDocuments', 'action' => 'edit', $candidateDocuments->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'CandidateDocuments', 'action' => 'view', $candidateDocuments->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($candidateDocuments->id) ?></td>
                                        <td>
                                            <?php if ($candidateDocuments->has('candidate')): ?>
                                                <?php
                                                    $displayValue = $candidateDocuments->candidate->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateDocuments->candidate->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateDocuments->candidate_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Candidates', 'action' => 'view', $candidateDocuments->candidate_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateDocuments->candidate_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($candidateDocuments->title) ?></td>
                                        <td><?= h($candidateDocuments->file_name) ?></td>
                                        <td><?= h($candidateDocuments->comments) ?></td>
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
                    <h3><?= __('No Related CandidateDocuments') ?></h3>
                    <p><?= __('There are no related CandidateDocuments for this Candidate.') ?></p>
                    <?= $this->Html->link(
                        __('Add First CandidateDocument'),
                        ['controller' => 'CandidateDocuments', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- CandidateEducations Tab -->
            <div id="tab-candidate_educations" class="view-tab-pane">
                <?php if (!empty($candidate->candidate_educations)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related CandidateEducations') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New CandidateEducation'),
                                ['controller' => 'CandidateEducations', 'action' => 'add'],
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
                                        <th><?= __('Candidate Id') ?></th>
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
                                                        data-column="candidate_id" 
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
                                                       placeholder="<?= __('Filter Candidate Id') ?>" 
                                                       data-column="candidate_id"
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
                                    <?php foreach ($candidate->candidate_educations as $candidateEducations): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'CandidateEducations', 'action' => 'edit', $candidateEducations->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'CandidateEducations', 'action' => 'view', $candidateEducations->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($candidateEducations->id) ?></td>
                                        <td>
                                            <?php if ($candidateEducations->has('candidate')): ?>
                                                <?php
                                                    $displayValue = $candidateEducations->candidate->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateEducations->candidate->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateEducations->candidate_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Candidates', 'action' => 'view', $candidateEducations->candidate_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateEducations->candidate_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($candidateEducations->has('masterStrata')): ?>
                                                <?php
                                                    $displayValue = $candidateEducations->masterStrata->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateEducations->masterStrata->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateEducations->master_strata_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterStratas', 'action' => 'view', $candidateEducations->master_strata_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateEducations->master_strata_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($candidateEducations->has('masterPropinsi')): ?>
                                                <?php
                                                    $displayValue = $candidateEducations->masterPropinsi->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateEducations->masterPropinsi->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateEducations->master_propinsi_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterPropinsis', 'action' => 'view', $candidateEducations->master_propinsi_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateEducations->master_propinsi_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($candidateEducations->has('masterKabupaten')): ?>
                                                <?php
                                                    $displayValue = $candidateEducations->masterKabupaten->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateEducations->masterKabupaten->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateEducations->master_kabupaten_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterKabupatens', 'action' => 'view', $candidateEducations->master_kabupaten_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateEducations->master_kabupaten_id) ?>
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
                    <h3><?= __('No Related CandidateEducations') ?></h3>
                    <p><?= __('There are no related CandidateEducations for this Candidate.') ?></p>
                    <?= $this->Html->link(
                        __('Add First CandidateEducation'),
                        ['controller' => 'CandidateEducations', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- CandidateExperiences Tab -->
            <div id="tab-candidate_experiences" class="view-tab-pane">
                <?php if (!empty($candidate->candidate_experiences)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related CandidateExperiences') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New CandidateExperience'),
                                ['controller' => 'CandidateExperiences', 'action' => 'add'],
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
                                        <th><?= __('Candidate Id') ?></th>
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
                                                        data-column="candidate_id" 
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
                                                       placeholder="<?= __('Filter Candidate Id') ?>" 
                                                       data-column="candidate_id"
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
                                    <?php foreach ($candidate->candidate_experiences as $candidateExperiences): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'CandidateExperiences', 'action' => 'edit', $candidateExperiences->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'CandidateExperiences', 'action' => 'view', $candidateExperiences->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($candidateExperiences->id) ?></td>
                                        <td>
                                            <?php if ($candidateExperiences->has('candidate')): ?>
                                                <?php
                                                    $displayValue = $candidateExperiences->candidate->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateExperiences->candidate->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateExperiences->candidate_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Candidates', 'action' => 'view', $candidateExperiences->candidate_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateExperiences->candidate_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($candidateExperiences->employment_start_year) ?></td>
                                        <td><?= h($candidateExperiences->employment_end_year) ?></td>
                                        <td><?= h($candidateExperiences->company_name) ?></td>
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
                    <h3><?= __('No Related CandidateExperiences') ?></h3>
                    <p><?= __('There are no related CandidateExperiences for this Candidate.') ?></p>
                    <?= $this->Html->link(
                        __('Add First CandidateExperience'),
                        ['controller' => 'CandidateExperiences', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- CandidateFamilies Tab -->
            <div id="tab-candidate_families" class="view-tab-pane">
                <?php if (!empty($candidate->candidate_families)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related CandidateFamilies') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New CandidateFamily'),
                                ['controller' => 'CandidateFamilies', 'action' => 'add'],
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
                                        <th><?= __('Candidate Id') ?></th>
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
                                                        data-column="candidate_id" 
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
                                                       placeholder="<?= __('Filter Candidate Id') ?>" 
                                                       data-column="candidate_id"
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
                                    <?php foreach ($candidate->candidate_families as $candidateFamilies): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => 'CandidateFamilies', 'action' => 'edit', $candidateFamilies->id],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'CandidateFamilies', 'action' => 'view', $candidateFamilies->id],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
                                        <td><?= h($candidateFamilies->id) ?></td>
                                        <td>
                                            <?php if ($candidateFamilies->has('candidate')): ?>
                                                <?php
                                                    $displayValue = $candidateFamilies->candidate->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateFamilies->candidate->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateFamilies->candidate_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'Candidates', 'action' => 'view', $candidateFamilies->candidate_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateFamilies->candidate_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($candidateFamilies->has('masterFamilyConnection')): ?>
                                                <?php
                                                    $displayValue = $candidateFamilies->masterFamilyConnection->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateFamilies->masterFamilyConnection->name;
                                                    }
                                                    if (empty($displayValue)) {
                                                        $displayValue = $candidateFamilies->master_family_connection_id;
                                                    }
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => 'MasterFamilyConnections', 'action' => 'view', $candidateFamilies->master_family_connection_id]) ?>
                                            <?php else: ?>
                                                <?= h($candidateFamilies->master_family_connection_id) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($candidateFamilies->name) ?></td>
                                        <td><?= h($candidateFamilies->age) ?></td>
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
                    <h3><?= __('No Related CandidateFamilies') ?></h3>
                    <p><?= __('There are no related CandidateFamilies for this Candidate.') ?></p>
                    <?= $this->Html->link(
                        __('Add First CandidateFamily'),
                        ['controller' => 'CandidateFamilies', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
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
            }
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
            }
        }
        
        if (tabLinks.length === 0 || tabPanes.length === 0) {
            console.error('❌ No tabs found! TabLinks:', tabLinks.length, 'TabPanes:', tabPanes.length);
            console.error('Container:', viewContainer);
            if (isDocument) {
                console.error('View wrapper:', document.querySelector('.view-content-wrapper[data-view-template="true"]'));
            }
            return;
        }
        
        console.log('✅ SUCCESS: Initializing', tabLinks.length, 'tab links and', tabPanes.length, 'tab panes');
        console.log('📍 Container:', isDocument ? 'document (full page)' : 'element (modal/container)');
        
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
            }
        }
        
        freshTabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('🖱️  Tab clicked:', this.getAttribute('data-tab'));
                
                // Get target tab
                const targetTab = this.getAttribute('data-tab');
                
                if (!targetTab) {
                    console.error('❌ No data-tab attribute found on clicked element');
                    return;
                }
                
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
                }
                
                if (targetPane) {
                    targetPane.classList.add('active');
                    console.log('✅ Activated pane:', targetTab);
                } else {
                    console.error('❌ Target pane not found:', targetTab);
                    console.error('Available panes:', freshTabPanes ? Array.from(freshTabPanes).map(p => p.id) : 'none');
                }
                
                // Store active tab in sessionStorage (optional)
                try {
                    sessionStorage.setItem('activeViewTab', targetTab);
                } catch (e) {
                    console.warn('sessionStorage not available:', e);
                }
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
                    }
                } else {
                    const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
                    if (viewWrapper) {
                        targetPane = viewWrapper.querySelector('#' + savedTab);
                        savedLink = viewWrapper.querySelector('[data-tab="' + savedTab + '"]');
                    } else {
                        targetPane = viewContainer.querySelector('#' + savedTab);
                        savedLink = viewContainer.querySelector('[data-tab="' + savedTab + '"]');
                    }
                }
                
                if (targetPane && savedLink) {
                    savedLink.click();
                }
            }
        } catch (e) {
            console.warn('Could not restore tab from sessionStorage:', e);
        }
    };
}

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
                }
            }
        }, 100);
    });
    
    // Also listen for show event (before animation)
    jQuery(document).on('show.bs.modal', '.modal', function(e) {
        console.log('Modal showing (before animation)...');
    });
}

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
                }
            }
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
                    }
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
                                }
                            }
                        }
                    });
                    
                    // Also check if tabs were added to an existing modal
                    if (node.closest('.modal-body')) {
                        const tabLinks = node.querySelectorAll('.view-tab-link');
                        if (tabLinks.length > 0) {
                            console.log('MutationObserver: Tabs added to modal, initializing...');
                            const modalBody = node.closest('.modal-body');
                            window.initializeTabSwitching(modalBody);
                        }
                    }
                }
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
        }
    }
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
}

/* Ensure tabs work in modals - override Bootstrap modal styles */
.modal .view-content-wrapper,
.modal-body .view-content-wrapper {
    overflow: visible !important;
    transform: none !important;
}

.modal .view-tabs-container,
.modal-body .view-tabs-container {
    overflow: visible !important;
}

.modal .view-tabs-nav,
.modal-body .view-tabs-nav {
    flex-wrap: nowrap !important;
    transform: none !important;
}

/* Fix Bootstrap modal conflicts */
.modal a.view-tab-link,
.modal-body a.view-tab-link {
    color: #586069 !important;
}

.modal a.view-tab-link.active,
.modal-body a.view-tab-link.active {
    color: #0969da !important;
}

.modal a.view-tab-link:hover,
.modal-body a.view-tab-link:hover {
    color: #24292f !important;
    text-decoration: none !important;
}

/* Button spacing for view actions */
.github-header-actions .btn {
    margin-right: 8px;
}

.github-header-actions .btn:last-child {
    margin-right: 0;
}

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
}

/* Override any modal/page styles that might affect tabs */
.modal .view-tabs-container,
.modal-body .view-tabs-container {
    margin: 20px 0 !important;
    width: 100% !important;
    max-width: 100% !important;
}

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
}

.view-tabs-nav.dragging {
    cursor: grabbing !important;
    scroll-behavior: auto !important;
}

/* Transparent scrollbar */
.view-tabs-nav::-webkit-scrollbar {
    height: 8px !important;
}

.view-tabs-nav::-webkit-scrollbar-track {
    background: transparent !important;
}

.view-tabs-nav::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1) !important;
    border-radius: 4px !important;
}

.view-tabs-nav::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.2) !important;
}

/* Firefox */
.view-tabs-nav {
    scrollbar-width: thin !important;
    scrollbar-color: rgba(0, 0, 0, 0.1) transparent !important;
}

.view-tab-item {
    margin: 0 !important;
    flex-shrink: 0 !important;
    display: block !important;
}

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
}

.view-tab-link:last-child {
    border-right: none !important;
}

.view-tab-link:hover {
    color: #24292f !important;
    background: rgba(175, 184, 193, 0.2) !important;
    border-bottom-color: #959da5 !important;
    text-decoration: none !important;
}

.view-tab-link.active {
    color: #0969da !important;
    border-bottom-color: #0969da !important;
    font-weight: 600 !important;
    background: #ffffff !important;
    z-index: 2 !important;
}

.view-tab-link.active::after {
    content: '' !important;
    position: absolute !important;
    bottom: -2px !important;
    left: 0 !important;
    right: 0 !important;
    height: 2px !important;
    background: #ffffff !important;
}

.tab-icon {
    flex-shrink: 0 !important;
    display: inline-block !important;
}

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
}

.view-tab-link.active .tab-badge {
    background: #0969da !important;
    color: #ffffff !important;
}

.view-tabs-content {
    margin-top: 20px !important;
    display: block !important;
    width: 100% !important;
}

.view-tab-pane {
    display: none !important;
    width: 100% !important;
}

.view-tab-pane.active {
    display: block !important;
}

/* Empty State */
.github-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--github-canvas-default, #ffffff);
    border: 1px solid var(--github-border-default, #d0d7de);
    border-radius: 12px;
}

.github-empty-state .empty-icon {
    opacity: 0.3;
    margin-bottom: 16px;
}

.github-empty-state h3 {
    margin: 0 0 8px;
    font-size: 18px;
    color: var(--github-fg-default, #24292f);
}

.github-empty-state p {
    margin: 0 0 20px;
    color: var(--github-fg-muted, #656d76);
}

/* GitHub Style View CSS */
.github-view-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.github-details-card,
.github-related-card {
    background: var(--github-canvas-default, #ffffff);
    border: 1px solid var(--github-border-default, #d0d7de);
    border-radius: 12px;
    overflow: hidden;
}

.github-details-header,
.github-related-header {
    padding: 16px 20px;
    background: var(--github-canvas-subtle, #f6f8fa);
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

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
}

.github-related-title::after {
    content: "";
    display: block;
    clear: both;
}

.github-details-body,
.github-related-body {
    padding: 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.github-details-table {
    width: 100%;
    border-collapse: collapse;
}

.github-details-table tr {
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);
}

.github-details-table tr:last-child {
    border-bottom: none;
}

.github-detail-label {
    width: 200px;
    padding: 16px 20px;
    font-weight: 600;
    color: var(--github-fg-default, #24292f);
    background: var(--github-canvas-subtle, #f6f8fa);
    text-align: left;
    vertical-align: top;
}

.github-detail-value {
    padding: 16px 20px;
    color: var(--github-fg-default, #24292f);
}

.github-link {
    color: var(--github-accent-fg, #0969da);
    text-decoration: none;
}

.github-link:hover {
    text-decoration: underline;
}

.github-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.badge-success {
    background: rgba(26, 127, 55, 0.1);
    color: #1a7f37;
}

.badge-secondary {
    background: var(--github-canvas-subtle, #f6f8fa);
    color: var(--github-fg-muted, #656d76);
}

.github-related-table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;
}

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
}

.github-related-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);
}

.github-related-table tbody tr:last-child td {
    border-bottom: none;
}

.github-related-actions {
    display: flex;
    gap: 4px;
}

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
    }

    body {
        padding: 0;
        margin: 0;
    }

    .container {
        padding: 0 8px;
    }
    
    /* Mobile Tab Styles */
    .view-tab-link {
        padding: 10px 16px;
        font-size: 14px;
    }
    
    .tab-icon {
        width: 14px;
        height: 14px;
    }
    
    .tab-badge {
        font-size: 11px;
        padding: 1px 6px;
    }
    
    .github-detail-label {
        width: 120px;
        padding: 12px 16px;
        font-size: 14px;
    }
    
    .github-detail-value {
        padding: 12px 16px;
        font-size: 14px;
    }
    
    .github-related-table {
        font-size: 14px;
    }
    
    .github-related-actions {
        flex-direction: column;
        gap: 4px;
    }
    
    .github-related-actions .github-btn {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .view-tab-link {
        padding: 8px 12px;
        font-size: 13px;
    }
    
    .github-detail-label {
        width: 100px;
        padding: 10px 12px;
        font-size: 13px;
    }
    
    .github-detail-value {
        padding: 10px 12px;
        font-size: 13px;
    }
}

/* Action Buttons Styles (same as index page) */
.action-buttons-hover {
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
    display: flex;
    gap: 2px;
}

.table-row-with-actions:hover .action-buttons-hover {
    opacity: 1;
}

.actions-column {
    background-color: #fff !important;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.table-striped tbody tr:nth-of-type(odd) .actions-column {
    background-color: rgba(102, 126, 234, 0.05) !important;
}

.table-striped tbody tr:nth-of-type(even) .actions-column {
    background-color: #fff !important;
}

.table-row-with-actions:hover .actions-column {
    background-color: rgba(102, 126, 234, 0.08) !important;
}

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
}

.btn-action-icon:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    text-decoration: none;
}

/* Filter Row Styles */
.filter-row input.filter-input {
    width: 100%;
    padding: 4px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 12px;
}

.filter-row input.filter-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

/* Drag to scroll for tables */
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive:active {
    cursor: grabbing;
}

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
}
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
            }
            
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
                    }
                    return cellVal.includes(filterVal);
                default:
                    return cellVal.includes(filterVal);
            }
        }
        
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
                        }
                    });
                    
                    // Get column index
                    const headers = Array.from(table.querySelectorAll('thead tr:first-child th'));
                    let filterColIndex = -1;
                    headers.forEach((th, idx) => {
                        if (th.textContent.toLowerCase().includes(filterColumn.replace('_', ' '))) {
                            filterColIndex = idx;
                        }
                    });
                    
                    if (filterColIndex >= 0 && cells[filterColIndex]) {
                        const cellText = cells[filterColIndex].textContent;
                        if (!applyFilter(cellText, filterVal, operator)) {
                            shouldShow = false;
                        }
                    }
                });
                
                row.style.display = shouldShow ? '' : 'none';
            });
        }
        
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
            }
        });
        
        // Close dropdown after clicking a link (with small delay for navigation)
        dropdownMenu.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                setTimeout(function() {
                    dropdownMenu.classList.remove('show');
                }, 100);
            }
        });
        
        console.log('✅ Dropdown menu initialized on view page');
    }
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
}

.dropdown-menu.show {
    display: block;
}

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
}

.dropdown-item:hover {
    color: #16181b;
    background-color: #f8f9fa;
    text-decoration: none;
}

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid #e9ecef;
}

.dropdown-toggle::after {
    display: none; /* Remove default Bootstrap caret */
}

/* Position dropdown container relatively */
.github-title-row > div:first-child {
    position: relative;
}
</style>

<!-- Delete Button Section - Bottom of Tab Container -->
<div style="margin-top: 30px; padding: 20px; border-top: 2px solid #e1e4e8; background-color: #f6f8fa;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h5 style="margin: 0; color: #d73a49; font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i> Danger Zone
            </h5>
            <p style="margin: 5px 0 0 0; color: #586069; font-size: 0.9rem;">
                Once you delete this candidate, there is no going back. Please be certain.
            </p>
        </div>
        <?= $this->Form->postLink(
            '<i class="fas fa-trash"></i> ' . __('Delete This Candidate'),
            ['action' => 'delete', $candidate->id],
            [
                'class' => 'btn btn-danger',
                'escape' => false,
                'confirm' => __('Are you sure you want to permanently delete this candidate: {0}?', $candidate->name),
                'style' => 'padding: 10px 20px; font-weight: 500;'
            ]
        ) ?>
    </div>
</div>

</div><!-- .view-content-wrapper -->