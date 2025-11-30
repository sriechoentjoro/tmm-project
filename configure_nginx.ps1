# Configure Nginx for TMM Application
# This script creates and deploys nginx configuration for the TMM application

$ErrorActionPreference = "Stop"

$serverUser = "root"
$serverHost = "103.214.112.58"
$appPath = "/var/www/tmm"
$serverName = "103.214.112.58"  # Can also add domain name

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Configure Nginx for TMM Application" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Nginx configuration content
$nginxConfig = @"
server {
    listen 80;
    listen [::]:80;
    
    server_name $serverName;
    
    root $appPath/webroot;
    index index.php index.html;
    
    access_log /var/log/nginx/tmm-access.log;
    error_log /var/log/nginx/tmm-error.log;
    
    # Main location
    location / {
        try_files `$uri `$uri/ /index.php?`$args;
    }
    
    # PHP-FPM configuration
    location ~ \.php$ {
        try_files `$uri =404;
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME `$document_root`$fastcgi_script_name;
        include fastcgi_params;
        
        # Additional FastCGI settings
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_read_timeout 300;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Deny access to sensitive directories
    location ~ /(config|src|vendor|tmp|logs|tests) {
        deny all;
        return 404;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;
}
"@

# Save config locally
$localConfigFile = "d:\xampp\htdocs\tmm\nginx_tmm.conf"
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($localConfigFile, $nginxConfig, $utf8NoBom)

Write-Host "[OK] Nginx configuration created locally: $localConfigFile" -ForegroundColor Green
Write-Host ""

# Display configuration
Write-Host "Configuration Preview:" -ForegroundColor Cyan
Write-Host "----------------------------------------" -ForegroundColor Gray
Write-Host $nginxConfig -ForegroundColor Gray
Write-Host "----------------------------------------" -ForegroundColor Gray
Write-Host ""

# Ask for confirmation
Write-Host "Ready to deploy to production server?" -ForegroundColor Yellow
Write-Host "This will:" -ForegroundColor White
Write-Host "  1. Upload configuration to /etc/nginx/sites-available/tmm" -ForegroundColor Gray
Write-Host "  2. Create symbolic link in sites-enabled" -ForegroundColor Gray
Write-Host "  3. Test nginx configuration" -ForegroundColor Gray
Write-Host "  4. Reload nginx" -ForegroundColor Gray
Write-Host ""
$confirm = Read-Host "Continue? (yes/no)"

if ($confirm -ne "yes") {
    Write-Host "Deployment cancelled" -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "Step 1: Uploading nginx configuration..." -ForegroundColor Green

# Upload to temp location first
scp $localConfigFile "${serverUser}@${serverHost}:/tmp/tmm.conf"

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Failed to upload configuration" -ForegroundColor Red
    exit 1
}

Write-Host "[OK] Configuration uploaded" -ForegroundColor Green

# Move to nginx directory and configure
Write-Host ""
Write-Host "Step 2: Installing configuration..." -ForegroundColor Green

$setupCommands = @"
# Backup existing configuration if it exists
if [ -f /etc/nginx/sites-available/tmm ]; then
    cp /etc/nginx/sites-available/tmm /etc/nginx/sites-available/tmm.backup_`$(date +%Y%m%d_%H%M%S)
    echo "Existing configuration backed up"
fi

# Move new configuration
mv /tmp/tmm.conf /etc/nginx/sites-available/tmm
chmod 644 /etc/nginx/sites-available/tmm

# Create symbolic link if not exists
if [ ! -L /etc/nginx/sites-enabled/tmm ]; then
    ln -s /etc/nginx/sites-available/tmm /etc/nginx/sites-enabled/tmm
    echo "Symbolic link created"
else
    echo "Symbolic link already exists"
fi

# Remove default site if it exists and conflicts
if [ -L /etc/nginx/sites-enabled/default ]; then
    rm /etc/nginx/sites-enabled/default
    echo "Default site disabled"
fi

echo "Configuration installed"
"@

ssh "$serverUser@$serverHost" $setupCommands
Write-Host "[OK] Configuration installed" -ForegroundColor Green

# Test nginx configuration
Write-Host ""
Write-Host "Step 3: Testing nginx configuration..." -ForegroundColor Green

$testResult = ssh "$serverUser@$serverHost" "nginx -t 2>&1"
Write-Host $testResult -ForegroundColor Gray

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Nginx configuration is valid" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Nginx configuration has errors" -ForegroundColor Red
    Write-Host "Please check the output above" -ForegroundColor Yellow
    exit 1
}

# Reload nginx
Write-Host ""
Write-Host "Step 4: Reloading nginx..." -ForegroundColor Green

ssh "$serverUser@$serverHost" "systemctl reload nginx"

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Nginx reloaded successfully" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Failed to reload nginx" -ForegroundColor Red
    ssh "$serverUser@$serverHost" "systemctl status nginx"
    exit 1
}

# Check nginx status
Write-Host ""
Write-Host "Step 5: Verifying nginx status..." -ForegroundColor Green

$statusOutput = ssh "$serverUser@$serverHost" "systemctl is-active nginx"

if ($statusOutput -match "active") {
    Write-Host "[OK] Nginx is running" -ForegroundColor Green
} else {
    Write-Host "[WARNING] Nginx status: $statusOutput" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "Nginx Configuration Complete!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration Details:" -ForegroundColor White
Write-Host "  Server: $serverName" -ForegroundColor Gray
Write-Host "  Document Root: $appPath/webroot" -ForegroundColor Gray
Write-Host "  PHP-FPM: unix:/run/php/php7.4-fpm.sock" -ForegroundColor Gray
Write-Host "  Access Log: /var/log/nginx/tmm-access.log" -ForegroundColor Gray
Write-Host "  Error Log: /var/log/nginx/tmm-error.log" -ForegroundColor Gray
Write-Host ""
Write-Host "Test the application:" -ForegroundColor Yellow
Write-Host "  http://$serverName" -ForegroundColor Cyan
Write-Host ""
Write-Host "View logs:" -ForegroundColor Yellow
Write-Host "  ssh $serverUser@$serverHost 'tail -f /var/log/nginx/tmm-error.log'" -ForegroundColor Cyan
Write-Host "  ssh $serverUser@$serverHost 'tail -f $appPath/logs/error.log'" -ForegroundColor Cyan
Write-Host ""
