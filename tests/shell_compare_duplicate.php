<?php
/**
 * Comparing two tables of the same name in two databases.
 *
 * This system had thirteen of those, each numbering from 1, so a wrong join
 * always matched and nothing errored. Deciding which copy is the live one is
 * done by reading both, and the whole value of the comparison is that what it
 * prints is the whole of what is there.
 *
 * It was not. A row present on one side only was printed with the columns both
 * sides share, because that was the list the comparison had been working with.
 * A promotion_histories row printed as "source_table= target_table=" - empty on
 * both - while its own promotion_reason and data_snapshot, the only columns it
 * had anything in, were not on the shared list at all. It looked like an empty
 * row. It held a real promotion, and it was nearly set aside on that reading.
 */
require __DIR__ . '/lib/harness.php';

list($shell, $dump) = reachInto('App\Shell\CompareDuplicateTableShell', 'dump');
$short = new ReflectionMethod('App\Shell\CompareDuplicateTableShell', 'short');
$short->setAccessible(true);

/**
 * Collect what dump() would print, instead of writing it to a console.
 *
 * @param array $rows Rows keyed by their key value.
 * @param array $keys Which of them to print.
 * @param array $columns The columns to print.
 * @param string $key The key column.
 * @return string Everything it said, as one string.
 */
function dumped(array $rows, array $keys, array $columns, $key = 'id')
{
    global $shell, $dump;

    $io = new CollectingIo();
    $property = new ReflectionProperty('Cake\Console\Shell', '_io');
    $property->setAccessible(true);
    $property->setValue($shell, $io);

    $dump->invoke($shell, $rows, $keys, $columns, $key);

    return implode("\n", $io->lines);
}

/** A console that keeps what it was told instead of printing it. */
class CollectingIo extends Cake\Console\ConsoleIo
{
    public $lines = [];

    public function __construct()
    {
    }

    public function out($message = '', $newlines = 1, $level = self::NORMAL)
    {
        foreach ((array)$message as $line) {
            $this->lines[] = $line;
        }

        return 1;
    }
}

echo "  a row that only one side has\n";
// The row from the promotion_histories case: nothing in the shared columns, a
// real promotion in its own.
$rows = [7 => [
    'id' => 7,
    'source_table' => null,
    'target_table' => '',
    'promotion_reason' => 'Lulus seleksi LPK, diajukan 12 Mei 2026',
    'data_snapshot' => '{"candidate_id":41,"name":"Nur Aini"}',
]];
$sharedOnly = ['id', 'source_table', 'target_table'];
$everyColumn = array_keys($rows[7]);

$out = dumped($rows, [7], $sharedOnly);
checkTrue('printed with the shared columns only, it reads as empty',
    strpos($out, '(nothing but the key)') !== false);

$out = dumped($rows, [7], $everyColumn);
checkTrue('printed with its own columns, the promotion is there',
    strpos($out, 'Lulus seleksi LPK') !== false);
// Long values are shortened, so what is checked is that the column is there
// at all with something in it - which is the difference between "empty row"
// and "row worth reading".
checkTrue('and the snapshot column is there with something in it',
    strpos($out, 'data_snapshot={"candidate_id') !== false);
checkTrue('so it no longer reads as empty',
    strpos($out, '(nothing but the key)') === false);

echo "  and that is what the shell hands it\n";
// Read off the source, because the mistake was in which list was passed, not in
// what dump() did with it.
$source = file_get_contents(TMM_ROOT . '/src/Shell/CompareDuplicateTableShell.php');
checkTrue("the left side is dumped with the left side's own columns",
    strpos($source, "\$this->dump(\$rows['left'], \$onlyLeft, \$columns['left'], \$key)") !== false);
checkTrue('and the right with the right\'s',
    strpos($source, "\$this->dump(\$rows['right'], \$onlyRight, \$columns['right'], \$key)") !== false);
checkTrue('with the reason written down beside it',
    strpos($source, 'decision made on a partial view') !== false);

echo "  a row that really is empty\n";
// It must still be possible to say so - the point is not to claim everything
// holds something.
$empty = [9 => ['id' => 9, 'source_table' => null, 'target_table' => '',
    'promotion_reason' => null, 'data_snapshot' => '']];
$out = dumped($empty, [9], array_keys($empty[9]));
checkTrue('is said to hold nothing but its key',
    strpos($out, '(nothing but the key)') !== false);

echo "  the key itself\n";
// Repeating it among the values would hide a row that holds nothing else behind
// its own id.
$out = dumped($rows, [7], $everyColumn);
checkTrue('is not repeated among the values', strpos($out, 'id=7') === false);
$out = dumped($rows, [7], $everyColumn, 'apprentice_order_id');
checkTrue('and where the key is not id, it is named on the line',
    strpos($out, 'apprentice_order_id 7:') !== false);

echo "  more rows than it will print\n";
$many = [];
for ($i = 1; $i <= 40; $i++) {
    $many[$i] = ['id' => $i, 'note' => 'row ' . $i];
}
$out = dumped($many, array_keys($many), ['id', 'note']);
checkTrue('says how many it did not show', strpos($out, 'more not shown') !== false);
checkTrue('and the count is the ones left over',
    strpos($out, (string)(40 - App\Shell\CompareDuplicateTableShell::DUMP_LIMIT)
        . ' more not shown') !== false);

echo "  a long value\n";
$long = [1 => ['id' => 1, 'data_snapshot' => str_repeat('x', 500)]];
$out = dumped($long, [1], ['id', 'data_snapshot']);
checkTrue('is shortened rather than filling the screen', strlen($out) < 300);
checkTrue('and the shortening is visible, not silent',
    mb_strpos($short->invoke($shell, str_repeat('x', 500)), "\u{2026}") !== false);
check('a short value is left exactly as it is',
    $short->invoke($shell, 'Nur Aini'), 'Nur Aini');
check('and a null is said to be one, not shown as blank',
    $short->invoke($shell, null), '(null)');

echo "  and the shell reads, it does not write\n";
checkTrue('there is no statement in it that changes anything',
    preg_match('/\b(INSERT|UPDATE|DELETE|RENAME|DROP)\s+(INTO|FROM|TABLE|`)/i',
        $source) === 0);

finish();
