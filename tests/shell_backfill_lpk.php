<?php
/**
 * Marking the LPKs that finished registering but were never recorded as having
 * finished.
 *
 * Two registration flows write to vocational_training_institutions and they kept
 * score in different columns. The older one set is_registered and registered_at;
 * the admin flow only ever moved status to 'active'. Every counter and badge that
 * asks "is it registered?" reads is_registered - so an LPK that finished through
 * the admin flow showed Status: Active and Registered: No on the same row, and
 * sat in the verify page's Pending count for good, with nothing an admin could
 * press to change it.
 *
 * Repairing that means writing a date, and a date written into a record is a
 * claim about when something happened. So the only rows touched are ones where
 * status says it happened, the date is taken from the best evidence there is, and
 * where there is no evidence the flag is still set but no date is invented.
 */
require __DIR__ . '/lib/harness.php';

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_backfill_lpk.sqlite';
$conn = sqliteConnections(['cms_tmm_stakeholders',
    'cms_authentication_authorization', 'default'], $db);
$conn->execute('CREATE TABLE stakeholder_activities (id INTEGER PRIMARY KEY,
    activity_type VARCHAR(50), stakeholder_type VARCHAR(50), stakeholder_id INTEGER,
    description TEXT, created DATETIME)');

list($shell, $activationTimes) = reachInto(
    'App\Shell\BackfillLpkRegistrationShell', '_activationTimes');
$asTime = new ReflectionMethod('App\Shell\BackfillLpkRegistrationShell', '_asTime');
$asTime->setAccessible(true);
$shorten = new ReflectionMethod('App\Shell\BackfillLpkRegistrationShell', '_shorten');
$shorten->setAccessible(true);

// _activationTimes() warns on a console when the log cannot be read, so the
// shell needs one that keeps what it is told.
class QuietIo extends Cake\Console\ConsoleIo
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

$io = new QuietIo();
$ioProperty = new ReflectionProperty('Cake\Console\Shell', '_io');
$ioProperty->setAccessible(true);
$ioProperty->setValue($shell, $io);

/**
 * Put an activity row on file.
 *
 * @param array $row Column values.
 * @return void
 */
function activity(array $row)
{
    global $conn;
    $conn->insert('stakeholder_activities', $row + [
        'activity_type' => 'activation', 'stakeholder_type' => 'lpk',
        'description' => 'activated', 'created' => '2026-05-11 09:00:00']);
}

/** @return array stakeholder_id => FrozenTime */
function activations()
{
    global $shell, $activationTimes;
    TableRegistry::getTableLocator()->clear();

    return $activationTimes->invoke($shell);
}

/** @param mixed $value Whatever a column held. @return \Cake\I18n\FrozenTime|null */
function asTime($value)
{
    global $shell, $asTime;

    return $asTime->invoke($shell, $value);
}

echo "  reading the activation log\n";
activity(['id' => 1, 'stakeholder_id' => 5, 'created' => '2026-05-11 09:15:00']);
$found = activations();
checkTrue('an activation gives a date for that institution', isset($found[5]));
check('and it is the one in the log', $found[5]->format('Y-m-d H:i:s'), '2026-05-11 09:15:00');
check('an institution with no activation gets none', isset($found[99]), false);

echo "  two activations for one institution\n";
// The earliest is taken: a second activation is a later event, not a correction
// of when registration finished.
activity(['id' => 2, 'stakeholder_id' => 5, 'created' => '2026-07-01 08:00:00']);
activity(['id' => 3, 'stakeholder_id' => 6, 'created' => '2026-06-02 10:00:00']);
activity(['id' => 4, 'stakeholder_id' => 6, 'created' => '2026-06-01 10:00:00']);
$found = activations();
check('the earliest is kept for the one logged in order',
    $found[5]->format('Y-m-d H:i:s'), '2026-05-11 09:15:00');
check('and for the one logged out of order too',
    $found[6]->format('Y-m-d H:i:s'), '2026-06-01 10:00:00');

echo "  activity that is not an activation\n";
activity(['id' => 5, 'activity_type' => 'registration', 'stakeholder_id' => 7]);
activity(['id' => 6, 'activity_type' => 'verification', 'stakeholder_id' => 7]);
check('is not read as one', isset(activations()[7]), false);

echo "  the stakeholder type\n";
// 'lpk' is what the log accepts. The LPK controller passed
// 'vocational_training' until recently, which the validator rejected - so no row
// should carry it, and both are matched in case an installation has some from
// before the validator.
activity(['id' => 7, 'stakeholder_type' => 'vocational_training', 'stakeholder_id' => 8]);
checkTrue('an older row written as vocational_training is still read',
    isset(activations()[8]));
activity(['id' => 8, 'stakeholder_type' => 'special_skill', 'stakeholder_id' => 9]);
check('but another kind of stakeholder is not', isset(activations()[9]), false);

echo "  a date the log cannot offer\n";
activity(['id' => 9, 'stakeholder_id' => 10, 'created' => null]);
check('an empty date is no date, not today', isset(activations()[10]), false);
activity(['id' => 10, 'stakeholder_id' => 11, 'created' => 'not a date at all']);
check('and neither is text that is not one', isset(activations()[11]), false);

echo "  no activity log at all\n";
// An installation that never ran the stakeholder migration has no such table,
// and that is a reason to fall back to the other evidence - not to stop.
$conn->execute('DROP TABLE stakeholder_activities');
TableRegistry::getTableLocator()->clear();
$io->lines = [];
check('gives no dates rather than failing', activations(), []);
checkTrue('and says why, so the empty column is not a mystery',
    strpos(implode(' ', $io->lines), 'No activity log to read dates from') !== false);

echo "  reading whatever a date column turned out to hold\n";
// These tables are built by hand-written SQL that has not always agreed with
// itself: the ORM hands back a FrozenTime where the column is a real DATETIME
// and a plain string where it is not, and ->format() on the string is fatal.
checkTrue('a time object is taken as it is',
    asTime(new FrozenTime('2026-05-11 09:15:00')) instanceof FrozenTime);
check('and keeps its value',
    asTime(new FrozenTime('2026-05-11 09:15:00'))->format('Y-m-d H:i:s'),
    '2026-05-11 09:15:00');
check('a string is read', asTime('2026-05-11 09:15:00')->format('Y-m-d H:i:s'),
    '2026-05-11 09:15:00');
check('a date with no time is read too', asTime('2026-05-11')->format('Y-m-d'),
    '2026-05-11');
check('nothing is nothing', asTime(null), null);
check('an empty string is nothing', asTime(''), null);
// MySQL's zero date is not empty, and FrozenTime does not refuse it: it turns
// 0000-00-00 into the 30th of November in the year minus one. Writing that into
// registered_at would put a date in the record that never happened.
check('the zero date is nothing, not the year minus one',
    asTime('0000-00-00 00:00:00'), null);
check('and its date-only form too', asTime('0000-00-00'), null);
check('while a real date around it is still read',
    asTime('2026-01-01')->format('Y-m-d'), '2026-01-01');
check('and text that is not a date is nothing rather than a fatal error',
    asTime('belum diverifikasi'), null);

echo "  what the shell will and will not write\n";
$source = file_get_contents(TMM_ROOT . '/src/Shell/BackfillLpkRegistrationShell.php');
$code = implode('', array_map(function ($token) {
    return is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)
        ? '' : (is_array($token) ? $token[1] : $token);
}, token_get_all($source)));

// status = 'active' is written in one place, after the account is saved and the
// password set. It is the record of what happened, not a guess about it.
checkTrue('only rows whose status says it happened are touched',
    strpos($code, "'status' => 'active'") !== false);
checkTrue('and only those not already marked',
    strpos($code, "'is_registered IS' => null") !== false
    && strpos($code, "'is_registered' => 0") !== false);
checkTrue('nothing is written without --apply',
    preg_match('/if\s*\(\s*!\s*(\$apply|\$this->param\(\x27apply\x27\))\s*\)/', $code) === 1);
checkTrue('and it says so when there is nothing to do',
    strpos($source, 'every active LPK is already marked') !== false);

echo "  where no date can be found\n";
// That it happened is certain even where when it happened was never written
// down. Setting the flag with no date is the true answer; inventing one is not.
checkTrue('the flag is still set', strpos($code, "\$data = ['is_registered' => 1];") !== false);
checkTrue('and the date only where there is one',
    strpos($code, 'if ($when !== null) {') !== false);
checkTrue('with the row shown as having no date on record',
    strpos($source, 'no date on record') !== false);

echo "  and each row says where its date came from\n";
// The operator is being asked to approve a claim about when something happened,
// so the evidence is named per row rather than in one line at the top.
checkTrue('the activation log is named as a source',
    strpos($source, "'activation log'") !== false);
checkTrue('and email_verified_at as the fallback',
    strpos($source, "'email_verified_at'") !== false);
checkTrue('under a column heading that says so',
    strpos($source, 'taken from') !== false);

echo "  and nothing else is disturbed\n";
checkTrue('no row is deleted', preg_match('/\bDELETE\s+FROM\b/i', $code) === 0
    && strpos($code, '->delete(') === false);
checkTrue('no table is dropped or renamed',
    preg_match('/\b(DROP|TRUNCATE|RENAME)\b/i', $code) === 0);
// updateAll rather than save(): a data repair should not run the entity rules
// and validators that a person filling in a form goes through.
checkTrue('and the repair does not pretend to be somebody filling in a form',
    strpos($code, 'updateAll') !== false);

$long = $shorten->invoke($shell, str_repeat('A', 80), 40);
echo "  a long institution name\n";
check('is cut to the width of the column', mb_strlen($long), 40);
checkTrue('and the cut is visible', mb_strpos($long, "\u{2026}") !== false);
check('a short one is left alone', $shorten->invoke($shell, 'LPK Sakura', 40), 'LPK Sakura');

echo "  and the same trap elsewhere\n";
// _asTime() exists because a date column here may hold a string. The one other
// place that builds a time from a column value is the token check, where an
// unreadable expiry would have thrown in the middle of somebody verifying their
// address.
$tokens = file_get_contents(TMM_ROOT . '/src/Model/Table/EmailVerificationTokensTable.php');
checkTrue('the token expiry is read rather than trusted',
    strpos($tokens, 'catch (\Exception $e)') !== false
    && strpos($tokens, 'has an expiry that cannot be read') !== false);
checkTrue('and an unreadable one is treated as expired, not as valid',
    preg_match('/cannot be read.{0,400}return false;/s', $tokens) === 1);

finish($db);
