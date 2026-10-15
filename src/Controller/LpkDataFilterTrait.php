<?php
namespace App\Controller;

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * LpkDataFilterTrait
 * 
 * Provides institution-based data filtering for LPK Penyangga users
 * Apply this trait to controllers that manage LPK-specific data
 */
trait LpkDataFilterTrait
{
    /**
     * How the records this controller manages belong to an institution.
     *
     * Two shapes in this application:
     *
     *   ['field' => 'vocational_training_institution_id']
     *       the table carries the column itself - candidates, trainees,
     *       apprentices and the three course tables;
     *
     *   ['through' => 'Candidates', 'foreignKey' => 'candidate_id']
     *       the record hangs off one that does - a candidate's education,
     *       experience, family, certificate, interview or medical check-up.
     *
     * A controller says which by declaring $institutionOwnership. The first
     * shape is the default, so the ones carrying the column need say nothing.
     *
     * @return array
     */
    protected function institutionOwnership()
    {
        if (property_exists($this, 'institutionOwnership')) {
            return (array)$this->institutionOwnership;
        }

        return ['field' => 'vocational_training_institution_id'];
    }

    /**
     * The institution a record belongs to, or null when it belongs to none.
     *
     * @param \Cake\Datasource\EntityInterface $record The record.
     * @return int|null
     */
    protected function owningInstitutionId($record)
    {
        $ownership = $this->institutionOwnership();

        if (isset($ownership['field'])) {
            $owner = $record->get($ownership['field']);

            return $owner === null || $owner === '' ? null : (int)$owner;
        }

        // Through a parent. Read the parent's column alone: the record may or
        // may not have been loaded with it contained, and a guard that only
        // works when somebody remembered the contain is not a guard.
        $parentId = $record->get($ownership['foreignKey']);
        if (!$parentId) {
            return null;
        }
        $parents = TableRegistry::getTableLocator()->get($ownership['through']);
        $parent = $parents->find()
            ->select(['vocational_training_institution_id'])
            ->where([$parents->getAlias() . '.id' => $parentId])
            ->enableHydration(false)
            ->first();

        return $parent && $parent['vocational_training_institution_id']
            ? (int)$parent['vocational_training_institution_id']
            : null;
    }

    /**
     * Narrow a query to what this user's institution owns.
     *
     * For an administrator or any role that is not an LPK it returns the query
     * untouched: this is institution scoping, not a second permission system.
     *
     * @param \Cake\ORM\Query $query The query the screen is about to run.
     * @return \Cake\ORM\Query
     */
    protected function scopeToInstitution(Query $query)
    {
        if (!$this->hasRole('lpk-penyangga')) {
            return $query;
        }

        $institutionId = $this->getUserInstitutionId();
        if (!$institutionId) {
            // Nothing belongs to an account with no institution. Written as a
            // string, because CakePHP drops an array condition under a numeric
            // key and the query would go out with no WHERE at all - which is
            // how where(['1' => 0]) once showed an LPK user every row in the
            // table instead of none.
            return $query->where(['1 = 0']);
        }

        $ownership = $this->institutionOwnership();
        if (isset($ownership['field'])) {
            return $query->where([
                $query->getRepository()->getAlias() . '.' . $ownership['field'] => $institutionId,
            ]);
        }

        // Through a parent: an inner join, so a record whose parent is gone
        // does not slip through on a left join's null.
        return $query->innerJoinWith($ownership['through'],
            function (Query $parent) use ($institutionId, $ownership) {
                return $parent->where([
                    $ownership['through'] . '.vocational_training_institution_id' => $institutionId,
                ]);
            });
    }

    /**
     * Narrow a list of candidates to this user's institution.
     *
     * The pick-lists these forms offer are the other half of the scoping. A
     * screen can refuse to open somebody else's candidate and still offer that
     * candidate in a dropdown, and attaching an education or an interview to
     * them writes a record into another institution's file.
     *
     * Separate from scopeToInstitution() because that one reads the ownership
     * of the records this controller manages, and these are candidates
     * whatever the controller is.
     *
     * @param \Cake\ORM\Query $query A query on Candidates.
     * @return \Cake\ORM\Query
     */
    protected function scopeCandidates(Query $query)
    {
        if (!$this->hasRole('lpk-penyangga')) {
            return $query;
        }

        $institutionId = $this->getUserInstitutionId();
        if (!$institutionId) {
            return $query->where(['1 = 0']);
        }

        return $query->where([
            $query->getRepository()->getAlias()
                . '.vocational_training_institution_id' => $institutionId,
        ]);
    }

    /**
     * Stop an LPK user opening a record that is not their institution's.
     *
     * The permission system in this application answers "may this role reach
     * this screen", which is not the same question as "may this person see
     * this record". Without this, an id in the address bar was enough: the
     * screens were granted to the role, and every record on them was reachable
     * by anybody the role was granted to.
     *
     * @param \Cake\Datasource\EntityInterface $record The record being opened.
     * @return \Cake\Http\Response|null A redirect when refused, null when allowed.
     */
    protected function refuseOtherInstitution($record)
    {
        if (!$this->hasRole('lpk-penyangga')) {
            return null;
        }

        $institutionId = $this->getUserInstitutionId();
        $owner = $this->owningInstitutionId($record);

        if ($institutionId && $owner && (int)$institutionId === $owner) {
            return null;
        }

        return $this->handleUnauthorizedAccess(
            $this->request->getParam('action'),
            __('That record belongs to another institution.')
        );
    }

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
            // No institution on the account, so nothing belongs to them.
            //
            // This read where(['1' => 0]). CakePHP treats a numeric array key
            // as no condition at all and drops it, so the query went out with
            // no WHERE clause and the user saw every record in the table
            // instead of none - the exact opposite of what the line says. A
            // string condition is a condition.
            return $query->where(['1 = 0']);
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
