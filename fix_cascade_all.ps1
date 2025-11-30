# Fix Cascade Dropdowns for Apprentices
# Update controller to show only stored values in edit mode

Write-Host "Fixing ApprenticesController..." -ForegroundColor Cyan

$controllerFile = "src\Controller\ApprenticesController.php"
$content = Get-Content $controllerFile -Raw

# Replace the edit() method's dropdown loading logic
$oldPattern = @'
        \$masterPropinsis = \$this->Apprentices->MasterPropinsis->find\('list', \['limit' => 200\]\);
        \$masterKabupatens = \$this->Apprentices->MasterKabupatens->find\('list', \['limit' => 200\]\);
        \$masterKecamatans = \$this->Apprentices->MasterKecamatans->find\('list', \['limit' => 200\]\);
        \$masterKelurahans = \$this->Apprentices->MasterKelurahans->find\('list', \['limit' => 200\]\);
        \$bloodTypes
'@

$newPattern = @'
        $masterPropinsis = $this->Apprentices->MasterPropinsis->find('list', ['limit' => 200]);
        
        // For edit mode: Only show the currently selected values
        if (!empty($apprentice->master_kabupaten_id)) {
            $masterKabupatens = $this->Apprentices->MasterKabupatens->find('list')
                ->where(['id' => $apprentice->master_kabupaten_id])->toArray();
        } else {
            $masterKabupatens = [];
        }
        
        if (!empty($apprentice->master_kecamatan_id)) {
            $masterKecamatans = $this->Apprentices->MasterKecamatans->find('list')
                ->where(['id' => $apprentice->master_kecamatan_id])->toArray();
        } else {
            $masterKecamatans = [];
        }
        
        if (!empty($apprentice->master_kelurahan_id)) {
            $masterKelurahans = $this->Apprentices->MasterKelurahans->find('list')
                ->where(['id' => $apprentice->master_kelurahan_id])->toArray();
        } else {
            $masterKelurahans = [];
        }
        
        // Cascade data for JavaScript
        $masterKabupatensData = $this->Apprentices->MasterKabupatens->find('all')->select(['id', 'title', 'propinsi_id'])->toArray();
        $masterKecamatansData = $this->Apprentices->MasterKecamatans->find('all')->select(['id', 'title', 'kabupaten_id'])->toArray();
        $masterKelurahansData = $this->Apprentices->MasterKelurahans->find('all')->select(['id', 'title', 'kecamatan_id'])->toArray();
        
        $bloodTypes
'@

if ($content -match $oldPattern) {
    $content = $content -replace [regex]::Escape($oldPattern), $newPattern
    Set-Content $controllerFile -Value $content -NoNewline
    Write-Host "✓ ApprenticesController updated!" -ForegroundColor Green
} else {
    Write-Host "✗ Pattern not found in ApprenticesController" -ForegroundColor Yellow
}

Write-Host "`nClearing cache..." -ForegroundColor Cyan
& .\bin\cake.bat cache clear_all

Write-Host "`n✓ Done! Test the edit pages now." -ForegroundColor Green
