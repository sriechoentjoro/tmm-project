<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeFlight[]|\Cake\Collection\CollectionInterface $apprenticeFlights
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <h2 style="margin: 0;"><?= __('Apprentice Flights') ?></h2>
        <div style="display: flex; align-items: center; gap: 10px;">
            <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('Add New'), ['action' => 'add'], ['class' => 'btn-export-light', 'escape' => false]) ?>
        </div>
    </div>
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div class="apprenticeFlights index content">
        <table class="table" style="border-collapse: collapse; width: 100%; min-width: 800px;">
            <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                <tr>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" class="actions"><?= __('Actions') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('apprentice_ticket_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('master_airline_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('flight_number') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('departure_airport_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('arrival_airport_id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('departure_datetime') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('arrival_datetime') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apprenticeFlights as $row): ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 10px 12px; white-space: nowrap;" class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    </td>
                    <td style="padding: 10px 12px;"><?= h($row['id']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['apprentice_ticket_id']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['master_airline_id']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['flight_number']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['departure_airport_id']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['arrival_airport_id']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['departure_datetime']) ?></td>
                    <td style="padding: 10px 12px;"><?= h($row['arrival_datetime']) ?></td>
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
