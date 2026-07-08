<?php
/**
 * @var \App\View\AppView $this
 * @var array $grouped
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <h2 style="margin: 0;"><?= __('Account Hierarchy') ?></h2>
    <p style="color: #6c757d;"><?= __('Chart of accounts grouped by account type.') ?></p>
</div>

<?php foreach ($grouped as $type => $accounts): ?>
<div class="card" style="margin-bottom: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); background: #fff; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 16px;">
        <strong><?= h(ucfirst($type)) ?></strong>
        <span style="opacity: 0.8;">(<?= count($accounts) ?>)</span>
    </div>
    <table class="table" style="border-collapse: collapse; width: 100%;">
        <tbody>
            <?php foreach ($accounts as $account): ?>
            <tr style="border-bottom: 1px solid #e9ecef;">
                <td style="padding: 10px 16px; width: 140px;"><code><?= h($account['code']) ?></code></td>
                <td style="padding: 10px 16px;"><?= h($account['name']) ?></td>
                <td style="padding: 10px 16px; width: 110px;">
                    <?= $account['is_active'] ? '<span style="color:#28a745;">Active</span>' : '<span style="color:#dc3545;">Inactive</span>' ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<?php if (empty($grouped)): ?>
<p style="color: #6c757d;"><?= __('No accounts defined yet.') ?></p>
<?php endif; ?>
