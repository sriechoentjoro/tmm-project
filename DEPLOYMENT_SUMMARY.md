# TMM Project Deployment Summary

## Deployment Details
- **Date:** November 24, 2025
- **Server:** root@103.214.112.58
- **Target Path:** /var/www/tmm
- **Files Transferred:** 3,914 files
- **Archive Size:** 182.51 MB

## Deployment Steps Completed

### ✓ 1. Project Upload
- Created compressed tar archive of project files
- Uploaded via SCP to production server
- Extracted to /var/www/tmm
- Excluded development files (.git, node_modules, *.ps1, *.md, etc.)

### ✓ 2. File Permissions
```bash
chown -R www-data:www-data /var/www/tmm
find . -type d -exec chmod 755 {} +
find . -type f -exec chmod 644 {} +
chmod +x bin/cake
chmod -R 775 tmp logs webroot/img/uploads webroot/files/uploads
```

### ✓ 3. Composer Dependencies
```bash
cd /var/www/tmm
composer install --no-dev --optimize-autoloader
```
- Installed production dependencies only
- Optimized autoloader for performance

### ✓ 4. Cache Cleared
```bash
rm -rf tmp/cache/models/* tmp/cache/persistent/* tmp/cache/views/*
```

## Next Steps Required

### 1. Database Configuration
**CRITICAL:** Update database credentials in production config file.

```bash
ssh root@103.214.112.58
nano /var/www/tmm/config/app.php
```

Update the following sections:
- `Datasources.default` - Main database connection
- `Datasources.cms_masters` - Masters database
- `Datasources.cms_orders` - Orders database
- `Datasources.cms_candidates` - Candidates database
- `Datasources.cms_trainees` - Trainees database
- `Datasources.cms_attendance` - Attendance database
- All other database connections as needed

**Example:**
```php
'default' => [
    'host' => 'localhost',
    'username' => 'your_db_user',
    'password' => 'your_db_password',
    'database' => 'your_database_name',
    // ... other settings
],
```

### 2. Web Server Configuration

#### Nginx Configuration
Create or update nginx site configuration:

```bash
nano /etc/nginx/sites-available/tmm
```

**Example configuration:**
```nginx
server {
    listen 80;
    server_name tmm.yourdomain.com 103.214.112.58;
    
    root /var/www/tmm/webroot;
    index index.php;
    
    access_log /var/log/nginx/tmm-access.log;
    error_log /var/log/nginx/tmm-error.log;
    
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;  # Adjust PHP version
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ /(config|src|vendor|tmp|logs) {
        deny all;
    }
}
```

Enable site and restart nginx:
```bash
ln -s /etc/nginx/sites-available/tmm /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

#### Apache Configuration (Alternative)
If using Apache instead of nginx:

```bash
nano /etc/apache2/sites-available/tmm.conf
```

**Example configuration:**
```apache
<VirtualHost *:80>
    ServerName tmm.yourdomain.com
    ServerAlias 103.214.112.58
    
    DocumentRoot /var/www/tmm/webroot
    
    <Directory /var/www/tmm/webroot>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/tmm-error.log
    CustomLog ${APACHE_LOG_DIR}/tmm-access.log combined
</VirtualHost>
```

Enable site and restart Apache:
```bash
a2ensite tmm
a2enmod rewrite
systemctl restart apache2
```

### 3. PHP Configuration
Verify PHP settings for CakePHP:

```bash
php -v  # Check PHP version (5.6 required for this project)
```

Required PHP extensions:
- mbstring
- intl
- simplexml
- pdo
- pdo_mysql
- gd (for image processing)

### 4. File Upload Directories
Ensure upload directories exist and are writable:

```bash
cd /var/www/tmm
mkdir -p webroot/img/uploads webroot/files/uploads
chmod -R 775 webroot/img/uploads webroot/files/uploads
chown -R www-data:www-data webroot/img/uploads webroot/files/uploads
```

### 5. Security Configuration
Update security settings in `config/app.php`:

```php
'Security' => [
    'salt' => 'YOUR_UNIQUE_SALT_HERE',  // Change this!
],
'Session' => [
    'defaults' => 'php',
    'cookie' => 'tmm_production',  // Unique cookie name
],
```

### 6. Test the Application

**Test URLs:**
- Homepage: http://103.214.112.58/
- Admin login: http://103.214.112.58/users/login
- Check database connectivity
- Test file uploads
- Verify menu system loads

**Check logs for errors:**
```bash
tail -f /var/www/tmm/logs/error.log
tail -f /var/log/nginx/tmm-error.log  # or apache2 log
```

### 7. Import Database (If Needed)
If you need to import the database from local:

```bash
# On local machine, export databases
mysqldump -u root cms_masters > cms_masters.sql
mysqldump -u root cms_orders > cms_orders.sql
# ... export all databases

# Upload to server
scp *.sql root@103.214.112.58:/tmp/

# On server, import
ssh root@103.214.112.58
mysql -u root -p cms_masters < /tmp/cms_masters.sql
mysql -u root -p cms_orders < /tmp/cms_orders.sql
# ... import all databases
```

## Automated Scripts Available

The following scripts are available in the project root:

1. **upload_project_to_production.ps1** - Full project upload (just used)
2. **export_all_databases.ps1** - Export all databases from local
3. **import_databases_to_production.ps1** - Import databases to production
4. **deploy_nginx_config.ps1** - Deploy nginx configuration

## Troubleshooting

### Bake Plugin Error
The "Plugin Bake could not be found" error is expected in production since bake is a development dependency. This is normal and does not affect the application.

### Permission Errors
If you see permission errors:
```bash
cd /var/www/tmm
chown -R www-data:www-data .
chmod -R 775 tmp logs webroot/img/uploads webroot/files/uploads
```

### Database Connection Errors
- Verify database credentials in `config/app.php`
- Check that MySQL is running: `systemctl status mysql`
- Verify databases exist: `mysql -u root -p -e "SHOW DATABASES;"`

### Web Server Not Serving
- Check nginx/apache is running: `systemctl status nginx`
- Verify site configuration is enabled
- Check error logs for details

## Quick Commands Reference

```bash
# SSH to server
ssh root@103.214.112.58

# Navigate to project
cd /var/www/tmm

# Check file permissions
ls -la

# View error logs
tail -f logs/error.log

# Clear cache
rm -rf tmp/cache/*/*

# Restart web server
systemctl restart nginx  # or apache2

# Check PHP version
php -v

# Test database connection
mysql -u root -p -e "SELECT 1"
```

## Deployment Checklist

- [x] Project files uploaded
- [x] File permissions set
- [x] Composer dependencies installed
- [x] Cache cleared
- [ ] Database configuration updated
- [ ] Web server configured
- [ ] Security salt updated
- [ ] Upload directories created
- [ ] Application tested
- [ ] Logs monitored

## Support

For issues or questions:
1. Check application logs: `/var/www/tmm/logs/error.log`
2. Check web server logs: `/var/log/nginx/` or `/var/log/apache2/`
3. Verify database connectivity
4. Review this deployment guide

---
**Deployment Date:** November 24, 2025  
**Project:** TMM - Multi-Database CMS  
**Server:** 103.214.112.58:/var/www/tmm
