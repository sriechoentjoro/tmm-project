<?php
/**
 * @var \App\View\AppView $this
 * @var array $stats
 * @var array $recentTrainees
 */
$this->assign('title', 'Training Dashboard');
?>

<div class="dashboard training content">
    <h1><?= __('Training Dashboard') ?></h1>
    
    <div class="row" style="margin: 20px 0;">
        <!-- Statistics Cards -->
        <div class="col-md-4" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($stats['totalTrainees']) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Total Trainees</p>
                <a href="<?= $this->Url->build(['controller' => 'Trainees', 'action' => 'index']) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View All →</a>
            </div>
        </div>
        
        <div class="col-md-4" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($stats['activeTrainees']) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Active Trainees</p>
                <a href="<?= $this->Url->build(['controller' => 'Trainees', 'action' => 'index', '?' => ['status' => 'active']]) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View Active →</a>
            </div>
        </div>
        
        <div class="col-md-4" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($stats['completedTrainees']) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Completed Trainees</p>
                <a href="<?= $this->Url->build(['controller' => 'Trainees', 'action' => 'index', '?' => ['status' => 'completed']]) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View Completed →</a>
            </div>
        </div>
    </div>
    
    <!-- Recent Trainees -->
    <div class="row" style="margin: 20px 0;">
        <div class="col-md-12">
            <div class="card" style="padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #f093fb; padding-bottom: 10px;">
                    <i class="fas fa-users"></i> Recent Trainees
                </h3>
                
                <?php if (!empty($recentTrainees)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Training Institution</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTrainees as $trainee): ?>
                                    <tr>
                                        <td><?= h($trainee->id) ?></td>
                                        <td><?= h($trainee->fullname ? $trainee->fullname : 'N/A') ?></td>
                                        <td>
                                            <?php if ($trainee->has('vocational_training_institution')): ?>
                                                <?= h($trainee->vocational_training_institution->name ? $trainee->vocational_training_institution->name : 'N/A') ?>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($trainee->is_active): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($trainee->created ? $trainee->created->format('Y-m-d H:i') : 'N/A') ?></td>
                                        <td>
                                            <?= $this->Html->link(__('View'), ['controller' => 'Trainees', 'action' => 'view', $trainee->id], ['class' => 'btn btn-sm btn-info']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No recent trainees found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
