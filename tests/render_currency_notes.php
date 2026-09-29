<?php
/**
 * Where the screens say what currency a figure is in, and what they left out.
 *
 * This application keeps a currency per payment and converts between none of
 * them. A total is a plain sum, so adding a payment recorded in yen to one
 * recorded in rupiah produces a number that is not money in any currency - and
 * it looks exactly like a number that is. There is no rate table to fix that
 * with, so the screens say it instead: the total names the currency it is
 * written in, and says how many rows were added to it that are not in that
 * currency.
 *
 * The same shape appears on the journals page: a source record whose currency is
 * not the book currency is held back rather than posted, because posting it would
 * put a figure in the books that the books cannot mean.
 */
require __DIR__ . '/lib/harness.php';

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_currency_notes.sqlite';
$conn = sqliteConnections(['cms_tmm_trainee_accountings', 'default'], $db);
$conn->execute('CREATE TABLE trainee_installments (id INTEGER PRIMARY KEY,
    trainee_id INTEGER, master_transaction_category_id INTEGER,
    master_currency_id INTEGER, payment_amount INTEGER, payment_date DATE,
    full_payment_amount INTEGER, payment_accummulated INTEGER,
    unpaid_amount INTEGER, is_paid_off INTEGER, notes VARCHAR(255))');
$installments = TableRegistry::getTableLocator()->get('TraineeInstallments');

/**
 * A payment as the tracking screen lists it.
 *
 * @param int $id Its id.
 * @param int $trainee Whose it is.
 * @param int $amount How much.
 * @param int $currency Which currency id.
 * @return \Cake\Datasource\EntityInterface
 */
function payment($id, $trainee, $amount, $currency)
{
    global $installments;
    $row = $installments->newEntity([
        'trainee_id' => $trainee, 'payment_amount' => $amount,
        'master_currency_id' => $currency, 'master_transaction_category_id' => 3,
        'payment_date' => new FrozenDate('2026-05-10'),
        'payment_accummulated' => $amount, 'full_payment_amount' => 24000000,
        'unpaid_amount' => 24000000 - $amount, 'is_paid_off' => false,
    ], ['validate' => false, 'accessibleFields' => ['*' => true]]);
    $row->id = $id;

    return $row;
}

/**
 * Render the tracking screen with this summary.
 *
 * @param string $label What state this is.
 * @param array $summary The four figures plus otherCurrency.
 * @param array $rows The payments listed.
 * @return string
 */
function tracking($label, array $summary, array $rows = [])
{
    return renderClean($label, 'tracking', [
        'installments' => $rows,
        'traineeNames' => [1 => 'Budi Santoso (TMM-001)', 2 => 'Nur Aini (TMM-002)'],
        'categoryNames' => [3 => 'Biaya Pelatihan'],
        'currencyNames' => [66 => 'IDR', 12 => 'JPY'],
        'paymentProgress' => [],
        'summary' => $summary + ['payingTrainees' => count($rows), 'paidOff' => 0,
            'collected' => 0, 'outstanding' => 0, 'otherCurrency' => 0],
        'bookCurrency' => ['id' => 66, 'code' => 'IDR', 'title' => 'Rupiah',
            'resolved' => 'code'],
        'traineesWithoutCost' => [],
        'filterTrainee' => 0,
        'filterPaid' => '',
    ], ['controller' => 'TraineeInstallments', 'templatePath' => 'TraineeInstallments',
        'url' => '/trainee-installments/tracking',
        'params' => ['controller' => 'TraineeInstallments', 'action' => 'tracking']]);
}

echo "  every payment in the book currency\n";
$html = tracking('renders', ['collected' => 4000000, 'outstanding' => 20000000,
    'otherCurrency' => 0], [payment(1, 1, 4000000, 66)]);
checkTrue('the totals are shown', strpos($html, 'Rp 4.000.000') !== false);
// Nothing was mixed, so there is nothing to warn about - and a warning that
// appears when nothing is wrong is one nobody reads when something is.
checkTrue('and no currency note, because nothing was mixed',
    strpos($html, 'class="currency-note"') === false);

echo "  a payment in another currency added into the total\n";
$html = tracking('renders', ['collected' => 4500000, 'outstanding' => 19500000,
    'otherCurrency' => 1], [payment(1, 1, 4000000, 66), payment(2, 2, 500000, 12)]);
checkTrue('the note appears', strpos($html, 'class="currency-note"') !== false);
checkTrue('it names the currency the totals are written in',
    strpos($html, 'IDR') !== false);
checkTrue('says how many rows are not in it', strpos($html, '1 trainee') !== false);
checkTrue('and that nothing converts between them',
    strpos($html, 'nothing here converts between them') !== false);
checkTrue('while each line still shows its own currency',
    strpos($html, 'JPY') !== false);

echo "  several of them\n";
$html = tracking('renders', ['collected' => 5000000, 'outstanding' => 19000000,
    'otherCurrency' => 3], [payment(1, 1, 4000000, 66)]);
checkTrue('the count is the number of trainees, not of payments',
    strpos($html, '3 trainee') !== false);

echo "  nothing recorded at all\n";
// An empty screen still has to be a screen: a page that renders nothing reads
// as a broken one rather than as an empty ledger.
$html = tracking('renders', ['collected' => 0, 'outstanding' => 0,
    'otherCurrency' => 0], []);
checkTrue('the summary is still there', strpos($html, 'Outstanding') !== false);
checkTrue('and no note is invented', strpos($html, 'class="currency-note"') === false);

// ======================================================= the journals page
/**
 * Render the auto-generated journals page.
 *
 * @param string $label What state this is.
 * @param array $vars blocked, sourceErrors, bookCurrency and the rest.
 * @return string
 */
function journals($label, array $vars)
{
    $mappings = ['ticket' => ['label' => 'Flight Ticket Cost', 'icon' => 'fa-plane',
        'ref_prefix' => 'TKT', 'debit_code' => '5200', 'credit_code' => '2000']];

    return renderClean($label, 'auto_generated', $vars + [
        'pendingByType' => [], 'pendingTotal' => 0, 'generatedCount' => 0,
        'mappings' => $mappings,
        'coa' => ['5200' => ['id' => 6, 'name' => 'Transportation', 'type' => 'Expense'],
                  '2000' => ['id' => 2, 'name' => 'Accounts Payable', 'type' => 'Liability']],
        'blocked' => [], 'sourceErrors' => [],
        'bookCurrency' => ['id' => 66, 'code' => 'IDR', 'title' => 'Rupiah',
            'resolved' => 'code'],
    ], ['controller' => 'Journals', 'templatePath' => 'Journals',
        'url' => '/journals/auto-generated',
        'params' => ['controller' => 'Journals', 'action' => 'autoGenerated']]);
}

echo "  a source record held back\n";
$blocked = [[
    'type' => 'ticket', 'reference_no' => 'TKT-9', 'subject' => 'Nur Aini',
    'date' => '2026-05-11', 'amount' => 45000, 'currency' => 'JPY',
    'block_reason' => 'Recorded in JPY, and the books are kept in IDR. '
        . 'Nothing here converts between them.',
]];
$html = journals('renders', ['blocked' => $blocked]);
// Matched as markup, not as a name: the class is in the stylesheet this
// template carries, so it is on the page whether the card is or not.
checkTrue('the held-back card appears',
    strpos($html, 'class="card card-blocked"') !== false);
checkTrue('and is counted', strpos($html, 'Held back') !== false);
checkTrue('the record is named', strpos($html, 'TKT-9') !== false);
checkTrue('with the amount as recorded, in its own currency',
    strpos($html, 'JPY 45.000') !== false);
checkTrue('and the reason it is held back', strpos($html, 'the books are kept in IDR') !== false);
// Held back means not counted and not posted. Saying so is the point of
// listing them rather than passing over them quietly.
checkTrue('it is said that Generate All passes over them',
    strpos($html, 'Generate All passes over them') !== false);

echo "  a source that could not be read at all\n";
// Which is not the same as having nothing left to journalize - the page used to
// swallow the exception and show an empty list, which reads as "all done".
$html = journals('renders', ['sourceErrors' =>
    ["tickets: SQLSTATE[42S02] Base table or view not found"]]);
checkTrue('is reported', strpos($html, 'could not be read') !== false);
checkTrue('and the counts are called incomplete',
    strpos($html, 'the counts below are incomplete') !== false);
checkTrue('and it is distinguished from having nothing left to do',
    strpos($html, 'not the same as having nothing left to journalize') !== false);
checkTrue('with the error quoted', strpos($html, '42S02') !== false);

echo "  nothing wrong at all\n";
$html = journals('renders', []);
checkTrue('no held-back card',
    strpos($html, 'class="card card-blocked"') === false);
checkTrue('and no source error', strpos($html, 'could not be read') === false);

finish($db);
