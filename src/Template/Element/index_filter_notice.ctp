<?php
/**
 * What the filter row is doing to the list below it.
 *
 * The boxes themselves show what was asked for, but not that the list has
 * been narrowed - and a narrowed list looks exactly like a short one. This
 * says so once, at the top, with a way back to the whole list.
 *
 * It also names any filter that named a column the table does not have. That
 * is a fault in the template, not in what anybody typed, and it used to be
 * invisible: the filter simply did nothing.
 *
 * @var \App\View\AppView $this
 * @var array $indexFilters applied and ignored, from AppController::applyIndexFilters()
 */
if (empty($indexFilters['applied']) && empty($indexFilters['ignored'])) {
    return;
}

$applied = isset($indexFilters['applied']) ? $indexFilters['applied'] : [];
$ignored = isset($indexFilters['ignored']) ? $indexFilters['ignored'] : [];

$keep = [];
foreach ($this->request->getQueryParams() as $key => $value) {
    if (strpos($key, 'filter_') !== 0 && $key !== 'page' && !is_array($value)) {
        $keep[$key] = $value;
    }
}
?>
<?php if ($applied): ?>
<div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center index-filter-notice">
    <i class="fas fa-filter mr-2"></i>
    <span>
        <?= __('This list is narrowed by what is in the filter row.') ?>
        <?php
        $named = [];
        foreach ($applied as $field => $value) {
            $named[] = '<strong>' . h(ucfirst(str_replace('_', ' ', $field))) . '</strong>: '
                . h($value);
        }
        ?>
        <?= implode(' &middot; ', $named) ?>
    </span>
    <span class="ml-auto">
        <?= $this->Html->link(
            '<i class="fas fa-times"></i> ' . __('Show the whole list'),
            ['?' => $keep],
            ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]
        ) ?>
    </span>
</div>
<?php endif; ?>
<?php if ($ignored): ?>
<div class="alert alert-warning py-2 px-3 mb-2 index-filter-notice">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <?= __('These filters were not applied, because this table has no such column: {0}',
        h(implode(', ', $ignored))) ?>
</div>
<?php endif; ?>
