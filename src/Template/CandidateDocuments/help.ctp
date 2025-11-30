<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="candidate-documents help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-file-upload"></i> Document Submission - Help Guide
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> Document Submission System</h5>
                <p>This guide explains how to properly submit and manage candidate documents in the <strong>cms_lpk_candidate_documents</strong> database.</p>
            </div>

            <!-- Document Requirements -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-clipboard-list"></i> Required Documents Checklist
                </h4>
                
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="30%">Document Type</th>
                            <th width="40%">Requirements</th>
                            <th width="25%">File Format</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><strong>Identity Card (KTP)</strong></td>
                            <td>Clear scan/photo of both sides, valid and not expired</td>
                            <td>PDF, JPG, PNG<br>Max 2MB</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><strong>Family Card (KK)</strong></td>
                            <td>Complete family card showing candidate's name</td>
                            <td>PDF, JPG, PNG<br>Max 2MB</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><strong>Birth Certificate</strong></td>
                            <td>Official birth certificate or extract</td>
                            <td>PDF, JPG, PNG<br>Max 2MB</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td><strong>Education Certificate</strong></td>
                            <td>Highest education diploma (SMA/SMK/D3/S1)</td>
                            <td>PDF, JPG, PNG<br>Max 3MB</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td><strong>Academic Transcript</strong></td>
                            <td>Complete transcript of grades</td>
                            <td>PDF, JPG, PNG<br>Max 3MB</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td><strong>Passport Photo</strong></td>
                            <td>Recent photo, 3x4 cm, white background</td>
                            <td>JPG, PNG<br>Max 1MB</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td><strong>Health Certificate</strong></td>
                            <td>Medical check-up results (not older than 3 months)</td>
                            <td>PDF, JPG, PNG<br>Max 2MB</td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td><strong>Police Clearance (SKCK)</strong></td>
                            <td>Valid SKCK (not older than 6 months)</td>
                            <td>PDF, JPG, PNG<br>Max 2MB</td>
                        </tr>
                        <tr>
                            <td>9</td>
                            <td><strong>Passport (if available)</strong></td>
                            <td>Valid passport with at least 18 months validity</td>
                            <td>PDF, JPG, PNG<br>Max 2MB</td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td><strong>Training Certificates</strong></td>
                            <td>Any relevant vocational training certificates</td>
                            <td>PDF, JPG, PNG<br>Max 2MB each</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Upload Process -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-cloud-upload-alt"></i> Document Upload Process
                </h4>
                
                <h5 class="mt-4">Step-by-Step Upload Guide</h5>
                <ol>
                    <li><strong>Access Document Submission</strong>
                        <ul>
                            <li>Go to <strong>LPK Menu → Document Submission</strong></li>
                            <li>Select the candidate from your institution's list</li>
                        </ul>
                    </li>
                    <li><strong>Choose Document Category</strong>
                        <ul>
                            <li>Select the document type from dropdown</li>
                            <li>Read the specific requirements for that document</li>
                        </ul>
                    </li>
                    <li><strong>Prepare Your File</strong>
                        <ul>
                            <li>Ensure file meets size and format requirements</li>
                            <li>Rename file descriptively (e.g., "KTP_JohnDoe.pdf")</li>
                            <li>Verify document is clear and legible</li>
                        </ul>
                    </li>
                    <li><strong>Upload Document</strong>
                        <ul>
                            <li>Click <span class="badge badge-primary">Choose File</span></li>
                            <li>Select your prepared document</li>
                            <li>Add notes or comments (optional but recommended)</li>
                            <li>Click <span class="badge badge-success">Upload</span></li>
                        </ul>
                    </li>
                    <li><strong>Verify Upload</strong>
                        <ul>
                            <li>Check for success message</li>
                            <li>Verify document appears in submission list</li>
                            <li>Note the submission date and status</li>
                        </ul>
                    </li>
                </ol>

                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Important Notes</strong>
                    <ul class="mb-0">
                        <li>Do not upload password-protected files</li>
                        <li>Ensure documents are not corrupted</li>
                        <li>Use clear scans, avoid blurry photos</li>
                        <li>Submit documents in the correct category</li>
                    </ul>
                </div>
            </div>

            <!-- Document Status -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-tasks"></i> Document Status Tracking
                </h4>
                
                <h5 class="mt-4">Understanding Document Status</h5>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Status</th>
                            <th>Meaning</th>
                            <th>Action Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-secondary">Not Submitted</span></td>
                            <td>Document has not been uploaded yet</td>
                            <td>Upload the required document</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-warning">Pending Review</span></td>
                            <td>Document uploaded, awaiting verification</td>
                            <td>Wait for admin review</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-info">Under Review</span></td>
                            <td>Document is being verified by admin</td>
                            <td>No action needed</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-success">Approved</span></td>
                            <td>Document verified and accepted</td>
                            <td>No action needed</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-danger">Rejected</span></td>
                            <td>Document does not meet requirements</td>
                            <td>Check rejection reason and re-upload</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-primary">Resubmitted</span></td>
                            <td>Document re-uploaded after rejection</td>
                            <td>Wait for re-review</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-4">Checking Document Status</h5>
                <ol>
                    <li>Go to <strong>LPK Menu → Document Dashboard</strong></li>
                    <li>View overall completion percentage</li>
                    <li>See detailed status for each document type</li>
                    <li>Click on candidate name to see individual document status</li>
                </ol>
            </div>

            <!-- Handling Rejections -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-redo"></i> Handling Document Rejections
                </h4>
                
                <h5 class="mt-4">Common Rejection Reasons</h5>
                <ul>
                    <li><i class="fa fa-times text-danger"></i> <strong>Poor Quality:</strong> Document is blurry, dark, or illegible</li>
                    <li><i class="fa fa-times text-danger"></i> <strong>Incomplete:</strong> Missing pages or information</li>
                    <li><i class="fa fa-times text-danger"></i> <strong>Expired:</strong> Document validity has expired</li>
                    <li><i class="fa fa-times text-danger"></i> <strong>Wrong Format:</strong> File type not supported</li>
                    <li><i class="fa fa-times text-danger"></i> <strong>Wrong Category:</strong> Document uploaded in incorrect category</li>
                    <li><i class="fa fa-times text-danger"></i> <strong>Mismatch:</strong> Information doesn't match candidate data</li>
                </ul>

                <h5 class="mt-4">How to Resubmit Rejected Documents</h5>
                <ol>
                    <li>Check the rejection reason in the document status</li>
                    <li>Prepare a corrected version of the document</li>
                    <li>Go to <strong>Document Submission</strong></li>
                    <li>Find the rejected document</li>
                    <li>Click <span class="badge badge-warning">Resubmit</span></li>
                    <li>Upload the corrected document</li>
                    <li>Add notes explaining the corrections made</li>
                    <li>Submit for re-review</li>
                </ol>
            </div>

            <!-- Dashboard Features -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-chart-bar"></i> Document Dashboard Features
                </h4>
                
                <h5 class="mt-4">Dashboard Overview</h5>
                <p>The Document Dashboard (<strong>LPK Menu → Document Dashboard</strong>) provides:</p>
                <ul>
                    <li><strong>Summary Statistics:</strong>
                        <ul>
                            <li>Total candidates registered</li>
                            <li>Total documents submitted</li>
                            <li>Documents pending review</li>
                            <li>Documents approved</li>
                            <li>Documents rejected</li>
                        </ul>
                    </li>
                    <li><strong>Completion Tracking:</strong>
                        <ul>
                            <li>Overall completion percentage</li>
                            <li>Per-candidate completion status</li>
                            <li>Missing documents list</li>
                        </ul>
                    </li>
                    <li><strong>Visual Reports:</strong>
                        <ul>
                            <li>Charts showing submission progress</li>
                            <li>Status distribution graphs</li>
                            <li>Timeline of submissions</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-lightbulb-o"></i> Best Practices
                </h4>
                <ul>
                    <li><strong>Scan Quality:</strong> Use 300 DPI or higher for scans</li>
                    <li><strong>File Naming:</strong> Use descriptive names (e.g., "KTP_NamaCandidate_2024.pdf")</li>
                    <li><strong>Batch Upload:</strong> Prepare all documents before starting upload session</li>
                    <li><strong>Regular Checks:</strong> Monitor dashboard daily for status updates</li>
                    <li><strong>Prompt Resubmission:</strong> Address rejections within 24 hours</li>
                    <li><strong>Backup Copies:</strong> Keep original documents and digital backups</li>
                    <li><strong>Verify Before Upload:</strong> Double-check document quality and correctness</li>
                    <li><strong>Complete Sets:</strong> Submit all required documents for each candidate</li>
                </ul>
            </div>

            <!-- Troubleshooting -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-wrench"></i> Troubleshooting
                </h4>
                
                <h5 class="mt-3">Upload Fails</h5>
                <ul>
                    <li>Check file size (must be under maximum limit)</li>
                    <li>Verify file format is supported (PDF, JPG, PNG)</li>
                    <li>Ensure stable internet connection</li>
                    <li>Try a different browser if problem persists</li>
                </ul>

                <h5 class="mt-3">Document Not Appearing</h5>
                <ul>
                    <li>Refresh the page</li>
                    <li>Check if upload completed successfully</li>
                    <li>Verify you selected correct candidate</li>
                    <li>Contact system administrator if issue continues</li>
                </ul>

                <h5 class="mt-3">Cannot See Candidate</h5>
                <ul>
                    <li>Ensure candidate is registered under YOUR institution</li>
                    <li>Remember: You can only see your institution's candidates</li>
                    <li>Check if candidate registration was completed</li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="alert alert-success">
                <h5><i class="fa fa-link"></i> Quick Links</h5>
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
