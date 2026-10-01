<?php
/**
 * Reset one account's password, as an administrator.
 *
 * The screen names the account before it changes anything. An administrator
 * reaching this from a list of users or a list of LPKs should not have to trust
 * that the id in the URL was the row they meant - a password reset on the wrong
 * account locks out somebody who was working.
 *
 * The rules come from the action, which is the same place they are enforced, so
 * the list cannot drift from the check. Generating one fills both boxes and
 * shows it, because an administrator setting a password for somebody else has to
 * be able to read it back to them.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\VocationalTrainingInstitution|\App\Model\Entity\SpecialSkillSupportInstitution|null $institution
 * @var array $rules
 */
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-key text-primary mr-2"></i>
                <h5 class="mb-0"><?= __('Reset Password') ?></h5>
            </div>
            <div class="card-body">

                <table class="table table-sm mb-4">
                    <tbody>
                        <tr>
                            <th style="width: 180px;"><?= __('Username') ?></th>
                            <td><strong><?= h($user->username) ?></strong></td>
                        </tr>
                        <tr>
                            <th><?= __('Full Name') ?></th>
                            <td><?= h($user->full_name ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Email') ?></th>
                            <td><?= h($user->email) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Roles') ?></th>
                            <td>
                                <?php if (empty($user->roles)): ?>
                                    <span class="text-muted"><?= __('None') ?></span>
                                <?php else: ?>
                                    <?php foreach ($user->roles as $role): ?>
                                        <span class="badge badge-secondary"><?= h($role->name) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if ($user->institution_id): ?>
                            <tr>
                                <th><?= __('Institution') ?></th>
                                <td>
                                    <?php if ($institution === null): ?>
                                        <span class="text-danger">
                                            <?= __('Institution #{0} is not on file any more.', $user->institution_id) ?>
                                        </span>
                                    <?php else: ?>
                                        <?= h($institution->has('name') ? $institution->name : $institution->title) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th><?= __('Enabled') ?></th>
                            <td>
                                <?php if ($user->is_active): ?>
                                    <span class="badge badge-success"><?= __('Yes') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= __('No') ?></span>
                                    <span class="text-muted small">
                                        <?= __('A new password will not let them in until the account is enabled.') ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="alert alert-info">
                    <strong><?= __('Password Requirements:') ?></strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($rules as $rule): ?>
                            <li><?= h($rule) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= __('The new password is not sent by email. Hand it over yourself, and ask them to change it after they log in.') ?>
                </div>

                <?= $this->Form->create(null, ['autocomplete' => 'off', 'id' => 'resetPasswordForm']) ?>
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
                                <button class="btn btn-outline-secondary" type="button" id="generatePassword"
                                        title="<?= __('Generate one') ?>">
                                    <i class="fas fa-random"></i>
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
                            ['action' => 'index'],
                            ['class' => 'btn btn-outline-secondary', 'escape' => false]
                        ) ?>
                        <?= $this->Form->button(
                            '<i class="fas fa-save"></i> ' . __('Reset Password'),
                            ['class' => 'btn btn-primary', 'escape' => false,
                             'confirm' => __('Reset the password for {0}?', $user->username)]
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
    var generate = document.getElementById('generatePassword');
    if (!password || !confirmation || !toggle || !generate) {
        return;
    }

    toggle.addEventListener('click', function () {
        var showing = password.getAttribute('type') === 'text';
        password.setAttribute('type', showing ? 'password' : 'text');
        confirmation.setAttribute('type', showing ? 'password' : 'text');
        toggle.querySelector('i').className = showing ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    generate.addEventListener('click', function () {
        // One character drawn from each required set first, so what is generated
        // always satisfies the rules the action enforces - a generator that can
        // produce a rejected password is worse than none.
        var sets = [
            'ABCDEFGHJKLMNPQRSTUVWXYZ',
            'abcdefghijkmnpqrstuvwxyz',
            '23456789',
            '!@#$%^&*?'
        ];
        var pool = sets.join('');
        var picks = [];
        var random = function (max) {
            if (window.crypto && window.crypto.getRandomValues) {
                var buffer = new Uint32Array(1);
                window.crypto.getRandomValues(buffer);
                return buffer[0] % max;
            }
            return Math.floor(Math.random() * max);
        };

        sets.forEach(function (set) { picks.push(set.charAt(random(set.length))); });
        while (picks.length < 14) {
            picks.push(pool.charAt(random(pool.length)));
        }
        for (var i = picks.length - 1; i > 0; i--) {
            var j = random(i + 1);
            var swap = picks[i];
            picks[i] = picks[j];
            picks[j] = swap;
        }

        var generated = picks.join('');
        password.value = generated;
        confirmation.value = generated;
        // Shown, because it has to be read out to whoever it belongs to.
        password.setAttribute('type', 'text');
        confirmation.setAttribute('type', 'text');
        toggle.querySelector('i').className = 'fas fa-eye-slash';
        password.focus();
    });
})();
</script>
