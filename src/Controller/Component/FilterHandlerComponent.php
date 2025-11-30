<?php
/**
 * Filter Handling Component
 * Add this to your controller's initialize() method to enable filtering
 * 
 * Usage in Controller:
 * public function initialize() {
 *     parent::initialize();
 *     $this->loadComponent('FilterHandler');
 * }
 * 
 * In index() action:
 * $query = $this->ModelName->find();
 * $query = $this->FilterHandler->applyFilters($query, $this->request->getQueryParams());
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\Query;

class FilterHandlerComponent extends Component
{
    /**
     * Apply filters from query parameters to ORM query
     *
     * @param Query $query The query object
     * @param array $queryParams Request query parameters
     * @return Query Modified query with filters applied
     */
    public function applyFilters(Query $query, array $queryParams = [])
    {
        foreach ($queryParams as $key => $value) {
            // Only process filter_* parameters
            if (strpos($key, 'filter_') !== 0) {
                continue;
            }
            
            // Skip empty values
            if (empty($value)) {
                continue;
            }
            
            // Extract field name
            $field = substr($key, 7); // Remove 'filter_' prefix
            
            // Apply LIKE condition for text search
            $query->where([
                $field . ' LIKE' => '%' . $value . '%'
            ]);
        }
        
        return $query;
    }
    
    /**
     * Get current filters from request
     *
     * @param array $queryParams Request query parameters
     * @return array Array of current filters
     */
    public function getCurrentFilters(array $queryParams = [])
    {
        $filters = [];
        
        foreach ($queryParams as $key => $value) {
            if (strpos($key, 'filter_') === 0 && !empty($value)) {
                $field = substr($key, 7);
                $filters[$field] = $value;
            }
        }
        
        return $filters;
    }
}
