#!/bin/bash
# Fix TMM permissions on production

cd /var/www/tmm

echo "Fixing tmp directory permissions..."
chmod -R 777 tmp
find tmp -type d -exec chmod 777 {} \;
find tmp -type f -exec chmod 666 {} \;

echo "Fixing logs directory permissions..."
chmod -R 777 logs
find logs -type d -exec chmod 777 {} \;
find logs -type f -exec chmod 666 {} \;

echo "Setting ownership to www-data..."
chown -R www-data:www-data tmp logs

echo "Permissions fixed!"
ls -la tmp/ | head -10
ls -la logs/ | head -5
