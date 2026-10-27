#!/usr/bin/env php
<?php
/**
 * Hold Export and Print to the same rows the screen shows.
 *
 * Thirteen controllers narrow index() to the signed-in user's own
 * institution, and their Export CSV, Export Excel and Print buttons ran the
 * same query without that narrowing: the screen showed one institution's
 * candidates and the download held every institution's. The comment above
 * the filter line in each of those actions said "Export gives what the
 * screen shows rather than the whole table", which was true of the filters
 * and not of the scoping.
 *
 * Nothing caught it because an export is a separate action with its own
 * query, built by the generator beside index() and never revisited when the
 * scoping was added. So this asks the one question that matters: if index()
 * narrows by institution, does every action that writes an export narrow the
 * same way?
 *
 * An export action is recognised by what it does - it hands a query to
 * doExportCsv, doExportExcel or doExportPrint - rather than by being called
 * exportCsv. The next one will not be called that.
 *
 * This reads source only. It cannot tell whether the scoping is correct,
 * only whether it is there; tests/institution_scope.php runs the queries.
 *
 * Usage:
 *     php bin/check-export-scope.php          report gaps, exit 1 if any
 *     php bin/check-export-scope.php --all    also list what it checked
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', $argv, true);

/**
 * Every public action of a controller, with its body.
 *
 * @param string $php The controller source.
 * @return array [action => body]
 */
function actionBodies($php)
{
    if (!preg_match_all('/\n    public function (\w+)\s*\([^)]*\)\s*\n?\s*\{/',
        $php, $found, PREG_OFFSET_CAPTURE)) {
        return [];
    }
    $out = [];
    $count = count($found[1]);
    for ($i = 0; $i < $count; $i++) {
        $start = $found[0][$i][1];
        $end = $i + 1 < $count ? $found[0][$i + 1][1] : strlen($php);
        $out[$found[1][$i][0]] = substr($php, $start, $end - $start);
    }

    return $out;
}

/**
 * Whether a body narrows its query to the user's institution.
 *
 * Both helpers count: scopeToInstitution reads the ownership the controller
 * declares, getInstitutionFilteredQuery is the older one that takes the
 * column by name. An action that delegates to another action of the same
 * controller is answered by that one.
 *
 * @param string $body The action body.
 * @return bool
 */
function narrows($body)
{
    return (bool)preg_match(
        '/scopeToInstitution|getInstitutionFilteredQuery|applyInstitutionFilter/', $body);
}

/**
 * Whether a body writes an export, and the action it delegates to if it does
 * nothing but delegate.
 *
 * @param string $body The action body.
 * @return array [writes an export, delegated-to action or null]
 */
function exportsFrom($body)
{
    if (preg_match('/return \$this->(\w+)\(\);/', $body, $to)
        && !preg_match('/->find\(/', $body)) {
        return [false, $to[1]];
    }

    return [(bool)preg_match('/doExportCsv|doExportExcel|doExportPrint/', $body), null];
}

$gaps = [];
$checked = 0;
$scoped = 0;

foreach (glob($root . '/src/Controller/*Controller.php') as $path) {
    $name = basename($path, 'Controller.php');
    if ($name === 'App') {
        continue;
    }
    $bodies = actionBodies(file_get_contents($path));
    if (!isset($bodies['index']) || !narrows($bodies['index'])) {
        continue;
    }
    $scoped++;

    foreach ($bodies as $action => $body) {
        if ($action === 'index') {
            continue;
        }
        list($isExport, $delegatesTo) = exportsFrom($body);
        if ($delegatesTo !== null) {
            // Answered by the action it hands over to; it builds no query of
            // its own. exportPdf is printReport with a print layout.
            continue;
        }
        if (!$isExport) {
            continue;
        }
        $checked++;
        if (!narrows($body)) {
            $gaps[$name][] = $action;
        } elseif ($showAll) {
            printf("  %-40s %s\n", $name . '::' . $action, 'narrows the way index does');
        }
    }
}

printf("%d controller(s) narrow index() by institution; %d export action(s) checked\n",
    $scoped, $checked);

if (!$gaps) {
    echo "every export narrows to the same rows its index screen shows\n";
    exit(0);
}

$total = array_sum(array_map('count', $gaps));
printf("\n%d export action(s) in %d controller(s) answer with rows their own\n"
    . "index screen would not show:\n\n", $total, count($gaps));
foreach ($gaps as $name => $actions) {
    printf("  %-38s %s\n", $name, implode(', ', $actions));
}
echo "\nThe screen narrows to the user's institution and the download does not,\n";
echo "so Export hands over every other institution's records.\n";
exit(1);
