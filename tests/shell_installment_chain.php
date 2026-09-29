<?php
/**
 * The running figures on a trainee's payments, rebuilt from the payments.
 *
 * Three columns on every installment row say where the trainee stands:
 * payment_accummulated, unpaid_amount and is_paid_off. They were written by
 * whichever screen last touched the row, so a payment edited or deleted left the
 * rows after it saying what they said before - and the arithmetic still added up
 * within each row, so nothing looked wrong. A trainee's balance could be out by a
 * whole payment with every figure on the page internally consistent.
 *
 * rebuildChain() is the one authority now. What it must get right is not only the
 * sums: it must leave a trainee with no payments alone rather than zeroing them,
 * it must be safe to run twice, and it must never change the payments themselves.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_installment_chain.sqlite';
// Saving a row through the ORM resolves the belongsTo associations, so the
// tables behind them have to exist - and Trainees is on a different connection
// in the real system, which is the arrangement worth reproducing rather than
// designing around.
$conn = sqliteConnections(['cms_tmm_trainee_accountings', 'cms_tmm_trainees',
    'default'], $db);
$conn->execute('CREATE TABLE trainee_installments (id INTEGER PRIMARY KEY,
    trainee_id INTEGER, master_transaction_category_id INTEGER,
    master_currency_id INTEGER, payment_amount INTEGER, payment_date DATE,
    full_payment_amount INTEGER, payment_accummulated INTEGER,
    unpaid_amount INTEGER, is_paid_off INTEGER, notes VARCHAR(255))');
$conn->execute('CREATE TABLE trainees (id INTEGER PRIMARY KEY, name VARCHAR(255),
    tmm_code VARCHAR(50))');
$conn->execute('CREATE TABLE master_transaction_categories (id INTEGER PRIMARY KEY,
    title VARCHAR(255))');
$conn->execute('CREATE TABLE master_currencies (id INTEGER PRIMARY KEY,
    code VARCHAR(10), title VARCHAR(255))');
$conn->execute("INSERT INTO trainees (id, name, tmm_code) VALUES
    (1, 'Budi Santoso', 'TMM-001'), (2, 'Nur Aini', 'TMM-002'),
    (3, 'Sri Wahyuni', 'TMM-003'), (4, 'Ali Basuki', 'TMM-004'),
    (5, 'Dewi Kartika', 'TMM-005')");
$conn->execute("INSERT INTO master_transaction_categories (id, title)
    VALUES (3, 'Biaya Pelatihan')");
$conn->execute("INSERT INTO master_currencies (id, code, title)
    VALUES (66, 'IDR', 'Rupiah')");

$installments = TableRegistry::getTableLocator()->get('TraineeInstallments');

/**
 * Put a row on file exactly as given, running figures and all - including wrong
 * ones, which is the state this exists to correct.
 *
 * @param array $row Column values.
 * @return void
 */
function onFile(array $row)
{
    global $conn;
    $row += ['master_currency_id' => 66, 'master_transaction_category_id' => 3,
        'payment_date' => '2026-05-10', 'full_payment_amount' => 0,
        'payment_accummulated' => 0, 'unpaid_amount' => 0, 'is_paid_off' => 0];
    $conn->insert('trainee_installments', $row);
}

/**
 * The rows of one trainee, oldest first, as columns worth checking.
 *
 * @param int $traineeId Whose.
 * @return array
 */
function chain($traineeId)
{
    global $installments;
    $out = [];
    foreach ($installments->find()->where(['trainee_id' => $traineeId])
            ->order(['id' => 'ASC']) as $row) {
        $out[] = [
            'paid' => (int)$row->payment_amount,
            'full' => (int)$row->full_payment_amount,
            'accumulated' => (int)$row->payment_accummulated,
            'unpaid' => (int)$row->unpaid_amount,
            'settled' => (int)$row->is_paid_off,
        ];
    }

    return $out;
}

/** @return array The summary rebuildChain() reports. */
function rebuild($traineeId)
{
    global $installments;

    return $installments->rebuildChain($traineeId);
}

echo "  three payments against a cost of 24 million\n";
// Only the opening row carries the owing cost; the later rows were written by
// screens that did not know it.
onFile(['id' => 1, 'trainee_id' => 1, 'payment_amount' => 4000000,
    'full_payment_amount' => 24000000, 'payment_accummulated' => 4000000,
    'unpaid_amount' => 20000000]);
onFile(['id' => 2, 'trainee_id' => 1, 'payment_amount' => 6000000]);
onFile(['id' => 3, 'trainee_id' => 1, 'payment_amount' => 2000000]);

$summary = rebuild(1);
check('the summary counts the rows', $summary['rows'], 3);
check('the owing cost is carried down from the opening row', $summary['full'], 24000000);
check('the accumulated total is the sum of the payments', $summary['accumulated'], 12000000);
check('and what is left is the difference', $summary['unpaid'], 12000000);
check('every row now reads as a running total', chain(1), [
    ['paid' => 4000000, 'full' => 24000000, 'accumulated' => 4000000,
     'unpaid' => 20000000, 'settled' => 0],
    ['paid' => 6000000, 'full' => 24000000, 'accumulated' => 10000000,
     'unpaid' => 14000000, 'settled' => 0],
    ['paid' => 2000000, 'full' => 24000000, 'accumulated' => 12000000,
     'unpaid' => 12000000, 'settled' => 0],
]);

echo "  running it again\n";
// A rebuild that is not safe to run twice is one nobody dares run once.
$before = chain(1);
$again = rebuild(1);
check('changes nothing', chain(1), $before);
check('and reports the same figures', $again, $summary);

echo "  a payment deleted behind the figures\n";
// The state this exists for: the rows after the deleted one still say what they
// said before, and each of them adds up on its own.
$conn->delete('trainee_installments', ['id' => 2]);
$stale = chain(1);
check('leaves the later rows overstating the total', $stale[1]['accumulated'], 12000000);
rebuild(1);
check('the rebuild brings them back to what is on file', chain(1), [
    ['paid' => 4000000, 'full' => 24000000, 'accumulated' => 4000000,
     'unpaid' => 20000000, 'settled' => 0],
    ['paid' => 2000000, 'full' => 24000000, 'accumulated' => 6000000,
     'unpaid' => 18000000, 'settled' => 0],
]);

echo "  paid in full\n";
onFile(['id' => 10, 'trainee_id' => 2, 'payment_amount' => 10000000,
    'full_payment_amount' => 10000000]);
$summary = rebuild(2);
check('nothing is left owing', $summary['unpaid'], 0);
check('and the row is marked settled', chain(2)[0]['settled'], 1);

echo "  paid more than was owed\n";
// An overpayment must not make what is owed negative: a negative balance reads
// as the institution owing the trainee, which is not what happened.
onFile(['id' => 20, 'trainee_id' => 3, 'payment_amount' => 12000000,
    'full_payment_amount' => 10000000]);
$summary = rebuild(3);
check('what is left stops at zero', $summary['unpaid'], 0);
check('the accumulated total still says what was paid', $summary['accumulated'], 12000000);
check('and the row is settled', chain(3)[0]['settled'], 1);

echo "  a negative payment\n";
// Nothing should write one, but a total that quietly went down would be the
// hardest kind of wrong to find.
onFile(['id' => 30, 'trainee_id' => 4, 'payment_amount' => 5000000,
    'full_payment_amount' => 10000000]);
onFile(['id' => 31, 'trainee_id' => 4, 'payment_amount' => -2000000]);
$summary = rebuild(4);
check('is not subtracted from the total', $summary['accumulated'], 5000000);

echo "  the owing cost on a later row\n";
// Where an older row somehow carries a larger figure, the largest is taken
// rather than silently shrinking what the trainee owes.
onFile(['id' => 40, 'trainee_id' => 5, 'payment_amount' => 1000000,
    'full_payment_amount' => 8000000]);
onFile(['id' => 41, 'trainee_id' => 5, 'payment_amount' => 1000000,
    'full_payment_amount' => 24000000]);
$summary = rebuild(5);
check('the largest is taken, not the first', $summary['full'], 24000000);

echo "  a trainee with no payments at all\n";
// Reporting zeroes here would be a claim about somebody who has no rows, and
// writing them would invent a debt of nothing.
$summary = rebuild(99);
check('is reported as nothing rather than as zeroes',
    $summary, ['rows' => 0, 'full' => 0, 'accumulated' => 0, 'unpaid' => 0]);
check('and no row is created', count(chain(99)), 0);

echo "  no trainee at all\n";
check('is not a rebuild of everybody', rebuild(0),
    ['rows' => 0, 'full' => 0, 'accumulated' => 0, 'unpaid' => 0]);
check('nor is a non-numeric id', rebuild('abc'),
    ['rows' => 0, 'full' => 0, 'accumulated' => 0, 'unpaid' => 0]);

echo "  and the payments themselves are never touched\n";
$amounts = $conn->execute('SELECT id, payment_amount, payment_date,
    master_currency_id FROM trainee_installments ORDER BY id')->fetchAll('assoc');
rebuild(1);
rebuild(2);
rebuild(3);
rebuild(4);
rebuild(5);
check('what was paid, when, and in what currency is left exactly as it was',
    $conn->execute('SELECT id, payment_amount, payment_date,
        master_currency_id FROM trainee_installments ORDER BY id')->fetchAll('assoc'),
    $amounts);

echo "  one trainee at a time\n";
// A rebuild asked for one trainee must not reach into another's rows.
$others = chain(2);
rebuild(1);
check('another trainee is left alone', chain(2), $others);

finish($db);
