<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Permissions Controller - assigns menus to roles.
 * Assigning a menu to a role automatically grants the role access to the
 * controller/action the menu URL points to (AppController reads role_menus
 * inside getRolePermissions()/hasPermission()).
 */
class PermissionsController extends AppController
{
    /**
     * Role-menu assignment matrix for one role at a time.
     * GET ?role_id=N shows the role's menus; POST saves the assignment.
     */
    public function index()
    {
        $rolesTable = TableRegistry::getTableLocator()->get('Roles');
        $roleMenusTable = TableRegistry::getTableLocator()->get('RoleMenus');
        $menusTable = TableRegistry::getTableLocator()->get('Menus');

        $roles = $rolesTable->find('list')->order(['id' => 'ASC'])->toArray();
        $roleId = (int)($this->request->getData('role_id') ?: $this->request->getQuery('role_id') ?: key($roles));

        if ($this->request->is('post') && $this->request->getData('save_assignments') !== null) {
            $selected = array_map('intval', (array)$this->request->getData('menu_ids'));

            $existing = $roleMenusTable->find()
                ->where(['role_id' => $roleId])
                ->all()
                ->indexBy('menu_id')
                ->toArray();

            $added = $removed = 0;
            foreach ($selected as $menuId) {
                if (!isset($existing[$menuId])) {
                    $roleMenusTable->save($roleMenusTable->newEntity([
                        'role_id' => $roleId,
                        'menu_id' => $menuId,
                        'is_active' => 1,
                    ]));
                    $added++;
                }
            }
            foreach ($existing as $menuId => $entity) {
                if (!in_array((int)$menuId, $selected, true)) {
                    $roleMenusTable->delete($entity);
                    $removed++;
                }
            }
            $this->Flash->success(__('Assignments saved: {0} added, {1} removed.', $added, $removed));

            return $this->redirect(['action' => 'index', '?' => ['role_id' => $roleId]]);
        }

        $menus = $menusTable->find()
            ->order(['Menus.id' => 'ASC'])
            ->toArray();
        $grouped = [];
        foreach ($menus as $menu) {
            $category = $menu->category !== null && $menu->category !== '' ? $menu->category : '(uncategorized)';
            $grouped[$category][] = $menu;
        }

        $assigned = $roleMenusTable->find()
            ->where(['role_id' => $roleId, 'is_active' => 1])
            ->extract('menu_id')
            ->toList();

        $this->set(compact('roles', 'roleId', 'grouped', 'assigned'));
    }

    /**
     * Clone one role's menu assignments to another role
     */
    public function cloneAccess()
    {
        $this->request->allowMethod(['post']);
        $fromRoleId = (int)$this->request->getData('from_role_id');
        $toRoleId = (int)$this->request->getData('to_role_id');

        if (!$fromRoleId || !$toRoleId || $fromRoleId === $toRoleId) {
            $this->Flash->error(__('Pick two different roles to clone access.'));

            return $this->redirect(['action' => 'index']);
        }

        $roleMenusTable = TableRegistry::getTableLocator()->get('RoleMenus');

        $sourceMenuIds = $roleMenusTable->find()
            ->where(['role_id' => $fromRoleId, 'is_active' => 1])
            ->extract('menu_id')
            ->toList();

        $existingTargetIds = $roleMenusTable->find()
            ->where(['role_id' => $toRoleId])
            ->extract('menu_id')
            ->toList();

        $cloned = 0;
        foreach ($sourceMenuIds as $menuId) {
            if (in_array($menuId, $existingTargetIds)) {
                continue;
            }
            $roleMenusTable->save($roleMenusTable->newEntity([
                'role_id' => $toRoleId,
                'menu_id' => $menuId,
                'is_active' => 1,
            ]));
            $cloned++;
        }

        $this->Flash->success(__('Cloned {0} menu assignment(s).', $cloned));

        return $this->redirect(['action' => 'index', '?' => ['role_id' => $toRoleId]]);
    }
}
