<?php
namespace App\Controller;

use Cake\Utility\Inflector;

/**
 * AJAX Filter Trait
 * 
 * Provides server-side AJAX filtering for index and view pages
 * Supports multiple filter operators for accurate database searching
 */
trait AjaxFilterTrait
{
    /**
     * Handle AJAX filter request
     * Called by index() when request is AJAX and has filter parameter
     * 
     * @return \Cake\Http\Response JSON response with filtered HTML rows
     */
    protected function handleAjaxFilter()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');
        
        $filters = $this->request->getQuery('filter', []);
        
        if (empty($filters)) {
            return $this->response->withStringBody(json_encode([
                'success' => false,
                'message' => 'No filters'
            ]));
        }
        
        $modelName = $this->modelClass;
        $table = $this->loadModel($modelName);
        $query = $table->find();
        
        // Apply filters using applyFilters method if it exists
        if (method_exists($this, 'applyFilters')) {
            $query = $this->applyFilters($query, $filters);
        } else {
            // Fallback: basic LIKE filtering
            $schema = $table->getSchema();
            foreach ($filters as $field => $value) {
                if (empty($value)) continue;
                if (strpos($field, '_operator') !== false) continue;
                if (strpos($field, '_range') !== false) continue;
                
                if ($schema->hasColumn($field)) {
                    $query->where([$modelName . '.' . $field . ' LIKE' => '%' . $value . '%']);
                }
            }
        }
        
        // Contain associations
        $associations = $table->associations();
        $contain = [];
        foreach ($associations as $assoc) {
            if ($assoc->type() === 'manyToOne') {
                $contain[] = $assoc->getName();
            }
        }
        if (!empty($contain)) {
            $query->contain($contain);
        }
        
        $results = $query->toArray();
        $total = $table->find()->count();
        $html = $this->generateTableRows($results);
        
        return $this->response->withStringBody(json_encode([
            'success' => true,
            'html' => $html,
            'count' => count($results),
            'total' => $total
        ]));
    }
    
    /**
     * Handle AJAX filter request for related models (used in view pages)
     * 
     * Example: /personnels/filterRelated?model=Companies&foreign_key=company_id&foreign_value=5&filter[name]=ABC
     * 
     * @return \Cake\Http\Response JSON response with filtered HTML rows
     */
    public function filterRelated()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');
        
        // Get parameters
        $relatedModel = $this->request->getQuery('model'); // e.g., "Companies"
        $foreignKey = $this->request->getQuery('foreign_key'); // e.g., "company_id"
        $foreignValue = $this->request->getQuery('foreign_value'); // e.g., 5
        $filters = $this->request->getQuery('filter', []); // Additional filters
        
        if (empty($relatedModel)) {
            return $this->response->withStringBody(json_encode([
                'success' => false,
                'message' => 'Related model not specified'
            ]));
        }
        
        // Load the related model
        try {
            $table = $this->loadModel($relatedModel);
        } catch (\Exception $e) {
            return $this->response->withStringBody(json_encode([
                'success' => false,
                'message' => 'Invalid model: ' . $relatedModel
            ]));
        }
        
        // Build query
        $query = $table->find();
        
        // Apply foreign key filter if provided
        if (!empty($foreignKey) && !empty($foreignValue)) {
            $query->where([$relatedModel . '.' . $foreignKey => $foreignValue]);
        }
        
        // Apply additional filters
        if (!empty($filters)) {
            $schema = $table->getSchema();
            foreach ($filters as $field => $value) {
                if (empty($value)) continue;
                if (strpos($field, '_operator') !== false) continue;
                if (strpos($field, '_range') !== false) continue;
                
                if ($schema->hasColumn($field)) {
                    $operator = isset($filters[$field . '_operator']) ? $filters[$field . '_operator'] : 'like';
                    
                    switch ($operator) {
                        case 'like':
                            $query->where([$relatedModel . '.' . $field . ' LIKE' => '%' . $value . '%']);
                            break;
                        case 'starts_with':
                            $query->where([$relatedModel . '.' . $field . ' LIKE' => $value . '%']);
                            break;
                        case 'ends_with':
                            $query->where([$relatedModel . '.' . $field . ' LIKE' => '%' . $value]);
                            break;
                        case '=':
                            $query->where([$relatedModel . '.' . $field => $value]);
                            break;
                        case '!=':
                            $query->where([$relatedModel . '.' . $field . ' !=' => $value]);
                            break;
                        case '<':
                            $query->where([$relatedModel . '.' . $field . ' <' => $value]);
                            break;
                        case '>':
                            $query->where([$relatedModel . '.' . $field . ' >' => $value]);
                            break;
                        case '<=':
                            $query->where([$relatedModel . '.' . $field . ' <=' => $value]);
                            break;
                        case '>=':
                            $query->where([$relatedModel . '.' . $field . ' >=' => $value]);
                            break;
                        case 'between':
                            $rangeValue = isset($filters[$field . '_range']) ? $filters[$field . '_range'] : null;
                            if ($rangeValue) {
                                $query->where([
                                    $relatedModel . '.' . $field . ' >=' => $value,
                                    $relatedModel . '.' . $field . ' <=' => $rangeValue
                                ]);
                            } else {
                                $query->where([$relatedModel . '.' . $field => $value]);
                            }
                            break;
                        default:
                            $query->where([$relatedModel . '.' . $field . ' LIKE' => '%' . $value . '%']);
                            break;
                    }
                }
            }
        }
        
        // Contain associations
        $associations = $table->associations();
        $contain = [];
        foreach ($associations as $assoc) {
            if ($assoc->type() === 'manyToOne') {
                $contain[] = $assoc->getName();
            }
        }
        if (!empty($contain)) {
            $query->contain($contain);
        }
        
        $results = $query->toArray();
        $total = $table->find()->where([$relatedModel . '.' . $foreignKey => $foreignValue])->count();
        $html = $this->generateRelatedTableRows($results, $relatedModel, $table);
        
        return $this->response->withStringBody(json_encode([
            'success' => true,
            'html' => $html,
            'count' => count($results),
            'total' => $total,
            'model' => $relatedModel
        ]));
    }
    
    /**
     * Generate HTML table rows for related model results
     * 
     * @param array $results Query results
     * @param string $modelName Model name (e.g., "Companies")
     * @param \Cake\ORM\Table $table Table instance
     * @return string HTML table rows
     */
    protected function generateRelatedTableRows($results, $modelName, $table)
    {
        if (empty($results)) {
            return '<tr><td colspan="100" class="text-center p-4 text-muted">
                <i class="fa fa-search"></i> No records found
            </td></tr>';
        }
        
        $html = '';
        $schema = $table->getSchema();
        $primaryKey = $table->getPrimaryKey();
        
        // Get associations
        $associations = [];
        try {
            $assocCollection = $table->associations();
            foreach ($assocCollection as $assoc) {
                if ($assoc->type() === 'manyToOne') {
                    $associations[] = $assoc;
                }
            }
        } catch (\Exception $e) {
            // Continue without associations
        }
        
        // Get visible fields (limit to 7 columns)
        $fields = [];
        foreach ($schema->columns() as $column) {
            $type = $schema->getColumnType($column);
            
            // Skip binary, text types
            if (in_array($type, ['binary', 'text'])) continue;
            
            // Skip image_url if thumbnail exists
            if ($column === 'image_url' && $schema->hasColumn('thumbnail_url')) continue;
            
            // Skip primary key and foreign keys
            if ($column === $primaryKey) continue;
            if (substr($column, -3) === '_id') continue;
            
            $fields[] = $column;
            if (count($fields) >= 7) break;
        }
        
        foreach ($results as $entity) {
            $html .= '<tr class="table-row-with-actions">';
            
            // Actions column first (sticky)
            $id = $entity->get($primaryKey);
            $controllerPath = Inflector::dasherize($modelName);
            
            $html .= '<td class="actions github-related-actions">';
            $html .= sprintf('<a href="/%s/view/%s" class="github-btn github-btn-sm github-btn-primary">
                <i class="fas fa-eye"></i> View</a> ', 
                $controllerPath, htmlspecialchars($id));
            $html .= sprintf('<a href="/%s/edit/%s" class="github-btn github-btn-sm github-btn-secondary">
                <i class="fas fa-edit"></i> Edit</a>', 
                $controllerPath, htmlspecialchars($id));
            $html .= '</td>';
            
            // Show associations first
            foreach ($associations as $assoc) {
                $property = $assoc->getProperty();
                $html .= '<td>';
                if ($entity->has($property) && $entity->{$property}) {
                    try {
                        $bindingKey = $assoc->getBindingKey();
                        $relatedId = is_array($bindingKey) ? $bindingKey[0] : $bindingKey;
                        $relatedId = $entity->{$property}->get($relatedId);
                        
                        $displayField = $assoc->getDisplayField();
                        $relatedName = $entity->{$property}->has($displayField) 
                            ? $entity->{$property}->get($displayField) 
                            : $relatedId;
                        $relatedController = Inflector::dasherize($assoc->getName());
                        $html .= sprintf('<a href="/%s/view/%s">%s</a>', 
                            $relatedController, 
                            htmlspecialchars($relatedId), 
                            htmlspecialchars($relatedName)
                        );
                    } catch (\Exception $e) {
                        $html .= htmlspecialchars($entity->get($assoc->getForeignKey()));
                    }
                }
                $html .= '</td>';
            }
            
            // Show regular fields
            foreach ($fields as $field) {
                $html .= '<td>';
                
                // Handle image/thumbnail
                if (strpos($field, 'thumbnail') !== false || strpos($field, 'image') !== false) {
                    $value = $entity->get($field);
                    if (!empty($value)) {
                        $html .= sprintf('<img src="/%s" alt="Thumbnail" style="max-width:50px; max-height:50px;">', 
                            htmlspecialchars($value));
                    }
                } else {
                    $value = $entity->get($field);
                    
                    // Handle datetime
                    if (is_object($value) && method_exists($value, 'i18nFormat')) {
                        $html .= htmlspecialchars($value->i18nFormat('yyyy-MM-dd HH:mm:ss'));
                    } else {
                        $html .= htmlspecialchars($value);
                    }
                }
                
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        return $html;
    }
    
    protected function generateTableRows($results)
    {
        if (empty($results)) {
            return '<tr><td colspan="100" class="text-center p-4 text-muted">
                <i class="fa fa-search"></i> No records found
            </td></tr>';
        }
        
        $html = '';
        $modelName = $this->modelClass;
        $table = $this->loadModel($modelName);
        $schema = $table->getSchema();
        $primaryKey = $table->getPrimaryKey();
        
        // Get associations
        $associations = [];
        try {
            $assocCollection = $table->associations();
            foreach ($assocCollection as $assoc) {
                if ($assoc->type() === 'manyToOne') {
                    $associations[] = $assoc;
                }
            }
        } catch (\Exception $e) {
            // If associations fail, continue without them
        }
        
        // Get visible fields (exclude image_url if thumbnail exists, exclude binary/text)
        $fields = [];
        foreach ($schema->columns() as $column) {
            $type = $schema->getColumnType($column);
            
            // Skip binary, text types
            if (in_array($type, ['binary', 'text'])) continue;
            
            // Skip image_url if thumbnail_url exists
            if ($column === 'image_url' && $schema->hasColumn('thumbnail_url')) continue;
            
            // Skip primary key and foreign keys
            if ($column === $primaryKey) continue;
            if (substr($column, -3) === '_id') continue;
            
            $fields[] = $column;
            if (count($fields) >= 7) break;
        }
        
        foreach ($results as $entity) {
            $html .= '<tr class="table-row-with-actions">';
            
            // Show associations first (foreign keys)
            foreach ($associations as $assoc) {
                $property = $assoc->getProperty();
                $html .= '<td>';
                if ($entity->has($property) && $entity->{$property}) {
                    try {
                        $bindingKey = $assoc->getBindingKey();
                        $relatedId = is_array($bindingKey) ? $bindingKey[0] : $bindingKey;
                        $relatedId = $entity->{$property}->get($relatedId);
                        
                        $displayField = $assoc->getDisplayField();
                        $relatedName = $entity->{$property}->has($displayField) 
                            ? $entity->{$property}->get($displayField) 
                            : $relatedId;
                        $relatedController = Inflector::dasherize($assoc->getName());
                        $html .= sprintf('<a href="/%s/view/%s">%s</a>', 
                            $relatedController, 
                            htmlspecialchars($relatedId), 
                            htmlspecialchars($relatedName)
                        );
                    } catch (\Exception $e) {
                        $html .= htmlspecialchars($entity->get($assoc->getForeignKey()));
                    }
                }
                $html .= '</td>';
            }
            
            // Show regular fields
            foreach ($fields as $field) {
                $html .= '<td>';
                
                // Handle image/thumbnail
                if (strpos($field, 'thumbnail') !== false || strpos($field, 'image') !== false) {
                    $value = $entity->get($field);
                    if (!empty($value)) {
                        $html .= sprintf('<img src="/%s" alt="Thumbnail" style="max-width:50px; max-height:50px;">', 
                            htmlspecialchars($value));
                    }
                } else {
                    $value = $entity->get($field);
                    
                    // Handle datetime
                    if (is_object($value) && method_exists($value, 'i18nFormat')) {
                        $html .= htmlspecialchars($value->i18nFormat('yyyy-MM-dd HH:mm:ss'));
                    } else {
                        $html .= htmlspecialchars($value);
                    }
                }
                
                $html .= '</td>';
            }
            
            // Actions column with hover buttons
            $id = $entity->get($primaryKey);
            $controllerPath = Inflector::dasherize($this->request->getParam('controller'));
            
            $html .= '<td class="actions">';
            $html .= sprintf('<a href="/%s/view/%s" class="btn-action-icon btn-view-icon" title="View">
                <i class="fas fa-eye"></i></a>', 
                $controllerPath, htmlspecialchars($id));
            $html .= '</td>';
            
            $html .= '</tr>';
        }
        
        return $html;
    }
}
