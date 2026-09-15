<?php
/**
 * Offer this apprentice order to vocational training institutions, and show
 * who already has it.
 *
 * The two emails this drives - apprentice_order_shared and
 * apprentice_order_cancelled - have been sitting in the email_templates table,
 * active, with nothing in the application asking for their keys. This panel and
 * ApprenticeOrdersController::share()/cancelShare() are the code they were
 * written for.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeOrder $apprenticeOrder
 * @var \App\Model\Entity\ApprenticeOrderShare[] $orderShares
 * @var array<int, \Cake\Datasource\EntityInterface> $shareableInstitutions
 * @var array<int, bool> $alreadySharedWith
 * @var bool $sharingInstalled Whether apprentice_order_shares exists yet.
 */
?>
<style>
    .aos-list { max-height: 260px; overflow-y: auto; border: 1px solid #e3e8ee; border-radius: 8px; padding: 8px 12px; }
    .aos-list label { display: block; padding: 4px 0; font-weight: 400; cursor: pointer; }
    .aos-list input { margin-right: 8px; }
    .aos-taken { color: #8898aa; }
    .aos-table { width: 100%; border-collapse: collapse; }
    .aos-table th, .aos-table td { padding: 8px 10px; border-bottom: 1px solid #eef1f5; text-align: left; font-size: 13px; }
    .aos-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; }
    .aos-badge.ok { background: #d4edda; color: #155724; }
    .aos-badge.off { background: #f1f3f5; color: #6c757d; }
    .aos-badge.warn { background: #fff3cd; color: #8a6d1f; }
    .aos-note { color: #8898aa; font-size: 12px; margin: 8px 0 0; }
</style>

<div class="github-details-card">
    <div class="github-details-header">
        <h3 class="github-details-title">
            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M5.5 3.5a2 2 0 1 1 1.4 1.91l-2.1 3.5a2 2 0 1 1-1.29-.77l2.1-3.5A2 2 0 0 1 5.5 3.5Zm5.5 6a2 2 0 1 1-1.4 1.91l-2.1-3.5a2 2 0 0 0 1.29-.77l2.1 3.5A2 2 0 0 1 11 9.5Z"></path>
            </svg>
            <?= __('Shared with Institutions') ?>
        </h3>
    </div>

    <div class="github-details-body">
        <?php if (empty($sharingInstalled)): ?>
            <p class="aos-note">
                <?= __('Sharing is not installed on this server yet. Run:') ?>
                <code>bin/cake create_apprentice_order_shares --apply</code>
            </p>
        <?php else: ?>

            <?php if (!empty($orderShares)): ?>
                <table class="aos-table">
                    <thead>
                        <tr>
                            <th><?= __('Institution') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Notified') ?></th>
                            <th><?= __('Shared By') ?></th>
                            <th><?= __('When') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderShares as $share): ?>
                        <tr>
                            <td>
                                <strong><?= h($share->lpk_name) ?></strong><br>
                                <small class="aos-taken"><?= h($share->lpk_email) ?></small>
                            </td>
                            <td>
                                <?php if ($share->isCancelled()): ?>
                                    <span class="aos-badge off"><?= __('Withdrawn') ?></span>
                                <?php else: ?>
                                    <span class="aos-badge ok"><?= __('Shared') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($share->notified): ?>
                                    <span class="aos-badge ok"><?= __('Yes') ?></span>
                                <?php else: ?>
                                    <?php // Saved but not delivered: worth showing apart from "shared". ?>
                                    <span class="aos-badge warn"><?= __('Email failed') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= h($share->shared_by_name) ?></td>
                            <td>
                                <small><?= $share->created ? h($share->created->format('Y-m-d H:i')) : '' ?></small>
                                <?php if ($share->isCancelled() && $share->cancelled_at): ?>
                                    <br><small class="aos-taken"><?= __('withdrawn {0}', h($share->cancelled_at->format('Y-m-d H:i'))) ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if (!$share->isCancelled()): ?>
                                    <?= $this->Form->postLink(
                                        __('Withdraw'),
                                        ['action' => 'cancelShare', $apprenticeOrder->id, $share->id],
                                        [
                                            'class' => 'btn btn-sm btn-outline-danger',
                                            'confirm' => __('Withdraw this order from {0}? They will be emailed.', $share->lpk_name),
                                        ]
                                    ) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="aos-note"><?= __('This order has not been shared with anyone yet.') ?></p>
            <?php endif; ?>

            <?php if (!empty($shareableInstitutions)): ?>
                <hr>
                <?= $this->Form->create(null, ['url' => ['action' => 'share', $apprenticeOrder->id]]) ?>
                <div class="aos-list">
                    <?php foreach ($shareableInstitutions as $institution): ?>
                        <?php $taken = isset($alreadySharedWith[(int)$institution->id]); ?>
                        <label class="<?= $taken ? 'aos-taken' : '' ?>">
                            <input type="checkbox" name="institution_ids[]"
                                   value="<?= h($institution->id) ?>"<?= $taken ? ' disabled' : '' ?>>
                            <?= h($institution->name) ?>
                            <?php if ($taken): ?>
                                &mdash; <?= __('already has it') ?>
                            <?php elseif (empty($institution->email)): ?>
                                &mdash; <?= __('no email address') ?>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 12px;">
                    <?= $this->Form->button(__('Share This Order'), ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
                <p class="aos-note">
                    <?= __('Each institution selected is emailed the order details. Withdrawing later emails them again.') ?>
                </p>
                <?= $this->Form->end() ?>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
