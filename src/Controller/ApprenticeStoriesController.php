<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeStories Controller
 *
 * @property \App\Model\Table\ApprenticeStoriesTable $ApprenticeStories
 */
class ApprenticeStoriesController extends AppController
{
    public function index()
    {
        $this->paginate = ['order' => ['ApprenticeStories.id' => 'DESC']];
        $apprenticeStories = $this->paginate($this->ApprenticeStories);
        $this->set(compact('apprenticeStories'));
    }

    public function view($id = null)
    {
        $apprenticeStory = $this->ApprenticeStories->get($id);
        $this->set('apprenticeStory', $apprenticeStory);
    }

    public function add()
    {
        $apprenticeStory = $this->ApprenticeStories->newEntity();
        if ($this->request->is('post')) {
            $apprenticeStory = $this->ApprenticeStories->patchEntity($apprenticeStory, $this->request->getData());
            if ($this->ApprenticeStories->save($apprenticeStory)) {
                $this->Flash->success(__('The Apprentice Story has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Apprentice Story could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeStories->getConnection()
            ->execute('SELECT id, tmm_code, apprentice_id_number FROM apprentices ORDER BY tmm_code')
            ->fetchAll('assoc');
        $apprenticeOptions = [];
        foreach ($apprentices as $a) {
            $label = $a['tmm_code'];
            if (!empty($a['apprentice_id_number'])) {
                $label .= ' (' . $a['apprentice_id_number'] . ')';
            }
            $apprenticeOptions[$a['id']] = $label;
        }
        $this->set(compact('apprenticeStory', 'apprenticeOptions'));
    }

    public function edit($id = null)
    {
        $apprenticeStory = $this->ApprenticeStories->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $apprenticeStory = $this->ApprenticeStories->patchEntity($apprenticeStory, $this->request->getData());
            if ($this->ApprenticeStories->save($apprenticeStory)) {
                $this->Flash->success(__('The Apprentice Story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Apprentice Story could not be saved. Please, try again.'));
        }
        $this->set('apprenticeStory', $apprenticeStory);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeStory = $this->ApprenticeStories->get($id);
        if ($this->ApprenticeStories->delete($apprenticeStory)) {
            $this->Flash->success(__('The Apprentice Story has been deleted.'));
        } else {
            $this->Flash->error(__('The Apprentice Story could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
