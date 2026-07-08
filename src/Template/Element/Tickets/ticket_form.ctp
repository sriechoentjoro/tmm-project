<?php
/**
 * Shared rich ticket form (add + edit)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ticket $ticket
 * @var array $traineeOptions   [id => "Name (TMM-code)"]
 * @var array $typeOptions      [id => title]
 * @var array $statusOptions    [id => title]
 * @var array $currencyOptions  [id => "IDR — Rupiah"]
 * @var bool $isEdit
 * @var array $linkedFlights    (edit only)
 */
$isEdit = !empty($isEdit);

// Keep a reference to a trainee that no longer exists in the trainees table,
// so editing the ticket does not silently drop or change it.
if ($isEdit && $ticket->trainee_id && !isset($traineeOptions[$ticket->trainee_id])) {
    $traineeOptions[$ticket->trainee_id] = '#' . $ticket->trainee_id . ' (' . __('unknown trainee') . ')';
}

$formatDatetime = function ($value) {
    $timestamp = $value ? strtotime($value) : false;
    return $timestamp ? date('d M Y H:i', $timestamp) : '-';
};
?>

<div class="ticket-form-page">
    <div class="page-header">
        <div>
            <h2>
                <i class="fa fa-ticket"></i>
                <?= $isEdit ? __('Edit Ticket') . ' <span class="ticket-id">#' . h($ticket->id) . '</span>' : __('Add Ticket') ?>
            </h2>
            <p class="text-muted"><?= __('Departure ticket for a trainee going to Japan') ?> 🇯🇵</p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('Back to List'),
                ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-pencil-square-o"></i> <?= __('Ticket Details') ?></h4></div>
                <div class="card-body">
                    <?= $this->Form->create($ticket) ?>

                    <div class="form-section-label"><i class="fa fa-user"></i> <?= __('Who is travelling') ?></div>
                    <?= $this->Form->control('trainee_id', [
                        'label' => __('Trainee'),
                        'options' => $traineeOptions,
                        'empty' => '— ' . __('Select trainee') . ' —',
                        'required' => true,
                    ]) ?>

                    <hr class="field-divider">
                    <div class="form-section-label"><i class="fa fa-ticket"></i> <?= __('Booking') ?></div>
                    <div class="field-grid">
                        <?= $this->Form->control('ticket_number', [
                            'label' => __('Ticket Number'),
                            'placeholder' => 'e.g. GIA20250818',
                        ]) ?>
                        <?= $this->Form->control('booking_ref', [
                            'label' => __('Booking Reference'),
                            'placeholder' => 'e.g. 123456789',
                        ]) ?>
                    </div>

                    <div class="field-grid">
                        <?= $this->Form->control('master_ticket_type_id', [
                            'label' => __('Ticket Type'),
                            'options' => $typeOptions,
                            'empty' => '— ' . __('Select type') . ' —',
                            'required' => true,
                        ]) ?>
                        <?= $this->Form->control('master_ticket_status_id', [
                            'label' => __('Status'),
                            'options' => $statusOptions,
                            'default' => 1,
                        ]) ?>
                    </div>

                    <hr class="field-divider">
                    <div class="form-section-label"><i class="fa fa-money"></i> <?= __('Cost') ?></div>
                    <div class="field-grid">
                        <?= $this->Form->control('total_price', [
                            'label' => __('Total Price'),
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                        ]) ?>
                        <?= $this->Form->control('master_currency_id', [
                            'label' => __('Currency'),
                            'options' => $currencyOptions,
                            'default' => 66,
                        ]) ?>
                    </div>

                    <div class="form-actions">
                        <?= $this->Form->button('<i class="fa fa-save"></i> ' . __('Save Ticket'),
                            ['escapeTitle' => false, 'class' => 'btn btn-success btn-lg']) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index'],
                            ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        <?php if ($isEdit): ?>
                            <span class="actions-spacer"></span>
                            <?= $this->Form->postLink('<i class="fa fa-trash"></i> ' . __('Delete'),
                                ['action' => 'delete', $ticket->id],
                                [
                                    'escape' => false,
                                    'class' => 'btn btn-outline-danger btn-lg',
                                    'confirm' => __('Delete ticket #{0}? This cannot be undone.', $ticket->id),
                                ]) ?>
                        <?php endif; ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <?php if ($isEdit): ?>
                <div class="card">
                    <div class="card-header"><h4><i class="fa fa-plane"></i> <?= __('Linked Flights') ?></h4></div>
                    <div class="card-body">
                        <?php if (!empty($linkedFlights)): ?>
                            <?php foreach ($linkedFlights as $flight): ?>
                                <div class="flight-card">
                                    <div class="flight-card-top">
                                        <strong><i class="fa fa-plane"></i> <?= h($flight['flight_number']) ?></strong>
                                        <span class="flight-card-tools">
                                            <span class="text-muted"><?= h($flight['airline_name'] ?: '') ?></span>
                                            <button type="button" class="btn btn-xs btn-outline-primary"
                                                    title="<?= __('Edit flight') ?>"
                                                    onclick="document.getElementById('edit-flight-<?= (int)$flight['id'] ?>').classList.toggle('open')">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <?= $this->Form->postLink('<i class="fa fa-trash"></i>',
                                                ['action' => 'transit'],
                                                [
                                                    'escape' => false,
                                                    'class' => 'btn btn-xs btn-outline-danger',
                                                    'title' => __('Remove flight'),
                                                    'data' => [
                                                        'form_action' => 'delete_leg',
                                                        'flight_id' => $flight['id'],
                                                        'redirect_ticket' => $ticket->id,
                                                    ],
                                                    'confirm' => __('Remove flight leg {0}?', $flight['flight_number']),
                                                ]) ?>
                                        </span>
                                    </div>
                                    <div class="flight-route">
                                        <span class="airport">
                                            <strong><?= h($flight['dep_code'] ?: '?') ?></strong>
                                            <small><?= h($flight['dep_city'] ?: '') ?></small>
                                        </span>
                                        <i class="fa fa-long-arrow-right"></i>
                                        <span class="airport">
                                            <strong><?= h($flight['arr_code'] ?: '?') ?></strong>
                                            <small><?= h($flight['arr_city'] ?: '') ?></small>
                                        </span>
                                    </div>
                                    <div class="flight-times text-muted">
                                        <i class="fa fa-clock-o"></i>
                                        <?= h($formatDatetime($flight['departure_datetime'])) ?>
                                        &rarr; <?= h($formatDatetime($flight['arrival_datetime'])) ?>
                                    </div>
                                    <div id="edit-flight-<?= (int)$flight['id'] ?>" class="leg-edit-panel">
                                        <?= $this->element('Tickets/leg_form', [
                                            'ticketId' => $ticket->id,
                                            'leg' => $flight,
                                            'redirectTicket' => $ticket->id,
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <details class="add-leg-details">
                                <summary><i class="fa fa-plus-circle"></i> <?= __('Add flight leg') ?></summary>
                                <?= $this->element('Tickets/leg_form', [
                                    'ticketId' => $ticket->id,
                                    'redirectTicket' => $ticket->id,
                                ]) ?>
                            </details>
                        <?php else: ?>
                            <p class="text-center text-muted py-4">
                                <i class="fa fa-plane fa-2x"></i><br>
                                <?= __('No flights linked to this ticket yet.') ?>
                            </p>
                        <?php endif; ?>
                        <?= $this->Html->link('<i class="fa fa-exchange"></i> ' . __('Manage Transit Routes'),
                            ['action' => 'transit'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary btn-block-link']) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header"><h4><i class="fa fa-info-circle"></i> <?= __('Status Guide') ?></h4></div>
                <div class="card-body">
                    <ul class="status-guide">
                        <li><span class="badge badge-info"><?= __('Booked') ?></span> <?= __('Ticket purchased, departure planned') ?></li>
                        <li><span class="badge badge-warning"><?= __('Checked In') ?></span> <?= __('Trainee has checked in for the flight') ?></li>
                        <li><span class="badge badge-success"><?= __('Completed') ?></span> <?= __('Trainee has departed to Japan') ?> 🇯🇵</li>
                        <li><span class="badge badge-danger"><?= __('Cancelled') ?></span> <?= __('Ticket was cancelled') ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ticket-form-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.ticket-form-page .page-header h2 { margin: 0 0 5px 0; }
.ticket-id { color: #6c757d; font-weight: normal; }
.ticket-form-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.ticket-form-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.ticket-form-page .card-header h4 { margin: 0; }
.ticket-form-page .card-body { padding: 20px; }
.field-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 15px;
}
@media (max-width: 640px) {
    .field-grid { grid-template-columns: 1fr; }
}
.ticket-form-page .input { margin-bottom: 18px; }
.ticket-form-page label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
    color: #495057;
    font-size: 0.95em;
}
.ticket-form-page .input input[type="text"],
.ticket-form-page .input input[type="number"],
.ticket-form-page .input select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    background: #fff;
    font-size: 15px;
    font-family: inherit;
    color: #333;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
}
.ticket-form-page .input input:focus,
.ticket-form-page .input select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}
.form-section-label {
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #764ba2;
    margin-bottom: 10px;
}
.form-section-label i { margin-right: 5px; }
.field-divider {
    border: none;
    border-top: 1px dashed #dee2e6;
    margin: 5px 0 15px 0;
}
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    align-items: center;
    flex-wrap: wrap;
}
.actions-spacer { flex: 1; }
.flight-card {
    border: 1px solid #e9ecef;
    border-left: 4px solid #667eea;
    border-radius: 8px;
    padding: 12px 15px;
    margin-bottom: 12px;
    background: #fafbff;
}
.flight-card-top {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}
.flight-card-tools {
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-xs { padding: 2px 8px; font-size: 12px; }
.leg-edit-panel { display: none; }
.leg-edit-panel.open {
    display: block;
    margin-top: 10px;
    border-top: 1px dashed #ced4da;
    padding-top: 10px;
}
.add-leg-details { margin-top: 5px; }
.add-leg-details summary {
    cursor: pointer;
    color: #667eea;
    font-weight: bold;
    padding: 6px 0;
    list-style: none;
}
.add-leg-details summary::-webkit-details-marker { display: none; }
.leg-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 10px;
}
.leg-field { display: flex; flex-direction: column; }
.leg-field label { font-size: 12px; font-weight: bold; color: #6c757d; margin-bottom: 4px; }
.leg-field select, .leg-field input {
    padding: 7px 10px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #fff;
    width: 100%;
}
.leg-field-submit { justify-content: flex-end; }
.flight-route {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
}
.flight-route .airport { display: flex; flex-direction: column; line-height: 1.2; }
.flight-route .airport small { color: #6c757d; }
.flight-route > i { color: #667eea; font-size: 18px; }
.flight-times { font-size: 0.85em; }
.btn-block-link { display: block; text-align: center; margin-top: 5px; }
.status-guide {
    list-style: none;
    padding: 0;
    margin: 0;
}
.status-guide li { padding: 6px 0; }
.status-guide .badge { margin-right: 6px; }
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
}
.badge-info { background: #17a2b8; color: white; }
.badge-warning { background: #ffc107; color: #333; }
.badge-success { background: #28a745; color: white; }
.badge-danger { background: #dc3545; color: white; }
</style>
