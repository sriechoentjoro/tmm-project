<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="candidates help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-user-graduate"></i> <?= __('LPK Candidate Registration - User Guide') ?>
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> <?= __('Welcome to LPK Candidate Management') ?></h5>
                <p><?= __('This module allows authorized LPK institutions to register and manage apprentice candidates. All data is stored in <strong>cms_lpk_candidates</strong> database.') ?></p>
                <p><?= __('<strong>Important:</strong> You can only see and manage candidates registered by YOUR institution. Other LPK candidates are not visible to you.') ?></p>
            </div>

            <!-- Wizard Registration Process -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-magic"></i> <?= __('Candidate Registration Wizard') ?>
                </h4>
                
                <p><?= __('The registration wizard guides you through a step-by-step process to ensure complete and accurate candidate data.') ?></p>

                <h5 class="mt-4"><?= __('Step 1: Identity Number Existence Check') ?></h5>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <?= __('<strong>Duplicate Prevention</strong>') ?><br>
                    <?= __('This step prevents duplicate registrations by checking if the candidate\'s identity number (KTP/NIK) already exists in the system.') ?>
                </div>
                <ol>
                    <li><?= __('Click <strong>LPK Menu → Candidate Registration (Wizard)</strong>') ?></li>
                    <li><?= __('Enter the candidate\'s <strong>Identity Number (NIK/KTP)</strong>') ?></li>
                    <li><?= __('Click <span class="badge badge-primary">Check Availability</span>') ?></li>
                    <li><?= __('System will verify:') ?>
                        <ul>
                            <li><?= __('If number exists → Show error and existing candidate details') ?></li>
                            <li><?= __('If number is new → Proceed to next step') ?></li>
                        </ul>
                    </li>
                </ol>

                <h5 class="mt-4"><?= __('Step 2: Basic Data Entry') ?></h5>
                <p><?= __('Enter the candidate\'s personal and contact information:') ?></p>
                <ul>
                    <li><strong><?= __('Personal Information:') ?></strong>
                        <ul>
                            <li><?= __('Full Name (as per ID card)') ?></li>
                            <li><?= __('Date of Birth') ?></li>
                            <li><?= __('Place of Birth') ?></li>
                            <li><?= __('Gender') ?></li>
                            <li><?= __('Blood Type') ?></li>
                            <li><?= __('Religion') ?></li>
                            <li><?= __('Marital Status') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Contact Information:') ?></strong>
                        <ul>
                            <li><?= __('Phone Number') ?></li>
                            <li><?= __('Email Address') ?></li>
                            <li><?= __('Emergency Contact') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Address Information:') ?></strong>
                        <ul>
                            <li><?= __('Complete Address') ?></li>
                            <li><?= __('Province (Propinsi)') ?></li>
                            <li><?= __('Regency (Kabupaten)') ?></li>
                            <li><?= __('District (Kecamatan)') ?></li>
                            <li><?= __('Village (Kelurahan)') ?></li>
                            <li><?= __('Postal Code') ?></li>
                        </ul>
                    </li>
                </ul>

                <h5 class="mt-4"><?= __('Step 3: Association Data') ?></h5>
                <p><?= __('Link the candidate to relevant entities:') ?></p>
                <ul>
                    <li><strong><?= __('Education Background:') ?></strong>
                        <ul>
                            <li><?= __('Highest Education Level (Strata)') ?></li>
                            <li><?= __('School/University Name') ?></li>
                            <li><?= __('Graduation Year') ?></li>
                            <li><?= __('Field of Study') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Work Experience:') ?></strong>
                        <ul>
                            <li><?= __('Previous Employment (if any)') ?></li>
                            <li><?= __('Job Position') ?></li>
                            <li><?= __('Duration') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Skills & Certifications:') ?></strong>
                        <ul>
                            <li><?= __('Relevant Skills') ?></li>
                            <li><?= __('Certificates/Training') ?></li>
                            <li><?= __('Language Proficiency') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Family Information:') ?></strong>
                        <ul>
                            <li><?= __('Father\'s Name & Occupation') ?></li>
                            <li><?= __('Mother\'s Name & Occupation') ?></li>
                            <li><?= __('Emergency Contact Person') ?></li>
                        </ul>
                    </li>
                </ul>

                <h5 class="mt-4"><?= __('Step 4: Review & Submit') ?></h5>
                <ol>
                    <li><?= __('Review all entered information for accuracy') ?></li>
                    <li><?= __('Make corrections if needed by going back to previous steps') ?></li>
                    <li><?= __('Click <span class="badge badge-success">Submit Registration</span>') ?></li>
                    <li><?= __('System automatically links candidate to YOUR institution') ?></li>
                    <li><?= __('Candidate is now visible in <strong>My Candidates</strong> list') ?></li>
                </ol>
            </div>

            <!-- Managing Candidates -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-users"></i> <?= __('Managing Your Candidates') ?>
                </h4>
                
                <h5 class="mt-4"><?= __('Viewing Your Candidates') ?></h5>
                <ol>
                    <li><?= __('Go to <strong>LPK Menu → My Candidates</strong>') ?></li>
                    <li><?= __('You will see ONLY candidates registered by your institution') ?></li>
                    <li><?= __('Use filters to search by:') ?>
                        <ul>
                            <li><?= __('Name') ?></li>
                            <li><?= __('Identity Number') ?></li>
                            <li><?= __('Registration Date') ?></li>
                            <li><?= __('Status') ?></li>
                        </ul>
                    </li>
                </ol>

                <div class="alert alert-info">
                    <i class="fa fa-shield-alt"></i> <?= __('<strong>Data Privacy</strong>') ?><br>
                    <?= __('For security and privacy, you cannot see candidates registered by other LPK institutions. This ensures each institution manages only their own data.') ?>
                </div>

                <h5 class="mt-4"><?= __('Editing Candidate Information') ?></h5>
                <ol>
                    <li><?= __('Find the candidate in <strong>My Candidates</strong>') ?></li>
                    <li><?= __('Click <span class="badge badge-warning">Edit</span>') ?></li>
                    <li><?= __('Update the necessary information') ?></li>
                    <li><?= __('Click <span class="badge badge-primary">Save Changes</span>') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('Viewing Candidate Details') ?></h5>
                <ol>
                    <li><?= __('Click <span class="badge badge-info">View</span> on any candidate') ?></li>
                    <li><?= __('See complete profile including:') ?>
                        <ul>
                            <li><?= __('Personal data') ?></li>
                            <li><?= __('Education history') ?></li>
                            <li><?= __('Work experience') ?></li>
                            <li><?= __('Family information') ?></li>
                            <li><?= __('Document submission status') ?></li>
                        </ul>
                    </li>
                </ol>
            </div>

            <!-- Document Submission -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-file-upload"></i> <?= __('Document Submission') ?>
                </h4>
                
                <p><?= __('After registering a candidate, you must submit required documents stored in <strong>cms_lpk_candidate_documents</strong> database.') ?></p>

                <h5 class="mt-4"><?= __('Required Documents') ?></h5>
                <ul>
                    <li><i class="fa fa-check text-success"></i> <?= __('Identity Card (KTP) - Scan/Photo') ?></li>
                    <li><i class="fa fa-check text-success"></i> <?= __('Family Card (KK)') ?></li>
                    <li><i class="fa fa-check text-success"></i> <?= __('Birth Certificate') ?></li>
                    <li><i class="fa fa-check text-success"></i> <?= __('Education Certificate (Ijazah)') ?></li>
                    <li><i class="fa fa-check text-success"></i> <?= __('Academic Transcript') ?></li>
                    <li><i class="fa fa-check text-success"></i> <?= __('Passport-size Photo (3x4 cm)') ?></li>
                    <li><i class="fa fa-check text-success"></i> <?= __('Health Certificate') ?></li>
                    <li><i class="fa fa-check text-success"></i> <?= __('Police Clearance (SKCK)') ?></li>
                </ul>

                <h5 class="mt-4"><?= __('How to Submit Documents') ?></h5>
                <ol>
                    <li><?= __('Go to <strong>LPK Menu → Document Submission</strong>') ?></li>
                    <li><?= __('Select the candidate from your list') ?></li>
                    <li><?= __('For each required document:') ?>
                        <ul>
                            <li><?= __('Click <span class="badge badge-primary">Upload</span>') ?></li>
                            <li><?= __('Select file (PDF, JPG, PNG accepted)') ?></li>
                            <li><?= __('Add notes if needed') ?></li>
                            <li><?= __('Click <span class="badge badge-success">Submit</span>') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Track submission status in real-time') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('Document Dashboard') ?></h5>
                <ol>
                    <li><?= __('Access <strong>LPK Menu → Document Dashboard</strong>') ?></li>
                    <li><?= __('View statistics:') ?>
                        <ul>
                            <li><?= __('Total candidates') ?></li>
                            <li><?= __('Documents submitted') ?></li>
                            <li><?= __('Documents pending') ?></li>
                            <li><?= __('Documents approved/rejected') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Monitor completion percentage per candidate') ?></li>
                </ol>
            </div>

            <!-- Database Structure -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-database"></i> <?= __('Database Structure') ?>
                </h4>
                
                <p><?= __('Your LPK data is organized across two databases:') ?></p>
                
                <h5>cms_lpk_candidates</h5>
                <ul>
                    <li><?= __('<code>candidates</code> - Main candidate records') ?></li>
                    <li><?= __('<code>candidate_educations</code> - Education history') ?></li>
                    <li><?= __('<code>candidate_experiences</code> - Work experience') ?></li>
                    <li><?= __('<code>candidate_families</code> - Family information') ?></li>
                    <li><?= __('<code>candidate_certifications</code> - Skills & certificates') ?></li>
                </ul>

                <h5>cms_lpk_candidate_documents</h5>
                <ul>
                    <li><?= __('<code>candidate_documents</code> - Uploaded files') ?></li>
                    <li><?= __('<code>candidate_submission_documents</code> - Submission tracking') ?></li>
                    <li><?= __('<code>document_categories</code> - Document types') ?></li>
                </ul>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-lightbulb"></i> <?= __('Best Practices') ?>
                </h4>
                <ul>
                    <li><?= __('<strong>Verify Identity:</strong> Always verify candidate\'s identity documents before registration') ?></li>
                    <li><?= __('<strong>Complete Data:</strong> Fill all required fields to avoid processing delays') ?></li>
                    <li><?= __('<strong>Document Quality:</strong> Ensure uploaded documents are clear and legible') ?></li>
                    <li><?= __('<strong>Regular Updates:</strong> Keep candidate information current') ?></li>
                    <li><?= __('<strong>Timely Submission:</strong> Submit all required documents promptly') ?></li>
                    <li><?= __('<strong>Follow-up:</strong> Monitor document approval status regularly') ?></li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="alert alert-success">
                <h5><i class="fa fa-link"></i> <?= __('Quick Links') ?></h5>
                <p class="mb-2">
                    <?= $this->Html->link('<i class="fa fa-tachometer-alt"></i> ' . __('LPK Dashboard'), ['controller' => 'Dashboard', 'action' => 'lpk'], ['class' => 'btn btn-sm btn-primary mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-user-plus"></i> ' . __('Register New Candidate'), ['controller' => 'Candidates', 'action' => 'wizard'], ['class' => 'btn btn-sm btn-success mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-users"></i> ' . __('My Candidates'), ['controller' => 'Candidates', 'action' => 'index'], ['class' => 'btn btn-sm btn-info mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-file-upload"></i> ' . __('Submit Documents'), ['controller' => 'CandidateDocuments', 'action' => 'index'], ['class' => 'btn btn-sm btn-info mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-info-circle"></i> ' . __('Document Help'), ['controller' => 'CandidateDocuments', 'action' => 'help'], ['class' => 'btn btn-sm btn-warning', 'escape' => false]) ?>
                </p>
            </div>

        </div>
    </div>
</div>
