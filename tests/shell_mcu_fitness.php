<?php
/**
 * Reading a medical result as pass or fail.
 *
 * master_medical_check_up_results holds a title somebody typed: "Fit", "Tidak
 * Fit", "Lolos", "Gagal". Nothing said which of those meant the person could
 * depart, so the departure screens asked a human to read the word every time -
 * and a screen that asks a human to read a word is a screen that will one day be
 * read wrong.
 *
 * is_fit is that reading, stored once. Getting it from the title is guessing, so
 * the guess has to be a careful one: the whole point of it is that a person who
 * is not fit must never come out as fit, and a title it cannot read must come
 * out as neither rather than as a default.
 */
require __DIR__ . '/lib/harness.php';

list($shell, $read) = reachInto('App\Shell\MarkMcuFitnessShell', 'readTitle');

/**
 * How the shell reads this title.
 *
 * @param string|null $title What somebody typed.
 * @return int|null 1 fit, 0 not fit, null cannot tell.
 */
function reading($title)
{
    global $shell, $read;

    return $read->invoke($shell, $title);
}

echo "  titles that mean fit\n";
foreach (['Fit', 'fit', 'FIT', 'Lolos', 'Lulus', 'Sehat', 'Pass', 'Layak',
        'Memenuhi Syarat', '  Fit  '] as $title) {
    check(sprintf('%-18s reads as fit', '"' . $title . '"'), reading($title), 1);
}

echo "  titles that mean not fit\n";
foreach (['Tidak Fit', 'tidak fit', 'Tidak Lolos', 'Tidak Sehat', 'Unfit',
        'Not Fit', 'No Fit', 'Fail', 'Gagal', 'Ditolak'] as $title) {
    check(sprintf('%-18s reads as not fit', '"' . $title . '"'), reading($title), 0);
}

echo "  the negative wins\n";
// Every one of these contains a word from the fit list. Reading in the other
// order would mark somebody fit who is not, which is the one mistake this must
// never make.
foreach (['Tidak Fit', 'Tidak Lolos', 'Tidak Sehat', 'Unfit', 'Not Fit'] as $title) {
    check(sprintf('%-18s is not read as fit for containing the word',
        '"' . $title . '"'), reading($title), 0);
}
checkTrue('the not-fit list is consulted before the fit list',
    strpos(file_get_contents(TMM_ROOT . '/src/Shell/MarkMcuFitnessShell.php'),
        'foreach (self::NOT_FIT') < strpos(
        file_get_contents(TMM_ROOT . '/src/Shell/MarkMcuFitnessShell.php'),
        'foreach (self::FIT'));

echo "  titles it cannot read\n";
// Neither is an answer. A default either way would put a medical judgement in
// the database that nobody made.
foreach (['Pending', 'Menunggu Hasil', 'Perlu Tes Ulang', 'Dirujuk',
        'Belum Diperiksa', '???'] as $title) {
    check(sprintf('%-18s reads as neither', '"' . $title . '"'), reading($title), null);
}
check('an empty title reads as neither', reading(''), null);
check('and so does nothing at all', reading(null), null);
check('and whitespace only', reading('   '), null);

echo "  and what the shell does with that\n";
$source = file_get_contents(TMM_ROOT . '/src/Shell/MarkMcuFitnessShell.php');
$code = implode('', array_map(function ($token) {
    return is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)
        ? '' : (is_array($token) ? $token[1] : $token);
}, token_get_all($source)));

checkTrue('it changes nothing without --apply',
    preg_match('/if\s*\(\s*!\s*(\$apply|\$this->param\(\x27apply\x27\))\s*\)/', $code) === 1);
// A title it cannot read is the case somebody has to decide by hand, so the
// shell has to name those rows rather than count them.
checkTrue('rows it cannot read are reported for somebody to settle',
    strpos($code, '_reportBlocked') !== false);
// A title it cannot read never reaches the list of rows to write: the branch
// that says so is the only one that does not add to $toWrite.
checkTrue('and it never writes a guess for them',
    preg_match('/if \(\$proposed === null\) \{\s*\$verdict = .*does not recognise/s',
        $code . $source) === 1
    && strpos($code, '$toWrite[] = [$row, $proposed];') !== false);

echo "  a reading a person has already overruled\n";
// is_fit can be set by hand. A shell that re-derived it from the word every
// time would undo the correction somebody made for a reason the title does not
// carry.
checkTrue('is kept, not overwritten',
    strpos($source, 'but a person said') !== false);
checkTrue('unless --overwrite is asked for', strpos($code, "'overwrite'") !== false);
checkTrue('and a row that already agrees is left alone rather than rewritten',
    strpos($source, 'already says') !== false);
checkTrue('nothing is deleted', preg_match('/\bDELETE\s+FROM\b/i', $code) === 0);
checkTrue('and no table is dropped or renamed',
    preg_match('/\b(DROP|TRUNCATE|RENAME)\b/i', $code) === 0);

echo "  and the reading can be overridden by hand\n";
// It is a guess from a word. Somebody has to be able to correct it without
// editing a shell.
checkTrue('the shell says its reading is a guess from the title',
    preg_match('/\b(guess|reads? the (word|title))/i', $source) === 1);

finish();
