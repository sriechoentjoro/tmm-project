# Simple Bake All Script - No user interaction required
Write-Host "=== Baking All CMS Databases ===" -ForegroundColor Cyan
Write-Host ""

$mysqlUser = "root"
$mysqlPass = "62xe6zyr"
$mysqlHost = "localhost"

# Database list
$databases = @(
    'cms_masters',
    'cms_lpk_candidates',
    'cms_lpk_candidate_documents',
    'cms_tmm_apprentices',
    'cms_tmm_apprentice_documents',
    'cms_tmm_apprentice_document_ticketings',
    'cms_tmm_organizations',
    'cms_tmm_stakeholders',
    'cms_tmm_trainees',
    'cms_tmm_trainee_accountings',
    'cms_tmm_trainee_trainings',
    'cms_tmm_trainee_training_scorings'
)

function ConvertTo-CamelCase {
    param($text)
    $words = $text -split '_'
    $result = ""
    foreach ($word in $words) {
        $result += (Get-Culture).TextInfo.ToTitleCase($word.ToLower())
    }
    return $result
}

function Get-DatabaseTables {
    param($database)
    $query = "SHOW TABLES"
    $mysqlCmd = "mysql -h$mysqlHost -u$mysqlUser -p$mysqlPass -D$database -N -e `"$query`" 2>nul"
    try {
        $tables = Invoke-Expression $mysqlCmd | Where-Object { $_ -ne "" }
        return $tables
    } catch {
        return @()
    }
}

$successCount = 0
$failCount = 0

foreach ($db in $databases) {
    Write-Host "=== Database: $db ===" -ForegroundColor Cyan
    
    $tables = Get-DatabaseTables -database $db
    if ($tables.Count -eq 0) {
        Write-Host "  No tables found, skipping..." -ForegroundColor Yellow
        continue
    }
    
    Write-Host "  Found $($tables.Count) tables" -ForegroundColor Green
    
    foreach ($table in $tables) {
        $modelName = ConvertTo-CamelCase -text $table
        Write-Host "  Baking $modelName..." -ForegroundColor Yellow -NoNewline
        
        # Use echo to auto-answer prompts
        $bakeCmd = "echo y | bin\cake bake all $modelName --connection $db --force --quiet 2>&1"
        $output = Invoke-Expression $bakeCmd
        
        if ($output -match "Baking|Creating|Table class") {
            Write-Host " OK" -ForegroundColor Green
            $successCount++
        } else {
            Write-Host " SKIP" -ForegroundColor Yellow
        }
    }
    Write-Host ""
}

Write-Host "=== Fixing UTF-8 BOM ===" -ForegroundColor Cyan
$utf8NoBom = New-Object System.Text.UTF8Encoding $false

Get-ChildItem src\Controller\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
}

Get-ChildItem src\Model\Table\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
}

Get-ChildItem src\Model\Entity\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
}

Write-Host "BOM fixed for all files" -ForegroundColor Green
Write-Host ""

Write-Host "=== Fixing Associations ===" -ForegroundColor Cyan
$fixedCount = 0

Get-ChildItem src\Model\Table\*.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $modified = $false
    
    if ($content -match "belongsTo\('Propinsis'|existsIn\(\[.*\], 'Propinsis'\)") {
        $content = $content -replace "belongsTo\('Propinsis'", "belongsTo('MasterPropinsis'"
        $content = $content -replace "existsIn\(\[(.*?)\], 'Propinsis'\)", "existsIn([`$1], 'MasterPropinsis')"
        $modified = $true
    }
    
    if ($content -match "belongsTo\('Kabupatens'|existsIn\(\[.*\], 'Kabupatens'\)") {
        $content = $content -replace "belongsTo\('Kabupatens'", "belongsTo('MasterKabupatens'"
        $content = $content -replace "existsIn\(\[(.*?)\], 'Kabupatens'\)", "existsIn([`$1], 'MasterKabupatens')"
        $modified = $true
    }
    
    if ($content -match "belongsTo\('Kecamatans'|existsIn\(\[.*\], 'Kecamatans'\)") {
        $content = $content -replace "belongsTo\('Kecamatans'", "belongsTo('MasterKecamatans'"
        $content = $content -replace "existsIn\(\[(.*?)\], 'Kecamatans'\)", "existsIn([`$1], 'MasterKecamatans')"
        $modified = $true
    }
    
    if ($modified) {
        [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
        $fixedCount++
    }
}

Get-ChildItem src\Controller\*Controller.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $modified = $false
    
    if ($content -match "->contain\(\['Propinsis'") {
        $content = $content -replace "->contain\(\['Propinsis'", "->contain(['MasterPropinsis'"
        $modified = $true
    }
    
    if ($content -match "->contain\(\['Kabupatens'") {
        $content = $content -replace "->contain\(\['Kabupatens'", "->contain(['MasterKabupatens'"
        $modified = $true
    }
    
    if ($content -match "->contain\(\['Kecamatans'") {
        $content = $content -replace "->contain\(\['Kecamatans'", "->contain(['MasterKecamatans'"
        $modified = $true
    }
    
    if ($modified) {
        [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
        $fixedCount++
    }
}

Write-Host "Fixed $fixedCount files" -ForegroundColor Green
Write-Host ""

Write-Host "=== Clearing Cache ===" -ForegroundColor Cyan
bin\cake cache clear_all 2>&1 | Out-Null
Write-Host "Cache cleared" -ForegroundColor Green
Write-Host ""

Write-Host "=== COMPLETE ===" -ForegroundColor Green
Write-Host "Successfully baked $successCount tables" -ForegroundColor Green
Write-Host "Test at: http://localhost:8765/" -ForegroundColor Yellow
