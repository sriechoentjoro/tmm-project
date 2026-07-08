<?php
/**
 * Adds the menu-linked actions that were missing from existing controllers.
 * Inserts each block before the controller's final closing brace.
 * Skips a controller when the first action name already exists. Safe to re-run.
 */

$patches = [];

$patches['VocationalTrainingInstitutionsController.php'] = <<<'EOT'

    /**
     * Register - menu alias for add()
     */
    public function register()
    {
        return $this->redirect(['action' => 'add']);
    }

    /**
     * Verification worklist - registration status of every LPK
     */
    public function verify()
    {
        $this->paginate = [
            'order' => ['VocationalTrainingInstitutions.is_registered' => 'ASC', 'VocationalTrainingInstitutions.id' => 'DESC'],
        ];
        $institutions = $this->paginate($this->VocationalTrainingInstitutions);
        $this->set(compact('institutions'));
    }
EOT;

$patches['ApprenticeOrdersController.php'] = <<<'EOT'

    /**
     * Order statistics grouped by status
     */
    public function statistics()
    {
        $query = $this->ApprenticeOrders->find();
        $byStatus = $query
            ->select([
                'status',
                'order_count' => $query->func()->count('*'),
                'total_quantity' => $query->func()->sum('quantity'),
                'total_male' => $query->func()->sum('male_trainee_number'),
                'total_female' => $query->func()->sum('female_trainee_number'),
            ])
            ->group('status')
            ->enableHydration(false)
            ->toArray();

        $totals = [
            'orders' => $this->ApprenticeOrders->find()->count(),
            'quantity' => (int)$this->ApprenticeOrders->find()->select(['s' => 'COALESCE(SUM(quantity), 0)'])->enableHydration(false)->first()['s'],
        ];

        $this->set(compact('byStatus', 'totals'));
    }
EOT;

$patches['CandidatesController.php'] = <<<'EOT'

    /**
     * Promotion worklist: candidates not yet promoted to trainee
     */
    public function promoteToTrainee()
    {
        $traineesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Trainees');
        $promotedIds = $traineesTable->find()
            ->where(['candidate_id IS NOT' => null])
            ->extract('candidate_id')
            ->toList();

        $conditions = [];
        if (!empty($promotedIds)) {
            $conditions['Candidates.id NOT IN'] = $promotedIds;
        }

        $this->paginate = ['order' => ['Candidates.id' => 'DESC']];
        $candidates = $this->paginate($this->Candidates->find()->where($conditions));
        $this->set(compact('candidates'));
    }

    /**
     * Promotion history for candidates
     */
    public function promotionHistory()
    {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_masters');
        $histories = \Cake\ORM\TableRegistry::getTableLocator()
            ->get('PromotionHistories', ['connection' => $conn])
            ->find()
            ->where(['source_table' => 'candidates'])
            ->order(['id' => 'DESC'])
            ->limit(500)
            ->toArray();
        $this->set(compact('histories'));
        $this->render('/Element/promotion_history');
    }
EOT;

$patches['TraineeTrainingBatchesController.php'] = <<<'EOT'

    /**
     * Register trainees - menu alias for add()
     */
    public function register()
    {
        return $this->redirect(['action' => 'add']);
    }
EOT;

$patches['TrainingsController.php'] = <<<'EOT'

    /**
     * Training batch locations overview for location shifting
     */
    public function locationShift()
    {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $batches = \Cake\ORM\TableRegistry::getTableLocator()
            ->get('TrainingBatches', ['connection' => $conn])
            ->find()
            ->order(['id' => 'DESC'])
            ->limit(500)
            ->toArray();
        $this->set(compact('batches'));
    }
EOT;

$patches['TraineesController.php'] = <<<'EOT'

    /**
     * Additional trainings list
     */
    public function additionalTraining()
    {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $additionalTrainings = \Cake\ORM\TableRegistry::getTableLocator()
            ->get('AdditionalTrainings', ['connection' => $conn])
            ->find()
            ->order(['id' => 'DESC'])
            ->limit(500)
            ->toArray();
        $this->set(compact('additionalTrainings'));
    }

    /**
     * Promotion worklist: trainees who passed training
     */
    public function promoteToApprentice()
    {
        $this->paginate = ['order' => ['Trainees.id' => 'DESC']];
        $trainees = $this->paginate($this->Trainees->find()->where(['Trainees.is_training_pass' => 1]));
        $this->set(compact('trainees'));
    }

    /**
     * Promotion checklist: pass flags per trainee
     */
    public function promotionChecklist()
    {
        $this->paginate = ['order' => ['Trainees.id' => 'DESC']];
        $trainees = $this->paginate($this->Trainees);
        $this->set(compact('trainees'));
    }

    /**
     * Promotion history for trainees
     */
    public function promotionHistory()
    {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_masters');
        $histories = \Cake\ORM\TableRegistry::getTableLocator()
            ->get('PromotionHistories', ['connection' => $conn])
            ->find()
            ->where(['source_table' => 'trainees'])
            ->order(['id' => 'DESC'])
            ->limit(500)
            ->toArray();
        $this->set(compact('histories'));
        $this->render('/Element/promotion_history');
    }
EOT;

$patches['TraineeTrainingTestScoresController.php'] = <<<'EOT'

    /**
     * Daily score entry - scores of the most recent test date
     */
    public function daily()
    {
        $latest = $this->TraineeTrainingTestScores->find()
            ->select(['d' => 'MAX(test_date)'])
            ->enableHydration(false)
            ->first();
        $latestDate = $latest && $latest['d'] ? $latest['d'] : null;

        $scores = [];
        if ($latestDate) {
            $scores = $this->TraineeTrainingTestScores->find()
                ->where(['test_date' => $latestDate])
                ->order(['id' => 'ASC'])
                ->limit(500)
                ->toArray();
        }
        $this->set(compact('scores', 'latestDate'));
    }

    /**
     * Pass/fail report - average score per trainee
     */
    public function report()
    {
        $query = $this->TraineeTrainingTestScores->find();
        $rows = $query
            ->select([
                'trainee_id',
                'tests' => $query->func()->count('*'),
                'avg_score' => $query->func()->avg('score'),
                'min_score' => $query->func()->min('score'),
                'max_score' => $query->func()->max('score'),
            ])
            ->group('trainee_id')
            ->enableHydration(false)
            ->toArray();
        $this->set(compact('rows'));
    }
EOT;

$patches['CandidateDocumentsController.php'] = <<<'EOT'

    /**
     * Submission checklist of candidate documents
     */
    public function submissionChecklist()
    {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_lpk_candidate_documents');
        $checklist = \Cake\ORM\TableRegistry::getTableLocator()
            ->get('CandidateSubmissionDocuments', ['connection' => $conn])
            ->find()
            ->order(['id' => 'DESC'])
            ->limit(500)
            ->toArray();
        $this->set(compact('checklist'));
    }
EOT;

$patches['TraineeInstallmentsController.php'] = <<<'EOT'

    /**
     * Payment tracking - all installments with paid status
     */
    public function tracking()
    {
        $this->paginate = ['order' => ['TraineeInstallments.id' => 'DESC']];
        $installments = $this->paginate($this->TraineeInstallments);
        $this->set(compact('installments'));
    }

    /**
     * Receipts - fully paid installments
     */
    public function receipts()
    {
        $this->paginate = ['order' => ['TraineeInstallments.id' => 'DESC']];
        $installments = $this->paginate($this->TraineeInstallments->find()->where(['TraineeInstallments.is_paid_off' => 1]));
        $this->set(compact('installments'));
    }
EOT;

foreach ($patches as $file => $code) {
    $path = __DIR__ . '/src/Controller/' . $file;
    $src = file_get_contents($path);
    if ($src === false) {
        echo "MISSING $file\n";
        continue;
    }
    // first action name in the block, e.g. "public function register("
    preg_match('/public function (\w+)\(/', $code, $m);
    if (strpos($src, 'function ' . $m[1] . '(') !== false) {
        echo "skip   $file ({$m[1]} exists)\n";
        continue;
    }
    $pos = strrpos($src, '}');
    $src = substr($src, 0, $pos) . $code . "\n}\n";
    file_put_contents($path, $src);
    echo "patched $file\n";
}
echo "done\n";
