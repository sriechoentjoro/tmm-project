<?php
/**
 * What TMM Training needs in front of them to make the two calls.
 *
 * Training hears three things: its own test results, summarised in the
 * certificate; whether TMM Documentation has the departure documents; and
 * what TMM Documentation's medical check-up came to. All three are shown
 * beside each apprentice. None of them decides anything - the screen exists so
 * the call is made on evidence rather than on memory.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Apprentice[] $apprentices
 * @var array $evidence Keyed by apprentice id.
 * @var array $summary
 * @var bool $ready Whether the flow columns exist yet.
 */
?>
<?= $this->Html->css('pretty-form.css') ?>

<style>
.dep-head {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    padding: 20px 24px; margin-bottom: 18px; border-radius: 14px;
    background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%); color: #fff;
}
.dep-head i { font-size: 26px; opacity: .9; }
.dep-head h1 { margin: 0; font-size: 21px; font-weight: 700; }
.dep-head p { margin: 3px 0 0; font-size: 13px; color: rgba(255,255,255,.82); max-width: 70ch; }
.dep-stats { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
.dep-stat {
    flex: 1 1 150px; background: #fff; border: 1px solid #e6ecf1;
    border-radius: 12px; padding: 14px 18px;
}
.dep-stat .n { font-size: 24px; font-weight: 700; color: #2c3e50; line-height: 1.1; }
.dep-stat .l { font-size: 12px; color: #6b7c8d; margin-top: 3px; }
.dep-stat.bad .n { color: #b3261e; }
.dep-note {
    background: #fff8e1; border: 1px solid #ffe0a3; border-radius: 12px;
    padding: 14px 18px; margin-bottom: 18px; font-size: 13.5px; color: #6b5320;
}
.dep-table { width: 100%; background: #fff; border-collapse: collapse; border-radius: 12px; overflow: hidden; }
.dep-table th {
    text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .6px;
    color: #55677a; background: #f6f9fb; padding: 11px 14px; border-bottom: 2px solid #e6ecf1;
}
.dep-table td { padding: 11px 14px; border-bottom: 1px solid #eef3f6; font-size: 13.5px; vertical-align: middle; }
.dep-table tr:last-child td { border-bottom: 0; }
.dep-name { font-weight: 600; color: #2c3e50; }
.dep-name small { display: block; font-weight: 400; color: #8496a6; font-size: 12px; }
.dep-chip {
    display: inline-block; padding: 3px 11px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700; white-space: nowrap;
}
.dep-chip.on { background: #e6f7ea; color: #1e7e42; }
.dep-chip.off { background: #f1f5f8; color: #66788a; }
.dep-chip.bad { background: #fdecea; color: #b3261e; }
.dep-chip.wait { background: #fff4e0; color: #96650d; }
.dep-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.btn-dep {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 14px; border: 0; border-radius: 8px;
    font-size: 13px; font-weight: 600; color: #fff !important;
    text-decoration: none !important; cursor: pointer;
    background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
}
.btn-dep.done { background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%); }
.btn-dep.undo { background: #fff; color: #55677a !important; border: 1px solid #d8e1e8; }
.dep-empty { background: #fff; border: 1px solid #e6ecf1; border-radius: 12px; padding: 40px; text-align: center; color: #7b8a99; }
</style>

<div class="dep-head">
    <i class="fas fa-plane-departure"></i>
    <div>
        <h1><?= __('Departure and Completion') ?></h1>
        <p><?= __('TMM Training makes both calls. The certificate is training\'s own evidence; the documents and the medical check-up come from TMM Documentation. Nothing here decides for you - a result marked not fit is the only thing that refuses a departure outright.') ?></p>
    </div>
</div>

<?php if (!$ready) : ?>
    <div class="dep-note">
        <strong><?= __('This installation is not ready yet.') ?></strong>
        <?= __('The columns that record who made a call and when are missing, so the buttons below would save less than they appear to. An administrator should run {0} first.', '<code>bin/cake add_apprentice_flow_columns --apply</code>') ?>
    </div>
<?php endif; ?>

<div class="dep-stats">
    <div class="dep-stat">
        <div class="n"><?= (int)$summary['total'] ?></div>
        <div class="l"><?= __('Apprentices on file') ?></div>
    </div>
    <div class="dep-stat">
        <div class="n"><?= (int)$summary['departed'] ?></div>
        <div class="l"><?= __('Recorded as in Japan') ?></div>
    </div>
    <div class="dep-stat">
        <div class="n"><?= (int)$summary['completed'] ?></div>
        <div class="l"><?= __('Recorded as completed') ?></div>
    </div>
    <div class="dep-stat<?= $summary['mcu_fail'] ? ' bad' : '' ?>">
        <div class="n"><?= (int)$summary['mcu_fail'] ?></div>
        <div class="l"><?= __('Marked not medically fit') ?></div>
    </div>
</div>

<?php if (!$apprentices) : ?>
    <div class="dep-empty">
        <p><?= __('No apprentices yet. They arrive by being promoted from a trainee.') ?></p>
        <?= $this->Html->link(__('Go to Trainees'), ['controller' => 'Trainees', 'action' => 'index'], ['class' => 'btn-dep']) ?>
    </div>
<?php else : ?>
<table class="dep-table">
    <thead>
        <tr>
            <th><?= __('Apprentice') ?></th>
            <th><?= __('Certificate') ?></th>
            <th><?= __('Documents') ?></th>
            <th><?= __('Medical') ?></th>
            <th><?= __('State') ?></th>
            <th><?= __('Call') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($apprentices as $apprentice) : ?>
            <?php
            $e = $evidence[$apprentice->id] ?? [];
            $departed = (bool)$apprentice->is_apprenticeship_pass;
            $completed = (bool)$apprentice->get('is_apprentice_pass');
            $mcu = $e['mcu'] ?? null;
            $accepted = (int)($e['documents_accepted'] ?? 0);
            $required = (int)($e['documents_required'] ?? 0);
            ?>
            <tr>
                <td>
                    <span class="dep-name">
                        <?= $this->Html->link(h($apprentice->name), ['action' => 'view', $apprentice->id]) ?>
                        <small><?= h($apprentice->tmm_code) ?></small>
                    </span>
                </td>
                <td>
                    <?php if (!empty($e['certificate'])) : ?>
                        <span class="dep-chip on"><?= h($e['certificate']) ?></span>
                    <?php else : ?>
                        <span class="dep-chip off"><?= __('none issued') ?></span>
                    <?php endif; ?>
                    <?php if ($e['score_average'] !== null) : ?>
                        <small style="color:#7b8a99;"><?= __('avg {0}', $e['score_average']) ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($required && $accepted >= $required) : ?>
                        <span class="dep-chip on"><?= $accepted ?> / <?= $required ?></span>
                    <?php elseif ($required) : ?>
                        <span class="dep-chip wait"><?= $accepted ?> / <?= $required ?></span>
                    <?php else : ?>
                        <span class="dep-chip off"><?= __('no required list') ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($mcu === 'fail') : ?>
                        <span class="dep-chip bad"><?= __('not fit') ?></span>
                    <?php elseif ($mcu === 'pass') : ?>
                        <span class="dep-chip on"><?= __('fit') ?></span>
                    <?php else : ?>
                        <span class="dep-chip off"><?= __('nobody said') ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($completed) : ?>
                        <span class="dep-chip on"><?= __('completed') ?></span>
                    <?php elseif ($departed) : ?>
                        <span class="dep-chip on"><?= __('in Japan') ?></span>
                    <?php else : ?>
                        <span class="dep-chip off"><?= __('not departed') ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="dep-actions">
                        <?php if (!$departed) : ?>
                            <?php if ($mcu === 'fail') : ?>
                                <span class="dep-chip bad"><?= __('blocked by the check-up') ?></span>
                            <?php else : ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-plane-departure"></i> ' . __('Left for Japan'),
                                    ['action' => 'markDeparted', $apprentice->id],
                                    [
                                        'class' => 'btn-dep',
                                        'escape' => false,
                                        'confirm' => __('Record that {0} has left for Japan?', $apprentice->name),
                                    ]
                                ) ?>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php if (!$completed) : ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-flag-checkered"></i> ' . __('Programme finished'),
                                    ['action' => 'markCompleted', $apprentice->id],
                                    [
                                        'class' => 'btn-dep done',
                                        'escape' => false,
                                        'confirm' => __('Record that {0} has completed the programme?', $apprentice->name),
                                    ]
                                ) ?>
                            <?php else : ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-undo"></i> ' . __('Take back') ,
                                    ['action' => 'undoCompleted', $apprentice->id],
                                    [
                                        'class' => 'btn-dep undo',
                                        'escape' => false,
                                        'confirm' => __('Take back the completion recorded for {0}?', $apprentice->name),
                                    ]
                                ) ?>
                            <?php endif; ?>
                            <?= $this->Form->postLink(
                                '<i class="fas fa-undo"></i> ' . __('Not departed'),
                                ['action' => 'undoDeparted', $apprentice->id],
                                [
                                    'class' => 'btn-dep undo',
                                    'escape' => false,
                                    'confirm' => __('Take back the departure recorded for {0}? This takes back the completion as well.', $apprentice->name),
                                ]
                            ) ?>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?= $this->element('process_flow_help') ?>
