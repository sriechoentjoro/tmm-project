<?php
/**
 * The badge colours, and whether anything defines them.
 *
 * The layout loads Bootstrap 5.1.3, which removed the contextual badge classes:
 * badge-success and its siblings became bg-success and friends. The templates
 * were written against Bootstrap 4 and still write the old names in 115 places,
 * plus a dozen more built at render time as class="badge badge-<?= $status ?>".
 *
 * A bare .badge in Bootstrap 5 sets color: #fff and no background, so each of
 * those was white text on a white card - written, and unreadable. It surfaced on
 * the password reset screen, where the Enabled row looked empty.
 *
 * Some pages had noticed and defined the classes again in their own <style>
 * block, which is why it was patchy rather than total. webroot/css/badge-compat.css
 * fills in the rest, once, for every page.
 */
require __DIR__ . '/lib/harness.php';

$contextual = ['badge-primary', 'badge-secondary', 'badge-success', 'badge-danger',
    'badge-warning', 'badge-info', 'badge-light', 'badge-dark'];

$shim = file_get_contents(TMM_ROOT . '/webroot/css/badge-compat.css');

/**
 * The badge classes a file defines in CSS.
 *
 * @param string $css Source.
 * @return array
 */
function definesBadges($css)
{
    preg_match_all('/(?<![-\w])\.(badge-[a-z0-9-]+)\s*[,{:]/', $css, $m);

    return array_unique($m[1]);
}

/**
 * The badge classes a file writes into markup.
 *
 * Only inside a class attribute, and not where the name is the tail of a longer
 * one: .grade-badge-sm is somebody else's class, not a badge modifier.
 *
 * @param string $html Source.
 * @return array name => times
 */
function usesBadges($html)
{
    $found = [];
    preg_match_all('/class="[^"]*"/', $html, $attributes);
    foreach ($attributes[0] as $attribute) {
        preg_match_all('/(?<![-\w])(badge-[a-z0-9-]+)/', $attribute, $m);
        foreach ($m[1] as $name) {
            $found[$name] = isset($found[$name]) ? $found[$name] + 1 : 1;
        }
    }

    return $found;
}

echo "  the stylesheet itself\n";
$defined = definesBadges($shim);
foreach ($contextual as $name) {
    checkTrue($name . ' has a colour', in_array($name, $defined, true));
}
checkTrue('and badge-pill keeps its shape', in_array('badge-pill', $defined, true));
check('nothing is forced with !important, so a page can still override it',
    substr_count($shim, '!important'), 0);

echo "  the layout loads it\n";
$layout = file_get_contents(TMM_ROOT . '/src/Template/Layout/elegant.ctp');
checkTrue('elegant.ctp links badge-compat',
    strpos($layout, "Html->css('badge-compat')") !== false);
checkTrue('after Bootstrap, which has nothing to say about these names',
    strpos($layout, "Html->css('badge-compat')") > strpos($layout, 'bootstrap@5.1.3'));

echo "  every badge the templates write\n";
// Written as Bootstrap 4, resolved by the stylesheet above or by the page's own
// <style>; written as Bootstrap 5, resolved by Bootstrap. Anything else is a
// class name nothing defines, which is a badge with no colour.
$templates = array_merge(
    glob(TMM_ROOT . '/src/Template/*/*.ctp'),
    glob(TMM_ROOT . '/src/Template/*/*/*.ctp')
);
$everywhere = $defined;
foreach (array_merge(glob(TMM_ROOT . '/webroot/css/*.css'), $templates) as $file) {
    $everywhere = array_merge($everywhere, definesBadges(file_get_contents($file)));
}
$orphans = [];
$seen = 0;
foreach ($templates as $file) {
    foreach (usesBadges(file_get_contents($file)) as $name => $times) {
        $seen += $times;
        if (in_array($name, $everywhere, true)) {
            continue;
        }
        $orphans[] = basename(dirname($file)) . '/' . basename($file) . ': ' . $name;
    }
}
checkTrue('there are some to check (' . $seen . ' uses)', $seen > 100);
check('and none of them names a class nothing defines', $orphans, []);

echo "  the contextual ones in particular\n";
$uncovered = [];
foreach ($templates as $file) {
    foreach (usesBadges(file_get_contents($file)) as $name => $times) {
        if (!in_array($name, $contextual, true)) {
            continue;
        }
        if (!in_array($name, $defined, true)) {
            $uncovered[] = basename(dirname($file)) . '/' . basename($file) . ': ' . $name;
        }
    }
}
check('every Bootstrap 4 badge written anywhere is covered by the stylesheet',
    $uncovered, []);

echo "  and the screens that were found broken\n";
foreach (['Users/reset_password.ctp', 'Users/change_password.ctp'] as $screen) {
    $html = file_get_contents(TMM_ROOT . '/src/Template/' . $screen);
    check($screen . ' was moved to Bootstrap 5 names rather than left to the shim',
        count(array_intersect(array_keys(usesBadges($html)), $contextual)), 0);
}

finish();
