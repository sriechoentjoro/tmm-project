<div class="dashboard management-dashboard">
    <div class="row">
        <div class="col-md-12">
            <h2><i class="fa fa-bar-chart"></i> Management Dashboard</h2>
            <p class="text-muted">Read-only system overview and analytics</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="stat-box stat-box-purple">
                <div class="stat-icon"><i class="fa fa-user-plus"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['totalCandidates']) ?></h3>
                    <p>Total Candidates</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box stat-box-teal">
                <div class="stat-icon"><i class="fa fa-graduation-cap"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['totalTrainees']) ?></h3>
                    <p>Total Trainees</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box stat-box-orange">
                <div class="stat-icon"><i class="fa fa-building"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['totalOrganizations']) ?></h3>
                    <p>Organizations</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-pie-chart"></i> Candidate Overview</h4>
                </div>
                <div class="card-body">
                    <?php
                    $overviewData = !empty($candidatesByMonth) ? $candidatesByMonth : ($candidatesByInstitution ?? []);
                    $overviewTitle = !empty($candidatesByMonth) ? __('Candidates by Month') : __('Candidates by Institution');
                    $overviewMax = 1;
                    foreach ($overviewData as $r) { $overviewMax = max($overviewMax, (int)$r['count']); }
                    ?>
                    <?php if (empty($overviewData)): ?>
                        <div class="chart-placeholder text-center">
                            <i class="fa fa-line-chart fa-5x text-muted"></i>
                            <p class="mt-3 text-muted"><?= __('No candidate data available yet') ?></p>
                        </div>
                    <?php else: ?>
                        <small class="text-muted d-block mb-2"><?= h($overviewTitle) ?></small>
                        <div class="bar-chart">
                            <?php foreach ($overviewData as $r): ?>
                            <div class="bar-row">
                                <div class="bar-label" title="<?= h($r['label']) ?>"><?= h($r['label']) ?></div>
                                <div class="bar-track">
                                    <div class="bar-fill bar-fill-purple" style="width: <?= round((int)$r['count'] / $overviewMax * 100) ?>%;"></div>
                                </div>
                                <div class="bar-value"><?= number_format((int)$r['count']) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-pie-chart"></i> Trainee Status</h4>
                </div>
                <div class="card-body">
                    <?php
                    $statusMax = 1; $statusTotal = 0;
                    foreach ($traineesByStatus as $r) { $statusMax = max($statusMax, (int)$r['count']); $statusTotal += (int)$r['count']; }
                    ?>
                    <?php if (empty($traineesByStatus)): ?>
                        <div class="chart-placeholder text-center">
                            <i class="fa fa-pie-chart fa-5x text-muted"></i>
                            <p class="mt-3 text-muted"><?= __('No trainee data available yet') ?></p>
                        </div>
                    <?php else: ?>
                        <small class="text-muted d-block mb-2"><?= __('Trainees by Status') ?> (<?= __('total') ?> <?= number_format($statusTotal) ?>)</small>
                        <div class="bar-chart">
                            <?php foreach ($traineesByStatus as $i => $r): ?>
                            <div class="bar-row">
                                <div class="bar-label" title="<?= h($r['label']) ?>"><?= h($r['label']) ?></div>
                                <div class="bar-track">
                                    <div class="bar-fill <?= $i % 2 === 0 ? 'bar-fill-teal' : 'bar-fill-orange' ?>" style="width: <?= round((int)$r['count'] / $statusMax * 100) ?>%;"></div>
                                </div>
                                <div class="bar-value"><?= number_format((int)$r['count']) ?> (<?= $statusTotal > 0 ? round((int)$r['count'] / $statusTotal * 100) : 0 ?>%)</div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-info-circle"></i> Quick Links</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <?= $this->Html->link(
                                '<i class="fa fa-users fa-3x"></i><br><br>View Candidates',
                                ['controller' => 'Candidates', 'action' => 'index'],
                                ['escape' => false, 'class' => 'quick-link-box']
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Html->link(
                                '<i class="fa fa-graduation-cap fa-3x"></i><br><br>View Trainees',
                                ['controller' => 'Trainees', 'action' => 'index'],
                                ['escape' => false, 'class' => 'quick-link-box']
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Html->link(
                                '<i class="fa fa-building fa-3x"></i><br><br>View Organizations',
                                ['controller' => 'AcceptanceOrganizations', 'action' => 'index'],
                                ['escape' => false, 'class' => 'quick-link-box']
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Html->link(
                                '<i class="fa fa-file-text fa-3x"></i><br><br>View Reports',
                                ['controller' => 'Reports', 'action' => 'index'],
                                ['escape' => false, 'class' => 'quick-link-box']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-box {
    display: flex;
    align-items: center;
    padding: 20px;
    border-radius: 8px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);

}
.stat-box-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

}
.stat-box-teal {
    background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);

}
.stat-box-orange {
    background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);

}
.stat-icon {
    font-size: 48px;
    margin-right: 20px;
    opacity: 0.9;

}
.stat-info h3 {
    font-size: 32px;
    margin: 0;
    font-weight: bold;

}
.stat-info p {
    margin: 5px 0 0 0;
    font-size: 14px;
    opacity: 0.9;

}
.chart-placeholder {
    padding: 40px;
    background: #f8f9fa;
    border-radius: 8px;
}
.bar-chart {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 10px 0;
}
.bar-row {
    display: flex;
    align-items: center;
    gap: 10px;
}
.bar-label {
    flex: 0 0 130px;
    font-size: 12px;
    color: #555;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: right;
}
.bar-track {
    flex: 1;
    background: #f0f1f5;
    border-radius: 4px;
    height: 18px;
    overflow: hidden;
}
.bar-fill {
    height: 100%;
    border-radius: 4px;
    min-width: 2px;
    transition: width 0.4s ease;
}
.bar-fill-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bar-fill-teal { background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); }
.bar-fill-orange { background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); }
.bar-value {
    flex: 0 0 70px;
    font-size: 12px;
    font-weight: bold;
    color: #333;
}
.quick-link-box {
    display: block;
    text-align: center;
    padding: 30px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);

}
.quick-link-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    text-decoration: none;
    color: white;

}
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;

}
.card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;

}</style>

