<?php
/**
 * LPK Registration - Index
 * List all LPK registrations with status
 */
?>
<div class="lpk-registration-index">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap"></i> <?= __('LPK Registration Management') ?>
                    </h3>
                    <div class="card-tools">
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('Register New LPK'),
                            ['action' => 'create'],
                            ['class' => 'btn btn-primary btn-sm', 'escape' => false]
                        ) ?>
                    </div>
                </div>

                <div class="card-body">
                    
                    <!-- Status Legend -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-info-circle"></i> <?= __('Registration Status:') ?></strong>
                                <span class="badge badge-warning ml-2"><?= __('Pending Verification') ?></span> <?= __('Waiting for email verification') ?>
                                <span class="badge badge-info ml-2"><?= __('Verified') ?></span> <?= __('Email verified, password not set') ?>
                                <span class="badge badge-success ml-2"><?= __('Active') ?></span> <?= __('Account activated, can login') ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($institutions)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?= $this->Paginator->sort('id', __('ID')) ?></th>
                                        <th><?= $this->Paginator->sort('name', __('Institution Name')) ?></th>
                                        <th><?= $this->Paginator->sort('registration_number', __('Reg. Number')) ?></th>
                                        <th><?= $this->Paginator->sort('director_name', __('Director')) ?></th>
                                        <th><?= $this->Paginator->sort('email', __('Email')) ?></th>
                                        <th><?= __('Location') ?></th>
                                        <th><?= $this->Paginator->sort('status', __('Status')) ?></th>
                                        <th><?= $this->Paginator->sort('created', __('Registered')) ?></th>
                                        <th class="text-center"><?= __('Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($institutions as $institution): ?>
                                    <tr>
                                        <td><?= $this->Number->format($institution->id) ?></td>
                                        <td>
                                            <strong><?= h($institution->name) ?></strong>
                                        </td>
                                        <td><?= h($institution->registration_number) ?></td>
                                        <td><?= h($institution->director_name) ?></td>
                                        <td>
                                            <i class="fas fa-envelope"></i>
                                            <?= h($institution->email) ?>
                                        </td>
                                        <td>
                                            <?php if ($institution->has('master_propinsi')): ?>
                                                <?= h($institution->master_propinsi->title) ?>
                                                <?php if ($institution->has('master_kabupaten')): ?>
                                                    , <?= h($institution->master_kabupaten->title) ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = 'secondary';
                                            $statusText = $institution->status;
                                            
                                            switch ($institution->status) {
                                                case 'pending_verification':
                                                    $statusBadge = 'warning';
                                                    $statusText = __('Pending Verification');
                                                    break;
                                                case 'verified':
                                                    $statusBadge = 'info';
                                                    $statusText = __('Verified');
                                                    break;
                                                case 'active':
                                                    $statusBadge = 'success';
                                                    $statusText = __('Active');
                                                    break;
                                            }
                                            ?>
                                            <span class="badge badge-<?= $statusBadge ?>">
                                                <?= $statusText ?>
                                            </span>
                                            
                                            <?php if ($institution->email_verified_at): ?>
                                                <br><small class="text-muted">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    <?= $institution->email_verified_at->format('Y-m-d H:i') ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= $institution->created ? $institution->created->format('Y-m-d H:i') : '-' ?></small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'VocationalTrainingInstitutions', 'action' => 'view', $institution->id],
                                                    [
                                                        'class' => 'btn btn-info btn-sm',
                                                        'escape' => false,
                                                        'title' => __('View Details')
                                                    ]
                                                ) ?>
                                                
                                                <?php if ($institution->status === 'pending_verification'): ?>
                                                    <?= $this->Html->link(
                                                        '<i class="fas fa-paper-plane"></i>',
                                                        ['action' => 'resendVerification', $institution->id],
                                                        [
                                                            'class' => 'btn btn-warning btn-sm',
                                                            'escape' => false,
                                                            'title' => __('Resend Verification Email'),
                                                            'confirm' => __('Resend verification email to {0}?', $institution->email)
                                                        ]
                                                    ) ?>
                                                <?php endif; ?>
                                                
                                                <?php if ($institution->status === 'verified'): ?>
                                                    <?= $this->Html->link(
                                                        '<i class="fas fa-key"></i>',
                                                        ['action' => 'setPassword', $institution->id, 'prefix' => false],
                                                        [
                                                            'class' => 'btn btn-primary btn-sm',
                                                            'escape' => false,
                                                            'title' => __('Set Password'),
                                                            'target' => '_blank'
                                                        ]
                                                    ) ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p>
                                    <?= $this->Paginator->counter([
                                        'format' => __('Showing {{start}} to {{end}} of {{count}} entries')
                                    ]) ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <ul class="pagination pagination-sm float-right">
                                    <?= $this->Paginator->first('<i class="fas fa-angle-double-left"></i>', ['escape' => false]) ?>
                                    <?= $this->Paginator->prev('<i class="fas fa-angle-left"></i>', ['escape' => false]) ?>
                                    <?= $this->Paginator->numbers() ?>
                                    <?= $this->Paginator->next('<i class="fas fa-angle-right"></i>', ['escape' => false]) ?>
                                    <?= $this->Paginator->last('<i class="fas fa-angle-double-right"></i>', ['escape' => false]) ?>
                                </ul>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5><?= __('No LPK registrations found') ?></h5>
                            <p><?= __('Click "Register New LPK" to add your first registration.') ?></p>
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('Register New LPK'),
                                ['action' => 'create'],
                                ['class' => 'btn btn-primary', 'escape' => false]
                            ) ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
}

.badge {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.btn-group .btn {
    margin: 0 2px;
}

@media (max-width: 768px) {
    .table {
        font-size: 0.875rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin: 2px 0;
    }
}
</style>
