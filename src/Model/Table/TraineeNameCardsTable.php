<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TraineeNameCards Model
 */
class TraineeNameCardsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('trainee_name_cards');
        $this->setDisplayField('trainee_id');
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
        return 'cms_masters';
    }
}
