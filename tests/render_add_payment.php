<?php
/**
 * The add-payment screen, and the warning that stops a payment being recorded
 * twice.
 *
 * A second copy of a payment moves the balance by a whole payment and nothing
 * later notices: the chain recalculates from what is on file, so it recalculates
 * correctly from the wrong rows. The trainee's account then says they have paid
 * more than they have, and the difference is found, if it is found, by somebody
 * comparing a receipt book to a screen.
 *
 * Two genuine payments of one amount on one day are possible, so the screen does
 * not refuse - it stops, names the payment already on file, and asks. Which is
 * only useful if it really names it.
 */
require __DIR__ . '/lib/harness.php';

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

// FormHelper resolves an entity to its table to read the schema, so the table
// has to answer. One SQLite file with the three columns the form touches is
// enough, and keeps the render honest: a control for a column that is not
// there would fail here rather than on the screen.
$db = sys_get_temp_dir() . '/tmm_add_payment.sqlite';
$conn = sqliteConnections(['cms_tmm_trainee_accountings', 'default'], $db);
$conn->execute('CREATE TABLE trainee_installments (id INTEGER PRIMARY KEY,
    trainee_id INTEGER, master_transaction_category_id INTEGER,
    master_currency_id INTEGER, payment_amount INTEGER, payment_date DATE,
    full_payment_amount INTEGER, payment_accummulated INTEGER,
    unpaid_amount INTEGER, is_paid_off INTEGER, notes VARCHAR(255))');

$installments = TableRegistry::getTableLocator()->get('TraineeInstallments');

/**
 * A blank payment, of the kind add() hands the form.
 *
 * @return \Cake\Datasource\EntityInterface
 */
function blankPayment()
{
    global $installments;

    return $installments->newEntity();
}

/**
 * A payment already on file.
 *
 * @param int $id Its id.
 * @param int|null $amount Rupiah.
 * @param string|null $date When it was recorded.
 * @return \Cake\Datasource\EntityInterface
 */
function onFile($id, $amount, $date)
{
    global $installments;
    $row = $installments->newEntity(['payment_amount' => $amount,
        'payment_date' => $date === null ? null : new FrozenDate($date)],
        ['validate' => false, 'accessibleFields' => ['*' => true]]);
    $row->id = $id;

    return $row;
}

$trainees = [1 => 'Budi Santoso (TMM-001)', 2 => 'Nur Aini (TMM-002)'];
$traineeStatus = [
    1 => ['accumulated' => 4000000, 'full' => 24000000, 'unpaid' => 20000000,
          'is_paid_off' => false],
];

/**
 * Render the add screen in one state.
 *
 * @param string $label What state this is.
 * @param array $duplicates Payments already on file that match.
 * @param array $submitted What was typed.
 * @return string
 */
function addPayment($label, array $duplicates = [], array $submitted = [])
{
    global $trainees, $traineeStatus;

    return renderClean($label, 'add', [
        'traineeInstallment' => blankPayment(),
        'trainees' => $trainees,
        'traineeStatus' => $traineeStatus,
        'masterTransactionCategories' => [3 => 'Biaya Pelatihan'],
        'masterCurrencies' => [66 => 'IDR'],
        'bookCurrencyId' => 66,
        'duplicates' => $duplicates,
        'submitted' => $submitted,
    ], ['controller' => 'TraineeInstallments', 'templatePath' => 'TraineeInstallments',
        'url' => '/trainee-installments/add',
        'params' => ['controller' => 'TraineeInstallments', 'action' => 'add']]);
}

echo "  an ordinary blank form\n";
$html = addPayment('renders');
checkTrue('has the trainee list', strpos($html, 'Budi Santoso (TMM-001)') !== false);
checkTrue('and a currency already chosen, so nobody has to know which',
    strpos($html, 'value="66"') !== false);
checkTrue('no warning, because there is nothing to warn about',
    strpos($html, 'class="duplicate-warning"') === false);
// The confirm flag is what turns a refusal into a deliberate second copy. It
// must not be on a form that was never stopped.
checkTrue('and no confirmation flag hidden in the form',
    strpos($html, 'confirm_duplicate') === false);

echo "  a payment that is already on file\n";
$html = addPayment('renders', [onFile(41, 2000000, '2026-05-10')],
    ['payment_amount' => '2000000', 'payment_date' => '2026-05-10']);
checkTrue('the screen says so', strpos($html, 'class="duplicate-warning"') !== false);
checkTrue('and says nothing was saved',
    strpos($html, 'Nothing has been saved') !== false);
checkTrue('the payment already on file is named by its number',
    strpos($html, '#41') !== false);
checkTrue('and linked, so it can be opened before deciding',
    strpos($html, 'trainee-installments/view/41') !== false);
checkTrue('with its amount written out', strpos($html, '2.000.000') !== false);
checkTrue('and its date', strpos($html, '2026-05-10') !== false);

echo "  and what to do about it\n";
checkTrue('the risk of a second copy is stated',
    strpos($html, 'nothing later will notice') !== false);
// Two genuine payments on one day are possible, so the screen must not refuse.
checkTrue('recording it anyway is offered', strpos($html, 'Record it anyway') !== false);
checkTrue('and that is what the confirmation flag is for',
    strpos($html, 'confirm_duplicate') !== false);

echo "  more than one already on file\n";
$html = addPayment('renders', [
    onFile(41, 2000000, '2026-05-10'),
    onFile(52, 2000000, '2026-05-10'),
], ['payment_amount' => '2000000', 'payment_date' => '2026-05-10']);
checkTrue('the count is said, not just "already on file"',
    strpos($html, 'already on file 2 times') !== false);
checkTrue('and both are listed', strpos($html, '#41') !== false
    && strpos($html, '#52') !== false);

echo "  a payment whose date did not survive\n";
// payment_date is nullable, and a row with none must not take the warning down
// with it - that row is exactly the one somebody needs to look at.
$html = addPayment('renders', [onFile(7, 500000, null)]);
checkTrue('is still listed', strpos($html, '#7') !== false);
checkTrue('with its date shown as unknown rather than as nothing',
    strpos($html, '?') !== false);

echo "  the running total the screen shows\n";
$html = addPayment('renders');
checkTrue('the trainee figures are handed to the page',
    strpos($html, '"accumulated":4000000') !== false);
checkTrue('including what is still owed', strpos($html, '"unpaid":20000000') !== false);
// json_encode of an empty PHP array gives [], which reads as a list in
// JavaScript; the template uses stdClass so a lookup by id cannot go wrong.
$html = renderClean('a screen with no figures at all renders', 'add', [
    'traineeInstallment' => blankPayment(), 'trainees' => $trainees,
    'traineeStatus' => [], 'masterTransactionCategories' => [],
    'masterCurrencies' => [66 => 'IDR'], 'bookCurrencyId' => 66,
    'duplicates' => [], 'submitted' => [],
], ['controller' => 'TraineeInstallments', 'templatePath' => 'TraineeInstallments',
    'url' => '/trainee-installments/add',
    'params' => ['controller' => 'TraineeInstallments', 'action' => 'add']]);
checkTrue('and hands over an object, not an empty list',
    strpos($html, 'var traineeStatus = {}') !== false);

finish($db);
