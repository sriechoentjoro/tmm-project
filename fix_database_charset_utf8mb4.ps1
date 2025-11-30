# Fix Database Charset to UTF8MB4
# Converts all tables in all databases to utf8mb4

$databases = @(
    'cms_masters',
    'cms_lpk_candidates',
    'cms_lpk_candidate_documents',
    'cms_tmm_apprentices',
    'cms_tmm_apprentice_documents',
    'cms_tmm_apprentice_document_ticketings',
    'cms_tmm_stakeholders',
    'cms_tmm_trainees',
    'cms_tmm_trainee_accountings',
    'cms_tmm_trainee_trainings',
    'cms_tmm_trainee_training_scorings',
    'cms_authentication_authorization'
)

$mysqlUser = 'root'
$mysqlPass = '62xe6zyr'

Write-Host "=== Converting All Databases to UTF8MB4 ===" -ForegroundColor Cyan

foreach ($db in $databases) {
    Write-Host "`nProcessing database: $db" -ForegroundColor Yellow
    
    # Get all tables in the database
    $tables = mysql -u $mysqlUser --password=$mysqlPass $db -e "SHOW TABLES;" --skip-column-names
    
    if ($tables) {
        foreach ($table in $tables) {
            Write-Host "  Converting table: $table" -ForegroundColor Gray
            
            # Convert table to utf8mb4
            $sql = "ALTER TABLE ``$table`` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
            mysql -u $mysqlUser --password=$mysqlPass $db -e $sql 2>&1 | Out-Null
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host "    [OK] $table converted successfully" -ForegroundColor Green
            } else {
                Write-Host "    [ERROR] Error converting $table" -ForegroundColor Red
            }
        }
    }
    
    # Set database default charset
    Write-Host "  Setting database default charset..." -ForegroundColor Gray
    $sql = "ALTER DATABASE ``$db`` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;"
    mysql -u $mysqlUser --password=$mysqlPass -e $sql 2>&1 | Out-Null
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "  [OK] Database $db default charset updated" -ForegroundColor Green
    }
}

Write-Host "`n=== Conversion Complete ===" -ForegroundColor Cyan
Write-Host "Note: Existing corrupted data (???) cannot be recovered." -ForegroundColor Yellow
Write-Host "You will need to re-enter Japanese text for existing records." -ForegroundColor Yellow
