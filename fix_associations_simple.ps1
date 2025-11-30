# Fix All Cross-Database Associations - Simple Version
Write-Host "Fixing cross-database associations..." -ForegroundColor Cyan

$files = Get-ChildItem -Path "src\Model\Table" -Filter "*Table.php" -Recurse
$count = 0

foreach ($file in $files) {
    $changed = $false
    $lines = Get-Content $file.FullName
    $newLines = @()
    
    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i]
        $newLines += $line
        
        # Check if this is a belongsTo/hasMany/hasOne line with foreignKey
        if ($line -match "foreignKey.*=>") {
            # Check if next few lines contain 'strategy'
            $hasStrategy = $false
            for ($j = $i; $j -lt [Math]::Min($i + 5, $lines.Count); $j++) {
                if ($lines[$j] -match "'strategy'") {
                    $hasStrategy = $true
                    break
                }
            }
            
            # If no strategy found, add it
            if (-not $hasStrategy) {
                $indent = "            "
                $newLines += $indent + "'strategy' => 'select',"
                $changed = $true
            }
        }
    }
    
    if ($changed) {
        $newLines | Set-Content $file.FullName
        $count++
        Write-Host "  Fixed: $($file.Name)" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Modified $count files" -ForegroundColor Yellow
Write-Host "Clearing cache..." -ForegroundColor Cyan
bin\cake cache clear_all
Write-Host "Done!" -ForegroundColor Green
