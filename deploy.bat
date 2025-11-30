@echo off
echo Uploading project files to root@103.214.112.58:/var/www/tmm...
echo You may be prompted for the server password.

scp -r * root@103.214.112.58:/var/www/tmm

echo.
echo Upload complete.
pause
