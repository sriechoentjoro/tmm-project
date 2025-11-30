
# Install Let's Encrypt SSL Certificate on Production Server
# WARNING: This will install SSL for the domain asahifamily.id
# Make sure DNS is pointed to 103.214.112.58 first!

Write-Host "=== SSL Certificate Installation Script ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "This script will:" -ForegroundColor Yellow
Write-Host "1. Install Certbot (Let's Encrypt client)" -ForegroundColor Yellow
Write-Host "2. Obtain SSL certificate for asahifamily.id" -ForegroundColor Yellow
Write-Host "3. Configure nginx to use HTTPS" -ForegroundColor Yellow
Write-Host "4. Enable auto-renewal" -ForegroundColor Yellow
Write-Host ""

$domain = Read-Host "Enter domain name (default: asahifamily.id)"
if ([string]::IsNullOrWhiteSpace($domain)) {
    $domain = "asahifamily.id"
}

$email = Read-Host "Enter email for Let's Encrypt notifications"
if ([string]::IsNullOrWhiteSpace($email)) {
    Write-Host "ERROR: Email is required!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Installing SSL for: $domain" -ForegroundColor Green
Write-Host "Notification email: $email" -ForegroundColor Green
Write-Host ""

$confirm = Read-Host "Continue? (y/n)"
if ($confirm -ne 'y') {
    Write-Host "Installation cancelled." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "Step 1: Installing Certbot..." -ForegroundColor Cyan

ssh root@103.214.112.58 @"
# Update package list
apt-get update

# Install Certbot and nginx plugin
apt-get install -y certbot python3-certbot-nginx

# Check if nginx is running
systemctl status nginx --no-pager
"@

Write-Host ""
Write-Host "Step 2: Obtaining SSL certificate..." -ForegroundColor Cyan

ssh root@103.214.112.58 @"
# Obtain certificate and auto-configure nginx
certbot --nginx -d $domain -d www.$domain --non-interactive --agree-tos --email $email

# Test auto-renewal
certbot renew --dry-run
"@

Write-Host ""
Write-Host "Step 3: Configuring nginx for TMM application..." -ForegroundColor Cyan

ssh root@103.214.112.58 @"
# Backup current nginx config
cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup.\$(date +%Y%m%d-%H%M%S)

# Create SSL-enabled nginx config for TMM
cat > /etc/nginx/sites-available/tmm-ssl << 'NGINX_EOF'
server {
    listen 80;
    server_name $domain www.$domain 103.214.112.58;
    
    # Redirect HTTP to HTTPS
    return 301 https://\\\$host\\\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name $domain www.$domain;
    
    # SSL Configuration (Certbot will add certificate paths)
    ssl_certificate /etc/letsencrypt/live/$domain/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$domain/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    root /var/www/tmm/webroot;
    index index.php;
    
    # Logging
    access_log /var/log/nginx/tmm-access.log;
    error_log /var/log/nginx/tmm-error.log;
    
    # Main location
    location / {
        try_files \\\$uri \\\$uri/ /index.php?\\\$query_string;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \\\$document_root\\\$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}

# Allow HTTP access via IP for development
server {
    listen 80;
    server_name 103.214.112.58;
    
    root /var/www/tmm/webroot;
    index index.php;
    
    location / {
        try_files \\\$uri \\\$uri/ /index.php?\\\$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \\\$document_root\\\$fastcgi_script_name;
        include fastcgi_params;
    }
}
NGINX_EOF

# Enable the site
ln -sf /etc/nginx/sites-available/tmm-ssl /etc/nginx/sites-enabled/tmm-ssl

# Test nginx configuration
nginx -t

# Reload nginx if test passes
if [ \\\$? -eq 0 ]; then
    systemctl reload nginx
    echo "✅ Nginx reloaded successfully"
else
    echo "❌ Nginx configuration test failed!"
    exit 1
fi
"@

Write-Host ""
Write-Host "Step 4: Setting up auto-renewal cron job..." -ForegroundColor Cyan

ssh root@103.214.112.58 @"
# Certbot already sets up auto-renewal via systemd timer
# Check if it's enabled
systemctl list-timers | grep certbot

# Add backup cron job just in case
(crontab -l 2>/dev/null; echo "0 3 * * * certbot renew --quiet --post-hook 'systemctl reload nginx'") | crontab -
"@

Write-Host ""
Write-Host "=== SSL Installation Complete! ===" -ForegroundColor Green
Write-Host ""
Write-Host "Test your site:" -ForegroundColor Cyan
Write-Host "  https://$domain/tmm" -ForegroundColor White
Write-Host "  https://www.$domain/tmm" -ForegroundColor White
Write-Host ""
Write-Host "HTTP access via IP still works:" -ForegroundColor Cyan
Write-Host "  http://103.214.112.58/tmm" -ForegroundColor White
Write-Host ""
Write-Host "Certificate auto-renewal:" -ForegroundColor Cyan
Write-Host "  Certbot will automatically renew before expiration" -ForegroundColor White
Write-Host "  Check status: ssh root@103.214.112.58 'certbot certificates'" -ForegroundColor Yellow
Write-Host ""
Write-Host "Nginx logs:" -ForegroundColor Cyan
Write-Host "  Access: /var/log/nginx/tmm-access.log" -ForegroundColor White
Write-Host "  Error: /var/log/nginx/tmm-error.log" -ForegroundColor White
Write-Host ""
