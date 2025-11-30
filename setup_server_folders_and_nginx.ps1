# ============================================
# Setup Server Folders and NGINX Configuration
# ============================================
# Purpose: Create project folders and configure NGINX for multi-project access
# Target: 103.214.112.58
# Projects: tmm, asahifamily.co.id, asahifamily.id
# ============================================

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  SETUP SERVER FOLDERS & NGINX CONFIGURATION" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

$serverIP = "103.214.112.58"
$serverUser = "root"

# ===== STEP 1: Create Project Folders =====
Write-Host "[STEP 1] Creating project folders on server..." -ForegroundColor Yellow
Write-Host ""

$folders = @(
    "/var/www/tmm",
    "/var/www/asahifamily.co.id"
)

foreach ($folder in $folders) {
    Write-Host "  Creating: $folder" -ForegroundColor White
    
    # Create folder if not exists
    $createCmd = "mkdir -p $folder"
    ssh ${serverUser}@${serverIP} $createCmd
    
    # Set ownership to www-data
    $chownCmd = "chown -R www-data:www-data $folder"
    ssh ${serverUser}@${serverIP} $chownCmd
    
    # Set permissions
    $chmodCmd = "chmod -R 755 $folder"
    ssh ${serverUser}@${serverIP} $chmodCmd
    
    Write-Host "  ✓ Created and configured: $folder" -ForegroundColor Green
}

Write-Host ""

# ===== STEP 2: Verify Folders =====
Write-Host "[STEP 2] Verifying created folders..." -ForegroundColor Yellow
ssh ${serverUser}@${serverIP} "ls -la /var/www/ | grep -E 'tmm|asahifamily'"

Write-Host ""

# ===== STEP 3: Create NGINX Configuration =====
Write-Host "[STEP 3] Creating NGINX configuration for dynamic folder access..." -ForegroundColor Yellow
Write-Host ""

# NGINX configuration content - write directly to file to avoid escaping issues
$nginxConfigContent = @'
# ============================================
# NGINX Dynamic Folder Configuration
# ============================================
# Purpose: Allow access to all folders in /var/www/
# URL Pattern: http://103.214.112.58/[folder_name]
# Auto-created by: setup_server_folders_and_nginx.ps1
# ============================================

server {
    listen 80;
    server_name 103.214.112.58;
    
    # Root directory for default access
    root /var/www/html;
    index index.php index.html index.htm;
    
    # Logging
    access_log /var/log/nginx/multi_project_access.log;
    error_log /var/log/nginx/multi_project_error.log;
    
    # Character encoding
    charset utf-8;
    
    # ===== LOCATION: TMM Project =====
    location /tmm {
        alias /var/www/tmm/webroot;
        try_files $uri $uri/ /tmm/index.php?$args;
        
        # PHP processing
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }
    
    # ===== LOCATION: Asahi Family .co.id =====
    location /asahifamily.co.id {
        alias /var/www/asahifamily.co.id/webroot;
        try_files $uri $uri/ /asahifamily.co.id/index.php?$args;
        
        # PHP processing
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }
    
    # ===== LOCATION: Asahi Family .id =====
    location /asahifamily.id {
        alias /var/www/asahifamily.id/webroot;
        try_files $uri $uri/ /asahifamily.id/index.php?$args;
        
        # PHP processing
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }
    
    # ===== DEFAULT: PHP processing =====
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
    
    # Static files optimization
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
'@

# Save NGINX config to temporary file
$tempConfigFile = "nginx_multi_project.conf"
$nginxConfigContent | Out-File -FilePath $tempConfigFile -Encoding UTF8

Write-Host "  NGINX configuration created locally: $tempConfigFile" -ForegroundColor White

# ===== STEP 4: Upload NGINX Configuration =====
Write-Host ""
Write-Host "[STEP 4] Uploading NGINX configuration to server..." -ForegroundColor Yellow

# Upload config file
scp $tempConfigFile ${serverUser}@${serverIP}:/etc/nginx/sites-available/multi_project

Write-Host "  ✓ Configuration uploaded" -ForegroundColor Green

# ===== STEP 5: Enable NGINX Site =====
Write-Host ""
Write-Host "[STEP 5] Enabling NGINX site configuration..." -ForegroundColor Yellow

# Remove default site if exists
ssh ${serverUser}@${serverIP} "rm -f /etc/nginx/sites-enabled/default"

# Create symlink
ssh ${serverUser}@${serverIP} "ln -sf /etc/nginx/sites-available/multi_project /etc/nginx/sites-enabled/multi_project"

Write-Host "  ✓ Site enabled" -ForegroundColor Green

# ===== STEP 6: Test NGINX Configuration =====
Write-Host ""
Write-Host "[STEP 6] Testing NGINX configuration..." -ForegroundColor Yellow

$testResult = ssh ${serverUser}@${serverIP} "nginx -t 2>&1"
Write-Host $testResult

if ($testResult -match "syntax is ok" -and $testResult -match "test is successful") {
    Write-Host "  ✓ NGINX configuration is valid!" -ForegroundColor Green
    
    # ===== STEP 7: Restart NGINX =====
    Write-Host ""
    Write-Host "[STEP 7] Restarting NGINX..." -ForegroundColor Yellow
    
    ssh ${serverUser}@${serverIP} "systemctl restart nginx"
    
    Start-Sleep -Seconds 2
    
    $nginxStatus = ssh ${serverUser}@${serverIP} "systemctl status nginx | grep Active"
    Write-Host "  $nginxStatus" -ForegroundColor White
    
    Write-Host "  ✓ NGINX restarted successfully!" -ForegroundColor Green
} else {
    Write-Host "  ✗ NGINX configuration has errors!" -ForegroundColor Red
    Write-Host "  Please review the configuration and fix errors." -ForegroundColor Yellow
    exit 1
}

# ===== STEP 8: Summary =====
Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  SETUP COMPLETED SUCCESSFULLY!" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Project Folder Mapping:" -ForegroundColor Yellow
Write-Host "  1. TMM Project" -ForegroundColor White
Write-Host "     Local:  D:\xampp\htdocs\tmm" -ForegroundColor Gray
Write-Host "     Server: /var/www/tmm" -ForegroundColor Gray
Write-Host "     URL:    http://103.214.112.58/tmm" -ForegroundColor Cyan
Write-Host ""
Write-Host "  2. Asahi Family .co.id" -ForegroundColor White
Write-Host "     Local:  D:\xampp\htdocs\asahifamily.co.id" -ForegroundColor Gray
Write-Host "     Server: /var/www/asahifamily.co.id" -ForegroundColor Gray
Write-Host "     URL:    http://103.214.112.58/asahifamily.co.id" -ForegroundColor Cyan
Write-Host ""
Write-Host "  3. Asahi Family .id" -ForegroundColor White
Write-Host "     Local:  D:\xampp\htdocs\asahifamily.id" -ForegroundColor Gray
Write-Host "     Server: /var/www/asahifamily.id" -ForegroundColor Gray
Write-Host "     URL:    http://103.214.112.58/asahifamily.id" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  1. Upload your project files to respective server folders" -ForegroundColor White
Write-Host "  2. Configure database connections in config/app_datasources.php" -ForegroundColor White
Write-Host "  3. Set proper permissions: chmod 777 tmp/ logs/ webroot/img/uploads/" -ForegroundColor White
Write-Host "  4. Test access via browser: http://103.214.112.58/[folder_name]" -ForegroundColor White
Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan

# Clean up temporary file
Remove-Item $tempConfigFile -Force
