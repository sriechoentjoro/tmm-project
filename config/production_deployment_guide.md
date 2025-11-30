# Production Deployment Guide - Institution Registration System

## Pre-Deployment Checklist

### 1. Code Review
- [ ] All controllers updated
- [ ] Email configuration set
- [ ] Database schema ready
- [ ] Entity models complete
- [ ] Templates functional

### 2. Security Review
- [ ] Passwords hashed (DefaultPasswordHasher)
- [ ] Tokens secure (64-char random)
- [ ] SQL injection prevented (ORM used)
- [ ] XSS prevented (h() helper used)
- [ ] CSRF protection enabled

### 3. Testing Complete
- [ ] Registration wizard tested
- [ ] Email sending tested
- [ ] User creation tested
- [ ] Login tested
- [ ] Role assignment tested

## Deployment Steps

### Step 1: Backup Current System

```bash
# Backup database
mysqldump -u root -p cms_tmm_stakeholders > backup_stakeholders_$(date +%Y%m%d).sql
mysqldump -u root -p system_authentication_authorization > backup_auth_$(date +%Y%m%d).sql

# Backup files
tar -czf backup_tmm_$(date +%Y%m%d).tar.gz /path/to/tmm/
```

### Step 2: Upload Files

Upload these files to production server:

```
src/Model/Entity/VocationalTrainingInstitution.php
src/Model/Entity/SpecialSkillSupportInstitution.php
src/Model/Entity/EmailTemplate.php
src/Model/Table/EmailTemplatesTable.php
src/Model/Table/EmailLogsTable.php
src/Model/Table/VocationalTrainingInstitutionsTable.php
src/Model/Table/SpecialSkillSupportInstitutionsTable.php
src/Controller/Component/EmailComponent.php
src/Controller/InstitutionRegistrationController.php
src/Controller/VocationalTrainingInstitutionsController.php
src/Controller/SpecialSkillSupportInstitutionsController.php
src/Template/InstitutionRegistration/complete.ctp
config/sql/institution_registration_schema.sql
```

### Step 3: Configure Email (Production)

Edit `config/app.php`:

```php
'EmailTransport' => [
    'default' => [
        'className' => 'Smtp',
        'host' => 'smtp.gmail.com', // Or your hosting SMTP
        'port' => 587,
        'timeout' => 30,
        'username' => 'your-production-email@domain.com',
        'password' => 'your-production-app-password',
        'tls' => true,
    ],
],

'Email' => [
    'default' => [
        'transport' => 'default',
        'from' => ['noreply@yourdomain.com' => 'TMM System'],
    ],
],
```

### Step 4: Run Database Migration

```bash
# SSH to production server
ssh user@production-server

# Navigate to project
cd /path/to/tmm

# Run SQL
mysql -u username -p cms_tmm_stakeholders < config/sql/institution_registration_schema.sql
```

Verify:
```sql
SHOW COLUMNS FROM vocational_training_institutions WHERE Field LIKE '%email%';
SELECT * FROM system_authentication_authorization.email_templates;
```

### Step 5: Clear Cache

```bash
# CakePHP cache
rm -rf tmp/cache/models/*
rm -rf tmp/cache/persistent/*
rm -rf tmp/cache/views/*

# Or use CakePHP command
bin/cake cache clear_all
```

### Step 6: Set Permissions

```bash
# Make writable
chmod -R 777 tmp/
chmod -R 777 logs/
chmod -R 755 webroot/

# Set owner (adjust to your server)
chown -R www-data:www-data /path/to/tmm/
```

### Step 7: Test on Production

1. **Test Email:**
```bash
php bin/cake.php test_email
```

2. **Create Test Institution:**
   - Access admin panel
   - Create test institution
   - Check email received

3. **Complete Registration:**
   - Click email link
   - Complete wizard
   - Verify auto-login

4. **Verify Database:**
```sql
SELECT * FROM vocational_training_institutions WHERE is_registered = 1;
SELECT * FROM system_authentication_authorization.users ORDER BY id DESC LIMIT 5;
```

### Step 8: Monitor

```bash
# Watch logs
tail -f logs/error.log

# Watch email logs
mysql -u username -p -e "SELECT * FROM system_authentication_authorization.email_logs ORDER BY created DESC LIMIT 10;"
```

## Production Configuration

### Email Template Customization

Update email template for production:

```sql
UPDATE system_authentication_authorization.email_templates
SET 
    subject = 'Complete Your Institution Registration - TMM System',
    body_html = '...' -- Your production HTML template
WHERE template_key = 'institution_registration';
```

### URL Configuration

Ensure correct base URL in `config/app.php`:

```php
'App' => [
    'fullBaseUrl' => 'https://yourdomain.com',
],
```

### Security Settings

```php
// config/app.php
'Security' => [
    'salt' => 'YOUR_PRODUCTION_SALT', // Change from default
],

'Session' => [
    'defaults' => 'php',
    'cookie' => 'TMMSESSID',
    'timeout' => 1440, // 24 hours
],
```

## Post-Deployment Checklist

- [ ] Email sending works
- [ ] Registration wizard accessible
- [ ] SSL certificate valid
- [ ] Database backups scheduled
- [ ] Error logging enabled
- [ ] Email logs monitored
- [ ] User accounts created successfully
- [ ] Login works
- [ ] Role permissions correct

## Monitoring & Maintenance

### Daily Checks

```sql
-- Check pending registrations
SELECT COUNT(*) as pending
FROM vocational_training_institutions
WHERE is_registered = 0 AND token_expires_at > NOW();

-- Check failed emails
SELECT COUNT(*) as failed
FROM system_authentication_authorization.email_logs
WHERE status = 'failed' AND DATE(created) = CURDATE();
```

### Weekly Tasks

1. Review email logs
2. Clean up expired tokens
3. Monitor user registrations
4. Check error logs

### Cleanup Script

```sql
-- Delete expired tokens (run weekly)
UPDATE vocational_training_institutions
SET registration_token = NULL,
    token_expires_at = NULL
WHERE is_registered = 0 
  AND token_expires_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- Archive old email logs (run monthly)
DELETE FROM system_authentication_authorization.email_logs
WHERE created < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

## Rollback Plan

If issues occur:

### 1. Restore Database

```bash
mysql -u username -p cms_tmm_stakeholders < backup_stakeholders_YYYYMMDD.sql
mysql -u username -p system_authentication_authorization < backup_auth_YYYYMMDD.sql
```

### 2. Restore Files

```bash
tar -xzf backup_tmm_YYYYMMDD.tar.gz -C /path/to/restore/
```

### 3. Clear Cache

```bash
rm -rf tmp/cache/*
```

## Support & Troubleshooting

### Common Issues

**Email not sending:**
- Check SMTP credentials
- Verify firewall allows outgoing SMTP
- Check email logs for errors

**Registration fails:**
- Check database constraints
- Verify role ID 6 exists
- Check error logs

**Token expired:**
- Regenerate token manually
- Resend email via admin

### Contact Information

- System Admin: [email]
- Database Admin: [email]
- Developer: [email]

## Success Metrics

Track these metrics:

- Institutions registered per week
- Email delivery rate
- Registration completion rate
- Average time to complete registration
- Failed email count

```sql
-- Weekly report
SELECT 
    COUNT(*) as total_institutions,
    SUM(is_registered) as registered,
    SUM(CASE WHEN is_registered = 0 THEN 1 ELSE 0 END) as pending,
    ROUND(SUM(is_registered) / COUNT(*) * 100, 2) as completion_rate
FROM vocational_training_institutions
WHERE created >= DATE_SUB(NOW(), INTERVAL 7 DAY);
```

## Documentation

Keep updated:
- API documentation
- User manual for institutions
- Admin manual for managing institutions
- Troubleshooting guide
- Change log

## Next Phase

After successful deployment:
1. Add email template management UI
2. Implement role-based authorization
3. Add registration status to admin index
4. Create admin dashboard for monitoring
5. Add email notification preferences
6. Implement email queue for bulk sending

---

**Deployment Date:** ___________
**Deployed By:** ___________
**Verified By:** ___________
**Status:** ___________
