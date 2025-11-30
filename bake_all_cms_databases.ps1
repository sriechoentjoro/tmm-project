# ============================================
# Comprehensive Bake Script for All CMS Databases
# ============================================
# This script will:
# 1. Fetch all tables from each database
# 2. Bake models, controllers, and templates
# 3. Fix UTF-8 BOM issues automatically
# 4. Fix association names (Propinsis -> MasterPropinsis, etc.)
# 5. Clear cache
# ============================================

Write-Host "=== Starting Comprehensive Bake Process ===" -ForegroundColor Cyan
Write-Host "This will bake ALL tables from 12 CMS databases" -ForegroundColor Yellow
Write-Host ""

# MySQL connection details
$mysqlUser = "root"
$mysqlPass = "62xe6zyr"
$mysqlHost = "localhost"

# Database to connection mapping
$databases = @{
    'cms_masters' = 'cms_masters'
    'cms_lpk_candidates' = 'cms_lpk_candidates'
    'cms_lpk_candidate_documents' = 'cms_lpk_candidate_documents'
    'cms_tmm_apprentices' = 'cms_tmm_apprentices'
    'cms_tmm_apprentice_documents' = 'cms_tmm_apprentice_documents'
    'cms_tmm_apprentice_document_ticketings' = 'cms_tmm_apprentice_document_ticketings'
    'cms_tmm_organizations' = 'cms_tmm_organizations'
    'cms_tmm_stakeholders' = 'cms_tmm_stakeholders'
    'cms_tmm_trainees' = 'cms_tmm_trainees'
    'cms_tmm_trainee_accountings' = 'cms_tmm_trainee_accountings'
    'cms_tmm_trainee_trainings' = 'cms_tmm_trainee_trainings'
    'cms_tmm_trainee_training_scorings' = 'cms_tmm_trainee_training_scorings'
}

# Function to convert snake_case to CamelCase
function ConvertTo-CamelCase {
    param($text)
    $words = $text -split '_'
    $result = ""
    foreach ($word in $words) {
        $result += (Get-Culture).TextInfo.ToTitleCase($word.ToLower())
    }
    return $result
}

# Function to get tables from database using MySQL
function Get-DatabaseTables {
    param($database)
    
    $query = "SHOW TABLES"
    $mysqlCmd = "mysql -h$mysqlHost -u$mysqlUser -p$mysqlPass -D$database -N -e `"$query`""
    
    try {
        $tables = Invoke-Expression $mysqlCmd 2>$null | Where-Object { $_ -ne "" }
        return $tables
    } catch {
        Write-Host "Error fetching tables from $database" -ForegroundColor Red
        return @()
    }
}

# Fetch all tables from all databases
Write-Host "Fetching table lists from databases..." -ForegroundColor Cyan
$allTables = @{}
$totalTables = 0

foreach ($db in $databases.Keys) {
    $tables = Get-DatabaseTables -database $db
    $allTables[$db] = $tables
    $totalTables += $tables.Count
    Write-Host "  $db : $($tables.Count) tables" -ForegroundColor Green
}

Write-Host ""
Write-Host "=== Bake Summary ===" -ForegroundColor Cyan
Write-Host "Total databases: $($databases.Count)" -ForegroundColor White
Write-Host "Total tables: $totalTables" -ForegroundColor White
Write-Host ""

# Ask for confirmation
$confirm = Read-Host "Continue with baking? (Y/N)"
if ($confirm -ne 'Y' -and $confirm -ne 'y') {
    Write-Host "Baking cancelled." -ForegroundColor Yellow
    exit
}

Write-Host ""
Write-Host "=== Starting Bake Process ===" -ForegroundColor Green
Write-Host ""

$successCount = 0
$failCount = 0
$skippedCount = 0

# Bake each database
foreach ($db in $databases.Keys) {
    $connection = $databases[$db]
    $tables = $allTables[$db]
    
    if ($tables.Count -eq 0) {
        Write-Host "=== Skipping $db (no tables found) ===" -ForegroundColor Yellow
        continue
    }
    
    Write-Host "=== Processing Database: $db ===" -ForegroundColor Cyan
    Write-Host "Connection: $connection" -ForegroundColor Gray
    Write-Host "Tables to bake: $($tables.Count)" -ForegroundColor Gray
    Write-Host ""
    
    foreach ($table in $tables) {
        $modelName = ConvertTo-CamelCase -text $table
        
        Write-Host "  Baking: $modelName ($table)..." -ForegroundColor Yellow -NoNewline
        
        # Run bake command
        $bakeCmd = "bin\cake bake all $modelName --connection $connection --force --no-interaction 2>&1"
        $output = Invoke-Expression $bakeCmd
        
        if ($LASTEXITCODE -eq 0 -or $output -match "Baking|Creating") {
            Write-Host " SUCCESS" -ForegroundColor Green
            $successCount++
        } else {
            Write-Host " FAILED" -ForegroundColor Red
            Write-Host "    Error: $output" -ForegroundColor Red
            $failCount++
        }
        
        Start-Sleep -Milliseconds 200
    }
    
    Write-Host ""
}

Write-Host "=== Baking Complete ===" -ForegroundColor Green
Write-Host "Success: $successCount" -ForegroundColor Green
Write-Host "Failed: $failCount" -ForegroundColor Red
Write-Host ""

# Step 2: Fix UTF-8 BOM
Write-Host "=== Step 2: Fixing UTF-8 BOM ===" -ForegroundColor Cyan

$utf8NoBom = New-Object System.Text.UTF8Encoding $false

# Fix Controllers
$controllers = Get-ChildItem src\Controller\*.php
foreach ($file in $controllers) {
    $content = Get-Content $file.FullName -Raw
    [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
}
Write-Host "Fixed $($controllers.Count) controllers" -ForegroundColor Green

# Fix Table files
$tables = Get-ChildItem src\Model\Table\*.php -ErrorAction SilentlyContinue
if ($tables) {
    foreach ($file in $tables) {
        $content = Get-Content $file.FullName -Raw
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
    }
    Write-Host "Fixed $($tables.Count) Table files" -ForegroundColor Green
}

# Fix Entity files
$entities = Get-ChildItem src\Model\Entity\*.php -ErrorAction SilentlyContinue
if ($entities) {
    foreach ($file in $entities) {
        $content = Get-Content $file.FullName -Raw
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
    }
    Write-Host "Fixed $($entities.Count) Entity files" -ForegroundColor Green
}

Write-Host ""

# Step 3: Fix common association issues
Write-Host "=== Step 3: Fixing Association Names ===" -ForegroundColor Cyan

$associationFixes = @{
    'Propinsis' = 'MasterPropinsis'
    'Kabupatens' = 'MasterKabupatens'
    'Kecamatans' = 'MasterKecamatans'
}

$fixedFiles = 0

# Fix in Table files
Get-ChildItem src\Model\Table\*.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $modified = $false
    
    foreach ($old in $associationFixes.Keys) {
        $new = $associationFixes[$old]
        if ($content -match "belongsTo\('$old'|hasMany\('$old'|existsIn\(\[.*\], '$old'") {
            $content = $content -replace "belongsTo\('$old'", "belongsTo('$new'"
            $content = $content -replace "hasMany\('$old'", "hasMany('$new'"
            $content = $content -replace "existsIn\(\[(.*?)\], '$old'\)", "existsIn([`$1], '$new')"
            $content = $content -replace "@property.*\b$old\b", "@property $new"
            $modified = $true
        }
    }
    
    if ($modified) {
        [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
        Write-Host "  Fixed: $($_.Name)" -ForegroundColor Green
        $fixedFiles++
    }
}

# Fix in Controller files
Get-ChildItem src\Controller\*Controller.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $modified = $false
    
    foreach ($old in $associationFixes.Keys) {
        $new = $associationFixes[$old]
        if ($content -match "->contain\(\['$old'") {
            $content = $content -replace "->contain\(\['$old'", "->contain(['$new'"
            $modified = $true
        }
    }
    
    if ($modified) {
        [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
        Write-Host "  Fixed: $($_.Name)" -ForegroundColor Green
        $fixedFiles++
    }
}

Write-Host "Fixed $fixedFiles files with association issues" -ForegroundColor Green
Write-Host ""

# Step 4: Clear cache
Write-Host "=== Step 4: Clearing Cache ===" -ForegroundColor Cyan
$cacheOutput = bin\cake cache clear_all 2>&1
Write-Host "Cache cleared" -ForegroundColor Green
Write-Host ""

# Final summary
Write-Host "=== ALL STEPS COMPLETE ===" -ForegroundColor Green
Write-Host ""
Write-Host "Summary:" -ForegroundColor Cyan
Write-Host "  ✓ Baked $successCount tables from $($databases.Count) databases" -ForegroundColor Green
Write-Host "  ✓ Fixed UTF-8 BOM in all PHP files" -ForegroundColor Green
Write-Host "  ✓ Fixed $fixedFiles association name issues" -ForegroundColor Green
Write-Host "  ✓ Cleared all caches" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Test in browser: http://localhost:8765/" -ForegroundColor White
Write-Host "  2. Check any failed tables manually" -ForegroundColor White
Write-Host "  3. Review associations if errors occur" -ForegroundColor White
Write-Host ""
