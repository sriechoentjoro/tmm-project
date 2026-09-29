<?php
/**
 * The address dropdowns, on a form and on an index filter row.
 *
 * Both used to be find('list', ['limit' => 200]) against tables holding 507
 * kabupaten, 6,651 kecamatan and 84,305 kelurahan - the project's own export
 * of cms_masters says so. On a form that meant an edit whose saved region fell
 * outside the first two hundred showed "-- Select --" and posted nothing, so
 * opening a record to fix a date wiped its address. On a filter row it meant a
 * select of the places that happen to have low ids.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_region_lists.sqlite';
$conn = sqliteConnections(['cms_masters', 'default'], $db);

$conn->execute('CREATE TABLE master_propinsis (id INTEGER PRIMARY KEY, title VARCHAR(255))');
$conn->execute('CREATE TABLE master_kabupatens (id INTEGER PRIMARY KEY,
    propinsi_id INTEGER, title VARCHAR(255))');
$conn->execute('CREATE TABLE master_kecamatans (id INTEGER PRIMARY KEY,
    propinsi_id INTEGER, kabupaten_id INTEGER, title VARCHAR(255))');
$conn->execute('CREATE TABLE master_kelurahans (id INTEGER PRIMARY KEY,
    propinsi_id INTEGER, kabupaten_id INTEGER, kecamatan_id INTEGER, title VARCHAR(255))');

$conn->execute("INSERT INTO master_propinsis (id, title) VALUES (1, 'Jawa Barat'), (2, 'Papua')");
// Three hundred kabupaten, so the old two-hundred cap would have cut the list.
for ($i = 1; $i <= 300; $i++) {
    $conn->execute('INSERT INTO master_kabupatens (id, propinsi_id, title) VALUES (?, ?, ?)',
        [$i, $i <= 150 ? 1 : 2, sprintf('Kabupaten %03d', $i)]);
}
// And a kecamatan and kelurahan whose ids are far beyond it.
$conn->execute('INSERT INTO master_kecamatans (id, propinsi_id, kabupaten_id, title) VALUES
    (10, 1, 1, ?), (11, 1, 1, ?), (5000, 2, 300, ?)',
    ['Kecamatan Awal', 'Kecamatan Berikut', 'Kecamatan Jauh']);
$conn->execute('INSERT INTO master_kelurahans (id, propinsi_id, kabupaten_id, kecamatan_id, title)
    VALUES (20, 1, 1, 10, ?), (90000, 2, 300, 5000, ?)',
    ['Kelurahan Dekat', 'Kelurahan Jauh']);

list($controller, $forForm) = reachInto('App\Controller\CandidatesController', 'regionLists');
$forFilters = new ReflectionMethod('App\Controller\CandidatesController', 'regionListsForFilters');
$forFilters->setAccessible(true);

/**
 * The lists a form would be given for this record.
 *
 * @param \Cake\ORM\Entity|null $entity The record being edited.
 * @return array
 */
function forForm($entity)
{
    global $controller, $forForm;

    return $forForm->invoke($controller, $entity);
}

/**
 * The lists an index filter row would be given for this query string.
 *
 * @param string $queryString What the filter row posted back.
 * @return array
 */
function forFilters($queryString)
{
    global $controller, $forFilters;
    giveRequest($controller, $queryString);

    return $forFilters->invoke($controller);
}

echo "  a form, with the record's own chain\n";
$out = forForm(new Entity(['master_propinsi_id' => 2, 'master_kabupaten_id' => 300,
    'master_kecamatan_id' => 5000, 'master_kelurahan_id' => 90000]));
check('every province is offered', count($out['masterPropinsis']), 2);
check("the kabupaten list is the chosen province's", count($out['masterKabupatens']), 150);
checkTrue('and holds one past the old two hundred', isset($out['masterKabupatens'][300]));
check("the kecamatan list is the chosen kabupaten's", array_keys($out['masterKecamatans']), [5000]);
check("the kelurahan list is the chosen kecamatan's", array_keys($out['masterKelurahans']), [90000]);

echo "  a blank form\n";
$out = forForm(new Entity([]));
check('still offers every province', count($out['masterPropinsis']), 2);
check('but nothing below one', $out['masterKabupatens'], []);
check('no record at all is the same as a blank one', forForm(null)['masterKabupatens'], []);

echo "  what the record says is always listed\n";
// A row saved before its parent was set. The dropdown must still show what the
// record holds, or the next save posts an empty value over it.
$out = forForm(new Entity(['master_kelurahan_id' => 90000]));
check('a saved kelurahan with no parent chosen is still there',
    array_keys($out['masterKelurahans']), [90000]);
$out = forForm(new Entity(['master_kecamatan_id' => 5000, 'master_kelurahan_id' => 20]));
check('one outside the chosen kecamatan is kept beside those inside',
    [isset($out['masterKelurahans'][20]), isset($out['masterKelurahans'][90000])], [true, true]);
$out = forForm(new Entity(['master_kecamatan_id' => 10, 'master_kelurahan_id' => 777777]));
check('but an id no row carries is not invented', array_keys($out['masterKelurahans']), [20]);

echo "  reading the record\n";
// The tables that refer to a region call the column master_propinsi_id; the
// region tables call their own parent propinsi_id.
check("the region tables' own column spelling is read",
    count(forForm(new Entity(['propinsi_id' => 1]))['masterKabupatens']), 150);
$titles = array_values(forForm(new Entity(['master_propinsi_id' => 1]))['masterKabupatens']);
$sorted = $titles;
sort($sorted);
check('the list is in name order, not id order', $titles, $sorted);

echo "  an index filter row\n";
$out = forFilters('');
check('an unfiltered index offers every province', count($out['masterPropinsis']), 2);
check('and nothing below one, because there are 507 of those', $out['masterKabupatens'], []);
check('nor any kecamatan', $out['masterKecamatans'], []);
check('nor any kelurahan', $out['masterKelurahans'], []);

$out = forFilters('filter_master_propinsi_id=2');
check('choosing a province fills the kabupaten filter', count($out['masterKabupatens']), 150);
check('and leaves the one below it empty', $out['masterKecamatans'], []);

check('choosing a kabupaten fills the kecamatan filter',
    array_keys(forFilters('filter_master_propinsi_id=2&filter_master_kabupaten_id=300')['masterKecamatans']),
    [5000]);
check('and a kecamatan fills the kelurahan filter',
    array_keys(forFilters('filter_master_kabupaten_id=300&filter_master_kecamatan_id=5000')['masterKelurahans']),
    [90000]);
check('the other column spelling works here too',
    count(forFilters('filter_propinsi_id=1')['masterKabupatens']), 150);
check('a filter value that is not a number is ignored, not queried',
    forFilters('filter_master_propinsi_id=nonsense')['masterKabupatens'], []);
check('and an empty one is not a choice',
    forFilters('filter_master_propinsi_id=')['masterKabupatens'], []);

echo "  and nothing anywhere is capped any more\n";
$capped = [];
foreach (array_merge(glob(TMM_ROOT . '/src/Controller/*.php'),
        glob(TMM_ROOT . '/src/Controller/*/*.php')) as $path) {
    foreach (file($path) as $n => $line) {
        if (preg_match("/Master(Propinsis|Kabupatens|Kecamatans|Kelurahans)->find\('list'/", $line)
            && strpos($line, '200') !== false) {
            $capped[] = basename($path) . ':' . ($n + 1);
        }
    }
}
check('no region dropdown is held to two hundred rows', $capped, []);

finish($db);
