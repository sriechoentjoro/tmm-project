<?php
/**
 * Email Templates (enriched)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EmailTemplate[]|\Cake\Collection\CollectionInterface $emailTemplates
 * @var array $counts   total / active
 * @var array $sentMap  template_key => jumlah terkirim
 */
?>
<style>
.et-stat{flex:1;min-width:150px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #667eea;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.et-stat .v{font-size:22px;font-weight:800;color:#212a3e}
.et-stat .l{font-size:12px;color:#6c757d}
.et-key{display:inline-block;padding:2px 8px;border-radius:8px;font-size:11px;background:#eef1fb;color:#4c5bd4;font-family:monospace}
.et-var{display:inline-block;padding:1px 6px;border-radius:6px;font-size:10px;background:#fff3cd;color:#856404;font-family:monospace;margin:1px}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-envelope-open-text"></i> <?= __('Email Templates') ?></h2>
        <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('Add New'), ['action' => 'add'], ['class' => 'btn-export-light', 'escape' => false]) ?>
    </div>
</div>

<!-- Ringkasan -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="et-stat"><div class="v"><?= (int)($counts['total'] ?? 0) ?></div><div class="l"><i class="fas fa-layer-group"></i> <?= __('Templates') ?></div></div>
    <div class="et-stat" style="border-left-color:#28a745"><div class="v"><?= (int)($counts['active'] ?? 0) ?></div><div class="l"><i class="fas fa-toggle-on"></i> <?= __('Active') ?></div></div>
    <div class="et-stat" style="border-left-color:#dc3545"><div class="v"><?= (int)($counts['total'] ?? 0) - (int)($counts['active'] ?? 0) ?></div><div class="l"><i class="fas fa-toggle-off"></i> <?= __('Inactive') ?></div></div>
    <div class="et-stat" style="border-left-color:#20c997"><div class="v"><?= (int)array_sum($sentMap) ?></div><div class="l"><i class="fas fa-paper-plane"></i> <?= __('Emails Sent') ?></div></div>
</div>

<div class="table-scroll-wrapper" style="overflow-x:auto;-webkit-overflow-scrolling:touch">
    <div class="emailTemplates index content">
        <table class="table" style="border-collapse:collapse;width:100%;min-width:800px">
            <thead style="background:linear-gradient(135deg,rgba(102,126,234,.15),rgba(118,75,162,.15))">
                <tr>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap" class="actions"><?= __('Actions') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Template Key') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Subject') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Variables') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap;text-align:center"><?= __('Sent') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap;text-align:center"><?= __('Active') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emailTemplates as $row): ?>
                <tr style="border-bottom:1px solid #e9ecef">
                    <td style="padding:10px 12px;white-space:nowrap" class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    </td>
                    <td style="padding:10px 12px"><span class="et-key"><?= h($row['template_key']) ?></span></td>
                    <td style="padding:10px 12px">
                        <strong><?= h($row['subject']) ?></strong>
                        <?php if (!empty($row['description'])): ?><br><small style="color:#98a2ad"><?= h($row['description']) ?></small><?php endif; ?>
                    </td>
                    <td style="padding:10px 12px;max-width:260px">
                        <?php
                            $vars = [];
                            if (!empty($row['variables'])) {
                                $decoded = json_decode($row['variables'], true);
                                if (is_array($decoded)) {
                                    $vars = array_values($decoded) === $decoded ? $decoded : array_keys($decoded);
                                } else {
                                    $vars = preg_split('/[,\s]+/', trim($row['variables']));
                                }
                            }
                            foreach (array_slice(array_filter((array)$vars), 0, 6) as $v): ?>
                            <span class="et-var"><?= h($v) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td style="padding:10px 12px;text-align:center"><?= isset($sentMap[$row['template_key']]) ? '<strong>' . (int)$sentMap[$row['template_key']] . '</strong>' : '<span style="color:#c3ccd6">0</span>' ?></td>
                    <td style="padding:10px 12px;text-align:center"><?= $row['is_active'] ? '<span style="color:#28a745">●</span>' : '<span style="color:#dc3545">○</span>' ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (iterator_count($emailTemplates) === 0): ?>
                <tr><td colspan="6" style="text-align:center;padding:30px;color:#6c757d"><?= __('No email templates yet.') ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="paginator" style="margin-top:15px">
    <ul class="pagination">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
</div>
