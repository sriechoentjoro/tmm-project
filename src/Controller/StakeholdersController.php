<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Stakeholders Controller
 *
 * @property \App\Model\Table\StakeholdersTable $Stakeholders
 */
class StakeholdersController extends AppController
{
    /**
     * Help method - Display help documentation
     *
     * @return void
     */
    public function help()
    {
        // This is just a view, no data processing needed
    }

    /**
     * Process Flow Documentation
     */
    public function processFlow()
    {
        // Handle language switching
        if ($lang = $this->request->getQuery('lang')) {
            if (in_array($lang, ['ind', 'eng', 'jpn'])) {
                $this->request->getSession()->write('Config.language', $lang);
                return $this->redirect(['action' => 'processFlow']);
            }
        }
        
        $this->viewBuilder()->setLayout('process_flow');
    }
}