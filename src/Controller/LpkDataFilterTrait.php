<?php
namespace App\Controller;

use Cake\ORM\Query;

/**
 * LpkDataFilterTrait
 * 
 * Provides institution-based data filtering for LPK Penyangga users
 * Apply this trait to controllers that manage LPK-specific data
 */
trait LpkDataFilterTrait
{
    /**
     * Apply institution filter to query for LPK users
     * 
     * @param Query $query The query to filter
     * @param string $institutionField The field name for institution_id in the table
     * @return Query
     */
    protected function applyInstitutionFilter(Query $query, $institutionField = 'vocational_training_institution_id')
    {
        // Only filter if user is LPK Penyangga
        if (!$this->hasRole('lpk-penyangga')) {
            return $query;
        }
        
        $institutionId = $this->getUserInstitutionId();
        
        if (!$institutionId) {
            // If LPK user has no institution_id, return empty query
            return $query->where(['1' => 0]);
        }
        
        // Filter by institution
        return $query->where([$institutionField => $institutionId]);
    }
    
    /**
     * Check if current user can access a specific record
     * 
     * @param mixed $record The record entity
     * @param string $institutionField The field name for institution_id
     * @return bool
     */
    protected function canAccessRecord($record, $institutionField = 'vocational_training_institution_id')
    {
        // Administrators can access everything
        if ($this->hasRole('administrator')) {
            return true;
        }
        
        // Non-LPK roles can access if authorized by other means
        if (!$this->hasRole('lpk-penyangga')) {
            return true;
        }
        
        // LPK users can only access their own institution's data
        $userInstitutionId = $this->getUserInstitutionId();
        $recordInstitutionId = $record->get($institutionField);
        
        return $userInstitutionId && $recordInstitutionId && 
               $userInstitutionId == $recordInstitutionId;
    }
    
    /**
     * Auto-assign institution ID to new records for LPK users
     * 
     * @param mixed $entity The entity to update
     * @param string $institutionField The field name for institution_id
     * @return mixed The updated entity
     */
    protected function setInstitutionId($entity, $institutionField = 'vocational_training_institution_id')
    {
        // Only for LPK users
        if (!$this->hasRole('lpk-penyangga')) {
            return $entity;
        }
        
        $institutionId = $this->getUserInstitutionId();
        
        if ($institutionId && !$entity->get($institutionField)) {
            $entity->set($institutionField, $institutionId);
        }
        
        return $entity;
    }
    
    /**
     * Get institution-filtered pagination
     * 
     * @param string $modelAlias The model alias
     * @param string $institutionField The institution field name
     * @return \Cake\ORM\Query
     */
    protected function getInstitutionFilteredQuery($modelAlias = null, $institutionField = 'vocational_training_institution_id')
    {
        $modelAlias = $modelAlias ?: $this->modelClass;
        $query = $this->{$modelAlias}->find();
        
        return $this->applyInstitutionFilter($query, $institutionField);
    }
}
