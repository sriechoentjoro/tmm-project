<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Role[] $roles
 */
?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><?= __('Add User') ?></h6>
                <?= $this->Html->link(__('List Users'), ['action' => 'index'], ['class' => 'btn btn-sm btn-secondary']) ?>
            </div>
            <div class="card-body">
                <?= $this->Form->create($user) ?>
                <fieldset>
                    <div class="form-group">
                        <?= $this->Form->control('username', ['class' => 'form-control']) ?>
                    </div>
                    <div class="form-group">
                        <?= $this->Form->control('password', ['class' => 'form-control']) ?>
                    </div>
                    <div class="form-group">
                        <?= $this->Form->control('email', ['class' => 'form-control']) ?>
                    </div>
                    <div class="form-group">
                        <?= $this->Form->control('full_name', ['class' => 'form-control']) ?>
                    </div>
                    <div class="form-group">
                        <?= $this->Form->control('is_active', ['class' => 'form-check-input ml-2']) ?>
                        <label class="ml-4">Is Active</label>
                    </div>
                    <div class="form-group">
                        <?= $this->Form->control('roles._ids', [
                            'options' => $roles,
                            'class' => 'form-control',
                            'multiple' => true,
                            'id' => 'role-select'
                        ]) ?>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple roles.</small>
                    </div>
                    
                    <hr>
                    <div id="institution-section" style="display: none;">
                        <h6 class="text-secondary">Institution Assignment</h6>
                        <div class="alert alert-info p-2">
                            <small><i class="fa fa-info-circle"></i> Select the institution this user belongs to.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Institution Type</label>
                            <select id="institution-type-select" class="form-control" disabled>
                                <option value="">Select Type</option>
                                <option value="SpecialSkillSupportInstitution">LPK Penyangga</option>
                                <option value="VocationalTrainingInstitution">LPK SO</option>
                            </select>
                            <?= $this->Form->hidden('institution_type', ['id' => 'institution-type-hidden']) ?>
                        </div>

                        <div class="form-group" id="lpk-penyangga-group" style="display: none;">
                            <label>LPK Penyangga</label>
                            <?= $this->Form->select('lpk_penyangga_id', $lpkPenyangga, [
                                'class' => 'form-control institution-select',
                                'id' => 'lpk-penyangga-select',
                                'empty' => 'Select LPK Penyangga'
                            ]) ?>
                        </div>

                        <div class="form-group" id="lpk-so-group" style="display: none;">
                            <label>LPK SO</label>
                            <?= $this->Form->select('lpk_so_id', $lpkSo, [
                                'class' => 'form-control institution-select',
                                'id' => 'lpk-so-select',
                                'empty' => 'Select LPK SO'
                            ]) ?>
                        </div>

                        <?= $this->Form->hidden('institution_id', ['id' => 'institution-id-hidden']) ?>
                    </div>
                </fieldset>
                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role-select');
    const institutionSection = document.getElementById('institution-section');
    const typeSelect = document.getElementById('institution-type-select');
    const typeHidden = document.getElementById('institution-type-hidden');
    const idHidden = document.getElementById('institution-id-hidden');
    const penyanggaGroup = document.getElementById('lpk-penyangga-group');
    const soGroup = document.getElementById('lpk-so-group');
    const penyanggaSelect = document.getElementById('lpk-penyangga-select');
    const soSelect = document.getElementById('lpk-so-select');

    function updateInstitutionVisibility() {
        // Get selected role text
        const selectedOptions = Array.from(roleSelect.selectedOptions).map(opt => opt.text.toLowerCase());
        const selectedValues = Array.from(roleSelect.selectedOptions).map(opt => opt.value); // IDs might be numbers

        let showInstitution = false;
        let type = '';

        // Check for LPK roles (adjust strings based on actual role names)
        if (selectedOptions.some(t => t.includes('penyangga'))) {
            showInstitution = true;
            type = 'SpecialSkillSupportInstitution';
        } else if (selectedOptions.some(t => t.includes('so') || t.includes('sending'))) {
            showInstitution = true;
            type = 'VocationalTrainingInstitution';

        if (showInstitution) {
            institutionSection.style.display = 'block';
            typeSelect.value = type;
            typeHidden.value = type;
            
            if (type === 'SpecialSkillSupportInstitution') {
                penyanggaGroup.style.display = 'block';
                soGroup.style.display = 'none';
                soSelect.value = ''; // Reset other
            } else {
                penyanggaGroup.style.display = 'none';
                soGroup.style.display = 'block';
                penyanggaSelect.value = ''; // Reset other
        } else {
            institutionSection.style.display = 'none';
            typeSelect.value = '';
            typeHidden.value = '';
            idHidden.value = '';
            penyanggaSelect.value = '';
            soSelect.value = '';

    // Listen for role changes
    roleSelect.addEventListener('change', updateInstitutionVisibility);

    // Listen for institution selection
    penyanggaSelect.addEventListener('change', function() {
        idHidden.value = this.value;
    });

    soSelect.addEventListener('change', function() {
        idHidden.value = this.value;
    });

    // Initial check
    updateInstitutionVisibility();
});
</script>

