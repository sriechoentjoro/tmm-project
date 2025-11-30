<div class="dashboard lpk-dashboard">
    <div class="row">
        <div class="col-md-12">
            <h2>LPK Dashboard - <?= h($stats['institutionName']) ?></h2>
            <p class="text-muted">Institution-specific candidate management</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="stat-card stat-card-primary">
                <h3><?= number_format($stats['totalCandidates']) ?></h3>
                <p>Total Candidates</p>
                <i class="fa fa-users"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-card-warning">
                <h3><?= number_format($stats['pendingCandidates']) ?></h3>
                <p>Pending Candidates</p>
                <i class="fa fa-clock-o"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-card-success">
                <h3><?= number_format($stats['approvedCandidates']) ?></h3>
                <p>Approved Candidates</p>
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Recent Candidates</h4>
                    <?= $this->Html->link('View All', ['controller' => 'Candidates', 'action' => 'index'], ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentCandidates)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCandidates as $candidate): ?>
                                        <tr>
                                            <td><?= h($candidate->id) ?></td>
                                            <td><?= h($candidate->full_name ?? 'N/A') ?></td>
                                            <td><?= h($candidate->created ? $candidate->created->format('Y-m-d H:i') : 'N/A') ?></td>
                                            <td>
                                                <?php if (isset($candidate->status)): ?>
                                                    <span class="badge badge-<?= $candidate->status == 'approved' ? 'success' : ($candidate->status == 'pending' ? 'warning' : 'secondary') ?>">
                                                        <?= h($candidate->status) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['controller' => 'Candidates', 'action' => 'view', $candidate->id], ['escape' => false, 'class' => 'btn btn-sm btn-info', 'title' => 'View']) ?>
                                                <?= $this->Html->link('<i class="fa fa-edit"></i>', ['controller' => 'Candidates', 'action' => 'edit', $candidate->id], ['escape' => false, 'class' => 'btn btn-sm btn-warning', 'title' => 'Edit']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-3x mb-3"></i><br>
                            No candidates found for your institution.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Quick Actions</h4>
                </div>
                <div class="card-body">
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> Add New Candidate', 
                        ['controller' => 'Candidates', 'action' => 'add'], 
                        ['escape' => false, 'class' => 'btn btn-success btn-lg']) ?>
                    <?= $this->Html->link('<i class="fa fa-list"></i> View All Candidates', 
                        ['controller' => 'Candidates', 'action' => 'index'], 
                        ['escape' => false, 'class' => 'btn btn-primary btn-lg']) ?>
                    <?= $this->Html->link('<i class="fa fa-file-text"></i> View Documents', 
                        ['controller' => 'CandidateDocuments', 'action' => 'index'], 
                        ['escape' => false, 'class' => 'btn btn-info btn-lg']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease;

.stat-card:hover {
    transform: translateY(-5px);

.stat-card-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

.stat-card-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);

.stat-card-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);

.stat-card h3 {
    font-size: 42px;
    margin: 0;
    font-weight: bold;

.stat-card p {
    margin: 10px 0 0 0;
    font-size: 16px;
    opacity: 0.9;

.stat-card i {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 60px;
    opacity: 0.2;

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;

.card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;

.badge {
    padding: 5px 10px;
    font-size: 12px;
    text-transform: uppercase;

.d-flex {
    display: flex;

.justify-content-between {
    justify-content: space-between;

.align-items-center {
    align-items: center;
</style>

