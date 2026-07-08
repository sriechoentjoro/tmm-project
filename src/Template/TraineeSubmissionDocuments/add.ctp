<?php
/**
 * Add Trainee Submission Document - rich upload form
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeSubmissionDocument $traineeSubmissionDocument
 * @var array $trainees              [id => "Name (TMM-code)"]
 * @var array $documents             [categoryName => [id => title]]
 * @var array $documentMeta          [id => meta] for the info panel
 * @var array $statuses              [id => name]
 * @var array $submissionsByTrainee  [traineeId => [documentId, ...]]
 * @var int   $requiredTotal
 */
$this->assign('title', 'Add Trainee Submission Document');
?>

<div class="add-submission-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-cloud-upload"></i> <?= __('Add Trainee Submission Document') ?></h2>
            <p class="text-muted"><?= __('Upload a pre-departure document for a trainee going to Japan') ?> 🇯🇵</p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-check-square-o"></i> ' . __('Checklist'),
                ['controller' => 'TraineeSubmissionDocuments', 'action' => 'checklist'],
                ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
            <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('Back to List'),
                ['controller' => 'TraineeSubmissionDocuments', 'action' => 'index'],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="row">
        <!-- Form -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-file-text-o"></i> <?= __('Document Details') ?></h4></div>
                <div class="card-body">
                    <?= $this->Form->create($traineeSubmissionDocument, ['type' => 'file']) ?>

                    <div class="form-step">
                        <span class="step-number">1</span>
                        <div class="step-body">
                            <?= $this->Form->control('trainee_id', [
                                'label' => __('Trainee'),
                                'options' => $trainees,
                                'empty' => '— ' . __('Select trainee') . ' —',
                                'required' => true,
                                'id' => 'trainee-select',
                            ]) ?>
                        </div>
                    </div>

                    <div class="form-step">
                        <span class="step-number">2</span>
                        <div class="step-body">
                            <?= $this->Form->control('apprenticeship_submission_document_id', [
                                'label' => __('Document Type') . ' (* = ' . __('required for departure') . ')',
                                'options' => $documents,
                                'empty' => '— ' . __('Select document type') . ' —',
                                'required' => true,
                                'id' => 'document-select',
                            ]) ?>
                            <div id="duplicate-warning" class="inline-alert" style="display:none;">
                                <i class="fa fa-exclamation-triangle"></i>
                                <?= __('This trainee already submitted this document. Saving will replace the existing file record.') ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-step">
                        <span class="step-number">3</span>
                        <div class="step-body">
                            <label><?= __('File') ?></label>
                            <div class="drop-zone-wrap">
                                <div id="drop-zone" class="drop-zone">
                                    <i class="fa fa-cloud-upload fa-2x"></i>
                                    <p id="drop-zone-text"><?= __('Click to choose a file or drag & drop it here') ?></p>
                                    <small class="text-muted">PDF, JPG, PNG, DOC(X), XLS(X), ZIP</small>
                                </div>
                                <?php // The input stays focusable (not display:none) so native "required"
                                      // validation can focus it and show its message over the drop zone. ?>
                                <?= $this->Form->file('document_file', [
                                    'id' => 'document-file',
                                    'class' => 'file-input-overlay',
                                    'accept' => '.pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.zip',
                                    'required' => true,
                                    'tabindex' => '-1',
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-step">
                        <span class="step-number">4</span>
                        <div class="step-body">
                            <?= $this->Form->control('master_document_submission_status_id', [
                                'label' => __('Submission Status'),
                                'options' => $statuses,
                                'default' => 1,
                            ]) ?>
                            <div class="status-legend">
                                <span class="legend-item">
                                    <span class="badge badge-success"><?= __('Submitted') ?></span>
                                    <?= __('document received and verified — counts toward departure readiness') ?>
                                </span>
                                <span class="legend-item">
                                    <span class="badge badge-warning"><?= __('Pending') ?></span>
                                    <?= __('uploaded but still awaiting review — not yet counted as complete') ?>
                                </span>
                            </div>
                            <?= $this->Form->control('notes', [
                                'label' => __('Notes') . ' (' . __('optional') . ')',
                                'type' => 'textarea',
                                'rows' => 3,
                            ]) ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <?= $this->Form->button('<i class="fa fa-save"></i> ' . __('Save Document'),
                            ['escapeTitle' => false, 'class' => 'btn btn-success btn-lg']) ?>
                        <?= $this->Html->link(__('Cancel'), ['controller' => 'TraineeSubmissionDocuments', 'action' => 'index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <!-- Info sidebar -->
        <div class="col-md-5">
            <div class="card" id="document-info-card" style="display:none;">
                <div class="card-header"><h4><i class="fa fa-info-circle"></i> <?= __('Selected Document') ?></h4></div>
                <div class="card-body">
                    <h5 id="info-title"></h5>
                    <p id="info-title-jp" class="text-muted"></p>
                    <table class="info-table">
                        <tr><th><?= __('Category') ?></th><td id="info-category"></td></tr>
                        <tr><th><?= __('Format') ?></th><td id="info-format"></td></tr>
                        <tr><th><?= __('Required') ?></th><td id="info-required"></td></tr>
                        <tr><th><?= __('Translation') ?></th><td id="info-translation"></td></tr>
                    </table>
                </div>
            </div>

            <div class="card" id="trainee-progress-card" style="display:none;">
                <div class="card-header"><h4><i class="fa fa-tasks"></i> <?= __('Trainee Progress') ?></h4></div>
                <div class="card-body">
                    <div class="progress-header">
                        <span id="progress-label"></span>
                        <span id="progress-count" class="progress-count"></span>
                    </div>
                    <div class="progress-track">
                        <div id="progress-fill" class="progress-fill"></div>
                    </div>
                    <p class="text-muted" style="margin-top:10px; font-size:0.85em;">
                        <i class="fa fa-lightbulb-o"></i>
                        <?= __('See every remaining document for this trainee in the') ?>
                        <a href="#" id="checklist-link"><?= __('checklist') ?></a>.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4><i class="fa fa-shield"></i> <?= __('Upload Rules') ?></h4></div>
                <div class="card-body">
                    <ul class="rules-list">
                        <li><i class="fa fa-check text-success"></i> <?= __('Allowed types: PDF, JPG, PNG, DOC(X), XLS(X), ZIP') ?></li>
                        <li><i class="fa fa-check text-success"></i> <?= __('Files are stored under') ?> <code>files/uploads/trainee_documents/</code></li>
                        <li><i class="fa fa-check text-success"></i> <?= __('Uploader and upload time are recorded automatically') ?></li>
                        <li><i class="fa fa-refresh text-warning"></i> <?= __('Re-uploading a document replaces the previous record') ?></li>
                        <li><i class="fa fa-flag text-info"></i> <?= __('Status "Submitted" counts toward departure readiness') ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var documentMeta = <?= json_encode($documentMeta) ?>;
    var submissionsByTrainee = <?= json_encode($submissionsByTrainee ?: new \stdClass()) ?>;
    var requiredTotal = <?= (int)$requiredTotal ?>;
    var checklistUrlBase = <?= json_encode($this->Url->build(['controller' => 'TraineeSubmissionDocuments', 'action' => 'checklist'])) ?>;

    var traineeSelect = document.getElementById('trainee-select');
    var documentSelect = document.getElementById('document-select');
    var duplicateWarning = document.getElementById('duplicate-warning');

    function badge(text, cls) {
        return '<span class="mini-badge mini-badge-' + cls + '">' + text + '</span>';
    }

    function updateDocumentInfo() {
        var card = document.getElementById('document-info-card');
        var meta = documentMeta[documentSelect.value];
        if (!meta) { card.style.display = 'none'; return; }
        card.style.display = '';
        document.getElementById('info-title').textContent = meta.title;
        document.getElementById('info-title-jp').textContent = meta.title_jp || '';
        document.getElementById('info-category').textContent = meta.category || '-';
        document.getElementById('info-format').textContent = meta.format || '-';
        document.getElementById('info-required').innerHTML = meta.is_required
            ? badge('<?= __('Required') ?>', 'danger') : badge('<?= __('Optional') ?>', 'secondary');
        document.getElementById('info-translation').innerHTML = meta.is_translation_required
            ? badge('<?= __('Translation required') ?>', 'warning') : badge('<?= __('Not required') ?>', 'secondary');
    }

    function updateTraineeProgress() {
        var card = document.getElementById('trainee-progress-card');
        var traineeId = traineeSelect.value;
        if (!traineeId) { card.style.display = 'none'; return; }
        card.style.display = '';
        var submitted = (submissionsByTrainee[traineeId] || []).length;
        var percent = requiredTotal > 0 ? Math.min(100, Math.round(submitted / requiredTotal * 100)) : 0;
        document.getElementById('progress-label').textContent =
            traineeSelect.options[traineeSelect.selectedIndex].text;
        document.getElementById('progress-count').textContent = submitted + ' / ' + requiredTotal;
        var fill = document.getElementById('progress-fill');
        fill.style.width = percent + '%';
        fill.style.background = percent >= 100 ? '#43e97b' : (percent >= 50 ? '#4facfe' : (percent > 0 ? '#f5a623' : '#ced4da'));
        document.getElementById('checklist-link').href = checklistUrlBase + '?trainee_id=' + traineeId;
    }

    function updateDuplicateWarning() {
        var traineeId = traineeSelect.value;
        var docId = parseInt(documentSelect.value, 10);
        var submitted = submissionsByTrainee[traineeId] || [];
        duplicateWarning.style.display = (traineeId && docId && submitted.indexOf(docId) !== -1) ? '' : 'none';
    }

    traineeSelect.addEventListener('change', function () { updateTraineeProgress(); updateDuplicateWarning(); });
    documentSelect.addEventListener('change', function () { updateDocumentInfo(); updateDuplicateWarning(); });
    updateDocumentInfo(); updateTraineeProgress(); updateDuplicateWarning();

    // Drop zone
    var dropZone = document.getElementById('drop-zone');
    var fileInput = document.getElementById('document-file');
    var dropText = document.getElementById('drop-zone-text');

    function showFile() {
        if (fileInput.files.length) {
            var f = fileInput.files[0];
            dropText.innerHTML = '<strong>' + f.name + '</strong> (' + (f.size / 1024 / 1024).toFixed(2) + ' MB)';
            dropZone.classList.add('has-file');
        } else {
            dropText.textContent = '<?= __('Click to choose a file or drag & drop it here') ?>';
            dropZone.classList.remove('has-file');
        }
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', showFile);
    ['dragover', 'dragenter'].forEach(function (evt) {
        dropZone.addEventListener(evt, function (e) { e.preventDefault(); dropZone.classList.add('drag-over'); });
    });
    ['dragleave', 'drop'].forEach(function (evt) {
        dropZone.addEventListener(evt, function (e) { e.preventDefault(); dropZone.classList.remove('drag-over'); });
    });
    dropZone.addEventListener('drop', function (e) {
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showFile();
        }
    });
})();
</script>

<style>
.add-submission-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.add-submission-page .page-header h2 { margin: 0 0 5px 0; }
.add-submission-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.add-submission-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.add-submission-page .card-header h4 { margin: 0; }
.add-submission-page .card-body { padding: 20px; }
.form-step {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}
.step-number {
    flex: 0 0 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}
.step-body { flex: 1; min-width: 0; }
.step-body label { font-weight: bold; }
.drop-zone-wrap { position: relative; }
.file-input-overlay {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    pointer-events: none;
    border: none;
    margin: 0;
    padding: 0;
}
.drop-zone {
    border: 2px dashed #adb5bd;
    border-radius: 10px;
    padding: 25px 15px;
    text-align: center;
    color: #6c757d;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
}
.drop-zone:hover, .drop-zone.drag-over {
    border-color: #667eea;
    background: #f4f5ff;
    color: #667eea;
}
.drop-zone.has-file {
    border-color: #28a745;
    border-style: solid;
    background: #f2fbf5;
    color: #28a745;
}
.drop-zone p { margin: 8px 0 3px 0; }
.inline-alert {
    margin-top: 8px;
    padding: 8px 12px;
    border-radius: 6px;
    background: #fff8e1;
    border: 1px solid #ffc107;
    color: #856404;
    font-size: 0.9em;
}
.status-legend {
    display: flex;
    flex-direction: column;
    gap: 6px;
    background: #fafbff;
    border: 1px solid #e4e8fb;
    border-radius: 8px;
    padding: 10px 14px;
    margin: -6px 0 15px 0;
    font-size: 0.88em;
    color: #495057;
}
.status-legend .legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
.status-legend .badge {
    padding: 3px 9px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
    white-space: nowrap;
    flex: 0 0 auto;
}
.status-legend .badge-success { background: #28a745; color: white; }
.status-legend .badge-warning { background: #ffc107; color: #333; }
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}
.info-table { width: 100%; }
.info-table th {
    text-align: left;
    color: #6c757d;
    font-weight: normal;
    padding: 6px 10px 6px 0;
    width: 40%;
}
.info-table td { padding: 6px 0; }
.mini-badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    text-transform: uppercase;
    font-weight: bold;
}
.mini-badge-danger { background: #dc3545; color: white; }
.mini-badge-warning { background: #ffc107; color: #333; }
.mini-badge-secondary { background: #e9ecef; color: #495057; }
.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    font-weight: bold;
}
.progress-count { color: #6c757d; font-weight: normal; }
.progress-track {
    height: 10px;
    background: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 5px;
    transition: width 0.5s ease;
    width: 0;
}
.rules-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.rules-list li { padding: 5px 0; }
.rules-list i { width: 20px; }
.text-success { color: #28a745; }
.text-warning { color: #f5a623; }
.text-info { color: #17a2b8; }
</style>
