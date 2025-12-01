<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="user-roles index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><?= __('User-Role Assignments') ?></h3>
        <?= $this->Html->link(__('Back to Users'), ['controller' => 'Users', 'action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('id') ?></th>
                            <th><?= $this->Paginator->sort('username') ?></th>
                            <th><?= $this->Paginator->sort('full_name') ?></th>
                            <th><?= $this->Paginator->sort('email') ?></th>
                            <th><?= __('Assigned Roles') ?></th>
                            <th><?= __('Role Count') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $this->Number->format($user->id) ?></td>
                            <td><?= h($user->username) ?></td>
                            <td><?= h($user->full_name) ?></td>
                            <td><?= h($user->email) ?></td>
                            <td>
                                <?php if (!empty($user->roles)): ?>
                                    <?php foreach ($user->roles as $role): ?>
                                        <span class="badge badge-info mr-1"><?= h($role->name) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No roles assigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary"><?= count($user->roles) ?></span>
                            </td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['action' => 'view', $user->id], ['class' => 'btn btn-sm btn-info']) ?>
                                <?= $this->Html->link(__('Assign Roles'), ['action' => 'assign', $user->id], ['class' => 'btn btn-sm btn-warning']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="paginator">
                <ul class="pagination justify-content-center">
                    <?= $this->Paginator->first('<< ' . __('first')) ?>
                    <?= $this->Paginator->prev('< ' . __('previous')) ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next(__('next') . ' >') ?>
                    <?= $this->Paginator->last(__('last') . ' >>') ?>
                </ul>
                <p class="text-center"><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
