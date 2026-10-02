<?php
/**
 * Change your own password.
 *
 * The current one is asked for because a session left open on a shared machine
 * is the ordinary case, not the unlikely one. The rules come from the action,
 * which is where they are enforced, so this list cannot drift from the check.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var array $rules
 */
?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-lock text-primary mr-2"></i>
                <h5 class="mb-0"><?= __('Change Password') ?></h5>
            </div>
            <div class="card-body">

                <p class="text-muted">
                    <?= __('Signed in as') ?>
                    <strong><?= h($user->username) ?></strong>
                    <?php if ($user->full_name): ?>
                        (<?= h($user->full_name) ?>)
                    <?php endif; ?>
                </p>

                <div class="alert alert-info">
                    <strong><?= __('Password Requirements:') ?></strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($rules as $rule): ?>
                            <li><?= h($rule) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?= $this->Form->create(null, ['autocomplete' => 'off']) ?>
                    <div class="form-group">
                        <label class="form-label" for="current-password"><?= __('Current Password') ?></label>
                        <?= $this->Form->control('current_password', [
                            'type' => 'password',
                            'id' => 'current-password',
                            'class' => 'form-control',
                            'label' => false,
                            'required' => true,
                            'autocomplete' => 'current-password',
                        ]) ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password"><?= __('New Password') ?></label>
                        <div class="input-group">
                            <?= $this->Form->control('password', [
                                'type' => 'password',
                                'id' => 'password',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'autocomplete' => 'new-password',
                            ]) ?>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                        title="<?= __('Show or hide') ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm-password"><?= __('Confirm New Password') ?></label>
                        <?= $this->Form->control('confirm_password', [
                            'type' => 'password',
                            'id' => 'confirm-password',
                            'class' => 'form-control',
                            'label' => false,
                            'required' => true,
                            'autocomplete' => 'new-password',
                        ]) ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <?= $this->Html->link(
                            '<i class="fas fa-times"></i> ' . __('Cancel'),
                            ['action' => 'profile'],
                            ['class' => 'btn btn-outline-secondary', 'escape' => false]
                        ) ?>
                        <?= $this->Form->button(
                            '<i class="fas fa-save"></i> ' . __('Change Password'),
                            ['class' => 'btn btn-primary', 'escape' => false]
                        ) ?>
                    </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var password = document.getElementById('password');
    var confirmation = document.getElementById('confirm-password');
    var toggle = document.getElementById('togglePassword');
    if (!password || !confirmation || !toggle) {
        return;
    }
    toggle.addEventListener('click', function () {
        var showing = password.getAttribute('type') === 'text';
        password.setAttribute('type', showing ? 'password' : 'text');
        confirmation.setAttribute('type', showing ? 'password' : 'text');
        toggle.querySelector('i').className = showing ? 'fas fa-eye' : 'fas fa-eye-slash';
    });
})();
</script>
