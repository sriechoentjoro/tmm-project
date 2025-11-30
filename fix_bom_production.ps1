# Fix BOM (Byte Order Mark) on Production Server
# Removes UTF-8 BOM from PHP files to prevent "Namespace declaration" errors

$ErrorActionPreference = "Stop"

$serverUser = "root"
$serverHost = "103.214.112.58"
$serverPath = "/var/www/tmm"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Fix BOM on Production Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Creating BOM fix script on server..." -ForegroundColor Yellow

$fixBomScript = @'
#!/bin/bash
echo "=== BOM Removal Script for Production ==="
echo "Target: /var/www/tmm"
echo ""

fixed=0
checked=0

echo "Scanning PHP files..."
find /var/www/tmm/src -name "*.php" -type f | while read file; do
    checked=$((checked + 1))
    
    # Check for UTF-8 BOM (EF BB BF)
    if [ "$(head -c 3 "$file" | xxd -p)" = "efbbbf" ]; then
        echo "[FIXING] $file"
        
        # Remove BOM using sed
        sed -i '1s/^\xEF\xBB\xBF//' "$file"
        
        fixed=$((fixed + 1))
    fi
done

echo ""
echo "=== Summary ==="
echo "Files checked: $checked"
echo "Files fixed: $fixed"
echo ""
echo "Clearing CakePHP cache..."
cd /var/www/tmm
rm -rf tmp/cache/models/*
rm -rf tmp/cache/persistent/*
rm -rf tmp/cache/views/*

echo "Done!"
'@

Write-Host "Uploading and executing script..." -ForegroundColor Yellow
Write-Host ""

$fixBomScript | ssh "${serverUser}@${serverHost}" "cat > /tmp/fix_bom.sh && chmod +x /tmp/fix_bom.sh && bash /tmp/fix_bom.sh && rm /tmp/fix_bom.sh"

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "BOM Fix Complete!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Cyan
    Write-Host "1. Test the application: http://103.214.112.58/tmm/" -ForegroundColor White
    Write-Host "2. Check error logs if issues persist" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "ERROR: BOM fix failed!" -ForegroundColor Red
    exit 1
}
