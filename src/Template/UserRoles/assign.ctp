<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Role[] $roles
 */
?>
<div class="user-roles assign content">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= __('Assign Roles to: ') . h($user->full_name) ?></h6>
                    <div>
                        <?= $this->Html->link(__('View Assignments'), ['action' => 'view', $user->id], ['class' => 'btn btn-sm btn-info']) ?>
                        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-secondary']) ?>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- User Info Summary -->
                    <div class="alert alert-info">
                        <strong><i class="fa fa-user"></i> User:</strong> <?= h($user->username) ?> (<?= h($user->email) ?>)<br>
                        <strong><i class="fa fa-id-badge"></i> Current Roles:</strong> 
                        <?php if (!empty($user->roles)): ?>
                            <?php foreach ($user->roles as $role): ?>
                                <span class="badge badge-info"><?= h($role->name) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="badge badge-secondary">None</span>
                        <?php endif; ?>
                    </div>

                    <?= $this->Form->create($user) ?>
                    <fieldset>
                        <legend><?= __('Select Roles') ?></legend>
                        
                        <div class="form-group">
                            <?= $this->Form->control('roles._ids', [
                                'options' => $roles,
                                'class' => 'form-control',
                                'multiple' => true,
                                'size' => 10,
                                'label' => 'Available Roles (Hold Ctrl/Cmd to select multiple)'
                            ]) ?>
                            <small class="form-text text-muted">
                                <i class="fa fa-info-circle"></i> Select one or more roles to assign to this user. 
                                Hold Ctrl (Windows/Linux) or Cmd (Mac) to select multiple roles.
                            </small>
                        </div>

                        <!-- Role Descriptions -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <strong>Role Descriptions</strong>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Role</th>
                                            <th>Permissions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>administrator</strong></td>
                                            <td>Full access to all features and data</td>
                                        </tr>
                                        <tr>
                                            <td><strong>management</strong></td>
                                            <td>Read-only access to all data for reporting</td>
                                        </tr>
                                        <tr>
                                            <td><strong>tmm-recruitment</strong></td>
                                            <td>Manage candidates and apprentice orders</td>
                                        </tr>
                                        <tr>
                                            <td><strong>tmm-training</strong></td>
                                            <td>Manage trainees and training data</td>
                                        </tr>
                                        <tr>
                                            <td><strong>tmm-documentation</strong></td>
                                            <td>Manage documents and ticketing</td>
                                        </tr>
                                        <tr>
                                            <td><strong>lpk-penyangga</strong></td>
                                            <td>Manage candidates for their institution only</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <i class="fa fa-exclamation-triangle"></i> <strong>Important:</strong>
                            <ul class="mb-0">
                                <li>Users can have multiple roles</li>
                                <li>For LPK users, ensure institution_id and institution_type are set</li>
                                <li>Changes take effect immediately after saving</li>
                            </ul>
                        </div>
                    </fieldset>
                    
                    <div class="mt-3">
                        <?= $this->Form->button(__('Save Role Assignments'), ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'view', $user->id], ['class' => 'btn btn-secondary']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
