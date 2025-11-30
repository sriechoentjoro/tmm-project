# Quick Reference: Database Export & Import

## 🚀 Quick Commands

### Export Databases (Local)
```powershell
powershell -ExecutionPolicy Bypass -File export_databases_with_drop.ps1
```
**Creates:** Timestamped folder with SQL files containing DROP IF EXISTS

### Upload & Import to Production
```powershell
powershell -ExecutionPolicy Bypass -File upload_and_import_databases.ps1
```
**Does:** Upload → Import → Verify → Cleanup

### Manual Import (Production)
```bash
ssh root@103.214.112.58
mysql -u root -p < /tmp/cms_masters.sql
```

## 📦 What Was Deployed

### Project Files
✅ `/var/www/tmm/` - Complete CakePHP application (3,914 files, 182.51 MB)

### Databases (12 total, 5.38 MB)
✅ **cms_masters** - Countries, provinces, job categories, organizations
✅ **cms_lpk_candidates** - LPK candidate records
✅ **cms_lpk_candidate_documents** - Candidate documents
✅ **cms_tmm_apprentices** - Apprentice records
✅ **cms_tmm_apprentice_documents** - Apprentice documents
✅ **cms_tmm_apprentice_document_ticketings** - Ticketing system
✅ **cms_tmm_stakeholders** - Stakeholder organizations
✅ **cms_tmm_trainees** - Trainee records
✅ **cms_tmm_trainee_accountings** - Accounting data
✅ **cms_tmm_trainee_trainings** - Training records
✅ **cms_tmm_trainee_training_scorings** - Training scores
✅ **system_authentication_authorization** - Users & permissions

## ⚙️ Configuration Required

### 1. Update Database Credentials
```bash
ssh root@103.214.112.58
nano /var/www/tmm/config/app.php
```

Change password for each datasource:
```php
'password' => 'YOUR_PRODUCTION_PASSWORD'
```

### 2. Web Server Setup
**Document Root:** `/var/www/tmm/webroot`

**Nginx Example:**
```nginx
server {
    listen 80;
    server_name 103.214.112.58;
    root /var/www/tmm/webroot;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        include fastcgi_params;
    }
}
```

### 3. Test the Application
```bash
curl http://103.214.112.58
```

## 🔧 Maintenance Commands

### Clear Cache
```bash
cd /var/www/tmm && rm -rf tmp/cache/*/*
```

### View Logs
```bash
tail -f /var/www/tmm/logs/error.log
```

### Backup Database
```bash
mysqldump -u root -p cms_masters > backup_$(date +%Y%m%d).sql
```

### Re-export & Re-import
```powershell
# Local
powershell -ExecutionPolicy Bypass -File export_databases_with_drop.ps1
powershell -ExecutionPolicy Bypass -File upload_and_import_databases.ps1
```

## 📝 Important Notes

1. **DROP IF EXISTS** - All SQL files include DROP DATABASE IF EXISTS, safe to re-run
2. **UTF-8 Encoding** - All exports use utf8mb4 charset
3. **Passwords** - Update production database passwords in app.php
4. **PHP Version** - Server runs PHP 7.4, project was developed for PHP 5.6 (test thoroughly)
5. **File Uploads** - Ensure webroot/img/uploads and webroot/files/uploads are writable

## 🆘 Troubleshooting

| Issue | Solution |
|-------|----------|
| Database connection error | Check credentials in config/app.php |
| 404 errors | Verify web server document root |
| Permission denied | Run: `chown -R www-data:www-data /var/www/tmm` |
| Cache issues | Clear: `rm -rf tmp/cache/*/*` |
| Import fails | Check MySQL user has CREATE DATABASE privilege |

## 📂 File Locations

**Local:**
- Scripts: `d:\xampp\htdocs\tmm\*.ps1`
- Exports: `d:\xampp\htdocs\tmm\database_exports\`

**Production:**
- App: `/var/www/tmm/`
- Config: `/var/www/tmm/config/app.php`
- Logs: `/var/www/tmm/logs/error.log`

## ✅ Deployment Status

- [x] Project uploaded (3,914 files)
- [x] 12 databases exported with DROP IF EXISTS
- [x] All databases imported successfully
- [x] Permissions configured
- [x] Composer dependencies installed
- [x] Cache cleared
- [ ] Database credentials updated (ACTION REQUIRED)
- [ ] Web server configured (ACTION REQUIRED)
- [ ] Application tested (ACTION REQUIRED)

---
**Last Export:** 20251124_035402  
**Server:** root@103.214.112.58  
**Status:** Ready for configuration
