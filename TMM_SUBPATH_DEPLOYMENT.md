# TMM Subpath Configuration Complete

## Configuration Summary

The TMM application has been successfully reconfigured to run at:
**http://103.214.112.58/tmm/**

---

## Changes Made

### 1. Nginx Configuration (`/etc/nginx/sites-available/tmm`)

- **Root path**: Changed from `/var/www/tmm/webroot` to `/var/www`
- **Location block**: Added `/tmm` alias pointing to `/var/www/tmm/webroot`
- **URL rewriting**: Configured to handle CakePHP routing at `/tmm` subpath
- **Static assets**: Configured to serve CSS, JS, images, fonts from `/tmm/*` path
- **PHP-FPM**: Updated SCRIPT_FILENAME and SCRIPT_NAME for subpath execution

### 2. CakePHP Configuration (`config/app.php`)

```php
'App' => [
    'base' => '/tmm',
    'fullBaseUrl' => 'http://103.214.112.58/tmm',
    // ... other settings
]
```

### 3. Additional Files Uploaded

- **LpkDataFilterTrait.php**: Missing trait file required by CandidatesController
- **CandidatesController.php**: Updated controller with wizard functionality (58KB)
- **wizard.ctp**: Wizard template for candidate registration (68KB)

---

## Testing Results

✅ **Homepage**: http://103.214.112.58/tmm/ - HTTP 200
✅ **Wizard Page**: http://103.214.112.58/tmm/candidates/wizard - HTTP 200
✅ **Static Assets**: http://103.214.112.58/tmm/css/style.css - HTTP 200
✅ **Cache Cleared**: Models, persistent, views cache cleared

---

## Application URLs

### Main URLs
- **Homepage**: http://103.214.112.58/tmm/
- **Candidate Wizard**: http://103.214.112.58/tmm/candidates/wizard
- **Users**: http://103.214.112.58/tmm/users
- **Login**: http://103.214.112.58/tmm/users/login

### Admin URLs
- **Dashboard**: http://103.214.112.58/tmm/
- **Candidates**: http://103.214.112.58/tmm/candidates
- **Trainees**: http://103.214.112.58/tmm/trainees
- **Organizations**: http://103.214.112.58/tmm/acceptance-organizations
- **Reports**: http://103.214.112.58/tmm/reports

---

## Verification Commands

### Check Nginx Status
```bash
ssh root@103.214.112.58 "systemctl status nginx"
```

### Test Application
```bash
curl -I http://103.214.112.58/tmm/
```

### View Error Logs
```bash
ssh root@103.214.112.58 "tail -f /var/www/tmm/logs/error.log"
ssh root@103.214.112.58 "tail -f /var/log/nginx/tmm-error.log"
```

### Clear Cache
```bash
ssh root@103.214.112.58 "cd /var/www/tmm && rm -rf tmp/cache/models/* tmp/cache/persistent/* tmp/cache/views/*"
```

---

## Configuration Files

### Local Files Updated
1. `d:\xampp\htdocs\tmm\nginx_tmm.conf` - Nginx configuration for /tmm subpath
2. `d:\xampp\htdocs\tmm\config\app.php` - CakePHP base path and full URL

### Production Files
1. `/etc/nginx/sites-available/tmm` - Nginx site configuration
2. `/var/www/tmm/config/app.php` - CakePHP configuration
3. `/var/www/tmm/src/Controller/CandidatesController.php` - Controller with wizard
4. `/var/www/tmm/src/Controller/LpkDataFilterTrait.php` - Required trait
5. `/var/www/tmm/src/Template/Candidates/wizard.ctp` - Wizard template

---

## Important Notes

### URL Path Structure
All URLs now require the `/tmm` prefix:
- ❌ OLD: `http://103.214.112.58/candidates/wizard`
- ✅ NEW: `http://103.214.112.58/tmm/candidates/wizard`

### Static Assets
All static assets (CSS, JS, images) are served through Nginx with:
- 30-day cache expiration
- Gzip compression enabled
- Direct file serving (no PHP processing)

### Security
- Sensitive directories blocked: `/tmm/config`, `/tmm/src`, `/tmm/vendor`, `/tmm/tmp`, `/tmm/logs`
- Hidden files denied (`.htaccess`, `.git`, etc.)
- Security headers enabled (X-Frame-Options, X-Content-Type-Options, X-XSS-Protection)

### Cache Management
After making configuration changes, always clear CakePHP cache:
```bash
cd /var/www/tmm
rm -rf tmp/cache/models/* tmp/cache/persistent/* tmp/cache/views/*
chmod -R 775 tmp/cache
chown -R www-data:www-data tmp/cache
```

---

## Troubleshooting

### Issue: 404 Not Found
**Problem**: Pages return 404 errors
**Solution**: 
1. Check Nginx configuration is loaded: `nginx -t`
2. Verify CakePHP base path in config/app.php
3. Clear CakePHP cache

### Issue: 500 Internal Server Error
**Problem**: Pages return 500 errors
**Solution**:
1. Check error logs: `/var/www/tmm/logs/error.log`
2. Verify all trait files are uploaded
3. Check file permissions (www-data:www-data)
4. Clear cache

### Issue: Static files not loading
**Problem**: CSS/JS/images show 404
**Solution**:
1. Verify files exist in `/var/www/tmm/webroot/`
2. Check Nginx configuration for static asset rules
3. Test direct access: `http://103.214.112.58/tmm/css/style.css`

### Issue: Wizard page errors
**Problem**: Wizard functionality not working
**Solution**:
1. Verify CandidatesController.php is uploaded (58KB)
2. Verify wizard.ctp exists (68KB)
3. Verify LpkDataFilterTrait.php exists
4. Clear cache

---

## Deployment Date
**Completed**: November 24, 2025

## Configuration Status
✅ Nginx configured for /tmm subpath
✅ CakePHP base path updated
✅ Static assets working
✅ Wizard functionality restored
✅ Cache cleared
✅ Permissions set correctly

---

## Quick Reference

### Test Application
```powershell
powershell -ExecutionPolicy Bypass -File "d:\xampp\htdocs\tmm\test_tmm_subpath.ps1"
```

### Reload Nginx
```bash
ssh root@103.214.112.58 "systemctl reload nginx"
```

### Upload Configuration
```powershell
scp "d:\xampp\htdocs\tmm\nginx_tmm.conf" root@103.214.112.58:/tmp/
ssh root@103.214.112.58 "mv /tmp/nginx_tmm.conf /etc/nginx/sites-available/tmm && nginx -t && systemctl reload nginx"
```

### Upload CakePHP Config
```powershell
scp "d:\xampp\htdocs\tmm\config\app.php" root@103.214.112.58:/var/www/tmm/config/
ssh root@103.214.112.58 "cd /var/www/tmm && rm -rf tmp/cache/*/* && chown -R www-data:www-data config/app.php"
```

---

**Application is now accessible at: http://103.214.112.58/tmm/**
