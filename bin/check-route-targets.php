#!/usr/bin/env php
<?php
/**
 * Find links that name a controller which does not exist.
 *
 * Three separate pages broke this way within a week, each time silently:
 *
 *   /admin/vocational-training-institutions/view/21   a link under /admin kept
 *       the prefix, but only App\Controller\VocationalTrainingInstitutionsController
 *       exists - there is no Admin\ one
 *   /lpk-registration/verify-email/<token>            the verification mail used
 *       'prefix' => false, but LpkRegistration exists ONLY under Admin\
 *
 * Neither shows up in a test run or a syntax check: CakePHP builds the URL
 * happily, and the failure only appears when somebody clicks. Worse, with no
 * Error/error400.ctp the result read "500 Internal Server Error", so a mis-built
 * link looked like a crashed server.
 *
 * This reads every array literal that names a controller explicitly, works out
 * the prefix in force, and checks the class is really there.
 *
 * Limits, by design rather than oversight: a link written as
 * ['action' => 'edit'] inherits the current controller and cannot be resolved
 * from the text alone, so it is skipped. Nothing is skipped silently - the
 * summary says how many were left out.
 *
 * Usage:
 *     php bin/check-route-targets.php            report problems, exit 1 if any
 *     php bin/check-route-targets.php --all      also list every link resolved
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);

/**
 * Controllers are checked as files, not through class_exists().
 *
 * PSR-4 maps App\Controller\ onto src/Controller/, so the two answer the same
 * question - but class_exists() autoloads, and autoloading a controller drags
 * in the whole framework and the application's own constants. Asking the
 * filesystem needs none of that, and cannot have side effects.
 */
function controllerFile($root, $prefix, $controller)
{
    $dir = $root . '/src/Controller/';
    if ($prefix) {
        foreach (explode('/', $prefix) as $part) {
            $dir .= str_replace(' ', '', ucwords(str_replace('_', ' ', $part))) . '/';
        }
    }

    return $dir . $controller . 'Controller.php';
}

$showAll = in_array('--all', array_slice($argv, 1), true);

/** Every PHP class and template under src/. */
function sources($root)
{
    $out = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
    foreach ($it as $file) {
        $p = $file->getPathname();
        $ext = substr($p, strrpos($p, '.') + 1);
        if (($ext === 'php' || $ext === 'ctp') && strpos($p, '/Bake') === false) {
            $out[] = $p;
        }
    }
    sort($out);

    return $out;
}

/**
 * The prefix a file's links default to.
 *
 * A template under src/Template/Admin/ or a controller in src/Controller/Admin/
 * is rendered for a prefixed request, so a link that says nothing about the
 * prefix keeps 'admin'. Everywhere else the default is no prefix.
 */
function defaultPrefix($root, $path)
{
    foreach (['/src/Template/', '/src/Controller/'] as $base) {
        $at = strpos($path, $root . $base);
        if ($at === 0) {
            $rel = substr($path, strlen($root . $base));
            $first = strtok($rel, '/');
            // Controller directories and template directories share their
            // names with prefixes; only the ones that exist as a prefix
            // directory under src/Controller count. Component is the exception
            // - it is a CakePHP convention directory, not a route prefix, and
            // treating it as one reported components' links as Admin\ ones.
            if ($first && $first !== 'Component' && is_dir($root . '/src/Controller/' . $first)) {
                return strtolower($first);
            }
        }
    }

    return null;
}

/**
 * Every method name a controller answers to, its traits and AppController
 * included.
 *
 * Read from the files rather than by reflection, for the same reason the class
 * itself is: loading a controller drags in the framework.
 *
 * @param string $root Application root.
 * @param string $file Controller or trait file.
 * @param array $cache Filled in as it goes, since AppController is asked for
 *  once per link otherwise.
 * @return array
 */
function actionsOf($root, $file, array &$cache)
{
    if (isset($cache[$file])) {
        return $cache[$file];
    }
    $cache[$file] = [];
    if (!is_file($file)) {
        return [];
    }

    $php = file_get_contents($file);
    preg_match_all('/function\s+([a-zA-Z_][A-Za-z0-9_]*)\s*\(/', $php, $m);
    $names = $m[1];

    // A trait brings its methods with it; ExportTrait and AjaxFilterTrait both
    // carry actions that links point at.
    preg_match_all('/^\s*use\s+([\\\\A-Za-z0-9_]+)\s*;/m', $php, $used);
    foreach ($used[1] as $name) {
        $short = ltrim(strrchr('\\' . $name, '\\'), '\\');
        foreach (['/src/Controller/', '/src/Controller/Traits/'] as $dir) {
            $candidate = $root . $dir . $short . '.php';
            if (is_file($candidate)) {
                $names = array_merge($names, actionsOf($root, $candidate, $cache));
                break;
            }
        }
    }

    $cache[$file] = array_values(array_unique($names));

    return $cache[$file];
}

$files = sources($root);
$problems = [];
$missingActions = [];
$actionCache = [];
$resolved = 0;
$skipped = 0;

foreach ($files as $path) {
    $lines = file($path);
    $default = defaultPrefix($root, $path);
    foreach ($lines as $n => $line) {
        // Two shapes, because one of them hid a bug for months.
        //
        //   'controller' => 'Users'                       a plain literal
        //   'controller' => $x ? 'Lpk' : 'SpecialSkill'   chosen at runtime
        //
        // The second reads as no controller at all to a pattern that wants a
        // quote straight after the arrow, so EmailServiceComponent's links to
        // two controllers that have never existed sat in a clean report. Every
        // quoted name on the line is taken instead: a branch is only worth
        // writing if either side can be reached, so either side naming a
        // missing controller is worth reporting.
        // Only as far as the next comma: that ends the value and keeps the
        // 'action' and 'prefix' keys that follow on the same line out of it.
        // No controller expression in this codebase contains a comma.
        if (!preg_match("/'controller'\s*=>\s*([^,]+)/", $line, $m)) {
            continue;
        }
        // Capitalised names only. A ternary's test reads as a quoted string
        // too - $type === 'lpk' ? 'Lpk' : 'SpecialSkill' - and 'lpk' there is a
        // value being compared, not a controller being named.
        if (!preg_match_all("/'([A-Z][A-Za-z0-9_]*)'/", $m[1], $names)) {
            continue;
        }
        $candidates = array_unique($names[1]);

        // The prefix may sit on the same line as the controller or on a
        // neighbouring one, since these arrays are routinely written across
        // several lines. Only the array this controller key belongs to counts.
        //
        // Reading a fixed window of thirteen lines as one string did not do
        // that. Two links can sit a few lines apart with different prefixes -
        // a View button with 'prefix' => false beside a Resend button with
        // 'prefix' => 'admin' - and the window then answered with whichever
        // pattern was tested first rather than the one belonging to this
        // link. It reported a correct link as pointing at a class that does
        // not exist, and skipped a wrong one as computed at runtime, both by
        // reading its neighbour. A checker that cries wolf spends the
        // reader's trust on nothing.
        //
        // So the window stops at a closing bracket in either direction: that
        // is where the neighbouring statement begins or ends.
        $lookAt = [$n];
        for ($i = $n - 1; $i >= max(0, $n - 6); $i--) {
            if (strpos($lines[$i], ']') !== false) {
                break;
            }
            $lookAt[] = $i;
        }
        if (strpos($line, ']') === false) {
            for ($i = $n + 1; $i < min(count($lines), $n + 7); $i++) {
                $lookAt[] = $i;
                if (strpos($lines[$i], ']') !== false) {
                    break;
                }
            }
        }

        $prefix = $default;
        $best = null;
        foreach ($lookAt as $i) {
            if (!preg_match("/'prefix'\s*=>\s*('[a-zA-Z]+'|false|null|\\$)/", $lines[$i], $pm)) {
                continue;
            }
            // Nearest wins, and a tie goes to the line above: the prefix key
            // is written before the controller key far more often than after.
            $distance = abs($i - $n) * 2 + ($i > $n ? 1 : 0);
            if ($best !== null && $distance >= $best) {
                continue;
            }
            $best = $distance;
            if ($pm[1] === '$') {
                $prefix = false;          // computed at runtime
            } elseif ($pm[1] === 'false' || $pm[1] === 'null') {
                $prefix = null;
            } else {
                $prefix = strtolower(trim($pm[1], "'"));
            }
        }

        if ($prefix === false) {
            // Computed at runtime - nothing to check.
            $skipped++;
            continue;
        }

        // The action belonging to this same array, by the same nearest-wins
        // rule. A link whose action is computed gives no literal to match and
        // is simply not checked; the controller still is.
        $action = null;
        $nearest = null;
        foreach ($lookAt as $i) {
            if (!preg_match("/'action'\s*=>\s*'([a-zA-Z_][A-Za-z0-9_]*)'/", $lines[$i], $am)) {
                continue;
            }
            $distance = abs($i - $n) * 2 + ($i > $n ? 1 : 0);
            if ($nearest !== null && $distance >= $nearest) {
                continue;
            }
            $nearest = $distance;
            $action = $am[1];
        }

        $rel = str_replace($root . '/', '', $path);
        foreach ($candidates as $controller) {
            $file = controllerFile($root, $prefix, $controller);
            $class = 'App\\Controller\\'
                . ($prefix ? str_replace(' ', '', ucwords(str_replace('_', ' ', $prefix))) . '\\' : '')
                . $controller . 'Controller';

            if (is_file($file)) {
                $resolved++;
                if ($showAll) {
                    printf("  ok   %s:%d  %s\n", $rel, $n + 1, $class);
                }
                // A class that exists is only half of it. Three buttons on
                // every candidate's page pointed at addInterview, addMcu and
                // uploadDocument, which have never existed, and four routes
                // pointed at the same places: every click a
                // MissingActionException, and this check said the links were
                // fine because it only ever asked about the class.
                if ($action !== null && count($candidates) === 1) {
                    $known = array_merge(
                        actionsOf($root, $file, $actionCache),
                        actionsOf($root, $root . '/src/Controller/AppController.php', $actionCache)
                    );
                    if (!in_array($action, $known, true)) {
                        $missingActions[] = [$rel, $n + 1, $class, $action];
                    }
                }
                continue;
            }

            // Say where it does exist, which is the fix nine times in ten.
            $alt = [];
            foreach ([null, 'admin'] as $try) {
                if ($try === $prefix) {
                    continue;
                }
                if (is_file(controllerFile($root, $try, $controller))) {
                    $alt[] = $try === null ? "'prefix' => false" : "'prefix' => '$try'";
                }
            }
            $problems[] = [$rel, $n + 1, $class, $alt];
        }
    }
}

printf("%d link(s) resolved, %d skipped (prefix computed at runtime)\n", $resolved, $skipped);

if ($problems) {
    printf("\n%d link(s) name a controller that does not exist:\n\n", count($problems));
    foreach ($problems as list($rel, $line, $class, $alt)) {
        printf("  %s:%d\n    %s\n", $rel, $line, $class);
        printf("    %s\n\n", $alt ? 'use ' . implode(' or ', $alt) : 'no such controller under any prefix');
    }
}

if ($missingActions) {
    printf("\n%d link(s) name an action the controller does not have:\n\n", count($missingActions));
    foreach ($missingActions as list($rel, $line, $class, $action)) {
        printf("  %s:%d\n    %s::%s\n\n", $rel, $line, $class, $action);
    }
}

if (!$problems && !$missingActions) {
    echo "every link naming a controller and an action points at one that exists\n";
    exit(0);
}
exit(1);
