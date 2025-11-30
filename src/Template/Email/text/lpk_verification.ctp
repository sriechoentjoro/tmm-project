WELCOME TO TMM SYSTEM, <?= strtoupper(h($institutionName)) ?>!

Dear <?= h($userName) ?>,

Your Vocational Training Institution (LPK) account has been created in the 
TMM Apprentice Management System. Before you can start managing your candidates 
and training programs, you need to verify your email address.

--------------------------------------------------------------------------------
INSTITUTION DETAILS
--------------------------------------------------------------------------------

Name: <?= h($institutionName) ?>

Email: <?= h($email) ?>

Registration Number: <?= h($registrationNumber) ?>


--------------------------------------------------------------------------------
YOUR TEMPORARY LOGIN CREDENTIALS
--------------------------------------------------------------------------------

Email: <?= h($email) ?>

Temporary Password: <?= h($temporaryPassword) ?>


⚠️ IMPORTANT: This temporary password will expire in 24 hours. You must verify 
your email and change your password before the expiration.

--------------------------------------------------------------------------------
VERIFICATION LINK
--------------------------------------------------------------------------------

Please click or copy this link to verify your email address:

<?= $verificationLink ?>


--------------------------------------------------------------------------------
WHAT HAPPENS NEXT?
--------------------------------------------------------------------------------

1. Click the verification link above to confirm your email address
2. Log in using your email and temporary password
3. Change your password to a secure password of your choice
4. Complete your profile and start managing candidates

--------------------------------------------------------------------------------
BENEFITS OF YOUR LPK ACCOUNT
--------------------------------------------------------------------------------

✓ Manage candidate registrations and profiles
✓ Track training progress and assessments
✓ Export candidate data and reports
✓ Access to monitoring dashboard
✓ Direct communication with administrators

If you did not request this account or have any questions, please contact 
the system administrator immediately.

Best regards,
TMM System Administrator
