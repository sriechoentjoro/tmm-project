<div class="dashboard admin-dashboard">
    <h2>Administrator Dashboard</h2>
    <p>Welcome to the TMM System Administration Panel</p>
    
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?= number_format($stats['totalUsers']) ?></h3>
                <p>Total Users</p>
                <i class="fa fa-users"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?= number_format($stats['totalCandidates']) ?></h3>
                <p>Total Candidates</p>
                <i class="fa fa-user-plus"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?= number_format($stats['totalTrainees']) ?></h3>
                <p>Total Trainees</p>
                <i class="fa fa-graduation-cap"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?= number_format($stats['totalLpkInstitutions']) ?></h3>
                <p>LPK Institutions</p>
                <i class="fa fa-building"></i>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Quick Actions</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <?= $this->Html->link('<i class="fa fa-users"></i> Manage Users', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'btn btn-primary btn-sm']) ?>
                        </li>
                        <li class="list-group-item">
                            <?= $this->Html->link('<i class="fa fa-shield"></i> Manage Roles', ['controller' => 'Roles', 'action' => 'index'], ['escape' => false, 'class' => 'btn btn-primary btn-sm']) ?>
                        </li>
                        <li class="list-group-item">
                            <?= $this->Html->link('<i class="fa fa-database"></i> View System Logs', ['controller' => 'Logs', 'action' => 'index'], ['escape' => false, 'class' => 'btn btn-primary btn-sm']) ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Activity</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">System activity monitoring coming soon...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;

.stat-card h3 {
    font-size: 36px;
    margin: 0;
    font-weight: bold;

.stat-card p {
    margin: 10px 0 0 0;
    font-size: 14px;
    opacity: 0.9;

.stat-card i {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 48px;
    opacity: 0.3;

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;

.card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
</style>

