<h2>Welcome to TMM System, <?= h($institutionName) ?>!</h2>

<p>Dear <strong><?= h($userName) ?></strong>,</p>

<p>
    Your Vocational Training Institution (LPK) account has been created in the TMM Apprentice Management System. 
    Before you can start managing your candidates and training programs, you need to verify your email address.
</p>

<div class="info-box">
    <strong>Institution Details:</strong>
    <p style="margin: 5px 0;">
        <strong>Name:</strong> <?= h($institutionName) ?><br>
        <strong>Email:</strong> <?= h($email) ?><br>
        <strong>Registration Number:</strong> <?= h($registrationNumber) ?>
    </p>
</div>

<div class="info-box">
    <strong>Your Temporary Login Credentials:</strong>
    <p style="margin: 5px 0;">
        <strong>Email:</strong> <?= h($email) ?><br>
        <strong>Temporary Password:</strong> <span style="font-family: monospace; background: #fff3cd; padding: 5px 10px; border-radius: 4px;"><?= h($temporaryPassword) ?></span>
    </p>
</div>

<div class="warning-box">
    <strong>⚠️ IMPORTANT:</strong> This temporary password will expire in 24 hours. You must verify your email and change your password before the expiration.
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="<?= $verificationLink ?>" class="btn">Verify Your Email Address</a>
</p>

<p style="font-size: 13px; color: #777; margin-top: 20px;">
    If the button above doesn't work, copy and paste this link into your browser:<br>
    <span style="word-break: break-all; color: #667eea;"><?= $verificationLink ?></span>
</p>

<div class="divider"></div>

<h3 style="color: #667eea; font-size: 18px;">What Happens Next?</h3>

<ol style="padding-left: 20px; color: #555;">
    <li><strong>Click the verification link</strong> above to confirm your email address</li>
    <li><strong>Log in</strong> using your email and temporary password</li>
    <li><strong>Change your password</strong> to a secure password of your choice</li>
    <li><strong>Complete your profile</strong> and start managing candidates</li>
</ol>

<div class="success-box">
    <strong>✓ Benefits of Your LPK Account:</strong>
    <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Manage candidate registrations and profiles</li>
        <li>Track training progress and assessments</li>
        <li>Export candidate data and reports</li>
        <li>Access to monitoring dashboard</li>
        <li>Direct communication with administrators</li>
    </ul>
</div>

<p>
    If you did not request this account or have any questions, please contact the system administrator immediately.
</p>

<p style="margin-top: 30px;">
    Best regards,<br>
    <strong>TMM System Administrator</strong>
</p>
