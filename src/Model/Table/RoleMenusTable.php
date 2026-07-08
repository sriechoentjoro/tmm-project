<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RoleMenus Model - assigns menus (cms_masters.menus) to roles.
 * A menu assignment also grants the role access to the controller/action
 * the menu URL points to (see AppController::getMenuRolePermissions()).
 */
class RoleMenusTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('role_menus');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('role_id')
            ->requirePresence('role_id', 'create')
            ->notEmptyString('role_id');

        $validator
            ->integer('menu_id')
            ->requirePresence('menu_id', 'create')
            ->notEmptyString('menu_id');

        return $validator;
    }

    public static function defaultConnectionName()
    {
        return 'cms_authentication_authorization';
    }
}
