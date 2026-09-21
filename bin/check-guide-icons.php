#!/usr/bin/env php
<?php
/**
 * Find icon names that the bundled Font Awesome does not have.
 *
 * The application ships Font Awesome 5. Icon names were renamed wholesale in
 * Font Awesome 6 - fa-pen-to-square, fa-share-nodes, fa-table-list and the
 * rest - and writing a version 6 name produces no error anywhere: the CSS has
 * no rule for it, so <i class="fas fa-pen-to-square"></i> renders an empty
 * box. Six such names had reached the page guides before anyone noticed,
 * because a missing icon looks like a design choice.
 *
 * This reads every fa-* name out of the guide definitions and the guide
 * element and checks the bundled stylesheet really defines it.
 *
 * Usage:
 *     php bin/check-guide-icons.php          report problems, exit 1 if any
 *     php bin/check-guide-icons.php --all    also list every icon resolved
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', $argv, true);

$cssFile = $root . '/webroot/css/fontawesome-all.min.css';
if (!is_file($cssFile)) {
    fwrite(STDERR, "cannot find webroot/css/fontawesome-all.min.css\n");
    exit(1);
}
$css = file_get_contents($cssFile);

// Every class the stylesheet gives a glyph to.
preg_match_all('/\.(fa-[a-z0-9-]+):before/', $css, $m);
$known = array_flip($m[1]);
printf("%d icon name(s) defined by the bundled stylesheet\n", count($known));

$files = glob($root . '/config/page_guides/*.php');
$element = $root . '/src/Template/Element/page_guide.ctp';
if (is_file($element)) {
    $files[] = $element;
}

$problems = [];
$checked = 0;
foreach ($files as $file) {
    $rel = ltrim(str_replace($root, '', $file), '/');
    foreach (file($file) as $n => $line) {
        if (!preg_match_all('/\bfa-[a-z0-9-]+/', $line, $hits)) {
            continue;
        }
        foreach ($hits[0] as $icon) {
            // fa-fw, fa-2x and friends are sizing helpers, not glyphs.
            if (in_array($icon, ['fa-fw', 'fa-lg', 'fa-sm', 'fa-xs', 'fa-2x', 'fa-3x', 'fa-spin', 'fa-pull-left', 'fa-pull-right'], true)) {
                continue;
            }
            $checked++;
            if (isset($known[$icon])) {
                if ($showAll) {
                    printf("  ok    %-30s %s:%d\n", $icon, $rel, $n + 1);
                }
                continue;
            }
            $problems[] = [$rel, $n + 1, $icon];
        }
    }
}

printf("%d icon use(s) checked in %d file(s)\n", $checked, count($files));

if (!$problems) {
    echo "every icon named in a guide exists in the bundled Font Awesome\n";
    exit(0);
}

printf("\n%d icon(s) the bundled Font Awesome does not define:\n\n", count($problems));
foreach ($problems as list($rel, $line, $icon)) {
    printf("  %s:%d\n    %s\n\n", $rel, $line, $icon);
}
exit(1);
