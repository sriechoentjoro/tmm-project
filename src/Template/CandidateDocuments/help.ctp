<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="candidate-documents help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-file-upload"></i> <?= __('Document Submission - Help Guide') ?>
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> <?= __('Document Submission System') ?></h5>
                <p><?= __('This guide explains how to properly submit and manage candidate documents in the <strong>cms_lpk_candidate_documents</strong> database.') ?></p>
            </div>

            <!-- Document Requirements -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-clipboard-list"></i> <?= __('Required Documents Checklist') ?>
                </h4>
                
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="30%"><?= __('Document Type') ?></th>
                            <th width="40%"><?= __('Requirements') ?></th>
                            <th width="25%"><?= __('File Format') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><?= __('<strong>Identity Card (KTP)</strong>') ?></td>
                            <td><?= __('Clear scan/photo of both sides, valid and not expired') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 2MB') ?></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><?= __('<strong>Family Card (KK)</strong>') ?></td>
                            <td><?= __('Complete family card showing candidate\'s name') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 2MB') ?></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><?= __('<strong>Birth Certificate</strong>') ?></td>
                            <td><?= __('Official birth certificate or extract') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 2MB') ?></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td><?= __('<strong>Education Certificate</strong>') ?></td>
                            <td><?= __('Highest education diploma (SMA/SMK/D3/S1)') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 3MB') ?></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td><?= __('<strong>Academic Transcript</strong>') ?></td>
                            <td><?= __('Complete transcript of grades') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 3MB') ?></td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td><?= __('<strong>Passport Photo</strong>') ?></td>
                            <td><?= __('Recent photo, 3x4 cm, white background') ?></td>
                            <td><?= __('JPG, PNG<br>Max 1MB') ?></td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td><?= __('<strong>Health Certificate</strong>') ?></td>
                            <td><?= __('Medical check-up results (not older than 3 months)') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 2MB') ?></td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td><?= __('<strong>Police Clearance (SKCK)</strong>') ?></td>
                            <td><?= __('Valid SKCK (not older than 6 months)') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 2MB') ?></td>
                        </tr>
                        <tr>
                            <td>9</td>
                            <td><?= __('<strong>Passport (if available)</strong>') ?></td>
                            <td><?= __('Valid passport with at least 18 months validity') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 2MB') ?></td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td><?= __('<strong>Training Certificates</strong>') ?></td>
                            <td><?= __('Any relevant vocational training certificates') ?></td>
                            <td><?= __('PDF, JPG, PNG<br>Max 2MB each') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Upload Process -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-cloud-upload-alt"></i> <?= __('Document Upload Process') ?>
                </h4>
                
                <h5 class="mt-4"><?= __('Step-by-Step Upload Guide') ?></h5>
                <ol>
                    <li><strong><?= __('Access Document Submission') ?></strong>
                        <ul>
                            <li><?= __('Go to <strong>LPK Menu → Document Submission</strong>') ?></li>
                            <li><?= __('Select the candidate from your institution\'s list') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Choose Document Category') ?></strong>
                        <ul>
                            <li><?= __('Select the document type from dropdown') ?></li>
                            <li><?= __('Read the specific requirements for that document') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Prepare Your File') ?></strong>
                        <ul>
                            <li><?= __('Ensure file meets size and format requirements') ?></li>
                            <li><?= __('Rename file descriptively (e.g., "KTP_JohnDoe.pdf")') ?></li>
                            <li><?= __('Verify document is clear and legible') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Upload Document') ?></strong>
                        <ul>
                            <li><?= __('Click <span class="badge badge-primary">Choose File</span>') ?></li>
                            <li><?= __('Select your prepared document') ?></li>
                            <li><?= __('Add notes or comments (optional but recommended)') ?></li>
                            <li><?= __('Click <span class="badge badge-success">Upload</span>') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Verify Upload') ?></strong>
                        <ul>
                            <li><?= __('Check for success message') ?></li>
                            <li><?= __('Verify document appears in submission list') ?></li>
                            <li><?= __('Note the submission date and status') ?></li>
                        </ul>
                    </li>
                </ol>

                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Important Notes</strong>
                    <ul class="mb-0">
                        <li><?= __('Do not upload password-protected files') ?></li>
                        <li><?= __('Ensure documents are not corrupted') ?></li>
                        <li><?= __('Use clear scans, avoid blurry photos') ?></li>
                        <li><?= __('Submit documents in the correct category') ?></li>
                    </ul>
                </div>
            </div>

            <!-- Document Status -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-tasks"></i> <?= __('Document Status Tracking') ?>
                </h4>
                
                <h5 class="mt-4"><?= __('Understanding Document Status') ?></h5>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Meaning') ?></th>
                            <th><?= __('Action Required') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= __('<span class="badge badge-secondary">Not Submitted</span>') ?></td>
                            <td><?= __('Document has not been uploaded yet') ?></td>
                            <td><?= __('Upload the required document') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('<span class="badge badge-warning">Pending Review</span>') ?></td>
                            <td><?= __('Document uploaded, awaiting verification') ?></td>
                            <td><?= __('Wait for admin review') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('<span class="badge badge-info">Under Review</span>') ?></td>
                            <td><?= __('Document is being verified by admin') ?></td>
                            <td><?= __('No action needed') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('<span class="badge badge-success">Approved</span>') ?></td>
                            <td><?= __('Document verified and accepted') ?></td>
                            <td><?= __('No action needed') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('<span class="badge badge-danger">Rejected</span>') ?></td>
                            <td><?= __('Document does not meet requirements') ?></td>
                            <td><?= __('Check rejection reason and re-upload') ?></td>
                        </tr>
                        <tr>
                            <td><?= __('<span class="badge badge-primary">Resubmitted</span>') ?></td>
                            <td><?= __('Document re-uploaded after rejection') ?></td>
                            <td><?= __('Wait for re-review') ?></td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-4"><?= __('Checking Document Status') ?></h5>
                <ol>
                    <li><?= __('Go to <strong>LPK Menu → Document Dashboard</strong>') ?></li>
                    <li><?= __('View overall completion percentage') ?></li>
                    <li><?= __('See detailed status for each document type') ?></li>
                    <li><?= __('Click on candidate name to see individual document status') ?></li>
                </ol>
            </div>

            <!-- Handling Rejections -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-redo"></i> <?= __('Handling Document Rejections') ?>
                </h4>
                
                <h5 class="mt-4"><?= __('Common Rejection Reasons') ?></h5>
                <ul>
                    <li><i class="fa fa-times text-danger"></i> <?= __('<strong>Poor Quality:</strong> Document is blurry, dark, or illegible') ?></li>
                    <li><i class="fa fa-times text-danger"></i> <?= __('<strong>Incomplete:</strong> Missing pages or information') ?></li>
                    <li><i class="fa fa-times text-danger"></i> <?= __('<strong>Expired:</strong> Document validity has expired') ?></li>
                    <li><i class="fa fa-times text-danger"></i> <?= __('<strong>Wrong Format:</strong> File type not supported') ?></li>
                    <li><i class="fa fa-times text-danger"></i> <?= __('<strong>Wrong Category:</strong> Document uploaded in incorrect category') ?></li>
                    <li><i class="fa fa-times text-danger"></i> <?= __('<strong>Mismatch:</strong> Information doesn\'t match candidate data') ?></li>
                </ul>

                <h5 class="mt-4"><?= __('How to Resubmit Rejected Documents') ?></h5>
                <ol>
                    <li><?= __('Check the rejection reason in the document status') ?></li>
                    <li><?= __('Prepare a corrected version of the document') ?></li>
                    <li><?= __('Go to <strong>Document Submission</strong>') ?></li>
                    <li><?= __('Find the rejected document') ?></li>
                    <li><?= __('Click <span class="badge badge-warning">Resubmit</span>') ?></li>
                    <li><?= __('Upload the corrected document') ?></li>
                    <li><?= __('Add notes explaining the corrections made') ?></li>
                    <li><?= __('Submit for re-review') ?></li>
                </ol>
            </div>

            <!-- Dashboard Features -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-chart-bar"></i> <?= __('Document Dashboard Features') ?>
                </h4>
                
                <h5 class="mt-4"><?= __('Dashboard Overview') ?></h5>
                <p><?= __('The Document Dashboard (<strong>LPK Menu → Document Dashboard</strong>) provides:') ?></p>
                <ul>
                    <li><strong><?= __('Summary Statistics:') ?></strong>
                        <ul>
                            <li><?= __('Total candidates registered') ?></li>
                            <li><?= __('Total documents submitted') ?></li>
                            <li><?= __('Documents pending review') ?></li>
                            <li><?= __('Documents approved') ?></li>
                            <li><?= __('Documents rejected') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Completion Tracking:') ?></strong>
                        <ul>
                            <li><?= __('Overall completion percentage') ?></li>
                            <li><?= __('Per-candidate completion status') ?></li>
                            <li><?= __('Missing documents list') ?></li>
                        </ul>
                    </li>
                    <li><strong><?= __('Visual Reports:') ?></strong>
                        <ul>
                            <li><?= __('Charts showing submission progress') ?></li>
                            <li><?= __('Status distribution graphs') ?></li>
                            <li><?= __('Timeline of submissions') ?></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-lightbulb-o"></i> <?= __('Best Practices') ?>
                </h4>
                <ul>
                    <li><?= __('<strong>Scan Quality:</strong> Use 300 DPI or higher for scans') ?></li>
                    <li><?= __('<strong>File Naming:</strong> Use descriptive names (e.g., "KTP_NamaCandidate_2024.pdf")') ?></li>
                    <li><?= __('<strong>Batch Upload:</strong> Prepare all documents before starting upload session') ?></li>
                    <li><?= __('<strong>Regular Checks:</strong> Monitor dashboard daily for status updates') ?></li>
                    <li><?= __('<strong>Prompt Resubmission:</strong> Address rejections within 24 hours') ?></li>
                    <li><?= __('<strong>Backup Copies:</strong> Keep original documents and digital backups') ?></li>
                    <li><?= __('<strong>Verify Before Upload:</strong> Double-check document quality and correctness') ?></li>
                    <li><?= __('<strong>Complete Sets:</strong> Submit all required documents for each candidate') ?></li>
                </ul>
            </div>

            <!-- Troubleshooting -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-wrench"></i> <?= __('Troubleshooting') ?>
                </h4>
                
                <h5 class="mt-3"><?= __('Upload Fails') ?></h5>
                <ul>
                    <li><?= __('Check file size (must be under maximum limit)') ?></li>
                    <li><?= __('Verify file format is supported (PDF, JPG, PNG)') ?></li>
                    <li><?= __('Ensure stable internet connection') ?></li>
                    <li><?= __('Try a different browser if problem persists') ?></li>
                </ul>

                <h5 class="mt-3"><?= __('Document Not Appearing') ?></h5>
                <ul>
                    <li><?= __('Refresh the page') ?></li>
                    <li><?= __('Check if upload completed successfully') ?></li>
                    <li><?= __('Verify you selected correct candidate') ?></li>
                    <li><?= __('Contact system administrator if issue continues') ?></li>
                </ul>

                <h5 class="mt-3"><?= __('Cannot See Candidate') ?></h5>
                <ul>
                    <li><?= __('Ensure candidate is registered under YOUR institution') ?></li>
                    <li><?= __('Remember: You can only see your institution\'s candidates') ?></li>
                    <li><?= __('Check if candidate registration was completed') ?></li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="alert alert-success">
                <h5><i class="fa fa-link"></i> <?= __('Quick Links') ?></h5>
                <p class="mb-2">
                    <?= $this->Html->link('<i class="fa fa-chart-bar"></i> Document Dashboard', ['controller' => 'CandidateDocumentManagementDashboardDetails', 'action' => 'index'], ['class' => 'btn btn-sm btn-primary mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-file-upload"></i> Submit Documents', ['controller' => 'CandidateDocuments', 'action' => 'index'], ['class' => 'btn btn-sm btn-success mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-users"></i> My Candidates', ['controller' => 'Candidates', 'action' => 'index'], ['class' => 'btn btn-sm btn-info mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-question-circle"></i> Candidate Help', ['controller' => 'Candidates', 'action' => 'help'], ['class' => 'btn btn-sm btn-warning', 'escape' => false]) ?>
                </p>
            </div>

        </div>
    </div>
</div>
