# Script untuk verifikasi database mapping dan asosiasi

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Database Association Verification" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Daftar tabel yang perlu dicek berdasarkan screenshot
$tablesToCheck = @(
    @{
        Table = 'CandidateEducations'
        File = 'src\Model\Table\CandidateEducationsTable.php'
        Associations = @('MasterStratas', 'MasterPropinsis', 'MasterKabupatens')
    }
)

foreach ($tableInfo in $tablesToCheck) {
    Write-Host "Checking: $($tableInfo.Table)" -ForegroundColor Yellow
    Write-Host "  File: $($tableInfo.File)" -ForegroundColor Gray
    
    if (Test-Path $tableInfo.File) {
        $content = Get-Content $tableInfo.File -Raw
        
        # Cek setiap asosiasi
        foreach ($assoc in $tableInfo.Associations) {
            Write-Host "  - Association: $assoc" -ForegroundColor White
            
            # Cek apakah belongsTo ada
            if ($content -match "belongsTo\('$assoc'") {
                Write-Host "    [OK] belongsTo found" -ForegroundColor Green
                
                # Cek apakah pakai strategy => select
                if ($content -match "belongsTo\('$assoc'[^\}]*'strategy'\s*=>\s*'select'") {
                    Write-Host "    [OK] 'strategy' => 'select' found" -ForegroundColor Green
                } else {
                    Write-Host "    [WARNING] 'strategy' => 'select' NOT found!" -ForegroundColor Red
                }
            } else {
                Write-Host "    [ERROR] belongsTo NOT found!" -ForegroundColor Red
            }
            
            # Cek apakah ada validation rule
            if ($content -match "existsIn\(\['[^']+'\],\s*'$assoc'\)") {
                Write-Host "    [OK] existsIn validation found" -ForegroundColor Green
            } else {
                Write-Host "    [WARNING] existsIn validation NOT found" -ForegroundColor Yellow
            }
        }
    } else {
        Write-Host "  [ERROR] File not found!" -ForegroundColor Red
    }
    
    Write-Host ""
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Database Tables Check" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# Cek apakah tabel master ada di database
$masterTables = @('master_stratas', 'master_propinsis', 'master_kabupatens', 'master_kecamatans', 'master_kelurahans')

foreach ($table in $masterTables) {
    Write-Host "Checking table: $table in cms_masters" -ForegroundColor Yellow
    
    $result = mysql -u root cms_masters -e "SHOW TABLES LIKE '$table';" 2>$null
    
    if ($result -match $table) {
        Write-Host "  [OK] Table exists" -ForegroundColor Green
        
        # Count rows
        $count = mysql -u root cms_masters -e "SELECT COUNT(*) as count FROM $table;" 2>$null | Select-String -Pattern '\d+' | Select-Object -Last 1
        if ($count) {
            Write-Host "  Rows: $count" -ForegroundColor Gray
        }
    } else {
        Write-Host "  [ERROR] Table NOT found!" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Done!" -ForegroundColor Green
