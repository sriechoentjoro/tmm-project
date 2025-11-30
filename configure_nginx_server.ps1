# ============================================
# Configure NGINX Server for Multi-Project Access
# ============================================
# Target: 103.214.112.58
# ============================================

$serverIP = "103.214.112.58"
$serverUser = "root"

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  NGINX SERVER CONFIGURATION" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# ===== STEP 1: Create Project Folders =====
Write-Host "[STEP 1] Creating project folders..." -ForegroundColor Yellow

$commands = @(
    "mkdir -p /var/www/tmm",
    "mkdir -p /var/www/asahifamily.co.id",
    "chown -R www-data:www-data /var/www/tmm",
    "chown -R www-data:www-data /var/www/asahifamily.co.id",
    "chmod -R 755 /var/www/tmm",
    "chmod -R 755 /var/www/asahifamily.co.id"
)

foreach ($cmd in $commands) {
    ssh ${serverUser}@${serverIP} $cmd
}

Write-Host "  Done!" -ForegroundColor Green
Write-Host ""

# ===== STEP 2: Backup existing NGINX config =====
Write-Host "[STEP 2] Backing up existing NGINX configuration..." -ForegroundColor Yellow

ssh ${serverUser}@${serverIP} "cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup.$(date +%Y%m%d)"

Write-Host "  Done!" -ForegroundColor Green
Write-Host ""

# ===== STEP 3: Create NGINX configurations =====
Write-Host "[STEP 3] Creating NGINX configurations..." -ForegroundColor Yellow
Write-Host ""

# Config 1: Main server for IP access and dynamic folders
Write-Host "  Creating main server config..." -ForegroundColor White

$mainServerScript = @'
cat > /etc/nginx/sites-available/main_server << 'NGINX_EOF'
server {
    listen 80 default_server;
    server_name 103.214.112.58;
    
    root /var/www/html;
    index index.php index.html index.htm;
    
    access_log /var/log/nginx/main_access.log;
    error_log /var/log/nginx/main_error.log;
    
    charset utf-8;
    
    # PhpMyAdmin - tetap seperti sekarang
    location /phpmyadmin {
        root /usr/share/;
        index index.php index.html index.htm;
        
        location ~ ^/phpmyadmin/(.+\.php)$ {
            try_files $uri =404;
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
        
        location ~* ^/phpmyadmin/(.+\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))$ {
            root /usr/share/;
        }
    }
    
    # TMM Project
    location /tmm {
        alias /var/www/tmm/webroot;
        try_files $uri $uri/ @tmm_rewrite;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }
    
    location @tmm_rewrite {
        rewrite ^/tmm/(.*)$ /tmm/index.php?/$1 last;
    }
    
    # Asahi Family .co.id
    location /asahifamily.co.id {
        alias /var/www/asahifamily.co.id/webroot;
        try_files $uri $uri/ @asahi_coid_rewrite;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }
    
    location @asahi_coid_rewrite {
        rewrite ^/asahifamily.co.id/(.*)$ /asahifamily.co.id/index.php?/$1 last;
    }
    
    # Asahi Family .id
    location /asahifamily.id {
        alias /var/www/asahifamily.id/webroot;
        try_files $uri $uri/ @asahi_id_rewrite;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }
    
    location @asahi_id_rewrite {
        rewrite ^/asahifamily.id/(.*)$ /asahifamily.id/index.php?/$1 last;
    }
    
    # Default PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    # Deny hidden files
    location ~ /\. {
        deny all;
    }
    
    # Static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public";
    }
}
NGINX_EOF
'@

ssh ${serverUser}@${serverIP} $mainServerScript

Write-Host "    Done!" -ForegroundColor Green

# Config 2: asahifamily.co.id domain
Write-Host "  Creating asahifamily.co.id config..." -ForegroundColor White

$asahiCoidScript = @'
cat > /etc/nginx/sites-available/asahifamily.co.id << 'NGINX_EOF'
server {
    listen 80;
    server_name asahifamily.co.id www.asahifamily.co.id;
    
    root /var/www/asahifamily.co.id/webroot;
    index index.php index.html;
    
    access_log /var/log/nginx/asahifamily_coid_access.log;
    error_log /var/log/nginx/asahifamily_coid_error.log;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    location ~ /\. {
        deny all;
    }
    
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public";
    }
}
NGINX_EOF
'@

ssh ${serverUser}@${serverIP} $asahiCoidScript

Write-Host "    Done!" -ForegroundColor Green

# Config 3: asahifamily.id domain
Write-Host "  Creating asahifamily.id config..." -ForegroundColor White

$asahiIdScript = @'
cat > /etc/nginx/sites-available/asahifamily.id << 'NGINX_EOF'
server {
    listen 80;
    server_name asahifamily.id www.asahifamily.id;
    
    root /var/www/asahifamily.id/webroot;
    index index.php index.html;
    
    access_log /var/log/nginx/asahifamily_id_access.log;
    error_log /var/log/nginx/asahifamily_id_error.log;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    location ~ /\. {
        deny all;
    }
    
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public";
    }
}
NGINX_EOF
'@

ssh ${serverUser}@${serverIP} $asahiIdScript

Write-Host "    Done!" -ForegroundColor Green
Write-Host ""

# ===== STEP 4: Enable sites =====
Write-Host "[STEP 4] Enabling NGINX sites..." -ForegroundColor Yellow

$enableScript = @'
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/main_server /etc/nginx/sites-enabled/main_server
ln -sf /etc/nginx/sites-available/asahifamily.co.id /etc/nginx/sites-enabled/asahifamily.co.id
ln -sf /etc/nginx/sites-available/asahifamily.id /etc/nginx/sites-enabled/asahifamily.id
'@

ssh ${serverUser}@${serverIP} $enableScript

Write-Host "  Done!" -ForegroundColor Green
Write-Host ""

# ===== STEP 5: Test configuration =====
Write-Host "[STEP 5] Testing NGINX configuration..." -ForegroundColor Yellow

$testResult = ssh ${serverUser}@${serverIP} "nginx -t 2>&1"
Write-Host $testResult

if ($testResult -match "syntax is ok" -and $testResult -match "test is successful") {
    Write-Host "  Configuration is valid!" -ForegroundColor Green
    Write-Host ""
    
    # ===== STEP 6: Restart NGINX =====
    Write-Host "[STEP 6] Restarting NGINX..." -ForegroundColor Yellow
    
    ssh ${serverUser}@${serverIP} "systemctl restart nginx"
    Start-Sleep -Seconds 2
    
    $status = ssh ${serverUser}@${serverIP} "systemctl is-active nginx"
    
    if ($status -match "active") {
        Write-Host "  NGINX is running!" -ForegroundColor Green
    } else {
        Write-Host "  WARNING: NGINX may not be running properly!" -ForegroundColor Red
    }
} else {
    Write-Host "  ERROR: NGINX configuration has errors!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  CONFIGURATION COMPLETED!" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Access URLs:" -ForegroundColor Yellow
Write-Host "  1. TMM Project:           http://103.214.112.58/tmm" -ForegroundColor Cyan
Write-Host "  2. Asahi Family .co.id:   http://103.214.112.58/asahifamily.co.id" -ForegroundColor Cyan
Write-Host "  3. Asahi Family .co.id:   http://asahifamily.co.id" -ForegroundColor Cyan
Write-Host "  4. Asahi Family .id:      http://103.214.112.58/asahifamily.id" -ForegroundColor Cyan
Write-Host "  5. Asahi Family .id:      http://asahifamily.id" -ForegroundColor Cyan
Write-Host "  6. PhpMyAdmin:            http://103.214.112.58/phpmyadmin" -ForegroundColor Cyan
Write-Host ""
Write-Host "Note: Domain access requires DNS configuration" -ForegroundColor Gray
Write-Host "================================================" -ForegroundColor Cyan
