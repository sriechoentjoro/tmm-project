<?php
/**
 * Reusable Status Guide panel for forms.
 *
 * Drop into any form with:
 *   <?= $this->element('status_guide') ?>                     // default stakeholder statuses
 *   <?= $this->element('status_guide', ['statuses' => [...]]) // custom set
 *
 * Options (all optional):
 * @var string $title    Panel heading (default "Status Guide")
 * @var array  $statuses List of ['label','class','description']
 *                       class ∈ st-active|st-suspended|st-inactive|st-pending|st-info|st-danger
 * @var string $tip      A helper line shown under the list
 * @var string $icon     Heading icon (default fa-info-circle)
 */
$title = isset($title) ? $title : __('Status Guide');
$icon = isset($icon) ? $icon : 'fa-info-circle';
$statuses = isset($statuses) ? $statuses : [
    ['label' => __('Active'), 'class' => 'st-active', 'description' => __('Operational and available for use')],
    ['label' => __('Suspended'), 'class' => 'st-suspended', 'description' => __('Temporarily inactive - under review')],
    ['label' => __('Inactive'), 'class' => 'st-inactive', 'description' => __('No longer operational')],
];
$tip = isset($tip) ? $tip : null;
?>
<div class="status-guide-panel">
    <div class="sgp-head"><i class="fa <?= h($icon) ?>"></i> <?= h($title) ?></div>
    <ul class="sgp-list">
        <?php foreach ($statuses as $s): ?>
            <li>
                <span class="sgp-badge <?= h(isset($s['class']) ? $s['class'] : 'st-info') ?>"><?= h($s['label']) ?></span>
                <span class="sgp-desc"><?= h($s['description']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if ($tip): ?>
        <p class="sgp-tip"><i class="fa fa-lightbulb-o"></i> <?= h($tip) ?></p>
    <?php endif; ?>
</div>

<style>
.status-guide-panel {
    background: #fafbff;
    border: 1px solid #e4e8fb;
    border-left: 4px solid #667eea;
    border-radius: 10px;
    padding: 14px 18px;
    margin: 12px 0 18px;
}
.status-guide-panel .sgp-head {
    font-weight: bold;
    color: #3b4890;
    margin-bottom: 10px;
    font-size: 0.95em;
}
.status-guide-panel .sgp-head i { margin-right: 6px; }
.status-guide-panel .sgp-list { list-style: none; padding: 0; margin: 0; }
.status-guide-panel .sgp-list li {
    display: flex;
    align-items: baseline;
    gap: 10px;
    padding: 5px 0;
    flex-wrap: wrap;
}
.status-guide-panel .sgp-badge {
    flex: 0 0 auto;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    white-space: nowrap;
}
.status-guide-panel .sgp-desc { color: #546e7a; font-size: 0.9em; }
.status-guide-panel .sgp-tip {
    margin: 10px 0 0;
    font-size: 0.85em;
    color: #78909c;
}
.status-guide-panel .st-active { background: #e6f9ee; color: #1e7e42; }
.status-guide-panel .st-suspended { background: #fff8e1; color: #856404; }
.status-guide-panel .st-inactive { background: #fdeaea; color: #c0392b; }
.status-guide-panel .st-pending { background: #fff8e1; color: #856404; }
.status-guide-panel .st-info { background: #e8f4fd; color: #2b7fd4; }
.status-guide-panel .st-danger { background: #fdeaea; color: #c0392b; }
</style>
