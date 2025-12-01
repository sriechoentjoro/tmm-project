<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="user-roles view content">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= h($user->full_name) ?> - Role Assignments</h6>
                    <div>
                        <?= $this->Html->link(__('Assign Roles'), ['action' => 'assign', $user->id], ['class' => 'btn btn-sm btn-warning']) ?>
                        <?= $this->Html->link(__('Edit User'), ['controller' => 'Users', 'action' => 'edit', $user->id], ['class' => 'btn btn-sm btn-info']) ?>
                        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-secondary']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">User Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%"><?= __('Username') ?></th>
                            <td><?= h($user->username) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Full Name') ?></th>
                            <td><?= h($user->full_name) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Email') ?></th>
                            <td><?= h($user->email) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Is Active') ?></th>
                            <td><?= $user->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Institution Type') ?></th>
                            <td><?= h($user->institution_type) ?: '<em class="text-muted">Not set</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Institution ID') ?></th>
                            <td><?= $user->institution_id ? $this->Number->format($user->institution_id) : '<em class="text-muted">Not set</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assigned Roles (<?= count($user->roles) ?>)</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($user->roles)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><?= __('Role ID') ?></th>
                                    <th><?= __('Role Name') ?></th>
                                    <th><?= __('Description') ?></th>
                                    <th class="actions"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user->roles as $role): ?>
                                <tr>
                                    <td><?= h($role->id) ?></td>
                                    <td><span class="badge badge-info"><?= h($role->name) ?></span></td>
                                    <td><?= h($role->description) ?></td>
                                    <td class="actions">
                                        <?= $this->Html->link(__('View Role'), ['controller' => 'Roles', 'action' => 'view', $role->id], ['class' => 'btn btn-sm btn-info']) ?>
                                        <?= $this->Form->postLink(
                                            __('Remove'),
                                            ['action' => 'remove', $user->id, $role->id],
                                            [
                                                'confirm' => __('Are you sure you want to remove the role "{0}" from this user?', $role->name),
                                                'class' => 'btn btn-sm btn-danger'
                                            ]
                                        ) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> <strong>No roles assigned</strong><br>
                        This user has no roles assigned. Click "Assign Roles" to add roles.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
