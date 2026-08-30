<?php
/**
 * Account Hierarchy (enriched) — saldo per akun dari journal_details
 *
 * @var \App\View\AppView $this
 * @var array $grouped
 * @var array $typeTotals
 */
$typeColor = [
    'Asset' => '#28a745', 'Liability' => '#dc3545', 'Equity' => '#667eea',
    'Revenue' => '#20c997', 'Expense' => '#fd7e14',
];
$money = function ($v) { return number_format((float)$v, 0); };
?>
<style>
.coa-stat{flex:1;min-width:150px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #667eea;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.coa-stat .v{font-size:20px;font-weight:800;color:#212a3e}
.coa-stat .l{font-size:12px;color:#6c757d}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <h2 style="margin:0"><i class="fas fa-sitemap"></i> <?= __('Account Hierarchy') ?></h2>
            <p style="color:#6c757d;margin:4px 0 0"><?= __('Chart of accounts grouped by type, with balances posted from journals.') ?></p>
        </div>
        <?= $this->Html->link('<i class="fas fa-book"></i> ' . __('Journals'), '/journals/auto-generated', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
    </div>
</div>

<!-- Ringkasan saldo per tipe -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px">
    <?php foreach ($typeTotals as $type => $total): ?>
    <div class="coa-stat" style="border-left-color:<?= $typeColor[$type] ?? '#6c757d' ?>">
        <div class="v"><?= $money($total) ?></div>
        <div class="l"><i class="fas fa-circle" style="color:<?= $typeColor[$type] ?? '#6c757d' ?>;font-size:9px"></i> <?= h(__($type)) ?> <?= __('balance') ?></div>
    </div>
    <?php endforeach; ?>
</div>

<?php foreach ($grouped as $type => $accounts): ?>
<div class="card" style="margin-bottom:20px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.08);background:#fff;overflow:hidden">
    <div style="background:<?= $typeColor[$type] ?? '#667eea' ?>;color:#fff;padding:12px 16px;display:flex;justify-content:space-between;align-items:center">
        <strong><?= h(__(ucfirst($type))) ?> <span style="opacity:.85">(<?= count($accounts) ?>)</span></strong>
        <span style="font-size:14px"><?= __('Total') ?>: <strong><?= $money($typeTotals[$type] ?? 0) ?></strong></span>
    </div>
    <table class="table" style="border-collapse:collapse;width:100%">
        <thead>
            <tr style="background:#f8f9fb">
                <th style="padding:8px 16px;width:130px;font-size:12px;color:#6c757d;text-align:left"><?= __('Code') ?></th>
                <th style="padding:8px 16px;font-size:12px;color:#6c757d;text-align:left"><?= __('Account') ?></th>
                <th style="padding:8px 16px;width:120px;font-size:12px;color:#6c757d;text-align:right"><?= __('Debit') ?></th>
                <th style="padding:8px 16px;width:120px;font-size:12px;color:#6c757d;text-align:right"><?= __('Credit') ?></th>
                <th style="padding:8px 16px;width:130px;font-size:12px;color:#6c757d;text-align:right"><?= __('Balance') ?></th>
                <th style="padding:8px 16px;width:90px;font-size:12px;color:#6c757d;text-align:center"><?= __('Status') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accounts as $account): ?>
            <tr style="border-bottom:1px solid #e9ecef">
                <td style="padding:10px 16px"><code><?= h($account['code']) ?></code></td>
                <td style="padding:10px 16px"><?= h($account['name']) ?>
                    <?php if ($account['_lines']): ?><small style="color:#98a2ad">· <?= (int)$account['_lines'] ?> <?= __('entries') ?></small><?php endif; ?>
                </td>
                <td style="padding:10px 16px;text-align:right;color:#555"><?= $account['_debit'] ? $money($account['_debit']) : '—' ?></td>
                <td style="padding:10px 16px;text-align:right;color:#555"><?= $account['_credit'] ? $money($account['_credit']) : '—' ?></td>
                <td style="padding:10px 16px;text-align:right;font-weight:700;color:<?= $account['_balance'] < 0 ? '#dc3545' : '#2a3444' ?>"><?= $money($account['_balance']) ?></td>
                <td style="padding:10px 16px;text-align:center">
                    <?= $account['is_active'] ? '<span style="color:#28a745">● ' . __('Active') . '</span>' : '<span style="color:#dc3545">○ ' . __('Inactive') . '</span>' ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<?php if (empty($grouped)): ?>
<p style="color:#6c757d"><?= __('No accounts defined yet.') ?></p>
<?php endif; ?>
