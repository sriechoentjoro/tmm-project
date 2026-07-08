<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <h2 style="margin: 0;"><?= __('Promotion History') ?></h2>
</div>

    <table class="table" style="border-collapse: collapse; width: 100%;">
        <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
            <tr>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('ID') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Source') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Record ID') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Promoted By') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Notes') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Date') ?></th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($histories as $row): ?>
            <tr style="border-bottom: 1px solid #e9ecef;">
                <td style="padding: 10px 12px;"><?= h($row['id']) ?></td>
                <td style="padding: 10px 12px;"><?= h($row['source_table']) ?></td>
                <td style="padding: 10px 12px;"><?= h($row['source_id']) ?></td>
                <td style="padding: 10px 12px;"><?= h($row['promoted_by_id']) ?></td>
                <td style="padding: 10px 12px;"><?= h($row['notes']) ?></td>
                <td style="padding: 10px 12px;"><?= h($row['created']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($histories)): ?>
            <tr><td colspan="6" style="padding: 15px; color: #6c757d;"><?= __('No promotion history recorded yet.') ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
