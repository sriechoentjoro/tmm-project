<?php
/**
 * @var \App\View\AppView $this
 * @var array $roles
 * @var int $roleId
 * @var array $grouped
 * @var array $assigned
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Role Menu Access') ?></h2>
        <?= $this->Html->link('<i class="fas fa-bars"></i> ' . __('Menu Management'), ['controller' => 'Menus', 'action' => 'index'], ['class' => 'btn-export-light', 'escape' => false]) ?>
    </div>
    <p style="color: #6c757d; margin: 8px 0 0;">
        <?= __('Tick the menus a role may see. Saving also grants the role permission to open the page each menu links to.') ?>
    </p>
</div>

<div class="card" style="padding: 16px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); background: #fff; margin-bottom: 20px; display: flex; gap: 30px; flex-wrap: wrap; align-items: flex-end;">
    <?= $this->Form->create(null, ['type' => 'get', 'url' => ['action' => 'index'], 'style' => 'margin: 0;']) ?>
    <label style="display: block;">
        <strong><?= __('Role') ?></strong><br>
        <select name="role_id" class="form-control" onchange="this.form.submit()">
            <?php foreach ($roles as $id => $name): ?>
            <option value="<?= $id ?>" <?= (int)$id === $roleId ? 'selected' : '' ?>><?= h($name) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <?= $this->Form->end() ?>

    <?= $this->Form->create(null, ['url' => ['action' => 'cloneAccess'], 'style' => 'margin: 0; display: flex; gap: 10px; align-items: flex-end;']) ?>
    <label>
        <strong><?= __('Clone access from') ?></strong><br>
        <select name="from_role_id" class="form-control">
            <?php foreach ($roles as $id => $name): ?>
            <option value="<?= $id ?>"><?= h($name) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        <strong><?= __('to') ?></strong><br>
        <select name="to_role_id" class="form-control">
            <?php foreach ($roles as $id => $name): ?>
            <option value="<?= $id ?>" <?= (int)$id === $roleId ? 'selected' : '' ?>><?= h($name) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit" class="btn btn-outline-primary"><?= __('Clone') ?></button>
    <?= $this->Form->end() ?>
</div>

<?= $this->Form->create(null, ['url' => ['action' => 'index']]) ?>
<input type="hidden" name="role_id" value="<?= $roleId ?>">
<input type="hidden" name="save_assignments" value="1">

<div style="margin-bottom: 14px; display: flex; gap: 10px; align-items: center;">
    <button type="submit" class="btn btn-primary"><?= __('Save Assignments for') ?> "<?= h(isset($roles[$roleId]) ? $roles[$roleId] : $roleId) ?>"</button>
    <a href="#" onclick="document.querySelectorAll('.menu-check').forEach(c => c.checked = true); return false;"><?= __('select all') ?></a>
    <a href="#" onclick="document.querySelectorAll('.menu-check').forEach(c => c.checked = false); return false;"><?= __('clear all') ?></a>
</div>

<?php foreach ($grouped as $category => $menus): ?>
<div class="card" style="margin-bottom: 18px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); background: #fff; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%); color: white; padding: 10px 16px; display: flex; justify-content: space-between; align-items: center;">
        <strong><?= h($category) ?></strong>
        <a href="#" style="color: white; font-size: 12px; text-decoration: underline;"
           onclick="this.closest('.card').querySelectorAll('.menu-check').forEach(c => c.checked = true); return false;"><?= __('select category') ?></a>
    </div>
    <div style="padding: 12px 16px; display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 6px;">
        <?php foreach ($menus as $menu): ?>
        <label style="display: flex; align-items: center; gap: 8px; <?= $menu->parent_id ? 'margin-left: 22px;' : 'font-weight: 600;' ?> <?= $menu->is_active ? '' : 'opacity: 0.5;' ?>">
            <input type="checkbox" class="menu-check" name="menu_ids[]" value="<?= $menu->id ?>" <?= in_array($menu->id, $assigned) ? 'checked' : '' ?>>
            <span>
                <?php if ($menu->icon): ?><i class="fas <?= h($menu->icon) ?>" style="color: #00BCD4;"></i><?php endif; ?>
                <?= h($menu->title) ?>
                <?php if (!$menu->is_active): ?><em style="font-size: 11px;">(<?= __('inactive') ?>)</em><?php endif; ?>
            </span>
        </label>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<button type="submit" class="btn btn-primary" style="margin-bottom: 30px;"><?= __('Save Assignments for') ?> "<?= h(isset($roles[$roleId]) ? $roles[$roleId] : $roleId) ?>"</button>
<?= $this->Form->end() ?>
