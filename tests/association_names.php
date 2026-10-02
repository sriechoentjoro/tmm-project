<?php
/**
 * The names templates use for associated records, against the names the ORM
 * gives them.
 *
 * CakePHP puts an association on the entity under a snake_case property:
 * belongsTo('MasterStratas') fills master_strata. Twenty-four places asked for
 * the camelCase spelling instead - $traineeEducations->has('masterStrata') -
 * which is never true, so every one of them fell through to its else branch
 * and printed the raw foreign key:
 *
 *     <?php if ($traineeEducations->has('masterStrata')): ?>
 *         ... the name, linked ...
 *     <?php else: ?>
 *         <?= h($traineeEducations->master_strata_id) ?>
 *
 * So a trainee's education showed 3 where it should have said S1, on eight
 * pages. Two of them printed nothing at all instead of an id.
 *
 * Alongside it, eight reads of ->fullname on entities that have no such
 * column: a user's profile page (the column is full_name), the dashboard's
 * recent candidates and trainees, and two interview pages (the column is
 * name). Those rendered an empty heading and two links with no text in them.
 *
 * The first check here is not a pattern match - it stands the ORM up and asks
 * it which property it really fills.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_association_names.sqlite';
$conn = sqliteConnections(['cms_tmm_trainees', 'cms_masters', 'default'], $db);
$conn->execute('CREATE TABLE trainee_educations (id INTEGER PRIMARY KEY,
    trainee_id INTEGER, master_strata_id INTEGER, master_propinsi_id INTEGER,
    master_kabupaten_id INTEGER, college_entry_date DATE,
    college_graduate_date DATE, college_name VARCHAR(255),
    college_major VARCHAR(255))');
$conn->execute('CREATE TABLE master_stratas (id INTEGER PRIMARY KEY, title VARCHAR(255))');
$conn->insert('master_stratas', ['id' => 3, 'title' => 'S1']);
$conn->insert('trainee_educations', ['id' => 1, 'trainee_id' => 7,
    'master_strata_id' => 3, 'college_name' => 'Politeknik Negeri']);

echo "  which property the ORM actually fills\n";
$row = TableRegistry::getTableLocator()->get('TraineeEducations')
    ->get(1, ['contain' => ['MasterStratas']]);
check('the camelCase spelling the templates used is not there',
    $row->has('masterStrata'), false);
check('the snake_case one is', $row->has('master_strata'), true);
check('and it holds the record, not the id', $row->master_strata->title, 'S1');

echo "  what the templates ask for now\n";
$camel = [];
foreach (glob(TMM_ROOT . '/src/Template/*/*.ctp') as $file) {
    $src = file_get_contents($file);
    preg_match_all("/has\('([a-z]+[A-Z][A-Za-z]*)'\)/", $src, $guards);
    preg_match_all('/->([a-z]+[A-Z][A-Za-z]*)->/', $src, $reads);
    foreach (array_unique(array_merge($guards[1], $reads[1])) as $name) {
        $camel[] = basename(dirname($file)) . '/' . basename($file) . ': ' . $name;
    }
}
check('no template asks for an association by a camelCase name', $camel, []);

echo "  and that the names they ask for are ones the model declares\n";
// Only the belongsTo spellings are checked: those are the ones that were
// wrong, and a table class names them plainly enough to compare.
$undeclared = [];
$checked = 0;
foreach (glob(TMM_ROOT . '/src/Template/*/*.ctp') as $file) {
    preg_match_all("/\\\$([a-zA-Z_]+)->has\('([a-z][a-z_0-9]*)'\)/",
        file_get_contents($file), $found, PREG_SET_ORDER);
    foreach ($found as $hit) {
        $alias = Cake\Utility\Inflector::camelize(
            Cake\Utility\Inflector::pluralize(preg_replace('/s$/', '', $hit[1])));
        $table = TMM_ROOT . '/src/Model/Table/' . $alias . 'Table.php';
        if (!is_file($table)) {
            continue;   // cannot tell which table the variable holds
        }
        $checked++;
        $source = file_get_contents($table);
        preg_match_all("/(belongsTo|hasOne)\(\s*'([A-Za-z0-9_]+)'/", $source, $m, PREG_SET_ORDER);
        $properties = [];
        foreach ($m as $one) {
            $properties[] = Cake\Utility\Inflector::underscore(
                Cake\Utility\Inflector::singularize($one[2]));
        }
        if (!$properties || in_array($hit[2], $properties, true)) {
            continue;
        }
        $undeclared[] = basename(dirname($file)) . '/' . basename($file)
            . ': $' . $hit[1] . '->has(\'' . $hit[2] . '\')';
    }
}
checkTrue('there are some to check (' . $checked . ')', $checked > 20);
// course_major is the one left: Trainee, Apprentice and Candidate courses each
// document that association, and no table class declares it - there is no
// CourseMajors model in this application at all. Those three cells still show
// the id, and will until somebody decides whether that model should exist.
$remaining = array_values(array_filter($undeclared, function ($one) {
    return strpos($one, 'course_major') === false;
}));
check('every other one names an association its table declares', $remaining, []);

echo "  the name columns that were read by the wrong name\n";
$wrong = [];
foreach (glob(TMM_ROOT . '/src/Template/*/*.ctp') as $file) {
    foreach (file($file) as $number => $line) {
        if (strpos($line, '->fullname') === false) {
            continue;
        }
        // The guarded fallback chains in the preview screens ask isset() first
        // and are reading whatever entity they were handed, so they are not
        // claiming the column exists.
        if (strpos($line, 'isset(') !== false
            || basename($file) === 'preview.ctp') {
            continue;
        }
        $wrong[] = basename(dirname($file)) . '/' . basename($file) . ':' . ($number + 1);
    }
}
check('nothing reads fullname on an entity that has no such column', $wrong, []);

$profile = file_get_contents(TMM_ROOT . '/src/Template/Users/profile.ctp');
checkTrue('the profile page reads the column users really has',
    strpos($profile, '$user->full_name') !== false);

finish($db);
