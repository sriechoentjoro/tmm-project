<?php
/**
 * Creates the view templates for the menu-linked actions added by
 * add_menu_actions.php. Skips existing files; safe to re-run.
 */

function tpl($title, $bodyHtml)
{
    return "<?php\n/**\n * @var \\App\\View\\AppView \$this\n */\n?>\n"
        . "<div class=\"index-header\" style=\"margin-bottom: 20px;\">\n"
        . "    <h2 style=\"margin: 0;\"><?= __('$title') ?></h2>\n"
        . "</div>\n"
        . $bodyHtml;
}

function thead(array $labels)
{
    $cells = '';
    foreach ($labels as $l) {
        $cells .= "                <th style=\"padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;\"><?= __('$l') ?></th>\n";
    }
    return "    <table class=\"table\" style=\"border-collapse: collapse; width: 100%;\">\n"
        . "        <thead style=\"background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);\">\n"
        . "            <tr>\n$cells            </tr>\n        </thead>\n        <tbody>\n";
}

$tfoot = "        </tbody>\n    </table>\n";

$files = [];

$files['VocationalTrainingInstitutions/verify.ctp'] = tpl('LPK Verification', "
<div class=\"table-scroll-wrapper\" style=\"overflow-x: auto;\">
" . thead(['Actions', 'ID', 'Name', 'Status', 'Registered', 'Registered At', 'Email']) . "
            <?php foreach (\$institutions as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px; white-space: nowrap;\">
                    <?= \$this->Html->link(__('View'), ['action' => 'view', \$row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                    <?= \$this->Html->link(__('Edit'), ['action' => 'edit', \$row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['name']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['status']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= \$row['is_registered'] ? '<span style=\"color:#28a745;\">&#10004; Yes</span>' : '<span style=\"color:#dc3545;\">&#10008; No</span>' ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['registered_at']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['email']) ?></td>
            </tr>
            <?php endforeach; ?>
$tfoot</div>
<div class=\"paginator\" style=\"margin-top: 15px;\">
    <ul class=\"pagination\">
        <?= \$this->Paginator->prev('< ' . __('previous')) ?>
        <?= \$this->Paginator->numbers() ?>
        <?= \$this->Paginator->next(__('next') . ' >') ?>
    </ul>
</div>
");

$files['ApprenticeOrders/statistics.ctp'] = tpl('Order Statistics', "
<div class=\"row\" style=\"display: flex; gap: 20px; margin-bottom: 25px;\">
    <div class=\"card\" style=\"background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; min-width: 220px;\">
        <h3 style=\"margin: 0; font-size: 2.2em;\"><?= number_format(\$totals['orders']) ?></h3>
        <p style=\"margin: 5px 0 0 0; opacity: 0.9;\"><?= __('Total Orders') ?></p>
    </div>
    <div class=\"card\" style=\"background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px; min-width: 220px;\">
        <h3 style=\"margin: 0; font-size: 2.2em;\"><?= number_format(\$totals['quantity']) ?></h3>
        <p style=\"margin: 5px 0 0 0; opacity: 0.9;\"><?= __('Total Requested Trainees') ?></p>
    </div>
</div>
" . thead(['Status', 'Orders', 'Quantity', 'Male', 'Female']) . "
            <?php foreach (\$byStatus as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px;\"><?= h(\$row['status'] ?: '(none)') ?></td>
                <td style=\"padding: 10px 12px;\"><?= number_format(\$row['order_count']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= number_format((int)\$row['total_quantity']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= number_format((int)\$row['total_male']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= number_format((int)\$row['total_female']) ?></td>
            </tr>
            <?php endforeach; ?>
$tfoot");

$files['Candidates/promote_to_trainee.ctp'] = tpl('Promote to Trainee', "
<p style=\"color: #6c757d;\"><?= __('Candidates below have not been promoted to trainee yet. Open a candidate to review and process the promotion.') ?></p>
<div class=\"table-scroll-wrapper\" style=\"overflow-x: auto;\">
" . thead(['Actions', 'ID', 'Code', 'Name', 'Status']) . "
            <?php foreach (\$candidates as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px; white-space: nowrap;\">
                    <?= \$this->Html->link(__('View'), ['action' => 'view', \$row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                </td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['candidate_code']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['name']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(isset(\$row['status']) ? \$row['status'] : '') ?></td>
            </tr>
            <?php endforeach; ?>
$tfoot</div>
<div class=\"paginator\" style=\"margin-top: 15px;\">
    <ul class=\"pagination\">
        <?= \$this->Paginator->prev('< ' . __('previous')) ?>
        <?= \$this->Paginator->numbers() ?>
        <?= \$this->Paginator->next(__('next') . ' >') ?>
    </ul>
</div>
");

$files['Element/promotion_history.ctp'] = tpl('Promotion History', "
" . thead(['ID', 'Source', 'Record ID', 'Promoted By', 'Notes', 'Date']) . "
            <?php foreach (\$histories as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['source_table']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['source_id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['promoted_by_id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['notes']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['created']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty(\$histories)): ?>
            <tr><td colspan=\"6\" style=\"padding: 15px; color: #6c757d;\"><?= __('No promotion history recorded yet.') ?></td></tr>
            <?php endif; ?>
$tfoot");

$files['Trainings/location_shift.ctp'] = tpl('Location Shifting', "
<p style=\"color: #6c757d;\"><?= __('Training batches and their current locations. Edit a batch to shift its location.') ?></p>
" . thead(['ID', 'Batch Code', 'Batch Name', 'Location', 'Start', 'End', 'Status']) . "
            <?php foreach (\$batches as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['batch_code']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['batch_name']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['training_location']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['start_date']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['end_date']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['status']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty(\$batches)): ?>
            <tr><td colspan=\"7\" style=\"padding: 15px; color: #6c757d;\"><?= __('No training batches yet.') ?></td></tr>
            <?php endif; ?>
$tfoot");

$files['Trainees/additional_training.ctp'] = tpl('Additional Training', "
" . thead(['ID', 'Title', 'Start', 'End', 'Location', 'Status']) . "
            <?php foreach (\$additionalTrainings as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['title']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['start_date']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['end_date']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['location']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['status']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty(\$additionalTrainings)): ?>
            <tr><td colspan=\"6\" style=\"padding: 15px; color: #6c757d;\"><?= __('No additional trainings recorded yet.') ?></td></tr>
            <?php endif; ?>
$tfoot");

$files['Trainees/promote_to_apprentice.ctp'] = tpl('Promote to Apprentice', "
<p style=\"color: #6c757d;\"><?= __('Trainees below passed training and are eligible for promotion. Open a trainee to review and process the promotion.') ?></p>
<div class=\"table-scroll-wrapper\" style=\"overflow-x: auto;\">
" . thead(['Actions', 'ID', 'TMM Code', 'Name', 'Training Pass', 'Status']) . "
            <?php foreach (\$trainees as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px; white-space: nowrap;\">
                    <?= \$this->Html->link(__('View'), ['action' => 'view', \$row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                </td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['tmm_code']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['name']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= \$row['is_training_pass'] ? '&#10004;' : '&#10008;' ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(isset(\$row['status']) ? \$row['status'] : '') ?></td>
            </tr>
            <?php endforeach; ?>
$tfoot</div>
<div class=\"paginator\" style=\"margin-top: 15px;\">
    <ul class=\"pagination\">
        <?= \$this->Paginator->prev('< ' . __('previous')) ?>
        <?= \$this->Paginator->numbers() ?>
        <?= \$this->Paginator->next(__('next') . ' >') ?>
    </ul>
</div>
");

$files['Trainees/promotion_checklist.ctp'] = tpl('Promotion Checklist', "
<div class=\"table-scroll-wrapper\" style=\"overflow-x: auto;\">
" . thead(['Actions', 'ID', 'TMM Code', 'Name', 'Candidate Pass', 'Training Pass', 'Apprenticeship Pass']) . "
            <?php foreach (\$trainees as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px; white-space: nowrap;\">
                    <?= \$this->Html->link(__('View'), ['action' => 'view', \$row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                </td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['tmm_code']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['name']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= \$row['is_candidate_pass'] ? '<span style=\"color:#28a745;\">&#10004;</span>' : '<span style=\"color:#dc3545;\">&#10008;</span>' ?></td>
                <td style=\"padding: 10px 12px;\"><?= \$row['is_training_pass'] ? '<span style=\"color:#28a745;\">&#10004;</span>' : '<span style=\"color:#dc3545;\">&#10008;</span>' ?></td>
                <td style=\"padding: 10px 12px;\"><?= \$row['is_apprenticeship_pass'] ? '<span style=\"color:#28a745;\">&#10004;</span>' : '<span style=\"color:#dc3545;\">&#10008;</span>' ?></td>
            </tr>
            <?php endforeach; ?>
$tfoot</div>
<div class=\"paginator\" style=\"margin-top: 15px;\">
    <ul class=\"pagination\">
        <?= \$this->Paginator->prev('< ' . __('previous')) ?>
        <?= \$this->Paginator->numbers() ?>
        <?= \$this->Paginator->next(__('next') . ' >') ?>
    </ul>
</div>
");

$files['TraineeTrainingTestScores/daily.ctp'] = tpl('Daily Score Entry', "
<p style=\"color: #6c757d;\">
    <?php if (\$latestDate): ?>
        <?= __('Showing scores for the most recent test date:') ?> <strong><?= h(\$latestDate) ?></strong>
    <?php else: ?>
        <?= __('No scores recorded yet.') ?>
    <?php endif; ?>
    &nbsp;<?= \$this->Html->link(__('Enter New Score'), ['action' => 'add'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
</p>
" . thead(['ID', 'Trainee', 'Competency', 'Score', 'Grade']) . "
            <?php foreach (\$scores as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['trainee_id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['master_training_competency_id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['score']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['master_training_test_score_grade_id']) ?></td>
            </tr>
            <?php endforeach; ?>
$tfoot");

$files['TraineeTrainingTestScores/report.ctp'] = tpl('Pass/Fail Report', "
<p style=\"color: #6c757d;\"><?= __('Average score per trainee across all recorded tests.') ?></p>
" . thead(['Trainee ID', 'Tests Taken', 'Average', 'Min', 'Max']) . "
            <?php foreach (\$rows as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px;\"><?= h(\$row['trainee_id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= number_format(\$row['tests']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(round(\$row['avg_score'], 2)) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['min_score']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['max_score']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty(\$rows)): ?>
            <tr><td colspan=\"5\" style=\"padding: 15px; color: #6c757d;\"><?= __('No scores recorded yet.') ?></td></tr>
            <?php endif; ?>
$tfoot");

$files['CandidateDocuments/submission_checklist.ctp'] = tpl('Candidate Document Submission Checklist', "
" . thead(['ID', 'Candidate ID', 'Document ID', 'Submitted', 'Submission Date']) . "
            <?php foreach (\$checklist as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['applicant_id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['document_id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= \$row['submitted'] ? '<span style=\"color:#28a745;\">&#10004;</span>' : '<span style=\"color:#dc3545;\">&#10008;</span>' ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['submission_date']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty(\$checklist)): ?>
            <tr><td colspan=\"5\" style=\"padding: 15px; color: #6c757d;\"><?= __('No checklist entries yet.') ?></td></tr>
            <?php endif; ?>
$tfoot");

$installmentsBody = "
<div class=\"table-scroll-wrapper\" style=\"overflow-x: auto;\">
" . thead(['Actions', 'ID', 'Trainee', 'Amount', 'Payment Date', 'Accumulated', 'Unpaid', 'Paid Off']) . "
            <?php foreach (\$installments as \$row): ?>
            <tr style=\"border-bottom: 1px solid #e9ecef;\">
                <td style=\"padding: 10px 12px; white-space: nowrap;\">
                    <?= \$this->Html->link(__('View'), ['action' => 'view', \$row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                </td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['trainee_id']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['payment_amount']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['payment_date']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['payment_accummulated']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= h(\$row['unpaid_amount']) ?></td>
                <td style=\"padding: 10px 12px;\"><?= \$row['is_paid_off'] ? '<span style=\"color:#28a745;\">&#10004;</span>' : '<span style=\"color:#dc3545;\">&#10008;</span>' ?></td>
            </tr>
            <?php endforeach; ?>
$tfoot</div>
<div class=\"paginator\" style=\"margin-top: 15px;\">
    <ul class=\"pagination\">
        <?= \$this->Paginator->prev('< ' . __('previous')) ?>
        <?= \$this->Paginator->numbers() ?>
        <?= \$this->Paginator->next(__('next') . ' >') ?>
    </ul>
</div>
";
$files['TraineeInstallments/tracking.ctp'] = tpl('Payment Tracking', $installmentsBody);
$files['TraineeInstallments/receipts.ctp'] = tpl('Receipt Management', $installmentsBody);

foreach ($files as $rel => $content) {
    $path = __DIR__ . '/src/Template/' . $rel;
    if (file_exists($path)) {
        echo "skip   $rel\n";
        continue;
    }
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0755, true);
    }
    file_put_contents($path, $content);
    echo "create $rel\n";
}
echo "done\n";
