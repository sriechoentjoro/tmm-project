@echo off
REM Auto-install Purchase Receipts System
REM Run this script to execute all SQL migrations

echo ========================================
echo Purchase Receipts System Installation
echo ========================================
echo.

REM Set MySQL path
SET MYSQL_PATH=d:\xampp\mysql\bin\mysql.exe
SET DB_NAME=asahi_inventories
SET DB_USER=root
SET DB_PASS=

echo Step 1: Creating tables...
"%MYSQL_PATH%" -u %DB_USER% %DB_NAME% < quick_import.sql
if %errorlevel% neq 0 (
    echo ERROR: Failed to create tables
    pause
    exit /b 1
)
echo SUCCESS: Tables created
echo.

echo Step 2: Adding navigation menus...
"%MYSQL_PATH%" -u %DB_USER% %DB_NAME% < add_purchase_receipts_menu.sql
if %errorlevel% neq 0 (
    echo ERROR: Failed to add menus
    pause
    exit /b 1
)
echo SUCCESS: Menus added
echo.

echo ========================================
echo Installation completed successfully!
echo ========================================
echo.
echo Next steps:
echo 1. Refresh your browser
echo 2. Check "Accounting" menu in navigation
echo 3. Start using Purchase Receipts system
echo.

pause
