<?php
/**
 * User view - profile card
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'User: ' . ($user->full_name ?: $user->username));

$roleColor = function ($roleName) {
    $map = [
        'administrator' => '#764ba2', 'management' => '#2b7fd4', 'tmm-recruitment' => '#e67e22',
        'tmm-training' => '#16a085', 'tmm-documentation' => '#2980b9', 'tmm-accounting' => '#27ae60',
        'lpk-penyangga' => '#8e44ad',
    ];
    return isset($map[$roleName]) ? $map[$roleName] : '#607d8b';
};
$src = trim((string)$user->full_name) ?: (string)$user->username;
$parts = preg_split('/\s+/', $src);
$initials = '';
foreach (array_slice($parts, 0, 2) as $p) { $initials .= mb_strtoupper(mb_substr($p, 0, 1)); }
$initials = $initials ?: 'U';
$statusMeta = [
    'active' => ['label' => __('Active'), 'class' => 'st-active', 'icon' => 'fa-check-circle'],
    'pending_verification' => ['label' => __('Pending Verification'), 'class' => 'st-pending', 'icon' => 'fa-hourglass-half'],
    'suspended' => ['label' => __('Suspended'), 'class' => 'st-suspended', 'icon' => 'fa-ban'],
];
$sm = isset($statusMeta[$user->status]) ? $statusMeta[$user->status] : ['label' => $user->status, 'class' => 'st-other', 'icon' => 'fa-user'];
$fmt = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Time) {
        return $value->format('d M Y H:i');
    }
    return $value ? (string)$value : '—';
};
?>

<div class="user-view-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-user"></i> <?= __('User Detail') ?></h2>
            <p class="text-muted"><?= __('Account profile and access') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-edit"></i> ' . __('Edit'),
                ['action' => 'edit', $user->id], ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
            <?= $this->Form->postLink('<i class="fa fa-trash"></i> ' . __('Delete'),
                ['action' => 'delete', $user->id],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-danger',
                 'confirm' => __('Delete user {0}?', $user->username)]) ?>
            <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('All Users'),
                ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="profile-card">
        <span class="profile-avatar"><?= h($initials) ?></span>
        <div class="profile-main">
            <h3><?= h($user->full_name ?: $user->username) ?></h3>
            <div class="profile-username"><i class="fa fa-at"></i> <?= h($user->username) ?></div>
            <div class="profile-badges">
                <span class="status-badge <?= $sm['class'] ?>"><i class="fa <?= $sm['icon'] ?>"></i> <?= h($sm['label']) ?></span>
                <?php if ($user->is_active): ?>
                    <span class="status-badge st-active"><i class="fa fa-toggle-on"></i> <?= __('Enabled') ?></span>
                <?php else: ?>
                    <span class="status-badge st-suspended"><i class="fa fa-toggle-off"></i> <?= __('Disabled') ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile-roles">
            <div class="pr-label"><?= __('Roles') ?></div>
            <?php if (!empty($user->roles)): ?>
                <?php foreach ($user->roles as $role): ?>
                    <span class="role-badge" style="background: <?= $roleColor($role->name) ?>;"><?= h($role->name) ?></span>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="text-muted"><?= __('No role assigned') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-address-card-o"></i> <?= __('Account') ?></h4></div>
                <div class="card-body">
                    <table class="detail-table">
                        <tr><th><?= __('User ID') ?></th><td>#<?= h($user->id) ?></td></tr>
                        <tr><th><?= __('Full Name') ?></th><td><?= h($user->full_name ?: '—') ?></td></tr>
                        <tr><th><?= __('Username') ?></th><td><?= h($user->username) ?></td></tr>
                        <tr><th><?= __('Email') ?></th><td><?= h($user->email) ?></td></tr>
                        <tr><th><?= __('Email Verified') ?></th>
                            <td><?= $user->email_verified_at ? '<span class="mini-ok"><i class="fa fa-check"></i> ' . h($fmt($user->email_verified_at)) . '</span>' : '<span class="text-muted">' . __('Not verified') . '</span>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-building"></i> <?= __('Institution & Meta') ?></h4></div>
                <div class="card-body">
                    <table class="detail-table">
                        <tr><th><?= __('Institution Type') ?></th>
                            <td><?= $user->institution_type ? h(ucwords(str_replace('_', ' ', $user->institution_type))) : '—' ?></td>
                        </tr>
                        <tr><th><?= __('Institution ID') ?></th><td><?= $user->institution_id ? '#' . h($user->institution_id) : '—' ?></td></tr>
                        <tr><th><?= __('Created') ?></th><td><?= h($fmt($user->created)) ?></td></tr>
                        <tr><th><?= __('Last Modified') ?></th><td><?= h($fmt($user->modified)) ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-view-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.user-view-page .page-header h2 { margin: 0 0 5px 0; }
.profile-card {
    display: flex;
    align-items: center;
    gap: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 22px 24px;
    margin-bottom: 20px;
    border-left: 5px solid #667eea;
    flex-wrap: wrap;
}
.profile-avatar {
    flex: 0 0 72px;
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
    font-size: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-main { flex: 1; min-width: 200px; }
.profile-main h3 { margin: 0 0 4px 0; }
.profile-username { color: #78909c; font-size: 0.95em; margin-bottom: 8px; }
.profile-badges { display: flex; gap: 8px; flex-wrap: wrap; }
.profile-roles { text-align: right; min-width: 160px; }
.pr-label { font-size: 0.75em; color: #90a4ae; text-transform: uppercase; margin-bottom: 5px; }
.user-view-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
}
.user-view-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.user-view-page .card-header h4 { margin: 0; }
.user-view-page .card-body { padding: 20px; }
.detail-table { width: 100%; border-collapse: collapse; }
.detail-table th {
    text-align: left;
    padding: 8px 12px 8px 0;
    color: #78909c;
    font-weight: normal;
    width: 42%;
    vertical-align: top;
}
.detail-table td { padding: 8px 0; font-weight: 500; }
.role-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    color: white;
    font-size: 11px;
    font-weight: bold;
    margin: 2px;
    white-space: nowrap;
}
.status-badge {
    padding: 5px 12px;
    border-radius: 14px;
    font-size: 12px;
    font-weight: bold;
    white-space: nowrap;
}
.st-active { background: #e6f9ee; color: #1e7e42; }
.st-pending { background: #fff8e1; color: #856404; }
.st-suspended { background: #fdeaea; color: #c0392b; }
.st-other { background: #f1f3f5; color: #6c757d; }
.mini-ok { color: #1e7e42; font-weight: bold; }
</style>
