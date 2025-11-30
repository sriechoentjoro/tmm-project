@echo off
echo ========================================
echo  Test Akses Project TMM
echo ========================================
echo.

echo [1] Test akses localhost/project_tmm/
curl -I http://localhost/project_tmm/ 2>nul | findstr "HTTP Location"
echo.

echo [2] Test akses localhost/project_tmm/webroot/
curl -I http://localhost/project_tmm/webroot/ 2>nul | findstr "HTTP"
echo.

echo ========================================
echo  Instruksi:
echo ========================================
echo.
echo Buka browser dan coba salah satu URL:
echo.
echo 1. http://localhost/project_tmm/
echo    (akan otomatis redirect ke webroot)
echo.
echo 2. http://localhost/project_tmm/webroot/
echo    (akses langsung ke aplikasi)
echo.
echo ========================================
echo.
echo Jika masih menampilkan directory listing:
echo 1. Tekan Ctrl+F5 di browser (hard refresh)
echo 2. Clear browser cache
echo 3. Atau tutup dan buka browser lagi
echo.
pause
