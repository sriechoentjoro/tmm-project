<?php
/**
 * Result of clicking the verification link in the registration email.
 *
 * The action has always set $tokenStatus and rendered this template, but the
 * file did not exist, so the link answered 500. Only the failing branches are
 * rendered here: on success the action redirects to the password page, so the
 * 'success' case is a courtesy fallback in case that redirect is ever removed.
 *
 * @var \App\View\AppView $this
 * @var string $tokenStatus  invalid | not_found | success | error
 * @var string|null $redirectUrl
 * @var \App\Model\Entity\VocationalTrainingInstitution|null $institution
 */
$this->assign('title', __('Verify Your Email Address'));

$panels = [
    'success' => [
        'icon' => 'fa-check-circle',
        'colour' => 'success',
        'heading' => __('Email Verified Successfully'),
        'body' => __('Your email address has been successfully verified.'),
    ],
    'invalid' => [
        'icon' => 'fa-unlink',
        'colour' => 'danger',
        'heading' => __('Registration Link Expired'),
        'body' => __('This verification link is invalid, expired, or has already been used.'),
    ],
    'not_found' => [
        'icon' => 'fa-question-circle',
        'colour' => 'warning',
        'heading' => __('Institution not found. Please contact support.'),
        'body' => __('Please contact the system administrator to request a new registration link.'),
    ],
    'error' => [
        'icon' => 'fa-exclamation-triangle',
        'colour' => 'danger',
        'heading' => __('Error updating verification status. Please try again or contact support.'),
        'body' => __('Please try again later or contact support if the problem persists.'),
    ],
];

$status = isset($tokenStatus) && isset($panels[$tokenStatus]) ? $tokenStatus : 'error';
$panel = $panels[$status];
?>
<div class="card">
    <div class="card-body text-center p-4">
        <i class="fas <?= $panel['icon'] ?> fa-3x text-<?= $panel['colour'] ?> mb-3"></i>
        <h4 class="mb-3"><?= $panel['heading'] ?></h4>
        <p class="text-muted mb-4"><?= $panel['body'] ?></p>

        <?php if ($status === 'success' && !empty($redirectUrl)): ?>
            <p class="mb-4">
                <?= __('Please log in and change your temporary password to a secure password of your choice.') ?>
            </p>
            <a href="<?= h($redirectUrl) ?>" class="btn btn-success btn-lg">
                <i class="fas fa-key"></i> <?= __('Create a secure password') ?>
            </a>
        <?php else: ?>
            <?= $this->Html->link(
                '<i class="fas fa-sign-in-alt"></i> ' . __('Log in to your account'),
                ['prefix' => false, 'controller' => 'Users', 'action' => 'login'],
                ['class' => 'btn btn-outline-secondary', 'escape' => false]
            ) ?>
        <?php endif; ?>
    </div>
</div>
