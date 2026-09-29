<?php
/**
 * The checks that stand between a duplicate table and a rename.
 *
 * Setting a table aside means renaming it to <name>_old: the rows are kept, so
 * the mistake is recoverable, but the application stops seeing them - and if
 * anything did still read that copy, it stops seeing them too. This system had
 * thirteen tables of the same name in two databases, each numbering from 1, so a
 * wrong join always matched and nothing errored. Picking the wrong copy to set
 * aside is exactly the kind of mistake that shows up weeks later.
 *
 * So the shell refuses while anything still appears to name the table, and the
 * whole of its worth is in what "appears to name" means. Three faults were found
 * in that judgement while it was being written, and they are the cases below.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Utility\Inflector;

list($shell, $mentions) = reachInto('App\Shell\SetAsideTableShell', 'mentions');
$code = new ReflectionMethod('App\Shell\SetAsideTableShell', 'code');
$code->setAccessible(true);

// A little tree to scan, so the rule is checked against files we wrote rather
// than against whatever the application happens to contain today.
$root = sys_get_temp_dir() . '/tmm_set_aside_' . bin2hex(random_bytes(4)) . DIRECTORY_SEPARATOR;
mkdir($root . 'Model/Table', 0777, true);
mkdir($root . 'Controller', 0777, true);
mkdir($root . 'Template/Widgets', 0777, true);

/**
 * Write a file into the tree.
 *
 * @param string $path Relative to the tree root.
 * @param string $contents What to write.
 * @return void
 */
function put($path, $contents)
{
    global $root;
    file_put_contents($root . $path, $contents);
}

/**
 * Which files name this table.
 *
 * @param string $table The table name.
 * @return array Paths relative to the tree root.
 */
function naming($table)
{
    global $shell, $mentions, $root;
    $found = $mentions->invoke($shell, $table, $root);
    sort($found);

    return $found;
}

echo "  a table named in code\n";
put('Controller/WidgetsController.php',
    '<?php class WidgetsController { public function x() {
        $this->query("SELECT * FROM promotion_histories"); } }');
check('is found', naming('promotion_histories'), ['Controller/WidgetsController.php']);

echo "  a table named only in a comment\n";
// The shell refused to set aside tables that its own docblock mentioned, which
// made it refuse the very tables it was written for.
put('Controller/CommentOnlyController.php',
    '<?php
/**
 * This one talks about legacy_widgets in prose, and touches nothing.
 */
class CommentOnlyController {
    // legacy_widgets used to live here
    public function x() { return 1; }
}');
check('is not a use of it', naming('legacy_widgets'), []);
checkTrue('because the comments are stripped before looking',
    strpos($code->invoke($shell, $root . 'Controller/CommentOnlyController.php'),
        'legacy_widgets') === false);

echo "  a name that is the start of another\n";
// Setting aside promotion_histories was blocked by a reference to
// promotion_histories_old, which is a different table - the one already set
// aside. An underscore is a word character, so the boundary falls where the
// name ends.
put('Controller/OldOnlyController.php',
    '<?php class OldOnlyController { public function x() {
        $this->query("SELECT * FROM promotion_histories_old"); } }');
unlink($root . 'Controller/WidgetsController.php');
check('does not count as a use of the shorter one',
    naming('promotion_histories'), []);
check('while the longer one is still found itself',
    naming('promotion_histories_old'), ['Controller/OldOnlyController.php']);

echo "  the table's own Table class\n";
// It always names the table, in setTable(). Listing it would make every table
// with a model look like it is in use, which is the shape of objection people
// stop reading - and the connection check has already settled that question.
put('Model/Table/WidgetsTable.php',
    '<?php class WidgetsTable extends Table {
        public function initialize(array $config) { $this->setTable("widgets"); } }');
check('is not counted against it', naming('widgets'), []);

echo "  another table's class naming it\n";
// Two Table classes can point at one table - ApprenticeTicketsTable and
// TicketsTable both setTable("tickets") - and that is exactly a use worth
// reporting.
put('Model/Table/ApprenticeWidgetsTable.php',
    '<?php class ApprenticeWidgetsTable extends Table {
        public function initialize(array $config) { $this->setTable("widgets"); } }');
check('is counted', naming('widgets'), ['Model/Table/ApprenticeWidgetsTable.php']);

echo "  a template naming it\n";
put('Template/Widgets/index.ctp',
    '<?php echo $this->query("SELECT count(*) FROM widgets"); ?>');
check('counts too, because raw SQL lives in templates here',
    naming('widgets'), ['Model/Table/ApprenticeWidgetsTable.php',
        'Template/Widgets/index.ctp']);

echo "  a file that will not parse\n";
// Better to over-report than to miss: an unparseable file is searched whole,
// comments and all, rather than skipped.
put('Controller/BrokenController.php',
    '<?php class Broken { function x( "SELECT * FROM stray_widgets"  <<<< ');
check('is searched anyway, not skipped', naming('stray_widgets'),
    ['Controller/BrokenController.php']);
checkTrue('because code() hands back the source rather than nothing',
    strpos($code->invoke($shell, $root . 'Controller/BrokenController.php'),
        'stray_widgets') !== false);

echo "  a file that is not code\n";
put('Controller/notes.txt', 'widgets is mentioned here, in a text file');
check('is not scanned', naming('widgets'), ['Model/Table/ApprenticeWidgetsTable.php',
    'Template/Widgets/index.ctp']);

echo "  and what the shell refuses to do\n";
// Read off the source rather than run, because the dangerous half is a rename.
$source = file_get_contents(TMM_ROOT . '/src/Shell/SetAsideTableShell.php');
checkTrue('it renames, and says so - it never drops',
    strpos($source, 'RENAME TABLE') !== false && stripos($source, 'DROP TABLE') === false);
checkTrue('it stops when the target name is already taken',
    strpos($source, 'already exists in') !== false);
checkTrue('it stops when anything still appears to use the table',
    strpos($source, 'Not renaming.') !== false);
checkTrue('it changes nothing without --apply',
    strpos($source, "if (!\$apply)") !== false);
checkTrue('it prints the statement that puts the table back',
    strpos($source, 'To put it back') !== false);
checkTrue('and says to clear the model cache, or the web process will not see it',
    strpos($source, 'tmp/cache/models') !== false);
checkTrue('a name that is not a table name is refused outright',
    strpos($source, 'does not look like a table name') !== false);

exec('rm -rf ' . escapeshellarg($root));
finish();
