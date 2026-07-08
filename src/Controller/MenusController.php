<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Menus Controller - navigation menu management.
 * Supports moving menus between categories, activating/deactivating
 * links, and full CRUD. Role access to menus is managed in /permissions.
 */
class MenusController extends AppController
{
    /**
     * All menus grouped by category, with role-access matrix
     */
    public function index()
    {
        $menus = $this->Menus->find()
            ->order(['Menus.id' => 'ASC'])
            ->toArray();

        $parents = [];
        foreach ($menus as $menu) {
            if ($menu->parent_id === null) {
                $parents[$menu->id] = $menu->title;
            }
        }

        $grouped = [];
        foreach ($menus as $menu) {
            $category = $menu->category !== null && $menu->category !== '' ? $menu->category : '(uncategorized)';
            $grouped[$category][] = $menu;
        }

        $categories = array_keys($grouped);

        // Load roles in fixed display order
        $locator  = \Cake\ORM\TableRegistry::getTableLocator();
        $roleOrder = ['lpk-penyangga', 'tmm-recruitment', 'tmm-training', 'tmm-documentation', 'tmm-accounting', 'management', 'administrator'];
        $rolesRaw  = $locator->get('Roles')->find()->toArray();
        $rolesById = [];
        foreach ($rolesRaw as $r) { $rolesById[$r->name] = $r; }
        $roles = array_values(array_filter(array_map(function ($name) use ($rolesById) {
            return isset($rolesById[$name]) ? $rolesById[$name] : null;
        }, $roleOrder)));

        // $roleMatrix[menu_id][role_id] = ['active' => bool, 'granted_actions' => string]
        $roleMatrix = [];
        $rmRows = $locator->get('RoleMenus')->find()->toArray();
        foreach ($rmRows as $rm) {
            $roleMatrix[$rm->menu_id][$rm->role_id] = [
                'active'          => (bool)$rm->is_active,
                'granted_actions' => $rm->granted_actions ?? '*',
            ];
        }

        // Top-level menus available as parent targets (keyed id => title for select)
        $parentOptions = [];
        foreach ($menus as $m) {
            if ($m->parent_id === null) {
                $parentOptions[$m->id] = $m->title;
            }
        }

        $this->set(compact('grouped', 'parents', 'categories', 'roles', 'roleMatrix', 'parentOptions'));
    }

    /**
     * AJAX: toggle a role_menus record on/off.
     * New rows default to granted_actions = '*' (full controller access).
     */
    public function toggleRoleAccess($menuId = null, $roleId = null)
    {
        $this->request->allowMethod(['post']);
        $RoleMenus = \Cake\ORM\TableRegistry::getTableLocator()->get('RoleMenus');

        $existing = $RoleMenus->find()
            ->where(['menu_id' => $menuId, 'role_id' => $roleId])
            ->first();

        if ($existing) {
            $nowActive = $existing->is_active ? 0 : 1;
            $existing->is_active = $nowActive;
            $RoleMenus->save($existing);
            $ga = $existing->granted_actions ?? '*';
        } else {
            $row = $RoleMenus->newEntity([
                'menu_id'         => $menuId,
                'role_id'         => $roleId,
                'is_active'       => 1,
                'granted_actions' => '*',
            ]);
            $RoleMenus->save($row);
            $nowActive = 1;
            $ga = '*';
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'active'          => (bool)$nowActive,
                'granted_actions' => $ga,
            ]));
    }

    /**
     * AJAX: update granted_actions for a role_menus row.
     */
    public function setGrantedActions($menuId = null, $roleId = null)
    {
        $this->request->allowMethod(['post']);
        $ga        = trim((string)$this->request->getData('granted_actions'));
        $RoleMenus = \Cake\ORM\TableRegistry::getTableLocator()->get('RoleMenus');

        $row = $RoleMenus->find()
            ->where(['menu_id' => $menuId, 'role_id' => $roleId, 'is_active' => 1])
            ->first();

        if (!$row) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'not found']));
        }

        $row->granted_actions = $ga === '' ? '*' : $ga;
        $RoleMenus->save($row);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'granted_actions' => $row->granted_actions,
            ]));
    }

    public function view($id = null)
    {
        $menu = $this->Menus->get($id, ['contain' => ['ChildMenus']]);
        $this->set('menu', $menu);
    }

    public function add()
    {
        $menu = $this->Menus->newEntity();
        if ($this->request->is('post')) {
            $menu = $this->Menus->patchEntity($menu, $this->request->getData());
            if ($this->Menus->save($menu)) {
                $this->Flash->success(__('The menu has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The menu could not be saved. Please, try again.'));
        }
        $this->setFormOptions();
        $this->set('menu', $menu);
    }

    public function edit($id = null)
    {
        $menu = $this->Menus->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $menu = $this->Menus->patchEntity($menu, $this->request->getData());
            if ($this->Menus->save($menu)) {
                $this->Flash->success(__('The menu has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The menu could not be saved. Please, try again.'));
        }
        $this->setFormOptions();
        $this->set('menu', $menu);
    }

    /**
     * Quick toggle of is_active
     */
    public function toggle($id = null)
    {
        $this->request->allowMethod(['post']);
        $menu = $this->Menus->get($id);
        $menu->is_active = $menu->is_active ? 0 : 1;
        if ($this->Menus->save($menu)) {
            $this->Flash->success(__('Menu "{0}" is now {1}.', $menu->title, $menu->is_active ? __('active') : __('inactive')));
        } else {
            $this->Flash->error(__('Could not toggle the menu.'));
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }

    /**
     * Move a menu (and its children) to another category
     */
    public function moveCategory($id = null)
    {
        $this->request->allowMethod(['post']);
        $menu = $this->Menus->get($id);
        $category = trim((string)$this->request->getData('category'));
        if ($category === '') {
            $this->Flash->error(__('Category may not be empty.'));

            return $this->redirect(['action' => 'index']);
        }

        $menu->category = $category;
        if ($this->Menus->save($menu)) {
            // Children follow their parent's category
            $this->Menus->updateAll(['category' => $category], ['parent_id' => $menu->id]);
            $this->Flash->success(__('Menu "{0}" moved to category "{1}".', $menu->title, $category));
        } else {
            $this->Flash->error(__('Could not move the menu.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Change a menu's parent (or make it top-level)
     */
    public function moveParent($id = null)
    {
        $this->request->allowMethod(['post']);
        $menu      = $this->Menus->get($id);
        $parentId  = $this->request->getData('parent_id');
        $newParent = ($parentId === '' || $parentId === null) ? null : (int)$parentId;

        if ($newParent !== null && $newParent === $menu->id) {
            $this->Flash->error(__('A menu cannot be its own parent.'));
            return $this->redirect(['action' => 'index']);
        }

        $menu->parent_id = $newParent;

        if ($newParent !== null) {
            $parent = $this->Menus->find()->where(['id' => $newParent])->first();
            if ($parent) {
                $menu->category = $parent->category;
            }
        }

        if ($this->Menus->save($menu)) {
            $parentLabel = $newParent
                ? $this->Menus->get($newParent)->title
                : __('(top level)');
            $this->Flash->success(__('Menu "{0}" moved under "{1}".', $menu->title, $parentLabel));
        } else {
            $this->Flash->error(__('Could not move the menu.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $menu = $this->Menus->get($id);

        $childCount = $this->Menus->find()->where(['parent_id' => $id])->count();
        if ($childCount > 0) {
            $this->Flash->error(__('Menu "{0}" has {1} sub-menu(s). Delete or move them first.', $menu->title, $childCount));

            return $this->redirect(['action' => 'index']);
        }

        if ($this->Menus->delete($menu)) {
            // Remove role assignments pointing at the deleted menu
            \Cake\ORM\TableRegistry::getTableLocator()->get('RoleMenus')->deleteAll(['menu_id' => $id]);
            $this->Flash->success(__('The menu has been deleted.'));
        } else {
            $this->Flash->error(__('The menu could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Shared select options for add/edit forms
     */
    protected function setFormOptions()
    {
        $parentMenus = $this->Menus->find('list')
            ->where(['parent_id IS' => null])
            ->order(['category' => 'ASC', 'sort_order' => 'ASC'])
            ->toArray();

        $categories = $this->Menus->find()
            ->select(['category'])
            ->distinct(['category'])
            ->where(['category IS NOT' => null])
            ->extract('category')
            ->toList();

        $this->set(compact('parentMenus', 'categories'));
    }
}
