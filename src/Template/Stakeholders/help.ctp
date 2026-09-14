<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="stakeholders help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-question-circle"></i> <?= __('Stakeholder Management - User Guide') ?>
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> <?= __('Welcome to Stakeholder Management') ?></h5>
                <p><?= __('This guide will help you understand how to manage stakeholders in the TMM Apprentice Management System. Stakeholders include organizations, institutions, and key partners involved in the apprenticeship program.') ?></p>
            </div>

            <!-- Stakeholder Overview Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-users"></i> <?= __('What are Stakeholders?') ?>
                </h4>
                
                <p><?= __('Stakeholders are organizations and institutions that play a role in the apprenticeship ecosystem:') ?></p>
                <ul>
                    <li><?= __('<strong>Vocational Training Institutions (LPK):</strong> Organizations that train candidates') ?></li>
                    <li><?= __('<strong>Acceptance Organizations:</strong> Japanese companies/organizations that accept apprentices') ?></li>
                    <li><?= __('<strong>Cooperative Associations:</strong> Cooperative organizations that facilitate placements') ?></li>
                    <li><?= __('<strong>Special Skill Support Institutions:</strong> Organizations providing additional training support') ?></li>
                </ul>
            </div>

            <!-- Managing Stakeholders Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-building"></i> <?= __('Managing Stakeholder Records') ?>
                </h4>
                
                <h5 class="mt-4"><?= __('How to View Stakeholder Information') ?></h5>
                <ol>
                    <li><?= __('Navigate to <strong>Stakeholder Management</strong> from the main menu') ?></li>
                    <li><?= __('Select the specific stakeholder type:') ?>
                        <ul>
                            <li><?= __('Vocational Training Institutions') ?></li>
                            <li><?= __('Acceptance Organizations') ?></li>
                            <li><?= __('Cooperative Associations') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Browse the list or use the search/filter features') ?></li>
                    <li><?= __('Click on any record to view detailed information') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('How to Add a New Stakeholder') ?></h5>
                <ol>
                    <li><?= __('Navigate to the specific stakeholder type page') ?></li>
                    <li><?= __('Click the <span class="badge badge-primary"><i class="fas fa-plus"></i> New</span> button') ?></li>
                    <li><?= __('Fill in the required information:') ?>
                        <ul>
                            <li><?= __('<strong>Name/Title:</strong> Official organization name') ?></li>
                            <li><?= __('<strong>Address:</strong> Complete physical address') ?></li>
                            <li><?= __('<strong>Contact Information:</strong> Phone, email, website') ?></li>
                            <li><?= __('<strong>Is Active:</strong> Set organization status') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Add any additional details specific to the stakeholder type') ?></li>
                    <li><?= __('Click <span class="badge badge-success"><i class="fas fa-save"></i> Submit</span> to save') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('How to Edit Stakeholder Information') ?></h5>
                <ol>
                    <li><?= __('Find the stakeholder record in the list') ?></li>
                    <li><?= __('Click the <span class="badge badge-warning"><i class="fas fa-edit"></i> Edit</span> button') ?></li>
                    <li><?= __('Update the necessary information') ?></li>
                    <li><?= __('Click <span class="badge badge-success"><i class="fas fa-save"></i> Save</span> to update') ?></li>
                </ol>
            </div>

            <!-- Vocational Training Institutions Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-school"></i> <?= __('Vocational Training Institutions (LPK)') ?>
                </h4>
                
                <p><?= __('<strong>Purpose:</strong> LPK organizations train candidates before they go to Japan.') ?></p>
                
                <h5 class="mt-3"><?= __('Key Information to Manage:') ?></h5>
                <ul>
                    <li><?= __('<strong>Institution Name:</strong> Official LPK name') ?></li>
                    <li><?= __('<strong>License Number:</strong> Government registration number') ?></li>
                    <li><?= __('<strong>Location:</strong> Province, city, complete address') ?></li>
                    <li><?= __('<strong>Capacity:</strong> Maximum number of trainees') ?></li>
                    <li><?= __('<strong>Specializations:</strong> Job categories/skills offered') ?></li>
                    <li><?= __('<strong>Contact Person:</strong> Director and staff contact information') ?></li>
                </ul>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> <?= __('<strong>Important:</strong> LPK users with role "lpk-penyangga" can only view and manage candidates from their own institution.') ?>
                </div>
            </div>

            <!-- Acceptance Organizations Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-industry"></i> <?= __('Acceptance Organizations') ?>
                </h4>
                
                <p><?= __('<strong>Purpose:</strong> Japanese companies/organizations that accept apprentices for training.') ?></p>
                
                <h5 class="mt-3"><?= __('Key Information to Manage:') ?></h5>
                <ul>
                    <li><?= __('<strong>Organization Name:</strong> Official company name (Japanese and English)') ?></li>
                    <li><?= __('<strong>Industry Type:</strong> Manufacturing, construction, hospitality, etc.') ?></li>
                    <li><?= __('<strong>Location:</strong> Prefecture and city in Japan') ?></li>
                    <li><?= __('<strong>Job Categories:</strong> Types of work offered') ?></li>
                    <li><?= __('<strong>Capacity:</strong> Number of apprentices they can accept') ?></li>
                    <li><?= __('<strong>Contact Information:</strong> Representative and contact details') ?></li>
                </ul>
            </div>

            <!-- Cooperative Associations Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-handshake"></i> <?= __('Cooperative Associations') ?>
                </h4>
                
                <p><?= __('<strong>Purpose:</strong> Organizations that facilitate placement and support for apprentices in Japan.') ?></p>
                
                <h5 class="mt-3"><?= __('Key Information to Manage:') ?></h5>
                <ul>
                    <li><?= __('<strong>Association Name:</strong> Official cooperative name') ?></li>
                    <li><?= __('<strong>License/Registration:</strong> OTIT registration number') ?></li>
                    <li><?= __('<strong>Service Area:</strong> Regions/prefectures they serve') ?></li>
                    <li><?= __('<strong>Member Organizations:</strong> Associated acceptance organizations') ?></li>
                    <li><?= __('<strong>Support Services:</strong> Types of support provided to apprentices') ?></li>
                </ul>
            </div>

            <!-- User Roles & Permissions -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-user-shield"></i> <?= __('User Roles & Permissions') ?>
                </h4>
                
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th><?= __('Role') ?></th>
                            <th><?= __('Access Level') ?></th>
                            <th><?= __('Stakeholder Management') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= '<span class="badge badge-danger">' . __('Administrator') . '</span>' ?></td>
                            <td><?= __('Full Access') ?></td>
                            <td><?= __('Create, Read, Update, Delete all stakeholders') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('<span class="badge badge-info">Management</span>') ?></td>
                            <td><?= __('Read-Only') ?></td>
                            <td><?= __('View all stakeholder information, export reports') ?></td>
                        </tr>
                        <tr>
                            <td><?= '<span class="badge badge-success">' . __('TMM Recruitment') . '</span>' ?></td>
                            <td><?= __('Full Access') ?></td>
                            <td><?= __('Manage stakeholders related to recruitment') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('<span class="badge badge-warning">LPK User</span>') ?></td>
                            <td><?= __('Institution-Scoped') ?></td>
                            <td><?= __('View only their own institution information') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-lightbulb"></i> <?= __('Best Practices') ?>
                </h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-check-circle text-success"></i> <?= __('Do\'s') ?></h5>
                        <ul>
                            <li><?= __('Keep stakeholder information up-to-date') ?></li>
                            <li><?= __('Verify contact information regularly') ?></li>
                            <li><?= __('Document all important communications') ?></li>
                            <li><?= __('Maintain accurate capacity numbers') ?></li>
                            <li><?= __('Update status when organizations become inactive') ?></li>
                            <li><?= __('Use clear, consistent naming conventions') ?></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-times-circle text-danger"></i> <?= __('Don\'ts') ?></h5>
                        <ul>
                            <li><?= __('Don\'t delete stakeholders with active apprentices') ?></li>
                            <li><?= __('Don\'t share sensitive organization information') ?></li>
                            <li><?= __('Don\'t modify records without proper authorization') ?></li>
                            <li><?= __('Don\'t forget to mark inactive organizations') ?></li>
                            <li><?= __('Don\'t duplicate stakeholder records') ?></li>
                            <li><?= __('Don\'t skip required fields') ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Common Tasks -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-tasks"></i> <?= __('Common Tasks') ?>
                </h4>
                
                <div class="accordion" id="commonTasksAccordion">
                    <!-- Task 1 -->
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne">
                                    <i class="fas fa-search"></i> <?= __('How to search for a specific stakeholder?') ?>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseOne" class="collapse" data-parent="#commonTasksAccordion">
                            <div class="card-body">
                                <ol>
                                    <li><?= __('Go to the stakeholder list page') ?></li>
                                    <li><?= __('Use the search box at the top of the table') ?></li>
                                    <li><?= __('Enter the organization name or other identifier') ?></li>
                                    <li><?= __('The list will filter automatically') ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Task 2 -->
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo">
                                    <i class="fas fa-file-export"></i> <?= __('How to export stakeholder data?') ?>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" data-parent="#commonTasksAccordion">
                            <div class="card-body">
                                <ol>
                                    <li><?= __('Navigate to the stakeholder list') ?></li>
                                    <li><?= __('Use filters if you want specific data') ?></li>
                                    <li><?= __('Click the export button (CSV, Excel, or PDF)') ?></li>
                                    <li><?= __('The file will download automatically') ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Task 3 -->
                    <div class="card">
                        <div class="card-header" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree">
                                    <i class="fas fa-link"></i> <?= __('How to link candidates to an LPK?') ?>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse" data-parent="#commonTasksAccordion">
                            <div class="card-body">
                                <ol>
                                    <li><?= __('Go to the Candidate edit page') ?></li>
                                    <li><?= __('Find the "Vocational Training Institution" dropdown') ?></li>
                                    <li><?= __('Select the appropriate LPK from the list') ?></li>
                                    <li><?= __('Save the candidate record') ?></li>
                                    <li><?= __('The candidate will now be associated with that LPK') ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-wrench"></i> <?= __('Troubleshooting') ?>
                </h4>
                
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th><?= __('Problem') ?></th>
                            <th><?= __('Solution') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= __('Can\'t see certain stakeholders') ?></td>
                            <td><?= __('Check your user role permissions. LPK users can only see their own institution.') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('Can\'t edit stakeholder information') ?></td>
                            <td><?= __('Verify you have the correct role (not read-only). Contact administrator if needed.') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('Duplicate stakeholders appear') ?></td>
                            <td><?= __('Contact administrator to merge records. Don\'t create new duplicates.') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('Export button not working') ?></td>
                            <td><?= __('Check your browser\'s pop-up blocker settings. Clear cache and try again.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Contact Support -->
            <div class="alert alert-success">
                <h5><i class="fas fa-life-ring"></i> <?= __('Need More Help?') ?></h5>
                <p class="mb-0">
                    <?= __('If you can\'t find the answer to your question in this guide, please contact your system administrator or the TMM support team.') ?>
                </p>
            </div>

            <!-- Back Button -->
            <div class="mt-4">
                <?= $this->Html->link(
                    '<i class="fas fa-arrow-left"></i> ' . __('Back to Dashboard'),
                    ['controller' => 'Dashboard', 'action' => 'index'],
                    ['class' => 'btn btn-secondary', 'escape' => false]
                ) ?>
            </div>

        </div>
    </div>
</div>

<style>
.help .card {
    border-radius: 10px;
}

.help h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.help h5 {
    color: #2c3e50;
    margin-top: 1.5rem;
}

.help ol, .help ul {
    margin-left: 1.5rem;
}

.help .alert h5 {
    margin-top: 0;
}

.help .accordion .card {
    margin-bottom: 0.5rem;
}

.help .accordion .btn-link {
    color: #4e73df;
    text-decoration: none;
    width: 100%;
    text-align: left;
}

.help .accordion .btn-link:hover {
    color: #224abe;
}

.help .badge {
    font-size: 90%;
}

.help table {
    font-size: 0.95rem;
}
</style>
