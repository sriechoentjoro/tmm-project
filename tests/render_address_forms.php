<?php
/**
 * The address controls on the forms the bake generator stamped them onto.
 *
 * The generator's address branch fired on any field matching /propinsi|province/
 * and then emitted four controls named propinsi_id, kabupaten_id, kecamatan_id
 * and kelurahan_id, throwing away the field it had matched. So on fourteen forms
 * the real column got no control: master_propinsi_id on a trainee, an apprentice
 * and both education tables; kode_propinsi on every region table. The four it
 * did emit read $propinsis, $kabupatens, $kecamatans and $kelurahans, which no
 * controller sets, so every one of them was empty, and the names they posted are
 * not columns, so save() dropped them.
 *
 * None of it errored. The dropdowns looked like lists still loading, and a
 * province could not be set from the form at all - which also froze the three
 * lists below it, because those are scoped to the province already on file.
 *
 * What is checked here is what the screen must show: one card, one control per
 * region column, the options the controller really sets, the saved value chosen,
 * and an id the cascade script can bind to.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

// FormHelper reads the schema through the entity's table, so the tables have to
// answer. Only the columns the forms touch are needed, and asking for exactly
// those keeps the render honest: a control for a column that is not there would
// fail here rather than on the screen.
$db = sys_get_temp_dir() . '/tmm_address_forms.sqlite';
$conn = sqliteConnections(['cms_masters', 'cms_tmm_trainees', 'default'], $db);
$conn->execute('CREATE TABLE master_propinsis (id INTEGER PRIMARY KEY,
    kode_propinsi VARCHAR(10), title VARCHAR(255))');
$conn->execute('CREATE TABLE master_kabupatens (id INTEGER PRIMARY KEY,
    propinsi_id INTEGER, kode_propinsi VARCHAR(10), kode_kabupaten VARCHAR(10),
    title VARCHAR(255))');
$conn->execute('CREATE TABLE master_kecamatans (id INTEGER PRIMARY KEY,
    propinsi_id INTEGER, kabupaten_id INTEGER, kode_propinsi VARCHAR(10),
    kode_kabupaten VARCHAR(10), kode_kecamatan VARCHAR(10), title VARCHAR(255))');
$conn->execute('CREATE TABLE master_kelurahans (id INTEGER PRIMARY KEY,
    propinsi_id INTEGER, kabupaten_id INTEGER, kecamatan_id INTEGER,
    kode_propinsi VARCHAR(10), kode_kabupaten VARCHAR(10),
    kode_kecamatan VARCHAR(10), kode_kelurahan VARCHAR(10),
    kode_pos VARCHAR(10), title VARCHAR(255))');
$conn->execute('CREATE TABLE trainee_educations (id INTEGER PRIMARY KEY,
    trainee_id INTEGER, master_strata_id INTEGER, master_propinsi_id INTEGER,
    master_kabupaten_id INTEGER, college_entry_date DATE,
    college_graduate_date DATE, college_name VARCHAR(255),
    college_major VARCHAR(255))');

$propinsis = [11 => 'Aceh', 32 => 'Jawa Barat', 33 => 'Jawa Tengah'];
$kabupatens = [3201 => 'Bogor', 3273 => 'Bandung'];
$kecamatans = [320101 => 'Nanggung', 320102 => 'Leuwiliang'];

/**
 * A saved row of one of the region tables, or of trainee_educations.
 *
 * @param string $model Table alias.
 * @param array $values Its columns.
 * @return \Cake\Datasource\EntityInterface
 */
function saved($model, array $values)
{
    $table = TableRegistry::getTableLocator()->get($model);
    $row = $table->newEntity($values, ['validate' => false,
        'accessibleFields' => ['*' => true]]);
    $row->setNew(false);

    return $row;
}

/**
 * How many controls the form carries for a column.
 *
 * @param string $html The rendered form.
 * @param string $field Column name.
 * @return int
 */
function controlsFor($html, $field)
{
    return preg_match_all('/name="' . preg_quote($field, '/') . '"/', $html);
}

echo "  a kelurahan, whose three parents are all its own columns\n";
$html = renderClean('renders', 'edit', [
    'masterKelurahan' => saved('MasterKelurahans', ['propinsi_id' => 32,
        'kabupaten_id' => 3201, 'kecamatan_id' => 320101,
        'kode_propinsi' => '32', 'kode_kabupaten' => '3201',
        'kode_kecamatan' => '320101', 'kode_kelurahan' => '3201011001',
        'kode_pos' => '16650', 'title' => 'Bantarkaret']),
    'masterPropinsis' => $propinsis,
    'masterKabupatens' => $kabupatens,
    'masterKecamatans' => $kecamatans,
], ['controller' => 'MasterKelurahans', 'templatePath' => 'MasterKelurahans', 'url' => '/master-kelurahans/edit/1',
    'params' => ['controller' => 'MasterKelurahans', 'action' => 'edit',
        'pass' => ['1']]]);

check('one address card', preg_match_all('/Address Information/', $html), 1);
foreach (['propinsi_id', 'kabupaten_id', 'kecamatan_id'] as $field) {
    check('one control for ' . $field . ', where there were three', controlsFor($html, $field), 1);
}
checkTrue('the province list is the one the controller sets',
    strpos($html, '>Jawa Barat<') !== false && strpos($html, '>Aceh<') !== false);
checkTrue('and its saved value is the chosen one',
    preg_match('/<option value="32" selected="selected">/', $html) === 1);
checkTrue('the kabupaten list too', strpos($html, '>Bogor<') !== false);
checkTrue('and the kecamatan list', strpos($html, '>Nanggung<') !== false);
checkTrue('the province select carries an id the cascade binds to',
    preg_match('/id="[a-z-]*propinsi-id"/', $html) === 1);
checkTrue('and so do the two below it',
    preg_match('/id="[a-z-]*kabupaten-id"/', $html) === 1
    && preg_match('/id="[a-z-]*kecamatan-id"/', $html) === 1);
check('no control for kelurahan_id, which is not a column of this table',
    controlsFor($html, 'kelurahan_id'), 0);
check('the kode_propinsi the address branch used to swallow is back',
    controlsFor($html, 'kode_propinsi'), 1);
checkTrue('nothing reads a list nobody sets',
    strpos($html, 'Notice') === false && strpos($html, 'Undefined') === false);

echo "  a propinsi, which has no parent region at all\n";
$html = renderClean('renders', 'edit', [
    'masterPropinsi' => saved('MasterPropinsis',
        ['kode_propinsi' => '32', 'title' => 'Jawa Barat']),
], ['controller' => 'MasterPropinsis', 'templatePath' => 'MasterPropinsis', 'url' => '/master-propinsis/edit/1',
    'params' => ['controller' => 'MasterPropinsis', 'action' => 'edit',
        'pass' => ['1']]]);

check('no address card, because there is no region to choose',
    preg_match_all('/Address Information/', $html), 0);
check('kode_propinsi has a control', controlsFor($html, 'kode_propinsi'), 1);
checkTrue('holding what is on file', strpos($html, 'value="32"') !== false);
foreach (['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id'] as $field) {
    check('no control for ' . $field, controlsFor($html, $field), 0);
}

echo "  an education record, which names its region master_propinsi_id\n";
$html = renderClean('renders', 'edit', [
    'traineeEducation' => saved('TraineeEducations', ['trainee_id' => 7,
        'master_strata_id' => 3, 'master_propinsi_id' => 33,
        'master_kabupaten_id' => 3273, 'college_name' => 'Politeknik Negeri']),
    'trainees' => [7 => 'Budi Santoso'],
    'masterStratas' => [3 => 'S1'],
    'masterPropinsis' => $propinsis,
    'masterKabupatens' => $kabupatens,
], ['controller' => 'TraineeEducations', 'templatePath' => 'TraineeEducations', 'url' => '/trainee-educations/edit/1',
    'params' => ['controller' => 'TraineeEducations', 'action' => 'edit',
        'pass' => ['1']]]);

check('one control for master_propinsi_id, which had none at all',
    controlsFor($html, 'master_propinsi_id'), 1);
check('and one for master_kabupaten_id, beside it in the same card',
    controlsFor($html, 'master_kabupaten_id'), 1);
checkTrue('the province list is there',
    strpos($html, '>Jawa Tengah<') !== false);
checkTrue('with the saved province chosen',
    preg_match('/<option value="33" selected="selected">/', $html) === 1);
checkTrue('the cascade can bind to it',
    preg_match('/id="master-propinsi-id"/', $html) === 1);
foreach (['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id'] as $field) {
    check('no control named bare ' . $field,
        preg_match_all('/name="' . $field . '"/', $html), 0);
}

finish($db);
