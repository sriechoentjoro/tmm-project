<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="stakeholders help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-users-cog"></i> <?= __('Stakeholder Management - User Guide') ?>
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> <?= __('Welcome to Stakeholder Management') ?></h5>
                <p><?= __('This module manages all stakeholder data in the <strong>cms_tmm_stakeholders</strong> database. Stakeholders include institutions, organizations, and associations involved in the apprentice program.') ?></p>
            </div>

            <!-- Vocational Training Institutions -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-graduation-cap"></i> <?= __('Vocational Training Institutions (LPK)') ?>
                </h4>
                
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <?= __('<strong>Special Authorization Process</strong>') ?><br>
                    <?= __('Vocational Training Institutions have a special authorization workflow to recruit apprentice candidates. This ensures proper vetting and compliance.') ?>
                </div>

                <h5 class="mt-4"><?= __('How to Register a Vocational Training Institution') ?></h5>
                <ol>
                    <li><?= __('Navigate to <strong>Stakeholder Management → Vocational Training Institutions</strong>') ?></li>
                    <li><?= __('Click <span class="badge badge-primary">New Institution</span>') ?></li>
                    <li><?= __('Fill in the required information:') ?>
                        <ul>
                            <li><?= __('<strong>Institution Name:</strong> Official registered name') ?></li>
                            <li><?= __('<strong>License Number:</strong> Government-issued license') ?></li>
                            <li><?= __('<strong>Address:</strong> Complete physical address') ?></li>
                            <li><?= __('<strong>Contact Information:</strong> Phone, email, website') ?></li>
                            <li><?= __('<strong>Director/Head:</strong> Name of institution leader') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Submit for review') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('Authorization to Recruit Candidates') ?></h5>
                <ol>
                    <li><?= __('Institution must be verified and approved by administrators') ?></li>
                    <li><?= __('Once approved, the institution receives:') ?>
                        <ul>
                            <li><?= __('Unique institution ID') ?></li>
                            <li><?= __('Access credentials for LPK user account') ?></li>
                            <li><?= __('Authorization to register candidates') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Institution can then access the <strong>LPK Menu</strong> to manage candidates') ?></li>
                    <li><?= __('All candidates registered by an LPK are linked to that institution') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('Monitoring LPK Activities') ?></h5>
                <ul>
                    <li><?= __('View total candidates registered per institution') ?></li>
                    <li><?= __('Track document submission status') ?></li>
                    <li><?= __('Monitor compliance with registration requirements') ?></li>
                    <li><?= __('Review authorization status and renewal dates') ?></li>
                </ul>
            </div>

            <!-- Special Skill Support Institutions -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-hands-helping"></i> <?= __('Special Skill Support Institutions') ?>
                </h4>
                
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <?= __('<strong>Special Authorization Process</strong>') ?><br>
                    <?= __('Similar to LPKs, Special Skill Support Institutions require special authorization to participate in the apprentice recruitment process.') ?>
                </div>

                <h5 class="mt-4"><?= __('How to Register a Special Skill Support Institution') ?></h5>
                <ol>
                    <li><?= __('Go to <strong>Stakeholder Management → Special Skill Support Institutions</strong>') ?></li>
                    <li><?= __('Click <span class="badge badge-primary">New Institution</span>') ?></li>
                    <li><?= __('Complete the registration form with:') ?>
                        <ul>
                            <li><?= __('<strong>Institution Name</strong>') ?></li>
                            <li><?= __('<strong>Specialization Area:</strong> Type of skills supported') ?></li>
                            <li><?= __('<strong>Certification/Accreditation:</strong> Relevant credentials') ?></li>
                            <li><?= __('<strong>Contact Details</strong>') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Submit for authorization review') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('Authorization Process') ?></h5>
                <ol>
                    <li><?= __('Administrator reviews institution credentials') ?></li>
                    <li><?= __('Verification of specialization and capacity') ?></li>
                    <li><?= __('Approval grants recruitment authorization') ?></li>
                    <li><?= __('Institution receives access to candidate recruitment features') ?></li>
                </ol>
            </div>

            <!-- Acceptance Organizations -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-building"></i> <?= __('Acceptance Organizations') ?>
                </h4>
                
                <p><?= __('These are Japanese companies and organizations that accept apprentices for training.') ?></p>

                <h5 class="mt-4"><?= __('Managing Acceptance Organizations') ?></h5>
                <ol>
                    <li><?= __('Navigate to <strong>Stakeholder Management → Acceptance Organizations</strong>') ?></li>
                    <li><?= __('Add new organizations with:') ?>
                        <ul>
                            <li><?= __('Company name (Japanese and English)') ?></li>
                            <li><?= __('Industry sector') ?></li>
                            <li><?= __('Location in Japan (Prefecture)') ?></li>
                            <li><?= __('Capacity for apprentices') ?></li>
                            <li><?= __('Contact person details') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Link organizations to apprentice orders') ?></li>
                    <li><?= __('Track placement history') ?></li>
                </ol>
            </div>

            <!-- Cooperative Associations -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-handshake"></i> <?= __('Cooperative Associations') ?>
                </h4>
                
                <p><?= __('Cooperative associations facilitate the apprentice program in Japan.') ?></p>

                <h5 class="mt-4"><?= __('Managing Cooperative Associations') ?></h5>
                <ol>
                    <li><?= __('Go to <strong>Stakeholder Management → Cooperative Associations</strong>') ?></li>
                    <li><?= __('Register associations with:') ?>
                        <ul>
                            <li><?= __('Association name') ?></li>
                            <li><?= __('Registration number') ?></li>
                            <li><?= __('Service areas') ?></li>
                            <li><?= __('Partner organizations') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Link to acceptance organizations') ?></li>
                    <li><?= __('Monitor association activities') ?></li>
                </ol>
            </div>

            <!-- Database Information -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-database"></i> <?= __('Database Structure') ?>
                </h4>
                
                <p><?= __('All stakeholder data is stored in the <strong>cms_tmm_stakeholders</strong> database, which includes:') ?></p>
                <ul>
                    <li><?= __('<code>vocational_training_institutions</code> - LPK data with authorization status') ?></li>
                    <li><?= __('<code>special_skill_support_institutions</code> - Support institution data') ?></li>
                    <li><?= __('<code>acceptance_organizations</code> - Japanese receiving companies') ?></li>
                    <li><?= __('<code>cooperative_associations</code> - Facilitating associations') ?></li>
                    <li><?= __('Related tables for addresses, contacts, and authorization records') ?></li>
                </ul>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-lightbulb-o"></i> <?= __('Best Practices') ?>
                </h4>
                <ul>
                    <li><?= __('<strong>Verify Credentials:</strong> Always verify institution licenses and certifications before approval') ?></li>
                    <li><?= __('<strong>Regular Audits:</strong> Review authorization status periodically') ?></li>
                    <li><?= __('<strong>Complete Data:</strong> Ensure all contact information is current and complete') ?></li>
                    <li><?= __('<strong>Authorization Tracking:</strong> Monitor expiration dates for licenses and authorizations') ?></li>
                    <li><?= __('<strong>Data Integrity:</strong> Use standard data input interfaces for consistency') ?></li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="alert alert-success">
                <h5><i class="fa fa-link"></i> <?= __('Quick Links') ?></h5>
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
