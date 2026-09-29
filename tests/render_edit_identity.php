<?php
/**
 * The hidden id an edit form carries, and the file it decides the fate of.
 *
 * Seventeen edit templates guarded that field with a variable nobody sets:
 *
 *     <?= $this->Form->create($traineeFamilyStory, ...) ?>
 *     <?php if (!empty($traineeFamilyStorie->id)): ?>
 *         <?= $this->Form->hidden('id') ?>
 *
 * The name is the plural with its trailing s chopped off, not a singular, so
 * only the tables whose plural is not a bare -s were hit: Stories, Families,
 * Categories, Statuses, Batches. !empty() on an undefined variable is false and
 * says nothing, so the guard was simply never true and the field never rendered.
 *
 * Saving still worked - edit() reads the id from the URL - so it looked like
 * nothing. What it cost is one step further on: uploadImage() and uploadFile()
 * in AppController delete the file being replaced, and they find the record to
 * delete it from by $this->request->getData('id'). With no id posted, that
 * branch never ran. On the five of the seventeen that carry an image_path -
 * the four Story tables and the institution one - replacing a photo left the
 * previous one in the web root, unreferenced by any record and still
 * downloadable by anyone holding its URL.
 *
 * So what is checked is the field itself: present with the real id on a saved
 * record, absent on a blank one, and never accessible enough to move a record's
 * primary key.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_edit_identity.sqlite';
$conn = sqliteConnections(['cms_tmm_trainees', 'cms_masters', 'default'], $db);
$conn->execute('CREATE TABLE trainee_family_stories (id INTEGER PRIMARY KEY,
    trainee_id INTEGER, title VARCHAR(255), date_occurrence DATE,
    problem_contents TEXT, problem_classification VARCHAR(255),
    problem_solution TEXT, problem_inference TEXT, image_path VARCHAR(255))');
$conn->execute('CREATE TABLE master_employee_statuses (id INTEGER PRIMARY KEY,
    slug VARCHAR(255), title VARCHAR(255), created DATETIME, updated DATETIME)');

/**
 * A row as edit() hands it over, or a blank one as add() does.
 *
 * @param string $model Table alias.
 * @param array $values Its columns.
 * @param int|null $id Its primary key, or null for a record not yet saved.
 * @return \Cake\Datasource\EntityInterface
 */
function record($model, array $values, $id = null)
{
    $table = TableRegistry::getTableLocator()->get($model);
    $row = $table->newEntity($values, ['validate' => false,
        'accessibleFields' => ['*' => true]]);
    if ($id !== null) {
        $row->id = $id;
        $row->setNew(false);
    }

    return $row;
}

/**
 * The hidden id field, if the form carries one.
 *
 * @param string $html Rendered form.
 * @return string|null Its value, or null when there is no such field.
 */
function hiddenId($html)
{
    if (!preg_match('/<input type="hidden" name="id" [^>]*value="([^"]*)"/', $html, $m)
        && !preg_match('/<input type="hidden" name="id" value="([^"]*)"/', $html, $m)) {
        return null;
    }

    return $m[1];
}

echo "  a story on file, which is one of the five carrying an image\n";
$html = renderClean('renders', 'edit', [
    'traineeFamilyStory' => record('TraineeFamilyStories', [
        'trainee_id' => 7, 'title' => 'Ayah sakit',
        'image_path' => 'img/uploads/traineefamilystories/AYAH.jpg',
    ], 412),
    'trainees' => [7 => 'Budi Santoso'],
], ['controller' => 'TraineeFamilyStories',
    'templatePath' => 'TraineeFamilyStories',
    'url' => '/trainee-family-stories/edit/412',
    'params' => ['controller' => 'TraineeFamilyStories', 'action' => 'edit',
        'pass' => ['412']]]);

check('the form carries the record id', hiddenId($html), '412');
checkTrue('so uploadImage() can find the file it is replacing',
    strpos($html, 'name="id"') !== false);
checkTrue('and the form still posts files', strpos($html, 'multipart/form-data') !== false);

echo "  a blank story, which has no id to carry\n";
$html = renderClean('renders', 'edit', [
    'traineeFamilyStory' => record('TraineeFamilyStories', ['trainee_id' => 7]),
    'trainees' => [7 => 'Budi Santoso'],
], ['controller' => 'TraineeFamilyStories',
    'templatePath' => 'TraineeFamilyStories',
    'url' => '/trainee-family-stories/add',
    'params' => ['controller' => 'TraineeFamilyStories', 'action' => 'add']]);

check('no hidden id is written', hiddenId($html), null);

echo "  a status, one of the twelve with no file at all\n";
$html = renderClean('renders', 'edit', [
    'masterEmployeeStatus' => record('MasterEmployeeStatuses',
        ['slug' => 'kontrak', 'title' => 'Karyawan Kontrak'], 3),
], ['controller' => 'MasterEmployeeStatuses',
    'templatePath' => 'MasterEmployeeStatuses',
    'url' => '/master-employee-statuses/edit/3',
    'params' => ['controller' => 'MasterEmployeeStatuses', 'action' => 'edit',
        'pass' => ['3']]]);

check('carries its id too', hiddenId($html), '3');

echo "  what the posted id may not do\n";
// Built without accessibleFields: passing that to newEntity() calls setAccess()
// on the entity, which sticks, so a fixture opened up that way would answer for
// itself rather than for the class - and would have reported this as broken.
$statuses = TableRegistry::getTableLocator()->get('MasterEmployeeStatuses');
$row = $statuses->newEntity();
$row->id = 3;
$row->title = 'Karyawan Tetap';
$row->setNew(false);
$statuses->patchEntity($row, ['id' => 99, 'title' => 'Diganti'],
    ['validate' => false]);
check('a posted id cannot move the record', $row->id, 3);
check('while the rest of the form still applies', $row->title, 'Diganti');

// The field is now written on seventeen forms, so the guarantee has to hold for
// all seventeen entities, not only the one rendered above.
$entities = ['AcceptanceOrganizationStory', 'ApprenticeFamily',
    'ApprenticeFamilyStory', 'CandidateDocumentCategory', 'CandidateFamily',
    'CooperativeAssociationStory', 'MasterApprenticeSubmissionDocumentCategory',
    'MasterDocumentPreparednessStatus', 'MasterDocumentSubmissionStatus',
    'MasterEmployeeStatus', 'MasterJobCategory', 'MasterMarriageStatus',
    'MasterOccupationCategory', 'TraineeFamily', 'TraineeFamilyStory',
    'TraineeTrainingBatch', 'VocationalTrainingInstitutionStory'];
$open = [];
foreach ($entities as $name) {
    $class = 'App\\Model\\Entity\\' . $name;
    $entity = new $class();
    if ($entity->isAccessible('id')) {
        $open[] = $name;
    }
}
check('no entity behind those forms lets a posted id through', $open, []);

echo "  every other edit form that replaces a file\n";
// The seventeen were the ones a variable name gave away. The fault is really
// "an edit form that uploads a file but posts no id", so the whole tree is
// asked, rather than trusting that the seventeen were all of them.
$missing = [];
foreach (glob(TMM_ROOT . '/src/Template/*/edit.ctp') as $template) {
    $folder = basename(dirname($template));
    $html = file_get_contents($template);
    if (!preg_match('/Form->create\(\$([A-Za-z0-9_]+)/', $html, $named)) {
        continue;
    }
    $entity = TMM_ROOT . '/src/Model/Entity/' . ucfirst($named[1]) . '.php';
    $controller = TMM_ROOT . '/src/Controller/' . $folder . 'Controller.php';
    if (!is_file($entity) || !is_file($controller)) {
        continue;
    }
    preg_match_all('/@property\s+(\S+)\s+\$([a-z_]+)/', file_get_contents($entity),
        $properties, PREG_SET_ORDER);
    $uploads = false;
    foreach ($properties as $property) {
        if (strpos($property[1], 'App\\Model\\Entity') !== false) {
            continue;
        }
        if (preg_match('/(image|photo|foto|gambar|file|attachment|document|pdf)/i',
            $property[2])) {
            $uploads = true;
            break;
        }
    }
    if (!$uploads || !preg_match('/uploadImage\(|uploadFile\(/',
        file_get_contents($controller))) {
        continue;
    }
    if (strpos($html, "Form->hidden('id')") === false) {
        $missing[] = $folder;
    }
}
check('all of them post the id, so none of them orphans a file', $missing, []);

finish($db);
