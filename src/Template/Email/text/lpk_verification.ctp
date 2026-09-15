<?php
/**
 * Plain-text half of the LPK verification email.
 *
 * Every line of this used to be an English literal, so the text part arrived in
 * English however the interface was set - while the HTML half beside it was
 * fully translated. Mail clients that prefer text/plain, and anyone reading on
 * a client that strips HTML, got a language nobody asked for.
 *
 * The label column is gone on purpose. It was padded to a fixed width, which
 * only lines up while every label is English: Indonesian labels are longer and
 * Japanese ones are shorter in characters but wider on screen, and str_pad
 * counts bytes, so a multibyte label misaligns the whole block. "Label: value"
 * on one line cannot misalign in any language.
 *
 * @var \App\View\AppView $this
 * @var string $directorName
 * @var string $institutionName
 * @var string $registrationNumber
 * @var string $email
 * @var string $registeredByAdmin
 * @var string $registrationDate
 * @var string $verificationUrl
 * @var string|null $username Absent from older senders of this template.
 */
?>
================================================================================
<?= __('TMM SYSTEM REGISTRATION - VERIFY YOUR EMAIL ADDRESS') ?>

================================================================================

<?= __('Dear {0},', h($directorName)) ?>


<?= __('Congratulations! Your Vocational Training Institution "{0}" has been successfully registered in the TMM (Training and Manpower Management) System.', h($institutionName)) ?>


<?= __('To complete your registration and activate your account, please verify your email address by visiting the link below:') ?>


<?= $verificationUrl ?>


--------------------------------------------------------------------------------
<?= __('REGISTRATION DETAILS') ?>

--------------------------------------------------------------------------------

<?= __('Institution Name:') ?> <?= h($institutionName) ?>

<?= __('Registration Number:') ?> <?= h($registrationNumber) ?>

<?= __('Email Address:') ?> <?= h($email) ?>

<?php if (!empty($username)): ?>
<?= __('Login Username:') ?> <?= h($username) ?>

<?php endif; ?>
<?= __('Director Name:') ?> <?= h($directorName) ?>

<?= __('Registered By:') ?> <?= h($registeredByAdmin) ?>

<?= __('Registration Date:') ?> <?= h($registrationDate) ?>


--------------------------------------------------------------------------------
<?= __('IMPORTANT') ?>

--------------------------------------------------------------------------------

<?= __('This verification link will expire in 24 HOURS. Please verify your email as soon as possible to avoid delays in account activation.') ?>


--------------------------------------------------------------------------------
<?= __('WHAT HAPPENS NEXT?') ?>

--------------------------------------------------------------------------------

<?= __('After verifying your email, you will be directed to create a secure password for your account. Once completed, you will receive a welcome email with your login credentials and instructions on how to access the TMM system.') ?>


--------------------------------------------------------------------------------
<?= __('SECURITY NOTICE') ?>

--------------------------------------------------------------------------------

<?= __('If you did not request this registration or believe this email was sent to you by mistake, please contact our support team immediately at:') ?>


<?= __('Email:') ?> support@asahifamily.id
<?= __('Phone:') ?> +62 21 8984 4450


================================================================================
<?= __('TMM - Training and Manpower Management System') ?>

PT. ASAHI FAMILY INDONESIA

Jl. Industri Raya III Blok AF No. 1, Kawasan Industri Jababeka
Cikarang, Bekasi 17530, Indonesia

<?= __('Email:') ?> support@asahifamily.id
<?= __('Phone:') ?> +62 21 8984 4450

<?= __('© {0} PT. ASAHI FAMILY INDONESIA. All rights reserved.', date('Y')) ?>


<?= __('This is an automated email. Please do not reply directly to this message.') ?>

================================================================================
