<?php
/**
 * Where an apprentice stands in the last two steps, and TMM Training's calls.
 *
 * The two flags the reports count - "in Japan" and "completed" - had nowhere
 * to be set. is_apprenticeship_pass was written as 0 by the promotion that
 * created the record and touched by nothing afterwards; is_apprentice_pass was
 * read in three places and written in none. This panel is where TMM Training
 * makes both calls, having heard its own test results (the certificate) and
 * TMM Documentation's (the documents and the check-up).
 *
 * It renders nothing where the columns are absent: an installation that has
 * not run bin/cake add_apprentice_flow_columns should show no button rather
 * than a button that silently saves less than it says.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Apprentice $apprentice
 * @var bool $canDecide Whether the reader may make these calls.
 */

$apprentices = \Cake\ORM\TableRegistry::getTableLocator()->get('Apprentices');
if (!$apprentices->getSchema()->hasColumn('departed_at')) {
    return;
}

$canDecide = $canDecide ?? false;
$departed = (bool)$apprentice->is_apprenticeship_pass;
$completed = (bool)$apprentice->get('is_apprentice_pass');
$departedAt = $apprentice->get('departed_at');
$mcu = $apprentices->getSchema()->hasColumn('mcu_result') ? $apprentice->get('mcu_result') : null;

$asDate = function ($value) {
    return $value instanceof \Cake\I18n\FrozenTime ? $value->i18nFormat('d MMM yyyy') : (string)$value;
};
?>
<?php $this->append('css'); ?>
<style>
.flow-panel {
    display: flex; flex-wrap: wrap; align-items: center; gap: 14px;
    padding: 14px 18px; margin: 0 0 18px; border-radius: 12px;
    background: #fff; border: 1px solid #e6ecf1; border-left: 5px solid #00BCD4;
}
.flow-panel .flow-state { font-size: 14px; color: #46586b; flex: 1 1 320px; line-height: 1.5; }
.flow-chip {
    display: inline-block; padding: 3px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700; white-space: nowrap;
}
.flow-chip.on { background: #e6f7ea; color: #1e7e42; }
.flow-chip.off { background: #f1f5f8; color: #66788a; }
.flow-chip.bad { background: #fdecea; color: #b3261e; }
.flow-panel .btn-flow {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 18px; border: 0; border-radius: 9px;
    font-size: 14px; font-weight: 600; color: #fff !important;
    text-decoration: none !important; cursor: pointer;
    background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
}
.flow-panel .btn-flow.done { background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%); }
.flow-panel .btn-flow.undo { background: #fff; color: #55677a !important; border: 1px solid #d8e1e8; }
</style>
<?php $this->end(); ?>

<div class="flow-panel">
    <div class="flow-state">
        <?php if ($completed) : ?>
            <span class="flow-chip on"><?= __('Programme completed') ?></span>
            <?= __('TMM Training recorded this programme as finished. The reports count this apprentice as completed.') ?>
        <?php elseif ($departed) : ?>
            <span class="flow-chip on"><?= __('In Japan') ?></span>
            <?= $departedAt
                ? __('Recorded as having left on {0}. The reports count this apprentice as in Japan.', $asDate($departedAt))
                : __('Recorded as having left. The reports count this apprentice as in Japan.') ?>
        <?php elseif ($mcu === 'fail') : ?>
            <span class="flow-chip bad"><?= __('Not medically fit') ?></span>
            <?= __('A check-up recorded for this apprentice is marked not fit, so a departure cannot be recorded until that changes.') ?>
        <?php else : ?>
            <span class="flow-chip off"><?= __('Not departed') ?></span>
            <?= __('The reports do not count this apprentice as in Japan yet. TMM Training records the departure once the certificate, the documents and the check-up are in.') ?>
        <?php endif; ?>
    </div>

    <?php if ($canDecide) : ?>
        <?php if (!$departed && $mcu !== 'fail') : ?>
            <?= $this->Form->postLink(
                '<i class="fas fa-plane-departure"></i> ' . __('Left for Japan'),
                ['action' => 'markDeparted', $apprentice->id],
                [
                    'class' => 'btn-flow',
                    'escape' => false,
                    'confirm' => __('Record that {0} has left for Japan?', $apprentice->name),
                ]
            ) ?>
        <?php elseif ($departed && !$completed) : ?>
            <?= $this->Form->postLink(
                '<i class="fas fa-flag-checkered"></i> ' . __('Programme finished'),
                ['action' => 'markCompleted', $apprentice->id],
                [
                    'class' => 'btn-flow done',
                    'escape' => false,
                    'confirm' => __('Record that {0} has completed the programme?', $apprentice->name),
                ]
            ) ?>
        <?php endif; ?>
        <?php if ($completed) : ?>
            <?= $this->Form->postLink(
                '<i class="fas fa-undo"></i> ' . __('Take back'),
                ['action' => 'undoCompleted', $apprentice->id],
                [
                    'class' => 'btn-flow undo',
                    'escape' => false,
                    'confirm' => __('Take back the completion recorded for {0}?', $apprentice->name),
                ]
            ) ?>
        <?php elseif ($departed) : ?>
            <?= $this->Form->postLink(
                '<i class="fas fa-undo"></i> ' . __('Not departed'),
                ['action' => 'undoDeparted', $apprentice->id],
                [
                    'class' => 'btn-flow undo',
                    'escape' => false,
                    'confirm' => __('Take back the departure recorded for {0}? This takes back the completion as well.', $apprentice->name),
                ]
            ) ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
