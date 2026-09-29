<?php
/**
 * What an export carries.
 *
 * Every export action asked for the same four columns - ID, Name, Created,
 * Modified - because that is what bake writes and none of the two hundred and
 * forty-five places it was written in was ever filled in. Of the ninety-two
 * tables here four have all three of name, created and modified and fifty-four
 * have none, and ExportTrait answers '' for a column that is not there. So
 * every export was an id and three empty cells, and nothing said so.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

$near = sys_get_temp_dir() . '/tmm_export_near.sqlite';
$far = sys_get_temp_dir() . '/tmm_export_far.sqlite';
$here = sqliteConnections(['default'], $near);
@unlink($far);
ConnectionManager::drop('other_db');
ConnectionManager::setConfig('other_db', ['className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Sqlite', 'database' => $far]);
$there = ConnectionManager::get('other_db');

$here->execute('CREATE TABLE lessons (id INTEGER PRIMARY KEY, subject VARCHAR(255),
    tutor_id INTEGER, room_id INTEGER, campus_id INTEGER, score INTEGER, held_on DATE)');
$here->execute('CREATE TABLE tutors (id INTEGER PRIMARY KEY, title VARCHAR(255))');
$here->execute('CREATE TABLE rooms (id INTEGER PRIMARY KEY, code VARCHAR(255))');
$there->execute('CREATE TABLE campuses (id INTEGER PRIMARY KEY, title VARCHAR(255))');

$here->execute("INSERT INTO tutors (id, title) VALUES (1, 'Pak Budi')");
$here->execute("INSERT INTO rooms (id, code) VALUES (7, 'R-7')");
$there->execute("INSERT INTO campuses (id, title) VALUES (3, 'Kampus Timur')");
$here->execute("INSERT INTO lessons (id, subject, tutor_id, room_id, campus_id, score, held_on)
    VALUES (1, 'Bahasa Jepang', 1, 7, 3, 88, '2026-05-10')");

$locator = TableRegistry::getTableLocator();

/**
 * Build the lessons table with the associations a case needs.
 *
 * @param array $associations Alias => options for belongsTo.
 * @return \Cake\ORM\Table
 */
function lessonsWith(array $associations)
{
    global $locator;
    $locator->clear();

    $tutors = $locator->get('Tutors');
    $tutors->setTable('tutors')->setDisplayField('title');
    $rooms = $locator->get('Rooms');
    // Nothing to show but the number that is already in the column.
    $rooms->setTable('rooms')->setDisplayField('id');
    $campuses = $locator->get('Campuses');
    $campuses->setTable('campuses')->setDisplayField('title');
    $campuses->setConnection(ConnectionManager::get('other_db'));

    $lessons = $locator->get('Lessons');
    $lessons->setTable('lessons')->setDisplayField('subject');
    foreach ($associations as $alias => $options) {
        $lessons->belongsTo($alias, $options);
    }

    return $lessons;
}

list($controller, $exportColumns) = reachInto('App\Controller\AppController', 'exportColumns');
$nested = new ReflectionMethod('App\Controller\AppController', 'getNestedValue');
$nested->setAccessible(true);
$format = new ReflectionMethod('App\Controller\AppController', 'formatCsvValue');
$format->setAccessible(true);

echo "  the columns\n";
// Campuses is joined, and sits in another database: it cannot be followed.
$query = lessonsWith(['Tutors' => [], 'Rooms' => [], 'Campuses' => []])->find();
list($headers, $fields) = $exportColumns->invoke($controller, $query);
check('every column of the table is exported', $fields,
    ['id', 'subject', 'tutor.title', 'room_id', 'campus_id', 'score', 'held_on']);
check('each headed as a person would write it', $headers,
    ['ID', 'Subject', 'Tutor', 'Room', 'Campus', 'Score', 'Held On']);
checkTrue('a foreign key is exported as the name it points at',
    in_array('tutor.title', $fields, true));
checkTrue('a target with nothing but its id to show keeps the id',
    in_array('room_id', $fields, true));
checkTrue('and one joined across databases is not followed',
    in_array('campus_id', $fields, true));

echo "  and the query still runs\n";
$row = $query->first();
checkTrue('the association it followed was added to the query',
    $row->has('tutor') && $row->tutor->title === 'Pak Budi');

echo "  the line that comes out\n";
$values = [];
foreach ($fields as $field) {
    $values[] = $format->invoke($controller, $nested->invoke($controller, $row, $field));
}
check('carries what somebody opened the file for', $values,
    ['1', 'Bahasa Jepang', 'Pak Budi', '7', '3', '88', '2026-05-10']);
$old = [];
foreach (['id', 'name', 'created', 'modified'] as $field) {
    $old[] = $format->invoke($controller, $nested->invoke($controller, $row, $field));
}
check('where it used to carry an id and three empty cells', $old, ['1', '', '', '']);

echo "  dates\n";
// FrozenDate is not a \DateTime - both it and FrozenTime extend
// DateTimeImmutable - and only FrozenTime was named, so a date column fell
// through to (string) and came out as the locale's short form, 5/10/26.
check('a date is written so a spreadsheet can read it',
    $format->invoke($controller, new Cake\I18n\FrozenDate('2026-05-10')), '2026-05-10');
check('a mutable date too',
    $format->invoke($controller, new Cake\I18n\Date('2026-05-10')), '2026-05-10');
check('and a date with a time',
    $format->invoke($controller, new Cake\I18n\FrozenTime('2026-05-10 14:30:00')),
    '2026-05-10 14:30:00');

echo "  a belongsTo that fetches separately\n";
// It can be followed whatever database it is in.
$query = lessonsWith(['Campuses' => ['strategy' => 'select']])->find();
list($headers, $fields) = $exportColumns->invoke($controller, $query);
checkTrue('reaches the other database', in_array('campus.title', $fields, true));
check('and the name comes back with the row',
    (string)$nested->invoke($controller, $query->first(), 'campus.title'), 'Kampus Timur');

echo "  the file itself\n";
$responseProp = new ReflectionProperty('Cake\Controller\Controller', 'response');
$responseProp->setAccessible(true);
$responseProp->setValue($controller, new Cake\Http\Response());

$query = lessonsWith(['Tutors' => []])->find();
list($headers, $fields) = $exportColumns->invoke($controller, $query);
ob_start();
$controller->doExportCsv($query, 'Lessons', $headers, $fields);
$csv = ob_get_clean();
$lines = array_values(array_filter(explode("\n", str_replace("\r", '', $csv))));
check('the header line names the real columns', trim($lines[0], "\xEF\xBB\xBF"),
    'ID,Subject,Tutor,Room,Campus,Score,"Held On"');
check('and the line under it carries the values', $lines[1],
    '1,"Bahasa Jepang","Pak Budi",7,3,88,2026-05-10');

echo "  a table wider than the alphabet\n";
// The Excel writer asked for chr(64 + count($headers)) as its last column,
// which is right up to Z and then walks off the end; the throw was caught and
// turned into a CSV named .xlsx.
$columns = [];
for ($i = 1; $i <= 30; $i++) {
    $columns[] = 'col' . $i . ' VARCHAR(20)';
}
$here->execute('CREATE TABLE wides (id INTEGER PRIMARY KEY, ' . implode(', ', $columns) . ')');
$here->execute('INSERT INTO wides (id, col1, col30) VALUES (1, ?, ?)', ['first', 'last']);
$locator->clear();
$wides = $locator->get('Wides');
$wides->setTable('wides')->setDisplayField('col1');
$query = $wides->find();
list($headers, $fields) = $exportColumns->invoke($controller, $query);
check('exports every one of its columns', count($headers), 31);

$responseProp->setValue($controller, new Cake\Http\Response());
$response = $controller->doExportExcel($query, 'Wides', $headers, $fields);
$body = (string)$response->getBody();
check('and Export Excel really is a spreadsheet, not a CSV in disguise',
    substr($body, 0, 2), 'PK');
checkTrue('with the right name on it',
    strpos($response->getHeaderLine('Content-Disposition'), '.xlsx') !== false);

@unlink($far);
finish($near);
