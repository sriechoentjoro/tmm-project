<?php
/**
 * Add / Edit Cooperative Association - rich form
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CooperativeAssociation $cooperativeAssociation
 */
$action = $this->request->getParam('action');
$isEdit = ($action === 'edit');
$this->assign('title', ($isEdit ? 'Edit' : 'Add') . ' Cooperative Association');

$dateVal = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Date) {
        return $value->format('Y-m-d');
    }
    return $value ? (string)$value : '';
};
?>

<?= $this->Html->script('form-confirm.js?v=2.0') ?>

<div class="coop-form-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-handshake-o"></i> <?= $isEdit ? __('Edit Cooperative Association') : __('Add Cooperative Association') ?></h2>
            <p class="text-muted"><?= __('Register a cooperative association (kumiai) partner') ?> 🇯🇵</p>
        </div>
        <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('Back to List'),
            ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>

    <?= $this->Form->create($cooperativeAssociation, ['data-confirm' => 'true', 'id' => 'cooperativeAssociationForm']) ?>
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-building"></i> <?= __('Association Details') ?></h4></div>
                <div class="card-body">
                    <div class="form-section-label"><i class="fa fa-id-card-o"></i> <?= __('Identity') ?></div>
                    <div class="input">
                        <label><?= __('Name') ?></label>
                        <?= $this->Form->text('name', [
                            'value' => $cooperativeAssociation->name,
                            'placeholder' => 'e.g. Asahi Cooperative Association',
                            'required' => true,
                        ]) ?>
                    </div>
                    <div class="input">
                        <label><?= __('Address') ?></label>
                        <?= $this->Form->textarea('address', [
                            'value' => $cooperativeAssociation->address,
                            'rows' => 2,
                            'placeholder' => __('Full address'),
                        ]) ?>
                    </div>
                    <div class="input">
                        <label><?= __('Telephone') ?></label>
                        <?= $this->Form->text('telephone', [
                            'value' => $cooperativeAssociation->telephone,
                            'placeholder' => 'e.g. +81 3-1234-5678',
                            'class' => 'no-uppercase',
                        ]) ?>
                    </div>

                    <hr class="field-divider">
                    <div class="form-section-label"><i class="fa fa-calendar"></i> <?= __('Permit Period') ?></div>
                    <div class="field-grid">
                        <div class="input">
                            <label><?= __('Date Permitted') ?></label>
                            <input type="date" name="date_permitted" id="date-permitted"
                                   value="<?= h($dateVal($cooperativeAssociation->date_permitted)) ?>">
                        </div>
                        <div class="input">
                            <label><?= __('Date Expired') ?></label>
                            <input type="date" name="date_expired" id="date-expired"
                                   value="<?= h($dateVal($cooperativeAssociation->date_expired)) ?>">
                        </div>
                    </div>
                    <div id="validity-hint" class="validity-hint" style="display:none;"></div>

                    <hr class="field-divider">
                    <div class="form-section-label"><i class="fa fa-toggle-on"></i> <?= __('Status') ?></div>
                    <div class="input">
                        <label><?= __('Operational Status') ?></label>
                        <?= $this->Form->select('status', [
                            'active' => __('Active - Operational'),
                            'suspended' => __('Suspended - Temporarily inactive'),
                            'inactive' => __('Inactive - Not operational'),
                        ], [
                            'value' => $cooperativeAssociation->status ?: 'active',
                            'default' => 'active',
                        ]) ?>
                        <small class="text-muted"><i class="fa fa-info-circle"></i> <?= __('Active associations can manage cooperative member organizations.') ?></small>
                    </div>

                    <div class="form-actions">
                        <?= $this->Form->button('<i class="fa fa-save"></i> ' . __('Save Association'),
                            ['escapeTitle' => false, 'class' => 'btn btn-success btn-lg', 'id' => 'submitBtn']) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index'],
                            ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        <?php if ($isEdit): ?>
                            <span class="actions-spacer"></span>
                            <?= $this->Form->postLink('<i class="fa fa-trash"></i> ' . __('Delete'),
                                ['action' => 'delete', $cooperativeAssociation->id],
                                ['escape' => false, 'class' => 'btn btn-outline-danger btn-lg',
                                 'confirm' => __('Delete this cooperative association? This cannot be undone.')]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <?= $this->element('status_guide', [
                'statuses' => [
                    ['label' => __('Active'), 'class' => 'st-active', 'description' => __('Operational - can manage member organizations')],
                    ['label' => __('Suspended'), 'class' => 'st-suspended', 'description' => __('Temporarily inactive - permit under review')],
                    ['label' => __('Inactive'), 'class' => 'st-inactive', 'description' => __('No longer operational')],
                ],
                'tip' => __('A cooperative association (kumiai) is the receiving body that places apprentices with member companies in Japan. Keep the permit dates current so expiry can be tracked.'),
            ]) ?>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<script>
$(function () {
    function updateValidity() {
        var from = document.getElementById('date-permitted').value;
        var to = document.getElementById('date-expired').value;
        var hint = document.getElementById('validity-hint');
        if (!from && !to) { hint.style.display = 'none'; return; }
        hint.style.display = '';
        if (from && to) {
            var d1 = new Date(from), d2 = new Date(to);
            if (d2 < d1) {
                hint.className = 'validity-hint vh-warn';
                hint.innerHTML = '<i class="fa fa-exclamation-triangle"></i> <?= __('Expiry is before the permitted date - please check.') ?>';
                return;
            }
            var today = new Date(); today.setHours(0, 0, 0, 0);
            var days = Math.round((d2 - today) / 86400000);
            if (days < 0) {
                hint.className = 'validity-hint vh-warn';
                hint.innerHTML = '<i class="fa fa-times-circle"></i> <?= __('Permit has expired.') ?>';
            } else {
                hint.className = 'validity-hint vh-ok';
                hint.innerHTML = '<i class="fa fa-check-circle"></i> <?= __('Valid - expires in') ?> ' + days + ' <?= __('day(s).') ?>';
            }
        } else {
            hint.className = 'validity-hint';
            hint.innerHTML = '<i class="fa fa-info-circle"></i> <?= __('Set both dates to see the validity period.') ?>';
        }
    }
    document.getElementById('date-permitted').addEventListener('change', updateValidity);
    document.getElementById('date-expired').addEventListener('change', updateValidity);
    updateValidity();

    // Auto-uppercase text inputs (kept from the original form), except phone/email
    $('input[type="text"]').not('.no-uppercase, [type="email"]').on('input', function () {
        var s = this.selectionStart, e = this.selectionEnd;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(s, e);
    });
});
</script>

<style>
.coop-form-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.coop-form-page .page-header h2 { margin: 0 0 5px 0; }
.coop-form-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.coop-form-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.coop-form-page .card-header h4 { margin: 0; }
.coop-form-page .card-body { padding: 20px; }
.form-section-label {
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #764ba2;
    margin-bottom: 10px;
}
.form-section-label i { margin-right: 5px; }
.field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 15px; }
@media (max-width: 640px) { .field-grid { grid-template-columns: 1fr; } }
.coop-form-page .input { margin-bottom: 18px; }
.coop-form-page label { font-weight: bold; display: block; margin-bottom: 6px; color: #495057; font-size: 0.95em; }
.coop-form-page input[type="text"],
.coop-form-page input[type="date"],
.coop-form-page textarea,
.coop-form-page select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    background: #fff;
    font-size: 15px;
    font-family: inherit;
    color: #333;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.coop-form-page input:focus, .coop-form-page textarea:focus, .coop-form-page select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}
.coop-form-page .input small { display: block; margin-top: 5px; }
.field-divider { border: none; border-top: 1px dashed #dee2e6; margin: 5px 0 18px 0; }
.validity-hint {
    margin: -8px 0 15px 0;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.9em;
    background: #eef1ff;
    border: 1px solid #c7cffb;
    color: #3b4890;
}
.validity-hint.vh-ok { background: #e6f9ee; border-color: #a8e6c1; color: #1e7e42; }
.validity-hint.vh-warn { background: #fff8e1; border-color: #ffe082; color: #856404; }
.form-actions { display: flex; gap: 10px; margin-top: 10px; align-items: center; flex-wrap: wrap; }
.actions-spacer { flex: 1; }
.guide-list { list-style: none; padding: 0; margin: 0; }
.guide-list li { padding: 8px 0; color: #495057; }
.status-badge {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    margin-right: 6px;
}
.st-active { background: #e6f9ee; color: #1e7e42; }
.st-suspended { background: #fff8e1; color: #856404; }
.st-inactive { background: #fdeaea; color: #c0392b; }
</style>

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
