<?php
/**
 * The shells that add a column, held to the properties all five share.
 *
 * These exist because features were written against columns the database did not
 * have. A column added by hand on one machine and not another is how an
 * installation ends up half-working: the feature is in the code, the screen
 * renders, and the save fails on a column that is not there - or worse, the code
 * guards on the column and quietly skips the feature, so the screen simply never
 * does what it says.
 *
 * A shell that adds one has to be safe to run on an installation that already has
 * it, safe to run twice, and must never reach for anything but ADD. It is run by
 * hand on a live database, once, by somebody following instructions - the last
 * place for a statement that does more than it says.
 */
require __DIR__ . '/lib/harness.php';

$shells = [
    'AddTicketPurchaseDateShell',
    'AddAuditLogShell',
    'AddSelectionFlowColumnsShell',
    'AddApprenticeFlowColumnsShell',
    'AddInstitutionEmailVerifiedAtShell',
];

echo "  what none of them may do\n";
foreach ($shells as $name) {
    $source = file_get_contents(TMM_ROOT . '/src/Shell/' . $name . '.php');
    $code = stripComments($source);

    // DROP, TRUNCATE and DELETE have no business in a shell whose job is to add.
    // A single mistyped statement here is a column of data gone.
    checkTrue($name . ' never drops, truncates or deletes',
        preg_match('/\b(DROP|TRUNCATE)\b/i', $code) === 0
        && preg_match('/\bDELETE\s+FROM\b/i', $code) === 0);
    // MODIFY or CHANGE would rewrite a column that is already there, which is
    // not the same as adding one that is missing.
    checkTrue($name . ' only adds, never modifies an existing column',
        preg_match('/\b(MODIFY|CHANGE)\s+COLUMN\b/i', $code) === 0);
}

echo "  what all of them do\n";
foreach ($shells as $name) {
    $source = file_get_contents(TMM_ROOT . '/src/Shell/' . $name . '.php');
    $code = stripComments($source);

    // Reporting before changing is the habit this whole set was written with:
    // the operator sees what would happen, then asks for it.
    // Written either way: some read the flag into $apply first, some test
    // $this->param('apply') where they need it.
    checkTrue($name . ' changes nothing without --apply',
        strpos($code, "'apply'") !== false
        && preg_match('/if\s*\(\s*!\s*(\$apply|\$this->param\(\x27apply\x27\))\s*\)/',
            $code) === 1);
    // Reading the columns first is what makes it safe to run on an
    // installation that already has them.
    checkTrue($name . ' looks at the columns before adding any',
        strpos($code, 'columns()') !== false || strpos($code, 'describe') !== false
        || strpos($code, 'hasColumn') !== false);
    // A column already there must be reported, not treated as a failure - the
    // shell is meant to be run on every installation, including the ones that
    // are already right.
    checkTrue($name . ' says so when there is nothing to do rather than failing',
        preg_match('/Nothing to do|already (there|has|exists)|no columns? to add/i',
            $source) === 1);
    checkTrue($name . ' prints the statement it would run',
        strpos($code, 'ALTER TABLE') !== false || strpos($code, 'CREATE TABLE') !== false);
}

echo "  and each names the columns it is responsible for\n";
// The docblock is where an operator reads what a shell will do before running
// it, and these are run from instructions rather than from a menu.
foreach ($shells as $name) {
    $source = file_get_contents(TMM_ROOT . '/src/Shell/' . $name . '.php');
    $doc = substr($source, 0, strpos($source, 'class ') ?: strlen($source));
    // Either the statement verbatim, or prose that says what it will change.
    // What matters is that an operator can read the intent before running it,
    // not which of the two forms it is in.
    checkTrue($name . ' says in its docblock what it will change',
        strpos($doc, 'ALTER TABLE') !== false || strpos($doc, 'CREATE TABLE') !== false
        || preg_match('/\b(adds?|creates?)\b.{0,80}\b(column|table)\b/is', $doc) === 1);
    checkTrue($name . ' says how to run it',
        strpos($doc, 'bin/cake') !== false);
}

echo "  the one that both creates and extends\n";
// AddAuditLogShell is the odd one: the table may be absent, or present and
// missing columns. Both have to be handled, and the second is the one that gets
// forgotten.
$audit = file_get_contents(TMM_ROOT . '/src/Shell/AddAuditLogShell.php');
checkTrue('creates the table when it is not there',
    strpos($audit, 'CREATE TABLE') !== false);
checkTrue('and adds only what is missing when it is',
    strpos($audit, 'ALTER TABLE') !== false);
checkTrue('and says which of the two it is about to do',
    preg_match('/adds only what is missing|extend/i', $audit) === 1);

/**
 * A file's code with its comments taken out.
 *
 * Every one of these shells writes the SQL it would run into its own docblock,
 * so a search for DROP over the whole file would find the word in prose
 * explaining that it does not drop.
 *
 * @param string $source PHP source.
 * @return string
 */
function stripComments($source)
{
    $code = '';
    foreach (token_get_all($source) as $token) {
        if (is_array($token)) {
            if ($token[0] === T_COMMENT || $token[0] === T_DOC_COMMENT) {
                continue;
            }
            $code .= $token[1];
            continue;
        }
        $code .= $token;
    }

    return $code;
}

finish();
