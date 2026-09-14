<?php
/**
 * Password setup, the step after the email is verified.
 *
 * The action has always rendered this template and asked for the login layout,
 * but neither existed, so the page answered 500 - an LPK could verify its email
 * and then had no way to finish registering.
 *
 * The rules below are the ones setPassword() actually enforces: at least eight
 * characters with an upper case letter, a lower case letter, a digit and a
 * symbol. They are listed so the requirement is visible before submitting
 * rather than discovered one flash message at a time.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VocationalTrainingInstitution $institution
 */
$this->assign('title', __('Create a secure password'));
?>
<div class="card">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <i class="fas fa-key fa-2x text-primary mb-2"></i>
            <h4 class="mb-1"><?= __('Create a secure password') ?></h4>
            <p class="text-muted mb-0">
                <?= __('Institution:') ?> <strong><?= h($institution->name) ?></strong>
            </p>
        </div>

        <div class="alert alert-info">
            <strong><?= __('Password Requirements:') ?></strong>
            <ul class="mb-0 mt-2">
                <li><?= __('Minimum 8 characters, include uppercase, lowercase, number & symbol') ?></li>
            </ul>
        </div>

        <?= $this->Form->create(null, ['autocomplete' => 'off']) ?>
            <div class="mb-3">
                <label class="form-label" for="password"><?= __('Password') ?></label>
                <?= $this->Form->control('password', [
                    'type' => 'password',
                    'id' => 'password',
                    'class' => 'form-control',
                    'label' => false,
                    'required' => true,
                    'autocomplete' => 'new-password',
                ]) ?>
            </div>

            <div class="mb-4">
                <label class="form-label" for="confirm-password"><?= __('Confirm Password') ?></label>
                <?= $this->Form->control('confirm_password', [
                    'type' => 'password',
                    'id' => 'confirm-password',
                    'class' => 'form-control',
                    'label' => false,
                    'required' => true,
                    'autocomplete' => 'new-password',
                ]) ?>
            </div>

            <div class="d-grid">
                <?= $this->Form->button(
                    '<i class="fas fa-check"></i> ' . __('Complete Registration'),
                    ['class' => 'btn btn-primary btn-lg', 'escape' => false]
                ) ?>
            </div>
        <?= $this->Form->end() ?>
    </div>
</div>
