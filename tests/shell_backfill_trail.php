<?php
/**
 * Copying an old promotion record into the decision trail.
 *
 * The trail is the answer to "who put this person forward, and when". It was
 * written from the day it existed, which left every promotion taken before that
 * outside it - and a trail with a hole in it is worse than no trail, because
 * nobody can tell a promotion that was never recorded from one that never
 * happened.
 *
 * Copying them across is only safe if it says no to everything it cannot state
 * truthfully. A guessed date, a guessed sequence, a line attached to nobody: each
 * would put a claim in the trail that nothing supports, and the trail is the
 * thing people will later rely on.
 */
require __DIR__ . '/lib/harness.php';

list($shell, $lineFor) = reachInto('App\Shell\BackfillPromotionTrailShell', 'lineFor');

/**
 * What the shell would write for this old row, or the reason it will not.
 *
 * @param array $row The row from the old promotion table.
 * @param string $table Which table it came from.
 * @return array|string The line, or a sentence saying why not.
 */
function lineFor(array $row, $table = 'promotion_histories')
{
    global $shell, $lineFor;

    return $lineFor->invoke($shell, $row + ['id' => 1], $table);
}

$good = [
    'id' => 7,
    'promotion_type' => 'candidate to trainee',
    'promotion_date' => '2026-05-11 09:15:00',
    'source_table' => 'candidates',
    'source_id' => 41,
    'target_table' => 'trainees',
    'target_id' => 12,
    'promoted_by' => 3,
    'promotion_reason' => 'Lulus seleksi LPK',
    'data_snapshot' => '{"candidate_id":41,"name":"Nur Aini"}',
    'is_cancelled' => 0,
];

echo "  a promotion that can be stated\n";
$line = lineFor($good);
checkTrue('becomes a line', is_array($line));
check('with the action the trail uses', $line['action'], 'candidate.promoteToTrainee');
check('the date from the old record, not today', $line['created'], '2026-05-11 09:15:00');
check('the subject as the trail names subjects', $line['subject_type'], 'Candidate');
check('and its id', $line['subject_id'], 41);
check('the person named from the snapshot', $line['subject_label'], 'Nur Aini');
check('and who did it', $line['user_id'], 3);

echo "  and what the line says about itself\n";
// A backfilled line must not read as a line written at the time. Somebody
// auditing this later has to be able to tell the difference.
$detail = json_decode($line['detail'], true);
checkTrue('it says it was copied', strpos($detail['backfilled'], 'copied from') === 0);
checkTrue('and from where', strpos($detail['backfilled'], 'promotion_histories #7') !== false);
checkTrue('and that the time is the old table\'s, not the trail\'s',
    strpos($detail['backfilled'], 'not in this trail') !== false);
checkTrue('where the snapshot can be read back from is recorded',
    strpos($detail['snapshot'], 'column data_snapshot') !== false);
check('and what it was promoted to', $detail['promoted_to'], 'trainees #12');
check('the reason given at the time is kept', $detail['reason'], 'Lulus seleksi LPK');

echo "  what it will not state\n";
// Each of these is a refusal, and each refusal says why in a sentence somebody
// can act on - a count of skipped rows would tell nobody anything.
$cancelled = lineFor(['is_cancelled' => 1] + $good);
checkTrue('a cancelled promotion is refused', is_string($cancelled));
checkTrue('because it is two decisions and only one has a date',
    strpos($cancelled, 'the reversal has no date here') !== false);

$unknown = lineFor(['promotion_type' => 'apprentice to alumnus'] + $good);
checkTrue('a promotion type it does not know is refused', is_string($unknown));
checkTrue('and the type is quoted so it can be looked at',
    strpos($unknown, 'apprentice to alumnus') !== false);

$noSource = lineFor(['source_table' => '', 'source_id' => 0] + $good);
checkTrue('a row with no source is refused', is_string($noSource));
checkTrue('because there is nobody to attach the line to',
    strpos($noSource, 'nobody to attach the line to') !== false);
checkTrue('and a source table with no id is refused too',
    is_string(lineFor(['source_id' => 0] + $good)));

echo "  a snapshot that cannot be read\n";
// The name is looked up from the source table instead. A line with no label is
// still a true line; a line with the wrong label is not.
$noSnapshot = lineFor(['data_snapshot' => 'not json at all'] + $good);
checkTrue('is not itself a refusal', is_array($noSnapshot));
checkTrue('and the label falls back rather than being invented',
    $noSnapshot['subject_label'] !== 'Nur Aini');

echo "  a very long name\n";
$long = lineFor(['data_snapshot' => json_encode(['name' => str_repeat('A', 400)])] + $good);
check('is cut to what the column holds', mb_strlen($long['subject_label']), 255);

echo "  a promotion with nobody recorded against it\n";
// Unknown is a fact. Attributing it to whoever is running the shell would be a
// worse answer than none.
$noUser = lineFor(['promoted_by' => null] + $good);
check('leaves the user empty', $noUser['user_id'], null);
check('and does not invent a username', $noUser['username'], null);

echo "  what the line never claims to know\n";
// These are recorded when a decision is taken through the application. A
// backfilled line has no honest value for them.
check('no ip address', $line['ip'], null);
check('no user agent', $line['user_agent'], null);
check('and no role names', $line['role_names'], null);

echo "  running it twice\n";
// Matched on the action, the subject and the date rather than on anything in the
// old table, so a second run is harmless however the first was written.
$source = file_get_contents(TMM_ROOT . '/src/Shell/BackfillPromotionTrailShell.php');
$code = implode('', array_map(function ($token) {
    return is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)
        ? '' : (is_array($token) ? $token[1] : $token);
}, token_get_all($source)));
checkTrue('is guarded by looking for the line first',
    strpos($code, 'alreadyThere') !== false);
checkTrue('matched on the action, the subject and the date',
    strpos($code, "['action', 'subject_id', 'created']") !== false);

echo "  and the old records are left where they are\n";
// The backfill copies. Moving or deleting the old rows would take away the only
// place the copy can be checked against.
checkTrue('nothing is deleted', preg_match('/\bDELETE\s+FROM\b/i', $code) === 0
    && strpos($code, '->delete(') === false);
checkTrue('and nothing is renamed or dropped',
    preg_match('/\b(RENAME|DROP|TRUNCATE)\b/i', $code) === 0);
checkTrue('and it changes nothing without --apply',
    preg_match('/if\s*\(\s*!\s*(\$apply|\$this->param\(\x27apply\x27\))\s*\)/', $code) === 1);

finish();
