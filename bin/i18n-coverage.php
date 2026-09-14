#!/usr/bin/env php
<?php
/**
 * Report what the interface cannot translate yet.
 *
 * The language switcher works — I18n::setLocale('ind'/'jpn') resolves through
 * ICU to src/Locale/id and src/Locale/ja — so a page that changes only partly
 * is never a switcher fault. It is one of two gaps:
 *
 *   missing   the string goes through __() but no catalog entry exists, so the
 *             English source is returned unchanged
 *   unwrapped the string is not in __() at all and can never be translated
 *
 * Text inside a <?php if ($currentLang === 'ind'): ?> branch counts as
 * neither. The process-flow pages write their prose once per language that
 * way instead of calling __(), so the literal in the Indonesian arm is
 * Indonesian and already switches correctly; reporting it as unwrapped would
 * put ~420 phantom entries in the total and hide the real gaps behind them.
 *
 * Usage:
 *     php bin/i18n-coverage.php                 summary per area
 *     php bin/i18n-coverage.php --area=Users    list one area's missing strings
 *     php bin/i18n-coverage.php --unwrapped     list likely unwrapped literals
 *     php bin/i18n-coverage.php --sync          append missing msgids to the
 *                                               catalogs with an empty msgstr
 *
 * --sync only adds entries; it never edits or removes an existing translation.
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$opts = [];
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--([a-z-]+)(?:=(.*))?$/', $arg, $m)) {
        $opts[$m[1]] = isset($m[2]) ? $m[2] : true;
    }
}

$catalogs = ['id' => $root . '/src/Locale/id/default.po',
             'ja' => $root . '/src/Locale/ja/default.po'];

/** Read the msgids a catalog defines, and which of them are translated. */
function readCatalog($path)
{
    $ids = [];
    $translated = [];
    $current = null;
    foreach (file($path) as $line) {
        $line = rtrim($line, "\r\n");
        if (preg_match('/^msgid "(.*)"$/', $line, $m)) {
            $current = stripcslashes($m[1]);
            if ($current !== '') {
                $ids[$current] = true;
            }
            continue;
        }
        if ($current !== null && preg_match('/^msgstr "(.*)"$/', $line, $m)) {
            if (stripcslashes($m[1]) !== '') {
                $translated[$current] = true;
            }
            $current = null;
        }
    }

    return [$ids, $translated];
}

/** Every template, excluding the bake generators and their backups. */
function templates($root)
{
    $out = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src/Template'));
    foreach ($it as $file) {
        $p = $file->getPathname();
        if (substr($p, -4) !== '.ctp' || strpos($p, '/Bake') !== false) {
            continue;
        }
        $out[] = $p;
    }
    sort($out);

    return $out;
}

/** The area a template belongs to — its directory under src/Template. */
function areaOf($root, $path)
{
    $rel = ltrim(str_replace($root . '/src/Template', '', $path), '/');
    $parts = explode('/', $rel);

    return count($parts) > 1 ? $parts[0] : 'root';
}

/**
 * Line numbers (1-based) that sit inside a $currentLang branch.
 *
 * Those arms hold prose already written per language, so a bare literal there
 * is translated copy, not a gap. The blocks do not nest in these templates,
 * but a depth counter keeps an inner if/endif from closing the outer one.
 */
function branchLines(array $lines)
{
    $inside = [];
    $depth = 0;
    foreach ($lines as $n => $line) {
        $opens = preg_match("/\bif\s*\(\s*\\\$currentLang\s*===/", $line);
        $closes = preg_match('/\bendif\b/', $line);
        if ($opens && $depth === 0) {
            $depth = 1;
            continue;
        }
        if ($depth > 0) {
            if (preg_match('/\bif\s*\(/', $line) && !preg_match('/\belseif\b/', $line)) {
                $depth++;
            }
            if ($closes) {
                $depth--;
                continue;
            }
            $inside[$n + 1] = true;
        }
    }

    return $inside;
}

$files = templates($root);

$used = [];        // msgid => [area => true]
$unwrapped = [];   // area => [ [file, line, text], ... ]
foreach ($files as $path) {
    $area = areaOf($root, $path);
    $lines = file($path);
    $branch = branchLines($lines);
    foreach ($lines as $n => $line) {
        if (preg_match_all("/__\(\s*'((?:[^'\\\\]|\\\\.)*)'/", $line, $m)) {
            foreach ($m[1] as $id) {
                $used[stripcslashes($id)][$area] = true;
            }
        }
        // Prose sitting straight in the markup: after an icon, inside a
        // heading or a hint, never reaching __(). Deliberately conservative —
        // it looks only where UI copy actually lives.
        if (isset($branch[$n + 1])) {
            continue;
        }
        // Whatever is already inside a __() call is translated, and the markup
        // a msgid carries is not markup the detector should look behind. Without
        // this, __('<strong>Birth Certificate</strong>') reads as a bare literal
        // sitting after a <strong>, and the help guides alone reported 104
        // strings that had in fact all been translated.
        // One pattern per quote character: a single-quoted PHP string routinely
        // carries double quotes of its own, as __('... role "lpk-penyangga" ...')
        // does, and a body that excludes both quote characters cannot span them.
        $bare = preg_replace(['/__\(\s*\'(?:[^\'\\\\]|\\\\.)*\'/',
                              '/__\(\s*"(?:[^"\\\\]|\\\\.)*"/'], '__(', $line);
        if (preg_match_all('#(?:</i>|<br>|<strong>|</strong>|</b>|<small[^>]*>|<th[^>]*>|<h[1-6][^>]*>|<span[^>]*>|<td[^>]*>|<p[^>]*>)\s*([A-Z][A-Za-z0-9 ,./()\'&%:+-]{11,})#', $bare, $m)) {
            foreach ($m[1] as $text) {
                $text = trim($text);
                if (strpos($text, '<?') !== false || strpos($text, 'http') === 0) {
                    continue;
                }
                $unwrapped[$area][] = [str_replace($root . '/', '', $path), $n + 1, $text];
            }
        }
    }
}

$report = [];
foreach ($catalogs as $lang => $path) {
    list($ids, $translated) = readCatalog($path);
    $report[$lang] = ['ids' => $ids, 'translated' => $translated];
}

// ---------------------------------------------------------------- --sync ---
if (!empty($opts['sync'])) {
    foreach ($catalogs as $lang => $path) {
        $known = $report[$lang]['ids'];
        $add = array_diff(array_keys($used), array_keys($known));
        if (!$add) {
            printf("%s: already complete\n", $lang);
            continue;
        }
        $out = "\n";
        foreach ($add as $id) {
            $out .= sprintf("msgid \"%s\"\nmsgstr \"\"\n\n", addcslashes($id, "\"\\\n"));
        }
        file_put_contents($path, rtrim(file_get_contents($path), "\n") . "\n" . $out, LOCK_EX);
        printf("%s: added %d msgid(s) with an empty translation\n", $lang, count($add));
    }
    exit(0);
}

// ----------------------------------------------------------- --unwrapped ---
if (!empty($opts['unwrapped'])) {
    $total = 0;
    foreach ($unwrapped as $area => $rows) {
        if (isset($opts['area']) && $opts['area'] !== true && $opts['area'] !== $area) {
            continue;
        }
        printf("\n%s\n", $area);
        foreach ($rows as list($file, $line, $text)) {
            printf("  %s:%d\n    %s\n", $file, $line, $text);
            $total++;
        }
    }
    printf("\n%d literal(s) not reaching __()\n", $total);
    exit(0);
}

// --------------------------------------------------------------- --area ----
if (isset($opts['area']) && $opts['area'] !== true) {
    $area = $opts['area'];
    printf("Strings used in %s with no translation:\n\n", $area);
    $n = 0;
    foreach ($used as $id => $areas) {
        if (!isset($areas[$area])) {
            continue;
        }
        $gap = [];
        foreach ($catalogs as $lang => $_) {
            if (empty($report[$lang]['translated'][$id])) {
                $gap[] = $lang;
            }
        }
        if ($gap) {
            printf("  [%s] %s\n", implode(',', $gap), $id);
            $n++;
        }
    }
    printf("\n%d string(s)\n", $n);
    exit(0);
}

// -------------------------------------------------------------- summary ----
// Seed from both maps. Building $areas from $used alone drops any area whose
// templates never call __() at all - exactly the areas most in need of the
// report - and their unwrapped literals went missing from the total with them.
$areas = [];
foreach (array_keys($unwrapped) as $area) {
    $areas[$area] = ['total' => 0];
}
foreach ($used as $id => $as) {
    foreach ($as as $area => $_) {
        $areas[$area]['total'] = ($areas[$area]['total'] ?? 0) + 1;
        foreach ($catalogs as $lang => $_x) {
            if (empty($report[$lang]['translated'][$id])) {
                $areas[$area][$lang] = ($areas[$area][$lang] ?? 0) + 1;
            }
        }
    }
}
uasort($areas, function ($a, $b) {
    return (($b['id'] ?? 0) + ($b['ja'] ?? 0)) <=> (($a['id'] ?? 0) + ($a['ja'] ?? 0));
});

printf("%-42s %7s %7s %7s %9s\n", 'area', 'strings', 'no id', 'no ja', 'unwrapped');
printf("%s\n", str_repeat('-', 78));
$sum = ['total' => 0, 'id' => 0, 'ja' => 0, 'un' => 0];
foreach ($areas as $area => $c) {
    $un = isset($unwrapped[$area]) ? count($unwrapped[$area]) : 0;
    printf("%-42s %7d %7d %7d %9d\n", $area, $c['total'], $c['id'] ?? 0, $c['ja'] ?? 0, $un);
    $sum['total'] += $c['total'];
    $sum['id'] += $c['id'] ?? 0;
    $sum['ja'] += $c['ja'] ?? 0;
    $sum['un'] += $un;
}
printf("%s\n", str_repeat('-', 78));
printf("%-42s %7d %7d %7d %9d\n", sprintf('%d templates, %d unique strings', count($files), count($used)),
    $sum['total'], $sum['id'], $sum['ja'], $sum['un']);
