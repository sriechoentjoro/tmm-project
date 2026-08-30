<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * EmailTemplates Controller
 *
 * @property \App\Model\Table\EmailTemplatesTable $EmailTemplates
 */
class EmailTemplatesController extends AppController
{
    public function index()
    {
        $this->paginate = ['order' => ['EmailTemplates.id' => 'DESC']];
        $emailTemplates = $this->paginate($this->EmailTemplates);

        // Ringkasan status + jumlah email terkirim (dari email_logs terasosiasi)
        $conn = $this->EmailTemplates->getConnection();
        $counts = $conn->execute(
            'SELECT COUNT(*) AS total, SUM(is_active = 1) AS active FROM email_templates'
        )->fetch('assoc');
        $sentMap = [];
        try {
            foreach ($conn->execute(
                'SELECT template_key, COUNT(*) AS n FROM email_logs GROUP BY template_key'
            )->fetchAll('assoc') as $r) {
                $sentMap[$r['template_key']] = $r['n'];
            }
        } catch (\Exception $e) {
            // email_logs mungkin tidak punya kolom template_key — abaikan
        }

        $this->set(compact('emailTemplates', 'counts', 'sentMap'));
    }

    public function view($id = null)
    {
        $emailTemplate = $this->EmailTemplates->get($id);
        $this->set('emailTemplate', $emailTemplate);
    }

    public function add()
    {
        $emailTemplate = $this->EmailTemplates->newEntity();
        if ($this->request->is('post')) {
            $emailTemplate = $this->EmailTemplates->patchEntity($emailTemplate, $this->request->getData());
            if ($this->EmailTemplates->save($emailTemplate)) {
                $this->Flash->success(__('The Email Template has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Email Template could not be saved. Please, try again.'));
        }
        $this->set('emailTemplate', $emailTemplate);
    }

    public function edit($id = null)
    {
        $emailTemplate = $this->EmailTemplates->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $emailTemplate = $this->EmailTemplates->patchEntity($emailTemplate, $this->request->getData());
            if ($this->EmailTemplates->save($emailTemplate)) {
                $this->Flash->success(__('The Email Template has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Email Template could not be saved. Please, try again.'));
        }
        $this->set('emailTemplate', $emailTemplate);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $emailTemplate = $this->EmailTemplates->get($id);
        if ($this->EmailTemplates->delete($emailTemplate)) {
            $this->Flash->success(__('The Email Template has been deleted.'));
        } else {
            $this->Flash->error(__('The Email Template could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
