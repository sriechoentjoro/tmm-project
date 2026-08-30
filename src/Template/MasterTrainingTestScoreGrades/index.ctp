<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterTrainingTestScoreGrade[]|\Cake\Collection\CollectionInterface $masterTrainingTestScoreGrades
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <h2 style="margin: 0;"><?= __('Master Training Test Score Grades') ?></h2>
        <div style="display: flex; align-items: center; gap: 10px;">
            <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('Add New'), ['action' => 'add'], ['class' => 'btn-export-light', 'escape' => false]) ?>
        </div>
    </div>
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div class="masterTrainingTestScoreGrades index content">
        <table class="table" style="border-collapse: collapse; width: 100%; min-width: 800px;">
            <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                <tr>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" class="actions"><?= __('Actions') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('title') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('min_score') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('max_score') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($masterTrainingTestScoreGrades as $row): ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 10px 12px; white-space: nowrap;" class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    </td>
                    <td style="padding: 10px 12px;"><?= h($row['id']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['title']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['min_score']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['max_score']) ?></td>
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
