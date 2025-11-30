<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * UserRoles Controller
 *
 * Manages the many-to-many relationship between Users and Roles
 * This is primarily for viewing and managing user-role assignments
 */
class UserRolesController extends AppController
{
    /**
     * Index method
     * Shows all user-role assignments
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->loadModel('Users');
        
        $this->paginate = [
            'contain' => ['Roles']
        ];
        
        $users = $this->paginate($this->Users);
        $this->set(compact('users'));
    }

    /**
     * View method
     * Shows all roles assigned to a specific user
     *
     * @param string|null $userId User id.
     * @return \Cake\Http\Response|null
     */
    public function view($userId = null)
    {
        $this->loadModel('Users');
        
        $user = $this->Users->get($userId, [
            'contain' => ['Roles']
        ]);

        $this->set('user', $user);
    }

    /**
     * Assign method
     * Assign roles to a user
     *
     * @param string|null $userId User id.
     * @return \Cake\Http\Response|null
     */
    public function assign($userId = null)
    {
        $this->loadModel('Users');
        $this->loadModel('Roles');
        
        $user = $this->Users->get($userId, [
            'contain' => ['Roles']
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Role assignments have been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Role assignments could not be updated. Please, try again.'));
        }

        $roles = $this->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles'));
    }

    /**
     * Remove method
     * Remove a specific role from a user
     *
     * @param string|null $userId User id.
     * @param string|null $roleId Role id.
     * @return \Cake\Http\Response|null
     */
    public function remove($userId = null, $roleId = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->loadModel('Users');
        
        $user = $this->Users->get($userId, [
            'contain' => ['Roles']
        ]);

        // Remove the specific role
        $updatedRoles = [];
        foreach ($user->roles as $role) {
            if ($role->id != $roleId) {
                $updatedRoles[] = ['id' => $role->id];
            }
        }

        $user->roles = $updatedRoles;
        
        if ($this->Users->save($user)) {
            $this->Flash->success(__('The role has been removed from the user.'));
        } else {
            $this->Flash->error(__('The role could not be removed. Please, try again.'));
        }

        return $this->redirect(['action' => 'view', $userId]);
    }
}
