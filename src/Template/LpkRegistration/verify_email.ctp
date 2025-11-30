<?php
/**
 * LPK Registration - Step 2: Email Verification
 * LPK clicks verification link from email
 * Public page - no authentication required
 */
?>
<div class="verify-email-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <?php if (isset($tokenStatus) && $tokenStatus === 'success'): ?>
                    <!-- Success Card -->
                    <div class="card card-success card-outline">
                        <div class="card-header text-center">
                            <h3 class="card-title">
                                <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="mb-3"><?= __('Email Verified Successfully!') ?></h4>
                            <p class="lead"><?= __('Your email address has been verified.') ?></p>
                            <p><?= __('Institution: {0}', h($institution->name)) ?></p>
                            <p><?= __('Email: {0}', h($institution->email)) ?></p>
                            
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle"></i>
                                <?= __('Next Step: Set your password to activate your account') ?>
                            </div>
                            
                            <div class="countdown-box mt-3 mb-3">
                                <p class="mb-2"><?= __('Redirecting to password setup in:') ?></p>
                                <h2 id="countdown" class="text-primary">5</h2>
                            </div>
                            
                            <?= $this->Html->link(
                                '<i class="fas fa-key"></i> ' . __('Continue to Password Setup'),
                                ['action' => 'setPassword', $institution->id],
                                [
                                    'class' => 'btn btn-primary btn-lg btn-block',
                                    'escape' => false,
                                    'id' => 'continue-btn'
                                ]
                            ) ?>
                        </div>
                    </div>
                    
                    <script>
                    // Countdown and auto-redirect
                    var countdown = 5;
                    var redirectUrl = '<?= $redirectUrl ?>';
                    
                    var countdownInterval = setInterval(function() {
                        countdown--;
                        $('#countdown').text(countdown);
                        
                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            window.location.href = redirectUrl;
                        }
                    }, 1000);
                    
                    // Manual continue button
                    $('#continue-btn').on('click', function() {
                        clearInterval(countdownInterval);
                    });
                    </script>
                    
                <?php elseif (isset($tokenStatus) && $tokenStatus === 'invalid'): ?>
                    <!-- Invalid Token Card -->
                    <div class="card card-danger card-outline">
                        <div class="card-header text-center">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="mb-3"><?= __('Invalid Verification Link') ?></h4>
                            <p class="lead"><?= __('This verification link is invalid, expired, or has already been used.') ?></p>
                            
                            <div class="alert alert-warning mt-4">
                                <h5><i class="fas fa-lightbulb"></i> <?= __('Possible Reasons:') ?></h5>
                                <ul class="text-left">
                                    <li><?= __('The link has expired (valid for 24 hours only)') ?></li>
                                    <li><?= __('The link has already been used') ?></li>
                                    <li><?= __('The link is incomplete or corrupted') ?></li>
                                    <li><?= __('Your email has already been verified') ?></li>
                                </ul>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6 mb-2">
                                    <?= $this->Html->link(
                                        '<i class="fas fa-redo"></i> ' . __('Request New Link'),
                                        '#',
                                        [
                                            'class' => 'btn btn-warning btn-block',
                                            'escape' => false,
                                            'onclick' => 'alert("Please contact your administrator to resend the verification email"); return false;'
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <?= $this->Html->link(
                                        '<i class="fas fa-headset"></i> ' . __('Contact Support'),
                                        'mailto:support@asahifamily.id',
                                        [
                                            'class' => 'btn btn-info btn-block',
                                            'escape' => false
                                        ]
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif (isset($tokenStatus) && $tokenStatus === 'not_found'): ?>
                    <!-- Institution Not Found Card -->
                    <div class="card card-danger card-outline">
                        <div class="card-header text-center">
                            <h3 class="card-title">
                                <i class="fas fa-times-circle text-danger" style="font-size: 3rem;"></i>
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="mb-3"><?= __('Institution Not Found') ?></h4>
                            <p class="lead"><?= __('The institution associated with this verification link could not be found.') ?></p>
                            
                            <div class="alert alert-danger mt-4">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= __('This may indicate a system error. Please contact support for assistance.') ?>
                            </div>
                            
                            <?= $this->Html->link(
                                '<i class="fas fa-headset"></i> ' . __('Contact Support'),
                                'mailto:support@asahifamily.id',
                                [
                                    'class' => 'btn btn-danger btn-lg btn-block mt-3',
                                    'escape' => false
                                ]
                            ) ?>
                        </div>
                    </div>
                    
                <?php elseif (isset($tokenStatus) && $tokenStatus === 'error'): ?>
                    <!-- Error Card -->
                    <div class="card card-danger card-outline">
                        <div class="card-header text-center">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="mb-3"><?= __('Verification Error') ?></h4>
                            <p class="lead"><?= __('An error occurred while verifying your email.') ?></p>
                            
                            <div class="alert alert-warning mt-4">
                                <i class="fas fa-info-circle"></i>
                                <?= __('Please try again or contact support if the problem persists.') ?>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6 mb-2">
                                    <?= $this->Html->link(
                                        '<i class="fas fa-redo"></i> ' . __('Try Again'),
                                        'javascript:location.reload()',
                                        [
                                            'class' => 'btn btn-warning btn-block',
                                            'escape' => false
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <?= $this->Html->link(
                                        '<i class="fas fa-headset"></i> ' . __('Contact Support'),
                                        'mailto:support@asahifamily.id',
                                        [
                                            'class' => 'btn btn-info btn-block',
                                            'escape' => false
                                        ]
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Loading/Processing Card -->
                    <div class="card card-info card-outline">
                        <div class="card-body text-center">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only"><?= __('Loading...') ?></span>
                            </div>
                            <h4 class="mb-3"><?= __('Verifying Your Email...') ?></h4>
                            <p class="lead"><?= __('Please wait while we verify your email address.') ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Help Box -->
                <div class="card card-light mt-3">
                    <div class="card-body">
                        <h5><i class="fas fa-question-circle"></i> <?= __('Need Help?') ?></h5>
                        <p class="mb-2"><?= __('If you encounter any issues, please contact:') ?></p>
                        <ul class="mb-0">
                            <li><?= __('Email: support@asahifamily.id') ?></li>
                            <li><?= __('Phone: +62 21 1234 5678') ?></li>
                            <li><?= __('Office Hours: Monday - Friday, 9:00 AM - 5:00 PM WIB') ?></li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<style>
.verify-email-page {
    padding: 3rem 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.verify-email-page .card {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: none;
    border-radius: 10px;
}

.verify-email-page .card-header {
    background-color: transparent;
    border-bottom: none;
    padding: 2rem 1rem 0;
}

.verify-email-page .card-body {
    padding: 2rem;
}

.countdown-box {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
}

#countdown {
    font-size: 3rem;
    font-weight: bold;
    margin: 0;
}

.spinner-border {
    border-width: 0.25rem;
}

@media (max-width: 768px) {
    .verify-email-page {
        padding: 1rem 0;
    }
    
    .verify-email-page .card-body {
        padding: 1.5rem;
    }
}
</style>
