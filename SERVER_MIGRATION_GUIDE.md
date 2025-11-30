# Server Migration Guide - asahifamily.id & asahifamily.co.id

**Date:** November 17, 2025  
**Server IP:** 103.214.112.58

---

## 📋 Task Overview

### Task 1: Move asahifamily.id
- **From:** `/var/www/html/asahi_transportation`
- **To:** `/var/www/html/asahi_factory`
- **Domain:** asahifamily.id (existing domain, already pointed to this server)

### Task 2: Setup asahifamily.co.id
- **Current Registrar:** Other registrar
- **Target Server:** 103.214.112.58
- **Target Folder:** `/var/www/html/asahi_transportation`
- **EPP Code:** `#q%h2jAN%` (for domain transfer)

### Task 3: Maintain projects folder access
- **Folder:** `/var/www/html/projects`
- **Access:** http://103.214.112.58/projects (direct IP access)

---

## 🔧 Step-by-Step Instructions

### PART 1: Move asahifamily.id to asahi_factory

#### Step 1.1: SSH to Server
```bash
ssh root@103.214.112.58
```

#### Step 1.2: Backup Current Setup
```bash
# Backup asahi_transportation folder
cd /var/www/html
tar -czf asahi_transportation_backup_$(date +%Y%m%d_%H%M%S).tar.gz asahi_transportation/

# Backup database (if applicable)
mysqldump -u root -p asahi_transportation_db > /root/asahi_transportation_db_backup_$(date +%Y%m%d_%H%M%S).sql

# Backup Apache/Nginx config
cp /etc/apache2/sites-available/asahifamily.id.conf /root/asahifamily.id.conf.backup
# OR for Nginx:
# cp /etc/nginx/sites-available/asahifamily.id /root/asahifamily.id.backup
```

#### Step 1.3: Copy Files to New Location
```bash
# Copy entire folder to new location
cp -r /var/www/html/asahi_transportation /var/www/html/asahi_factory

# Or move if you want to remove old location:
# mv /var/www/html/asahi_transportation /var/www/html/asahi_factory

# Set proper ownership
chown -R www-data:www-data /var/www/html/asahi_factory
# Or for Apache:
# chown -R apache:apache /var/www/html/asahi_factory

# Set proper permissions
find /var/www/html/asahi_factory -type d -exec chmod 755 {} \;
find /var/www/html/asahi_factory -type f -exec chmod 644 {} \;

# Make writable directories (logs, tmp, uploads, etc.)
chmod -R 775 /var/www/html/asahi_factory/logs
chmod -R 775 /var/www/html/asahi_factory/tmp
chmod -R 775 /var/www/html/asahi_factory/webroot/img/uploads
chmod -R 775 /var/www/html/asahi_factory/webroot/files/uploads
```

#### Step 1.4: Update Apache Virtual Host Configuration
```bash
# Edit Apache config for asahifamily.id
nano /etc/apache2/sites-available/asahifamily.id.conf
```

**Update DocumentRoot in config:**
```apache
<VirtualHost *:80>
    ServerName asahifamily.id
    ServerAlias www.asahifamily.id
    
    # CHANGE THIS LINE:
    DocumentRoot /var/www/html/asahi_factory
    
    <Directory /var/www/html/asahi_factory>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/asahifamily.id_error.log
    CustomLog ${APACHE_LOG_DIR}/asahifamily.id_access.log combined
</VirtualHost>

# SSL Configuration (if HTTPS is enabled)
<VirtualHost *:443>
    ServerName asahifamily.id
    ServerAlias www.asahifamily.id
    
    # CHANGE THIS LINE:
    DocumentRoot /var/www/html/asahi_factory
    
    <Directory /var/www/html/asahi_factory>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/asahifamily.id/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/asahifamily.id/privkey.pem
    
    ErrorLog ${APACHE_LOG_DIR}/asahifamily.id_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/asahifamily.id_ssl_access.log combined
</VirtualHost>
```

**OR for Nginx:**
```bash
nano /etc/nginx/sites-available/asahifamily.id
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name asahifamily.id www.asahifamily.id;

    # CHANGE THIS LINE:
    root /var/www/html/asahi_factory;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    access_log /var/log/nginx/asahifamily.id_access.log;
    error_log /var/log/nginx/asahifamily.id_error.log;
}

# SSL Configuration
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name asahifamily.id www.asahifamily.id;

    # CHANGE THIS LINE:
    root /var/www/html/asahi_factory;
    index index.php index.html;

    ssl_certificate /etc/letsencrypt/live/asahifamily.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/asahifamily.id/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    access_log /var/log/nginx/asahifamily.id_ssl_access.log;
    error_log /var/log/nginx/asahifamily.id_ssl_error.log;
}
```

#### Step 1.5: Test Configuration and Restart
```bash
# For Apache:
apache2ctl configtest
systemctl restart apache2

# For Nginx:
nginx -t
systemctl restart nginx

# Check status
systemctl status apache2
# OR
systemctl status nginx
```

#### Step 1.6: Test Website
```bash
# Test from server
curl -I http://localhost -H "Host: asahifamily.id"
curl -I https://asahifamily.id

# Check from browser:
# http://asahifamily.id
# https://asahifamily.id
```

---

### PART 2: Setup asahifamily.co.id in asahi_transportation

#### Step 2.1: Domain Transfer Process

**⚠️ IMPORTANT: Domain transfer takes 5-7 days. Do NOT start transfer until you're ready!**

**Option A: Transfer domain to your registrar**
1. Login to current domain registrar
2. Unlock domain for transfer
3. Get EPP/Auth code: `#q%h2jAN%`
4. Initiate transfer at your preferred registrar (e.g., Cloudflare, Namecheap, GoDaddy)
5. Wait for confirmation email
6. Approve transfer
7. Wait 5-7 days for transfer completion

**Option B: Point domain to your server without transfer**
1. Login to current domain registrar
2. Go to DNS Management
3. Update A record to point to: `103.214.112.58`
4. Update NS records (if using custom nameservers)

**DNS Configuration (at registrar or DNS provider):**
```
Type    Name    Value               TTL
A       @       103.214.112.58      3600
A       www     103.214.112.58      3600
```

#### Step 2.2: Prepare asahi_transportation Folder
```bash
# If folder doesn't exist, create it
mkdir -p /var/www/html/asahi_transportation

# Copy files from asahi_factory or setup new site
# Option 1: Copy from asahi_factory
cp -r /var/www/html/asahi_factory/* /var/www/html/asahi_transportation/

# Option 2: Clone from Git repository
# git clone https://github.com/your-repo/asahi_transportation.git /var/www/html/asahi_transportation

# Set ownership
chown -R www-data:www-data /var/www/html/asahi_transportation

# Set permissions
find /var/www/html/asahi_transportation -type d -exec chmod 755 {} \;
find /var/www/html/asahi_transportation -type f -exec chmod 644 {} \;

# Writable directories
chmod -R 775 /var/www/html/asahi_transportation/logs
chmod -R 775 /var/www/html/asahi_transportation/tmp
chmod -R 775 /var/www/html/asahi_transportation/webroot/img/uploads
chmod -R 775 /var/www/html/asahi_transportation/webroot/files/uploads
```

#### Step 2.3: Create Apache Virtual Host for asahifamily.co.id
```bash
nano /etc/apache2/sites-available/asahifamily.co.id.conf
```

```apache
<VirtualHost *:80>
    ServerName asahifamily.co.id
    ServerAlias www.asahifamily.co.id
    
    DocumentRoot /var/www/html/asahi_transportation
    
    <Directory /var/www/html/asahi_transportation>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/asahifamily.co.id_error.log
    CustomLog ${APACHE_LOG_DIR}/asahifamily.co.id_access.log combined
</VirtualHost>
```

**Enable the site:**
```bash
a2ensite asahifamily.co.id.conf
apache2ctl configtest
systemctl reload apache2
```

**OR for Nginx:**
```bash
nano /etc/nginx/sites-available/asahifamily.co.id
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name asahifamily.co.id www.asahifamily.co.id;

    root /var/www/html/asahi_transportation;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    access_log /var/log/nginx/asahifamily.co.id_access.log;
    error_log /var/log/nginx/asahifamily.co.id_error.log;
}
```

```bash
ln -s /etc/nginx/sites-available/asahifamily.co.id /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

#### Step 2.4: Setup SSL Certificate (Let's Encrypt)
```bash
# Install certbot (if not already installed)
apt update
apt install certbot python3-certbot-apache -y
# OR for Nginx:
# apt install certbot python3-certbot-nginx -y

# Generate SSL certificate
certbot --apache -d asahifamily.co.id -d www.asahifamily.co.id
# OR for Nginx:
# certbot --nginx -d asahifamily.co.id -d www.asahifamily.co.id

# Follow prompts:
# - Enter email address
# - Agree to terms
# - Choose redirect HTTP to HTTPS (recommended)

# Auto-renewal is setup automatically
# Test renewal:
certbot renew --dry-run
```

#### Step 2.5: Test asahifamily.co.id
```bash
# Test HTTP
curl -I http://asahifamily.co.id

# Test HTTPS
curl -I https://asahifamily.co.id

# Check DNS propagation
nslookup asahifamily.co.id
dig asahifamily.co.id
```

---

### PART 3: Setup Direct IP Access for /projects

#### Step 3.1: Create Default Site for IP Access
```bash
nano /etc/apache2/sites-available/000-default.conf
```

**Apache configuration:**
```apache
<VirtualHost *:80>
    # This is the default site for IP access
    ServerName 103.214.112.58
    
    DocumentRoot /var/www/html
    
    # Main directory
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Projects directory with directory listing
    <Directory /var/www/html/projects>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.html index.php
    </Directory>
    
    Alias /projects /var/www/html/projects
    
    ErrorLog ${APACHE_LOG_DIR}/ip_access_error.log
    CustomLog ${APACHE_LOG_DIR}/ip_access_access.log combined
</VirtualHost>
```

**Enable and restart:**
```bash
a2ensite 000-default.conf
apache2ctl configtest
systemctl reload apache2
```

**OR for Nginx:**
```bash
nano /etc/nginx/sites-available/default
```

```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    
    server_name 103.214.112.58;
    
    root /var/www/html;
    index index.php index.html index.htm;

    # Main location
    location / {
        autoindex on;
        try_files $uri $uri/ =404;
    }

    # Projects directory
    location /projects {
        alias /var/www/html/projects;
        autoindex on;
        index index.html index.php;
    }

    # PHP processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }

    access_log /var/log/nginx/ip_access_access.log;
    error_log /var/log/nginx/ip_access_error.log;
}
```

```bash
nginx -t
systemctl reload nginx
```

#### Step 3.2: Test IP Access
```bash
# Test from server
curl -I http://103.214.112.58/projects/

# Test from browser:
# http://103.214.112.58/projects/
```

#### Step 3.3: Create Index Page for Projects (Optional)
```bash
nano /var/www/html/projects/index.html
```

```html
<!DOCTYPE html>
<html>
<head>
    <title>Projects Directory</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #333; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { color: #0066cc; text-decoration: none; font-size: 18px; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Available Projects</h1>
    <ul>
        <li><a href="project_tmm/">📁 TMM Apprentice Management</a></li>
        <li><a href="asahi_v3/">📁 Asahi V3</a></li>
        <!-- Add more project links as needed -->
    </ul>
</body>
</html>
```

---

## 🧪 Testing Checklist

### After Part 1 (asahifamily.id move):
- [ ] http://asahifamily.id loads correctly
- [ ] https://asahifamily.id works with SSL
- [ ] All pages and links work
- [ ] Database connections work
- [ ] File uploads work
- [ ] Login/authentication works

### After Part 2 (asahifamily.co.id setup):
- [ ] DNS resolves to 103.214.112.58
- [ ] http://asahifamily.co.id loads correctly
- [ ] https://asahifamily.co.id works with SSL
- [ ] All functionality works

### After Part 3 (IP access):
- [ ] http://103.214.112.58/projects/ accessible
- [ ] Directory listing works (if enabled)
- [ ] All project subdirectories accessible

---

## 🔒 Security Checklist

```bash
# Set proper firewall rules
ufw allow 80/tcp
ufw allow 443/tcp
ufw allow 22/tcp
ufw enable

# Check firewall status
ufw status

# Disable directory listing (if not wanted)
# For Apache: Add "Options -Indexes" in Directory directive
# For Nginx: Remove "autoindex on;"

# Set secure file permissions
find /var/www/html -type d -exec chmod 755 {} \;
find /var/www/html -type f -exec chmod 644 {} \;

# Protect sensitive files
chmod 640 /var/www/html/*/config/app.php
chmod 640 /var/www/html/*/config/app_local.php

# Install fail2ban (if not installed)
apt install fail2ban -y
systemctl enable fail2ban
systemctl start fail2ban
```

---

## 📊 Post-Migration Monitoring

```bash
# Monitor Apache/Nginx logs
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log

# Monitor access logs
tail -f /var/log/apache2/access.log
tail -f /var/log/nginx/access.log

# Check SSL certificate status
certbot certificates

# Monitor disk space
df -h

# Monitor memory usage
free -h

# Check running processes
ps aux | grep apache2
ps aux | grep nginx
ps aux | grep php-fpm
```

---

## 🆘 Rollback Plan (If Something Goes Wrong)

### Rollback Part 1 (asahifamily.id):
```bash
# Restore old Apache/Nginx config
cp /root/asahifamily.id.conf.backup /etc/apache2/sites-available/asahifamily.id.conf
systemctl reload apache2

# Restore files
rm -rf /var/www/html/asahi_factory
tar -xzf asahi_transportation_backup_*.tar.gz -C /var/www/html/
```

### Rollback Part 2 (asahifamily.co.id):
```bash
# Disable site
a2dissite asahifamily.co.id.conf
# OR
rm /etc/nginx/sites-enabled/asahifamily.co.id

# Remove SSL certificate
certbot delete --cert-name asahifamily.co.id

# Update DNS to point back to old server
```

---

## 📞 Support Contacts

- **Server Admin:** root@103.214.112.58
- **Domain Registrar:** Contact support with EPP code `#q%h2jAN%`
- **SSL Provider:** Let's Encrypt (automatic)

---

## ✅ Final Verification Commands

```bash
# Check all virtual hosts
apache2ctl -S
# OR
nginx -T

# Test all domains
curl -I http://asahifamily.id
curl -I https://asahifamily.id
curl -I http://asahifamily.co.id
curl -I https://asahifamily.co.id
curl -I http://103.214.112.58/projects/

# Check DNS
nslookup asahifamily.id
nslookup asahifamily.co.id

# Check SSL certificates
openssl s_client -connect asahifamily.id:443 -servername asahifamily.id
openssl s_client -connect asahifamily.co.id:443 -servername asahifamily.co.id
```

---

**⚠️ IMPORTANT NOTES:**

1. **Domain Transfer:** Takes 5-7 days. Site will continue working during transfer if DNS is already pointing to your server.

2. **DNS Propagation:** Changes take 24-48 hours to propagate globally. Use https://dnschecker.org to monitor.

3. **Backup Everything:** Always backup before making changes.

4. **Test Before Going Live:** Test thoroughly in browser from different locations.

5. **SSL Certificates:** Let's Encrypt certificates auto-renew every 90 days.

6. **Database:** If applications use databases, ensure connection strings are updated in config files.

---

**Created:** November 17, 2025  
**Last Updated:** November 17, 2025
