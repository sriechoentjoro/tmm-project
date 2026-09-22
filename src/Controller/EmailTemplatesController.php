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
        $this->_setPreviewData($emailTemplate);
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
        $this->_setPreviewData($emailTemplate);
    }

    /**
     * What the editor needs to draw the message as it will arrive.
     *
     * Two things, and the point of both is that the preview cannot drift from
     * what is actually sent:
     *
     *   previewChrome  the branded letterhead, rendered here through the same
     *                  view and layout EmailComponent sends through, split in
     *                  two at a sentinel where the body goes. The editor drops
     *                  the body between the halves. Change the layout and the
     *                  preview changes with it, because it is the layout.
     *   previewData    a value for every {{placeholder}} in the template, so
     *                  the preview reads as a message rather than as a form
     *                  with holes in it.
     *
     * @param \App\Model\Entity\EmailTemplate $emailTemplate The template.
     * @return void
     */
    protected function _setPreviewData($emailTemplate)
    {
        $this->set('previewChrome', $this->_previewChrome());
        $this->set('previewData', $this->_previewData($emailTemplate));
    }

    /**
     * The branded letterhead, split where the body belongs.
     *
     * @return array{before: string, after: string}
     */
    protected function _previewChrome()
    {
        $sentinel = '@@TMM_EMAIL_BODY@@';

        $view = new \Cake\View\View($this->request, null, null, [
            'templatePath' => 'Email' . DS . 'html',
            'template' => 'db_template',
            'layout' => 'email_branded',
            'layoutPath' => 'Email' . DS . 'html',
        ]);
        $view->set('content', $sentinel);

        $rendered = $view->render();
        $at = strpos($rendered, $sentinel);
        if ($at === false) {
            // The layout stopped fetching the content block. Rather than show a
            // letterhead with no message in it, show no letterhead.
            return ['before' => '', 'after' => ''];
        }

        return [
            'before' => substr($rendered, 0, $at),
            'after' => substr($rendered, $at + strlen($sentinel)),
        ];
    }

    /**
     * A sample value for every placeholder the template uses.
     *
     * Declared variables come from the 'variables' column, which installations
     * have filled in as JSON, as a comma-separated list, or not at all. What
     * actually decides the list, though, is the text: every {{name}} in the
     * subject or either body gets a value, declared or not, because an
     * undeclared one is exactly the kind of thing a preview should make visible.
     *
     * @param \App\Model\Entity\EmailTemplate $emailTemplate The template.
     * @return array<string, string>
     */
    protected function _previewData($emailTemplate)
    {
        $known = [
            'institution_name' => 'LPK Karya Mandiri',
            'username' => 'lpkkaryamandiri',
            'email' => 'direktur@lpk-contoh.com',
            'director_name' => 'Budi Santoso',
            'registration_url' => 'https://tmm-demo.widagdo.web.id/institution-registration/complete/TOKEN',
            'registration_number' => 'LKM',
            'expiry_date' => (new \Cake\I18n\FrozenTime('+48 hours'))->format('Y-m-d H:i:s'),
            'password' => 'C0ntoh!Sandi',
            'login_url' => 'https://tmm-demo.widagdo.web.id/users/login',
        ];

        $text = implode("\n", [
            (string)$emailTemplate->subject,
            (string)$emailTemplate->body_html,
            (string)$emailTemplate->body_text,
            (string)$emailTemplate->variables,
        ]);

        $data = [];
        if (preg_match_all('/\{\{\s*([A-Za-z0-9_]+)\s*\}\}/', $text, $m)) {
            foreach (array_unique($m[1]) as $name) {
                $data[$name] = isset($known[$name])
                    ? $known[$name]
                    : '[' . $name . ']';
            }
        }

        // Declared but never used: still worth a value, so that adding the
        // placeholder to the body shows something immediately.
        foreach ($this->_declaredVariables($emailTemplate->variables) as $name) {
            if (!isset($data[$name])) {
                $data[$name] = isset($known[$name]) ? $known[$name] : '[' . $name . ']';
            }
        }

        return $data;
    }

    /**
     * Variable names out of the 'variables' column, whatever shape it is in.
     *
     * @param string|null $variables The column value.
     * @return array<int, string>
     */
    protected function _declaredVariables($variables)
    {
        $variables = trim((string)$variables);
        if ($variables === '') {
            return [];
        }

        $decoded = json_decode($variables, true);
        if (is_array($decoded)) {
            // Either ["a","b"] or {"a":"description"} - both are in use.
            $names = $decoded === array_values($decoded) ? $decoded : array_keys($decoded);

            return array_values(array_filter(array_map('strval', $names), 'strlen'));
        }

        $names = preg_split('/[\s,;]+/', $variables, -1, PREG_SPLIT_NO_EMPTY);

        return array_map(function ($n) {
            return trim($n, "{} \t");
        }, $names);
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

    /**
     * The guide behind the "?" button.
     *
     * The page itself is config/page_guides/EmailTemplates.php, rendered by
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
