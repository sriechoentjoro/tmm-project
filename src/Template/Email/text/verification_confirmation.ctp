EMAIL VERIFIED SUCCESSFULLY!

Dear <?= h($userName) ?>,

✓ Congratulations! Your email address has been successfully verified.

Your <?= h($institutionType) ?> account is now active in the TMM Apprentice 
Management System. You can now log in and start using all the features 
available to your institution.

--------------------------------------------------------------------------------
YOUR ACCOUNT INFORMATION
--------------------------------------------------------------------------------

Institution: <?= h($institutionName) ?>

Email: <?= h($email) ?>

Account Type: <?= h($institutionType) ?>

Status: Verified ✓


⚠️ IMPORTANT NEXT STEP: Please log in and change your temporary password 
to a secure password of your choice.

--------------------------------------------------------------------------------
DASHBOARD LINK
--------------------------------------------------------------------------------

Click or copy this link to access your dashboard:

<?= $profileLink ?>


--------------------------------------------------------------------------------
NEXT STEPS
--------------------------------------------------------------------------------

1. Log in to your account using your email and temporary password
2. Change your password immediately for security
3. Complete your profile information to unlock all features
4. Explore the dashboard and familiarize yourself with the system
5. Start managing your <?= $institutionType === 'LPK' ? 'candidates' : 'trainees' ?>


--------------------------------------------------------------------------------
<?= strtoupper($institutionType) ?> ACCOUNT FEATURES
--------------------------------------------------------------------------------

<?php if ($institutionType === 'LPK'): ?>
✓ Add and manage candidate profiles
✓ Track training progress and assessments
✓ Generate candidate reports and certificates
✓ Export data for external use
✓ Monitor dashboard with real-time statistics
✓ Receive notifications and updates
<?php else: ?>
✓ Add and manage trainee profiles
✓ Track skill development progress
✓ Generate skill assessment reports
✓ Export trainee data for analysis
✓ Access skill monitoring dashboard
✓ Receive system notifications
<?php endif; ?>


If you need help getting started, please refer to the help documentation 
in the system or contact the administrator for assistance.

Welcome aboard!
TMM System Administrator
