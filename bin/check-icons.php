#!/usr/bin/env php
<?php
/**
 * Find icon names the bundled Font Awesome does not define.
 *
 * The application ships Font Awesome 5. Icon names were renamed on both sides
 * of it: version 4 used an -o suffix for outline icons and short names like
 * fa-pencil, fa-refresh and fa-dashboard; version 6 renamed them again to
 * fa-pen-to-square, fa-triangle-exclamation and friends. Writing either
 * produces no error anywhere - the stylesheet has no rule for the class, so
 * <i class="fas fa-clock-o"></i> renders an empty box. That looks like a
 * design choice rather than a fault, which is why 187 of them accumulated
 * across 45 files before anybody counted.
 *
 * This reads every fa-* name out of the templates, the page guides, the
 * controllers and the scripts, and checks the bundled stylesheet really
 * defines it.
 *
 * Two kinds of match are not icons and are skipped, each for a stated reason:
 * the sizing and animation helpers (fa-fw, fa-2x, fa-spin and the rest), and
 * the names listed in EXPECTED below.
 *
 * Usage:
 *     php bin/check-icons.php          report problems, exit 1 if any
 *     php bin/check-icons.php --all    also list every icon resolved
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', $argv, true);

/**
 * Classes that share the fa- prefix without naming a glyph.
 */
const HELPERS = [
    'fa-fw', 'fa-lg', 'fa-sm', 'fa-xs', 'fa-1x', 'fa-2x', 'fa-3x', 'fa-4x', 'fa-5x',
    'fa-6x', 'fa-7x', 'fa-8x', 'fa-9x', 'fa-10x', 'fa-spin', 'fa-pulse', 'fa-border',
    'fa-pull-left', 'fa-pull-right', 'fa-stack', 'fa-stack-1x', 'fa-stack-2x',
    'fa-inverse', 'fa-li', 'fa-ul', 'fa-rotate-90', 'fa-rotate-180', 'fa-rotate-270',
    'fa-flip-horizontal', 'fa-flip-vertical', 'fa-flip-both',
];

/**
 * Matches that look like an unknown icon but are not, with the reason.
 */
const EXPECTED = [
    // A template writes the class as fa-file- followed by a short PHP echo
    // tag that appends pdf, word or alt. The name is finished at runtime and
    // every branch of it is a real FA5 icon. (Spelling that echo tag out here
    // would close this PHP block: its own closing delimiter ends the file,
    // comment or not.)
    'fa-file-' => 'built at runtime from the file extension',
    // Element/entity_form.ctp says "fa-save, not fa-floppy-disk" to record
    // which library is installed. Renaming inside that sentence would erase
    // the note.
    'fa-floppy-disk' => 'named in a comment that explains the rule',
];

$cssFile = $root . '/webroot/css/fontawesome-all.min.css';
if (!is_file($cssFile)) {
    fwrite(STDERR, "cannot find webroot/css/fontawesome-all.min.css\n");
    exit(1);
}
$css = file_get_contents($cssFile);

preg_match_all('/\.(fa-[a-z0-9-]+):before/', $css, $m);
$known = array_flip($m[1]);
printf("%d icon name(s) defined by the bundled stylesheet\n", count($known));

/**
 * Every file the application renders or runs. Anything else - a .bak, a
 * .ctp.bak.<date>, a backup of the bake templates - is not searched: an icon
 * nobody can see is not a fault worth reporting.
 */
$exts = ['php', 'ctp', 'js', 'html'];
$dirs = [$root . '/src', $root . '/config', $root . '/webroot/js'];

$files = [];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file->isFile()) {
            continue;
        }
        if (!in_array(strtolower($file->getExtension()), $exts, true)) {
            continue;
        }
        if (strpos($file->getFilename(), 'fontawesome') !== false) {
            continue;
        }
        $files[] = $file->getPathname();
    }
}
sort($files);

$problems = [];
$expected = 0;
$checked = 0;
foreach ($files as $file) {
    $rel = ltrim(str_replace($root, '', $file), '/');
    foreach (file($file) as $n => $line) {
        if (!preg_match_all('/\bfa-[a-z0-9-]+/', $line, $hits)) {
            continue;
        }
        foreach ($hits[0] as $icon) {
            if (in_array($icon, HELPERS, true)) {
                continue;
            }
            if (isset(EXPECTED[$icon])) {
                $expected++;
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

printf(
    "%d icon use(s) checked in %d file(s), %d known exception(s)\n",
    $checked,
    count($files),
    $expected
);

if (!$problems) {
    echo "every icon named in the application exists in the bundled Font Awesome\n";
    exit(0);
}

printf("\n%d icon(s) the bundled Font Awesome does not define:\n\n", count($problems));
foreach ($problems as list($rel, $line, $icon)) {
    printf("  %s:%d\n    %s\n\n", $rel, $line, $icon);
}
echo "Font Awesome 4 names end in -o (fa-clock-o); version 6 names read like\n";
echo "fa-pen-to-square. This application ships version 5: fa-clock, fa-edit.\n";
exit(1);
