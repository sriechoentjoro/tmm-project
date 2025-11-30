<h2>Email Verified Successfully!</h2>

<p>Dear <strong><?= h($userName) ?></strong>,</p>

<div class="success-box">
    <strong>✓ Congratulations!</strong> Your email address has been successfully verified.
</div>

<p>
    Your <?= h($institutionType) ?> account is now active in the TMM Apprentice Management System. 
    You can now log in and start using all the features available to your institution.
</p>

<div class="info-box">
    <strong>Your Account Information:</strong>
    <p style="margin: 5px 0;">
        <strong>Institution:</strong> <?= h($institutionName) ?><br>
        <strong>Email:</strong> <?= h($email) ?><br>
        <strong>Account Type:</strong> <?= h($institutionType) ?><br>
        <strong>Status:</strong> <span style="color: #28a745; font-weight: 600;">Verified ✓</span>
    </p>
</div>

<div class="warning-box">
    <strong>⚠️ IMPORTANT NEXT STEP:</strong> Please log in and change your temporary password to a secure password of your choice.
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="<?= $profileLink ?>" class="btn">Go to Your Dashboard</a>
</p>

<p style="font-size: 13px; color: #777; margin-top: 20px;">
    If the button above doesn't work, copy and paste this link into your browser:<br>
    <span style="word-break: break-all; color: #667eea;"><?= $profileLink ?></span>
</p>

<div class="divider"></div>

<h3 style="color: #667eea; font-size: 18px;">Next Steps:</h3>

<ol style="padding-left: 20px; color: #555;">
    <li><strong>Log in to your account</strong> using your email and temporary password</li>
    <li><strong>Change your password</strong> immediately for security</li>
    <li><strong>Complete your profile information</strong> to unlock all features</li>
    <li><strong>Explore the dashboard</strong> and familiarize yourself with the system</li>
    <li><strong>Start managing</strong> your <?= $institutionType === 'LPK' ? 'candidates' : 'trainees' ?></li>
</ol>

<?php if ($institutionType === 'LPK'): ?>
<div class="success-box">
    <strong>✓ LPK Account Features:</strong>
    <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Add and manage candidate profiles</li>
        <li>Track training progress and assessments</li>
        <li>Generate candidate reports and certificates</li>
        <li>Export data for external use</li>
        <li>Monitor dashboard with real-time statistics</li>
        <li>Receive notifications and updates</li>
    </ul>
</div>
<?php else: ?>
<div class="success-box">
    <strong>✓ Special Skill Account Features:</strong>
    <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Add and manage trainee profiles</li>
        <li>Track skill development progress</li>
        <li>Generate skill assessment reports</li>
        <li>Export trainee data for analysis</li>
        <li>Access skill monitoring dashboard</li>
        <li>Receive system notifications</li>
    </ul>
</div>
<?php endif; ?>

<p>
    If you need help getting started, please refer to the help documentation in the system 
    or contact the administrator for assistance.
</p>

<p style="margin-top: 30px;">
    Welcome aboard!<br>
    <strong>TMM System Administrator</strong>
</p>
