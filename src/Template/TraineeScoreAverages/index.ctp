<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeScoreAverage[]|\Cake\Collection\CollectionInterface $traineeScoreAverages
 * @var array $standing "traineeId:competencyId" => ['average','grade_id'] from the test scores
 * @var int $drifted How many rows disagree with them
 * @var array $mastertrainingtestscoregrades [id => title]
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <h2 style="margin: 0;"><?= __('Trainee Score Averages') ?></h2>
        <div style="display: flex; align-items: center; gap: 10px;">
            <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('Add New'), ['action' => 'add'], ['class' => 'btn-export-light', 'escape' => false]) ?>
        </div>
    </div>
</div>

<?php if (!empty($drifted)): ?>
    <div class="drift-note">
        <div>
            <strong><i class="fas fa-exclamation-triangle"></i>
                <?= __('{0} row(s) no longer match the test scores', $drifted) ?></strong>
            <p><?= __('The average and the grade here are typed into the form, not calculated, so they were right when somebody entered them and drift as soon as a test score is entered, corrected or deleted. Where a row disagrees, the computed figure is shown beside it with an arrow. The scores are the ones to believe.') ?></p>
        </div>
        <?= $this->Form->postLink(
            '<i class="fas fa-sync"></i> ' . __('Recompute them all'),
            ['action' => 'refresh'],
            ['escape' => false, 'class' => 'btn btn-sm btn-warning',
             'confirm' => __('Replace the typed averages and grades with what the test scores give, on every row?')]
        ) ?>
    </div>
<?php elseif (!empty($standing)): ?>
    <div class="drift-note drift-ok">
        <div>
            <strong><i class="fas fa-check-circle"></i> <?= __('Every row matches the test scores') ?></strong>
            <p><?= __('These figures are typed rather than calculated, so this is true as of now rather than kept true. Check again after tests are marked.') ?></p>
        </div>
    </div>
<?php endif; ?>

<style>
.drift-note {
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    flex-wrap: wrap;
    padding: 13px 18px; margin: 0 0 16px; border-radius: 10px;
    background: #fff8e1; border-left: 4px solid #fb8c00; color: #5d4037;
}
.drift-note.drift-ok { background: #e8f5e9; border-left-color: #43a047; color: #33691e; }
.drift-note strong { display: block; margin-bottom: 4px; color: #e65100; }
.drift-note.drift-ok strong { color: #2e7d32; }
.drift-note p { margin: 0; font-size: 13px; line-height: 1.6; max-width: 76ch; }
.counted {
    display: inline-block; margin-left: 6px; padding: 1px 6px;
    border-radius: 8px; background: #fff3e0; color: #e65100;
    font-size: 11px; font-weight: 800;
}
</style>

<div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div class="traineeScoreAverages index content">
        <table class="table" style="border-collapse: collapse; width: 100%; min-width: 800px;">
            <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                <tr>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" class="actions"><?= __('Actions') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('trainee_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_training_competency_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('score_average') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_training_test_score_grade_id') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($traineeScoreAverages as $row): ?>
                <?php $real = $standing[(int)$row->trainee_id . ':' . (int)$row->master_training_competency_id] ?? null; ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 10px 12px; white-space: nowrap;" class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    </td>
                    <td style="padding: 10px 12px;"><?= h($row['id']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row->has('trainee') ? $row->trainee->name : $row['trainee_id']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row->has('master_training_competency') ? $row->master_training_competency->title : $row['master_training_competency_id']) ?></td>
                    <td style="padding: 10px 12px;">
                        <?= h($row['score_average']) ?>
                        <?php if ($real !== null && abs((float)$row['score_average'] - (float)$real['average']) > 0.05): ?>
                            <span class="counted" title="<?= h(__('What the test scores average to')) ?>">→ <?= h($real['average']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 10px 12px;">
                        <?= h($row->has('master_training_test_score_grade') ? $row->master_training_test_score_grade->title : $row['master_training_test_score_grade_id']) ?>
                        <?php if ($real !== null && $real['grade_id'] !== null
                            && (int)$row['master_training_test_score_grade_id'] !== $real['grade_id']): ?>
                            <span class="counted" title="<?= h(__('The band that average falls in')) ?>">
                                → <?= h($mastertrainingtestscoregrades[$real['grade_id']] ?? ('#' . $real['grade_id'])) ?>
                            </span>
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
