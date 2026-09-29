<?php
/**
 * Every page guide, rendered.
 *
 * The guides replaced 89 copies of a 250-line process_flow.ctp with one small
 * array per module and one renderer. That is a good trade, and it moves the
 * risk: a guide is now data, and a key spelt wrong in data renders as a gap in
 * the page with a notice nobody sees - debug is off on the server, so the
 * reader gets a guide with a missing section and no sign that anything failed.
 *
 * So each one is rendered here, and any warning from the application's own code
 * fails the check. This also catches a guide that names a screen which no
 * longer exists, and a diagram whose syntax the renderer chokes on.
 */
require __DIR__ . '/lib/harness.php';

$guides = glob(TMM_ROOT . '/config/page_guides/*.php');
sort($guides);
checkTrue('there are guides to render', count($guides) > 0);

$rendered = 0;
$problems = 0;
$thin = [];

foreach ($guides as $file) {
    $module = basename($file, '.php');
    list($html, $warnings) = renderView('page_guide', ['module' => $module],
        ['isElement' => true, 'controller' => $module,
         'url' => '/' . strtolower($module) . '/process-flow',
         'params' => ['controller' => $module, 'action' => 'processFlow']]);

    if ($warnings) {
        $problems++;
        printf("  %-62s FAIL\n", $module);
        foreach ($warnings as $warning) {
            echo '      ', $warning, "\n";
        }
        continue;
    }

    $rendered++;
    // A guide that renders but says almost nothing is worth knowing about: it
    // means the array is there and empty, which reads as a working page.
    if (strlen($html) < 1500) {
        $thin[] = $module . ' (' . strlen($html) . ' bytes)';
    }
}

check('every guide renders without a warning from our own code', $problems, 0);
check('and all of them rendered', $rendered, count($guides));

echo "  what they contain\n";
// One guide read closely, so the renderer is known to be putting the data on
// the page rather than merely not falling over.
$one = 'TraineeScoreAverages';
list($html, $warnings) = renderView('page_guide', ['module' => $one],
    ['isElement' => true, 'controller' => $one,
     'url' => '/trainee-score-averages/process-flow',
     'params' => ['controller' => $one, 'action' => 'processFlow']]);
$guide = include TMM_ROOT . '/config/page_guides/' . $one . '.php';

check('the render is clean', $warnings, []);
checkTrue('the title is on the page', strpos($html, $guide['title']) !== false);
checkTrue('and the lead', strpos($html, substr($guide['lead'], 0, 40)) !== false);
checkTrue('each caution is on the page', count(array_filter($guide['cautions'],
    function ($caution) use ($html) {
        return strpos($html, substr($caution, 0, 40)) !== false;
    })) === count($guide['cautions']));
checkTrue('and each step', count(array_filter($guide['steps'],
    function ($step) use ($html) {
        return strpos($html, $step['title']) !== false;
    })) === count($guide['steps']));

if ($thin) {
    echo "\n  guides that render almost nothing (an empty array reads as a page):\n";
    foreach ($thin as $one) {
        echo '      ', $one, "\n";
    }
}

finish();
