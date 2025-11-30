<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="stakeholders help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-question-circle"></i> Stakeholder Management - User Guide
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Welcome to Stakeholder Management</h5>
                <p>This guide will help you understand how to manage stakeholders in the TMM Apprentice Management System. Stakeholders include organizations, institutions, and key partners involved in the apprenticeship program.</p>
            </div>

            <!-- Stakeholder Overview Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-users"></i> What are Stakeholders?
                </h4>
                
                <p>Stakeholders are organizations and institutions that play a role in the apprenticeship ecosystem:</p>
                <ul>
                    <li><strong>Vocational Training Institutions (LPK):</strong> Organizations that train candidates</li>
                    <li><strong>Acceptance Organizations:</strong> Japanese companies/organizations that accept apprentices</li>
                    <li><strong>Cooperative Associations:</strong> Cooperative organizations that facilitate placements</li>
                    <li><strong>Special Skill Support Institutions:</strong> Organizations providing additional training support</li>
                </ul>
            </div>

            <!-- Managing Stakeholders Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-building"></i> Managing Stakeholder Records
                </h4>
                
                <h5 class="mt-4">How to View Stakeholder Information</h5>
                <ol>
                    <li>Navigate to <strong>Stakeholder Management</strong> from the main menu</li>
                    <li>Select the specific stakeholder type:
                        <ul>
                            <li>Vocational Training Institutions</li>
                            <li>Acceptance Organizations</li>
                            <li>Cooperative Associations</li>
                        </ul>
                    </li>
                    <li>Browse the list or use the search/filter features</li>
                    <li>Click on any record to view detailed information</li>
                </ol>

                <h5 class="mt-4">How to Add a New Stakeholder</h5>
                <ol>
                    <li>Navigate to the specific stakeholder type page</li>
                    <li>Click the <span class="badge badge-primary"><i class="fas fa-plus"></i> New</span> button</li>
                    <li>Fill in the required information:
                        <ul>
                            <li><strong>Name/Title:</strong> Official organization name</li>
                            <li><strong>Address:</strong> Complete physical address</li>
                            <li><strong>Contact Information:</strong> Phone, email, website</li>
                            <li><strong>Is Active:</strong> Set organization status</li>
                        </ul>
                    </li>
                    <li>Add any additional details specific to the stakeholder type</li>
                    <li>Click <span class="badge badge-success"><i class="fas fa-save"></i> Submit</span> to save</li>
                </ol>

                <h5 class="mt-4">How to Edit Stakeholder Information</h5>
                <ol>
                    <li>Find the stakeholder record in the list</li>
                    <li>Click the <span class="badge badge-warning"><i class="fas fa-edit"></i> Edit</span> button</li>
                    <li>Update the necessary information</li>
                    <li>Click <span class="badge badge-success"><i class="fas fa-save"></i> Save</span> to update</li>
                </ol>
            </div>

            <!-- Vocational Training Institutions Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-school"></i> Vocational Training Institutions (LPK)
                </h4>
                
                <p><strong>Purpose:</strong> LPK organizations train candidates before they go to Japan.</p>
                
                <h5 class="mt-3">Key Information to Manage:</h5>
                <ul>
                    <li><strong>Institution Name:</strong> Official LPK name</li>
                    <li><strong>License Number:</strong> Government registration number</li>
                    <li><strong>Location:</strong> Province, city, complete address</li>
                    <li><strong>Capacity:</strong> Maximum number of trainees</li>
                    <li><strong>Specializations:</strong> Job categories/skills offered</li>
                    <li><strong>Contact Person:</strong> Director and staff contact information</li>
                </ul>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> LPK users with role "lpk-penyangga" can only view and manage candidates from their own institution.
                </div>
            </div>

            <!-- Acceptance Organizations Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-industry"></i> Acceptance Organizations
                </h4>
                
                <p><strong>Purpose:</strong> Japanese companies/organizations that accept apprentices for training.</p>
                
                <h5 class="mt-3">Key Information to Manage:</h5>
                <ul>
                    <li><strong>Organization Name:</strong> Official company name (Japanese and English)</li>
                    <li><strong>Industry Type:</strong> Manufacturing, construction, hospitality, etc.</li>
                    <li><strong>Location:</strong> Prefecture and city in Japan</li>
                    <li><strong>Job Categories:</strong> Types of work offered</li>
                    <li><strong>Capacity:</strong> Number of apprentices they can accept</li>
                    <li><strong>Contact Information:</strong> Representative and contact details</li>
                </ul>
            </div>

            <!-- Cooperative Associations Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-handshake"></i> Cooperative Associations
                </h4>
                
                <p><strong>Purpose:</strong> Organizations that facilitate placement and support for apprentices in Japan.</p>
                
                <h5 class="mt-3">Key Information to Manage:</h5>
                <ul>
                    <li><strong>Association Name:</strong> Official cooperative name</li>
                    <li><strong>License/Registration:</strong> OTIT registration number</li>
                    <li><strong>Service Area:</strong> Regions/prefectures they serve</li>
                    <li><strong>Member Organizations:</strong> Associated acceptance organizations</li>
                    <li><strong>Support Services:</strong> Types of support provided to apprentices</li>
                </ul>
            </div>

            <!-- User Roles & Permissions -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-user-shield"></i> User Roles & Permissions
                </h4>
                
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Role</th>
                            <th>Access Level</th>
                            <th>Stakeholder Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-danger">Administrator</span></td>
                            <td>Full Access</td>
                            <td>Create, Read, Update, Delete all stakeholders</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-info">Management</span></td>
                            <td>Read-Only</td>
                            <td>View all stakeholder information, export reports</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-success">TMM Recruitment</span></td>
                            <td>Full Access</td>
                            <td>Manage stakeholders related to recruitment</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-warning">LPK User</span></td>
                            <td>Institution-Scoped</td>
                            <td>View only their own institution information</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-lightbulb"></i> Best Practices
                </h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-check-circle text-success"></i> Do's</h5>
                        <ul>
                            <li>Keep stakeholder information up-to-date</li>
                            <li>Verify contact information regularly</li>
                            <li>Document all important communications</li>
                            <li>Maintain accurate capacity numbers</li>
                            <li>Update status when organizations become inactive</li>
                            <li>Use clear, consistent naming conventions</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-times-circle text-danger"></i> Don'ts</h5>
                        <ul>
                            <li>Don't delete stakeholders with active apprentices</li>
                            <li>Don't share sensitive organization information</li>
                            <li>Don't modify records without proper authorization</li>
                            <li>Don't forget to mark inactive organizations</li>
                            <li>Don't duplicate stakeholder records</li>
                            <li>Don't skip required fields</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Common Tasks -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-tasks"></i> Common Tasks
                </h4>
                
                <div class="accordion" id="commonTasksAccordion">
                    <!-- Task 1 -->
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne">
                                    <i class="fas fa-search"></i> How to search for a specific stakeholder?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseOne" class="collapse" data-parent="#commonTasksAccordion">
                            <div class="card-body">
                                <ol>
                                    <li>Go to the stakeholder list page</li>
                                    <li>Use the search box at the top of the table</li>
                                    <li>Enter the organization name or other identifier</li>
                                    <li>The list will filter automatically</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Task 2 -->
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo">
                                    <i class="fas fa-file-export"></i> How to export stakeholder data?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" data-parent="#commonTasksAccordion">
                            <div class="card-body">
                                <ol>
                                    <li>Navigate to the stakeholder list</li>
                                    <li>Use filters if you want specific data</li>
                                    <li>Click the export button (CSV, Excel, or PDF)</li>
                                    <li>The file will download automatically</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Task 3 -->
                    <div class="card">
                        <div class="card-header" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree">
                                    <i class="fas fa-link"></i> How to link candidates to an LPK?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse" data-parent="#commonTasksAccordion">
                            <div class="card-body">
                                <ol>
                                    <li>Go to the Candidate edit page</li>
                                    <li>Find the "Vocational Training Institution" dropdown</li>
                                    <li>Select the appropriate LPK from the list</li>
                                    <li>Save the candidate record</li>
                                    <li>The candidate will now be associated with that LPK</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fas fa-wrench"></i> Troubleshooting
                </h4>
                
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Problem</th>
                            <th>Solution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Can't see certain stakeholders</td>
                            <td>Check your user role permissions. LPK users can only see their own institution.</td>
                        </tr>
                        <tr>
                            <td>Can't edit stakeholder information</td>
                            <td>Verify you have the correct role (not read-only). Contact administrator if needed.</td>
                        </tr>
                        <tr>
                            <td>Duplicate stakeholders appear</td>
                            <td>Contact administrator to merge records. Don't create new duplicates.</td>
                        </tr>
                        <tr>
                            <td>Export button not working</td>
                            <td>Check your browser's pop-up blocker settings. Clear cache and try again.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Contact Support -->
            <div class="alert alert-success">
                <h5><i class="fas fa-life-ring"></i> Need More Help?</h5>
                <p class="mb-0">
                    If you can't find the answer to your question in this guide, please contact your system administrator or the TMM support team.
                </p>
            </div>

            <!-- Back Button -->
            <div class="mt-4">
                <?= $this->Html->link(
                    '<i class="fas fa-arrow-left"></i> Back to Dashboard',
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
