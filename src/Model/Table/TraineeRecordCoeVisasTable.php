<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TraineeRecordCoeVisas Model
 */
class TraineeRecordCoeVisasTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('trainee_record_coe_visas');
        $this->setDisplayField('trainee_id');
        $this->setPrimaryKey('id');

    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_trainee_documents';
    }
}
