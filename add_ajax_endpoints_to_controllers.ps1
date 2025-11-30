# ============================================
# ADD AJAX ENDPOINT TO CONTROLLERS
# ============================================
# Purpose: Add getRelated() method to controllers
# for AJAX lazy-loading of related records
# ============================================

$rootPath = "D:\xampp\htdocs\project_tmm"
$controllersPath = "$rootPath\src\Controller"
$logFile = "$rootPath\add_ajax_endpoints_log.txt"

"Add AJAX Endpoints Script - Started: $(Get-Date)" | Out-File $logFile

# List of controllers that got new tabs
$controllersToUpdate = @(
    'AcceptanceOrganizationsController',
    'ApprenticeOrdersController',
    'ApprenticesController',
    'CooperativeAssociationsController',
    'MasterAuthorizationRolesController',
    'MasterCandidateInterviewResultsController',
    'MasterCandidateInterviewTypesController',
    'MasterJapanPrefecturesController',
    'TraineesController',
    'UsersController',
    'VocationalTrainingInstitutionsController'
)

# AJAX method template
$ajaxMethodTemplate = @'

    /**
     * AJAX method to get related records with filtering and pagination
     * Used by related_records_table element for lazy loading
     */
    public function getRelated()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json')
            ->withCharset('UTF-8');
        
        try {
            // Get filter parameters
            $filterField = $this->request->getQuery('filter_field');
            $filterValue = $this->request->getQuery('filter_value');
            $page = (int)$this->request->getQuery('page', 1);
            $limit = min((int)$this->request->getQuery('limit', 50), 100);
            $filtersJson = $this->request->getQuery('filters');
            
            // Parse column filters
            $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
            
            // Build query with primary filter
            $query = $this->TABLENAME->find()
                ->where([$filterField => $filterValue])
                ->limit($limit)
                ->offset(($page - 1) * $limit);
            
            // Apply column filters
            if ($columnFilters && is_array($columnFilters)) {
                foreach ($columnFilters as $column => $filter) {
                    if (empty($filter['value'])) continue;
                    
                    $operator = isset($filter['operator']) ? $filter['operator'] : 'contains';
                    $value = $filter['value'];
                    
                    switch ($operator) {
                        case 'equals':
                            $query->where([$column => $value]);
                            break;
                        case 'contains':
                            $query->where([$column . ' LIKE' => '%' . $value . '%']);
                            break;
                        case 'starts_with':
                            $query->where([$column . ' LIKE' => $value . '%']);
                            break;
                        case 'ends_with':
                            $query->where([$column . ' LIKE' => '%' . $value]);
                            break;
                        case 'greater_than':
                            $query->where([$column . ' >' => $value]);
                            break;
                        case 'less_than':
                            $query->where([$column . ' <' => $value]);
                            break;
                        case 'not_empty':
                            $query->where([$column . ' IS NOT' => null]);
                            break;
                    }
                }
            }
            
            // Get total count for pagination
            $total = $query->count();
            
            // Execute query
            $results = $query->toArray();
            
            // Check file existence for image/photo/file fields
            foreach ($results as $result) {
                foreach ($result->toArray() as $field => $value) {
                    if (preg_match('/(image|photo|file|document)/i', $field) && !empty($value)) {
                        $fullPath = WWW_ROOT . $value;
                        $result->{$field . '_exists'} = file_exists($fullPath);
                    }
                }
            }
            
            // Build response
            $response = [
                'success' => true,
                'data' => $results,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];
            
            return $this->response->withStringBody(json_encode($response));
            
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
            return $this->response->withStringBody(json_encode($response));
        }
    }
'@

# Function to add AJAX method to controller
function Add-AjaxMethod($controllerFile, $tableName) {
    $content = Get-Content $controllerFile -Raw -ErrorAction SilentlyContinue
    
    if (-not $content) {
        return $false
    }
    
    # Check if method already exists
    if ($content -match 'public function getRelated') {
        return $false
    }
    
    # Replace TABLENAME placeholder with actual table name
    $method = $ajaxMethodTemplate -replace 'TABLENAME', $tableName
    
    # Find the last closing brace of the class
    $lastBrace = $content.LastIndexOf('}')
    if ($lastBrace -eq -1) {
        return $false
    }
    
    # Insert method before the last closing brace
    $newContent = $content.Substring(0, $lastBrace) + $method + "`n" + $content.Substring($lastBrace)
    
    # Write back to file
    try {
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($controllerFile, $newContent, $utf8NoBom)
        return $true
    }
    catch {
        return $false
    }
}

# Main execution
Write-Host "Adding AJAX endpoints to controllers..." -ForegroundColor Cyan
Write-Host ""

$totalControllers = 0
$updatedControllers = 0
$skippedControllers = 0

foreach ($controller in $controllersToUpdate) {
    $totalControllers++
    
    $controllerFile = "$controllersPath\$controller.php"
    
    if (-not (Test-Path $controllerFile)) {
        Write-Host "SKIP: File not found - $controller" -ForegroundColor Yellow
        "SKIP: File not found - $controller" | Out-File $logFile -Append
        $skippedControllers++
        continue
    }
    
    # Extract table name from controller name
    $tableName = $controller -replace 'Controller$', ''
    
    Write-Host "Processing: $controller" -ForegroundColor Yellow
    "Processing: $controllerFile" | Out-File $logFile -Append
    
    $updated = Add-AjaxMethod $controllerFile $tableName
    
    if ($updated) {
        Write-Host "  SUCCESS: Added getRelated() method" -ForegroundColor Green
        "  SUCCESS: Added getRelated() method to $controller" | Out-File $logFile -Append
        $updatedControllers++
    } else {
        Write-Host "  SKIP: Method already exists or error occurred" -ForegroundColor Gray
        "  SKIP: Method already exists in $controller" | Out-File $logFile -Append
        $skippedControllers++
    }
}

Write-Host ""
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host "UPDATE COMPLETE" -ForegroundColor Green
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host "Total controllers: $totalControllers" -ForegroundColor White
Write-Host "Controllers updated: $updatedControllers" -ForegroundColor Green
Write-Host "Controllers skipped: $skippedControllers" -ForegroundColor Yellow
Write-Host ""
Write-Host "Log file: $logFile" -ForegroundColor Cyan

# Summary to log
"" | Out-File $logFile -Append
"================================================================================" | Out-File $logFile -Append
"SUMMARY" | Out-File $logFile -Append
"Total controllers: $totalControllers" | Out-File $logFile -Append
"Controllers updated: $updatedControllers" | Out-File $logFile -Append
"Controllers skipped: $skippedControllers" | Out-File $logFile -Append
"Completed: $(Get-Date)" | Out-File $logFile -Append
