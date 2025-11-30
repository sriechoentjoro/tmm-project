#!/bin/bash
# Import All TMM Databases
# Generated: 20251124_035309
# Usage: bash import_all.sh [mysql_user] [mysql_password]

MYSQL_USER=${1:-root}
MYSQL_PASSWORD=${2:-}

if [ -z "$MYSQL_PASSWORD" ]; then
    echo "Usage: bash import_all.sh [mysql_user] [mysql_password]"
    echo "Example: bash import_all.sh root mypassword"
    exit 1
fi

echo "========================================"
echo "TMM Database Import"
echo "========================================"
echo ""
echo "Importing: cms_masters"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_masters.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_masters imported successfully"
else
    echo "  [ERROR] Failed to import cms_masters"
fi
echo ""
echo "Importing: cms_lpk_candidates"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_lpk_candidates.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_lpk_candidates imported successfully"
else
    echo "  [ERROR] Failed to import cms_lpk_candidates"
fi
echo ""
echo "Importing: cms_lpk_candidate_documents"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_lpk_candidate_documents.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_lpk_candidate_documents imported successfully"
else
    echo "  [ERROR] Failed to import cms_lpk_candidate_documents"
fi
echo ""
echo "Importing: cms_tmm_apprentices"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_tmm_apprentices.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_tmm_apprentices imported successfully"
else
    echo "  [ERROR] Failed to import cms_tmm_apprentices"
fi
echo ""
echo "Importing: cms_tmm_apprentice_documents"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_tmm_apprentice_documents.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_tmm_apprentice_documents imported successfully"
else
    echo "  [ERROR] Failed to import cms_tmm_apprentice_documents"
fi
echo ""
echo "Importing: cms_tmm_apprentice_document_ticketings"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_tmm_apprentice_document_ticketings.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_tmm_apprentice_document_ticketings imported successfully"
else
    echo "  [ERROR] Failed to import cms_tmm_apprentice_document_ticketings"
fi
echo ""
echo "Importing: cms_tmm_stakeholders"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_tmm_stakeholders.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_tmm_stakeholders imported successfully"
else
    echo "  [ERROR] Failed to import cms_tmm_stakeholders"
fi
echo ""
echo "Importing: cms_tmm_trainees"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_tmm_trainees.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_tmm_trainees imported successfully"
else
    echo "  [ERROR] Failed to import cms_tmm_trainees"
fi
echo ""
echo "Importing: cms_tmm_trainee_accountings"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_tmm_trainee_accountings.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_tmm_trainee_accountings imported successfully"
else
    echo "  [ERROR] Failed to import cms_tmm_trainee_accountings"
fi
echo ""
echo "Importing: cms_tmm_trainee_trainings"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_tmm_trainee_trainings.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_tmm_trainee_trainings imported successfully"
else
    echo "  [ERROR] Failed to import cms_tmm_trainee_trainings"
fi
echo ""
echo "Importing: cms_tmm_trainee_training_scorings"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < cms_tmm_trainee_training_scorings.sql
if [ $? -eq 0 ]; then
    echo "  [OK] cms_tmm_trainee_training_scorings imported successfully"
else
    echo "  [ERROR] Failed to import cms_tmm_trainee_training_scorings"
fi
echo ""
echo "Importing: system_authentication_authorization"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD < system_authentication_authorization.sql
if [ $? -eq 0 ]; then
    echo "  [OK] system_authentication_authorization imported successfully"
else
    echo "  [ERROR] Failed to import system_authentication_authorization"
fi
echo ""

echo "========================================"
echo "Import Complete!"
echo "========================================"