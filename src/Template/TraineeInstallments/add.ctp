<?php
/**
 * Add Trainee Installment - rich payment form with live balance preview
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeInstallment $traineeInstallment
 * @var array $trainees                    [id => "Name (TMM-code)"]
 * @var array $traineeStatus               [id => ['accumulated','full','unpaid','is_paid_off']]
 * @var \Cake\ORM\Query $masterTransactionCategories
 * @var \Cake\ORM\Query $masterCurrencies
 */
$this->assign('title', 'Add Installment Payment');
?>

<div class="installment-form-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-money"></i> <?= __('Add Installment Payment') ?></h2>
            <p class="text-muted"><?= __('Record a payment - the running balance is calculated automatically') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-line-chart"></i> ' . __('Payment Tracking'),
                ['action' => 'tracking'], ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
            <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('Back to List'),
                ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-pencil-square-o"></i> <?= __('Payment Details') ?></h4></div>
                <div class="card-body">
                    <?= $this->Form->create($traineeInstallment) ?>

                    <?= $this->Form->control('trainee_id', [
                        'label' => __('Trainee'),
                        'options' => $trainees,
                        'empty' => '— ' . __('Select trainee') . ' —',
                        'required' => true,
                        'id' => 'trainee-select',
                    ]) ?>

                    <div id="no-cost-note" class="inline-note inline-note-block" style="display:none;">
                        <i class="fa fa-flag"></i>
                        <?= __('No owing cost set for this trainee yet.') ?>
                        <?= $this->Html->link(__('Set it first on Payment Tracking'), ['action' => 'tracking']) ?>.
                    </div>
                    <div id="paidoff-note" class="inline-note inline-note-done" style="display:none;">
                        <i class="fa fa-check-circle"></i>
                        <?= __('This trainee is already paid off - the receipt has been issued. No further payment can be recorded.') ?>
                    </div>

                    <hr class="field-divider">

                    <label class="mode-label"><?= __('Payment Mode') ?></label>
                    <div class="mode-cards">
                        <label class="mode-card" id="mode-card-partial">
                            <input type="radio" name="payment_mode" value="partial" checked>
                            <span class="mode-title"><i class="fa fa-sliders"></i> <?= __('Free Amount') ?></span>
                            <span class="mode-desc"><?= __('Pay any amount toward the balance') ?></span>
                        </label>
                        <label class="mode-card" id="mode-card-full">
                            <input type="radio" name="payment_mode" value="full">
                            <span class="mode-title"><i class="fa fa-bolt"></i> <?= __('Full Payment') ?></span>
                            <span class="mode-desc"><?= __('Settle the entire remaining balance') ?> — <strong id="mode-full-amount">-</strong></span>
                        </label>
                    </div>

                    <div class="field-grid">
                        <div class="input number required">
                            <label for="payment-amount"><?= __('Payment Amount (Rp)') ?></label>
                            <input type="number" name="payment_amount" id="payment-amount" min="1" step="1" required placeholder="e.g. 1000000">
                        </div>
                        <div class="input date required">
                            <label for="payment-date"><?= __('Payment Date') ?></label>
                            <input type="date" name="payment_date" id="payment-date" required value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="field-grid">
                        <?= $this->Form->control('master_transaction_category_id', [
                            'label' => __('Transaction Type'),
                            'options' => $masterTransactionCategories,
                            'empty' => '— ' . __('Select type') . ' —',
                            'required' => true,
                        ]) ?>
                        <?= $this->Form->control('master_currency_id', [
                            'label' => __('Currency'),
                            'options' => $masterCurrencies,
                            'default' => 66,
                        ]) ?>
                    </div>

                    <div class="form-actions">
                        <?= $this->Form->button('<i class="fa fa-save"></i> ' . __('Save Payment'),
                            ['escapeTitle' => false, 'class' => 'btn btn-success btn-lg']) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'tracking'],
                            ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card" id="status-card" style="display:none;">
                <div class="card-header"><h4><i class="fa fa-user"></i> <?= __('Current Status') ?></h4></div>
                <div class="card-body">
                    <div class="progress-row-header">
                        <span id="status-name" class="progress-name"></span>
                        <span id="status-count" class="progress-count"></span>
                    </div>
                    <div class="progress-track">
                        <div id="status-fill" class="progress-fill"></div>
                    </div>
                    <p id="status-note" class="text-muted" style="margin: 8px 0 0; font-size: 0.9em;"></p>
                </div>
            </div>

            <div class="card preview-card" id="preview-card" style="display:none;">
                <div class="card-header"><h4><i class="fa fa-magic"></i> <?= __('After This Payment') ?></h4></div>
                <div class="card-body">
                    <div class="preview-line">
                        <span><?= __('Paid so far') ?></span>
                        <strong id="preview-accumulated">-</strong>
                    </div>
                    <div class="preview-line">
                        <span><?= __('Total to pay') ?></span>
                        <strong id="preview-full">-</strong>
                    </div>
                    <div class="preview-line preview-remaining">
                        <span><?= __('Remaining') ?></span>
                        <strong id="preview-remaining">-</strong>
                    </div>
                    <div class="progress-track" style="margin-top:10px;">
                        <div id="preview-fill" class="progress-fill"></div>
                    </div>
                    <div id="preview-paidoff" class="paidoff-banner" style="display:none;">
                        🎉 <?= __('This payment settles the balance - trainee will be marked PAID OFF') ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4><i class="fa fa-info-circle"></i> <?= __('How it works') ?></h4></div>
                <div class="card-body">
                    <ol class="guide-steps">
                        <li><strong><?= __('Set the owing cost first') ?></strong> — <?= __('done once per trainee on the') ?> <?= $this->Html->link(__('Payment Tracking'), ['action' => 'tracking']) ?> <?= __('page.') ?></li>
                        <li><strong><?= __('Choose the payment mode') ?></strong> — <?= __('Free Amount for any partial payment, or Full Payment to settle the whole remaining balance at once.') ?></li>
                        <li><strong><?= __('Difference is calculated') ?></strong> — <?= __('remaining = owing cost minus accumulated payments.') ?></li>
                        <li><strong><?= __('Receipt at zero') ?></strong> — <?= __('when the difference reaches 0 the system issues the receipt and closes the balance.') ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var traineeStatus = <?= json_encode($traineeStatus ?: new \stdClass()) ?>;
    var traineeSelect = document.getElementById('trainee-select');
    var amountInput = document.getElementById('payment-amount');

    function rp(n) {
        return 'Rp ' + (parseInt(n, 10) || 0).toLocaleString('id-ID');
    }
    function fillColor(percent) {
        return percent >= 100 ? '#43e97b' : (percent >= 50 ? '#4facfe' : '#f5a623');
    }
    function currentMode() {
        var checked = document.querySelector('input[name="payment_mode"]:checked');
        return checked ? checked.value : 'partial';
    }

    function update() {
        var traineeId = traineeSelect.value;
        var status = traineeStatus[traineeId];
        var statusCard = document.getElementById('status-card');
        var previewCard = document.getElementById('preview-card');
        var paidoffNote = document.getElementById('paidoff-note');
        var noCostNote = document.getElementById('no-cost-note');
        var saveButton = document.querySelector('.installment-form-page button[type="submit"]');
        var mode = currentMode();

        // Mode card highlight + full amount label
        document.getElementById('mode-card-partial').classList.toggle('active', mode === 'partial');
        document.getElementById('mode-card-full').classList.toggle('active', mode === 'full');

        if (!traineeId) {
            statusCard.style.display = 'none';
            previewCard.style.display = 'none';
            noCostNote.style.display = 'none';
            paidoffNote.style.display = 'none';
            document.getElementById('mode-full-amount').textContent = '-';
            if (saveButton) saveButton.disabled = false;
            return;
        }

        // Flow gates: owing cost must exist, and settled trainees are closed
        var noCost = !status;
        var isSettled = status && status.is_paid_off;
        noCostNote.style.display = noCost ? '' : 'none';
        paidoffNote.style.display = isSettled ? '' : 'none';
        if (saveButton) saveButton.disabled = !!(noCost || isSettled);

        var accumulated = status ? status.accumulated : 0;
        var full = status ? status.full : 0;
        var remaining = status ? status.unpaid : 0;

        document.getElementById('mode-full-amount').textContent = status ? rp(remaining) : '-';

        // Credit-card style: full payment locks the amount to the remaining balance
        if (mode === 'full' && status && !isSettled) {
            amountInput.value = remaining;
            amountInput.readOnly = true;
        } else {
            amountInput.readOnly = false;
        }

        // Current status card
        statusCard.style.display = '';
        var name = traineeSelect.options[traineeSelect.selectedIndex].text;
        document.getElementById('status-name').textContent = name;
        var percent = full > 0 ? Math.min(100, Math.round(accumulated / full * 100)) : 0;
        document.getElementById('status-count').textContent = rp(accumulated) + ' / ' + rp(full);
        var statusFill = document.getElementById('status-fill');
        statusFill.style.width = percent + '%';
        statusFill.style.background = fillColor(percent);
        document.getElementById('status-note').textContent = noCost
            ? '<?= __('Owing cost not set yet.') ?>'
            : (isSettled ? '<?= __('Already paid off.') ?>' : '<?= __('Remaining:') ?> ' + rp(remaining));

        // After-payment preview
        var amount = parseInt(amountInput.value, 10) || 0;
        if (amount > 0 && status && !isSettled) {
            previewCard.style.display = '';
            var newAccumulated = accumulated + amount;
            var newRemaining = Math.max(0, full - newAccumulated);
            var newPercent = full > 0 ? Math.min(100, Math.round(newAccumulated / full * 100)) : 0;
            document.getElementById('preview-accumulated').textContent = rp(newAccumulated);
            document.getElementById('preview-full').textContent = rp(full);
            document.getElementById('preview-remaining').textContent = rp(newRemaining);
            var previewFill = document.getElementById('preview-fill');
            previewFill.style.width = newPercent + '%';
            previewFill.style.background = fillColor(newPercent);
            document.getElementById('preview-paidoff').style.display =
                (full > 0 && newRemaining === 0) ? '' : 'none';
        } else {
            previewCard.style.display = 'none';
        }
    }

    traineeSelect.addEventListener('change', update);
    amountInput.addEventListener('input', update);
    document.querySelectorAll('input[name="payment_mode"]').forEach(function (radio) {
        radio.addEventListener('change', update);
    });
    update();
})();
</script>

<style>
.installment-form-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.installment-form-page .page-header h2 { margin: 0 0 5px 0; }
.installment-form-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.installment-form-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.installment-form-page .card-header h4 { margin: 0; }
.installment-form-page .card-body { padding: 20px; }
.field-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 15px;
}
@media (max-width: 640px) {
    .field-grid { grid-template-columns: 1fr; }
}
.installment-form-page .input { margin-bottom: 18px; }
.installment-form-page label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
    color: #495057;
    font-size: 0.95em;
}
.installment-form-page .input input[type="text"],
.installment-form-page .input input[type="number"],
.installment-form-page .input input[type="date"],
.installment-form-page .input select {
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
.installment-form-page .input input:focus,
.installment-form-page .input select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}
.installment-form-page .input input:disabled { background: #f1f3f5; color: #6c757d; }
.inline-note {
    padding: 10px 15px;
    margin-bottom: 15px;
    background: #eef1ff;
    border: 1px solid #c7cffb;
    border-left: 4px solid #667eea;
    border-radius: 8px;
    color: #3b4890;
}
.inline-note-done {
    background: #e6f9ee;
    border-color: #a8e6c1;
    border-left-color: #28a745;
    color: #1e7e42;
}
.inline-note-block {
    background: #fff8e1;
    border-color: #ffe082;
    border-left-color: #ffc107;
    color: #856404;
}
.mode-label { font-weight: bold; display: block; margin-bottom: 8px; color: #495057; font-size: 0.95em; }
.mode-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 18px;
}
@media (max-width: 640px) {
    .mode-cards { grid-template-columns: 1fr; }
}
.mode-card {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    background: #fafbff;
    transition: border-color 0.15s, box-shadow 0.15s;
    margin: 0;
}
.mode-card:hover { border-color: #b3bdf0; }
.mode-card.active {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    background: #fff;
}
.mode-card input[type="radio"] { position: absolute; opacity: 0; }
.mode-title { font-weight: bold; color: #3b4890; }
.mode-desc { font-size: 0.85em; color: #6c757d; }
#payment-amount[readonly] { background: #f1f3f5; color: #3b4890; font-weight: bold; }
.installment-form-page button[type="submit"]:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.guide-steps { padding-left: 20px; margin: 0; }
.guide-steps li { padding: 5px 0; color: #495057; }
.field-divider {
    border: none;
    border-top: 1px dashed #dee2e6;
    margin: 5px 0 18px 0;
}
.form-actions { display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
.progress-row-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    flex-wrap: wrap;
    gap: 4px;
}
.progress-name { font-weight: bold; }
.progress-count { font-size: 0.9em; color: #6c757d; }
.progress-track { height: 10px; background: #e9ecef; border-radius: 5px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 5px; transition: width 0.4s ease; width: 0; }
.preview-card { border-left: 4px solid #4facfe; }
.preview-line {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px dashed #e9ecef;
}
.preview-line:last-of-type { border-bottom: none; }
.preview-remaining strong { color: #c0392b; }
.paidoff-banner {
    margin-top: 12px;
    padding: 10px 14px;
    background: #e6f9ee;
    border: 1px solid #a8e6c1;
    border-radius: 8px;
    color: #1e7e42;
    font-weight: bold;
}
.guide-list { list-style: none; padding: 0; margin: 0; }
.guide-list li { padding: 5px 0; color: #495057; }
.guide-list i { width: 20px; }
.text-success { color: #28a745; }
</style>
