<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LpkPhysicalTests Model
 */
class LpkPhysicalTestsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('candidate_record_placement_tests');
        $this->setDisplayField('candidate_id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_lpk_candidates';
    }
}
