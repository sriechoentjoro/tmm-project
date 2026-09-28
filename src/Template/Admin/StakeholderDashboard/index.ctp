<?php
/**
 * @var \App\View\AppView $this
 * @var array $statistics
 * @var \Cake\ORM\ResultSet $recentActivities
 * @var array $pendingApprovals
 * @var string|null $approvalProblem
 * @var array $waiting
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
                    <i class="fas fa-users-cog"></i> <?= __('Stakeholder Management Dashboard') ?>
                </h1>
                <p class="text-muted"><?= __('Monitor and manage all stakeholder types from one central location') ?></p>
            </div>
            <div class="col-md-6 text-right">
                <?= $this->Html->link(
                    '<i class="fas fa-download"></i> ' . __('Export Statistics'),
                    ['action' => 'exportStatistics'],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-question-circle"></i> ' . __('Help'),
                    // There is no Help controller. The guide this button wants
                    // is VocationalTrainingInstitutions::help(), whose docblock
                    // is literally "Stakeholder Management Guide".
                    ['prefix' => false, 'controller' => 'VocationalTrainingInstitutions', 'action' => 'help'],
                    ['class' => 'btn btn-info', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>

    <!-- Alert Cards for Pending Actions -->
    <?php if ($statistics['overall']['total_pending_verifications'] > 0 || count($pendingApprovals) > 0): ?>
    <div class="row mb-4">
        <?php if ($statistics['overall']['total_pending_verifications'] > 0): ?>
        <div class="col-md-6">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> <?= __('Pending Verifications') ?></h5>
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
        
        <?php if (count($pendingApprovals) > 0): ?>
        <div class="col-md-6">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-clock"></i> <?= __('Pending Approvals') ?></h5>
                <p>
                    <strong><?= count($pendingApprovals) ?></strong> <?= __('approval request(s) are on file.') ?>
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
                        <p class="stat-label"><?= __('Total Stakeholders') ?></p>
                        <small class="text-muted"><?= __('All types combined') ?></small>
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
                        <p class="stat-label"><?= __('LPK Institutions') ?></p>
                        <small class="text-muted">
                            <?= $statistics['lpk']['active'] ?> active, 
                            <?= $statistics['lpk']['pending_verification'] ?> pending
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link('Manage LPK <i class="fas fa-arrow-right"></i>', 
                        ['prefix' => false, 'controller' => 'VocationalTrainingInstitutions', 'action' => 'index'],
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
                        <p class="stat-label"><?= __('Special Skill Institutions') ?></p>
                        <small class="text-muted">
                            <?= $statistics['special_skill']['active'] ?> active, 
                            <?= $statistics['special_skill']['pending_verification'] ?> pending
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link('Manage Special Skill <i class="fas fa-arrow-right"></i>', 
                        ['prefix' => false, 'controller' => 'SpecialSkillSupportInstitutions', 'action' => 'index'],
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
                        <p class="stat-label"><?= __('Acceptance Organizations') ?></p>
                        <small class="text-muted">
                            <?= $statistics['acceptance_org']['active'] ?> active
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link('Manage Organizations <i class="fas fa-arrow-right"></i>', 
                        ['prefix' => false, 'controller' => 'AcceptanceOrganizations', 'action' => 'index'],
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
                        <p class="stat-label"><?= __('Cooperative Associations') ?></p>
                        <small class="text-muted">
                            <?= $statistics['cooperative_assoc']['active'] ?> active
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link('Manage Cooperatives <i class="fas fa-arrow-right"></i>', 
                        ['prefix' => false, 'controller' => 'CooperativeAssociations', 'action' => 'index'],
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
                        <p class="stat-label"><?= __('Active Stakeholders') ?></p>
                        <small class="text-success"><?= __('Verified & Active') ?></small>
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
                        <p class="stat-label"><?= __('Pending Verifications') ?></p>
                        <small class="text-warning"><?= __('Awaiting Email Verification') ?></small>
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
                        <p class="stat-label"><?= __('Suspended Stakeholders') ?></p>
                        <small class="text-danger"><?= __('Temporarily Inactive') ?></small>
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
                        <i class="fas fa-chart-pie"></i> <?= __('Stakeholder Type Distribution') ?>
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
                        <i class="fas fa-chart-bar"></i> <?= __('Status Distribution by Type') ?>
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
                        <i class="fas fa-chart-line"></i> <?= __('Registration Trend (Last 6 Months)') ?>
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
                        <i class="fas fa-history"></i> <?= __('Recent Activities') ?>
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
                                <p><?= __('No recent activities') ?></p>
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
                        <i class="fas fa-envelope-open-text"></i> <?= __('Registrations Not Finished') ?>
                        <span class="badge badge-warning ml-2"><?= count($waiting['rows']) ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!$waiting['statusKnown']): ?>
                        <div class="p-3 text-muted small">
                            <i class="fas fa-database"></i>
                            <?= __('This list cannot be built here. The registration status column is not on this installation, so there is no way to tell which registrations are still waiting.') ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($waiting['tokenProblem'] !== null): ?>
                        <div class="p-3 small alert alert-danger mb-0">
                            <strong><?= __('The verification links could not be read.') ?></strong><br>
                            <?= __('The institutions below are listed from their own records, but whether the link each one was sent is still live could not be established.') ?><br>
                            <span class="text-monospace"><?= h($waiting['tokenProblem']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (count($waiting['rows']) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><?= __('Institution') ?></th>
                                        <th><?= __('Type') ?></th>
                                        <th><?= __('Waiting since') ?></th>
                                        <th><?= __('Link') ?></th>
                                        <th><?= __('Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($waiting['rows'] as $row): ?>
                                    <tr>
                                        <td>
                                            <strong><?= h($row['name']) ?></strong><br>
                                            <small class="text-muted"><?= h($row['contact']) ?> &middot; <?= h($row['email']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= $row['type'] === 'lpk' ? __('LPK') : __('Special Skill') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?= $row['since'] ? h($row['since']->timeAgoInWords()) : __('unknown') ?></small>
                                        </td>
                                        <td>
                                            <?php if ($row['link'] === 'live'): ?>
                                                <span class="badge badge-success"><?= __('Live') ?></span><br>
                                                <small class="text-muted"><?= __('expires {0}', h($row['expires']->timeAgoInWords())) ?></small>
                                            <?php elseif ($row['link'] === 'expired'): ?>
                                                <span class="badge badge-danger"><?= __('Expired') ?></span><br>
                                                <small class="text-muted"><?= __('cannot finish without a new link') ?></small>
                                            <?php elseif ($row['link'] === 'used'): ?>
                                                <span class="badge badge-secondary"><?= __('Already used') ?></span><br>
                                                <small class="text-muted"><?= __('the link was followed but a password was never set') ?></small>
                                            <?php elseif ($row['link'] === 'none'): ?>
                                                <span class="badge badge-warning"><?= __('None sent') ?></span>
                                            <?php else: ?>
                                                <small class="text-muted"><?= __('not checked') ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['type'] === 'lpk'): ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['prefix' => false, 'controller' => 'VocationalTrainingInstitutions', 'action' => 'view', $row['id']],
                                                    ['class' => 'btn btn-sm btn-info', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-paper-plane"></i>',
                                                    ['prefix' => 'admin', 'controller' => 'LpkRegistration', 'action' => 'resendVerification', $row['id']],
                                                    ['class' => 'btn btn-sm btn-warning', 'escape' => false, 'title' => __('Send the verification link again')]
                                                ) ?>
                                            <?php else: ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['prefix' => false, 'controller' => 'SpecialSkillSupportInstitutions', 'action' => 'view', $row['id']],
                                                    ['class' => 'btn btn-sm btn-info', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                                <?php
                                                /**
                                                 * No resend button for this type.
                                                 *
                                                 * LpkRegistration::resendVerification() loads
                                                 * VocationalTrainingInstitutions and would fetch the
                                                 * wrong institution if handed a special skill id -
                                                 * both tables number from 1, so it would find one and
                                                 * mail the wrong address rather than fail. There is no
                                                 * resend action for this type; build one and its link
                                                 * belongs here.
                                                 */
                                                ?>
                                                <small class="text-muted"><?= __('no resend screen') ?></small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif ($waiting['statusKnown']): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="mb-1"><?= __('Every registration has been finished') ?></p>
                            <small><?= __('No institution is sitting at pending verification.') ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="card" id="pending-approvals">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check"></i> <?= __('Pending Approvals') ?>
                        <span class="badge badge-info ml-2"><?= count($pendingApprovals) ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($approvalProblem !== null): ?>
                        <div class="p-3 small alert alert-danger mb-0">
                            <strong><?= __('The approval queue could not be read.') ?></strong><br>
                            <span class="text-monospace"><?= h($approvalProblem) ?></span>
                        </div>
                    <?php elseif (count($pendingApprovals) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><?= __('Type') ?></th>
                                        <th><?= __('Stakeholder') ?></th>
                                        <th><?= __('Submitted') ?></th>
                                        <th><?= __('Actions') ?></th>
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
                                            <small><?= $approval->submitted_at ? h($approval->submitted_at->timeAgoInWords()) : __('unknown') ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            /**
                                             * No Review button.
                                             *
                                             * It linked to AdminApprovalQueue::review(), and no
                                             * AdminApprovalQueue controller exists under any prefix -
                                             * only the table class does. There is no review screen to
                                             * send anyone to, so the button went where nothing was;
                                             * inventing a destination would be worse than admitting
                                             * there isn't one.
                                             */
                                            ?>
                                            <small class="text-muted">
                                                <?= __('No review screen yet') ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-3 text-muted small">
                            <i class="fas fa-info-circle"></i>
                            <?= __('Nothing files into this queue.') ?>
                            <?= __('No registration in this system waits for an admin decision: an institution goes live by following the link it was emailed and choosing a password, with no approval step in between. So this panel being empty says nothing about the state of the system - the list above is the one that does.') ?>
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

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
