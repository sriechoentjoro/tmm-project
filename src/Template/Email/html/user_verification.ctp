<?php
/**
 * The link that activates a login account an administrator created.
 *
 * Adding a user on /users/add sets no status, so the row takes the column's
 * default and lands on pending_verification. Nothing moved it off until this:
 * the account holder confirms the address, and the account goes active.
 *
 * @var \App\View\AppView $this
 * @var string $fullName
 * @var string $username
 * @var string $email
 * @var string $verificationUrl
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('Verify your TMM account') ?></title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background-color:#f4f4f4;">
    <div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
        <div style="background:linear-gradient(135deg,#0d6efd 0%,#0dcaf0 100%);padding:40px 20px;text-align:center;">
            <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:600;">
                <?= __('Verify your TMM account') ?>
            </h1>
        </div>

        <div style="padding:32px 28px;color:#333333;line-height:1.6;">
            <p style="margin-top:0;"><?= __('Hello {0},', h($fullName)) ?></p>

            <p>
                <?= __('An account has been created for you on the TMM system. Confirm this address to activate it.') ?>
            </p>

            <div style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:16px;margin:24px 0;">
                <div style="margin-bottom:8px;">
                    <strong><?= __('Username') ?>:</strong> <?= h($username) ?>
                </div>
                <div>
                    <strong><?= __('Email') ?>:</strong> <?= h($email) ?>
                </div>
            </div>

            <div style="text-align:center;margin:32px 0;">
                <a href="<?= h($verificationUrl) ?>"
                   style="display:inline-block;background:#0d6efd;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:6px;font-weight:600;">
                    <?= __('Activate my account') ?>
                </a>
            </div>

            <p style="font-size:13px;color:#6c757d;">
                <?= __('If the button does not work, copy this address into your browser:') ?><br>
                <span style="word-break:break-all;"><?= h($verificationUrl) ?></span>
            </p>

            <p style="font-size:13px;color:#6c757d;">
                <?= __('This link is good for 24 hours. If it expires, ask an administrator to send another.') ?>
            </p>

            <p style="font-size:13px;color:#6c757d;">
                <?= __('If you were not expecting this, you can ignore it - nothing happens until the link is opened.') ?>
            </p>
        </div>

        <div style="background:#f8f9fa;padding:20px;text-align:center;font-size:12px;color:#6c757d;">
            <?= __('TMM System') ?>
        </div>
    </div>
</body>
</html>
