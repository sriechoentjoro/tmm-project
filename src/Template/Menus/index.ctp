<?php
/**
 * @var \App\View\AppView $this
 * @var array $grouped
 * @var array $parents
 * @var array $categories
 * @var array $roles
 * @var array $roleMatrix  [menu_id][role_id] = true
 */

// Short labels for role column headers
$roleShort = [
    'administrator'    => 'Admin',
    'management'       => 'Mgmt',
    'tmm-recruitment'  => 'Recruit',
    'tmm-training'     => 'Train',
    'tmm-documentation'=> 'Docs',
    'lpk-penyangga'    => 'LPK',
    'tmm-accounting'   => 'Acctg',
];
$roleColors = [
    'administrator'    => '#e53935',
    'management'       => '#8e24aa',
    'tmm-recruitment'  => '#1e88e5',
    'tmm-training'     => '#43a047',
    'tmm-documentation'=> '#fb8c00',
    'lpk-penyangga'    => '#00acc1',
    'tmm-accounting'   => '#6d4c41',
];
?>
<style>
/* ── Per-menu tbody grouping ── */
tbody.menu-group { border-bottom: 3px solid #e0e0e0; }
tbody.menu-group:last-child { border-bottom: none; }

tbody.menu-group tr.menu-data-row td {
    background: #fff;
    border-bottom: none;
    padding-top: 9px;
    padding-bottom: 5px;
}
tbody.menu-group tr.menu-role-row td {
    background: #f0f4ff;
    border-bottom: none;
    padding-top: 5px;
    padding-bottom: 9px;
}
/* Left accent bar shared by both rows */
tbody.menu-group tr td:first-child {
    border-left: 4px solid #b0bec5;
}
tbody.menu-group:hover tr td:first-child {
    border-left-color: #00BCD4;
}
tbody.menu-group:hover tr.menu-data-row td { background: #f5feff; }
tbody.menu-group:hover tr.menu-role-row td { background: #e8f4ff; }

/* ── Role toggle dots ── */
.role-toggle {
    display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 26px; border-radius: 50%; border: 2px solid;
    cursor: pointer; font-size: 13px; transition: all .15s; background: #fff;
    flex-shrink: 0;
}
.role-toggle.on  { color: #fff; }
.role-toggle.off { background: #fff; opacity: .3; }
.role-toggle:hover { opacity: 1 !important; transform: scale(1.15); }

/* ── Scope badges ── */
.ga-badge {
    font-size: 9px; border-radius: 3px; padding: 1px 4px;
    white-space: nowrap; cursor: pointer; font-weight: 600;
}
.ga-badge.all  { background:#e8f5e9; color:#2e7d32; }
.ga-badge.some { background:#fff8e1; color:#e65100; }

/* ── Granted-actions popover ── */
.ga-popover {
    display: none; position: fixed; z-index: 9999;
    background: #fff; border: 1px solid #c5cae9; border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,.15); padding: 10px 12px; min-width: 240px;
}
.ga-popover input { width: 100%; padding: 5px 8px; border: 1px solid #c5cae9; border-radius: 5px; font-size: 12px; margin: 6px 0; box-sizing: border-box; }
.ga-popover .ga-btns { display: flex; gap: 6px; margin-top: 6px; }
.ga-popover button { flex: 1; padding: 4px; border-radius: 5px; border: none; cursor: pointer; font-size: 11px; }
.ga-popover .btn-all  { background:#e8f5e9; color:#2e7d32; }
.ga-popover .btn-save { background:#3f51b5; color:#fff; }
</style>

<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Menu Management') ?></h2>
        <div style="display: flex; gap: 10px;">
            <?= $this->Html->link('<i class="fas fa-user-lock"></i> ' . __('Role Access'), ['controller' => 'Permissions', 'action' => 'index'], ['class' => 'btn-export-light', 'escape' => false]) ?>
            <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('Add Menu'), ['action' => 'add'], ['class' => 'btn-export-light', 'escape' => false]) ?>
        </div>
    </div>
    <p style="color: #6c757d; margin: 8px 0 0;"><?= __('Toggle activates/deactivates a link, Move changes its category (children follow). Click a role dot to grant/revoke access instantly.') ?></p>
</div>

<?php foreach ($grouped as $category => $menus): ?>
<div class="card" style="margin-bottom: 22px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); background: #fff; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%); color: white; padding: 12px 16px;">
        <strong><?= h($category) ?></strong>
        <span style="opacity: 0.85;">(<?= count($menus) ?>)</span>
    </div>
    <div class="table-scroll-wrapper" style="overflow-x: auto;">
    <table class="table" style="border-collapse: collapse; width: 100%;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 10px 12px; text-align: left;"><?= __('Actions') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('ID') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('Category') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('Title') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('Parent') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('URL / Controller::Action') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('Order') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('Active') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('Move to Category') ?></th>
                <th style="padding: 10px 12px; text-align: left;"><?= __('Move Parent') ?></th>
            </tr>
        </thead>
        <?php foreach ($menus as $menu): ?>
        <tbody class="menu-group" style="<?= $menu->is_active ? '' : 'opacity:0.5;' ?>">
            <!-- Main data row -->
            <tr class="menu-data-row">
                <td style="padding: 6px 10px; white-space: nowrap;">
                    <?= $this->Html->link(
                        '<i class="fas fa-edit"></i>',
                        ['action' => 'edit', $menu->id],
                        ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false, 'title' => __('Edit')]
                    ) ?>
                    <?= $this->Form->postLink(
                        $menu->is_active
                            ? '<i class="fas fa-toggle-on"></i>'
                            : '<i class="fas fa-toggle-off"></i>',
                        ['action' => 'toggle', $menu->id],
                        [
                            'class'  => 'btn btn-sm ' . ($menu->is_active ? 'btn-outline-warning' : 'btn-outline-success'),
                            'escape' => false,
                            'title'  => $menu->is_active ? __('Deactivate') : __('Activate'),
                        ]
                    ) ?>
                    <?= $this->Form->postLink(
                        '<i class="fas fa-trash-alt"></i>',
                        ['action' => 'delete', $menu->id],
                        [
                            'class'   => 'btn btn-sm btn-outline-danger',
                            'escape'  => false,
                            'title'   => __('Delete'),
                            'confirm' => __('Delete menu "{0}"?', $menu->title),
                        ]
                    ) ?>
                </td>
                <td style="padding: 8px 12px;"><?= h($menu->id) ?></td>
                <td style="padding: 8px 12px; font-size: 12px; white-space: nowrap;">
                    <span style="background:#e3f2fd;color:#1565c0;border-radius:4px;padding:2px 7px;font-weight:500"><?= h($menu->category) ?></span>
                </td>
                <td style="padding: 8px 12px; <?= $menu->parent_id ? 'padding-left: 28px;' : 'font-weight: 600;' ?>">
                    <?php if ($menu->icon): ?><i class="fas <?= h($menu->icon) ?>" style="color: #00BCD4; margin-right: 6px;"></i><?php endif; ?>
                    <?= h($menu->title) ?>
                </td>
                <td style="padding: 8px 12px;"><?= $menu->parent_id ? h($parents[$menu->parent_id] ?? $menu->parent_id) : '—' ?></td>
                <td style="padding: 8px 12px;">
                    <code style="font-size: 12px;"><?= h($menu->url) ?></code>
                    <?php if ($menu->controller): ?>
                    <br><span style="font-size:11px;color:#5c6bc0;font-family:monospace"><?= h($menu->controller) ?>::<?= h($menu->action) ?></span>
                    <?php endif; ?>
                </td>
                <td style="padding: 8px 12px;"><?= h($menu->sort_order) ?></td>
                <td style="padding: 8px 12px;"><?= $menu->is_active ? '<span style="color:#28a745;">&#10004;</span>' : '<span style="color:#dc3545;">&#10008;</span>' ?></td>
                <td style="padding: 8px 12px;">
                    <?= $this->Form->create(null, ['url' => ['action' => 'moveCategory', $menu->id], 'style' => 'display:flex;gap:4px;margin:0;']) ?>
                    <select name="category" class="form-control form-control-sm" style="font-size:12px;max-width:160px;">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= h($cat) ?>" <?= $cat === $menu->category ? 'selected' : '' ?>><?= h($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-size:12px;"><?= __('Move') ?></button>
                    <?= $this->Form->end() ?>
                </td>
                <td style="padding: 8px 12px;">
                    <?= $this->Form->create(null, ['url' => ['action' => 'moveParent', $menu->id], 'style' => 'display:flex;gap:4px;margin:0;']) ?>
                    <select name="parent_id" class="form-control form-control-sm" style="font-size:12px;max-width:160px;">
                        <option value=""><?= __('(top level)') ?></option>
                        <?php foreach ($parentOptions as $pid => $ptitle): ?>
                        <?php if ($pid === $menu->id) continue; ?>
                        <option value="<?= $pid ?>" <?= (int)$menu->parent_id === $pid ? 'selected' : '' ?>><?= h($ptitle) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-size:12px;"><?= __('Move') ?></button>
                    <?= $this->Form->end() ?>
                </td>
            </tr>
            <!-- Role access sub-row -->
            <tr class="menu-role-row">
                <td colspan="10" style="padding:4px 12px 8px 20px;">
                    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;">
                        <span style="font-size:10px;color:#9e9e9e;white-space:nowrap;font-weight:600;text-transform:uppercase;letter-spacing:.5px"><?= __('Role Access') ?>:</span>
                        <?php foreach ($roles as $role):
                            $cell   = $roleMatrix[$menu->id][$role->id] ?? null;
                            $active = $cell && $cell['active'];
                            $ga     = $active ? ($cell['granted_actions'] ?? '*') : '';
                            $color  = $roleColors[$role->name] ?? '#607d8b';
                            $label  = $roleShort[$role->name]  ?? $role->name;
                            $isAll  = ($ga === '*' || $ga === '');
                        ?>
                        <div style="display:inline-flex;align-items:center;gap:5px;position:relative;">
                            <button type="button"
                                class="role-toggle <?= $active ? 'on' : 'off' ?>"
                                style="border-color:<?= h($color) ?>;<?= $active ? 'background:' . h($color) . ';' : '' ?>"
                                data-menu="<?= $menu->id ?>"
                                data-role="<?= $role->id ?>"
                                data-color="<?= h($color) ?>"
                                title="<?= h($label . ($active ? ' — click to revoke' : ' — click to grant')) ?>">
                                <?= $active ? '&#10003;' : '&bull;' ?>
                            </button>
                            <span style="font-size:11px;color:<?= h($color) ?>;font-weight:600;white-space:nowrap"><?= h($label) ?></span>
                            <?php if ($active): ?>
                            <span class="ga-badge <?= $isAll ? 'all' : 'some' ?>"
                                  data-menu="<?= $menu->id ?>" data-role="<?= $role->id ?>"
                                  title="<?= h('Granted: ' . $ga) ?>">
                                <?= $isAll ? 'all' : 'custom' ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
        </tbody>
        <?php endforeach; ?>
    </table>
    </div>
</div>
<?php endforeach; ?>

<div class="ga-popover" id="gaPopover">
    <div style="font-size:12px;font-weight:600;color:#3d3d6b;margin-bottom:4px"><?= __('Granted Actions') ?></div>
    <div style="font-size:11px;color:#888;margin-bottom:4px"><?= __('Use * for all actions, or comma-separated list: index,view,add,edit,delete') ?></div>
    <input type="text" id="gaInput" placeholder="* or index,view,add,edit,delete">
    <div class="ga-btns">
        <button class="btn-all" onclick="document.getElementById('gaInput').value='*'"><?= __('Set All (*)') ?></button>
        <button class="btn-save" id="gaSaveBtn"><?= __('Save') ?></button>
    </div>
</div>

<script>
(function () {
    var CSRF = (document.querySelector('meta[name="csrfToken"]') || {}).content
            || ((document.querySelector('input[name="_csrfToken"]') || {}).value || '');

    function postJson(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': CSRF,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: body ? JSON.stringify(body) : null
        }).then(function (r) { return r.json(); });
    }

    /* ── Toggle on/off ── */
    document.querySelectorAll('.role-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var menuId = btn.dataset.menu;
            var roleId = btn.dataset.role;
            var color  = btn.dataset.color;
            var td     = btn.closest('td');

            btn.disabled = true;
            btn.style.opacity = '0.5';

            postJson('/menus/toggle-role-access/' + menuId + '/' + roleId)
            .then(function (data) {
                btn.disabled = false;
                btn.style.opacity = '';

                // Remove old badge
                var old = td.querySelector('.ga-badge');
                if (old) old.remove();

                if (data.active) {
                    btn.classList.replace('off', 'on');
                    btn.style.background  = color;
                    btn.style.borderColor = color;
                    btn.innerHTML = '&#10003;';

                    var badge = document.createElement('span');
                    var ga    = data.granted_actions || '*';
                    badge.className   = 'ga-badge ' + (ga === '*' ? 'all' : 'some');
                    badge.dataset.menu = menuId;
                    badge.dataset.role = roleId;
                    badge.title   = 'Granted: ' + ga;
                    badge.textContent = (ga === '*') ? 'all' : 'custom';
                    td.appendChild(badge);
                    attachBadgeClick(badge);
                } else {
                    btn.classList.replace('on', 'off');
                    btn.style.background  = '#fff';
                    btn.style.borderColor = color;
                    btn.innerHTML = '&bull;';
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.style.opacity = '';
                alert('Toggle failed. Please try again.');
            });
        });
    });

    /* ── Scope badge popover ── */
    var popover   = document.getElementById('gaPopover');
    var gaInput   = document.getElementById('gaInput');
    var gaSaveBtn = document.getElementById('gaSaveBtn');
    var activeBadge = null;

    function attachBadgeClick(badge) {
        badge.addEventListener('click', openPopover);
    }
    document.querySelectorAll('.ga-badge').forEach(attachBadgeClick);

    function openPopover(e) {
        e.stopPropagation();
        activeBadge = this;
        gaInput.value = this.title.replace('Granted: ', '');
        var rect = this.getBoundingClientRect();
        popover.style.display = 'block';
        popover.style.top  = (window.scrollY + rect.bottom + 4) + 'px';
        popover.style.left = Math.min(rect.left, window.innerWidth - 260) + 'px';
    }

    gaSaveBtn.addEventListener('click', function () {
        if (!activeBadge) return;
        var menuId = activeBadge.dataset.menu;
        var roleId = activeBadge.dataset.role;
        var ga     = gaInput.value.trim() || '*';

        gaSaveBtn.textContent = '...';
        postJson('/menus/set-granted-actions/' + menuId + '/' + roleId, { granted_actions: ga })
        .then(function (data) {
            gaSaveBtn.textContent = '<?= __('Save') ?>';
            var newGa = data.granted_actions || '*';
            activeBadge.className   = 'ga-badge ' + (newGa === '*' ? 'all' : 'some');
            activeBadge.title       = 'Granted: ' + newGa;
            activeBadge.textContent = (newGa === '*') ? 'all' : 'custom';
            popover.style.display   = 'none';
            activeBadge = null;
        })
        .catch(function () {
            gaSaveBtn.textContent = '<?= __('Save') ?>';
            alert('Save failed.');
        });
    });

    document.addEventListener('click', function (e) {
        if (!popover.contains(e.target)) {
            popover.style.display = 'none';
            activeBadge = null;
        }
    });
})();
</script>
