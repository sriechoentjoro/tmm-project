<?php
/**
 * The print view of an export.
 *
 * It used to work its own cells out, and named only FrozenTime among the date
 * classes - so a date column printed as the locale's short form, 5/10/26, which
 * is either the tenth of May or the fifth of October, while the same column in
 * the CSV read 2026-05-10. A value behind a dot went through no formatting at
 * all. Three exports of one list should not disagree about what is in it, so the
 * cells are laid out once in ExportTrait::doExportPrint() and this page only
 * prints them.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\Entity;

/**
 * Lay the cells out the way doExportPrint() does - through the same two helpers
 * the CSV goes through, which is the whole point of the change.
 *
 * @param array $rows Entities.
 * @param array $fields Field paths.
 * @return array Rows of formatted strings.
 */
function layOut(array $rows, array $fields)
{
    static $instance = null, $nested = null, $format = null;
    if ($instance === null) {
        $reflection = new ReflectionClass('App\Controller\AppController');
        $instance = $reflection->newInstanceWithoutConstructor();
        $nested = $reflection->getMethod('getNestedValue');
        $nested->setAccessible(true);
        $format = $reflection->getMethod('formatCsvValue');
        $format->setAccessible(true);
    }

    $out = [];
    foreach ($rows as $row) {
        $cells = [];
        foreach ($fields as $field) {
            $cells[] = $format->invoke($instance, $nested->invoke($instance, $row, $field));
        }
        $out[] = $cells;
    }

    return $out;
}

$headers = ['ID', 'Subject', 'Tutor', 'Room', 'Score', 'Held On'];
$fields = ['id', 'subject', 'tutor.title', 'room_id', 'score', 'held_on'];
$data = [
    new Entity(['id' => 1, 'subject' => 'Bahasa Jepang', 'room_id' => 7, 'score' => 88,
        'held_on' => new Cake\I18n\FrozenDate('2026-05-10'),
        'tutor' => new Entity(['title' => 'Pak Budi'])]),
    // A row with nothing in it but an id, which is ordinary in this data.
    new Entity(['id' => 2, 'subject' => 'Keselamatan Kerja', 'room_id' => null,
        'score' => null, 'held_on' => null, 'tutor' => null]),
];

/**
 * @param string $label What state this is.
 * @param array $rows Laid-out cells.
 * @param array $headers Column headings.
 * @return string
 */
function printView($label, array $rows, array $headers)
{
    return renderClean($label, 'export_print',
        ['rows' => $rows, 'headers' => $headers, 'title' => 'Lessons Report',
         'fields' => []],
        ['isElement' => true, 'controller' => 'Lessons',
         'url' => '/lessons/print-report',
         'params' => ['controller' => 'Lessons', 'action' => 'printReport']]);
}

echo "  an ordinary list\n";
$html = printView('renders', layOut($data, $fields), $headers);
checkTrue('the title is on the page', strpos($html, 'Lessons Report') !== false);
check('every heading is printed', count(array_filter($headers,
    function ($header) use ($html) {
        return strpos($html, $header) !== false;
    })), count($headers));
checkTrue('a followed association prints its name, not its id',
    strpos($html, 'Pak Budi') !== false);
checkTrue('a date prints the way the CSV writes it', strpos($html, '2026-05-10') !== false);
checkTrue("and not as the locale's short form", strpos($html, '5/10/26') === false);
check('the row count is the number of rows', substr_count($html, '<tr'), 3);
checkTrue('and it is stated', strpos($html, 'Total Records: <strong>2') !== false);

echo "  a row with nothing in it\n";
checkTrue('does not break the table', substr_count($html, '<td') === 12);

echo "  a table wider than the alphabet\n";
// Once the columns come from the table rather than being four every time, wide
// ones are ordinary.
$wideHeaders = [];
$wideFields = [];
for ($i = 1; $i <= 30; $i++) {
    $wideHeaders[] = 'Col ' . $i;
    $wideFields[] = 'col' . $i;
}
$html = printView('renders', layOut([new Entity(array_fill_keys($wideFields, 'x'))],
    $wideFields), $wideHeaders);
check('prints all thirty columns', substr_count($html, '<td'), 30);
// '<th' would also count the '<thead' that opens the table.
check('and all thirty headings', substr_count($html, '<th>'), 30);

echo "  an empty list\n";
$html = printView('renders', [], $headers);
checkTrue('still prints its headings', strpos($html, 'Held On') !== false);
checkTrue('and says none', strpos($html, 'Total Records: <strong>0') !== false);

echo "  the cells are laid out in one place\n";
// The template must not work them out itself, or it will disagree with the CSV
// again the next time a type is added.
$template = file_get_contents(TMM_ROOT . '/src/Template/Element/export_print.ctp');
// Looked for as code, not as words: the file explains in a comment what it
// used to do, and that comment is worth keeping.
$code = implode('', array_map(function ($token) {
    return is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)
        ? '' : (is_array($token) ? $token[1] : $token);
}, token_get_all($template)));
checkTrue('the template works no value out for itself',
    strpos($code, 'instanceof') === false && strpos($code, '->format(') === false
    && strpos($code, 'explode') === false);
checkTrue('and doExportPrint formats them through the CSV helpers',
    strpos(file_get_contents(TMM_ROOT . '/src/Controller/ExportTrait.php'),
        '$this->formatCsvValue($this->getNestedValue($row, $field))') !== false);

finish();
