<?php
/**
 * Tabbed view wrapper: first tab shows the record detail (captured in the
 * 'detailPane' view block by the calling template), following tabs list
 * related data discovered by AppController::getRelatedDataTabs().
 *
 * Usage in a view template:
 *   <?php $this->start('detailPane'); ?> ...detail html... <?php $this->end(); ?>
 *   <?= $this->element('view_tabs') ?>
 *
 * @var \App\View\AppView $this
 */
$relatedTabs = isset($relatedTabs) ? $relatedTabs : (isset($this->viewVars['relatedTabs']) ? $this->viewVars['relatedTabs'] : []);
?>
<style>
.sv-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    list-style: none;
    margin: 0 0 -1px 0;
    padding: 0;
}
.sv-tab {
    padding: 10px 18px;
    border: 1px solid #d0d7de;
    border-bottom: none;
    border-radius: 8px 8px 0 0;
    background: #f6f8fa;
    color: #57606a;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    user-select: none;
}
.sv-tab:hover {
    background: #eef1f4;
}
.sv-tab.active {
    background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
    color: #fff;
    border-color: #00838F;
}
.sv-tab .sv-count {
    display: inline-block;
    min-width: 20px;
    padding: 1px 7px;
    margin-left: 6px;
    border-radius: 10px;
    background: rgba(0,0,0,0.15);
    font-size: 12px;
    text-align: center;
}
.sv-pane {
    display: none;
    border: 1px solid #d0d7de;
    border-radius: 0 8px 8px 8px;
    background: #fff;
    padding: 18px;
}
.sv-pane.active {
    display: block;
}
.sv-related-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.sv-related-table th {
    padding: 10px 12px;
    border-bottom: 2px solid #00BCD4;
    background: linear-gradient(135deg, rgba(0, 188, 212, 0.12) 0%, rgba(0, 131, 143, 0.12) 100%);
    text-align: left;
    white-space: nowrap;
}
.sv-related-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #e9ecef;
}
</style>

<div class="simple-view-tabs">
    <ul class="sv-tabs">
        <li class="sv-tab active" data-pane="sv-pane-detail"><i class="fas fa-file-alt"></i> <?= __('Detail') ?></li>
        <?php foreach ($relatedTabs as $i => $tab): ?>
        <li class="sv-tab" data-pane="sv-pane-rel<?= $i ?>">
            <?= h($tab['label']) ?><span class="sv-count"><?= count($tab['rows']) ?></span>
        </li>
        <?php endforeach; ?>
    </ul>

    <div id="sv-pane-detail" class="sv-pane active">
        <?= $this->fetch('detailPane') ?>
    </div>

    <?php foreach ($relatedTabs as $i => $tab): ?>
    <div id="sv-pane-rel<?= $i ?>" class="sv-pane">
        <div style="overflow-x: auto;">
            <table class="sv-related-table">
                <thead>
                    <tr>
                        <?php foreach ($tab['columns'] as $col): ?>
                        <th><?= h(ucwords(str_replace('_', ' ', $col))) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tab['rows'] as $row): ?>
                    <tr>
                        <?php foreach ($tab['columns'] as $col): ?>
                        <td><?= h(isset($row[$col]) && is_scalar($row[$col]) ? $row[$col] : '') ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($tab['rows']) >= 50): ?>
        <p style="color: #6c757d; margin-top: 10px; font-size: 12px;"><?= __('Showing the latest 50 records.') ?></p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.simple-view-tabs').forEach(function (wrap) {
        wrap.querySelectorAll('.sv-tab').forEach(function (tab) {
            tab.addEventListener('click', function () {
                wrap.querySelectorAll('.sv-tab').forEach(function (t) { t.classList.remove('active'); });
                wrap.querySelectorAll('.sv-pane').forEach(function (p) { p.classList.remove('active'); });
                tab.classList.add('active');
                var pane = wrap.querySelector('#' + tab.dataset.pane);
                if (pane) pane.classList.add('active');
            });
        });
    });
});
</script>
