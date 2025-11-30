# XAMPP SMTP Configuration Guide for Gmail

## Option 1: Configure php.ini (Simplest)

### Step 1: Edit php.ini

1. **Open:** `C:\xampp\php\php.ini`
2. **Find** the `[mail function]` section (around line 1000)
3. **Replace** with:

```ini
[mail function]
; For Win32 only.
SMTP = smtp.gmail.com
smtp_port = 587

; For Win32 only.
sendmail_from = sriechoentjoro@gmail.com

; For Unix only.
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
```

### Step 2: Configure sendmail

1. **Open:** `C:\xampp\sendmail\sendmail.ini`
2. **Replace** entire content with:

```ini
[sendmail]

smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=sriechoentjoro@gmail.com
auth_password=unqqevrzplpwysnk
force_sender=sriechoentjoro@gmail.com
```

### Step 3: Restart Apache

1. Open XAMPP Control Panel
2. Stop Apache
3. Start Apache

### Step 4: Test

```bash
cd d:\xampp\htdocs\tmm
php test_email_simple.php
```

---

## Option 2: Use Fake Sendmail (Alternative)

If Gmail SMTP doesn't work, use a fake sendmail that logs emails to files:

### Step 1: Download fake sendmail

1. Download: https://github.com/sendmail-tester/sendmail-tester/releases
2. Extract to: `C:\xampp\sendmail\`

### Step 2: Configure

Edit `C:\xampp\sendmail\sendmail.ini`:

```ini
[sendmail]
smtp_server=localhost
smtp_port=25
error_logfile=error.log
debug_logfile=debug.log
```

### Step 3: View Emails

Emails will be saved to: `C:\xampp\sendmail\emails\`

---

## Option 3: Use MailHog (Recommended for Development)

MailHog catches all emails and displays them in a web interface.

### Step 1: Download MailHog

1. Download: https://github.com/mailhog/MailHog/releases
2. Download `MailHog_windows_amd64.exe`
3. Rename to `mailhog.exe`
4. Move to: `C:\xampp\mailhog\`

### Step 2: Run MailHog

```bash
cd C:\xampp\mailhog
mailhog.exe
```

Keep this window open. MailHog runs on http://localhost:8025

### Step 3: Configure php.ini

```ini
[mail function]
SMTP = localhost
smtp_port = 1025
sendmail_from = sriechoentjoro@gmail.com
sendmail_path = "C:\xampp\sendmail\sendmail.exe -t"
```

### Step 4: Configure sendmail.ini

```ini
[sendmail]
smtp_server=localhost
smtp_port=1025
```

### Step 5: View Emails

Open browser: http://localhost:8025

All emails will appear here!

---

## Troubleshooting

### Error: "SMTP connect() failed"

**Solution 1:** Disable Windows Firewall temporarily
```bash
# Run as Administrator
netsh advfirewall set allprofiles state off
```

**Solution 2:** Allow PHP through firewall
1. Windows Defender Firewall
2. Allow an app
3. Browse to `C:\xampp\php\php.exe`
4. Allow both Private and Public

### Error: "Could not authenticate"

- Double-check app password (no spaces)
- Ensure 2-Step Verification is enabled on Google account
- Try generating a new app password

### Error: "Connection timeout"

- Check antivirus isn't blocking
- Try port 465 instead of 587
- Use MailHog instead (localhost, no authentication needed)

---

## Quick Test Script

Save as `test_mail.php` in `d:\xampp\htdocs\tmm\`:

```php
<?php
$to = 'sriechoentjoro@gmail.com';
$subject = 'Test Email from XAMPP';
$message = 'If you receive this, SMTP is working!';
$headers = 'From: sriechoentjoro@gmail.com';

if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Email failed to send.";
}
?>
```

Run: `php test_mail.php`

---

## Recommended Approach

For development on XAMPP, I recommend **MailHog** because:
- ✅ No authentication needed
- ✅ Web interface to view emails
- ✅ Catches all emails (nothing sent to real addresses)
- ✅ Easy to test email templates
- ✅ No firewall issues

For production, use real Gmail SMTP or your hosting provider's SMTP.
