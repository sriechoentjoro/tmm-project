<?php
namespace App\Controller\Traits;

use Cake\Utility\Inflector;

/**
 * Searchable Trait
 * 
 * Provides method to apply search filters from request query parameters
 * Supports:
 * - filter_column=value (from table-enhanced.js)
 * - filter[column]=value (standard array)
 * - filter_column_operator=op (operator support)
 */
trait SearchableTrait
{
    /**
     * Apply search filters from request query parameters
     * 
     * @param \Cake\ORM\Query $query The query to modify
     * @return \Cake\ORM\Query The modified query
     */
    public function applySearchFilter($query)
    {
        $queryParams = $this->request->getQueryParams();
        $filters = [];
        
        // 1. Parse filter_ params (from table-enhanced.js)
        foreach ($queryParams as $key => $value) {
            if (strpos($key, 'filter_') === 0) {
                $field = substr($key, 7); // Remove 'filter_'
                
                // Skip operators for now
                if (strpos($field, '_operator') !== false) {
                    continue;
                }
                
                if ($value !== '' && $value !== null) {
                    $filters[$field] = $value;
                }
            }
        }
        
        // 2. Parse filter[] params (standard/ajax-table-filter.js)
        if (!empty($queryParams['filter']) && is_array($queryParams['filter'])) {
            foreach ($queryParams['filter'] as $field => $value) {
                if ($value !== '' && $value !== null) {
                    $filters[$field] = $value;
                }
            }
        }
        
        if (empty($filters)) {
            return $query;
        }
        
        $alias = $query->getRepository()->getAlias();
        $schema = $query->getRepository()->getSchema();
        
        foreach ($filters as $field => $value) {
            // Determine operator
            $operator = '=';
            
            // Check filter_field_operator
            if (isset($queryParams['filter_' . $field . '_operator'])) {
                $operator = $queryParams['filter_' . $field . '_operator'];
            } 
            // Check filter_operator[field] (if passed as array)
            elseif (isset($queryParams['filter_operator'][$field])) {
                $operator = $queryParams['filter_operator'][$field];
            }
            
            // Determine DB field
            // If field contains dot, assume it's Alias.field
            if (strpos($field, '.') !== false) {
                $dbField = $field;
            } else {
                // Check if field exists in main table
                if ($schema->hasColumn($field)) {
                    $dbField = "$alias.$field";
                } else {
                    // Might be a virtual field or association field not fully qualified
                    // For now assume main table if not qualified
                    $dbField = "$alias.$field";
                }
            }
            
            // Apply condition based on operator
            switch ($operator) {
                case 'like':
                    $query->where(["$dbField LIKE" => "%$value%"]);
                    break;
                case 'not_like':
                    $query->where(["$dbField NOT LIKE" => "%$value%"]);
                    break;
                case 'starts_with':
                    $query->where(["$dbField LIKE" => "$value%"]);
                    break;
                case 'ends_with':
                    $query->where(["$dbField LIKE" => "%$value"]);
                    break;
                case 'between':
                    // Not fully supported yet without second value
                    $query->where(["$dbField" => $value]);
                    break;
                case '!=':
                    $query->where(["$dbField !=" => $value]);
                    break;
                case '>':
                    $query->where(["$dbField >" => $value]);
                    break;
                case '<':
                    $query->where(["$dbField <" => $value]);
                    break;
                case '>=':
                    $query->where(["$dbField >=" => $value]);
                    break;
                case '<=':
                    $query->where(["$dbField <=" => $value]);
                    break;
                default: // =
                    $query->where(["$dbField" => $value]);
            }
        }
        
        return $query;
    }
}
