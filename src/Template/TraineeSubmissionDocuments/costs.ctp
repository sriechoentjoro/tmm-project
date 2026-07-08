<?php
/**
 * @var \App\View\AppView $this
 * @var array $costs
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <h2 style="margin: 0;"><?= __('Document Cost Management') ?></h2>
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto;">
    <table class="table" style="border-collapse: collapse; width: 100%;">
        <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
            <tr>
                <?php if (!empty($costs)): ?>
                    <?php foreach (array_keys($costs[0]) as $col): ?>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= h(ucwords(str_replace('_', ' ', $col))) ?></th>
                    <?php endforeach; ?>
                <?php else: ?>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Document Costs') ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($costs as $row): ?>
            <tr style="border-bottom: 1px solid #e9ecef;">
                <?php foreach ($row as $value): ?>
                <td style="padding: 10px 12px;"><?= h(is_scalar($value) || $value === null ? $value : (string)$value) ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($costs)): ?>
            <tr><td style="padding: 15px; color: #6c757d;"><?= __('No document cost records yet.') ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
