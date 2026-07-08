<?php
/**
 * @var \App\View\AppView $this
 * @var array $byYear
 * @var array $totals
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <h2 style="margin: 0;"><?= __('Order Statistics') ?></h2>
</div>

<div class="row" style="display: flex; gap: 20px; margin-bottom: 25px;">
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; min-width: 220px;">
        <h3 style="margin: 0; font-size: 2.2em;"><?= number_format($totals['orders']) ?></h3>
        <p style="margin: 5px 0 0 0; opacity: 0.9;"><?= __('Total Orders') ?></p>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px; min-width: 220px;">
        <h3 style="margin: 0; font-size: 2.2em;"><?= number_format($totals['trainees']) ?></h3>
        <p style="margin: 5px 0 0 0; opacity: 0.9;"><?= __('Total Requested Trainees') ?></p>
    </div>
</div>

<table class="table" style="border-collapse: collapse; width: 100%; max-width: 800px;">
    <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
        <tr>
            <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Departure Year') ?></th>
            <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Orders') ?></th>
            <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Male') ?></th>
            <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Female') ?></th>
            <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Total') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($byYear as $row): ?>
        <tr style="border-bottom: 1px solid #e9ecef;">
            <td style="padding: 10px 12px;"><?= h($row['departure_year'] ?: '(not set)') ?></td>
            <td style="padding: 10px 12px;"><?= number_format($row['order_count']) ?></td>
            <td style="padding: 10px 12px;"><?= number_format((int)$row['total_male']) ?></td>
            <td style="padding: 10px 12px;"><?= number_format((int)$row['total_female']) ?></td>
            <td style="padding: 10px 12px;"><?= number_format((int)$row['total_male'] + (int)$row['total_female']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($byYear)): ?>
        <tr><td colspan="5" style="padding: 15px; color: #6c757d;"><?= __('No orders yet.') ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>
