<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Menu $menu
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Menu') ?>: <?= h($menu->title) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to Menus'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $menu->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>
<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="max-width: 640px;">
        <tbody>
            <tr><th style="text-align: left; padding: 8px 12px; width: 180px;"><?= __('ID') ?></th><td style="padding: 8px 12px;"><?= h($menu->id) ?></td></tr>
            <tr><th style="text-align: left; padding: 8px 12px;"><?= __('Category') ?></th><td style="padding: 8px 12px;"><?= h($menu->category) ?></td></tr>
            <tr><th style="text-align: left; padding: 8px 12px;"><?= __('URL') ?></th><td style="padding: 8px 12px;"><code><?= h($menu->url) ?></code></td></tr>
            <tr><th style="text-align: left; padding: 8px 12px;"><?= __('Icon') ?></th><td style="padding: 8px 12px;"><i class="fas <?= h($menu->icon) ?>"></i> <code><?= h($menu->icon) ?></code></td></tr>
            <tr><th style="text-align: left; padding: 8px 12px;"><?= __('Sort Order') ?></th><td style="padding: 8px 12px;"><?= h($menu->sort_order) ?></td></tr>
            <tr><th style="text-align: left; padding: 8px 12px;"><?= __('Active') ?></th><td style="padding: 8px 12px;"><?= $menu->is_active ? __('Yes') : __('No') ?></td></tr>
        </tbody>
    </table>

    <?php if (!empty($menu->child_menus)): ?>
    <h4><?= __('Sub-menus') ?></h4>
    <ul>
        <?php foreach ($menu->child_menus as $child): ?>
        <li><?= h($child->title) ?> — <code><?= h($child->url) ?></code></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
