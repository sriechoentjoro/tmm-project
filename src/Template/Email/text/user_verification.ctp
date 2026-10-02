<?php
/**
 * The plain-text half of the account verification mail.
 *
 * @var \App\View\AppView $this
 * @var string $fullName
 * @var string $username
 * @var string $email
 * @var string $verificationUrl
 */
?>
<?= __('Hello {0},', $fullName) ?>


<?= __('An account has been created for you on the TMM system. Confirm this address to activate it.') ?>


<?= __('Username') ?>: <?= $username ?>

<?= __('Email') ?>: <?= $email ?>


<?= __('Open this address to activate your account:') ?>

<?= $verificationUrl ?>


<?= __('This link is good for 24 hours. If it expires, ask an administrator to send another.') ?>


<?= __('If you were not expecting this, you can ignore it - nothing happens until the link is opened.') ?>


<?= __('TMM System') ?>
