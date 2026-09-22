<?php
/**
 * The decision trail.
 *
 * Twelve moments in the pipeline write a line here: putting a candidate
 * forward and taking it back, the two promotions, recording a departure or a
 * completed programme and taking either back, setting an owing cost, and the
 * three permission changes. Everything else is deliberately absent - a trail
 * of every column change would bury the decisions this exists to surface.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Audit[]|\Cake\Collection\CollectionInterface $entries
 * @var array $actions Distinct action names, for the filter.
 * @var array $summary
 * @var bool $ready Whether the table has been prepared.
 * @var string $filterAction
 * @var array $map Canonical field name => the column it lives in here.
 */
$logs = \Cake\ORM\TableRegistry::getTableLocator()->get('Audit');
$map = $map ?? [];

// The table names most of these differently: the subject is `model` and
// `foreign_key`, the free text is `description`. Reading through the map
// keeps the page working on either shape.
$f = function ($entry, $canonical) use ($logs, $map) {
    return $logs->field($entry, $canonical, $map);
};
$asDateTime = function ($value) {
    if ($value instanceof \Cake\I18n\FrozenTime || $value instanceof \Cake\I18n\Time) {
        return $value->i18nFormat('d MMM yyyy HH:mm');
    }

    return (string)$value;
};

// A dotted action name reads better split: candidate.propose -> Candidate /
// propose. Nothing is translated here on purpose - these are stored values,
// and inventing a translation for one would hide which value was recorded.
$actionParts = function ($action) {
    $bits = explode('.', (string)$action, 2);

    return count($bits) === 2 ? $bits : ['', (string)$action];
};
?>
<?= $this->Html->css('pretty-form.css') ?>

<style>
.trail-head {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    padding: 20px 24px; margin-bottom: 18px; border-radius: 14px;
    background: linear-gradient(135deg, #455a64 0%, #263238 100%); color: #fff;
}
.trail-head i { font-size: 26px; opacity: .9; }
.trail-head h1 { margin: 0; font-size: 21px; font-weight: 700; }
.trail-head p { margin: 3px 0 0; font-size: 13px; color: rgba(255,255,255,.8); max-width: 72ch; }
.trail-stats { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
.trail-stat { flex: 1 1 150px; background: #fff; border: 1px solid #e6ecf1; border-radius: 12px; padding: 14px 18px; }
.trail-stat .n { font-size: 24px; font-weight: 700; color: #2c3e50; line-height: 1.1; }
.trail-stat .l { font-size: 12px; color: #6b7c8d; margin-top: 3px; }
.trail-note {
    background: #fff8e1; border: 1px solid #ffe0a3; border-radius: 12px;
    padding: 14px 18px; margin-bottom: 18px; font-size: 13.5px; color: #6b5320;
}
.trail-filter { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
.trail-filter a {
    padding: 5px 13px; border-radius: 20px; font-size: 12.5px; font-weight: 600;
    text-decoration: none !important; border: 1px solid #d8e1e8; color: #55677a; background: #fff;
}
.trail-filter a.on { background: #455a64; border-color: #455a64; color: #fff; }
.trail-table { width: 100%; background: #fff; border-collapse: collapse; border-radius: 12px; overflow: hidden; }
.trail-table th {
    text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .6px;
    color: #55677a; background: #f6f9fb; padding: 11px 14px; border-bottom: 2px solid #e6ecf1;
}
.trail-table td { padding: 10px 14px; border-bottom: 1px solid #eef3f6; font-size: 13.5px; vertical-align: top; }
.trail-table tr:last-child td { border-bottom: 0; }
.trail-when { white-space: nowrap; color: #6b7c8d; }
.trail-who strong { color: #2c3e50; }
.trail-who small { display: block; color: #8496a6; font-size: 11.5px; }
.trail-act { display: inline-block; padding: 2px 9px; border-radius: 6px; background: #eceff1; color: #37474f; font-size: 12px; font-weight: 600; white-space: nowrap; }
.trail-act.undo { background: #fdecea; color: #b3261e; }
.trail-subject small { display: block; color: #8496a6; font-size: 11.5px; }
.trail-detail { color: #6b7c8d; font-size: 12px; word-break: break-word; max-width: 34ch; }
.trail-empty { background: #fff; border: 1px solid #e6ecf1; border-radius: 12px; padding: 40px; text-align: center; color: #7b8a99; }
</style>

<div class="trail-head">
    <i class="fas fa-clipboard-list"></i>
    <div>
        <h1><?= __('Decision Trail') ?></h1>
        <p><?= __('Who made each call, when, and about whom. Twelve moments in the pipeline write a line here; ordinary edits do not, so what is left is the decisions somebody may later be asked about.') ?></p>
    </div>
</div>

<?php if (!$ready) : ?>
    <div class="trail-note">
        <strong><?= __('The trail is not recording yet.') ?></strong>
        <?= __('The table it writes to has not been prepared on this installation, so decisions made until then leave no line. An administrator should run {0}.', '<code>bin/cake add_audit_log --apply</code>') ?>
    </div>
<?php endif; ?>

<div class="trail-stats">
    <div class="trail-stat">
        <div class="n"><?= (int)$summary['total'] ?></div>
        <div class="l"><?= __('Decisions recorded') ?></div>
    </div>
    <div class="trail-stat">
        <div class="n"><?= (int)$summary['people'] ?></div>
        <div class="l"><?= __('People who made them') ?></div>
    </div>
    <div class="trail-stat">
        <div class="n"><?= (int)$summary['today'] ?></div>
        <div class="l"><?= __('Today') ?></div>
    </div>
</div>

<?php if ($actions) : ?>
    <div class="trail-filter">
        <?= $this->Html->link(__('All'), ['action' => 'trail'],
            ['class' => $filterAction === '' ? 'on' : '']) ?>
        <?php foreach ($actions as $action) : ?>
            <?= $this->Html->link(h($action), ['action' => 'trail', '?' => ['action' => $action]],
                ['class' => $filterAction === $action ? 'on' : '']) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!count($entries)) : ?>
    <div class="trail-empty">
        <p><?= $ready
            ? __('Nothing recorded yet. The trail starts from the next decision somebody makes.')
            : __('Nothing can be recorded until the table is prepared.') ?></p>
    </div>
<?php else : ?>
<table class="trail-table">
    <thead>
        <tr>
            <th><?= __('When') ?></th>
            <th><?= __('Who') ?></th>
            <th><?= __('Decision') ?></th>
            <th><?= __('About') ?></th>
            <th><?= __('Detail') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($entries as $entry) : ?>
            <?php
            list($area, $what) = $actionParts($f($entry, 'action'));
            $isUndo = stripos((string)$what, 'undo') !== false || stripos((string)$what, 'withdraw') !== false;
            ?>
            <tr>
                <td class="trail-when"><?= h($asDateTime($f($entry, 'created'))) ?></td>
                <td class="trail-who">
                    <strong><?= h($f($entry, 'username') ?: __('unknown')) ?></strong>
                    <?php if ($f($entry, 'role_names')) : ?>
                        <small><?= h($f($entry, 'role_names')) ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="trail-act<?= $isUndo ? ' undo' : '' ?>"><?= h($f($entry, 'action')) ?></span>
                </td>
                <td class="trail-subject">
                    <?= h($f($entry, 'subject_label') ?: '-') ?>
                    <?php if ($f($entry, 'subject_type')) : ?>
                        <small><?= h($f($entry, 'subject_type')) ?> #<?= h($f($entry, 'subject_id')) ?></small>
                    <?php endif; ?>
                </td>
                <td class="trail-detail"><?= h($f($entry, 'detail')) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="paginator" style="margin-top:16px;">
    <ul class="pagination">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} of {{count}} total')]) ?></p>
</div>
<?php endif; ?>

<?= $this->element('process_flow_help') ?>
