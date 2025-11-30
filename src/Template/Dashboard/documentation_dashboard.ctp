<?php
/**
 * @var \App\View\AppView $this
 * @var array $stats
 * @var array $recentDocuments
 */
$this->assign('title', 'Documentation Dashboard');
?>

<div class="dashboard documentation content">
    <h1><?= __('Documentation Dashboard') ?></h1>
    
    <div class="row" style="margin: 20px 0;">
        <!-- Statistics Cards -->
        <div class="col-md-6" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($stats['totalDocuments']) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Total Documents</p>
                <a href="<?= $this->Url->build(['controller' => 'ApprenticeDocuments', 'action' => 'index']) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View All →</a>
            </div>
        </div>
        
        <div class="col-md-6" style="padding: 10px;">
            <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5em;"><?= number_format($stats['pendingDocuments']) ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Pending Documents</p>
                <a href="<?= $this->Url->build(['controller' => 'ApprenticeDocuments', 'action' => 'index', '?' => ['status' => 'pending']]) ?>" style="color: white; text-decoration: underline; font-size: 0.9em; margin-top: 10px; display: inline-block;">View Pending →</a>
            </div>
        </div>
    </div>
    
    <!-- Recent Documents -->
    <div class="row" style="margin: 20px 0;">
        <div class="col-md-12">
            <div class="card" style="padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                    <i class="fas fa-file-alt"></i> Recent Documents
                </h3>
                
                <?php if (!empty($recentDocuments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Apprentice</th>
                                    <th>Document Type</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentDocuments as $doc): ?>
                                    <tr>
                                        <td><?= h($doc->id) ?></td>
                                        <td>
                                            <?php if ($doc->has('apprentice')): ?>
                                                <?= h($doc->apprentice->fullname ? $doc->apprentice->fullname : 'N/A') ?>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($doc->document_type ? $doc->document_type : 'N/A') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $doc->status === 'pending' ? 'warning' : 'success' ?>">
                                                <?= h($doc->status ? $doc->status : 'N/A') ?>
                                            </span>
                                        </td>
                                        <td><?= h($doc->created ? $doc->created->format('Y-m-d H:i') : 'N/A') ?></td>
                                        <td>
                                            <?= $this->Html->link(__('View'), ['controller' => 'ApprenticeDocuments', 'action' => 'view', $doc->id], ['class' => 'btn btn-sm btn-info']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No recent documents found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
