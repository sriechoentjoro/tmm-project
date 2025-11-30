# Script untuk memperbaiki konfigurasi Apache XAMPP

Write-Host "=== Perbaikan Konfigurasi Apache XAMPP ===" -ForegroundColor Cyan
Write-Host ""

$xamppPath = "D:\xampp"
$httpdConfPath = "$xamppPath\apache\conf\httpd.conf"

if (-not (Test-Path $httpdConfPath)) {
    Write-Host "ERROR: File httpd.conf tidak ditemukan di: $httpdConfPath" -ForegroundColor Red
    Write-Host "Pastikan XAMPP terinstall di C:\xampp" -ForegroundColor Yellow
    exit
}

Write-Host "1. Membaca file httpd.conf..." -ForegroundColor Yellow
$content = Get-Content $httpdConfPath -Raw

$modified = $false

# Cek dan aktifkan mod_rewrite
if ($content -match '#LoadModule rewrite_module modules/mod_rewrite.so') {
    Write-Host "   - Mengaktifkan mod_rewrite..." -ForegroundColor Green
    $content = $content -replace '#LoadModule rewrite_module modules/mod_rewrite.so', 'LoadModule rewrite_module modules/mod_rewrite.so'
    $modified = $true
} else {
    Write-Host "   - mod_rewrite sudah aktif" -ForegroundColor Gray
}

# Cek AllowOverride
if ($content -match 'AllowOverride None') {
    Write-Host "   - Mengubah AllowOverride None menjadi All..." -ForegroundColor Green
    $content = $content -replace 'AllowOverride None', 'AllowOverride All'
    $modified = $true
} else {
    Write-Host "   - AllowOverride sudah diset" -ForegroundColor Gray
}

if ($modified) {
    Write-Host ""
    Write-Host "2. Menyimpan perubahan ke httpd.conf..." -ForegroundColor Yellow
    
    # Backup file asli
    $backupPath = "$httpdConfPath.backup_" + (Get-Date -Format "yyyyMMdd_HHmmss")
    Copy-Item $httpdConfPath $backupPath
    Write-Host "   - Backup dibuat: $backupPath" -ForegroundColor Gray
    
    # Simpan perubahan
    Set-Content $httpdConfPath -Value $content -NoNewline
    Write-Host "   - Konfigurasi disimpan!" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "3. Restart Apache diperlukan!" -ForegroundColor Yellow
    Write-Host "   Buka XAMPP Control Panel dan:" -ForegroundColor White
    Write-Host "   1. Stop Apache" -ForegroundColor White
    Write-Host "   2. Start Apache" -ForegroundColor White
} else {
    Write-Host ""
    Write-Host "Tidak ada perubahan yang diperlukan." -ForegroundColor Green
}

Write-Host ""
Write-Host "=== Cara Akses Aplikasi ===" -ForegroundColor Cyan
Write-Host "Setelah Apache restart, buka browser:" -ForegroundColor White
Write-Host "http://localhost/project_tmm/" -ForegroundColor Yellow
Write-Host ""
Write-Host "Atau langsung ke webroot:" -ForegroundColor White
Write-Host "http://localhost/project_tmm/webroot/" -ForegroundColor Yellow
