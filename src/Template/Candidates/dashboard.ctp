<?php
/**
 * @var \App\View\AppView $this
 * @var array $byYear
 * @var array $byHost
 * @var array $bySuper
 */
?>
<div class="candidates dashboard">
    <h2><?= __('Candidate Management Dashboard') ?></h2>
    
    <div class="row">
        <!-- By Status Statistics -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-chart-line"></i> Candidates by Status</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($byYear as $item): ?>
                            <tr>
                                <td><?= h($item->status) ?></td>
                                <td><span class="badge badge-primary"><?= h($item->count) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- By Host Company -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4><i class="fas fa-building"></i> Candidates by Host Company</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($byHost as $item): ?>
                            <tr>
                                <td><?= $item->has('acceptance_organization') ? h($item->acceptance_organization->title) : 'N/A' ?></td>
                                <td><span class="badge badge-success"><?= h($item->count) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- By Supervising Company -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4><i class="fas fa-user-tie"></i> Candidates by Supervising Company</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company ID</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bySuper as $item): ?>
                            <tr>
                                <td><?= h($item->supervising_company_id) ?></td>
                                <td><span class="badge badge-info"><?= h($item->count) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h4><i class="fas fa-chart-line"></i> Quick Actions</h4>
                </div>
                <div class="card-body">
                    <?= $this->Html->link(
                        '<i class="fas fa-users"></i> View All Candidates',
                        ['action' => 'index'],
                        ['class' => 'btn btn-primary mr-2', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-user-plus"></i> Register New Candidate',
                        ['action' => 'wizard'],
                        ['class' => 'btn btn-success mr-2', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-file-upload"></i> Document Submission',
                        ['controller' => 'CandidateDocuments', 'action' => 'index'],
                        ['class' => 'btn btn-info', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.card-header {
    border-radius: 8px 8px 0 0;
    padding: 15px;
}
.card-header h4 {
    margin: 0;
    font-size: 16px;
}
.card-body {
    padding: 15px;
}
.badge {
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 14px;
}
.badge-primary {
    background-color: #007bff;
}
.badge-success {
    background-color: #28a745;
}
.badge-info {
    background-color: #17a2b8;
}
.mt-4 {
    margin-top: 2rem;
}
.mr-2 {
    margin-right: 0.5rem;
}
</style>
