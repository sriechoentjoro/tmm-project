<?php
/**
 * The income statement and the balance sheet, and the period they cover.
 *
 * Two silences met on this page. The reports summed every journal line
 * whatever the entry's status, so a voided entry still moved the figures;
 * counting posted entries only is right, but it also means the numbers moved
 * the day the rule changed, and a figure that changed because the rule behind
 * it changed is worse than a wrong one - nobody can tell without being told.
 * And the period was fixed, so "the report" meant a span nobody had chosen.
 *
 * Both are now stated on the page, which only helps if the page really says
 * them. An empty report caused by a typo in a date looks exactly like an empty
 * report caused by having no entries.
 */
require __DIR__ . '/lib/harness.php';

$income = [
    ['code' => '4000', 'name' => 'Training Revenue', 'type' => 'Revenue',
     'total_debit' => 0, 'total_credit' => 1300000, 'balance' => -1300000],
    ['code' => '4900', 'name' => 'Other Income', 'type' => 'Revenue',
     'total_debit' => 0, 'total_credit' => 0, 'balance' => 0],
    ['code' => '5100', 'name' => 'Document Expense', 'type' => 'Expense',
     'total_debit' => 400000, 'total_credit' => 0, 'balance' => 400000],
];
$sheet = [
    ['code' => '1000', 'name' => 'Cash', 'type' => 'Asset',
     'total_debit' => 900000, 'total_credit' => 0, 'balance' => 900000],
    ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability',
     'total_debit' => 0, 'total_credit' => 900000, 'balance' => -900000],
];

/**
 * Render the report in one state.
 *
 * @param string $label What state this is.
 * @param array $vars rows, reportTitle, period, excluded, countedStatus.
 * @return string
 */
function report($label, array $vars)
{
    $action = stripos($vars['reportTitle'], 'Income') !== false
        ? 'incomeStatement' : 'balanceSheet';

    return renderClean($label, 'financial_report', $vars + ['excluded' => [],
        'countedStatus' => 'Posted'],
        ['controller' => 'Reports', 'templatePath' => 'Reports',
         'url' => '/reports/' . $action,
         'params' => ['controller' => 'Reports', 'action' => $action]]);
}

echo "  an income statement over the default period\n";
$html = report('renders', ['rows' => $income, 'reportTitle' => 'Income Statement',
    'period' => ['from' => '2026-01-01', 'to' => '2026-09-26',
        'default' => true, 'swapped' => false]]);
checkTrue('there is a period bar', strpos($html, 'class="period-bar"') !== false);
// Counted by the field names, not by type="date": the stylesheet in this
// template carries an input[type="date"] rule, which would be counted too.
checkTrue('a span has both ends', substr_count($html, 'name="from"') === 1
    && substr_count($html, 'name="to"') === 1);
checkTrue('both are prefilled with the period in force',
    strpos($html, 'value="2026-01-01"') !== false
    && strpos($html, 'value="2026-09-26"') !== false);
checkTrue('the scope note states what was counted and when',
    strpos($html, 'entries marked') !== false && strpos($html, 'dated') !== false);
checkTrue('no swap notice, because nothing was swapped',
    strpos($html, 'class="period-swapped"') === false);
// The reset link is the way back, so it must not appear when you are already
// on the default - it would offer to take you where you are.
checkTrue('and no way back, because this is the default',
    strpos($html, 'class="period-reset"') === false);

echo "  a period somebody chose\n";
$html = report('renders', ['rows' => $income, 'reportTitle' => 'Income Statement',
    'period' => ['from' => '2025-04-01', 'to' => '2025-06-30',
        'default' => false, 'swapped' => false],
    'excluded' => ['Void' => 1, 'Draft' => 2]]);
checkTrue('the chosen dates are in the boxes',
    strpos($html, 'value="2025-04-01"') !== false);
checkTrue('there is a way back to the default',
    strpos($html, 'class="period-reset"') !== false);
checkTrue('and what was left out is counted', strpos($html, '1 Void') !== false
    && strpos($html, '2 Draft') !== false);

echo "  dates the wrong way round\n";
// An empty report caused by a typo looks exactly like an empty report caused by
// having no entries, which is why this says so rather than showing nothing.
$html = report('renders', ['rows' => $income, 'reportTitle' => 'Income Statement',
    'period' => ['from' => '2026-01-01', 'to' => '2026-06-30',
        'default' => false, 'swapped' => true]]);
checkTrue('are said to have been swapped',
    strpos($html, 'class="period-swapped"') !== false);
checkTrue('and why that is worth saying',
    strpos($html, 'looks exactly like an empty report') !== false);

echo "  a balance sheet\n";
$html = report('renders', ['rows' => $sheet, 'reportTitle' => 'Balance Sheet',
    'period' => ['from' => null, 'to' => '2026-09-26',
        'default' => true, 'swapped' => false]]);
checkTrue('takes one date, not a span', substr_count($html, 'name="to"') === 1
    && substr_count($html, 'name="from"') === 0);
checkTrue('and says why', strpos($html, 'position on one day') !== false);
checkTrue('the scope note reads as at, not between',
    strpos($html, 'everything up to') !== false);
checkTrue('there is no From box to fill in', strpos($html, 'name="from"') === false);

echo "  nothing to report\n";
// A report with no rows still has to say what it counted, or an empty page
// reads as a broken one.
$html = report('renders', ['rows' => [], 'reportTitle' => 'Income Statement',
    'period' => ['from' => '2026-01-01', 'to' => '2026-09-26',
        'default' => true, 'swapped' => false]]);
checkTrue('the scope is still stated', strpos($html, 'entries marked') !== false);
checkTrue('and the period bar is still there',
    strpos($html, 'class="period-bar"') !== false);

echo "  the figures\n";
$html = report('renders', ['rows' => $income, 'reportTitle' => 'Income Statement',
    'period' => ['from' => '2026-01-01', 'to' => '2026-09-26',
        'default' => true, 'swapped' => false]]);
checkTrue('an account is named', strpos($html, 'Training Revenue') !== false);
checkTrue('and its figure is on the page in rupiah',
    strpos($html, 'Rp 1.300.000') !== false);
// An account with nothing in it is still listed: a chart of accounts with a
// gap in it is how a missing posting rule hides.
checkTrue('an account with nothing in it is still listed',
    strpos($html, 'Other Income') !== false);

finish();
