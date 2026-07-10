<?php
/**
 * Documentation Dashboard - TMM Documentation role
 *
 * Pre-departure document management for trainees going to Japan.
 *
 * @var \App\View\AppView $this
 * @var array $stats
 * @var array $documentReadiness
 * @var array $categoryProgress
 * @var array $recentSubmissions
 * @var array $flights
 * @var bool $hasUpcomingFlights
 */
$this->assign('title', 'Documentation Dashboard');

$formatDatetime = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Time) {
        return $value->format('Y-m-d H:i');
    }
    return $value ? (string)$value : 'N/A';
};

$progressColor = function ($percent) {
    if ($percent >= 100) {
        return '#43e97b';
    }
    if ($percent >= 50) {
        return '#4facfe';
    }
    if ($percent > 0) {
        return '#f5a623';
    }
    return '#ced4da';
};
?>

<div class="dashboard documentation-dashboard">
    <div class="row">
        <div class="col-md-12">
            <h2><i class="fa fa-folder-open"></i> Documentation Dashboard</h2>
            <p class="text-muted">Pre-departure document management for trainees going to Japan 🇯🇵</p>
        </div>
    </div>

    <!-- Statistics cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stat-card stat-card-purple">
                <h3><?= number_format($stats['totalTrainees']) ?></h3>
                <p>Trainees</p>
                <i class="fa fa-users"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-blue">
                <h3><?= number_format($stats['totalSubmissions']) ?></h3>
                <p>Document Submissions</p>
                <i class="fa fa-files-o"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-green">
                <h3><?= number_format($stats['submittedDocuments']) ?></h3>
                <p>Submitted</p>
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-pink">
                <h3><?= number_format($stats['pendingDocuments']) ?></h3>
                <p>Pending Review</p>
                <i class="fa fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <!-- Pre-departure pipeline -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-road"></i> Pre-Departure Pipeline</h4>
                </div>
                <div class="card-body">
                    <div class="pipeline">
                        <?= $this->Html->link(
                            '<span class="pipeline-icon"><i class="fa fa-file-text"></i></span>' .
                            '<span class="pipeline-count">' . number_format($stats['totalSubmissions']) . '</span>' .
                            '<span class="pipeline-label">Submission Docs</span>',
                            ['controller' => 'TraineeSubmissionDocuments', 'action' => 'index'],
                            ['escape' => false, 'class' => 'pipeline-stage']
                        ) ?>
                        <span class="pipeline-arrow"><i class="fa fa-angle-right"></i></span>
                        <?= $this->Html->link(
                            '<span class="pipeline-icon"><i class="fa fa-id-card-o"></i></span>' .
                            '<span class="pipeline-count">' . number_format($stats['passportRecords']) . '</span>' .
                            '<span class="pipeline-label">Passports</span>',
                            ['controller' => 'TraineeRecordPasports', 'action' => 'index'],
                            ['escape' => false, 'class' => 'pipeline-stage']
                        ) ?>
                        <span class="pipeline-arrow"><i class="fa fa-angle-right"></i></span>
                        <?= $this->Html->link(
                            '<span class="pipeline-icon"><i class="fa fa-heartbeat"></i></span>' .
                            '<span class="pipeline-count">' . number_format($stats['medicalCheckUps']) . '</span>' .
                            '<span class="pipeline-label">Medical Check-Ups</span>',
                            ['controller' => 'TraineeRecordMedicalCheckUps', 'action' => 'index'],
                            ['escape' => false, 'class' => 'pipeline-stage']
                        ) ?>
                        <span class="pipeline-arrow"><i class="fa fa-angle-right"></i></span>
                        <?= $this->Html->link(
                            '<span class="pipeline-icon"><i class="fa fa-file-text-o"></i></span>' .
                            '<span class="pipeline-count">' . number_format($stats['coeVisaRecords']) . '</span>' .
                            '<span class="pipeline-label">COE / Visa</span>',
                            ['controller' => 'TraineeRecordCoeVisas', 'action' => 'index'],
                            ['escape' => false, 'class' => 'pipeline-stage']
                        ) ?>
                        <span class="pipeline-arrow"><i class="fa fa-angle-right"></i></span>
                        <?= $this->Html->link(
                            '<span class="pipeline-icon"><i class="fa fa-plane"></i></span>' .
                            '<span class="pipeline-count">' . number_format($stats['totalFlights']) . '</span>' .
                            '<span class="pipeline-label">Tickets & Flights</span>',
                            ['controller' => 'Tickets', 'action' => 'index'],
                            ['escape' => false, 'class' => 'pipeline-stage']
                        ) ?>
                        <span class="pipeline-arrow"><i class="fa fa-angle-right"></i></span>
                        <span class="pipeline-stage pipeline-stage-final">
                            <span class="pipeline-icon">🇯🇵</span>
                            <span class="pipeline-count">&nbsp;</span>
                            <span class="pipeline-label">Depart to Japan</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Readiness + category progress -->
    <div class="row mt-4">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fa fa-tasks"></i> Document Readiness per Trainee</h4>
                    <?= $this->Html->link('Checklist',
                        ['controller' => 'TraineeSubmissionDocuments', 'action' => 'checklist'],
                        ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($documentReadiness)): ?>
                        <?php foreach ($documentReadiness as $row): ?>
                            <div class="readiness-row">
                                <div class="readiness-header">
                                    <span class="readiness-name">
                                        <?= h($row['name']) ?>
                                        <?php if (!empty($row['tmm_code'])): ?>
                                            <small class="text-muted">(<?= h($row['tmm_code']) ?>)</small>
                                        <?php endif; ?>
                                    </span>
                                    <span class="readiness-count">
                                        <?= (int)$row['submitted'] ?> / <?= (int)$row['required'] ?> required docs
                                        <?php if ($row['percent'] >= 100): ?>
                                            <span class="badge badge-success">Complete</span>
                                        <?php elseif ($row['percent'] > 0): ?>
                                            <span class="badge badge-warning">In Progress</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Not Started</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: <?= (int)$row['percent'] ?>%; background: <?= $progressColor($row['percent']) ?>;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-3x mb-3"></i><br>
                            No trainees found.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-pie-chart"></i> Submissions by Category</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($categoryProgress)): ?>
                        <?php foreach ($categoryProgress as $category): ?>
                            <div class="readiness-row">
                                <div class="readiness-header">
                                    <span class="readiness-name"><?= h($category['name']) ?></span>
                                    <span class="readiness-count">
                                        <?= (int)$category['submitted'] ?> / <?= (int)$category['expected'] ?>
                                        <small class="text-muted">(<?= (int)$category['documentTypes'] ?> doc types)</small>
                                    </span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: <?= (int)$category['percent'] ?>%; background: <?= $progressColor($category['percent']) ?>;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-3x mb-3"></i><br>
                            No document categories found.
                        </p>
                    <?php endif; ?>
                    <p class="text-muted" style="font-size: 0.85em; margin-top: 15px;">
                        <i class="fa fa-info-circle"></i>
                        Expected = required document types &times; number of trainees.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent submissions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fa fa-history"></i> Recent Document Submissions</h4>
                    <div>
                        <?= $this->Html->link('<i class="fa fa-plus-circle"></i> Add Submission',
                            ['controller' => 'TraineeSubmissionDocuments', 'action' => 'add'],
                            ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
                        <?= $this->Html->link('<i class="fa fa-th-list"></i> View All',
                            ['controller' => 'TraineeSubmissionDocuments', 'action' => 'index'],
                            ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentSubmissions)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Trainee</th>
                                        <th>Document</th>
                                        <th>Status</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSubmissions as $submission): ?>
                                        <?php
                                        $statusName = isset($submission['status_name']) ? $submission['status_name'] : null;
                                        $badgeClass = 'secondary';
                                        if ($statusName === 'Submitted') {
                                            $badgeClass = 'success';
                                        } elseif ($statusName === 'Pending') {
                                            $badgeClass = 'warning';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= h($submission['id']) ?></td>
                                            <td><?= h($submission['trainee_name'] ?: 'N/A') ?></td>
                                            <td><?= h($submission['document_title'] ?: 'N/A') ?></td>
                                            <td>
                                                <span class="badge badge-<?= $badgeClass ?>"><?= h($statusName ?: 'N/A') ?></span>
                                            </td>
                                            <td><?= h($formatDatetime($submission['uploaded_at'] ?: $submission['created'])) ?></td>
                                            <td>
                                                <?= $this->Html->link('<i class="fa fa-eye"></i>',
                                                    ['controller' => 'TraineeSubmissionDocuments', 'action' => 'view', $submission['id']],
                                                    ['escape' => false, 'class' => 'btn btn-sm btn-info', 'title' => 'View']) ?>
                                                <?= $this->Html->link('<i class="fa fa-edit"></i>',
                                                    ['controller' => 'TraineeSubmissionDocuments', 'action' => 'edit', $submission['id']],
                                                    ['escape' => false, 'class' => 'btn btn-sm btn-warning', 'title' => 'Edit']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-3x mb-3"></i><br>
                            No document submissions yet.
                            <?= $this->Html->link('Add the first submission',
                                ['controller' => 'TraineeSubmissionDocuments', 'action' => 'add']) ?>.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Departures + quick actions -->
    <div class="row mt-4">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>
                        <i class="fa fa-plane"></i>
                        <?= $hasUpcomingFlights ? 'Upcoming Departures' : 'Recent Departures' ?>
                    </h4>
                    <?= $this->Html->link('Departure Management',
                        ['controller' => 'Tickets', 'action' => 'departures'],
                        ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($flights)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Flight</th>
                                        <th>Airline</th>
                                        <th>Route</th>
                                        <th>Departure</th>
                                        <th>Arrival</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($flights as $flight): ?>
                                        <tr>
                                            <td><strong><?= h($flight['flight_number']) ?></strong></td>
                                            <td><?= h($flight['airline_name'] ?: ($flight['airline_code'] ?: 'N/A')) ?></td>
                                            <td>
                                                <?php
                                                $from = $flight['departure_airport'] ?: $flight['departure_city'];
                                                $to = $flight['arrival_airport'] ?: $flight['arrival_city'];
                                                ?>
                                                <?= h($from ?: '?') ?> <i class="fa fa-long-arrow-right"></i> <?= h($to ?: '?') ?>
                                            </td>
                                            <td><?= h($formatDatetime($flight['departure_datetime'])) ?></td>
                                            <td><?= h($formatDatetime($flight['arrival_datetime'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-plane fa-3x mb-3"></i><br>
                            No flight schedules found.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-bolt"></i> Quick Actions</h4>
                </div>
                <div class="card-body quick-actions">
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> Add Submission Document',
                        ['controller' => 'TraineeSubmissionDocuments', 'action' => 'add'],
                        ['escape' => false, 'class' => 'btn btn-success btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-check-square-o"></i> Document Checklist',
                        ['controller' => 'TraineeSubmissionDocuments', 'action' => 'checklist'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-line-chart"></i> Progress Tracking',
                        ['controller' => 'TraineeSubmissionDocuments', 'action' => 'progress'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-id-card-o"></i> Passport Records',
                        ['controller' => 'TraineeRecordPasports', 'action' => 'index'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-file-text-o"></i> COE &amp; Visa Records',
                        ['controller' => 'TraineeRecordCoeVisas', 'action' => 'index'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-heartbeat"></i> Medical Check-Ups',
                        ['controller' => 'TraineeRecordMedicalCheckUps', 'action' => 'index'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-plane"></i> Tickets &amp; Flights',
                        ['controller' => 'Tickets', 'action' => 'index'],
                        ['escape' => false, 'class' => 'btn btn-info btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-folder-open-o"></i> Departure Documents',
                        ['controller' => 'TraineeDocuments', 'action' => 'departure'],
                        ['escape' => false, 'class' => 'btn btn-secondary btn-block']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    color: white;
    transition: transform 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.stat-card-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card-blue   { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card-green  { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.stat-card-pink   { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card h3 {
    font-size: 36px;
    margin: 0;
    font-weight: bold;
}
.stat-card p {
    margin: 8px 0 0 0;
    font-size: 14px;
    opacity: 0.95;
}
.stat-card i {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 48px;
    opacity: 0.25;
}
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.card-header h4 { margin: 0; }
.card-body { padding: 20px; }
.pipeline {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 5px;
}
.pipeline-stage {
    flex: 1;
    min-width: 110px;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px 10px;
    border-radius: 8px;
    background: #f8f9fa;
    text-decoration: none;
    color: #333;
    transition: transform 0.2s, box-shadow 0.2s;
}
a.pipeline-stage:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
    color: #333;
}
.pipeline-stage-final {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.pipeline-icon { font-size: 24px; margin-bottom: 5px; }
.pipeline-count { font-size: 22px; font-weight: bold; }
.pipeline-label { font-size: 12px; text-align: center; opacity: 0.85; }
.pipeline-arrow {
    display: flex;
    align-items: center;
    font-size: 24px;
    color: #adb5bd;
}
.readiness-row { margin-bottom: 18px; }
.readiness-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    flex-wrap: wrap;
}
.readiness-name { font-weight: bold; }
.readiness-count { font-size: 0.9em; color: #6c757d; }
.progress-track {
    height: 10px;
    background: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 5px;
    transition: width 0.5s ease;
}
.badge {
    padding: 4px 8px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
}
.badge-success { background: #28a745; color: white; }
.badge-warning { background: #ffc107; color: #333; }
.badge-secondary { background: #6c757d; color: white; }
.quick-actions .btn { margin-bottom: 10px; text-align: left; }
.btn-block { display: block; width: 100%; }
.d-flex { display: flex; }
.justify-content-between { justify-content: space-between; }
.align-items-center { align-items: center; }
</style>
