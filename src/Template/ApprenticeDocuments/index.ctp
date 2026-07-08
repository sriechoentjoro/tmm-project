<?php
/**
 * Apprentice Documents - rich index (documents for apprentices in Japan)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeDocument[]|\Cake\Collection\CollectionInterface $apprenticeDocuments
 * @var array $apprenticeNames [id => "Name (code)"]
 * @var array $documentTitles  [id => title]
 * @var array $statusNames     [id => name]
 * @var array $userNames       [id => username]
 * @var array $summary         ['total' => n, 'Submitted' => n, 'Pending' => n]
 * @var int $filterApprentice
 * @var int $filterDocument
 * @var int $filterStatus
 */
$this->assign('title', 'Apprentice Documents');

$formatDatetime = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Time) {
        return $value->format('Y-m-d H:i');
    }
    return $value ? (string)$value : '-';
};

$fileIcon = function ($path) {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $map = [
        'pdf' => 'fa-file-pdf-o', 'doc' => 'fa-file-word-o', 'docx' => 'fa-file-word-o',
        'xls' => 'fa-file-excel-o', 'xlsx' => 'fa-file-excel-o', 'zip' => 'fa-file-archive-o',
        'jpg' => 'fa-file-image-o', 'jpeg' => 'fa-file-image-o', 'png' => 'fa-file-image-o',
    ];
    return isset($map[$extension]) ? $map[$extension] : 'fa-file-o';
};

$hasFilter = $filterApprentice || $filterDocument || $filterStatus;
?>

<div class="ad-index-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-folder-open"></i> <?= __('Apprentice Documents') ?></h2>
            <p class="text-muted"><?= __('Submission documents for apprentices in Japan') ?> 🇯🇵</p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Document'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link('<i class="fa fa-users"></i> ' . __('Apprentices'),
                ['controller' => 'Apprentices', 'action' => 'index'],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= number_format(isset($summary['total']) ? $summary['total'] : 0) ?></span>
            <span class="summary-label"><i class="fa fa-files-o"></i> <?= __('Total Documents') ?></span>
        </div>
        <div class="summary-card summary-submitted">
            <span class="summary-number"><?= number_format(isset($summary['Submitted']) ? $summary['Submitted'] : 0) ?></span>
            <span class="summary-label"><i class="fa fa-check-circle"></i> <?= __('Submitted') ?></span>
        </div>
        <div class="summary-card summary-pending">
            <span class="summary-number"><?= number_format(isset($summary['Pending']) ? $summary['Pending'] : 0) ?></span>
            <span class="summary-label"><i class="fa fa-hourglass-half"></i> <?= __('Pending') ?></span>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar">
        <form method="get" class="filter-form">
            <div class="filter-field">
                <label><?= __('Apprentice') ?></label>
                <select name="apprentice_id" onchange="this.form.submit()">
                    <option value=""><?= __('All apprentices') ?></option>
                    <?php foreach ($apprenticeNames as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $filterApprentice == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label><?= __('Document') ?></label>
                <select name="document_id" onchange="this.form.submit()">
                    <option value=""><?= __('All documents') ?></option>
                    <?php foreach ($documentTitles as $id => $title): ?>
                        <option value="<?= $id ?>" <?= $filterDocument == $id ? 'selected' : '' ?>><?= h($title) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label><?= __('Status') ?></label>
                <select name="status_id" onchange="this.form.submit()">
                    <option value=""><?= __('All statuses') ?></option>
                    <?php foreach ($statusNames as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $filterStatus == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($hasFilter): ?>
                <div class="filter-field filter-reset">
                    <label>&nbsp;</label>
                    <?= $this->Html->link('<i class="fa fa-times"></i> ' . __('Reset'),
                        ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php $rows = iterator_to_array($apprenticeDocuments); ?>
    <?php if (!empty($rows)): ?>
        <div class="card">
            <div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table rich-table">
                    <thead>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                            <th><?= $this->Paginator->sort('apprentice_id', __('Apprentice')) ?></th>
                            <th><?= $this->Paginator->sort('apprenticeship_submission_document_id', __('Document')) ?></th>
                            <th><?= __('File') ?></th>
                            <th><?= $this->Paginator->sort('master_document_submission_status_id', __('Status')) ?></th>
                            <th><?= $this->Paginator->sort('uploaded_by', __('Uploaded By')) ?></th>
                            <th><?= $this->Paginator->sort('uploaded_at', __('Uploaded At')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-info', 'title' => __('View')]) ?>
                                <?= $this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'edit', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary', 'title' => __('Edit')]) ?>
                            </td>
                            <td class="text-muted">#<?= h($row['id']) ?></td>
                            <td>
                                <strong><?= h(isset($apprenticeNames[$row['apprentice_id']]) ? $apprenticeNames[$row['apprentice_id']] : ('#' . $row['apprentice_id'])) ?></strong>
                            </td>
                            <td>
                                <?= h(isset($documentTitles[$row['apprenticeship_submission_document_id']])
                                    ? $documentTitles[$row['apprenticeship_submission_document_id']]
                                    : ('#' . $row['apprenticeship_submission_document_id'])) ?>
                            </td>
                            <td>
                                <?php if ($row['file_path']): ?>
                                    <?php $filePath = str_replace('\\', '/', $row['file_path']); ?>
                                    <a href="<?= $this->Url->build('/' . ltrim($filePath, '/')) ?>" target="_blank" class="file-link" title="<?= h($filePath) ?>">
                                        <i class="fa <?= $fileIcon($filePath) ?>"></i>
                                        <?= h(basename($filePath)) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusName = isset($statusNames[$row['master_document_submission_status_id']])
                                    ? $statusNames[$row['master_document_submission_status_id']] : null;
                                $badgeClass = $statusName === 'Submitted' ? 'success' : ($statusName === 'Pending' ? 'warning' : 'secondary');
                                ?>
                                <span class="badge badge-<?= $badgeClass ?>"><?= h($statusName ?: '-') ?></span>
                            </td>
                            <td><?= h(isset($userNames[$row['uploaded_by']]) ? $userNames[$row['uploaded_by']] : ($row['uploaded_by'] ?: '-')) ?></td>
                            <td class="text-muted"><?= h($formatDatetime($row['uploaded_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="paginator" style="margin-top: 15px;">
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="empty-state">
                <i class="fa fa-folder-open fa-4x"></i>
                <h4><?= $hasFilter ? __('No documents match the current filter.') : __('No apprentice documents yet.') ?></h4>
                <p class="text-muted"><?= __('Documents submitted for apprentices in Japan will appear here.') ?></p>
                <div>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Document'),
                        ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-success']) ?>
                    <?php if ($hasFilter): ?>
                        <?= $this->Html->link(__('Reset Filter'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.ad-index-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.ad-index-page .page-header h2 { margin: 0 0 5px 0; }
.summary-strip { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.summary-card {
    flex: 1;
    min-width: 160px;
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.summary-total     { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-submitted { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-pending   { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-number { font-size: 28px; font-weight: bold; line-height: 1.1; }
.summary-label { font-size: 13px; opacity: 0.95; }
.filter-bar {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 15px 20px;
    margin-bottom: 20px;
}
.filter-form { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; }
.filter-field { display: flex; flex-direction: column; min-width: 180px; }
.filter-field label { font-size: 12px; font-weight: bold; color: #6c757d; margin-bottom: 4px; }
.filter-field select {
    padding: 7px 10px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #fff;
}
.ad-index-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
}
.rich-table { border-collapse: collapse; width: 100%; min-width: 900px; margin: 0; }
.rich-table thead {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
}
.rich-table th {
    padding: 12px;
    border-bottom: 2px solid #667eea;
    white-space: nowrap;
    text-align: left;
}
.rich-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}
.rich-table tbody tr:hover { background: #f8f9ff; }
.rich-table .actions { white-space: nowrap; }
/* Floating (sticky) actions column - divider only while scrolled */
.rich-table th.actions,
.rich-table td.actions {
    position: sticky;
    left: 0;
    z-index: 5;
    width: 1%;
    padding-left: 10px;
    padding-right: 10px;
}
.rich-table th.actions { background: #e9ecfa; z-index: 6; }
.rich-table td.actions { background: #fff; }
.rich-table tbody tr:hover td.actions { background: #f8f9ff; }
.rich-table td.actions .btn { padding: 4px 8px; }
.table-scroll-wrapper.is-scrolled .rich-table th.actions,
.table-scroll-wrapper.is-scrolled .rich-table td.actions {
    border-right: 2px solid #d3d9f5;
    box-shadow: 4px 0 8px -2px rgba(60, 70, 130, 0.18);
}
.file-link { color: #667eea; text-decoration: none; white-space: nowrap; }
.file-link:hover { text-decoration: underline; }
.file-link i { margin-right: 4px; }
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
}
.badge-success { background: #28a745; color: white; }
.badge-warning { background: #ffc107; color: #333; }
.badge-secondary { background: #6c757d; color: white; }
.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
.empty-state .btn { margin: 5px; }
</style>

<script>
(function () {
    document.querySelectorAll('.ad-index-page .table-scroll-wrapper').forEach(function (wrapper) {
        var update = function () {
            wrapper.classList.toggle('is-scrolled', wrapper.scrollLeft > 2);
        };
        wrapper.addEventListener('scroll', update, { passive: true });
        update();
    });
})();
</script>
