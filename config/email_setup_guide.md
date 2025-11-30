# Email Configuration Guide for TMM System

## Gmail SMTP Setup

### Step 1: Enable 2-Step Verification
1. Go to https://myaccount.google.com/security
2. Enable "2-Step Verification" if not already enabled

### Step 2: Create App Password
1. Go to https://myaccount.google.com/apppasswords
2. Select "Mail" and "Other (Custom name)"
3. Enter "TMM System" as the name
4. Click "Generate"
5. Copy the 16-character password (remove spaces)

### Step 3: Update config/app.php

Add this to your `config/app.php` file in the `EmailTransport` section:

```php
'EmailTransport' => [
    'default' => [
        'className' => 'Smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'timeout' => 30,
        'username' => 'sriechoentjoro@gmail.com',
        'password' => 'YOUR_APP_PASSWORD_HERE', // 16-character app password
        'tls' => true,
    ],
],

'Email' => [
    'default' => [
        'transport' => 'default',
        'from' => ['sriechoentjoro@gmail.com' => 'TMM System'],
    ],
],
```

### Step 4: Test Email Sending

Run the test script:
```bash
cd d:\xampp\htdocs\tmm
php bin\cake.php test_email
```

Or test directly in browser by creating a test controller action.

## Alternative: Using PHP mail()

If Gmail doesn't work, you can use PHP's built-in mail():

```php
'EmailTransport' => [
    'default' => [
        'className' => 'Mail',
    ],
],
```

Note: This requires your server to have sendmail or similar configured.

## Troubleshooting

### "Could not send email" Error
- Check app password is correct (no spaces)
- Verify 2-step verification is enabled
- Check firewall allows port 587
- Try port 465 with SSL instead of TLS

### "Connection timeout" Error
- XAMPP might block outgoing SMTP
- Try using a different SMTP server
- Check antivirus/firewall settings

### Test Email Not Received
- Check spam folder
- Verify email address is correct
- Check email logs in database: `SELECT * FROM system_authentication_authorization.email_logs;`
