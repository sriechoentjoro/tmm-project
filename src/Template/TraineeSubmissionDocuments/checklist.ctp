<?php
/**
 * @var \App\View\AppView $this
 * @var array $trainees
 * @var int $traineeId
 * @var \Cake\Datasource\ResultSetInterface $masterDocuments
 * @var array $categories keyed by category id
 * @var array $submissions keyed by master document id
 */

$total = 0;
$uploaded = 0;
$requiredTotal = 0;
$requiredUploaded = 0;
$byCategory = [];
foreach ($masterDocuments as $doc) {
    $categoryName = $categories[$doc->master_trainee_submission_document_category_id] ?? __('Uncategorized');
    $byCategory[$categoryName][] = $doc;
    $isUploaded = !empty($submissions[$doc->id]);
    $total++;
    if ($isUploaded) { $uploaded++; }
    if ($doc->is_required) {
        $requiredTotal++;
        if ($isUploaded) { $requiredUploaded++; }
    }
}
$pct = $total ? (int)round($uploaded * 100 / $total) : 0;
?>
<style>
.doc-checklist-table { width: 100%; border-collapse: collapse; }
.doc-checklist-table th {
    text-align: left; font-size: 12.5px; color: #8a99a8; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    padding: 10px 14px; border-bottom: 2px solid #eef1f4;
}
.doc-checklist-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f7; vertical-align: middle; }
.doc-checklist-table tr:last-child td { border-bottom: none; }
.doc-checklist-table tr.row-uploaded { background: #f4fdf7; }
.doc-status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12.5px; font-weight: 700; border-radius: 20px; padding: 5px 14px; white-space: nowrap;
}
.doc-status-badge.uploaded { background: #d7f5e2; color: #1e7e46; }
.doc-status-badge.missing { background: #fdeaea; color: #c0392b; }
.badge-required { background: #fff3cd; color: #8a6d1a; font-size: 11px; font-weight: 700; border-radius: 4px; padding: 3px 8px; }
.badge-optional { background: #eef1f4; color: #6c7a87; font-size: 11px; font-weight: 700; border-radius: 4px; padding: 3px 8px; }
.badge-translation { background: #e3ecfd; color: #2c5dbb; font-size: 11px; font-weight: 700; border-radius: 4px; padding: 3px 8px; white-space: nowrap; }
.doc-progress-wrap { background: #e9f0f3; border-radius: 20px; height: 14px; overflow: hidden; }
.doc-progress-bar {
    height: 100%; border-radius: 20px;
    background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
    transition: width .4s ease;
}
.doc-title-jp { display: block; font-size: 12px; color: #8a99a8; }
.doc-upload-form { display: flex; gap: 8px; align-items: center; }
.doc-upload-form input[type="file"] { font-size: 12px; max-width: 190px; padding: 4px; }
.btn-doc-upload {
    background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
    color: #fff; border: none; border-radius: 6px; padding: 7px 14px;
    font-size: 12.5px; font-weight: 700; cursor: pointer; white-space: nowrap;
}
.btn-doc-upload:hover { box-shadow: 0 4px 10px rgba(0, 151, 167, 0.4); }
.btn-doc-remove {
    background: #fff; color: #c0392b; border: 1px solid #f0b9b3; border-radius: 6px;
    padding: 6px 10px; font-size: 12px; cursor: pointer;
}
.btn-doc-remove:hover { background: #fdeaea; }
.doc-file-link { font-size: 12.5px; white-space: nowrap; }
.doc-date { font-size: 12px; color: #8a99a8; white-space: nowrap; }
</style>

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Trainee Submissions'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-chart-line"></i> ' . __('Progress Tracking'), ['action' => 'progress'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-users"></i> ' . __('List Trainees'), ['controller' => 'Trainees', 'action' => 'index'], ['escape' => false]) ?></li>
    </ul>
</nav>

<div class="traineeSubmissionDocuments form content cand-form">

    <!-- Page Header -->
    <div class="cand-hero">
        <h3>
            <i class="fas fa-tasks"></i>
            <?= __('Trainee Document Preparation Checklist') ?>
        </h3>
        <p><i class="fas fa-info-circle"></i> <?= __('Select a trainee to see at a glance which preparation documents have been uploaded. Upload a file on each row to mark it as prepared.') ?></p>
    </div>

    <!-- Trainee selector + progress -->
    <div class="cand-section">
        <div class="cand-section-header">
            <div class="icon-chip"><i class="fas fa-user-check"></i></div>
            <div>
                <h5><?= __('Trainee') ?></h5>
                <small><?= __('Checklist status is stored per trainee') ?></small>
            </div>
        </div>
        <div class="cand-section-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?= __('Select Trainee') ?></label>
                    <select id="checklist-trainee" class="form-control">
                        <option value=""><?= __('-- Select Trainee --') ?></option>
                        <?php foreach ($trainees as $tid => $tname): ?>
                            <option value="<?= (int)$tid ?>" <?= $tid == $traineeId ? 'selected' : '' ?>><?= h($tname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($traineeId): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?= __('Progress') ?></label>
                    <div class="doc-progress-wrap">
                        <div class="doc-progress-bar" style="width: <?= $pct ?>%;"></div>
                    </div>
                    <small class="form-text text-muted">
                        <?= $uploaded ?> / <?= $total ?> <?= __('documents uploaded') ?>
                        &nbsp;&bull;&nbsp;
                        <?= $requiredUploaded ?> / <?= $requiredTotal ?> <?= __('required documents') ?>
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!$traineeId): ?>
        <div class="cand-section">
            <div class="cand-section-body text-center text-muted" style="padding: 40px;">
                <i class="fas fa-arrow-up" style="font-size: 28px; color: #00BCD4;"></i>
                <p class="mt-3 mb-0"><?= __('Please select a trainee above to view and update the document checklist.') ?></p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($byCategory as $categoryName => $docs): ?>
        <div class="cand-section">
            <div class="cand-section-header">
                <div class="icon-chip"><i class="fas fa-folder-open"></i></div>
                <div>
                    <h5><?= h($categoryName) ?></h5>
                    <small><?= count($docs) ?> <?= __('document(s)') ?></small>
                </div>
            </div>
            <div class="cand-section-body" style="padding: 8px 24px 16px;">
                <table class="doc-checklist-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;"><?= __('Document') ?></th>
                            <th style="width: 13%;"><?= __('Type') ?></th>
                            <th style="width: 12%;"><?= __('Status') ?></th>
                            <th style="width: 17%;"><?= __('File / Date') ?></th>
                            <th><?= __('Upload') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($docs as $doc):
                        $sub = $submissions[$doc->id] ?? null;
                        $isUploaded = (bool)$sub;
                        $uploadedAt = ($sub && $sub->uploaded_at)
                            ? (is_string($sub->uploaded_at) ? $sub->uploaded_at : $sub->uploaded_at->format('Y-m-d H:i'))
                            : '';
                    ?>
                        <tr class="<?= $isUploaded ? 'row-uploaded' : '' ?>">
                            <td>
                                <strong><?= h($doc->title) ?></strong>
                                <?php if (!empty($doc->title_jp)): ?>
                                    <span class="doc-title-jp"><?= h($doc->title_jp) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($doc->format)): ?>
                                    <span class="doc-title-jp"><i class="fas fa-file-alt"></i> <?= h($doc->format) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($doc->is_required): ?>
                                    <span class="badge-required"><?= __('Required') ?></span>
                                <?php else: ?>
                                    <span class="badge-optional"><?= __('Optional') ?></span>
                                <?php endif; ?>
                                <?php if ($doc->is_translation_required): ?>
                                    <span class="badge-translation"><i class="fas fa-language"></i> <?= __('Translation') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="doc-status-badge <?= $isUploaded ? 'uploaded' : 'missing' ?>">
                                    <i class="fas <?= $isUploaded ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                    <?= $isUploaded ? __('Uploaded') : __('Not Uploaded') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($isUploaded): ?>
                                    <a class="doc-file-link" href="<?= $this->Url->build('/' . $sub->file_path) ?>" target="_blank">
                                        <i class="fas fa-paperclip"></i> <?= __('View file') ?>
                                    </a>
                                    <div class="doc-date"><?= h($uploadedAt) ?></div>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 12.5px;">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="doc-upload-form">
                                    <?= $this->Form->create(null, ['type' => 'file', 'url' => ['action' => 'checklist'], 'style' => 'display:flex;gap:8px;align-items:center;margin:0;']) ?>
                                    <?= $this->Form->hidden('trainee_id', ['value' => $traineeId]) ?>
                                    <?= $this->Form->hidden('document_id', ['value' => $doc->id]) ?>
                                    <input type="file" name="document_file" required>
                                    <button type="submit" class="btn-doc-upload">
                                        <i class="fas fa-upload"></i> <?= $isUploaded ? __('Replace') : __('Upload') ?>
                                    </button>
                                    <?= $this->Form->end() ?>
                                    <?php if ($isUploaded): ?>
                                        <?= $this->Form->create(null, ['url' => ['action' => 'removeSubmission'], 'style' => 'margin:0;', 'onsubmit' => "return confirm('" . __('Remove this document record?') . "');"]) ?>
                                        <?= $this->Form->hidden('trainee_id', ['value' => $traineeId]) ?>
                                        <?= $this->Form->hidden('document_id', ['value' => $doc->id]) ?>
                                        <button type="submit" class="btn-doc-remove" title="<?= __('Remove record') ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <?= $this->Form->end() ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php $this->append('script'); ?>
<script>
$(function () {
    $('#checklist-trainee').on('change', function () {
        var id = $(this).val();
        var url = '<?= $this->Url->build(['controller' => 'TraineeSubmissionDocuments', 'action' => 'checklist']) ?>';
        window.location = id ? url + '?trainee_id=' + id : url;
    });
});
</script>
<?php $this->end(); ?>
