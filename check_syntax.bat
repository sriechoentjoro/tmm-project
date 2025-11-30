@echo off
echo ========================================
echo  PHP Syntax Check - All Model Files
echo ========================================
echo.

set ERROR_COUNT=0

for %%f in (src\Model\Table\*.php) do (
    php -l "%%f" > nul 2>&1
    if errorlevel 1 (
        echo [ERROR] %%f
        php -l "%%f"
        set /a ERROR_COUNT+=1
    ) else (
        echo [OK] %%~nxf
    )
)

echo.
echo ========================================
echo Total errors: %ERROR_COUNT%
echo ========================================

if %ERROR_COUNT% == 0 (
    echo All files OK!
) else (
    echo Found %ERROR_COUNT% files with syntax errors!
)

pause
