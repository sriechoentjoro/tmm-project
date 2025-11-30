<?php
namespace App\Traits;

use Cake\Utility\Inflector;

/**
 * AJAX Filter Trait
 */
trait AjaxFilterTrait
{
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
        
        // Apply filters
        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            
            $schema = $table->getSchema();
            if ($schema->hasColumn($field)) {
                $query->where([$modelName . '.' . $field . ' LIKE' => '%' . $value . '%']);
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
        
        // Get first 7 fields
        $fields = [];
        foreach ($schema->columns() as $column) {
            $type = $schema->getColumnType($column);
            if (!in_array($type, ['binary', 'text'])) {
                $fields[] = $column;
                if (count($fields) >= 7) break;
            }
        }
        
        foreach ($results as $entity) {
            $html .= '<tr>';
            
            foreach ($fields as $field) {
                $html .= '<td>';
                
                // Check foreign key
                $isForeignKey = false;
                foreach ($table->associations() as $assoc) {
                    if ($assoc->type() === 'manyToOne' && $assoc->getForeignKey() === $field) {
                        $property = $assoc->getProperty();
                        if ($entity->has($property) && $entity->{$property}) {
                            $relatedId = $entity->{$property}->{$assoc->getPrimaryKey()};
                            $relatedName = $entity->{$property}->{$assoc->getDisplayField()};
                            $relatedController = Inflector::dasherize($assoc->getName());
                            $html .= sprintf('<a href="/%s/view/%s">%s</a>', 
                                $relatedController, 
                                htmlspecialchars($relatedId), 
                                htmlspecialchars($relatedName)
                            );
                        }
                        $isForeignKey = true;
                        break;
                    }
                }
                
                if (!$isForeignKey) {
                    $value = $entity->get($field);
                    $html .= htmlspecialchars($value);
                }
                
                $html .= '</td>';
            }
            
            // Actions
            $id = $entity->get($primaryKey);
            $controllerPath = Inflector::dasherize($this->request->getParam('controller'));
            
            $html .= '<td class="text-center">';
            $html .= '<div class="btn-group" role="group">';
            $html .= sprintf('<a href="/%s/view/%s" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>', 
                $controllerPath, htmlspecialchars($id));
            $html .= sprintf('<a href="/%s/edit/%s" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>', 
                $controllerPath, htmlspecialchars($id));
            $html .= sprintf('<form method="post" action="/%s/delete/%s" class="d-inline" onsubmit="return confirm(\'Sure?\')">
                <input type="hidden" name="_csrfToken" value="%s">
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                </form>', 
                $controllerPath, htmlspecialchars($id), htmlspecialchars($this->request->getAttribute('csrfToken')));
            $html .= '</div></td>';
            
            $html .= '</tr>';
        }
        
        return $html;
    }
}
