<?php
/**
 * Where a candidate stands in selection, and the institution's one decision.
 *
 * The hand-over from an institution to recruitment had no screen. The promotion
 * list asked for a flag that only promotion itself set, so it was always empty
 * and nobody could see why. This panel is the missing half: the institution
 * says the candidate has passed its selection, and from that moment recruitment
 * can see them. Whether the candidate actually becomes a trainee stays
 * recruitment's decision.
 *
 * It renders nothing at all where the columns are absent - an installation that
 * has not run bin/cake add_selection_flow_columns should show no button rather
 * than a button that silently does nothing.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 * @var bool $canPropose Whether the reader may put this candidate forward.
 */

$candidates = \Cake\ORM\TableRegistry::getTableLocator()->get('Candidates');
if (!$candidates->getSchema()->hasColumn('lpk_proposed_at')) {
    return;
}

$proposedAt = $candidate->get('lpk_proposed_at');
$mcu = $candidates->getSchema()->hasColumn('mcu_result') ? $candidate->get('mcu_result') : null;
$canPropose = $canPropose ?? false;
?>
<?php $this->append('css'); ?>
<style>
.selection-panel {
    display: flex; flex-wrap: wrap; align-items: center; gap: 14px;
    padding: 14px 18px; margin: 0 0 18px; border-radius: 12px;
    background: #fff; border: 1px solid #e6ecf1; border-left: 5px solid #00BCD4;
}
.selection-panel .selection-state { font-size: 14px; color: #46586b; flex: 1 1 320px; line-height: 1.5; }
.selection-panel .selection-state strong { color: #2c3e50; }
.selection-chip {
    display: inline-block; padding: 3px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700; white-space: nowrap;
}
.selection-chip.on { background: #e6f7ea; color: #1e7e42; }
.selection-chip.off { background: #f1f5f8; color: #66788a; }
.selection-chip.bad { background: #fdecea; color: #b3261e; }
.selection-panel .btn-selection {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 18px; border: 0; border-radius: 9px;
    font-size: 14px; font-weight: 600; color: #fff !important;
    text-decoration: none !important; cursor: pointer;
    background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
}
.selection-panel .btn-selection.withdraw {
    background: #fff; color: #55677a !important; border: 1px solid #d8e1e8;
}
</style>
<?php $this->end(); ?>

<div class="selection-panel">
    <div class="selection-state">
        <?php if ($mcu === 'fail') : ?>
            <span class="selection-chip bad"><?= __('Not medically fit') ?></span>
            <?= __('A check-up recorded for this candidate is marked not fit, so they cannot be put forward until that changes.') ?>
        <?php elseif ($proposedAt) : ?>
            <span class="selection-chip on"><?= __('Put forward') ?></span>
            <?= __('Declared through selection on {0}. Recruitment can see this candidate on its promotion list.',
                $proposedAt instanceof \Cake\I18n\FrozenTime ? $proposedAt->i18nFormat('d MMM yyyy') : (string)$proposedAt) ?>
        <?php else : ?>
            <span class="selection-chip off"><?= __('Not put forward') ?></span>
            <?= __('Recruitment does not see this candidate yet. Declare them through selection when the tests, the interview and the documents are done.') ?>
        <?php endif; ?>
    </div>

    <?php if ($canPropose && $mcu !== 'fail') : ?>
        <?php if ($proposedAt) : ?>
            <?= $this->Form->postLink(
                '<i class="fas fa-rotate-left"></i> ' . __('Take back'),
                ['action' => 'withdrawProposal', $candidate->id],
                [
                    'class' => 'btn-selection withdraw',
                    'escape' => false,
                    'confirm' => __('Take {0} back off the promotion list?', $candidate->name),
                ]
            ) ?>
        <?php else : ?>
            <?= $this->Form->postLink(
                '<i class="fas fa-paper-plane"></i> ' . __('Declare passed selection'),
                ['action' => 'propose', $candidate->id],
                [
                    'class' => 'btn-selection',
                    'escape' => false,
                    'confirm' => __('Put {0} forward to recruitment?', $candidate->name),
                ]
            ) ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
