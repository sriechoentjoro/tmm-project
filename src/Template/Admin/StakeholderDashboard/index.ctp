<?php
/**
 * @var \App\View\AppView $this
 * @var array $statistics
 * @var \Cake\ORM\ResultSet $recentActivities
 * @var \Cake\ORM\ResultSet $pendingApprovals
 * @var \Cake\ORM\ResultSet $pendingVerifications
 * @var array $chartData
 */
$this->assign('title', 'Stakeholder Management Dashboard');
?>

<div class="stakeholder-dashboard">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="page-title">
                    <i class="fas fa-users-cog"></i> Stakeholder Management Dashboard
                </h1>
                <p class="text-muted">Monitor and manage all stakeholder types from one central location</p>
            </div>
            <div class="col-md-6 text-right">
                <?= $this->Html->link(
                    '<i class="fas fa-download"></i> Export Statistics',
                    ['action' => 'exportStatistics'],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-question-circle"></i> Help',
                    ['controller' => 'Help', 'action' => 'stakeholderManagement'],
                    ['class' => 'btn btn-info', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>

    <!-- Alert Cards for Pending Actions -->
    <?php if ($statistics['overall']['total_pending_verifications'] > 0 || $pendingApprovals->count() > 0): ?>
    <div class="row mb-4">
        <?php if ($statistics['overall']['total_pending_verifications'] > 0): ?>
        <div class="col-md-6">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Pending Verifications</h5>
                <p>
                    <strong><?= $statistics['overall']['total_pending_verifications'] ?></strong> institution(s) are waiting for email verification.
                </p>
                <hr>
                <p class="mb-0">
                    <?= $this->Html->link('View Pending Verifications', '#pending-verifications', ['class' => 'alert-link']) ?>
                </p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($pendingApprovals->count() > 0): ?>
        <div class="col-md-6">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-clock"></i> Pending Approvals</h5>
                <p>
                    <strong><?= $pendingApprovals->count() ?></strong> approval request(s) require your review.
                </p>
                <hr>
                <p class="mb-0">
                    <?= $this->Html->link('Review Approvals', '#pending-approvals', ['class' => 'alert-link']) ?>
                </p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Overall Statistics Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-purple">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?= h($statistics['overall']['total_stakeholders']) ?></h3>
                        <p class="stat-label">Total Stakeholders</p>
                        <small class="text-muted">All types combined</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- LPK Statistics Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-blue">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?= h($statistics['lpk']['total']) ?></h3>
                        <p class="stat-label">LPK Institutions</p>
                        <small class="text-muted">
                            <?= $statistics['lpk']['active'] ?> active, 
                            <?= $statistics['lpk']['pending_verification'] ?> pending
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link('Manage LPK <i class="fas fa-arrow-right"></i>', 
                        ['controller' => 'VocationalTrainingInstitutions', 'action' => 'index'],
                        ['class' => 'btn btn-sm btn-link', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>

        <!-- Special Skill Statistics Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-green">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?= h($statistics['special_skill']['total']) ?></h3>
                        <p class="stat-label">Special Skill Institutions</p>
                        <small class="text-muted">
                            <?= $statistics['special_skill']['active'] ?> active, 
                            <?= $statistics['special_skill']['pending_verification'] ?> pending
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link('Manage Special Skill <i class="fas fa-arrow-right"></i>', 
                        ['controller' => 'SpecialSkillSupportInstitutions', 'action' => 'index'],
                        ['class' => 'btn btn-sm btn-link', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>

        <!-- Acceptance Organizations Statistics Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-orange">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?= h($statistics['acceptance_org']['total']) ?></h3>
                        <p class="stat-label">Acceptance Organizations</p>
                        <small class="text-muted">
                            <?= $statistics['acceptance_org']['active'] ?> active
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link('Manage Organizations <i class="fas fa-arrow-right"></i>', 
                        ['controller' => 'AcceptanceOrganizations', 'action' => 'index'],
                        ['class' => 'btn btn-sm btn-link', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>

        <!-- Cooperative Associations Statistics Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-pink">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?= h($statistics['cooperative_assoc']['total']) ?></h3>
                        <p class="stat-label">Cooperative Associations</p>
                        <small class="text-muted">
                            <?= $statistics['cooperative_assoc']['active'] ?> active
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link('Manage Cooperatives <i class="fas fa-arrow-right"></i>', 
                        ['controller' => 'CooperativeAssociations', 'action' => 'index'],
                        ['class' => 'btn btn-sm btn-link', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>

        <!-- Active Stakeholders Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-success">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?= h($statistics['overall']['total_active']) ?></h3>
                        <p class="stat-label">Active Stakeholders</p>
                        <small class="text-success">Verified & Active</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Verifications Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-warning">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?= h($statistics['overall']['total_pending_verifications']) ?></h3>
                        <p class="stat-label">Pending Verifications</p>
                        <small class="text-warning">Awaiting Email Verification</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspended Stakeholders Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-danger">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?= h($statistics['overall']['total_suspended']) ?></h3>
                        <p class="stat-label">Suspended Stakeholders</p>
                        <small class="text-danger">Temporarily Inactive</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Stakeholder Distribution Pie Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Stakeholder Type Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="stakeholderDistributionChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution Bar Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Status Distribution by Type
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statusDistributionChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend Line Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Registration Trend (Last 6 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities and Pending Items -->
    <div class="row">
        <!-- Recent Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Recent Activities
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="activity-feed">
                        <?php if ($recentActivities->count() > 0): ?>
                            <?php foreach ($recentActivities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon activity-<?= h($activity->activity_type) ?>">
                                    <?php 
                                    $icon = 'circle';
                                    switch($activity->activity_type) {
                                        case 'registration': $icon = 'user-plus'; break;
                                        case 'verification': $icon = 'check-circle'; break;
                                        case 'login': $icon = 'sign-in-alt'; break;
                                        case 'profile_update': $icon = 'edit'; break;
                                        case 'admin_approval': $icon = 'thumbs-up'; break;
                                        case 'admin_rejection': $icon = 'thumbs-down'; break;
                                        case 'suspension': $icon = 'ban'; break;
                                    }
                                    ?>
                                    <i class="fas fa-<?= $icon ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <p class="activity-description">
                                        <?= h($activity->description) ?>
                                    </p>
                                    <small class="activity-time text-muted">
                                        <i class="far fa-clock"></i>
                                        <?= $activity->created->timeAgoInWords() ?>
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No recent activities</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Verifications & Approvals -->
        <div class="col-lg-6 mb-4">
            <!-- Pending Verifications -->
            <div class="card mb-3" id="pending-verifications">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-envelope-open-text"></i> Pending Email Verifications
                        <span class="badge badge-warning ml-2"><?= $pendingVerifications->count() ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($pendingVerifications->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Expires</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingVerifications as $verification): ?>
                                    <tr>
                                        <td>
                                            <strong><?= h($verification->full_name) ?></strong><br>
                                            <small class="text-muted"><?= h($verification->email) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= h($verification->institution_type === 'vocational_training' ? 'LPK' : 'Special Skill') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?= $verification->verification_token_expires->timeAgoInWords() ?></small>
                                        </td>
                                        <td>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-eye"></i>',
                                                ['controller' => 'Users', 'action' => 'view', $verification->id],
                                                ['class' => 'btn btn-sm btn-info', 'escape' => false, 'title' => 'View']
                                            ) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p>No pending verifications</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="card" id="pending-approvals">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check"></i> Pending Approvals
                        <span class="badge badge-info ml-2"><?= $pendingApprovals->count() ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($pendingApprovals->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Stakeholder</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingApprovals as $approval): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?= h(ucfirst($approval->approval_type)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?= h(ucfirst(str_replace('_', ' ', $approval->stakeholder_type))) ?></small><br>
                                            <small class="text-muted">ID: <?= h($approval->stakeholder_id) ?></small>
                                        </td>
                                        <td>
                                            <small><?= $approval->submitted_at->timeAgoInWords() ?></small>
                                        </td>
                                        <td>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-eye"></i>',
                                                ['controller' => 'AdminApprovalQueue', 'action' => 'review', $approval->id],
                                                ['class' => 'btn btn-sm btn-primary', 'escape' => false, 'title' => 'Review']
                                            ) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p>No pending approvals</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Stakeholder Distribution Pie Chart
var stakeholderDistCtx = document.getElementById('stakeholderDistributionChart').getContext('2d');
var stakeholderDistChart = new Chart(stakeholderDistCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($chartData['stakeholder_distribution']['labels']) ?>,
        datasets: [{
            data: <?= json_encode($chartData['stakeholder_distribution']['data']) ?>,
            backgroundColor: <?= json_encode($chartData['stakeholder_distribution']['colors']) ?>
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Status Distribution Bar Chart
var statusDistCtx = document.getElementById('statusDistributionChart').getContext('2d');
var statusDistChart = new Chart(statusDistCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartData['status_distribution']['labels']) ?>,
        datasets: [
            {
                label: 'LPK',
                data: <?= json_encode($chartData['status_distribution']['lpk']) ?>,
                backgroundColor: '#667eea'
            },
            {
                label: 'Special Skill',
                data: <?= json_encode($chartData['status_distribution']['special_skill']) ?>,
                backgroundColor: '#764ba2'
            },
            {
                label: 'Acceptance Org',
                data: <?= json_encode($chartData['status_distribution']['acceptance_org']) ?>,
                backgroundColor: '#f093fb'
            },
            {
                label: 'Cooperative Assoc',
                data: <?= json_encode($chartData['status_distribution']['cooperative_assoc']) ?>,
                backgroundColor: '#4facfe'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Monthly Trend Line Chart
var monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
var monthlyTrendChart = new Chart(monthlyTrendCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartData['monthly_trend']['labels']) ?>,
        datasets: [
            {
                label: 'LPK',
                data: <?= json_encode($chartData['monthly_trend']['lpk']) ?>,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4
            },
            {
                label: 'Special Skill',
                data: <?= json_encode($chartData['monthly_trend']['special_skill']) ?>,
                borderColor: '#764ba2',
                backgroundColor: 'rgba(118, 75, 162, 0.1)',
                tension: 0.4
            },
            {
                label: 'Acceptance Org',
                data: <?= json_encode($chartData['monthly_trend']['acceptance_org']) ?>,
                borderColor: '#f093fb',
                backgroundColor: 'rgba(240, 147, 251, 0.1)',
                tension: 0.4
            },
            {
                label: 'Cooperative Assoc',
                data: <?= json_encode($chartData['monthly_trend']['cooperative_assoc']) ?>,
                borderColor: '#4facfe',
                backgroundColor: 'rgba(79, 172, 254, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<style>
.stakeholder-dashboard .stat-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stakeholder-dashboard .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.stakeholder-dashboard .stat-card-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.stakeholder-dashboard .stat-card-blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
.stakeholder-dashboard .stat-card-green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
.stakeholder-dashboard .stat-card-orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
.stakeholder-dashboard .stat-card-pink { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
.stakeholder-dashboard .stat-card-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
.stakeholder-dashboard .stat-card-warning { background: linear-gradient(135deg, #ffc107 0%, #ff6f00 100%); color: white; }
.stakeholder-dashboard .stat-card-danger { background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); color: white; }

.stakeholder-dashboard .stat-icon {
    font-size: 3rem;
    opacity: 0.3;
    position: absolute;
    right: 20px;
    top: 20px;
}

.stakeholder-dashboard .stat-content {
    position: relative;
    z-index: 1;
}

.stakeholder-dashboard .stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0;
}

.stakeholder-dashboard .stat-label {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.stakeholder-dashboard .card-footer {
    background: rgba(0,0,0,0.05);
    border-top: none;
}

.stakeholder-dashboard .card-footer .btn-link {
    color: white;
    text-decoration: none;
    font-weight: 500;
}

.stakeholder-dashboard .card-footer .btn-link:hover {
    color: rgba(255,255,255,0.8);
    text-decoration: underline;
}

.activity-feed {
    max-height: 500px;
    overflow-y: auto;
}

.activity-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: start;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.activity-registration { background: #e3f2fd; color: #1976d2; }
.activity-verification { background: #e8f5e9; color: #388e3c; }
.activity-login { background: #fff3e0; color: #f57c00; }
.activity-profile_update { background: #f3e5f5; color: #7b1fa2; }
.activity-admin_approval { background: #e0f2f1; color: #00796b; }
.activity-admin_rejection { background: #ffebee; color: #c62828; }
.activity-suspension { background: #fce4ec; color: #c2185b; }

.activity-content {
    flex: 1;
}

.activity-description {
    margin: 0;
    font-size: 0.9rem;
    color: #333;
}

.activity-time {
    display: block;
    margin-top: 5px;
}
</style>
