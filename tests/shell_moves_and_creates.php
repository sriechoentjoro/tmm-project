<?php
/**
 * The two shells that move rows and make a table, held to what they must not do.
 *
 * Both work across databases, which is where this system's faults live. It keeps
 * fifteen, several hold tables of the same name, and each numbers from 1 - so a
 * row written into the wrong one is found by nothing and errors nowhere.
 *
 * CreateApprenticeOrderSharesShell makes a table and then looks for copies of it
 * in other databases. That second half nearly renamed the table it had just
 * created: it skipped the canonical connection by NAME, and several names point
 * at one database here - 'default' and 'cms_masters' deliberately so - so the
 * alias would have found the new table and renamed it straight back out of
 * existence.
 *
 * MoveStuckEmailTemplatesShell moves rows from the copy nothing reads into the
 * copy the application reads. A move that overwrites a live template with a
 * stranded one would replace working mail with whatever was abandoned.
 */
require __DIR__ . '/lib/harness.php';

/**
 * A file's code with the comments taken out.
 *
 * Both shells write the SQL they would run into their docblocks, so searching
 * the whole file for RENAME finds the word in prose explaining the rename.
 *
 * @param string $path The file.
 * @return string
 */
function codeOf($path)
{
    return implode('', array_map(function ($token) {
        return is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)
            ? '' : (is_array($token) ? $token[1] : $token);
    }, token_get_all(file_get_contents($path))));
}

$sharesFile = TMM_ROOT . '/src/Shell/CreateApprenticeOrderSharesShell.php';
$shares = file_get_contents($sharesFile);
$sharesCode = codeOf($sharesFile);

echo "  the share table's own creation\n";
checkTrue('is a CREATE, and the statement is in the docblock to be read first',
    strpos($shares, 'CREATE TABLE apprentice_order_shares') !== false);
checkTrue('and nothing is created without --apply',
    preg_match('/if\s*\(\s*!\s*(\$apply|\$this->param\(\x27apply\x27\))\s*\)/',
        $sharesCode) === 1);
checkTrue('the table it creates and the name it sets copies aside are different',
    strpos($sharesCode, "const TABLE = 'apprentice_order_shares'") !== false
    && strpos($sharesCode, "const SET_ASIDE = 'apprentice_order_shares_old'") !== false);

echo "  looking for copies in other databases\n";
// The fault that was caught: skipping by connection name is not skipping by
// database, and an alias for the canonical database would have found the table
// just created.
checkTrue('the canonical DATABASE is what gets skipped, not the connection name',
    strpos($sharesCode, "\$canonicalConfig = ConnectionManager::get(\$canonical)->config();") !== false
    && strpos($sharesCode, "\$seen[isset(\$canonicalConfig['database'])") !== false);
checkTrue('each database is visited once however many names it answers to',
    strpos($sharesCode, 'if (isset($seen[$database])) {') !== false
    && strpos($sharesCode, '$seen[$database] = true;') !== false);
checkTrue('and the reason is written down beside it',
    strpos($shares, "Skipping the canonical connection by name is not enough") !== false);

echo "  what it does with a copy it finds\n";
checkTrue('renames it, keeping the rows', strpos($sharesCode, 'RENAME') !== false);
checkTrue('and never drops one',
    preg_match('/\b(DROP\s+TABLE|TRUNCATE)\b/i', $sharesCode) === 0);
checkTrue('a copy can be left alone on purpose',
    strpos($sharesCode, "'keep-strays'") !== false);
// If the _old name is already taken, renaming would fail or clobber. It has to
// be noticed first.
checkTrue('and it checks whether the set-aside name is already taken',
    strpos($sharesCode, "'taken' => in_array(self::SET_ASIDE, \$tables, true)") !== false);
checkTrue('a connection it cannot reach is passed over rather than aborting the run',
    preg_match('/catch \(\\\\Exception \$e\) \{\s*continue;/', $sharesCode) === 1);

// ==================================================== the template move
$moveFile = TMM_ROOT . '/src/Shell/MoveStuckEmailTemplatesShell.php';
$move = file_get_contents($moveFile);
$moveCode = codeOf($moveFile);

echo "  moving a stranded template\n";
checkTrue('changes nothing without --apply',
    preg_match('/if\s*\(\s*!\s*(\$apply|\$this->param\(\x27apply\x27\))\s*\)/', $moveCode) === 1);
// Moving from a database to itself is a no-op at best and a duplicate at worst.
checkTrue('refuses when the two sides are the same connection',
    strpos($moveCode, 'if ($from === $to)') !== false);
checkTrue('and says nothing is stranded rather than reporting a move of none',
    strpos($move, 'No template is stranded') !== false);

echo "  what it will not overwrite\n";
// The live copy is the one the application reads. A stranded template that
// happens to share a key is not an improvement on a working one.
checkTrue('a key that already exists on the live side is not replaced',
    strpos($moveCode, 'if (isset($live[$key]))') !== false);
checkTrue('a row with no template_key is skipped and named',
    strpos($move, 'has no template_key') !== false);
checkTrue('and the columns that could not be carried over are named',
    strpos($move, 'columns not carried over') !== false);

echo "  and the stranded rows are left where they were\n";
// It copies. Deleting the source would remove the only place the copy can be
// checked against, and this ran once on a live database.
checkTrue('nothing is deleted', preg_match('/\bDELETE\s+FROM\b/i', $moveCode) === 0
    && strpos($moveCode, '->delete(') === false);
checkTrue('and nothing is dropped or renamed',
    preg_match('/\b(DROP|TRUNCATE|RENAME)\b/i', $moveCode) === 0);

echo "  both of them say where they are working\n";
// A shell that writes across databases and does not name them is one nobody can
// check before running.
foreach ([['CreateApprenticeOrderShares', $shares], ['MoveStuckEmailTemplates', $move]]
        as $pair) {
    list($name, $source) = $pair;
    $doc = substr($source, 0, strpos($source, 'class ') ?: strlen($source));
    checkTrue($name . ' says how to run it in its docblock',
        strpos($doc, 'bin/cake') !== false);
    // Either the databases are named in the docblock, or the shell takes the
    // one it cannot know as an option. Both let an operator see where it will
    // write before running it.
    checkTrue($name . ' says where it works, or asks',
        preg_match('/cms_[a-z_]+/', $doc) === 1 || strpos($source, "'from'") !== false);
}

echo "  where the move writes to\n";
// The destination is not an option, and should not be: it is by definition the
// connection the application reads, taken from the table itself. An option there
// would let somebody move templates into a third database nothing reads either -
// which is the fault being repaired, done again.
checkTrue('is taken from the table the application uses, not from a flag',
    strpos($moveCode, "\$to = \$templates->getConnection()->configName();") !== false);
checkTrue('and only the side to read from is asked for',
    strpos($moveCode, "'from'") !== false && strpos($moveCode, "addOption('to'") === false);

echo "  and the move says the source is left alone\n";
// This ran once, on a live database, on rows nobody had a second copy of.
checkTrue('in the docblock, where it is read before running',
    strpos($move, 'The source table is never modified') !== false);

finish();
