<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PostApprentices Controller
 *
 * @property \App\Model\Table\PostApprenticesTable $PostApprentices
 */
class PostApprenticesController extends AppController
{
    public function index()
    {
        $this->paginate = ['order' => ['PostApprentices.id' => 'DESC']];
        $postApprentices = $this->paginate($this->PostApprentices);
        $this->set(compact('postApprentices'));
    }

    public function view($id = null)
    {
        $postApprentice = $this->PostApprentices->get($id);
        $this->set('postApprentice', $postApprentice);
    }

    public function add()
    {
        $postApprentice = $this->PostApprentices->newEntity();
        if ($this->request->is('post')) {
            $postApprentice = $this->PostApprentices->patchEntity($postApprentice, $this->request->getData());
            if ($this->PostApprentices->save($postApprentice)) {
                $this->Flash->success(__('The Post Apprentice has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Post Apprentice could not be saved. Please, try again.'));
        }
        $rows = $this->PostApprentices->getConnection()
            ->execute('SELECT id, tmm_code, apprentice_id_number FROM apprentices ORDER BY tmm_code')
            ->fetchAll('assoc');
        $apprenticeOptions = [];
        foreach ($rows as $r) {
            $label = $r['tmm_code'];
            if (!empty($r['apprentice_id_number'])) $label .= ' (' . $r['apprentice_id_number'] . ')';
            $apprenticeOptions[$r['id']] = $label;
        }
        $statusOptions = [
            'Employed'       => __('Employed'),
            'Self-Employed'  => __('Self-Employed'),
            'Continuing Study' => __('Continuing Study'),
            'Unemployed'     => __('Unemployed'),
            'Other'          => __('Other'),
        ];
        $this->set(compact('postApprentice', 'apprenticeOptions', 'statusOptions'));
    }

    public function edit($id = null)
    {
        $postApprentice = $this->PostApprentices->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $postApprentice = $this->PostApprentices->patchEntity($postApprentice, $this->request->getData());
            if ($this->PostApprentices->save($postApprentice)) {
                $this->Flash->success(__('The Post Apprentice has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Post Apprentice could not be saved. Please, try again.'));
        }
        $this->set('postApprentice', $postApprentice);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $postApprentice = $this->PostApprentices->get($id);
        if ($this->PostApprentices->delete($postApprentice)) {
            $this->Flash->success(__('The Post Apprentice has been deleted.'));
        } else {
            $this->Flash->error(__('The Post Apprentice could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
