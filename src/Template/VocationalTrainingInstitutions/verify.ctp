<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <h2 style="margin: 0;"><?= __('LPK Verification') ?></h2>
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto;">
    <table class="table" style="border-collapse: collapse; width: 100%;">
        <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
            <tr>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Actions') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('ID') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Name') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Status') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Registered') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Registered At') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;"><?= __('Email') ?></th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($institutions as $row): ?>
            <tr style="border-bottom: 1px solid #e9ecef;">
                <td style="padding: 10px 12px; white-space: nowrap;">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </td>
                <td style="padding: 10px 12px;"><?= h($row['id']) ?></td>
                <td style="padding: 10px 12px;"><?= h($row['name']) ?></td>
                <td style="padding: 10px 12px;"><?= h($row['status']) ?></td>
                <td style="padding: 10px 12px;"><?= $row['is_registered'] ? '<span style="color:#28a745;">&#10004; Yes</span>' : '<span style="color:#dc3545;">&#10008; No</span>' ?></td>
                <td style="padding: 10px 12px;"><?= h($row['registered_at']) ?></td>
                <td style="padding: 10px 12px;"><?= h($row['email']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="paginator" style="margin-top: 15px;">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
    </ul>
</div>
