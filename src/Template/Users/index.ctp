<?php
/**
 * Users - rich admin index
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 * @var array $roleList     [id => name]
 * @var array $summary      ['total','active','pending','inactive']
 * @var array $byRole       [roleId => count]
 * @var int $filterRole
 * @var string $filterStatus
 * @var string $search
 */
$this->assign('title', 'User Management');

$roleColor = function ($roleName) {
    $map = [
        'administrator' => '#764ba2', 'management' => '#2b7fd4', 'tmm-recruitment' => '#e67e22',
        'tmm-training' => '#16a085', 'tmm-documentation' => '#2980b9', 'tmm-accounting' => '#27ae60',
        'lpk-penyangga' => '#8e44ad',
    ];
    return isset($map[$roleName]) ? $map[$roleName] : '#607d8b';
};
$statusMeta = [
    'active' => ['label' => __('Active'), 'class' => 'st-active'],
    'pending_verification' => ['label' => __('Pending'), 'class' => 'st-pending'],
    'suspended' => ['label' => __('Suspended'), 'class' => 'st-suspended'],
];
$initials = function ($name, $username) {
    $src = trim((string)$name) ?: (string)$username;
    $parts = preg_split('/\s+/', $src);
    $ini = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $ini .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $ini ?: 'U';
};
$hasFilter = $filterRole || $filterStatus !== '' || $search !== '';
?>

<div class="users-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-users"></i> <?= __('User Management') ?></h2>
            <p class="text-muted"><?= __('System accounts, roles and access') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('New User'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link('<i class="fa fa-shield"></i> ' . __('Manage Roles'),
                ['controller' => 'Roles', 'action' => 'index'],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= number_format($summary['total']) ?></span>
            <span class="summary-label"><i class="fa fa-users"></i> <?= __('Total Users') ?></span>
        </div>
        <div class="summary-card summary-active">
            <span class="summary-number"><?= number_format($summary['active']) ?></span>
            <span class="summary-label"><i class="fa fa-check-circle"></i> <?= __('Active') ?></span>
        </div>
        <div class="summary-card summary-pending">
            <span class="summary-number"><?= number_format($summary['pending']) ?></span>
            <span class="summary-label"><i class="fa fa-hourglass-half"></i> <?= __('Pending') ?></span>
        </div>
        <div class="summary-card summary-roles">
            <span class="summary-number"><?= number_format(count($roleList)) ?></span>
            <span class="summary-label"><i class="fa fa-shield"></i> <?= __('Roles') ?></span>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar">
        <form method="get" class="filter-form">
            <div class="filter-field filter-search">
                <label><?= __('Search') ?></label>
                <input type="text" name="q" value="<?= h($search) ?>" placeholder="<?= __('Name, username or email…') ?>">
            </div>
            <div class="filter-field">
                <label><?= __('Role') ?></label>
                <select name="role_id" onchange="this.form.submit()">
                    <option value=""><?= __('All roles') ?></option>
                    <?php foreach ($roleList as $rid => $rname): ?>
                        <option value="<?= $rid ?>" <?= $filterRole == $rid ? 'selected' : '' ?>>
                            <?= h($rname) ?> (<?= isset($byRole[$rid]) ? $byRole[$rid] : 0 ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label><?= __('Status') ?></label>
                <select name="status" onchange="this.form.submit()">
                    <option value=""><?= __('All statuses') ?></option>
                    <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>><?= __('Active') ?></option>
                    <option value="pending_verification" <?= $filterStatus === 'pending_verification' ? 'selected' : '' ?>><?= __('Pending') ?></option>
                    <option value="suspended" <?= $filterStatus === 'suspended' ? 'selected' : '' ?>><?= __('Suspended') ?></option>
                </select>
            </div>
            <div class="filter-field">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> <?= __('Search') ?></button>
            </div>
            <?php if ($hasFilter): ?>
                <div class="filter-field">
                    <label>&nbsp;</label>
                    <?= $this->Html->link('<i class="fa fa-times"></i> ' . __('Reset'),
                        ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php $rows = iterator_to_array($users); ?>
    <?php if (!empty($rows)): ?>
        <div class="card">
            <div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table rich-table">
                    <thead>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('User') ?></th>
                            <th><?= $this->Paginator->sort('email', __('Email')) ?></th>
                            <th><?= __('Roles') ?></th>
                            <th><?= __('Institution') ?></th>
                            <th><?= $this->Paginator->sort('status', __('Status')) ?></th>
                            <th><?= $this->Paginator->sort('is_active', __('Enabled')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $user): ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', $user->id],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-info', 'title' => __('View')]) ?>
                                <?= $this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'edit', $user->id],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary', 'title' => __('Edit')]) ?>
                                <?= $this->Form->postLink('<i class="fa fa-trash"></i>', ['action' => 'delete', $user->id],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-danger', 'title' => __('Delete'),
                                     'confirm' => __('Delete user {0}? This cannot be undone.', $user->username)]) ?>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar"><?= h($initials($user->full_name, $user->username)) ?></span>
                                    <div>
                                        <strong><?= h($user->full_name ?: $user->username) ?></strong>
                                        <div class="text-muted"><i class="fa fa-at"></i> <?= h($user->username) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= h($user->email) ?></td>
                            <td>
                                <?php if (!empty($user->roles)): ?>
                                    <?php foreach ($user->roles as $role): ?>
                                        <span class="role-badge" style="background: <?= $roleColor($role->name) ?>;"><?= h($role->name) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted"><?= __('no role') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user->institution_id): ?>
                                    <span class="inst-chip"><i class="fa fa-building-o"></i>
                                        <?= h($user->institution_type === 'vocational_training' ? 'LPK' : 'SO') ?> #<?= h($user->institution_id) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $sm = isset($statusMeta[$user->status]) ? $statusMeta[$user->status] : ['label' => $user->status, 'class' => 'st-other']; ?>
                                <span class="status-badge <?= $sm['class'] ?>"><?= h($sm['label']) ?></span>
                            </td>
                            <td>
                                <?php if ($user->is_active): ?>
                                    <span class="toggle-badge toggle-on"><i class="fa fa-check"></i> <?= __('Yes') ?></span>
                                <?php else: ?>
                                    <span class="toggle-badge toggle-off"><i class="fa fa-ban"></i> <?= __('No') ?></span>
                                <?php endif; ?>
                            </td>
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
                <i class="fa fa-user-times fa-4x"></i>
                <h4><?= $hasFilter ? __('No users match the current filter.') : __('No users yet.') ?></h4>
                <div>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('New User'),
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
.users-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.users-page .page-header h2 { margin: 0 0 5px 0; }
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
.summary-total   { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-active  { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-pending { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-roles   { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
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
.filter-field { display: flex; flex-direction: column; min-width: 150px; }
.filter-search { flex: 1; min-width: 220px; }
.filter-field label { font-size: 12px; font-weight: bold; color: #6c757d; margin-bottom: 4px; }
.filter-field input, .filter-field select {
    padding: 8px 11px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #fff;
}
.users-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
    overflow: hidden;
}
.rich-table { border-collapse: collapse; width: 100%; min-width: 900px; margin: 0; }
.rich-table thead {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
}
.rich-table th { padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap; text-align: left; }
.rich-table td { padding: 10px 12px; border-bottom: 1px solid #e9ecef; vertical-align: middle; }
.rich-table tbody tr:hover { background: #f8f9ff; }
.rich-table .actions { white-space: nowrap; }
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
.rich-table td.actions .btn { padding: 4px 7px; }
.table-scroll-wrapper.is-scrolled .rich-table th.actions,
.table-scroll-wrapper.is-scrolled .rich-table td.actions {
    border-right: 2px solid #d3d9f5;
    box-shadow: 4px 0 8px -2px rgba(60, 70, 130, 0.18);
}
.user-cell { display: flex; align-items: center; gap: 10px; }
.avatar {
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.user-cell .text-muted { font-size: 0.85em; }
.role-badge {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 12px;
    color: white;
    font-size: 10px;
    font-weight: bold;
    margin: 1px 2px 1px 0;
    white-space: nowrap;
}
.inst-chip {
    background: #eef1ff;
    color: #3b4890;
    padding: 3px 9px;
    border-radius: 10px;
    font-size: 0.85em;
    font-weight: bold;
    white-space: nowrap;
}
.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}
.st-active { background: #e6f9ee; color: #1e7e42; }
.st-pending { background: #fff8e1; color: #856404; }
.st-suspended { background: #fdeaea; color: #c0392b; }
.st-other { background: #f1f3f5; color: #6c757d; }
.toggle-badge { font-size: 0.85em; font-weight: bold; }
.toggle-on { color: #28a745; }
.toggle-off { color: #adb5bd; }
.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
.empty-state .btn { margin: 5px; }
</style>

<script>
(function () {
    document.querySelectorAll('.users-page .table-scroll-wrapper').forEach(function (wrapper) {
        var update = function () {
            wrapper.classList.toggle('is-scrolled', wrapper.scrollLeft > 2);
        };
        wrapper.addEventListener('scroll', update, { passive: true });
        update();
    });
})();
</script>

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
