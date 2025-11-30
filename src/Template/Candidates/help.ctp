<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="candidates help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-user-graduate"></i> LPK Candidate Registration - User Guide
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> Welcome to LPK Candidate Management</h5>
                <p>This module allows authorized LPK institutions to register and manage apprentice candidates. All data is stored in <strong>cms_lpk_candidates</strong> database.</p>
                <p><strong>Important:</strong> You can only see and manage candidates registered by YOUR institution. Other LPK candidates are not visible to you.</p>
            </div>

            <!-- Wizard Registration Process -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-magic"></i> Candidate Registration Wizard
                </h4>
                
                <p>The registration wizard guides you through a step-by-step process to ensure complete and accurate candidate data.</p>

                <h5 class="mt-4">Step 1: Identity Number Existence Check</h5>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Duplicate Prevention</strong><br>
                    This step prevents duplicate registrations by checking if the candidate's identity number (KTP/NIK) already exists in the system.
                </div>
                <ol>
                    <li>Click <strong>LPK Menu → Candidate Registration (Wizard)</strong></li>
                    <li>Enter the candidate's <strong>Identity Number (NIK/KTP)</strong></li>
                    <li>Click <span class="badge badge-primary">Check Availability</span></li>
                    <li>System will verify:
                        <ul>
                            <li>If number exists → Show error and existing candidate details</li>
                            <li>If number is new → Proceed to next step</li>
                        </ul>
                    </li>
                </ol>

                <h5 class="mt-4">Step 2: Basic Data Entry</h5>
                <p>Enter the candidate's personal and contact information:</p>
                <ul>
                    <li><strong>Personal Information:</strong>
                        <ul>
                            <li>Full Name (as per ID card)</li>
                            <li>Date of Birth</li>
                            <li>Place of Birth</li>
                            <li>Gender</li>
                            <li>Blood Type</li>
                            <li>Religion</li>
                            <li>Marital Status</li>
                        </ul>
                    </li>
                    <li><strong>Contact Information:</strong>
                        <ul>
                            <li>Phone Number</li>
                            <li>Email Address</li>
                            <li>Emergency Contact</li>
                        </ul>
                    </li>
                    <li><strong>Address Information:</strong>
                        <ul>
                            <li>Complete Address</li>
                            <li>Province (Propinsi)</li>
                            <li>Regency (Kabupaten)</li>
                            <li>District (Kecamatan)</li>
                            <li>Village (Kelurahan)</li>
                            <li>Postal Code</li>
                        </ul>
                    </li>
                </ul>

                <h5 class="mt-4">Step 3: Association Data</h5>
                <p>Link the candidate to relevant entities:</p>
                <ul>
                    <li><strong>Education Background:</strong>
                        <ul>
                            <li>Highest Education Level (Strata)</li>
                            <li>School/University Name</li>
                            <li>Graduation Year</li>
                            <li>Field of Study</li>
                        </ul>
                    </li>
                    <li><strong>Work Experience:</strong>
                        <ul>
                            <li>Previous Employment (if any)</li>
                            <li>Job Position</li>
                            <li>Duration</li>
                        </ul>
                    </li>
                    <li><strong>Skills & Certifications:</strong>
                        <ul>
                            <li>Relevant Skills</li>
                            <li>Certificates/Training</li>
                            <li>Language Proficiency</li>
                        </ul>
                    </li>
                    <li><strong>Family Information:</strong>
                        <ul>
                            <li>Father's Name & Occupation</li>
                            <li>Mother's Name & Occupation</li>
                            <li>Emergency Contact Person</li>
                        </ul>
                    </li>
                </ul>

                <h5 class="mt-4">Step 4: Review & Submit</h5>
                <ol>
                    <li>Review all entered information for accuracy</li>
                    <li>Make corrections if needed by going back to previous steps</li>
                    <li>Click <span class="badge badge-success">Submit Registration</span></li>
                    <li>System automatically links candidate to YOUR institution</li>
                    <li>Candidate is now visible in <strong>My Candidates</strong> list</li>
                </ol>
            </div>

            <!-- Managing Candidates -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-users"></i> Managing Your Candidates
                </h4>
                
                <h5 class="mt-4">Viewing Your Candidates</h5>
                <ol>
                    <li>Go to <strong>LPK Menu → My Candidates</strong></li>
                    <li>You will see ONLY candidates registered by your institution</li>
                    <li>Use filters to search by:
                        <ul>
                            <li>Name</li>
                            <li>Identity Number</li>
                            <li>Registration Date</li>
                            <li>Status</li>
                        </ul>
                    </li>
                </ol>

                <div class="alert alert-info">
                    <i class="fa fa-shield-alt"></i> <strong>Data Privacy</strong><br>
                    For security and privacy, you cannot see candidates registered by other LPK institutions. This ensures each institution manages only their own data.
                </div>

                <h5 class="mt-4">Editing Candidate Information</h5>
                <ol>
                    <li>Find the candidate in <strong>My Candidates</strong></li>
                    <li>Click <span class="badge badge-warning">Edit</span></li>
                    <li>Update the necessary information</li>
                    <li>Click <span class="badge badge-primary">Save Changes</span></li>
                </ol>

                <h5 class="mt-4">Viewing Candidate Details</h5>
                <ol>
                    <li>Click <span class="badge badge-info">View</span> on any candidate</li>
                    <li>See complete profile including:
                        <ul>
                            <li>Personal data</li>
                            <li>Education history</li>
                            <li>Work experience</li>
                            <li>Family information</li>
                            <li>Document submission status</li>
                        </ul>
                    </li>
                </ol>
            </div>

            <!-- Document Submission -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-file-upload"></i> Document Submission
                </h4>
                
                <p>After registering a candidate, you must submit required documents stored in <strong>cms_lpk_candidate_documents</strong> database.</p>

                <h5 class="mt-4">Required Documents</h5>
                <ul>
                    <li><i class="fa fa-check text-success"></i> Identity Card (KTP) - Scan/Photo</li>
                    <li><i class="fa fa-check text-success"></i> Family Card (KK)</li>
                    <li><i class="fa fa-check text-success"></i> Birth Certificate</li>
                    <li><i class="fa fa-check text-success"></i> Education Certificate (Ijazah)</li>
                    <li><i class="fa fa-check text-success"></i> Academic Transcript</li>
                    <li><i class="fa fa-check text-success"></i> Passport-size Photo (3x4 cm)</li>
                    <li><i class="fa fa-check text-success"></i> Health Certificate</li>
                    <li><i class="fa fa-check text-success"></i> Police Clearance (SKCK)</li>
                </ul>

                <h5 class="mt-4">How to Submit Documents</h5>
                <ol>
                    <li>Go to <strong>LPK Menu → Document Submission</strong></li>
                    <li>Select the candidate from your list</li>
                    <li>For each required document:
                        <ul>
                            <li>Click <span class="badge badge-primary">Upload</span></li>
                            <li>Select file (PDF, JPG, PNG accepted)</li>
                            <li>Add notes if needed</li>
                            <li>Click <span class="badge badge-success">Submit</span></li>
                        </ul>
                    </li>
                    <li>Track submission status in real-time</li>
                </ol>

                <h5 class="mt-4">Document Dashboard</h5>
                <ol>
                    <li>Access <strong>LPK Menu → Document Dashboard</strong></li>
                    <li>View statistics:
                        <ul>
                            <li>Total candidates</li>
                            <li>Documents submitted</li>
                            <li>Documents pending</li>
                            <li>Documents approved/rejected</li>
                        </ul>
                    </li>
                    <li>Monitor completion percentage per candidate</li>
                </ol>
            </div>

            <!-- Database Structure -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-database"></i> Database Structure
                </h4>
                
                <p>Your LPK data is organized across two databases:</p>
                
                <h5>cms_lpk_candidates</h5>
                <ul>
                    <li><code>candidates</code> - Main candidate records</li>
                    <li><code>candidate_educations</code> - Education history</li>
                    <li><code>candidate_experiences</code> - Work experience</li>
                    <li><code>candidate_families</code> - Family information</li>
                    <li><code>candidate_certifications</code> - Skills & certificates</li>
                </ul>

                <h5>cms_lpk_candidate_documents</h5>
                <ul>
                    <li><code>candidate_documents</code> - Uploaded files</li>
                    <li><code>candidate_submission_documents</code> - Submission tracking</li>
                    <li><code>document_categories</code> - Document types</li>
                </ul>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-lightbulb-o"></i> Best Practices
                </h4>
                <ul>
                    <li><strong>Verify Identity:</strong> Always verify candidate's identity documents before registration</li>
                    <li><strong>Complete Data:</strong> Fill all required fields to avoid processing delays</li>
                    <li><strong>Document Quality:</strong> Ensure uploaded documents are clear and legible</li>
                    <li><strong>Regular Updates:</strong> Keep candidate information current</li>
                    <li><strong>Timely Submission:</strong> Submit all required documents promptly</li>
                    <li><strong>Follow-up:</strong> Monitor document approval status regularly</li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="alert alert-success">
                <h5><i class="fa fa-link"></i> Quick Links</h5>
                <p class="mb-2">
                    <?= $this->Html->link('<i class="fa fa-tachometer-alt"></i> LPK Dashboard', ['controller' => 'Dashboard', 'action' => 'lpk'], ['class' => 'btn btn-sm btn-primary mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-user-plus"></i> Register New Candidate', ['controller' => 'Candidates', 'action' => 'wizard'], ['class' => 'btn btn-sm btn-success mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-users"></i> My Candidates', ['controller' => 'Candidates', 'action' => 'index'], ['class' => 'btn btn-sm btn-info mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-file-upload"></i> Submit Documents', ['controller' => 'CandidateDocuments', 'action' => 'index'], ['class' => 'btn btn-sm btn-info mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-info-circle"></i> Document Help', ['controller' => 'CandidateDocuments', 'action' => 'help'], ['class' => 'btn btn-sm btn-warning', 'escape' => false]) ?>
                </p>
            </div>

        </div>
    </div>
</div>
