<?php
/**
 * Document Cost Management (enriched)
 *
 * @var \App\View\AppView $this
 * @var array $costs
 * @var array $traineeMap
 * @var array $summary
 * @var array $byType
 */
$money = function ($v) { return number_format((float)$v, 0); };
$statusColor = ['paid' => '#28a745', 'partial' => '#fd7e14', 'unpaid' => '#dc3545'];
?>
<style>
.dc-stat{flex:1;min-width:150px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #667eea;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.dc-stat .v{font-size:22px;font-weight:800;color:#212a3e}
.dc-stat .l{font-size:12px;color:#6c757d}
.dc-table th{padding:11px 12px;border-bottom:2px solid #667eea;background:linear-gradient(135deg,rgba(102,126,234,.12),rgba(118,75,162,.12));white-space:nowrap;font-size:13px}
.dc-table td{padding:9px 12px;border-bottom:1px solid #edf0f4;font-size:13px}
.pay-badge{display:inline-block;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:700;color:#fff}
.doc-tag{display:inline-block;padding:2px 8px;border-radius:8px;font-size:11px;background:#eef1fb;color:#4c5bd4;font-weight:600}
</style>

<div class="index-header" style="margin-bottom:16px">
    <h2 style="margin:0"><i class="fas fa-file-invoice-dollar"></i> <?= __('Document Cost Management') ?></h2>
</div>

<!-- Ringkasan biaya -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="dc-stat"><div class="v"><?= (int)$summary['count'] ?></div><div class="l"><i class="fas fa-receipt"></i> <?= __('Cost Records') ?></div></div>
    <div class="dc-stat" style="border-left-color:#667eea"><div class="v"><?= $money($summary['total']) ?></div><div class="l"><i class="fas fa-coins"></i> <?= __('Total Billed') ?></div></div>
    <div class="dc-stat" style="border-left-color:#28a745"><div class="v"><?= $money($summary['paid']) ?></div><div class="l"><i class="fas fa-check-circle"></i> <?= __('Paid') ?></div></div>
    <div class="dc-stat" style="border-left-color:#dc3545"><div class="v"><?= $money($summary['outstanding']) ?></div><div class="l"><i class="fas fa-exclamation-circle"></i> <?= __('Outstanding') ?></div></div>
</div>

<?php if (!empty($byType)): ?>
<div style="margin-bottom:14px;font-size:13px;color:#6c757d">
    <strong><?= __('By document type') ?>:</strong>
    <?php foreach ($byType as $t => $amt): ?>
        <span class="doc-tag" style="margin:0 4px"><?= h(ucfirst(str_replace('_', ' ', $t))) ?>: <?= $money($amt) ?></span>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div style="margin-bottom:10px">
    <input type="text" id="dcSearch" class="form-control" placeholder="🔍 <?= __('Filter by trainee / type / status…') ?>"
           style="max-width:340px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
</div>

<div class="table-scroll-wrapper" style="overflow-x:auto">
    <table class="table dc-table" style="border-collapse:collapse;width:100%" id="dcTable">
        <thead>
            <tr>
                <th><?= __('Trainee') ?></th>
                <th><?= __('Document Type') ?></th>
                <th><?= __('Description') ?></th>
                <th style="text-align:right"><?= __('Amount') ?></th>
                <th style="text-align:right"><?= __('Paid') ?></th>
                <th style="text-align:center"><?= __('Status') ?></th>
                <th><?= __('Payment Date') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($costs as $row): ?>
            <?php $t = $traineeMap[$row['trainee_id']] ?? null; ?>
            <tr>
                <td>
                    <?php if ($t): ?>
                        <strong><?= $this->Html->link(h($t['name']), '/trainees/view/' . $row['trainee_id'], ['style' => 'color:#4c5bd4']) ?></strong>
                        <small style="color:#98a2ad"><?= h($t['tmm_code']) ?></small>
                    <?php else: ?>#<?= h($row['trainee_id']) ?><?php endif; ?>
                </td>
                <td><span class="doc-tag"><?= h(ucfirst(str_replace('_', ' ', $row['document_type']))) ?></span></td>
                <td style="color:#555"><?= h($row['description']) ?></td>
                <td style="text-align:right;font-weight:700"><?= $money($row['amount']) ?> <small style="color:#98a2ad"><?= h($row['currency_code']) ?></small></td>
                <td style="text-align:right;color:#28a745"><?= $row['paid_amount'] ? $money($row['paid_amount']) : '—' ?></td>
                <td style="text-align:center"><span class="pay-badge" style="background:<?= $statusColor[$row['payment_status']] ?? '#6c757d' ?>"><?= h(__(ucfirst($row['payment_status']))) ?></span></td>
                <td style="color:#6c757d"><?= h($row['payment_date']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($costs)): ?>
            <tr><td colspan="7" style="padding:15px;color:#6c757d;text-align:center"><?= __('No document cost records yet.') ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('dcSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#dcTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>
