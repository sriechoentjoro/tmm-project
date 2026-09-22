<?php
/**
 * What a financial report counted, and what it left out.
 *
 * The three financial reports used to sum every journal line whatever the
 * entry's status, so a voided entry still moved the figures. They now count
 * posted entries only - which is right, but it also means the numbers moved
 * the day that changed. A figure that changed because the rule behind it
 * changed is worse than a wrong figure: nobody can tell the difference
 * without being told. So every one of them says so, on the page.
 *
 * @var \App\View\AppView $this
 * @var array $excluded [status => count] of entries not counted
 * @var string $countedStatus The status that is counted
 */
$excluded = $excluded ?? [];
$countedStatus = $countedStatus ?? 'Posted';
$total = array_sum($excluded);
?>
<div class="report-scope-note">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong><?= __('Counted: entries marked {0}.', h($countedStatus)) ?></strong>
        <?php if ($total) : ?>
            <?php
            $parts = [];
            foreach ($excluded as $status => $count) {
                $parts[] = $count . ' ' . h($status);
            }
            ?>
            <?php /* No plural agreement in the sentence: __n() is used nowhere
                     else in this application and bin/i18n-coverage.php does not
                     scan for it, so a plural form here would be a string the
                     coverage check cannot see. */ ?>
            <?= __('Left out: {0} ({1}).', $total, implode(', ', $parts)) ?>
        <?php else : ?>
            <?= __('Nothing is left out - every entry on file is posted.') ?>
        <?php endif; ?>
    </div>
</div>

<style>
.report-scope-note {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 11px 16px; margin: 0 0 16px; border-radius: 10px;
    background: #f1f8fb; border: 1px solid #d3e7f0;
    font-size: 13px; color: #47606e; line-height: 1.5;
}
.report-scope-note i { color: #2b8ab5; margin-top: 2px; }
.report-scope-note strong { color: #24505f; }
@media print { .report-scope-note { background: none; border: 0; padding: 0 0 8px; } }
</style>
