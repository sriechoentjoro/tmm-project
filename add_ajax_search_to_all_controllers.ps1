# Add AJAX Search Methods to All Controllers
# This script adds searchXXX() methods for all HasMany relationships

Write-Host "=== Adding AJAX Search Methods to Controllers ===" -ForegroundColor Cyan
Write-Host ""

# Controllers that need AJAX search methods based on HasMany relationships
$controllerMethods = @{
    'CandidatesController' = @(
        @{method='searchTrainees'; table='Trainees'; foreignKey='candidate_id'}
    )
    'TraineesController' = @(
        @{method='searchTraineeCertifications'; table='TraineeCertifications'; foreignKey='trainee_id'},
        @{method='searchTraineeCourses'; table='TraineeCourses'; foreignKey='trainee_id'},
        @{method='searchTraineeEducations'; table='TraineeEducations'; foreignKey='trainee_id'},
        @{method='searchTraineeExperiences'; table='TraineeExperiences'; foreignKey='trainee_id'},
        @{method='searchTraineeFamilies'; table='TraineeFamilies'; foreignKey='trainee_id'},
        @{method='searchTraineeTrainingBatches'; table='TraineeTrainingBatches'; foreignKey='trainee_id'}
    )
    'CooperativeAssociationsController' = @(
        @{method='searchApprenticeOrders'; table='ApprenticeOrders'; foreignKey='cooperative_association_id'}
    )
    'AcceptanceOrganizationsController' = @(
        @{method='searchApprenticeOrders'; table='ApprenticeOrders'; foreignKey='acceptance_organization_id'}
    )
    'UsersController' = @(
        @{method='searchCreatedRecords'; table='ApprenticeOrders'; foreignKey='created_by'}
    )
}

$templateContent = @'
    /**
     * AJAX Search for related {TABLE} with server-side filtering
     *
     * @return \Cake\Http\Response|null JSON response
     */
    public function search{TABLE}()
    {
        $this->request->allowMethod(['get', 'post']);
        $this->autoRender = false;
        
        try {
            $parentId = $this->request->getQuery('{FOREIGN_KEY}');
            $page = (int) $this->request->getQuery('page', 1);
            $limit = (int) $this->request->getQuery('limit', 50);
            $filtersJson = $this->request->getQuery('filters', '{}');
            
            $filters = [];
            if (is_string($filtersJson)) {
                $filters = json_decode($filtersJson, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $filters = [];
                }
            } elseif (is_array($filtersJson)) {
                $filters = $filtersJson;
            }
            
            if (empty($parentId)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'error' => 'Missing parent ID']));
            }
            
            $relatedTable = \Cake\ORM\TableRegistry::getTableLocator()->get('{TABLE}');
            $query = $relatedTable->find()->where(['{FOREIGN_KEY}' => $parentId]);
            
            if (!empty($filters) && is_array($filters)) {
                $orConditions = [];
                
                foreach ($filters as $field => $filterData) {
                    if (!is_array($filterData)) continue;
                    
                    $value = isset($filterData['value']) ? trim($filterData['value']) : '';
                    $operator = isset($filterData['operator']) ? $filterData['operator'] : 'contains';
                    
                    if ($value === '' && !in_array($operator, ['file_exists', 'file_not_exists'])) {
                        continue;
                    }
                    
                    switch ($operator) {
                        case 'contains':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => '%' . $value . '%'];
                            }
                            break;
                        case 'equals':
                            if (!empty($value)) {
                                $orConditions[] = [$field => $value];
                            }
                            break;
                        case 'not_equals':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' !=' => $value];
                            }
                            break;
                        case 'starts_with':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => $value . '%'];
                            }
                            break;
                        case 'ends_with':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => '%' . $value];
                            }
                            break;
                        case 'greater_than':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' >' => $value];
                            }
                            break;
                        case 'less_than':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' <' => $value];
                            }
                            break;
                        case 'file_exists':
                            $orConditions[] = function ($exp, $q) use ($field) {
                                return $exp->and_([
                                    $exp->isNotNull($field),
                                    $exp->notEq($field, '')
                                ]);
                            };
                            break;
                        case 'file_not_exists':
                            $orConditions[] = function ($exp, $q) use ($field) {
                                return $exp->or_([
                                    $exp->isNull($field),
                                    $exp->eq($field, '')
                                ]);
                            };
                            break;
                    }
                }
                
                if (!empty($orConditions)) {
                    $query->where(['OR' => $orConditions]);
                }
            }
            
            $total = $query->count();
            $records = $query->limit($limit)->offset(($page - 1) * $limit)->all();
            
            $formattedRecords = [];
            foreach ($records as $record) {
                $formattedRecords[] = $record->toArray();
            }
            
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'records' => $formattedRecords,
                    'total' => $total,
                    'page' => $page,
                    'pages' => ceil($total / $limit)
                ]));
                
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
'@

$addedCount = 0

foreach ($controllerName in $controllerMethods.Keys) {
    $controllerPath = "src/Controller/$controllerName.php"
    
    if (!(Test-Path $controllerPath)) {
        Write-Host "⚠ Controller not found: $controllerName" -ForegroundColor Yellow
        continue
    }
    
    Write-Host "Processing: $controllerName" -ForegroundColor Cyan
    
    $content = Get-Content $controllerPath -Raw -Encoding UTF8
    
    foreach ($method in $controllerMethods[$controllerName]) {
        $methodName = $method.method
        $tableName = $method.table
        $foreignKey = $method.foreignKey
        
        # Check if method already exists
        if ($content -match "function $methodName\(") {
            Write-Host "  OK $methodName already exists - SKIP" -ForegroundColor Green
            continue
        }
        
        Write-Host "  + Adding $methodName..." -ForegroundColor Yellow
        
        # Generate method code
        $methodCode = $templateContent -replace '{TABLE}', $tableName -replace '{FOREIGN_KEY}', $foreignKey
        
        # Find position to insert (before last closing brace)
        $lastBracePos = $content.LastIndexOf('}')
        if ($lastBracePos -gt 0) {
            $content = $content.Substring(0, $lastBracePos) + "`n" + $methodCode + "`n" + $content.Substring($lastBracePos)
            $addedCount++
        }
    }
    
    # Save file
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($controllerPath, $content, $utf8NoBom)
    
    Write-Host "  OK Updated $controllerName" -ForegroundColor Green
}

Write-Host ""
Write-Host "=== Summary ===" -ForegroundColor Cyan
Write-Host "Added $addedCount AJAX search methods" -ForegroundColor Green
Write-Host ""
Write-Host "Done! Run: bin\cake cache clear_all" -ForegroundColor Yellow
