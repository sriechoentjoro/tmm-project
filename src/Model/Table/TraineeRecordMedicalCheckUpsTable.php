<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TraineeRecordMedicalCheckUps Model
 */
class TraineeRecordMedicalCheckUpsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('trainee_record_medical_check_ups');
        $this->setDisplayField('title');
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
