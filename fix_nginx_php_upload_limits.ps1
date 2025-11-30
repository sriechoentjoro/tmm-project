# Fix Nginx 413 Error - Request Entity Too Large
# Fix PHP upload limits for phpMyAdmin and application

$ErrorActionPreference = "Stop"

$serverUser = "root"
$serverHost = "103.214.112.58"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Fix Nginx 413 & PHP Upload Limits" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "This script will:" -ForegroundColor Yellow
Write-Host "1. Increase Nginx client_max_body_size to 512M" -ForegroundColor White
Write-Host "2. Increase PHP upload_max_filesize to 512M" -ForegroundColor White
Write-Host "3. Increase PHP post_max_size to 512M" -ForegroundColor White
Write-Host "4. Increase PHP max_execution_time to 600s" -ForegroundColor White
Write-Host "5. Increase PHP max_input_time to 600s" -ForegroundColor White
Write-Host "6. Restart Nginx and PHP-FPM services" -ForegroundColor White
Write-Host ""

$sshCommands = @'
#!/bin/bash
set -e

echo "========================================="
echo "Step 1: Backup current configurations"
echo "========================================="
cp /etc/nginx/sites-available/phpmyadmin.conf /etc/nginx/sites-available/phpmyadmin.conf.backup-$(date +%Y%m%d-%H%M%S)
cp /etc/php/7.4/fpm/php.ini /etc/php/7.4/fpm/php.ini.backup-$(date +%Y%m%d-%H%M%S)
echo "Backups created"
echo ""

echo "========================================="
echo "Step 2: Update Nginx Configuration"
echo "========================================="

# Add client_max_body_size to phpmyadmin.conf
sed -i '/server {/a \    client_max_body_size 512M;' /etc/nginx/sites-available/phpmyadmin.conf

# Also add to main nginx.conf in http block
if ! grep -q "client_max_body_size" /etc/nginx/nginx.conf; then
    sed -i '/http {/a \    client_max_body_size 512M;' /etc/nginx/nginx.conf
    echo "Added client_max_body_size to /etc/nginx/nginx.conf"
else
    sed -i 's/client_max_body_size [^;]*;/client_max_body_size 512M;/g' /etc/nginx/nginx.conf
    echo "Updated client_max_body_size in /etc/nginx/nginx.conf"
fi

echo "Nginx configuration updated"
echo ""

echo "========================================="
echo "Step 3: Update PHP Configuration"
echo "========================================="

# Update PHP settings
sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 512M/' /etc/php/7.4/fpm/php.ini
sed -i 's/^post_max_size = .*/post_max_size = 512M/' /etc/php/7.4/fpm/php.ini
sed -i 's/^max_execution_time = .*/max_execution_time = 600/' /etc/php/7.4/fpm/php.ini
sed -i 's/^max_input_time = .*/max_input_time = 600/' /etc/php/7.4/fpm/php.ini
sed -i 's/^memory_limit = .*/memory_limit = 512M/' /etc/php/7.4/fpm/php.ini

echo "PHP configuration updated"
echo ""

echo "========================================="
echo "Step 4: Verify Configurations"
echo "========================================="
echo "Nginx client_max_body_size:"
grep -n "client_max_body_size" /etc/nginx/nginx.conf | head -1
echo ""
echo "PHP Upload Settings:"
grep -E '^(upload_max_filesize|post_max_size|max_execution_time|max_input_time|memory_limit)' /etc/php/7.4/fpm/php.ini
echo ""

echo "========================================="
echo "Step 5: Test Nginx Configuration"
echo "========================================="
if nginx -t; then
    echo "Nginx configuration syntax is valid"
else
    echo "Nginx configuration has errors!"
    exit 1
fi
echo ""

echo "========================================="
echo "Step 6: Restart Services"
echo "========================================="
systemctl restart nginx
echo "Nginx restarted"

systemctl restart php7.4-fpm
echo "PHP-FPM restarted"
echo ""

echo "========================================="
echo "Step 7: Verify Services Status"
echo "========================================="
systemctl status nginx | grep Active
systemctl status php7.4-fpm | grep Active
echo ""

echo "========================================="
echo "ALL DONE - Configuration Complete!"
echo "========================================="
echo ""
echo "New Limits:"
echo "- Nginx: client_max_body_size = 512M"
echo "- PHP: upload_max_filesize = 512M"
echo "- PHP: post_max_size = 512M"
echo "- PHP: max_execution_time = 600s"
echo "- PHP: max_input_time = 600s"
echo "- PHP: memory_limit = 512M"
echo ""
echo "You can now upload files up to 512MB in phpMyAdmin and the application."
echo ""
'@

Write-Host "Executing configuration update on server..." -ForegroundColor Yellow
Write-Host ""

# Save commands to temp file
$tempScript = "/tmp/fix_upload_limits.sh"
$sshCommands | ssh "${serverUser}@${serverHost}" "cat > $tempScript && chmod +x $tempScript && bash $tempScript && rm $tempScript"

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "ERROR: Configuration update failed!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "SUCCESS - Upload Limits Increased!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host "1. Test phpMyAdmin at http://103.214.112.58/phpmyadmin" -ForegroundColor White
Write-Host "2. Try importing your database file" -ForegroundColor White
Write-Host "3. Files up to 512MB should now work" -ForegroundColor White
Write-Host ""
