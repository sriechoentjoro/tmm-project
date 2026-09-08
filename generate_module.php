<?php
/**
 * Minimal module generator (bake replacement — TwigView unavailable on this server).
 * Generates Entity, Table, Controller and index/view/add/edit templates for the
 * modules listed below, following the app's existing conventions. Skips any file
 * that already exists, so it is safe to re-run.
 *
 * Usage: php generate_module.php
 */

$dbUser = 'tmm';
$dbPass = 'YOUR_DB_PASSWORD';

// name, singular, table, database (= connection name), readonly, templatesOnly
$modules = [
    ['TraineeRecordPasports',        'TraineeRecordPasport',        'trainee_record_pasports',          'cms_tmm_trainee_documents',           false, false],
    ['TraineeRecordCoeVisas',        'TraineeRecordCoeVisa',        'trainee_record_coe_visas',         'cms_tmm_trainee_documents',           false, false],
    ['TraineeRecordMedicalCheckUps', 'TraineeRecordMedicalCheckUp', 'trainee_record_medical_check_ups', 'cms_tmm_trainee_documents',           false, false],
    ['TraineeSubmissionDocuments',   'TraineeSubmissionDocument',   'trainee_submission_documents',     'cms_tmm_trainee_documents',           false, false],
    ['Tickets',                      'Ticket',                      'tickets',                          'cms_tmm_trainee_document_ticketings', false, false],
    ['ChartOfAccounts',              'ChartOfAccount',              'chart_of_accounts',                'cms_tmm_trainee_accountings',         false, false],
    ['Journals',                     'Journal',                     'journals',                         'cms_tmm_trainee_accountings',         false, false],
    ['TraineeCertificates',          'TraineeCertificate',          'trainee_certificates',             'cms_masters',                         false, false],
    ['TraineeNameCards',             'TraineeNameCard',             'trainee_name_cards',               'cms_masters',                         false, false],
    ['ApprenticeStories',            'ApprenticeStory',             'apprentice_stories',               'cms_tmm_apprentices',                 false, false],
    ['PostApprentices',              'PostApprentice',              'post_apprentices',                 'cms_tmm_apprentices',                 false, false],
    ['ApprenticeDocuments',          'ApprenticeDocument',          'apprentice_submission_documents',  'cms_tmm_apprentice_documents',        false, false],
    ['LpkPhysicalTests',             'LpkPhysicalTest',             'candidate_record_placement_tests', 'cms_lpk_candidates',                  false, false],
    ['EmailTemplates',               'EmailTemplate',               'email_templates',                  'cms_authentication_authorization',    false, false],
    ['Audit',                        'Audit',                       'logs',                             'cms_authentication_authorization',    true,  false],
    // controller + model already exist; only the CRUD templates are missing
    ['TraineeInstallments',          'TraineeInstallment',          'trainee_installments',             'cms_tmm_trainee_accountings',         false, true],
    ['TraineeTrainingTestScores',    'TraineeTrainingTestScore',    'trainee_training_test_scores',     'cms_tmm_trainee_training_scorings',   false, true],
    ['MasterCurrencies',             'MasterCurrency',              'master_currencies',                'cms_tmm_trainee_accountings',         false, true],
    ['MasterPaymentMethods',         'MasterPaymentMethod',         'master_payment_methods',           'cms_tmm_trainee_accountings',         false, true],
    ['MasterTransactionCategories',  'MasterTransactionCategory',   'master_transaction_categories',    'cms_tmm_trainee_accountings',         false, true],
    ['MasterTrainingCompetencies',   'MasterTrainingCompetency',    'master_training_competencies',     'cms_tmm_trainee_training_scorings',   false, true],
    ['MasterTrainingTestScoreGrades','MasterTrainingTestScoreGrade','master_training_test_score_grades','cms_tmm_trainee_training_scorings',   false, true],
    ['TraineeScoreAverages',         'TraineeScoreAverage',         'trainee_score_averages',           'cms_tmm_trainee_training_scorings',   false, true],
    ['CandidateSubmissionDocuments', 'CandidateSubmissionDocument', 'candidate_submission_documents',   'cms_lpk_candidate_documents',         false, true],
    ['ApprenticeFlights',            'ApprenticeFlight',            'apprentice_flights',               'cms_tmm_apprentice_document_ticketings', false, true],
];

$appRoot = __DIR__;
$pdo = new PDO('mysql:host=localhost', $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

function columnsFor(PDO $pdo, $db, $table)
{
    $stmt = $pdo->prepare("SELECT column_name AS name, data_type AS type FROM information_schema.columns
        WHERE table_schema = ? AND table_name = ? ORDER BY ordinal_position");
    $stmt->execute([$db, $table]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function pickDisplayField(array $cols)
{
    $names = array_column($cols, 'name');
    foreach (['name', 'title', 'subject', 'code', 'document_number', 'account_name', 'description'] as $cand) {
        if (in_array($cand, $names)) {
            return $cand;
        }
    }
    return isset($names[1]) ? $names[1] : 'id';
}

function writeIfMissing($path, $content)
{
    if (file_exists($path)) {
        echo "skip   $path\n";
        return;
    }
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0755, true);
    }
    file_put_contents($path, $content);
    echo "create $path\n";
}

// ---- file templates use %TOKENS% replaced via strtr; no PHP interpolation ----

$entityTpl = <<<'EOT'
<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * %SINGULAR% Entity
 */
class %SINGULAR% extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

EOT;

$tableTpl = <<<'EOT'
<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * %NAME% Model
 */
class %NAME%Table extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('%TABLE%');
        $this->setDisplayField('%DISPLAY%');
        $this->setPrimaryKey('id');
%TIMESTAMP%    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return '%DB%';
    }
}

EOT;

$controllerCrudTpl = <<<'EOT'
<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * %NAME% Controller
 *
 * @property \App\Model\Table\%NAME%Table $%NAME%
 */
class %NAME%Controller extends AppController
{
    public function index()
    {
        $this->paginate = ['order' => ['%NAME%.id' => 'DESC']];
        $%CVAR% = $this->paginate($this->%NAME%);
        $this->set(compact('%CVAR%'));
    }

    public function view($id = null)
    {
        $%SVAR% = $this->%NAME%->get($id);
        $this->set('%SVAR%', $%SVAR%);
    }

    public function add()
    {
        $%SVAR% = $this->%NAME%->newEntity();
        if ($this->request->is('post')) {
            $%SVAR% = $this->%NAME%->patchEntity($%SVAR%, $this->request->getData());
            if ($this->%NAME%->save($%SVAR%)) {
                $this->Flash->success(__('The %HSING% has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The %HSING% could not be saved. Please, try again.'));
        }
        $this->set('%SVAR%', $%SVAR%);
    }

    public function edit($id = null)
    {
        $%SVAR% = $this->%NAME%->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $%SVAR% = $this->%NAME%->patchEntity($%SVAR%, $this->request->getData());
            if ($this->%NAME%->save($%SVAR%)) {
                $this->Flash->success(__('The %HSING% has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The %HSING% could not be saved. Please, try again.'));
        }
        $this->set('%SVAR%', $%SVAR%);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $%SVAR% = $this->%NAME%->get($id);
        if ($this->%NAME%->delete($%SVAR%)) {
            $this->Flash->success(__('The %HSING% has been deleted.'));
        } else {
            $this->Flash->error(__('The %HSING% could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

EOT;

$controllerReadonlyTpl = <<<'EOT'
<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * %NAME% Controller (read-only)
 *
 * @property \App\Model\Table\%NAME%Table $%NAME%
 */
class %NAME%Controller extends AppController
{
    public function index()
    {
        $this->paginate = ['order' => ['%NAME%.id' => 'DESC']];
        $%CVAR% = $this->paginate($this->%NAME%);
        $this->set(compact('%CVAR%'));
    }

    public function view($id = null)
    {
        $%SVAR% = $this->%NAME%->get($id);
        $this->set('%SVAR%', $%SVAR%);
    }
}

EOT;

$indexTpl = <<<'EOT'
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\%SINGULAR%[]|\Cake\Collection\CollectionInterface $%CVAR%
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <h2 style="margin: 0;"><?= __('%HNAME%') ?></h2>%ADDBUTTON%
    </div>
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div class="%CVAR% index content">
        <table class="table" style="border-collapse: collapse; width: 100%; min-width: 800px;">
            <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                <tr>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" class="actions"><?= __('Actions') ?></th>
%THCELLS%                </tr>
            </thead>
            <tbody>
                <?php foreach ($%CVAR% as $row): ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 10px 12px; white-space: nowrap;" class="actions">
%ROWACTIONS%                    </td>
%TDCELLS%                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="paginator" style="margin-top: 15px;">
    <ul class="pagination">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
</div>

EOT;

$viewTpl = <<<'EOT'
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\%SINGULAR% $%SVAR%
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('%HSING%') ?> #<?= h($%SVAR%->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
%EDITLINK%        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
%VIEWROWS%        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>

EOT;

$formTpl = <<<'EOT'
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\%SINGULAR% $%SVAR%
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('%VERB% %HSING%') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($%SVAR%) ?>
    <fieldset>
        <?php
%FORMFIELDS%        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>

EOT;

foreach ($modules as list($name, $singular, $table, $db, $readonly, $templatesOnly)) {
    $cols = columnsFor($pdo, $db, $table);
    if (!$cols) {
        echo "ERROR: no columns for $db.$table — skipped\n";
        continue;
    }
    $names = array_column($cols, 'name');
    $displayField = pickDisplayField($cols);
    $hasTimestamp = in_array('created', $names) && in_array('modified', $names);

    $tokens = [
        '%NAME%'     => $name,
        '%SINGULAR%' => $singular,
        '%TABLE%'    => $table,
        '%DB%'       => $db,
        '%DISPLAY%'  => $displayField,
        '%CVAR%'     => lcfirst($name),
        '%SVAR%'     => lcfirst($singular),
        '%HNAME%'    => trim(preg_replace('/(?<!^)[A-Z]/', ' $0', $name)),
        '%HSING%'    => trim(preg_replace('/(?<!^)[A-Z]/', ' $0', $singular)),
        '%TIMESTAMP%' => $hasTimestamp ? "\n        \$this->addBehavior('Timestamp');\n" : "\n",
    ];

    if (!$templatesOnly) {
        writeIfMissing("$appRoot/src/Model/Entity/$singular.php", strtr($entityTpl, $tokens));
        writeIfMissing("$appRoot/src/Model/Table/{$name}Table.php", strtr($tableTpl, $tokens));
        writeIfMissing(
            "$appRoot/src/Controller/{$name}Controller.php",
            strtr($readonly ? $controllerReadonlyTpl : $controllerCrudTpl, $tokens)
        );
    }

    // ---- templates ----
    $indexCols = ['id'];
    if ($displayField !== 'id') {
        $indexCols[] = $displayField;
    }
    foreach ($cols as $c) {
        if (count($indexCols) >= 8) break;
        if (in_array($c['name'], $indexCols)) continue;
        if (in_array($c['type'], ['text', 'longtext', 'mediumtext', 'blob', 'longblob'])) continue;
        if (preg_match('/password|token/i', $c['name'])) continue;
        $indexCols[] = $c['name'];
    }

    $thCells = $tdCells = '';
    foreach ($indexCols as $cn) {
        $thCells .= "                    <th style=\"padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;\" scope=\"col\"><?= \$this->Paginator->sort('$cn') ?></th>\n";
        $tdCells .= "                    <td style=\"padding: 10px 12px;\"><?= h(\$row['$cn']) ?></td>\n";
    }

    $rowActions = "                        <?= \$this->Html->link(__('View'), ['action' => 'view', \$row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>\n";
    if (!$readonly) {
        $rowActions .= "                        <?= \$this->Html->link(__('Edit'), ['action' => 'edit', \$row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>\n";
    }
    $addButton = $readonly ? '' : "\n        <div style=\"display: flex; align-items: center; gap: 10px;\">\n            <?= \$this->Html->link('<i class=\"fas fa-plus\"></i> ' . __('Add New'), ['action' => 'add'], ['class' => 'btn-export-light', 'escape' => false]) ?>\n        </div>";

    $viewRows = '';
    foreach ($cols as $c) {
        $cn = $c['name'];
        if (preg_match('/password|token/i', $cn)) continue;
        $label = ucwords(str_replace('_', ' ', $cn));
        $svar = $tokens['%SVAR%'];
        $viewRows .= "            <tr><th style=\"padding: 8px 12px; text-align: left; width: 220px;\"><?= __('$label') ?></th><td style=\"padding: 8px 12px;\"><?= h(\${$svar}['$cn']) ?></td></tr>\n";
    }
    $editLink = $readonly ? '' : "            <?= \$this->Html->link(__('Edit'), ['action' => 'edit', \${$tokens['%SVAR%']}['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>\n";

    $tplTokens = $tokens + [
        '%THCELLS%' => $thCells,
        '%TDCELLS%' => $tdCells,
        '%ROWACTIONS%' => $rowActions,
        '%ADDBUTTON%' => $addButton,
        '%VIEWROWS%' => $viewRows,
        '%EDITLINK%' => $editLink,
    ];

    writeIfMissing("$appRoot/src/Template/$name/index.ctp", strtr($indexTpl, $tplTokens));
    writeIfMissing("$appRoot/src/Template/$name/view.ctp", strtr($viewTpl, $tplTokens));

    if (!$readonly) {
        $formFields = '';
        foreach ($cols as $c) {
            $cn = $c['name'];
            if (in_array($cn, ['id', 'created', 'modified'])) continue;
            $formFields .= "        echo \$this->Form->control('$cn');\n";
        }
        foreach (['add' => 'Add', 'edit' => 'Edit'] as $tpl => $verb) {
            writeIfMissing(
                "$appRoot/src/Template/$name/$tpl.ctp",
                strtr($formTpl, $tplTokens + ['%VERB%' => $verb, '%FORMFIELDS%' => $formFields])
            );
        }
    }
}

echo "done\n";
