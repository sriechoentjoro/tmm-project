<?php
/**
 * Institution Registration Completion Wizard
 * 
 * @var \App\View\AppView $this
 * @var object $institution
 * @var string $token
 * @var bool $expired
 */
?>
<div class="institution-registration-wizard">
    <div class="container" style="max-width: 800px; margin: 50px auto;">
        
        <?php if (isset($expired) && $expired): ?>
            <!-- Expired Token View -->
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <i class="fas fa-clock fa-4x text-warning mb-4"></i>
                    <h2 class="mb-3">Registration Link Expired</h2>
                    <p class="lead">This registration link has expired.</p>
                    <p>Please contact the system administrator to request a new registration link.</p>
                    <div class="mt-4">
                        <p><strong>Institution:</strong> <?= h($institution->name ?? $institution->company_name) ?></p>
                        <p><strong>Email:</strong> <?= h($institution->email) ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Registration Wizard -->
            <div class="card shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-building"></i>
                        Complete Your Institution Registration
                    </h3>
                </div>
                
                <div class="card-body p-4">
                    <!-- Progress Steps -->
                    <div class="wizard-steps mb-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="step completed">
                                    <div class="step-number">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-label">Verify</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="step completed">
                                    <div class="step-number">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-label">Review</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="step active">
                                    <div class="step-number">3</div>
                                    <div class="step-label">Set Password</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Institution Details (Read-Only) -->
                    <div class="institution-details mb-4 p-3 bg-light rounded">
                        <h5 class="mb-3">
                            <i class="fas fa-info-circle text-primary"></i>
                            Institution Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Name:</strong><br>
                                <?= h($institution->name ?? $institution->company_name) ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Email:</strong><br>
                                <?= h($institution->email) ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Username:</strong><br>
                                <code><?= h($institution->username) ?></code>
                            </div>
                            <?php if (!empty($institution->address)): ?>
                            <div class="col-md-6 mb-2">
                                <strong>Address:</strong><br>
                                <?= h($institution->address) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Password Form -->
                    <?= $this->Form->create(null, ['class' => 'registration-form']) ?>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-shield-alt"></i>
                        <strong>Create a secure password</strong><br>
                        Your password must be at least 8 characters long and should include uppercase, lowercase, numbers, and symbols.
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            Password <span class="text-danger">*</span>
                        </label>
                        <?= $this->Form->control('password', [
                            'type' => 'password',
                            'class' => 'form-control form-control-lg',
                            'placeholder' => 'Enter your password',
                            'label' => false,
                            'required' => true,
                            'id' => 'password',
                            'autocomplete' => 'new-password'
                        ]) ?>
                        <div class="password-strength mt-2" id="password-strength"></div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            Confirm Password <span class="text-danger">*</span>
                        </label>
                        <?= $this->Form->control('password_confirm', [
                            'type' => 'password',
                            'class' => 'form-control form-control-lg',
                            'placeholder' => 'Re-enter your password',
                            'label' => false,
                            'required' => true,
                            'id' => 'password-confirm',
                            'autocomplete' => 'new-password'
                        ]) ?>
                        <div class="invalid-feedback" id="password-match-error"></div>
                    </div>

                    <!-- Password Requirements Checklist -->
                    <div class="password-requirements mb-4 p-3 border rounded">
                        <h6 class="mb-2">Password Requirements:</h6>
                        <ul class="list-unstyled mb-0">
                            <li id="req-length">
                                <i class="fas fa-circle text-muted"></i>
                                At least 8 characters
                            </li>
                            <li id="req-uppercase">
                                <i class="fas fa-circle text-muted"></i>
                                Contains uppercase letter (A-Z)
                            </li>
                            <li id="req-lowercase">
                                <i class="fas fa-circle text-muted"></i>
                                Contains lowercase letter (a-z)
                            </li>
                            <li id="req-number">
                                <i class="fas fa-circle text-muted"></i>
                                Contains number (0-9)
                            </li>
                            <li id="req-special">
                                <i class="fas fa-circle text-muted"></i>
                                Contains special character (!@#$%^&*)
                            </li>
                        </ul>
                    </div>

                    <div class="form-actions">
                        <?= $this->Form->button(
                            '<i class="fas fa-check-circle"></i> Complete Registration',
                            [
                                'class' => 'btn btn-primary btn-lg btn-block',
                                'id' => 'submit-btn',
                                'type' => 'submit'
                            ]
                        ) ?>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

.wizard-steps .step {
    position: relative;

.wizard-steps .step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 10px;

.wizard-steps .step.completed .step-number {
    background: #28a745;
    color: white;

.wizard-steps .step.active .step-number {
    background: #667eea;
    color: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);

.wizard-steps .step-label {
    font-size: 14px;
    color: #6c757d;

.password-strength {
    height: 5px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;

.password-strength .strength-bar {
    height: 100%;
    transition: all 0.3s;

.password-requirements li {
    padding: 5px 0;
    transition: all 0.3s;

.password-requirements li.met {
    color: #28a745;

.password-requirements li.met i {
    color: #28a745 !important;
</style>

<?php $this->start('script'); ?>
<script>
$(document).ready(function() {
    const $password = $('#password');
    const $passwordConfirm = $('#password-confirm');
    const $submitBtn = $('#submit-btn');

    // Password strength indicator
    $password.on('input', function() {
        const password = $(this).val();
        let strength = 0;
        
        // Check requirements
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        // Update checklist
        $('#req-length').toggleClass('met', hasLength);
        $('#req-uppercase').toggleClass('met', hasUppercase);
        $('#req-lowercase').toggleClass('met', hasLowercase);
        $('#req-number').toggleClass('met', hasNumber);
        $('#req-special').toggleClass('met', hasSpecial);

        // Calculate strength
        if (hasLength) strength++;
        if (hasUppercase) strength++;
        if (hasLowercase) strength++;
        if (hasNumber) strength++;
        if (hasSpecial) strength++;

        // Update strength bar
        const colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997'];
        const widths = [20, 40, 60, 80, 100];
        
        $('#password-strength').html(
            `<div class="strength-bar" style="width: ${widths[strength - 1] || 0}%; background: ${colors[strength - 1] || '#e9ecef'}"></div>`
        );
    });

    // Password confirmation validation
    $passwordConfirm.on('input', function() {
        const password = $password.val();
        const confirm = $(this).val();
        
        if (confirm && password !== confirm) {
            $(this).addClass('is-invalid');
            $('#password-match-error').text('Passwords do not match').show();
        } else {
            $(this).removeClass('is-invalid');
            $('#password-match-error').hide();
    });

    // Form validation
    $('.registration-form').on('submit', function(e) {
        const password = $password.val();
        const confirm = $passwordConfirm.val();

        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long');
            return false;

        if (password !== confirm) {
            e.preventDefault();
            alert('Passwords do not match');
            return false;
    });
});
</script>
<?php $this->end(); ?>

