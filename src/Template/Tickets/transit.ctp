<?php
/**
 * Transit Routes - manage flight legs (itineraries) per ticket
 *
 * @var \App\View\AppView $this
 * @var array $tickets        ticket rows with type/status titles
 * @var array $legsByTicket   [ticketId => [leg rows ordered by departure]]
 * @var array $traineeNames   [id => "Name (TMM-code)"]
 * @var array $airlineOptions [id => "Name (CODE)"]
 * @var array $airportOptions [id => "CODE — City, Country"]
 */
$this->assign('title', 'Transit Routes');

$formatDatetime = function ($value) {
    $timestamp = $value ? strtotime($value) : false;
    return $timestamp ? date('d M Y H:i', $timestamp) : '-';
};

$formatDuration = function ($minutes) {
    $minutes = (int)$minutes;
    $hours = intdiv(abs($minutes), 60);
    $mins = abs($minutes) % 60;
    return ($minutes < 0 ? '-' : '') . ($hours ? $hours . 'h ' : '') . $mins . 'm';
};

$ticketsWithLegs = [];
$ticketsWithoutLegs = [];
foreach ($tickets as $ticket) {
    if (!empty($legsByTicket[$ticket['id']])) {
        $ticketsWithLegs[] = $ticket;
    } else {
        $ticketsWithoutLegs[] = $ticket;
    }
}
$totalLegs = 0;
$transitCount = 0;
foreach ($legsByTicket as $legs) {
    $totalLegs += count($legs);
    if (count($legs) > 1) {
        $transitCount++;
    }
}

?>

<div class="transit-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-exchange"></i> <?= __('Transit Routes') ?></h2>
            <p class="text-muted"><?= __('Build and manage flight itineraries per ticket - legs, routes and layovers') ?> 🇯🇵</p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-ticket"></i> ' . __('Tickets'),
                ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Ticket'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= count($ticketsWithLegs) ?></span>
            <span class="summary-label"><i class="fa fa-map"></i> <?= __('Itineraries') ?></span>
        </div>
        <div class="summary-card summary-legs">
            <span class="summary-number"><?= $totalLegs ?></span>
            <span class="summary-label"><i class="fa fa-plane"></i> <?= __('Flight Legs') ?></span>
        </div>
        <div class="summary-card summary-transit">
            <span class="summary-number"><?= $transitCount ?></span>
            <span class="summary-label"><i class="fa fa-exchange"></i> <?= __('With Transit') ?></span>
        </div>
        <div class="summary-card summary-missing">
            <span class="summary-number"><?= count($ticketsWithoutLegs) ?></span>
            <span class="summary-label"><i class="fa fa-question-circle"></i> <?= __('No Flights Yet') ?></span>
        </div>
    </div>

    <?php foreach ($ticketsWithLegs as $ticket): ?>
        <?php $legs = $legsByTicket[$ticket['id']]; ?>
        <div class="card itinerary-card">
            <div class="card-header itinerary-header">
                <div>
                    <strong>
                        <i class="fa fa-user"></i>
                        <?= h(isset($traineeNames[$ticket['trainee_id']]) ? $traineeNames[$ticket['trainee_id']] : ('#' . $ticket['trainee_id'])) ?>
                    </strong>
                    <span class="text-muted">
                        &middot; <?= __('Ticket') ?> <?= h($ticket['ticket_number'] ?: ('#' . $ticket['id'])) ?>
                        <?php if ($ticket['booking_ref']): ?>(<?= h($ticket['booking_ref']) ?>)<?php endif; ?>
                    </span>
                    <?php if ($ticket['type_title']): ?>
                        <span class="badge badge-outline"><?= h($ticket['type_title']) ?></span>
                    <?php endif; ?>
                    <span class="badge badge-secondary"><?= count($legs) ?> <?= count($legs) > 1 ? __('legs') : __('leg') ?></span>
                </div>
                <?= $this->Html->link('<i class="fa fa-edit"></i> ' . __('Edit Ticket'),
                    ['action' => 'edit', $ticket['id']], ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary']) ?>
            </div>
            <div class="card-body">
                <?php foreach ($legs as $i => $leg): ?>
                    <?php if ($i > 0):
                        $previous = $legs[$i - 1];
                        $layover = (strtotime($leg['departure_datetime']) - strtotime($previous['arrival_datetime'])) / 60;
                        ?>
                        <div class="layover-row">
                            <?php if ($layover >= 0): ?>
                                <span class="layover-chip">
                                    <i class="fa fa-clock-o"></i>
                                    <?= __('Layover in {0}', h($previous['arr_code'] ?: '?')) ?>: <?= $formatDuration($layover) ?>
                                </span>
                            <?php else: ?>
                                <span class="layover-chip layover-warning">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <?= __('Schedule overlap') ?> (<?= $formatDuration($layover) ?>) — <?= __('check the times') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="leg-row">
                        <span class="leg-number"><?= $i + 1 ?></span>
                        <div class="leg-flight">
                            <strong><?= h($leg['flight_number']) ?></strong>
                            <small class="text-muted"><?= h($leg['airline_name'] ?: $leg['airline_code'] ?: '') ?></small>
                        </div>
                        <div class="leg-route">
                            <span class="airport">
                                <strong><?= h($leg['dep_code'] ?: '?') ?></strong>
                                <small><?= h($leg['dep_city'] ?: '') ?></small>
                                <small class="text-muted"><?= h($formatDatetime($leg['departure_datetime'])) ?></small>
                            </span>
                            <span class="route-arrow">
                                <i class="fa fa-plane"></i>
                                <small><?= $formatDuration((strtotime($leg['arrival_datetime']) - strtotime($leg['departure_datetime'])) / 60) ?></small>
                            </span>
                            <span class="airport">
                                <strong><?= h($leg['arr_code'] ?: '?') ?></strong>
                                <small><?= h($leg['arr_city'] ?: '') ?></small>
                                <small class="text-muted"><?= h($formatDatetime($leg['arrival_datetime'])) ?></small>
                            </span>
                        </div>
                        <div class="leg-actions">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    title="<?= __('Edit leg') ?>"
                                    onclick="document.getElementById('edit-leg-<?= (int)$leg['id'] ?>').classList.toggle('open')">
                                <i class="fa fa-edit"></i>
                            </button>
                            <?= $this->Form->postLink('<i class="fa fa-trash"></i>',
                                ['action' => 'transit'],
                                [
                                    'escape' => false,
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'title' => __('Remove leg'),
                                    'data' => ['form_action' => 'delete_leg', 'flight_id' => $leg['id']],
                                    'confirm' => __('Remove flight leg {0}?', $leg['flight_number']),
                                ]) ?>
                        </div>
                    </div>
                    <div id="edit-leg-<?= (int)$leg['id'] ?>" class="leg-edit-panel">
                        <div class="leg-edit-title"><i class="fa fa-edit"></i> <?= __('Edit leg {0}', h($leg['flight_number'])) ?></div>
                        <?= $this->element('Tickets/leg_form', ['ticketId' => $ticket['id'], 'leg' => $leg]) ?>
                    </div>
                <?php endforeach; ?>

                <details class="add-leg-details">
                    <summary><i class="fa fa-plus-circle"></i> <?= __('Add flight leg') ?></summary>
                    <?php
                    $lastArrival = end($legs);
                    $defaultDeparture = $lastArrival && strtotime($lastArrival['arrival_datetime'])
                        ? date('Y-m-d\TH:i', strtotime($lastArrival['arrival_datetime']) + 2 * 3600)
                        : '';
                    echo $this->element('Tickets/leg_form', ['ticketId' => $ticket['id'], 'defaultDeparture' => $defaultDeparture]);
                    ?>
                </details>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (!empty($ticketsWithoutLegs)): ?>
        <div class="card">
            <div class="card-header"><h4><i class="fa fa-question-circle"></i> <?= __('Tickets without flights yet') ?></h4></div>
            <div class="card-body">
                <?php foreach ($ticketsWithoutLegs as $ticket): ?>
                    <details class="add-leg-details missing-ticket">
                        <summary>
                            <strong><?= h(isset($traineeNames[$ticket['trainee_id']]) ? $traineeNames[$ticket['trainee_id']] : ('#' . $ticket['trainee_id'])) ?></strong>
                            <span class="text-muted">
                                &middot; <?= __('Ticket') ?> <?= h($ticket['ticket_number'] ?: ('#' . $ticket['id'])) ?>
                                <?php if ($ticket['booking_ref']): ?>(<?= h($ticket['booking_ref']) ?>)<?php endif; ?>
                            </span>
                            <span class="add-first-leg"><i class="fa fa-plus-circle"></i> <?= __('Add first leg') ?></span>
                        </summary>
                        <?= $this->element('Tickets/leg_form', ['ticketId' => $ticket['id']]) ?>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($ticketsWithLegs) && empty($ticketsWithoutLegs)): ?>
        <div class="card">
            <div class="empty-state">
                <i class="fa fa-plane fa-4x"></i>
                <h4><?= __('No tickets yet.') ?></h4>
                <p class="text-muted"><?= __('Create a ticket first, then build its flight itinerary here.') ?></p>
                <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Ticket'),
                    ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-success']) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.transit-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.transit-page .page-header h2 { margin: 0 0 5px 0; }
.summary-strip { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.summary-card {
    flex: 1;
    min-width: 150px;
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.summary-total   { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-legs    { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.summary-transit { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-missing { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-number { font-size: 28px; font-weight: bold; line-height: 1.1; }
.summary-label { font-size: 13px; opacity: 0.95; }
.transit-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
}
.transit-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.transit-page .card-header h4 { margin: 0; }
.transit-page .card-body { padding: 20px; }
.itinerary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}
.leg-row {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 15px;
    border: 1px solid #e9ecef;
    border-left: 4px solid #667eea;
    border-radius: 8px;
    background: #fafbff;
    flex-wrap: wrap;
}
.leg-number {
    flex: 0 0 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}
.leg-flight { min-width: 120px; display: flex; flex-direction: column; }
.leg-route {
    display: flex;
    align-items: center;
    gap: 20px;
    flex: 1;
    flex-wrap: wrap;
}
.leg-route .airport { display: flex; flex-direction: column; line-height: 1.3; }
.route-arrow {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #667eea;
    line-height: 1.2;
}
.leg-actions { margin-left: auto; }
.layover-row { padding: 6px 0 6px 50px; }
.layover-chip {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 15px;
    background: #eef1ff;
    border: 1px dashed #667eea;
    color: #4a5568;
    font-size: 0.85em;
}
.layover-warning {
    background: #fff8e1;
    border-color: #ffc107;
    color: #856404;
}
.leg-edit-panel { display: none; }
.leg-edit-panel.open {
    display: block;
    margin: 8px 0 8px 43px;
    border-left: 3px solid #4facfe;
    padding-left: 12px;
}
.leg-edit-title { font-weight: bold; color: #2b7fd4; padding: 6px 0; }
.add-leg-details { margin-top: 12px; }
.add-leg-details summary {
    cursor: pointer;
    color: #667eea;
    font-weight: bold;
    padding: 8px 0;
    list-style: none;
}
.add-leg-details summary::-webkit-details-marker { display: none; }
.add-leg-details[open] summary { color: #764ba2; }
.missing-ticket summary {
    color: #333;
    font-weight: normal;
    padding: 10px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.missing-ticket .add-first-leg { margin-left: auto; color: #28a745; font-weight: bold; }
.leg-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
.badge {
    padding: 3px 9px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
}
.badge-secondary { background: #6c757d; color: white; }
.badge-outline { border: 1px solid #764ba2; color: #764ba2; background: #f7f3fb; }
.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
</style>
