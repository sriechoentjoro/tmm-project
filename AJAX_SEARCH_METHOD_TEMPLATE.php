/**
 * GENERIC AJAX SEARCH METHOD - Copy this to any controller
 * 
 * Replace XXXXX with your related table name (e.g., Apprentices, TraineeCertifications)
 * Replace xxxxx_id with your foreign key field (e.g., apprentice_order_id, trainee_id)
 */

/**
 * AJAX Search for related XXXXX with server-side filtering
 *
 * @return \Cake\Http\Response|null JSON response
 */
public function searchXXXXX()
{
    $this->request->allowMethod(['get', 'post']);
    $this->autoRender = false;
    
    try {
        $parentId = $this->request->getQuery('xxxxx_id'); // e.g., apprentice_order_id, trainee_id
        $page = (int) $this->request->getQuery('page', 1);
        $limit = (int) $this->request->getQuery('limit', 50);
        $filtersJson = $this->request->getQuery('filters', '{}');
        
        // Decode filters JSON
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
        
        $relatedTable = \Cake\ORM\TableRegistry::getTableLocator()->get('XXXXX'); // e.g., 'Apprentices'
        $query = $relatedTable->find()
            ->where(['xxxxx_id' => $parentId]); // e.g., ['apprentice_order_id' => $parentId]
        
        // Apply filters with OR logic (match ANY filter, not ALL)
        if (!empty($filters) && is_array($filters)) {
            $orConditions = [];
            
            foreach ($filters as $field => $filterData) {
                if (!is_array($filterData)) continue;
                
                $value = isset($filterData['value']) ? trim($filterData['value']) : '';
                $operator = isset($filterData['operator']) ? $filterData['operator'] : 'contains';
                
                // Skip if value is empty and operator requires a value
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
                    case 'greater_equal':
                        if (!empty($value)) {
                            $orConditions[] = [$field . ' >=' => $value];
                        }
                        break;
                    case 'less_equal':
                        if (!empty($value)) {
                            $orConditions[] = [$field . ' <=' => $value];
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
            
            // Apply OR conditions if any filters were added
            if (!empty($orConditions)) {
                $query->where(['OR' => $orConditions]);
            }
        }
        
        // Get total count with filters
        $total = $query->count();
        
        // Get paginated results
        $records = $query->limit($limit)->offset(($page - 1) * $limit)->all();
        
        // Format records for JSON
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
