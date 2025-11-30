<?php
/**
 * @var \App\View\AppView $this
 * @var int $totalCandidates
 * @var int $totalTrainees
 * @var int $totalOrders
 * @var int $totalOrganizations
 * @var array $recentCandidates
 * @var array $recentTrainees
 */
$this->assign('title', 'Dashboard');
?>

<div class="dashboard index content">
    <h1><?= __('Dashboard') ?></h1>
    
    <div class="row" style="margin: 20px 0;">
        <!-- Statistics Cards -->
        <div class="col-md-3" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($totalCandidates) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Total Candidates</p>
                <a href="<?= $this->Url->build(['controller' => 'Candidates', 'action' => 'index']) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View All â†’</a>
            </div>
        </div>
        
        <div class="col-md-3" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($totalTrainees) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Total Trainees</p>
                <a href="<?= $this->Url->build(['controller' => 'Trainees', 'action' => 'index']) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View All â†’</a>
            </div>
        </div>
        
        <div class="col-md-3" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($totalOrders) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Total Orders</p>
                <a href="<?= $this->Url->build(['controller' => 'ApprenticeOrders', 'action' => 'index']) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View All â†’</a>
            </div>
        </div>
        
        <div class="col-md-3" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($totalOrganizations) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Organizations</p>
                <a href="<?= $this->Url->build(['controller' => 'AcceptanceOrganizations', 'action' => 'index']) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View All â†’</a>
            </div>
        </div>
    </div>
    
    <div class="row" style="margin: 20px 0;">
        <!-- Recent Candidates -->
        <div class="col-md-6" style="padding: 10px;">
            <div class="card" style="padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #667eea;">
                    <i class="fas fa-users"></i> Recent Candidates
                </h3>
                <?php if (!empty($recentCandidates)): ?>
                <table class="table" style="margin-top: 15px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCandidates as $candidate): ?>
                        <tr>
                            <td><?= h($candidate->id) ?></td>
                            <td>
                                <?= $this->Html->link(
                                    h($candidate->fullname),
                                    ['controller' => 'Candidates', 'action' => 'view', $candidate->id]
                                ) ?>
                            </td>
                            <td>
                                <?php if ($candidate->has('apprentice_order')): ?>
                                    <?= h($candidate->apprentice_order->title) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="color: #999; font-style: italic;">No candidates yet.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Trainees -->
        <div class="col-md-6" style="padding: 10px;">
            <div class="card" style="padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #f5576c;">
                    <i class="fas fa-user-graduate"></i> Recent Trainees
                </h3>
                <?php if (!empty($recentTrainees)): ?>
                <table class="table" style="margin-top: 15px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Organization</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTrainees as $trainee): ?>
                        <tr>
                            <td><?= h($trainee->id) ?></td>
                            <td>
                                <?= $this->Html->link(
                                    h($trainee->fullname),
                                    ['controller' => 'Trainees', 'action' => 'view', $trainee->id]
                                ) ?>
                            </td>
                            <td>
                                <?php if ($trainee->has('acceptance_organization')): ?>
                                    <?= h($trainee->acceptance_organization->title) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="color: #999; font-style: italic;">No trainees yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="row" style="margin: 20px 0;">
        <div class="col-md-12" style="padding: 10px;">
            <div class="card" style="padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: #f8f9fa;">
                <h3 style="margin-top: 0;">Quick Actions</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <?= $this->Html->link(
                        '<i class="fas fa-user-plus"></i> Add New Candidate',
                        ['controller' => 'Candidates', 'action' => 'add'],
                        ['class' => 'button', 'escape' => false, 'style' => 'background: #667eea; color: white;']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-file-alt"></i> New Apprentice Order',
                        ['controller' => 'ApprenticeOrders', 'action' => 'add'],
                        ['class' => 'button', 'escape' => false, 'style' => 'background: #4facfe; color: white;']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-building"></i> Add Organization',
                        ['controller' => 'AcceptanceOrganizations', 'action' => 'add'],
                        ['class' => 'button', 'escape' => false, 'style' => 'background: #fa709a; color: white;']
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -10px;
    margin-right: -10px;
.col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
.col-md-12 {
    flex: 0 0 100%;
    max-width: 100%;
@media (max-width: 768px) {
    .col-md-3, .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
.table {
    width: 100%;
    border-collapse: collapse;
.table th,
.table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
.table th {
    background: #f5f5f5;
    font-weight: 600;
</style>

