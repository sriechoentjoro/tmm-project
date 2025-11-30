<h2>New LPK Registration - Action Required</h2>

<p>Dear <strong><?= h($adminName) ?></strong>,</p>

<p>
    A new Vocational Training Institution (LPK) has been registered in the TMM Apprentice Management System 
    and requires verification.
</p>

<div class="info-box">
    <strong>Institution Details:</strong>
    <p style="margin: 5px 0;">
        <strong>Name:</strong> <?= h($institutionName) ?><br>
        <strong>Email:</strong> <?= h($email) ?><br>
        <strong>Registration Number:</strong> <?= h($registrationNumber) ?><br>
        <strong>Status:</strong> <span style="color: #ffc107; font-weight: 600;"><?= h($status) ?></span>
    </p>
</div>

<div class="warning-box">
    <strong>⚠️ ACTION REQUIRED:</strong> The institution owner has been sent a verification email. 
    Please review the institution details and monitor the verification process.
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="<?= $editLink ?>" class="btn">Review Institution Details</a>
</p>

<p style="font-size: 13px; color: #777; margin-top: 20px;">
    If the button above doesn't work, copy and paste this link into your browser:<br>
    <span style="word-break: break-all; color: #667eea;"><?= $editLink ?></span>
</p>

<div class="divider"></div>

<h3 style="color: #667eea; font-size: 18px;">What You Should Do:</h3>

<ol style="padding-left: 20px; color: #555;">
    <li><strong>Review institution details</strong> for accuracy and completeness</li>
    <li><strong>Monitor verification status</strong> - user has 24 hours to verify</li>
    <li><strong>Check for suspicious activity</strong> or invalid information</li>
    <li><strong>Complete additional fields</strong> if needed after verification</li>
    <li><strong>Approve or reject</strong> the registration based on your review</li>
</ol>

<div class="success-box">
    <strong>✓ Verification Process:</strong>
    <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Institution owner receives verification email with temporary password</li>
        <li>Owner clicks verification link (expires in 24 hours)</li>
        <li>Owner logs in and changes password</li>
        <li>Admin reviews and completes registration (optional)</li>
        <li>Admin approves or rejects the registration</li>
    </ul>
</div>

<p style="margin-top: 30px;">
    This is an automated notification from the TMM Apprentice Management System.<br>
    <strong>TMM System</strong>
</p>
