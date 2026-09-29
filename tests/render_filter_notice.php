<?php
/**
 * The banner that says the list below it has been narrowed.
 *
 * A narrowed list looks exactly like a short one. Somebody who has forgotten
 * what is in the filter row - or arrived on a link that carried one - reads four
 * rows as four records. So the banner says it once, at the top, names what is
 * filtering, and offers the whole list back.
 *
 * It also names any filter that named a column the table does not have. That is
 * a fault in a template, not in what anybody typed, and it used to be invisible:
 * the filter simply did nothing.
 */
require __DIR__ . '/lib/harness.php';

/**
 * Render the notice for a state.
 *
 * @param string $label What state this is.
 * @param array $query What the filter row posted.
 * @param array $indexFilters What the controller worked out.
 * @return string
 */
function notice($label, array $query, array $indexFilters)
{
    return renderClean($label, 'index_filter_notice', ['indexFilters' => $indexFilters],
        ['isElement' => true, 'controller' => 'Widgets', 'url' => '/widgets',
         'query' => $query, 'params' => ['controller' => 'Widgets', 'action' => 'index']]);
}

echo "  a narrowed list\n";
$html = notice('renders', ['sort' => 'id', 'direction' => 'asc', 'page' => '3',
    'filter_name' => 'bud', 'filter_name_operator' => 'like'],
    ['applied' => ['name' => 'bud', 'score' => '5'], 'ignored' => []]);
checkTrue('says the list is narrowed', strpos($html, 'This list is narrowed') !== false);
checkTrue('names the column', strpos($html, '<strong>Name</strong>') !== false);
checkTrue('and what it was narrowed to', strpos($html, 'bud') !== false);
checkTrue('offers the whole list back', strpos($html, 'Show the whole list') !== false);
checkTrue('and that link drops every filter',
    strpos($html, 'filter_') === false);
checkTrue('and the page number, so the whole list starts at its first page',
    strpos($html, 'page=') === false);
checkTrue('while keeping the sort', strpos($html, 'sort=id') !== false);

echo "  a column the table does not have\n";
$html = notice('renders', ['filter_nosuch' => 'x'],
    ['applied' => [], 'ignored' => ['nosuch', 'alsonot']]);
checkTrue('is named', strpos($html, 'has no such column') !== false);
checkTrue('and all of them are', strpos($html, 'nosuch') !== false
    && strpos($html, 'alsonot') !== false);
checkTrue('without claiming the list is narrowed',
    strpos($html, 'This list is narrowed') === false);

echo "  both at once\n";
$html = notice('renders', ['filter_name' => 'bud', 'filter_nosuch' => 'x'],
    ['applied' => ['name' => 'bud'], 'ignored' => ['nosuch']]);
checkTrue('says both things', strpos($html, 'This list is narrowed') !== false
    && strpos($html, 'has no such column') !== false);

echo "  nothing to say\n";
$html = notice('renders', [], ['applied' => [], 'ignored' => []]);
check('prints nothing at all', trim($html), '');

echo "  and the layout only asks for it when there is something to ask\n";
// The variable is set by AppController::applyIndexFilters() and only when a
// filter was applied, so the layout must not render the element unconditionally.
$layout = file_get_contents(TMM_ROOT . '/src/Template/Layout/elegant.ctp');
checkTrue('the layout guards on the variable being set',
    strpos($layout, "isset(\$indexFilters) ? \$this->element('index_filter_notice') : ''") !== false);

finish();
