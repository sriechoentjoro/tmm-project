<?php
/**
 * The three yes/no flags that were asked as passwords.
 *
 * is_holding_passport, is_training_pass and is_apprenticeship_pass are
 * tinyint(1) columns. The bake template decided password fields with
 * preg_match('/(password|pass|pwd|kata_sandi)/i', $field), and "pass" is
 * inside all three names, so on six forms they were rendered as masked boxes
 * with "Minimum 8 characters, include uppercase, lowercase, number & symbol"
 * written underneath - seventeen controls in all. The generator does have a
 * boolean branch; it simply sat below the password one and never ran.
 *
 * The damage is not that they looked wrong. An empty password box posts an
 * empty string, Cake's boolean marshaller reads that as false, and the two
 * columns default to 1 in the database. So every candidate, trainee and
 * apprentice saved through those forms was recorded as having failed training
 * and failed the apprenticeship - the two figures ReportsController counts as
 * "passed", "in training" and "in Japan". Nothing errored and nobody typed
 * anything: leaving a field alone was enough.
 *
 * What is checked here is that consequence, not only the markup: the same
 * posts that used to flip a 1 to a 0 are run through the real tables.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_pass_flags.sqlite';
$conn = sqliteConnections(['cms_tmm_trainees', 'default'], $db);
$conn->execute('CREATE TABLE trainees (id INTEGER PRIMARY KEY, name VARCHAR(255),
    is_holding_passport BOOLEAN DEFAULT 0,
    is_training_pass BOOLEAN DEFAULT 1,
    is_apprenticeship_pass BOOLEAN DEFAULT 1,
    created DATETIME, modified DATETIME)');
$conn->insert('trainees', ['id' => 1, 'name' => 'MUHAMAD QOWIYADI',
    'is_holding_passport' => 1, 'is_training_pass' => 1,
    'is_apprenticeship_pass' => 1]);

$trainees = TableRegistry::getTableLocator()->get('Trainees');

/**
 * Patch the record with what a form posted and read the flag back.
 *
 * @param array $post What the form sent for the three flags.
 * @return \Cake\Datasource\EntityInterface The record as it would be saved.
 */
function posted(array $post)
{
    global $trainees;

    return $trainees->patchEntity($trainees->get(1), $post);
}

echo "  what an empty masked box used to do\n";
// This is the old form: nobody types a password into "Is Training Pass", so
// the box posts ''.
$was = posted(['is_training_pass' => '', 'is_apprenticeship_pass' => '']);
check('an empty string reads as false', $was->is_training_pass, false);
check('on both flags', $was->is_apprenticeship_pass, false);
// Rules and associations off: Trainees belongsTo thirteen tables across six
// databases, and saving would reach for every one of them. What is under test
// is what the column ends up holding, not whether the foreign keys resolve.
$trainees->save($was, ['checkRules' => false, 'associated' => false]);
check('and saving it writes 0 over the 1 that was there',
    (int)$trainees->get(1)->is_training_pass, 0);
check('on the other flag too', (int)$trainees->get(1)->is_apprenticeship_pass, 0);
// Back to passed, for the cases below.
$conn->update('trainees',
    ['is_training_pass' => 1, 'is_apprenticeship_pass' => 1], ['id' => 1]);

echo "  what a checkbox posts\n";
// Cake writes a hidden 0 beside every checkbox, so an unchecked box posts '0'
// and a checked one posts '1'. Both are deliberate now, and visible.
$off = posted(['is_training_pass' => '0']);
check('unchecked is false', $off->is_training_pass, false);
$on = posted(['is_training_pass' => '1']);
check('checked is true', $on->is_training_pass, true);
$saved = $trainees->save($on, ['checkRules' => false, 'associated' => false]);
checkTrue('and it saves', (bool)$saved);
check('as 1 in the column', (int)$trainees->get(1)->is_training_pass, 1);

echo "  the forms that asked them\n";
$screens = [
    'Apprentices/add', 'Apprentices/edit',
    'Candidates/add', 'Candidates/edit',
    'Trainees/add', 'Trainees/edit',
];
$flags = ['is_holding_passport', 'is_training_pass', 'is_apprenticeship_pass'];
foreach ($screens as $screen) {
    $file = TMM_ROOT . '/src/Template/' . $screen . '.ctp';
    if (!is_file($file)) {
        continue;
    }
    $markup = file_get_contents($file);
    $masked = 0;
    $boxes = 0;
    foreach ($flags as $flag) {
        if (!preg_match("/control\('" . $flag . "', \[(.*?)\n\s*\]\)/s", $markup, $m)) {
            continue;
        }
        if (strpos($m[1], "'type' => 'password'") !== false) {
            $masked++;
        }
        if (strpos($m[1], "'type' => 'checkbox'") !== false) {
            $boxes++;
        }
    }
    check($screen . ' masks none of them', $masked, 0);
    checkTrue($screen . ' asks the ones it has as checkboxes', $boxes > 0);
}

echo "  and the generator that wrote them that way\n";
foreach (['add', 'form'] as $which) {
    $bake = file_get_contents(TMM_ROOT . '/src/Template/Bake/Template/' . $which . '.ctp');
    $boolean = strpos($bake, "if (\$fieldType === 'boolean'):");
    $password = strpos($bake, 'PASSWORD FIELD DETECTION');
    checkTrue($which . '.ctp asks the column type before searching the name',
        $boolean !== false && $password !== false && $boolean < $password);
    checkTrue($which . '.ctp matches password as a whole word',
        strpos($bake, "'/(^|_)(password|passwd|pwd|kata_sandi|sandi)(_|$)/i'") !== false);
}

finish($db);
