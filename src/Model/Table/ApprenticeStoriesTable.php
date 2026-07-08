<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeStories Model
 */
class ApprenticeStoriesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('apprentice_stories');
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
        return 'cms_tmm_apprentices';
    }
}
