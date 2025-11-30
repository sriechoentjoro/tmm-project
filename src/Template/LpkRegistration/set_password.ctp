<?php
/**
 * LPK Registration - Step 3: Set Password
 * LPK sets password to activate account
 * Public page - no authentication required
 */
?>
<div class="set-password-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card card-primary card-outline">
                    <div class="card-header text-center">
                        <h3>
                            <i class="fas fa-key"></i> <?= __('Set Your Password') ?>
                        </h3>
                    </div>
                    
                    <?= $this->Form->create(null, ['id' => 'setPasswordForm']) ?>
                    <div class="card-body">
                        
                        <!-- Institution Info -->
                        <div class="alert alert-info">
                            <h5><i class="fas fa-building"></i> <?= __('Institution Information') ?></h5>
                            <p class="mb-1"><strong><?= __('Name:') ?></strong> <?= h($institution->name) ?></p>
                            <p class="mb-0"><strong><?= __('Email:') ?></strong> <?= h($institution->email) ?></p>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-shield-alt"></i>
                            <strong><?= __('Password Requirements:') ?></strong>
                            <ul class="mb-0 mt-2" id="password-requirements">
                                <li id="req-length">
                                    <i class="fas fa-circle text-muted"></i>
                                    <?= __('At least 8 characters') ?>
                                </li>
                                <li id="req-uppercase">
                                    <i class="fas fa-circle text-muted"></i>
                                    <?= __('At least one uppercase letter (A-Z)') ?>
                                </li>
                                <li id="req-lowercase">
                                    <i class="fas fa-circle text-muted"></i>
                                    <?= __('At least one lowercase letter (a-z)') ?>
                                </li>
                                <li id="req-number">
                                    <i class="fas fa-circle text-muted"></i>
                                    <?= __('At least one number (0-9)') ?>
                                </li>
                                <li id="req-special">
                                    <i class="fas fa-circle text-muted"></i>
                                    <?= __('At least one special character (!@#$%^&*)') ?>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="form-group">
                            <label class="form-label">
                                <?= __('Password') ?> <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <?= $this->Form->control('password', [
                                    'type' => 'password',
                                    'class' => 'form-control',
                                    'placeholder' => __('Enter your password'),
                                    'label' => false,
                                    'required' => true,
                                    'id' => 'password-input'
                                ]) ?>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Password Strength Meter -->
                        <div class="form-group">
                            <label class="form-label"><?= __('Password Strength:') ?></label>
                            <div class="progress" style="height: 25px;">
                                <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%">
                                    <span id="password-strength-text"><?= __('No password') ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Confirm Password Field -->
                        <div class="form-group">
                            <label class="form-label">
                                <?= __('Confirm Password') ?> <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <?= $this->Form->control('confirm_password', [
                                    'type' => 'password',
                                    'class' => 'form-control',
                                    'placeholder' => __('Re-enter your password'),
                                    'label' => false,
                                    'required' => true,
                                    'id' => 'confirm-password-input'
                                ]) ?>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-confirm-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <small id="password-match-message" class="form-text"></small>
                        </div>
                        
                        <!-- Terms & Conditions -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <?= $this->Form->checkbox('accept_terms', [
                                    'id' => 'accept-terms',
                                    'class' => 'custom-control-input',
                                    'required' => true
                                ]) ?>
                                <label class="custom-control-label" for="accept-terms">
                                    <?= __('I agree to the ') ?>
                                    <a href="#" data-toggle="modal" data-target="#termsModal">
                                        <?= __('Terms and Conditions') ?>
                                    </a>
                                </label>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="card-footer">
                        <?= $this->Form->button(
                            '<i class="fas fa-check"></i> ' . __('Activate Account'),
                            [
                                'class' => 'btn btn-primary btn-lg btn-block',
                                'escape' => false,
                                'id' => 'submit-btn',
                                'disabled' => true
                            ]
                        ) ?>
                        <p class="text-center mt-3 mb-0">
                            <small class="text-muted">
                                <?= __('By activating your account, you will be able to login and access the TMM system.') ?>
                            </small>
                        </p>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
                
                <!-- Help Box -->
                <div class="card card-light mt-3">
                    <div class="card-body">
                        <h5><i class="fas fa-question-circle"></i> <?= __('Need Help?') ?></h5>
                        <p class="mb-2"><?= __('If you encounter any issues, please contact:') ?></p>
                        <ul class="mb-0">
                            <li><?= __('Email: support@asahifamily.id') ?></li>
                            <li><?= __('Phone: +62 21 1234 5678') ?></li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- Terms & Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('Terms and Conditions') ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><?= __('1. Account Usage') ?></h6>
                <p><?= __('You agree to use this account only for authorized purposes within the TMM system.') ?></p>
                
                <h6><?= __('2. Data Privacy') ?></h6>
                <p><?= __('All data entered into the system will be handled according to applicable privacy laws and regulations.') ?></p>
                
                <h6><?= __('3. Security') ?></h6>
                <p><?= __('You are responsible for maintaining the confidentiality of your password and account.') ?></p>
                
                <h6><?= __('4. Compliance') ?></h6>
                <p><?= __('You must comply with all applicable laws and regulations when using this system.') ?></p>
                
                <h6><?= __('5. Account Termination') ?></h6>
                <p><?= __('We reserve the right to terminate accounts that violate these terms.') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?= __('Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var passwordInput = $('#password-input');
    var confirmInput = $('#confirm-password-input');
    var submitBtn = $('#submit-btn');
    var acceptTerms = $('#accept-terms');
    
    // Password strength checker
    function checkPasswordStrength(password) {
        var strength = 0;
        var requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };
        
        // Update requirements list
        updateRequirement('req-length', requirements.length);
        updateRequirement('req-uppercase', requirements.uppercase);
        updateRequirement('req-lowercase', requirements.lowercase);
        updateRequirement('req-number', requirements.number);
        updateRequirement('req-special', requirements.special);
        
        // Calculate strength
        if (requirements.length) strength++;
        if (requirements.uppercase) strength++;
        if (requirements.lowercase) strength++;
        if (requirements.number) strength++;
        if (requirements.special) strength++;
        
        return {
            score: strength,
            allMet: Object.values(requirements).every(v => v === true)
        };
    }
    
    function updateRequirement(id, met) {
        var element = $('#' + id);
        var icon = element.find('i');
        
        if (met) {
            icon.removeClass('fa-circle text-muted').addClass('fa-check-circle text-success');
        } else {
            icon.removeClass('fa-check-circle text-success').addClass('fa-circle text-muted');
        }
    }
    
    function updateStrengthBar(score) {
        var bar = $('#password-strength-bar');
        var text = $('#password-strength-text');
        var width = (score / 5) * 100;
        
        bar.css('width', width + '%');
        bar.removeClass('bg-danger bg-warning bg-success');
        
        if (score === 0) {
            text.text('<?= __("No password") ?>');
            bar.addClass('bg-secondary');
        } else if (score <= 2) {
            text.text('<?= __("Weak") ?>');
            bar.addClass('bg-danger');
        } else if (score <= 4) {
            text.text('<?= __("Medium") ?>');
            bar.addClass('bg-warning');
        } else {
            text.text('<?= __("Strong") ?>');
            bar.addClass('bg-success');
        }
    }
    
    // Check password on keyup
    passwordInput.on('keyup', function() {
        var password = $(this).val();
        var result = checkPasswordStrength(password);
        
        updateStrengthBar(result.score);
        checkFormValid();
    });
    
    // Check password match
    confirmInput.on('keyup', function() {
        var password = passwordInput.val();
        var confirm = $(this).val();
        var message = $('#password-match-message');
        
        if (confirm.length > 0) {
            if (password === confirm) {
                message.html('<i class="fas fa-check-circle text-success"></i> <?= __("Passwords match") ?>');
            } else {
                message.html('<i class="fas fa-times-circle text-danger"></i> <?= __("Passwords do not match") ?>');
            }
        } else {
            message.html('');
        }
        
        checkFormValid();
    });
    
    // Check if form is valid
    function checkFormValid() {
        var password = passwordInput.val();
        var confirm = confirmInput.val();
        var terms = acceptTerms.is(':checked');
        var result = checkPasswordStrength(password);
        
        var isValid = result.allMet && (password === confirm) && confirm.length > 0 && terms;
        
        submitBtn.prop('disabled', !isValid);
    }
    
    // Terms checkbox
    acceptTerms.on('change', checkFormValid);
    
    // Toggle password visibility
    $('#toggle-password').on('click', function() {
        var icon = $(this).find('i');
        var input = passwordInput;
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    $('#toggle-confirm-password').on('click', function() {
        var icon = $(this).find('i');
        var input = confirmInput;
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Form submission
    $('#setPasswordForm').on('submit', function() {
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> <?= __("Activating account...") ?>');
    });
});
</script>

<style>
.set-password-page {
    padding: 3rem 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.set-password-page .card {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: none;
    border-radius: 10px;
}

.set-password-page .card-header {
    background-color: #667eea;
    color: white;
    border-radius: 10px 10px 0 0 !important;
    padding: 1.5rem;
}

.set-password-page .card-body {
    padding: 2rem;
}

#password-requirements li {
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

#password-requirements li i {
    margin-right: 0.5rem;
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.3s ease, background-color 0.3s ease;
    font-weight: bold;
}

.input-group-append .btn {
    border-color: #ced4da;
}

@media (max-width: 768px) {
    .set-password-page {
        padding: 1rem 0;
    }
    
    .set-password-page .card-body {
        padding: 1.5rem;
    }
}
</style>
