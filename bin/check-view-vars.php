<?php
/**
 * Which view variables does a template read that nobody sets?
 *
 * This is one of the quietest faults in the application. A template names a
 * variable, no controller sets it, and the page renders a blank where the answer
 * should be - or, where the read is wrapped in isset(), an empty dropdown that
 * looks like a list still loading. PHP says nothing worth noticing, the page
 * returns 200, and the screen is simply wrong.
 *
 * It has cost real screens: the trainee score averages, two panels of the
 * stakeholder dashboard, and the address card that bake stamped onto fourteen
 * forms, where four dropdowns read $propinsis, $kabupatens, $kecamatans and
 * $kelurahans and no controller has ever set any of them.
 *
 * What counts as set: anything the controller, AppController, a controller trait
 * or a component passes to set(), setVars() or viewVars, including through
 * compact(); anything the layout reads, since the layout is fed separately; and
 * anything the template assigns before it reads.
 *
 * Reads are found with the tokenizer rather than a pattern, so a closure
 * parameter, a foreach target, a list() and a catch variable all count as
 * assignments rather than as missing variables.
 *
 * It also answers the question you have to ask before acting on any of that:
 * is this template rendered at all? A template whose action does nothing but
 * redirect, or which has no action and is rendered by nothing, reports every
 * variable it reads as missing forever - and it is not a screen with a hole in
 * it, it is a file nobody sees. Trainees/dashboard.ctp read nine names nobody
 * set and looked like a whole empty dashboard; its action redirects to
 * Dashboard::training, and the live template there is a superset of it.
 *
 * Usage: php bin/check-view-vars.php [--all] [--root=PATH]
 *   --all   also list the names that are read but assigned later in the file,
 *           which is legal and usually harmless.
 *   --root  look at another tree than this one. tests/check_view_vars.php uses
 *           it to hold this script to a small tree whose answers are known.
 */
$root = dirname(__DIR__);
$showAll = false;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--all') {
        $showAll = true;
    } elseif (strpos($arg, '--root=') === 0) {
        $root = rtrim(substr($arg, 7), '/');
    }
}
if (!is_dir($root . '/src/Template')) {
    fwrite(STDERR, "No src/Template under $root\n");
    exit(2);
}

/**
 * add_interview -> addInterview, the name its action would carry.
 *
 * Inflector would do this, but loading the framework would make this script
 * need vendor/, and it is most useful on a machine where that is not certain.
 *
 * @param string $name Template file name.
 * @return string
 */
function actionName($name)
{
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
}

/**
 * Why nothing renders this template, or null when something does.
 *
 * Only the start of the action's body is read. Whether it is a bare redirect is
 * decided by its first statement, and looking for the method's closing brace
 * instead would mean guessing at brace-counting through strings and heredocs.
 *
 * @param string $php The controller's source.
 * @param string $action The template's name, without .ctp.
 * @return string|null
 */
function whyUnrendered($php, $action)
{
    $names = array_unique([$action, actionName($action)]);
    foreach ($names as $name) {
        $quoted = preg_quote($name, '/');
        if (preg_match('/(?:render|setTemplate)\(\s*[\'"]' . $quoted . '[\'"]/', $php)) {
            return null;
        }
    }

    $head = null;
    foreach ($names as $name) {
        if (preg_match('/public function ' . preg_quote($name, '/') . '\s*\([^)]*\)[^{;]*\{/s',
            $php, $m, PREG_OFFSET_CAPTURE)) {
            $head = substr($php, $m[0][1] + strlen($m[0][0]), 400);
            break;
        }
    }
    if ($head === null) {
        return 'no action of that name, and no render() names it';
    }
    // An action whose first statement is a redirect never reaches its template.
    if (preg_match('/^\s*(?:(?:\/\/|#)[^\n]*\n\s*)*return \$this->redirect\(([^;]*)\);/s',
        $head, $m)) {
        return 'its action only redirects: ' . trim(preg_replace('/\s+/', ' ', $m[1]));
    }

    return null;
}

// Names a template holds without anybody passing them in.
$free = ['this', 'GLOBALS', '_SERVER', '_GET', '_POST', '_COOKIE', '_FILES',
    '_ENV', '_REQUEST', '_SESSION', 'argv', 'argc', 'http_response_header'];

/**
 * Every name a file hands to the view, however it spells it.
 *
 * @param string $php Source.
 * @return array
 */
function viewVarsSet($php)
{
    $names = [];
    // ->set('name', ...) and ->set("name", ...)
    preg_match_all('/->set\(\s*[\'"]([A-Za-z0-9_]+)[\'"]/', $php, $m);
    $names = array_merge($names, $m[1]);
    // 'name' => ... inside a ->set([ ... ]) or ->setVars([ ... ]) call
    if (preg_match_all('/->set(?:Vars)?\(\s*\[(.*?)\]\s*\)/s', $php, $blocks)) {
        foreach ($blocks[1] as $block) {
            preg_match_all('/[\'"]([A-Za-z0-9_]+)[\'"]\s*=>/', $block, $keys);
            $names = array_merge($names, $keys[1]);
        }
    }
    // compact('a', 'b') anywhere - it is only ever used to hand vars over
    preg_match_all('/compact\(([^)]*)\)/', $php, $compacts);
    foreach ($compacts[1] as $block) {
        preg_match_all('/[\'"]([A-Za-z0-9_]+)[\'"]/', $block, $keys);
        $names = array_merge($names, $keys[1]);
    }
    // viewVars['name'] = ...
    preg_match_all('/viewVars\[[\'"]([A-Za-z0-9_]+)[\'"]\]/', $php, $m);
    $names = array_merge($names, $m[1]);

    return array_unique($names);
}

/**
 * The variables a template reads before it assigns them.
 *
 * @param string $php Source.
 * @return array name => line of the first read
 */
function readBeforeAssigned($php)
{
    $tokens = token_get_all($php);
    // Drop whitespace and comments, keeping the line of each token.
    $flat = [];
    foreach ($tokens as $token) {
        if (is_array($token)) {
            if (in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            $flat[] = [$token[0], $token[1], $token[2]];
            continue;
        }
        $line = $flat ? $flat[count($flat) - 1][2] : 1;
        $flat[] = [null, $token, $line];
    }

    $assignOps = ['=', '.=', '+=', '-=', '*=', '/=', '%=', '**=', '??=',
        '|=', '&=', '^=', '<<=', '>>='];
    $seen = [];
    $count = count($flat);
    for ($i = 0; $i < $count; $i++) {
        if ($flat[$i][0] !== T_VARIABLE) {
            continue;
        }
        $name = substr($flat[$i][1], 1);
        if (isset($seen[$name])) {
            continue;
        }

        $written = false;
        $next = isset($flat[$i + 1]) ? $flat[$i + 1][1] : '';
        // Step back over a type, so a typed parameter and a catch both look
        // like a bare one: function (array $keys), catch (Exception $e).
        $at = $i;
        while ($at > 0 && (in_array($flat[$at - 1][0],
            [T_STRING, T_ARRAY, T_CALLABLE, T_NS_SEPARATOR], true)
            || $flat[$at - 1][1] === '?' || $flat[$at - 1][1] === '|')) {
            $at--;
        }
        $prev = $at > 0 ? $flat[$at - 1] : [null, '', 0];

        if (in_array($next, $assignOps, true)) {
            $written = true;
        } elseif ($next === '[') {
            // $name['k'] = ... writes, $name['k'] alone reads. Walk the
            // subscripts and look at what follows the last one.
            $depth = 0;
            $j = $i + 1;
            while ($j < $count) {
                if ($flat[$j][1] === '[') {
                    $depth++;
                } elseif ($flat[$j][1] === ']') {
                    $depth--;
                    if ($depth === 0 && (!isset($flat[$j + 1]) || $flat[$j + 1][1] !== '[')) {
                        break;
                    }
                }
                $j++;
            }
            $after = isset($flat[$j + 1]) ? $flat[$j + 1][1] : '';
            $written = in_array($after, $assignOps, true);
        } elseif ($prev[0] === T_AS || $prev[0] === T_GLOBAL || $prev[0] === T_STATIC
            || $prev[0] === T_DOUBLE_ARROW || $prev[1] === '&' || $prev[1] === ',') {
            // foreach (... as $k => $v), global, static, a by-reference
            // parameter, and a comma inside a parameter list or use() clause.
            // The comma case is decided below by looking for the bracket.
            $written = $prev[0] !== null && $prev[1] !== ',';
            if ($prev[1] === ',' || $prev[1] === '&') {
                $written = inParameterList($flat, $at);
            }
        } elseif ($prev[1] === '(') {
            $written = inParameterList($flat, $at);
        }
        if (!$written) {
            $written = inDestructuring($flat, $i);
        }

        $seen[$name] = $written ? false : $flat[$i][2];
    }

    return array_filter($seen, function ($line) {
        return $line !== false;
    });
}

/**
 * Is this variable a target of [$a, $b] = ... ?
 *
 * Short list syntax assigns, but reads like a subscript to a pattern. The
 * bracket it sits in is told apart from an array access by what stands in front
 * of it, and from an array literal by the '=' that follows its close.
 *
 * @param array $flat Tokens.
 * @param int $at Index of the variable.
 * @return bool
 */
function inDestructuring(array $flat, $at)
{
    $depth = 0;
    $open = null;
    for ($i = $at; $i >= 0; $i--) {
        $text = $flat[$i][1];
        if ($text === ']') {
            $depth++;
            continue;
        }
        if ($text === '[') {
            if ($depth > 0) {
                $depth--;
                continue;
            }
            $open = $i;
            break;
        }
        if ($text === ';' || $text === '{' || $text === '}' || $text === '(') {
            return false;
        }
    }
    if ($open === null) {
        return false;
    }
    // A subscript, not a list: $row['k'], foo()['k'], $a[0][1].
    $before = $open > 0 ? $flat[$open - 1] : [null, '', 0];
    if ($before[0] === T_VARIABLE || in_array($before[1], [')', ']'], true)) {
        return false;
    }
    // foreach ($rows as [$a, $b]) assigns too, with no '=' to show it.
    if ($before[0] === T_AS) {
        return true;
    }

    $depth = 0;
    for ($i = $open, $n = count($flat); $i < $n; $i++) {
        if ($flat[$i][1] === '[') {
            $depth++;
        } elseif ($flat[$i][1] === ']') {
            $depth--;
            if ($depth === 0) {
                return isset($flat[$i + 1]) && $flat[$i + 1][1] === '=';
            }
        }
    }

    return false;
}

/**
 * Is this variable inside a signature, a use() clause or a catch?
 *
 * @param array $flat Tokens.
 * @param int $at Index of the variable.
 * @return bool
 */
function inParameterList(array $flat, $at)
{
    $depth = 0;
    for ($i = $at; $i >= 0; $i--) {
        $text = $flat[$i][1];
        if ($text === ')') {
            $depth++;
            continue;
        }
        if ($text === '(') {
            if ($depth > 0) {
                $depth--;
                continue;
            }
            $before = $i > 0 ? $flat[$i - 1] : [null, '', 0];
            $two = $i > 1 ? $flat[$i - 2] : [null, '', 0];

            return in_array($before[0], [T_FUNCTION, T_FN, T_USE, T_CATCH, T_LIST], true)
                || in_array($two[0], [T_FUNCTION, T_FN], true)
                || $before[1] === 'list';
        }
        if ($text === ';' || $text === '{' || $text === '}') {
            return false;
        }
    }

    return false;
}

// What the shared places hand over, which every template gets.
$shared = [];
foreach (array_merge(
    [$root . '/src/Controller/AppController.php'],
    glob($root . '/src/Controller/*Trait.php'),
    glob($root . '/src/Controller/Traits/*.php'),
    glob($root . '/src/Controller/Component/*.php'),
    glob($root . '/src/View/*.php'),
    glob($root . '/src/View/Helper/*.php')
) as $file) {
    if (!is_file($file)) {
        continue;
    }
    $shared = array_merge($shared, viewVarsSet(file_get_contents($file)));
}

// The layout is rendered with its own variables, so a name it reads is one the
// application supplies somewhere for it and not a template's business.
$layoutReads = [];
foreach (glob($root . '/src/Template/Layout/*.ctp') as $file) {
    $layoutReads = array_merge($layoutReads,
        array_keys(readBeforeAssigned(file_get_contents($file))));
}

$controllers = [];
foreach (array_merge(
    glob($root . '/src/Controller/*Controller.php'),
    glob($root . '/src/Controller/*/*Controller.php')
) as $file) {
    $controllers[basename($file, 'Controller.php')] = $file;
}

$skipDirs = ['Element', 'Layout', 'Error', 'Email', 'Bake', 'Plugin'];
$templates = 0;
$noController = [];
$findings = [];
$unrendered = [];
foreach (glob($root . '/src/Template/*', GLOB_ONLYDIR) as $dir) {
    $name = basename($dir);
    if (in_array($name, $skipDirs, true)) {
        continue;
    }
    if (!isset($controllers[$name])) {
        $noController[] = $name;
        continue;
    }
    $controllerSource = file_get_contents($controllers[$name]);
    $sets = array_merge(viewVarsSet($controllerSource), $shared);
    foreach (glob($dir . '/*.ctp') as $file) {
        $templates++;
        // Asked first: a template nothing renders is not a screen with a hole
        // in it, and reporting its variables as missing sends somebody looking
        // for a page that does not exist.
        $why = whyUnrendered($controllerSource, basename($file, '.ctp'));
        if ($why !== null) {
            $unrendered[$name . '/' . basename($file)] = $why;
            continue;
        }
        $reads = readBeforeAssigned(file_get_contents($file));
        $missing = [];
        foreach ($reads as $variable => $line) {
            if (in_array($variable, $free, true)
                || in_array($variable, $sets, true)
                || in_array($variable, $layoutReads, true)) {
                continue;
            }
            $missing[$variable] = $line;
        }
        if ($missing) {
            $findings[$name . '/' . basename($file)] = $missing;
        }
    }
}

$total = 0;
foreach ($findings as $file => $missing) {
    echo $file, "\n";
    foreach ($missing as $variable => $line) {
        printf("    line %-5d $%s\n", $line, $variable);
        $total++;
    }
}
if ($unrendered) {
    echo $findings ? "\n" : '';
    echo "Nothing renders these, so what they read was not checked:\n";
    foreach ($unrendered as $file => $why) {
        printf("  %-48s %s\n", $file, $why);
    }
}

printf("\n%d template(s) checked in %d controller(s)\n", $templates, count($controllers));
if ($noController) {
    printf("%d template folder(s) have no controller of that name: %s\n",
        count($noController), implode(', ', $noController));
}
if ($total) {
    printf("%d name(s) in %d template(s) that nothing sets\n", $total, count($findings));
} else {
    echo "every variable a rendered template reads is set by something\n";
}
if ($unrendered) {
    printf("%d template(s) nothing renders\n", count($unrendered));
}
exit($total || $unrendered ? 1 : 0);
