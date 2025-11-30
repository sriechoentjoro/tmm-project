<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="users profile">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-user"></i> <?= __('My Profile') ?>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Profile Avatar -->
                        <div class="col-md-3 text-center">
                            <?php if (!empty($user->photo)): ?>
                                <img src="<?= h($user->photo) ?>" alt="Profile Photo" class="img-thumbnail mb-3" style="max-width: 200px; border-radius: 10px;">
                            <?php else: ?>
                                <?php 
                                $userName = !empty($user->fullname) ? $user->fullname : $user->username;
                                $nameParts = explode(' ', trim($userName));
                                if (count($nameParts) >= 2) {
                                    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($userName, 0, 2));
                                }
                                $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e', '#e67e22'];
                                $colorIndex = ord($userName[0]) % count($colors);
                                $bgColor = $colors[$colorIndex];
                                ?>
                                <div style="width: 150px; height: 150px; border-radius: 50%; background-color: <?= $bgColor ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-weight: bold; font-size: 48px; color: white; border: 5px solid rgba(0,0,0,0.1);">
                                    <?= h($initials) ?>
                                </div>
                            <?php endif; ?>
                            
                            <h4><?= h($user->fullname) ?></h4>
                            <p class="text-muted">@<?= h($user->username) ?></p>
                            
                            <div class="mt-3">
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i> ' . __('Edit Profile'),
                                    ['action' => 'settings'],
                                    ['class' => 'btn btn-primary btn-block', 'escape' => false]
                                ) ?>
                            </div>
                        </div>
                        
                        <!-- Profile Details -->
                        <div class="col-md-9">
                            <h4 class="mb-3"><?= __('Profile Information') ?></h4>
                            
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%;"><?= __('Full Name') ?></th>
                                    <td><?= h($user->fullname) ?></td>
                                </tr>
                                <tr>
                                    <th><?= __('Username') ?></th>
                                    <td><?= h($user->username) ?></td>
                                </tr>
                                <tr>
                                    <th><?= __('Email') ?></th>
                                    <td>
                                        <?php if (!empty($user->email)): ?>
                                            <a href="mailto:<?= h($user->email) ?>">
                                                <i class="fas fa-envelope"></i> <?= h($user->email) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?= __('Roles') ?></th>
                                    <td>
                                        <?php if (!empty($user->roles)): ?>
                                            <?php foreach ($user->roles as $role): ?>
                                                <span class="badge badge-info"><?= h($role->name) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($user->has('vocational_training_institution')): ?>
                                <tr>
                                    <th><?= __('Vocational Training Institution') ?></th>
                                    <td>
                                        <?= $this->Html->link(
                                            h($user->vocational_training_institution->name),
                                            ['controller' => 'VocationalTrainingInstitutions', 'action' => 'view', $user->vocational_training_institution->id]
                                        ) ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th><?= __('Is Active') ?></th>
                                    <td>
                                        <?php if ($user->is_active): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            
                            <div class="mt-4">
                                <h5><?= __('Account Security') ?></h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <?= __('To change your password, please contact the system administrator.') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link(
                        '<i class="fas fa-arrow-left"></i> ' . __('Back'),
                        ['controller' => 'Dashboard', 'action' => 'index'],
                        ['class' => 'btn btn-secondary', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>
