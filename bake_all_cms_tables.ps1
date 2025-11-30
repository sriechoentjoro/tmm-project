# Bake All CMS Tables Script
# This script bakes all tables from all CMS databases

$ErrorActionPreference = "Continue"
$projectRoot = "d:\xampp\htdocs\project_tmm"
Set-Location $projectRoot

Write-Host "=== Starting Comprehensive Bake Process ===" -ForegroundColor Cyan
Write-Host "This will bake all tables from 12 CMS databases" -ForegroundColor Yellow
Write-Host ""

# Database to Connection mapping
$databases = @{
    'cms_masters' = @(
        'MasterBloodTypes',
        'MasterDepartments',
        'MasterDocumentPreparednessStatuses',
        'MasterDocumentSubmissionStatuses',
        'MasterEmployeeStatuses',
        'MasterEmploymentPositions',
        'MasterFamilyConnections',
        'MasterGenders',
        'MasterIcons',
        'MasterJobCategories',
        'MasterKabupatens',
        'MasterKecamatans',
        'MasterKelurahans',
        'MasterMarriageStatuses',
        'MasterMedicalCheckUpResults',
        'MasterOccupationCategories',
        'MasterOccupations',
        'MasterPropinsis',
        'MasterRejectedReasons',
        'MasterReligions',
        'MasterStratas',
        'MasterTranslations',
        'Sessions'
    )
    'cms_lpk_candidates' = @()  # Will query dynamically
    'cms_lpk_candidate_documents' = @()
    'cms_tmm_apprentices' = @()
    'cms_tmm_apprentice_documents' = @()
    'cms_tmm_apprentice_document_ticketings' = @()
    'cms_tmm_organizations' = @()
    'cms_tmm_stakeholders' = @()
    'cms_tmm_trainees' = @()
    'cms_tmm_trainee_accountings' = @()
    'cms_tmm_trainee_trainings' = @()
    'cms_tmm_trainee_training_scorings' = @()
}

# Function to convert table name to CamelCase
function Convert-ToCamelCase {
    param([string]$tableName)
    
    $parts = $tableName -split '_'
    $result = ""
    foreach ($part in $parts) {
        if ($part.Length -gt 0) {
            $result += $part.Substring(0,1).ToUpper() + $part.Substring(1).ToLower()
        }
    }
    return $result
}

# Get tables dynamically for each database
Write-Host "Fetching table lists from databases..." -ForegroundColor Cyan
foreach ($db in $databases.Keys) {
    if ($databases[$db].Count -eq 0) {
        $query = "SELECT table_name FROM information_schema.tables WHERE table_schema='$db' AND table_type='BASE TABLE' ORDER BY table_name;"
        $tables = mysql -u root -p62xe6zyr -N -e $query
        
        $tableList = @()
        foreach ($table in $tables) {
            $camelCase = Convert-ToCamelCase $table
            $tableList += $camelCase
        }
        $databases[$db] = $tableList
    }
}

# Display summary
Write-Host ""
Write-Host "=== Bake Summary ===" -ForegroundColor Green
$totalTables = 0
foreach ($db in $databases.Keys | Sort-Object) {
    $count = $databases[$db].Count
    $totalTables += $count
    Write-Host "  $db : $count tables" -ForegroundColor Gray
}
Write-Host "  TOTAL: $totalTables tables" -ForegroundColor Yellow
Write-Host ""

$continue = Read-Host "Continue with baking? (Y/N)"
if ($continue -ne 'Y' -and $continue -ne 'y') {
    Write-Host "Bake cancelled by user" -ForegroundColor Red
    exit
}

# Bake all tables
$successCount = 0
$errorCount = 0
$skippedCount = 0

foreach ($db in $databases.Keys | Sort-Object) {
    $connection = $db
    Write-Host ""
    Write-Host "=== Processing Database: $db ===" -ForegroundColor Cyan
    Write-Host "Connection: $connection" -ForegroundColor Gray
    Write-Host ""
    
    foreach ($table in $databases[$db]) {
        Write-Host "  Baking: $table..." -NoNewline
        
        # Run bake command
        $output = & bin\cake bake all $table --connection $connection --force 2>&1
        
        if ($LASTEXITCODE -eq 0 -or $output -like "*Bake All complete*") {
            Write-Host " SUCCESS" -ForegroundColor Green
            $successCount++
        } else {
            Write-Host " ERROR" -ForegroundColor Red
            Write-Host "    Error details: $output" -ForegroundColor DarkRed
            $errorCount++
        }
    }
}

Write-Host ""
Write-Host "=== Post-Bake Cleanup ===" -ForegroundColor Cyan

# Fix BOM issues
Write-Host "Fixing UTF-8 BOM..." -NoNewline
$controllers = Get-ChildItem "$projectRoot\src\Controller\*.php"
foreach ($file in $controllers) {
    $content = Get-Content $file.FullName -Raw
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
}

$tables = @(Get-ChildItem "$projectRoot\src\Model\Table\*.php" -ErrorAction SilentlyContinue)
foreach ($file in $tables) {
    $content = Get-Content $file.FullName -Raw
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
}

$entities = @(Get-ChildItem "$projectRoot\src\Model\Entity\*.php" -ErrorAction SilentlyContinue)
foreach ($file in $entities) {
    $content = Get-Content $file.FullName -Raw
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
}
Write-Host " DONE" -ForegroundColor Green

# Clear cache
Write-Host "Clearing cache..." -NoNewline
& bin\cake cache clear_all | Out-Null
Write-Host " DONE" -ForegroundColor Green

Write-Host ""
Write-Host "=== Bake Complete ===" -ForegroundColor Green
Write-Host "  Success: $successCount" -ForegroundColor Green
Write-Host "  Errors:  $errorCount" -ForegroundColor Red
Write-Host "  Total:   $totalTables" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "  1. Review any errors above"
Write-Host "  2. Check controller files for proper associations"
Write-Host "  3. Test in browser: http://localhost:8765/"
Write-Host ""
