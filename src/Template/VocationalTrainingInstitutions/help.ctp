<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="stakeholders help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-users-cog"></i> Stakeholder Management - User Guide
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> Welcome to Stakeholder Management</h5>
                <p>This module manages all stakeholder data in the <strong>cms_tmm_stakeholders</strong> database. Stakeholders include institutions, organizations, and associations involved in the apprentice program.</p>
            </div>

            <!-- Vocational Training Institutions -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-graduation-cap"></i> Vocational Training Institutions (LPK)
                </h4>
                
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Special Authorization Process</strong><br>
                    Vocational Training Institutions have a special authorization workflow to recruit apprentice candidates. This ensures proper vetting and compliance.
                </div>

                <h5 class="mt-4">How to Register a Vocational Training Institution</h5>
                <ol>
                    <li>Navigate to <strong>Stakeholder Management → Vocational Training Institutions</strong></li>
                    <li>Click <span class="badge badge-primary">New Institution</span></li>
                    <li>Fill in the required information:
                        <ul>
                            <li><strong>Institution Name:</strong> Official registered name</li>
                            <li><strong>License Number:</strong> Government-issued license</li>
                            <li><strong>Address:</strong> Complete physical address</li>
                            <li><strong>Contact Information:</strong> Phone, email, website</li>
                            <li><strong>Director/Head:</strong> Name of institution leader</li>
                        </ul>
                    </li>
                    <li>Submit for review</li>
                </ol>

                <h5 class="mt-4">Authorization to Recruit Candidates</h5>
                <ol>
                    <li>Institution must be verified and approved by administrators</li>
                    <li>Once approved, the institution receives:
                        <ul>
                            <li>Unique institution ID</li>
                            <li>Access credentials for LPK user account</li>
                            <li>Authorization to register candidates</li>
                        </ul>
                    </li>
                    <li>Institution can then access the <strong>LPK Menu</strong> to manage candidates</li>
                    <li>All candidates registered by an LPK are linked to that institution</li>
                </ol>

                <h5 class="mt-4">Monitoring LPK Activities</h5>
                <ul>
                    <li>View total candidates registered per institution</li>
                    <li>Track document submission status</li>
                    <li>Monitor compliance with registration requirements</li>
                    <li>Review authorization status and renewal dates</li>
                </ul>
            </div>

            <!-- Special Skill Support Institutions -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-hands-helping"></i> Special Skill Support Institutions
                </h4>
                
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Special Authorization Process</strong><br>
                    Similar to LPKs, Special Skill Support Institutions require special authorization to participate in the apprentice recruitment process.
                </div>

                <h5 class="mt-4">How to Register a Special Skill Support Institution</h5>
                <ol>
                    <li>Go to <strong>Stakeholder Management → Special Skill Support Institutions</strong></li>
                    <li>Click <span class="badge badge-primary">New Institution</span></li>
                    <li>Complete the registration form with:
                        <ul>
                            <li><strong>Institution Name</strong></li>
                            <li><strong>Specialization Area:</strong> Type of skills supported</li>
                            <li><strong>Certification/Accreditation:</strong> Relevant credentials</li>
                            <li><strong>Contact Details</strong></li>
                        </ul>
                    </li>
                    <li>Submit for authorization review</li>
                </ol>

                <h5 class="mt-4">Authorization Process</h5>
                <ol>
                    <li>Administrator reviews institution credentials</li>
                    <li>Verification of specialization and capacity</li>
                    <li>Approval grants recruitment authorization</li>
                    <li>Institution receives access to candidate recruitment features</li>
                </ol>
            </div>

            <!-- Acceptance Organizations -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-building"></i> Acceptance Organizations
                </h4>
                
                <p>These are Japanese companies and organizations that accept apprentices for training.</p>

                <h5 class="mt-4">Managing Acceptance Organizations</h5>
                <ol>
                    <li>Navigate to <strong>Stakeholder Management → Acceptance Organizations</strong></li>
                    <li>Add new organizations with:
                        <ul>
                            <li>Company name (Japanese and English)</li>
                            <li>Industry sector</li>
                            <li>Location in Japan (Prefecture)</li>
                            <li>Capacity for apprentices</li>
                            <li>Contact person details</li>
                        </ul>
                    </li>
                    <li>Link organizations to apprentice orders</li>
                    <li>Track placement history</li>
                </ol>
            </div>

            <!-- Cooperative Associations -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-handshake"></i> Cooperative Associations
                </h4>
                
                <p>Cooperative associations facilitate the apprentice program in Japan.</p>

                <h5 class="mt-4">Managing Cooperative Associations</h5>
                <ol>
                    <li>Go to <strong>Stakeholder Management → Cooperative Associations</strong></li>
                    <li>Register associations with:
                        <ul>
                            <li>Association name</li>
                            <li>Registration number</li>
                            <li>Service areas</li>
                            <li>Partner organizations</li>
                        </ul>
                    </li>
                    <li>Link to acceptance organizations</li>
                    <li>Monitor association activities</li>
                </ol>
            </div>

            <!-- Database Information -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-database"></i> Database Structure
                </h4>
                
                <p>All stakeholder data is stored in the <strong>cms_tmm_stakeholders</strong> database, which includes:</p>
                <ul>
                    <li><code>vocational_training_institutions</code> - LPK data with authorization status</li>
                    <li><code>special_skill_support_institutions</code> - Support institution data</li>
                    <li><code>acceptance_organizations</code> - Japanese receiving companies</li>
                    <li><code>cooperative_associations</code> - Facilitating associations</li>
                    <li>Related tables for addresses, contacts, and authorization records</li>
                </ul>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-lightbulb-o"></i> Best Practices
                </h4>
                <ul>
                    <li><strong>Verify Credentials:</strong> Always verify institution licenses and certifications before approval</li>
                    <li><strong>Regular Audits:</strong> Review authorization status periodically</li>
                    <li><strong>Complete Data:</strong> Ensure all contact information is current and complete</li>
                    <li><strong>Authorization Tracking:</strong> Monitor expiration dates for licenses and authorizations</li>
                    <li><strong>Data Integrity:</strong> Use standard data input interfaces for consistency</li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="alert alert-success">
                <h5><i class="fa fa-link"></i> Quick Links</h5>
                <p class="mb-2">
                    <?= $this->Html->link('<i class="fa fa-tachometer-alt"></i> Dashboard', ['controller' => 'Dashboard', 'action' => 'stakeholders'], ['class' => 'btn btn-sm btn-primary mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-graduation-cap"></i> LPK Institutions', ['controller' => 'VocationalTrainingInstitutions', 'action' => 'index'], ['class' => 'btn btn-sm btn-info mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-hands-helping"></i> Support Institutions', ['controller' => 'SpecialSkillSupportInstitutions', 'action' => 'index'], ['class' => 'btn btn-sm btn-info mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-building"></i> Organizations', ['controller' => 'AcceptanceOrganizations', 'action' => 'index'], ['class' => 'btn btn-sm btn-info', 'escape' => false]) ?>
                </p>
            </div>

        </div>
    </div>
</div>
