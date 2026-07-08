<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PostApprentice $postApprentice
 * @var array $apprenticeOptions
 * @var array $statusOptions
 */
?>
<?= $this->Html->css('datepicker-fix.css') ?>
<?= $this->Html->script('form-confirm.js?v=2.0') ?>

<style>
.pa-form-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(0,131,143,0.10);
    overflow: hidden;
    margin-bottom: 30px;
}
.pa-form-header {
    background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
    padding: 22px 30px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.pa-form-header .header-icon {
    width: 52px;
    height: 52px;
    background: rgba(255,255,255,0.18);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.pa-form-header .header-icon i {
    font-size: 24px;
    color: #fff;
}
.pa-form-header h3 {
    margin: 0 0 2px;
    color: #fff;
    font-size: 20px;
    font-weight: 700;
}
.pa-form-header p {
    margin: 0;
    color: rgba(255,255,255,0.78);
    font-size: 13px;
}
.pa-form-body {
    padding: 30px;
}
.pa-section {
    border-left: 3px solid #00BCD4;
    padding-left: 16px;
    margin-bottom: 28px;
}
.pa-section-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.3px;
    color: #00838F;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.form-label {
    font-weight: 600;
    font-size: 13px;
    color: #374151;
    margin-bottom: 5px;
    display: block;
}
.req { color: #e53e3e; margin-left: 2px; }
.optional { color: #94a3b8; font-weight: 400; font-size: 12px; margin-left: 4px; }
.form-control, select.form-control {
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 9px 14px;
    font-size: 14px;
    width: 100%;
    background: #f8fafc;
    color: #1a202c;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus, select.form-control:focus {
    border-color: #00BCD4;
    box-shadow: 0 0 0 3px rgba(0,188,212,0.12);
    background: #fff;
    outline: none;
}
textarea.form-control { min-height: 110px; resize: vertical; }
.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
}
.status-card {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #f8fafc;
    position: relative;
}
.status-card:hover {
    border-color: #00BCD4;
    background: #f0fdff;
}
.status-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}
.status-card.selected {
    border-color: #00BCD4;
    background: #e0f7fa;
    box-shadow: 0 0 0 3px rgba(0,188,212,0.15);
}
.status-card i {
    font-size: 22px;
    color: #94a3b8;
    margin-bottom: 6px;
    display: block;
    transition: color 0.2s;
}
.status-card.selected i { color: #00838F; }
.status-card span {
    font-size: 12px;
    font-weight: 600;
    color: #4a5568;
    display: block;
}
.status-card.selected span { color: #00838F; }
.employer-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
@media (max-width: 576px) { .employer-row { grid-template-columns: 1fr; } }
.form-actions {
    display: flex;
    gap: 12px;
    padding: 20px 30px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 14px 14px;
}
.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 26px;
    background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0,188,212,0.25);
}
.btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,188,212,0.35);
    color: #fff;
    text-decoration: none;
}
.btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 20px;
    background: #fff;
    color: #4a5568;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-cancel:hover {
    background: #f1f5f9;
    color: #1a202c;
    text-decoration: none;
}
</style>

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Post Apprentices'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-users"></i> ' . __('List Apprentices'), ['controller' => 'Apprentices', 'action' => 'index'], ['escape' => false]) ?></li>
    </ul>
</nav>

<div class="postApprentices form content">

    <?= $this->Form->create($postApprentice, [
        'data-confirm' => 'true',
        'id'           => 'postApprenticeForm',
    ]) ?>

    <div class="pa-form-card">

        <!-- Header -->
        <div class="pa-form-header">
            <div class="header-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <h3><?= __('Add Post-Apprentice Record') ?></h3>
                <p><?= __('Track the apprentice\'s status, employment, and activities after completing the programme') ?></p>
            </div>
        </div>

        <div class="pa-form-body">

            <!-- Section 1: Apprentice & Date -->
            <div class="pa-section">
                <div class="pa-section-title">
                    <i class="fas fa-id-card"></i> <?= __('Apprentice & Return Date') ?>
                </div>
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="form-label"><?= __('Apprentice') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('apprentice_id', [
                            'options'  => $apprenticeOptions,
                            'class'    => 'form-control',
                            'label'    => false,
                            'empty'    => __('-- Select Apprentice --'),
                            'required' => true,
                        ]) ?>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label"><?= __('Return Date') ?> <span class="optional">(<?= __('optional') ?>)</span></label>
                        <?= $this->Form->control('return_date', [
                            'type'         => 'text',
                            'class'        => 'form-control datepicker',
                            'label'        => false,
                            'placeholder'  => 'YYYY-MM-DD',
                            'autocomplete' => 'off',
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Section 2: Current Status (visual radio cards) -->
            <div class="pa-section">
                <div class="pa-section-title">
                    <i class="fas fa-briefcase"></i> <?= __('Current Status') ?>
                </div>
                <?= $this->Form->control('current_status', ['type' => 'hidden', 'id' => 'CurrentStatus', 'label' => false]) ?>
                <div class="status-grid" id="statusGrid">
                    <?php
                    $statusIcons = [
                        'Employed'         => 'fa-building',
                        'Self-Employed'    => 'fa-store',
                        'Continuing Study' => 'fa-graduation-cap',
                        'Unemployed'       => 'fa-user-times',
                        'Other'            => 'fa-ellipsis-h',
                    ];
                    foreach ($statusOptions as $val => $label):
                        $icon = $statusIcons[$val] ?? 'fa-circle';
                        $sel  = ($postApprentice->current_status === $val) ? ' selected' : '';
                    ?>
                    <div class="status-card<?= $sel ?>" data-value="<?= h($val) ?>">
                        <i class="fas <?= $icon ?>"></i>
                        <span><?= h($label) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Section 3: Employment Detail -->
            <div class="pa-section">
                <div class="pa-section-title">
                    <i class="fas fa-industry"></i> <?= __('Employment Detail') ?>
                </div>
                <div class="employer-row mb-3">
                    <div>
                        <label class="form-label"><?= __('Employer / Organisation') ?> <span class="optional">(<?= __('optional') ?>)</span></label>
                        <?= $this->Form->control('employer', [
                            'class'       => 'form-control',
                            'label'       => false,
                            'placeholder' => __('Company or organisation name'),
                            'maxlength'   => 255,
                        ]) ?>
                    </div>
                    <div>
                        <label class="form-label"><?= __('Position / Role') ?> <span class="optional">(<?= __('optional') ?>)</span></label>
                        <?= $this->Form->control('position', [
                            'class'       => 'form-control',
                            'label'       => false,
                            'placeholder' => __('Job title or role'),
                            'maxlength'   => 255,
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Section 4: Notes -->
            <div class="pa-section">
                <div class="pa-section-title">
                    <i class="fas fa-sticky-note"></i> <?= __('Notes') ?>
                </div>
                <label class="form-label"><?= __('Additional Notes') ?> <span class="optional">(<?= __('optional') ?>)</span></label>
                <?= $this->Form->control('notes', [
                    'type'        => 'textarea',
                    'class'       => 'form-control',
                    'label'       => false,
                    'placeholder' => __('Any additional context, observations, or follow-up actions…'),
                    'rows'        => 4,
                ]) ?>
            </div>

        </div><!-- /.pa-form-body -->

        <!-- Footer -->
        <div class="form-actions">
            <?= $this->Form->button('<i class="fas fa-save"></i> ' . __('Save Record'), [
                'type'   => 'submit',
                'class'  => 'btn-save',
                'id'     => 'submitBtn',
                'escape' => false,
            ]) ?>
            <?= $this->Html->link(
                '<i class="fas fa-times"></i> ' . __('Cancel'),
                ['action' => 'index'],
                ['class' => 'btn-cancel', 'escape' => false]
            ) ?>
        </div>

    </div><!-- /.pa-form-card -->

    <?= $this->Form->end() ?>
</div>

<?php $this->append('script'); ?>
<script>
$(document).ready(function () {

    // ── Datepicker ─────────────────────────────────────────────────────────
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        container: 'body',
        zIndexOffset: 1050
    });

    // ── Status card picker ─────────────────────────────────────────────────
    var $hidden = $('#CurrentStatus');

    $('#statusGrid .status-card').on('click', function () {
        $('#statusGrid .status-card').removeClass('selected');
        $(this).addClass('selected');
        $hidden.val($(this).data('value'));
    });

    // Pre-select if value already set (e.g. validation fail)
    var existing = $hidden.val();
    if (existing) {
        $('#statusGrid .status-card[data-value="' + existing + '"]').addClass('selected');
    }

    // Validate status chosen before submit
    $('#postApprenticeForm').on('submit', function (e) {
        if (!$hidden.val()) {
            e.preventDefault();
            $('#statusGrid').css('outline', '2px solid #e53e3e');
            $('html, body').animate({ scrollTop: $('#statusGrid').offset().top - 100 }, 300);
            return false;
        }
    });
    $('#statusGrid .status-card').on('click', function () {
        $('#statusGrid').css('outline', '');
    });

});
</script>
<?php $this->end(); ?>
