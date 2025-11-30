# Convert ApprenticeOrders Apprentices Tab to AJAX Lazy Loading

$viewFile = "src\Template\ApprenticeOrders\view.ctp"

Write-Host "Converting ApprenticeOrders view to AJAX tabs..." -ForegroundColor Cyan

# Read file
$content = Get-Content $viewFile -Raw

# Find and replace the apprentices tab content
# Pattern: From <div id="tab-apprentices" to next </div> closing the tab
$pattern = '(?s)(<div id="tab-apprentices" class="view-tab-pane">).*?(?=\s*</div>\s*<!-- End Tabs Container -->)'

$replacement = @'
<div id="tab-apprentices" class="view-tab-pane">
                <div id="apprentices-pane">
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Apprentices') ?>
                        </h3>
                    </div>
                    <div class="github-details-body">
                        <?= $this->element('related_records_table', [
                            'tabId' => 'apprentices',
                            'title' => __('Apprentices'),
                            'filterField' => 'apprenticeship_order_id',
                            'filterValue' => $apprenticeOrder->id,
                            'ajaxUrl' => $this->Url->build(['controller' => 'ApprenticeOrders', 'action' => 'getRelated']),
                            'controller' => 'apprentices',
                            'columns' => [
                                ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                                ['name' => 'tmm_code', 'label' => 'TMM Code', 'type' => 'text', 'sortable' => true],
                                ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'sortable' => true],
                                ['name' => 'identity_number', 'label' => 'Identity Number', 'type' => 'text', 'sortable' => true],
                                ['name' => 'birth_date', 'label' => 'Birth Date', 'type' => 'date', 'sortable' => true],
                                ['name' => 'image_photo', 'label' => 'Photo', 'type' => 'image', 'sortable' => false]
                            ]
                        ]) ?>
                    </div>
                </div>
                </div>
            </div>

        </div>
'@

if ($content -match $pattern) {
    $content = $content -replace $pattern, $replacement
    
    # Save without BOM
    [System.IO.File]::WriteAllText($viewFile, $content, (New-Object System.Text.UTF8Encoding $false))
    
    Write-Host "✅ Successfully converted ApprenticeOrders view!" -ForegroundColor Green
    Write-Host "   - Replaced Apprentices tab with AJAX element" -ForegroundColor White
} else {
    Write-Host "❌ Pattern not found - manual edit required" -ForegroundColor Red
}

Write-Host ""
Write-Host "Next: Clear cache with: bin\cake cache clear_all" -ForegroundColor Yellow
