# TMM Quick Deployment Reference Card

## 🚀 Daily Deployment (One Command)

```powershell
# 1. Add all changes, commit, and push
git add -A; git commit -m "Your message"; git push origin main

# 2. Deploy to production
ssh root@103.214.112.58 "cd /var/www/tmm && git pull origin main && rm -rf tmp/cache/* && systemctl restart php7.4-fpm"
```

## 📋 Common Commands

### Check Status
```powershell
# Local status
git status

# Production status
ssh root@103.214.112.58 "cd /var/www/tmm && git log -1 --oneline && git status"
```

### View Logs
```powershell
# Production error log
ssh root@103.214.112.58 "tail -50 /var/www/tmm/logs/error.log"

# Nginx error log
ssh root@103.214.112.58 "tail -50 /var/log/nginx/error.log"
```

### Test Application
```powershell
# HTTP test
Invoke-WebRequest -Uri "http://103.214.112.58/tmm/" -UseBasicParsing

# Browser
Start-Process "http://103.214.112.58/tmm/"
```

### Clear Cache (Production)
```powershell
ssh root@103.214.112.58 "cd /var/www/tmm && rm -rf tmp/cache/* && systemctl restart php7.4-fpm"
```

### Restart Services (Production)
```powershell
ssh root@103.214.112.58 "systemctl restart php7.4-fpm && systemctl reload nginx"
```

## 🔧 Syntax Check Before Deploy

```powershell
# Check critical files
php -l config/app.php
php -l config/app_datasources.php
php -l src/Controller/YourController.php
php -l src/Template/YourTemplate/file.ctp
```

## 📁 File Management

### Upload Files (NOT via Git)
```powershell
# Upload images
scp -r webroot/img/uploads/* root@103.214.112.58:/var/www/tmm/webroot/img/uploads/

# Set permissions
ssh root@103.214.112.58 "chown -R www-data:www-data /var/www/tmm/webroot/img/uploads/ && chmod -R 775 /var/www/tmm/webroot/img/uploads/"
```

### Database Updates
```powershell
# Export local
.\export_all_databases.ps1

# Upload and import
scp backups/cms_*.sql root@103.214.112.58:/tmp/
ssh root@103.214.112.58 "mysql -u root -p < /tmp/cms_masters.sql"
```

## 🆘 Emergency Rollback

```powershell
# Find commit to rollback to
git log --oneline -10

# Rollback on production
ssh root@103.214.112.58 "cd /var/www/tmm && git reset --hard <commit-hash> && rm -rf tmp/cache/* && systemctl restart php7.4-fpm"
```

## 📊 URLs

- **Production:** http://103.214.112.58/tmm/
- **GitHub:** https://github.com/sriechoentjoro/tmm-project
- **Login:** http://103.214.112.58/tmm/users/login

## 📖 Full Documentation

- `GIT_DEPLOYMENT_GUIDE.md` - Complete deployment guide
- `GIT_DEPLOYMENT_SUCCESS.md` - Setup status and history
- `ADMIN_STAKEHOLDER_MANAGEMENT_IMPLEMENTATION.md` - Admin features spec

---

**Quick Help:** `cat GIT_DEPLOYMENT_QUICK_REF.md`
