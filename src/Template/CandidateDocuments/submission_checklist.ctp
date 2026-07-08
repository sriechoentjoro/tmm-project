<?php
/**
 * @var \App\View\AppView $this
 * @var array $candidates
 * @var int $candidateId
 * @var \Cake\Datasource\ResultSetInterface $masterDocuments
 * @var array $submissions keyed by document_id
 */

$total = 0;
$uploaded = 0;
$requiredTotal = 0;
$requiredUploaded = 0;
$byCategory = [];
foreach ($masterDocuments as $doc) {
    $categoryTitle = $doc->candidate_document_category->title ?? __('Uncategorized');
    $byCategory[$categoryTitle][] = $doc;
    // Count as uploaded if there's an actual file OR the submitted flag is set
    $hasFile = !empty($uploadedDocs[$doc->id]);
    $isUploaded = $hasFile || (!empty($submissions[$doc->id]) && $submissions[$doc->id]->submitted);
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
    font-size: 12.5px; font-weight: 700; border-radius: 20px; padding: 5px 14px;
}
.doc-status-badge.uploaded { background: #d7f5e2; color: #1e7e46; }
.doc-status-badge.missing { background: #fdeaea; color: #c0392b; }
.badge-required { background: #fff3cd; color: #8a6d1a; font-size: 11px; font-weight: 700; border-radius: 4px; padding: 3px 8px; }
.badge-optional { background: #eef1f4; color: #6c7a87; font-size: 11px; font-weight: 700; border-radius: 4px; padding: 3px 8px; }
.doc-progress-wrap { background: #e9f0f3; border-radius: 20px; height: 14px; overflow: hidden; }
.doc-progress-bar {
    height: 100%; border-radius: 20px;
    background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
    transition: width .4s ease;
}
.doc-date { font-size: 12px; color: #8a99a8; white-space: nowrap; }
.btn-doc-action {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;
    border: none; cursor: pointer; text-decoration: none; white-space: nowrap;
    transition: opacity 0.18s;
}
.btn-doc-action:hover { opacity: 0.82; text-decoration: none; }
.btn-doc-upload { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
.btn-doc-view   { background: linear-gradient(135deg, #00BCD4, #0097A7); color: #fff; }

/* File view overlay modal */
#docViewOverlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.65); align-items: center; justify-content: center;
}
#docViewOverlay.open { display: flex; }
#docViewDialog {
    background: #fff; border-radius: 10px; width: 92vw; max-width: 1100px;
    height: 90vh; display: flex; flex-direction: column; overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,.35);
}
#docViewHeader {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 18px;
    background: linear-gradient(135deg, #667eea, #764ba2); color: #fff;
    flex-shrink: 0;
}
#docViewHeader strong { font-size: 14px; }
#docViewClose {
    background: rgba(255,255,255,.25); border: none; color: #fff;
    border-radius: 50%; width: 28px; height: 28px; font-size: 18px; line-height: 1;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
}
#docViewClose:hover { background: rgba(255,255,255,.4); }
#docViewBody { flex: 1; overflow: auto; background: #f8f9fa; }
</style>

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Candidate Documents'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('Document Master List'), ['controller' => 'CandidateDocumentsMasterList', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-users"></i> ' . __('List Candidates'), ['controller' => 'Candidates', 'action' => 'index'], ['escape' => false]) ?></li>
    </ul>
</nav>

<div class="candidateDocuments form content cand-form">

    <!-- Page Header -->
    <div class="cand-hero">
        <h3>
            <i class="fas fa-tasks"></i>
            <?= __('Candidate Document Submission Checklist') ?>
        </h3>
        <p><i class="fas fa-info-circle"></i> <?= __('Select a candidate to see at a glance which documents have been uploaded. Use the switches to mark documents as submitted.') ?></p>
    </div>

    <!-- Candidate selector + progress -->
    <div class="cand-section">
        <div class="cand-section-header">
            <div class="icon-chip"><i class="fas fa-user-check"></i></div>
            <div>
                <h5><?= __('Candidate') ?></h5>
                <small><?= __('Checklist status is stored per candidate') ?></small>
            </div>
        </div>
        <div class="cand-section-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?= __('Select Candidate') ?></label>
                    <select id="checklist-candidate" class="form-control">
                        <option value=""><?= __('-- Select Candidate --') ?></option>
                        <?php foreach ($candidates as $cid => $cname): ?>
                            <option value="<?= (int)$cid ?>" <?= $cid == $candidateId ? 'selected' : '' ?>><?= h($cname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($candidateId): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?= __('Progress') ?></label>
                    <div class="doc-progress-wrap">
                        <div class="doc-progress-bar" id="doc-progress-bar" style="width: <?= $pct ?>%;"></div>
                    </div>
                    <small class="form-text text-muted">
                        <span id="doc-progress-text"><?= $uploaded ?> / <?= $total ?></span> <?= __('documents uploaded') ?>
                        &nbsp;&bull;&nbsp;
                        <span id="doc-progress-required"><?= $requiredUploaded ?> / <?= $requiredTotal ?></span> <?= __('required documents') ?>
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!$candidateId): ?>
        <div class="cand-section">
            <div class="cand-section-body text-center text-muted" style="padding: 40px;">
                <i class="fas fa-arrow-up" style="font-size: 28px; color: #00BCD4;"></i>
                <p class="mt-3 mb-0"><?= __('Please select a candidate above to view and update the document checklist.') ?></p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($byCategory as $categoryTitle => $docs): ?>
        <div class="cand-section">
            <div class="cand-section-header">
                <div class="icon-chip"><i class="fas fa-folder-open"></i></div>
                <div>
                    <h5><?= h($categoryTitle) ?></h5>
                    <small><?= count($docs) ?> <?= __('document(s)') ?></small>
                </div>
            </div>
            <div class="cand-section-body" style="padding: 8px 24px 16px;">
                <table class="doc-checklist-table">
                    <thead>
                        <tr>
                            <th style="width: 36%;"><?= __('Document') ?></th>
                            <th style="width: 10%;"><?= __('Type') ?></th>
                            <th><?= __('Notes') ?></th>
                            <th style="width: 13%;"><?= __('Status') ?></th>
                            <th style="width: 12%;"><?= __('Date') ?></th>
                            <th style="width: 9%;"><?= __('File') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($docs as $doc):
                        $sub = $submissions[$doc->id] ?? null;
                    ?>
                        <?php
                            $uploadedDoc = $uploadedDocs[$doc->id] ?? null;
                            $fileExists = false;
                            if ($uploadedDoc && !empty($uploadedDoc->file_path)) {
                                $fileFull = WWW_ROOT . str_replace('/', DS, $uploadedDoc->file_path);
                                $fileExists = file_exists($fileFull);
                            }
                            $hasFile = $uploadedDoc && !empty($uploadedDoc->file_path);
                            $isUploaded = $hasFile || (!empty($submissions[$doc->id]) && $submissions[$doc->id]->submitted);
                            $dateStr = ($sub && $sub->submission_date) ? (is_string($sub->submission_date) ? $sub->submission_date : $sub->submission_date->format('Y-m-d')) : '';
                            if ($hasFile && empty($dateStr) && !empty($uploadedDoc->submission_date)) {
                                $dateStr = is_string($uploadedDoc->submission_date) ? $uploadedDoc->submission_date : $uploadedDoc->submission_date->format('Y-m-d');
                            }
                            $uploadUrl = $this->Url->build([
                                'controller' => 'CandidateDocuments', 'action' => 'add',
                                '?' => ['candidate_id' => $candidateId, 'master_list_id' => $doc->id, 'title' => $doc->title]
                            ], ['escape' => false]);
                        ?>
                        <tr class="<?= $isUploaded ? 'row-uploaded' : '' ?>">
                            <td><strong><?= h($doc->title) ?></strong></td>
                            <td>
                                <?php if ($doc->is_required): ?>
                                    <span class="badge-required"><?= __('Required') ?></span>
                                <?php else: ?>
                                    <span class="badge-optional"><?= __('Optional') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted" style="font-size: 13px;"><?= h($doc->notes) ?></td>
                            <td>
                                <span class="doc-status-badge <?= $isUploaded ? 'uploaded' : 'missing' ?>">
                                    <i class="fas <?= $isUploaded ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                    <?= $isUploaded ? __('Uploaded') : __('Not Uploaded') ?>
                                </span>
                            </td>
                            <td class="doc-date"><?= h($dateStr) ?></td>
                            <td>
                                <?php if ($fileExists): ?>
                                    <button type="button" class="btn-doc-action btn-doc-view"
                                            onclick="openDocViewer('<?= h($uploadedDoc->file_path) ?>', '<?= h(basename($uploadedDoc->file_path)) ?>')">
                                        <i class="fas fa-eye"></i> <?= __('View') ?>
                                    </button>
                                <?php else: ?>
                                    <a href="<?= $uploadUrl ?>" class="btn-doc-action btn-doc-upload">
                                        <i class="fas fa-upload"></i> <?= __('Upload') ?>
                                    </a>
                                <?php endif; ?>
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
document.addEventListener('DOMContentLoaded', function () {
    var sel = document.getElementById('checklist-candidate');
    if (sel) {
        sel.addEventListener('change', function () {
            var id = this.value;
            var url = '<?= $this->Url->build(['controller' => 'CandidateDocuments', 'action' => 'submissionChecklist']) ?>';
            window.location = id ? url + '?candidate_id=' + id : url;
        });
    }
});

var baseUrl = '<?= rtrim($this->Url->build('/', ['fullBase' => true]), '/') ?>';

function openDocViewer(filePath, fileName) {
    var ext = filePath.split('.').pop().toLowerCase();
    var url = baseUrl + '/' + filePath;
    var body = document.getElementById('docViewBody');
    body.innerHTML = '';

    if (['jpg','jpeg','png','gif','bmp','webp','svg'].indexOf(ext) >= 0) {
        var img = document.createElement('img');
        img.src = url;
        img.style.cssText = 'max-width:100%;max-height:100%;display:block;margin:auto;padding:16px;box-sizing:border-box;';
        body.appendChild(img);
    } else if (ext === 'pdf') {
        var frame = document.createElement('iframe');
        frame.src = url;
        frame.style.cssText = 'width:100%;height:100%;border:none;display:block;';
        frame.frameBorder = '0';
        body.appendChild(frame);
    } else {
        body.innerHTML = '<div style="text-align:center;padding:60px;">'
            + '<i class="fas fa-file" style="font-size:56px;color:#667eea;margin-bottom:16px;display:block;"></i>'
            + '<p style="font-size:14px;font-weight:600;">' + fileName + '</p>'
            + '<a href="' + url + '" download class="btn-doc-action btn-doc-view" style="display:inline-flex;">'
            + '<i class="fas fa-download"></i> Download</a></div>';
    }

    document.getElementById('docViewTitle').textContent = fileName;
    document.getElementById('docViewOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeDocViewer() {
    document.getElementById('docViewOverlay').classList.remove('open');
    document.getElementById('docViewBody').innerHTML = '';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDocViewer();
});
</script>
<?php $this->end(); ?>

<!-- File View Modal Overlay -->
<div id="docViewOverlay" onclick="if(event.target===this)closeDocViewer()">
    <div id="docViewDialog">
        <div id="docViewHeader">
            <strong id="docViewTitle"></strong>
            <button id="docViewClose" onclick="closeDocViewer()" title="Close">&times;</button>
        </div>
        <div id="docViewBody"></div>
    </div>
</div>

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
