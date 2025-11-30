<div class="dashboard recruitment-dashboard">
    <div class="row">
        <div class="col-md-12">
            <h2><i class="fa fa-user-plus"></i> TMM Recruitment Dashboard</h2>
            <p class="text-muted">Candidate and recruitment order management</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="stat-card stat-card-blue">
                <h3><?= number_format($stats['totalCandidates']) ?></h3>
                <p>Total Candidates</p>
                <i class="fa fa-users"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-card-orange">
                <h3><?= number_format($stats['pendingCandidates']) ?></h3>
                <p>Pending Review</p>
                <i class="fa fa-hourglass-half"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-card-green">
                <h3><?= number_format($stats['approvedCandidates']) ?></h3>
                <p>Approved</p>
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fa fa-list"></i> Recent Candidates</h4>
                    <div>
                        <?= $this->Html->link('<i class="fa fa-plus-circle"></i> Add Candidate', 
                            ['controller' => 'Candidates', 'action' => 'add'], 
                            ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
                        <?= $this->Html->link('<i class="fa fa-th-list"></i> View All', 
                            ['controller' => 'Candidates', 'action' => 'index'], 
                            ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentCandidates)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>LPK Institution</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCandidates as $candidate): ?>
                                        <tr>
                                            <td><?= h($candidate->id) ?></td>
                                            <td><?= h($candidate->full_name ?? 'N/A') ?></td>
                                            <td><?= h($candidate->vocational_training_institution->name ?? 'N/A') ?></td>
                                            <td><?= h($candidate->created ? $candidate->created->format('Y-m-d') : 'N/A') ?></td>
                                            <td>
                                                <?= $this->Html->link('<i class="fa fa-eye"></i>', 
                                                    ['controller' => 'Candidates', 'action' => 'view', $candidate->id], 
                                                    ['escape' => false, 'class' => 'btn btn-sm btn-info']) ?>
                                                <?= $this->Html->link('<i class="fa fa-edit"></i>', 
                                                    ['controller' => 'Candidates', 'action' => 'edit', $candidate->id], 
                                                    ['escape' => false, 'class' => 'btn btn-sm btn-warning']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-3x mb-3"></i><br>
                            No recent candidates found.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    padding: 25px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    transition: transform 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);

.stat-card-blue {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;

.stat-card-orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;

.stat-card-green {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;

.stat-card h3 {
    font-size: 36px;
    margin: 0;
    font-weight: bold;

.stat-card p {
    margin: 8px 0 0 0;
    font-size: 14px;
    opacity: 0.95;

.stat-card i {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 48px;
    opacity: 0.25;
</style>

