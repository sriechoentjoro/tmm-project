@echo off
REM Import All TMM Databases (Windows)
REM Generated: 20251124_035250
REM Usage: import_all.bat [mysql_user] [mysql_password]

SET MYSQL_USER=%1
SET MYSQL_PASSWORD=%2

IF "%MYSQL_USER%"=="" SET MYSQL_USER=root

IF "%MYSQL_PASSWORD%"=="" (
    echo Usage: import_all.bat [mysql_user] [mysql_password]
    echo Example: import_all.bat root mypassword
    exit /b 1
)

echo ========================================
echo TMM Database Import
echo ========================================
echo.
echo Importing: cms_masters
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_masters.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_masters imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_masters
)
echo.
echo Importing: cms_lpk_candidates
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_lpk_candidates.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_lpk_candidates imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_lpk_candidates
)
echo.
echo Importing: cms_lpk_candidate_documents
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_lpk_candidate_documents.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_lpk_candidate_documents imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_lpk_candidate_documents
)
echo.
echo Importing: cms_tmm_apprentices
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_tmm_apprentices.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_tmm_apprentices imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_tmm_apprentices
)
echo.
echo Importing: cms_tmm_apprentice_documents
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_tmm_apprentice_documents.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_tmm_apprentice_documents imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_tmm_apprentice_documents
)
echo.
echo Importing: cms_tmm_apprentice_document_ticketings
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_tmm_apprentice_document_ticketings.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_tmm_apprentice_document_ticketings imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_tmm_apprentice_document_ticketings
)
echo.
echo Importing: cms_tmm_stakeholders
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_tmm_stakeholders.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_tmm_stakeholders imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_tmm_stakeholders
)
echo.
echo Importing: cms_tmm_trainees
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_tmm_trainees.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_tmm_trainees imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_tmm_trainees
)
echo.
echo Importing: cms_tmm_trainee_accountings
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_tmm_trainee_accountings.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_tmm_trainee_accountings imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_tmm_trainee_accountings
)
echo.
echo Importing: cms_tmm_trainee_trainings
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_tmm_trainee_trainings.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_tmm_trainee_trainings imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_tmm_trainee_trainings
)
echo.
echo Importing: cms_tmm_trainee_training_scorings
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < cms_tmm_trainee_training_scorings.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] cms_tmm_trainee_training_scorings imported successfully
) ELSE (
    echo   [ERROR] Failed to import cms_tmm_trainee_training_scorings
)
echo.
echo Importing: system_authentication_authorization
mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% < system_authentication_authorization.sql
IF %ERRORLEVEL% EQU 0 (
    echo   [OK] system_authentication_authorization imported successfully
) ELSE (
    echo   [ERROR] Failed to import system_authentication_authorization
)
echo.

echo ========================================
echo Import Complete!
echo ========================================
pause