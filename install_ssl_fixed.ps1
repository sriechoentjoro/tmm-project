# Install Let's Encrypt SSL Certificate on Production Server
# Fixed version with proper Linux line endings

Write-Host "=== SSL Certificate Installation Script ===" -ForegroundColor Cyan
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

# Step 1: Install Certbot
Write-Host ""
Write-Host "Step 1: Installing Certbot..." -ForegroundColor Cyan

ssh root@103.214.112.58 "apt-get update && apt-get install -y certbot python3-certbot-nginx && systemctl status nginx --no-pager"

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Certbot installation failed!" -ForegroundColor Red
    exit 1
}

# Step 2: Obtain SSL certificate
Write-Host ""
Write-Host "Step 2: Obtaining SSL certificate..." -ForegroundColor Cyan

ssh root@103.214.112.58 "certbot --nginx -d $domain -d www.$domain --non-interactive --agree-tos --email $email && certbot renew --dry-run"

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Certificate generation failed!" -ForegroundColor Red
    Write-Host "Common causes:" -ForegroundColor Yellow
    Write-Host "  - DNS not pointing to server" -ForegroundColor Yellow
    Write-Host "  - Firewall blocking port 80/443" -ForegroundColor Yellow
    Write-Host "  - Domain not accessible from internet" -ForegroundColor Yellow
    exit 1
}

# Step 3: Configure nginx
Write-Host ""
Write-Host "Step 3: Configuring nginx..." -ForegroundColor Cyan

# Create nginx config file locally first
$nginxConfig = @"
server {
    listen 80;
    server_name $domain www.$domain 103.214.112.58;
    
    # Redirect HTTP to HTTPS (except IP access)
    if (`$host != "103.214.112.58") {
        return 301 https://`$host`$request_uri;
    }
    
    # Allow HTTP access via IP
    root /var/www/tmm/webroot;
    index index.php;
    
    location / {
        try_files `$uri `$uri/ /index.php?`$query_string;
    }
    
    location ~ \.php`$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME `$document_root`$fastcgi_script_name;
        include fastcgi_params;
    }
}

server {
    listen 443 ssl http2;
    server_name $domain www.$domain;
    
    ssl_certificate /etc/letsencrypt/live/$domain/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$domain/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
    
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    root /var/www/tmm/webroot;
    index index.php;
    
    access_log /var/log/nginx/tmm-ssl-access.log;
    error_log /var/log/nginx/tmm-ssl-error.log;
    
    location / {
        try_files `$uri `$uri/ /index.php?`$query_string;
    }
    
    location ~ \.php`$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME `$document_root`$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\. {
        deny all;
    }
}
"@

# Save locally
$nginxConfig | Out-File -FilePath "tmm-nginx-ssl.conf" -Encoding UTF8 -NoNewline

# Upload to server
Write-Host "Uploading nginx configuration..." -ForegroundColor Cyan
Get-Content "tmm-nginx-ssl.conf" | ssh root@103.214.112.58 "cat > /etc/nginx/sites-available/tmm-ssl"

# Enable and test
ssh root@103.214.112.58 "ln -sf /etc/nginx/sites-available/tmm-ssl /etc/nginx/sites-enabled/tmm-ssl && nginx -t && systemctl reload nginx"

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Nginx configured successfully" -ForegroundColor Green
} else {
    Write-Host "❌ Nginx configuration failed!" -ForegroundColor Red
    exit 1
}

# Step 4: Setup auto-renewal
Write-Host ""
Write-Host "Step 4: Setting up auto-renewal..." -ForegroundColor Cyan

ssh root@103.214.112.58 "systemctl list-timers | grep certbot && echo '0 3 * * * certbot renew --quiet --post-hook \"systemctl reload nginx\"' | crontab -"

# Cleanup
Remove-Item "tmm-nginx-ssl.conf" -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "=== SSL Installation Complete! ===" -ForegroundColor Green
Write-Host ""
Write-Host "✅ Test your HTTPS site:" -ForegroundColor Cyan
Write-Host "   https://$domain/tmm" -ForegroundColor White
Write-Host "   https://www.$domain/tmm" -ForegroundColor White
Write-Host ""
Write-Host "✅ HTTP via IP still works:" -ForegroundColor Cyan
Write-Host "   http://103.214.112.58/tmm" -ForegroundColor White
Write-Host ""
Write-Host "Check certificate status:" -ForegroundColor Yellow
Write-Host "   ssh root@103.214.112.58 'certbot certificates'" -ForegroundColor Gray
Write-Host ""
