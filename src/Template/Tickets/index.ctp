<?php
/**
 * Tickets & Flights - rich index (also rendered by the costs action)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ticket[]|\Cake\Collection\CollectionInterface $tickets
 * @var array $traineeNames   [id => "Name (TMM-code)"]
 * @var array $typeNames      [id => title]  (Direct / Transit)
 * @var array $statusNames    [id => title]  (Booked / Checked In / Completed / Cancelled)
 * @var array $currencyNames  [id => code]
 * @var array $flightsByTicket [ticketId => [flight rows]]
 * @var array $summary        ['total' => n, 'Booked' => n, ...]
 * @var int $filterTrainee
 * @var int $filterType
 * @var int $filterStatus
 */
$this->assign('title', 'Tickets & Flights');

$statusBadge = function ($name) {
    switch ($name) {
        case 'Booked':
            return 'info';
        case 'Checked In':
            return 'warning';
        case 'Completed':
            return 'success';
        case 'Cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
};

$formatFlightDate = function ($value) {
    $timestamp = $value ? strtotime($value) : false;
    return $timestamp ? date('d M Y H:i', $timestamp) : '';
};

$hasFilter = $filterTrainee || $filterType || $filterStatus;
?>

<div class="tickets-index-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-plane"></i> <?= __('Tickets & Flights') ?></h2>
            <p class="text-muted"><?= __('Departure tickets for trainees going to Japan') ?> 🇯🇵</p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Ticket'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link('<i class="fa fa-exchange"></i> ' . __('Transit Routes'),
                ['action' => 'transit'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= number_format(isset($summary['total']) ? $summary['total'] : 0) ?></span>
            <span class="summary-label"><i class="fa fa-ticket"></i> <?= __('Total Tickets') ?></span>
        </div>
        <div class="summary-card summary-booked">
            <span class="summary-number"><?= number_format(isset($summary['Booked']) ? $summary['Booked'] : 0) ?></span>
            <span class="summary-label"><i class="fa fa-bookmark"></i> <?= __('Booked') ?></span>
        </div>
        <div class="summary-card summary-completed">
            <span class="summary-number"><?= number_format(isset($summary['Completed']) ? $summary['Completed'] : 0) ?></span>
            <span class="summary-label"><i class="fa fa-check-circle"></i> <?= __('Completed') ?></span>
        </div>
        <div class="summary-card summary-cancelled">
            <span class="summary-number"><?= number_format(isset($summary['Cancelled']) ? $summary['Cancelled'] : 0) ?></span>
            <span class="summary-label"><i class="fa fa-ban"></i> <?= __('Cancelled') ?></span>
        </div>
    </div>

    <!-- Status legend -->
    <div class="status-legend">
        <span class="legend-title"><i class="fa fa-info-circle"></i> <?= __('Status legend:') ?></span>
        <span class="legend-item">
            <span class="badge badge-info"><?= __('Booked') ?></span>
            <?= __('ticket purchased, departure planned') ?>
        </span>
        <span class="legend-item">
            <span class="badge badge-warning"><?= __('Checked In') ?></span>
            <?= __('trainee has checked in for the flight') ?>
        </span>
        <span class="legend-item">
            <span class="badge badge-success"><?= __('Completed') ?></span>
            <?= __('trainee has departed to Japan') ?> 🇯🇵
        </span>
        <span class="legend-item">
            <span class="badge badge-danger"><?= __('Cancelled') ?></span>
            <?= __('ticket was cancelled') ?>
        </span>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar">
        <form method="get" class="filter-form">
            <div class="filter-field">
                <label><?= __('Trainee') ?></label>
                <select name="trainee_id" onchange="this.form.submit()">
                    <option value=""><?= __('All trainees') ?></option>
                    <?php foreach ($traineeNames as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $filterTrainee == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label><?= __('Ticket Type') ?></label>
                <select name="type_id" onchange="this.form.submit()">
                    <option value=""><?= __('All types') ?></option>
                    <?php foreach ($typeNames as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $filterType == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label><?= __('Status') ?></label>
                <select name="status_id" onchange="this.form.submit()">
                    <option value=""><?= __('All statuses') ?></option>
                    <?php foreach ($statusNames as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $filterStatus == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($hasFilter): ?>
                <div class="filter-field filter-reset">
                    <label>&nbsp;</label>
                    <?= $this->Html->link('<i class="fa fa-times"></i> ' . __('Reset'),
                        ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php $rows = iterator_to_array($tickets); ?>
    <?php if (!empty($rows)): ?>
        <div class="card">
            <div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table rich-table">
                    <thead>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                            <th><?= $this->Paginator->sort('trainee_id', __('Trainee')) ?></th>
                            <th><?= $this->Paginator->sort('ticket_number', __('Ticket / Booking Ref')) ?></th>
                            <th><?= $this->Paginator->sort('master_ticket_type_id', __('Type')) ?></th>
                            <th><?= __('Flights') ?></th>
                            <th class="text-right"><?= $this->Paginator->sort('total_price', __('Price')) ?></th>
                            <th><?= $this->Paginator->sort('master_ticket_status_id', __('Status')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-info', 'title' => __('View')]) ?>
                                <?= $this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'edit', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary', 'title' => __('Edit')]) ?>
                            </td>
                            <td class="text-muted">#<?= h($row['id']) ?></td>
                            <td>
                                <strong><?= h(isset($traineeNames[$row['trainee_id']]) ? $traineeNames[$row['trainee_id']] : ('#' . $row['trainee_id'])) ?></strong>
                            </td>
                            <td>
                                <?php if ($row['ticket_number']): ?>
                                    <strong><?= h($row['ticket_number']) ?></strong><br>
                                <?php endif; ?>
                                <small class="text-muted"><i class="fa fa-bookmark-o"></i> <?= h($row['booking_ref'] ?: '-') ?></small>
                            </td>
                            <td>
                                <?php $typeName = isset($typeNames[$row['master_ticket_type_id']]) ? $typeNames[$row['master_ticket_type_id']] : null; ?>
                                <?php if ($typeName === 'Direct'): ?>
                                    <span class="badge badge-outline-blue"><i class="fa fa-long-arrow-right"></i> <?= h($typeName) ?></span>
                                <?php elseif ($typeName === 'Transit'): ?>
                                    <span class="badge badge-outline-purple"><i class="fa fa-exchange"></i> <?= h($typeName) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($flightsByTicket[$row['id']])): ?>
                                    <?php foreach ($flightsByTicket[$row['id']] as $flight): ?>
                                        <div class="flight-line">
                                            <i class="fa fa-plane"></i>
                                            <strong><?= h($flight['flight_number']) ?></strong>
                                            <?php if ($flight['dep_code'] || $flight['arr_code']): ?>
                                                <?= h($flight['dep_code'] ?: '?') ?> <i class="fa fa-long-arrow-right"></i> <?= h($flight['arr_code'] ?: '?') ?>
                                            <?php endif; ?>
                                            <small class="text-muted"><?= h($formatFlightDate($flight['departure_datetime'])) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted"><?= __('No flights linked') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <strong><?= number_format((float)$row['total_price'], 2) ?></strong>
                                <small class="text-muted"><?= h(isset($currencyNames[$row['master_currency_id']]) ? $currencyNames[$row['master_currency_id']] : '') ?></small>
                            </td>
                            <td>
                                <?php $statusName = isset($statusNames[$row['master_ticket_status_id']]) ? $statusNames[$row['master_ticket_status_id']] : null; ?>
                                <span class="badge badge-<?= $statusBadge($statusName) ?>"><?= h($statusName ?: '-') ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="paginator" style="margin-top: 15px;">
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="empty-state">
                <i class="fa fa-plane fa-4x"></i>
                <h4><?= $hasFilter ? __('No tickets match the current filter.') : __('No tickets yet.') ?></h4>
                <p class="text-muted"><?= __('Create a ticket to start tracking a trainee\'s departure to Japan.') ?></p>
                <div>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Ticket'),
                        ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-success']) ?>
                    <?php if ($hasFilter): ?>
                        <?= $this->Html->link(__('Reset Filter'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.tickets-index-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.tickets-index-page .page-header h2 { margin: 0 0 5px 0; }
.summary-strip {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
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
.summary-total     { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-booked    { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.summary-completed { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-cancelled { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-number { font-size: 28px; font-weight: bold; line-height: 1.1; }
.summary-label { font-size: 13px; opacity: 0.95; }
.status-legend {
    display: flex;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
    background: #fafbff;
    border: 1px solid #e4e8fb;
    border-radius: 10px;
    padding: 10px 18px;
    margin-bottom: 20px;
    font-size: 0.88em;
    color: #495057;
}
.status-legend .legend-title { font-weight: bold; color: #764ba2; white-space: nowrap; }
.status-legend .legend-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.filter-bar {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 15px 20px;
    margin-bottom: 20px;
}
.filter-form {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: flex-end;
}
.filter-field { display: flex; flex-direction: column; min-width: 180px; }
.filter-field label { font-size: 12px; font-weight: bold; color: #6c757d; margin-bottom: 4px; }
.filter-field select {
    padding: 7px 10px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #fff;
}
.tickets-index-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
}
.rich-table { border-collapse: collapse; width: 100%; min-width: 950px; margin: 0; }
.rich-table thead {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
}
.rich-table th {
    padding: 12px;
    border-bottom: 2px solid #667eea;
    white-space: nowrap;
    text-align: left;
}
.rich-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}
.rich-table tbody tr:hover { background: #f8f9ff; }
.rich-table .actions { white-space: nowrap; }
/* Floating (sticky) actions column - stays visible while the table scrolls horizontally.
   The shadow/divider only appears while actually scrolled (.is-scrolled is toggled by JS),
   so the column blends into the table when there is nothing underneath it. */
.rich-table th.actions,
.rich-table td.actions {
    position: sticky;
    left: 0;
    z-index: 5;
    width: 1%;
    padding-left: 10px;
    padding-right: 10px;
}
.rich-table th.actions { background: #e9ecfa; z-index: 6; }
.rich-table td.actions { background: #fff; }
.rich-table tbody tr:hover td.actions { background: #f8f9ff; }
.rich-table td.actions .btn { padding: 4px 8px; }
.table-scroll-wrapper.is-scrolled .rich-table th.actions,
.table-scroll-wrapper.is-scrolled .rich-table td.actions {
    border-right: 2px solid #d3d9f5;
    box-shadow: 4px 0 8px -2px rgba(60, 70, 130, 0.18);
    transition: box-shadow 0.15s ease;
}
.rich-table .text-right { text-align: right; }
.flight-line { white-space: nowrap; padding: 2px 0; }
.flight-line i.fa-plane { color: #667eea; margin-right: 3px; }
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
    white-space: nowrap;
}
.badge-info { background: #17a2b8; color: white; }
.badge-warning { background: #ffc107; color: #333; }
.badge-success { background: #28a745; color: white; }
.badge-danger { background: #dc3545; color: white; }
.badge-secondary { background: #6c757d; color: white; }
.badge-outline-blue { border: 1px solid #4facfe; color: #2b7fd4; background: #f0f8ff; }
.badge-outline-purple { border: 1px solid #764ba2; color: #764ba2; background: #f7f3fb; }
.empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #6c757d;
}
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
.empty-state .btn { margin: 5px; }
</style>

<script>
// Show the floating-column divider/shadow only while the table is horizontally scrolled
(function () {
    document.querySelectorAll('.tickets-index-page .table-scroll-wrapper').forEach(function (wrapper) {
        var update = function () {
            wrapper.classList.toggle('is-scrolled', wrapper.scrollLeft > 2);
        };
        wrapper.addEventListener('scroll', update, { passive: true });
        update();
    });
})();
</script>
