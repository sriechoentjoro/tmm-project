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

        // Peta apprentice + ringkasan klasifikasi masalah
        $conn = $this->ApprenticeStories->getConnection();
        $apprenticeMap = [];
        foreach ($conn->execute('SELECT id, name, tmm_code FROM apprentices')->fetchAll('assoc') as $r) {
            $apprenticeMap[$r['id']] = $r;
        }
        $classStats = $conn->execute(
            'SELECT problem_classification AS label, COUNT(*) AS n
             FROM apprentice_stories GROUP BY problem_classification ORDER BY n DESC'
        )->fetchAll('assoc');
        $totalStories = array_sum(array_column($classStats, 'n'));

        $this->set(compact('apprenticeStories', 'apprenticeMap', 'classStats', 'totalStories'));
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
            $apprenticeStory = $this->ApprenticeStories->patchEntity($apprenticeStory, $this->_dataWithImage());
            if ($this->ApprenticeStories->save($apprenticeStory)) {
                $this->Flash->success(__('The Apprentice Story has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Apprentice Story could not be saved. Please, try again.'));
        }
        $this->set(compact('apprenticeStory'));
        $this->set('apprenticeOptions', $this->_apprenticeOptions());
    }

    public function edit($id = null)
    {
        $apprenticeStory = $this->ApprenticeStories->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $apprenticeStory = $this->ApprenticeStories->patchEntity($apprenticeStory, $this->_dataWithImage());
            if ($this->ApprenticeStories->save($apprenticeStory)) {
                $this->Flash->success(__('The Apprentice Story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Apprentice Story could not be saved. Please, try again.'));
        }
        $this->set(compact('apprenticeStory'));
        $this->set('apprenticeOptions', $this->_apprenticeOptions());
    }

    /**
     * Request data with the uploaded picture stored and image_path pointing at it.
     *
     * The add form used to offer a drag-and-drop upload zone whose JavaScript
     * wrote the chosen file's NAME into a hidden image_path field. Nothing
     * moved the file: the record kept a bare filename pointing at a file that
     * was never on the server, and the picture the user chose was gone the
     * moment the page submitted. The edit form, meanwhile, offered image_path
     * as a plain text box. Same record, two forms, neither of which stored an
     * image.
     *
     * ApprenticeFamilyStories - the same shape of record - has always used
     * uploadImage(). This does the same, and leaves image_path untouched when
     * no file was chosen, so editing a story does not wipe its picture.
     *
     * @return array
     */
    protected function _dataWithImage()
    {
        $data = $this->request->getData();
        unset($data['image_path']);

        $file = $data['image_upload'] ?? null;
        $hasUpload = is_array($file)
            && isset($file['error'])
            && $file['error'] === UPLOAD_ERR_OK;

        if ($hasUpload) {
            $path = $this->uploadImage('ApprenticeStories', 'image_upload', 'apprenticestories');
            if ($path) {
                $data['image_path'] = $path;
            }
        }
        unset($data['image_upload']);

        return $data;
    }

    /**
     * The apprentice dropdown for the add/edit form.
     *
     * add() built this list inline and edit() did not build it at all, so the
     * edit form showed apprentice_id as a number box - the same record, two
     * different forms.
     *
     * @return array
     */
    protected function _apprenticeOptions()
    {
        $options = [];
        $rows = $this->ApprenticeStories->getConnection()
            ->execute('SELECT id, tmm_code, apprentice_id_number FROM apprentices ORDER BY tmm_code')
            ->fetchAll('assoc');
        foreach ($rows as $row) {
            $label = $row['tmm_code'] ?: __('Apprentice #{0}', $row['id']);
            if (!empty($row['apprentice_id_number'])) {
                $label .= ' (' . $row['apprentice_id_number'] . ')';
            }
            $options[$row['id']] = $label;
        }

        return $options;
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

    /**
     * The guide behind the "?" button.
     *
     * The page itself is config/page_guides/ApprenticeStories.php, rendered by
     * Element/page_guide.ctp. The language switch writes the choice to the
     * session so the guide can be read in Indonesian, English or Japanese
     * without changing the language of the whole application first.
     *
     * @return \Cake\Http\Response|null
     */
    public function processFlow()
    {
        if ($lang = $this->request->getQuery('lang')) {
            if (in_array($lang, ['ind', 'eng', 'jpn'], true)) {
                $this->request->getSession()->write('Config.language', $lang);

                return $this->redirect(['action' => 'processFlow']);
            }
        }
    }
}
