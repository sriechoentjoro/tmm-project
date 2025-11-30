#!/bin/bash
# Update TMM config for https://asahifamily.id/tmm

FILE="/var/www/tmm/config/app.php"

# Backup
cp "$FILE" "$FILE.backup-$(date +%Y%m%d-%H%M%S)"

# Update fullBaseUrl
sed -i "s/'fullBaseUrl' => false,/'fullBaseUrl' => 'https:\/\/asahifamily.id\/tmm',/" "$FILE"

echo "Updated fullBaseUrl in $FILE"
grep -A1 -B1 fullBaseUrl "$FILE" | head -5
