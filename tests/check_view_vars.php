<?php
/**
 * bin/check-view-vars.php, held to a tree whose answers are known.
 *
 * The script drives decisions now - it is what found the address card on
 * fourteen forms, the seventeen mis-named edit guards, and the templates
 * nothing renders - so a false positive in it costs somebody an afternoon, and
 * a false negative hides a blank screen.
 *
 * It reads templates with the tokenizer rather than a pattern, and the cases
 * that cost the most tuning are all here: a closure parameter, a typed
 * parameter, a catch variable, a foreach target and short list syntax all had
 * to stop being reported as missing variables, and each of them was reported
 * once.
 *
 * The second half is the question that has to come first. A template whose
 * action only redirects is not a screen with a hole in it; every variable it
 * reads is "missing" and none of it matters. Getting that wrong is not
 * hypothetical: Trainees/dashboard.ctp was read here as a whole empty
 * dashboard before anybody looked at what renders it, which is nothing.
 */
require __DIR__ . '/lib/harness.php';

$tree = sys_get_temp_dir() . '/tmm_view_vars_' . getmypid();

/**
 * Write a file into the fake tree, making its folder.
 *
 * @param string $path Relative to the tree root.
 * @param string $body Contents.
 * @return void
 */
function put($path, $body)
{
    global $tree;
    $full = $tree . '/' . $path;
    if (!is_dir(dirname($full))) {
        mkdir(dirname($full), 0777, true);
    }
    file_put_contents($full, $body);
}

/**
 * Remove the fake tree.
 *
 * @param string $path Directory or file.
 * @return void
 */
function scrub($path)
{
    if (is_dir($path)) {
        foreach (array_diff(scandir($path), ['.', '..']) as $entry) {
            scrub($path . '/' . $entry);
        }
        @rmdir($path);

        return;
    }
    @unlink($path);
}

/**
 * Run the script over the fake tree.
 *
 * @return array [output, exit code]
 */
function report()
{
    global $tree;
    $command = escapeshellarg(PHP_BINARY) . ' '
        . escapeshellarg(TMM_ROOT . '/bin/check-view-vars.php')
        . ' --root=' . escapeshellarg($tree) . ' 2>&1';
    $lines = [];
    $status = 0;
    exec($command, $lines, $status);

    return [implode("\n", $lines), $status];
}

scrub($tree);
put('src/Controller/AppController.php', '<?php
class AppController {
    public function beforeRender() { $this->set("everywhere", 1); }
}');

// A controller that sets what its templates read, in each of the three ways.
put('src/Controller/WidgetsController.php', '<?php
class WidgetsController extends AppController {
    public function index() {
        $widgets = []; $total = 0;
        $this->set(compact("widgets", "total"));
    }
    public function view($id = null) { $this->set("widget", $id); }
    public function edit($id = null) { $this->set(["widget" => $id, "options" => []]); }
    public function chart() { $this->render("chart_pane"); }
    public function gone() { return $this->redirect(["action" => "index"]); }
    public function tidy() {
        // Superseded by index
        return $this->redirect(["controller" => "Other", "action" => "index"]);
    }
}');

echo "  a variable nobody sets\n";
put('src/Template/Widgets/index.ctp',
    '<?= $widgets ?><?= $total ?><?= $everywhere ?><?= $missingOne ?>');
list($out, $status) = report();
checkTrue('is named, with its line', strpos($out, '$missingOne') !== false);
checkTrue('and the template it is in', strpos($out, 'Widgets/index.ctp') !== false);
check('the run fails', $status, 1);
checkTrue('what compact() hands over is not named',
    strpos($out, '$widgets') === false && strpos($out, '$total') === false);
checkTrue('nor what AppController sets for every page',
    strpos($out, '$everywhere') === false);

echo "  the shapes that are assignments, not reads\n";
put('src/Template/Widgets/index.ctp', '<?php
usort($widgets, function ($a, $b) { return $a["n"] <=> $b["n"]; });
foreach ($widgets as $key => $row) { echo $key . $row; }
try { $when = new DateTime(); } catch (Exception $e) { echo $e->getMessage(); }
$nameOf = function ($record, array $keys) { return $keys[0]; };
[$label, $colour] = $total;
echo $label . $colour;
');
list($out, $status) = report();
check('none of them is reported', $status, 0);
checkTrue('and the run says so',
    strpos($out, 'every variable a rendered template reads is set') !== false);

echo "  a template whose action only redirects\n";
put('src/Template/Widgets/gone.ctp', '<?= $neverSetAnywhere ?>');
list($out, $status) = report();
checkTrue('is named as unrendered', strpos($out, 'Widgets/gone.ctp') !== false);
checkTrue('with the redirect it makes instead',
    strpos($out, 'only redirects: ["action" => "index"]') !== false);
checkTrue('and what it reads is not reported as missing',
    strpos($out, '$neverSetAnywhere') === false);
check('the run still fails, because a file nobody renders is a finding', $status, 1);

echo "  a redirect behind a comment is still a redirect\n";
put('src/Template/Widgets/tidy.ctp', '<?= $alsoNeverSet ?>');
list($out, $status) = report();
checkTrue('named too', strpos($out, 'Widgets/tidy.ctp') !== false);
checkTrue('and it does not report the variable',
    strpos($out, '$alsoNeverSet') === false);

echo "  a template reached by render() rather than by its own name\n";
put('src/Template/Widgets/chart_pane.ctp', '<?= $chartRows ?>');
list($out, $status) = report();
checkTrue('is not called unrendered',
    strpos($out, 'Widgets/chart_pane.ctp   ') === false
    && strpos($out, 'chart_pane.ctp  ') === false);
checkTrue('so what it reads is checked, and named',
    strpos($out, '$chartRows') !== false);

echo "  a template with no action and no render() naming it\n";
put('src/Template/Widgets/add_thing.ctp', '<?= $thing ?>');
list($out, $status) = report();
checkTrue('is named as unrendered', strpos($out, 'Widgets/add_thing.ctp') !== false);
checkTrue('saying no action carries that name',
    strpos($out, 'no action of that name') !== false);

echo "  a tree with nothing wrong in it\n";
scrub($tree);
put('src/Controller/AppController.php', '<?php class AppController {}');
put('src/Controller/WidgetsController.php', '<?php
class WidgetsController extends AppController {
    public function index() { $widgets = []; $this->set(compact("widgets")); }
}');
put('src/Template/Widgets/index.ctp', '<?php foreach ($widgets as $w) { echo $w; } ?>');
list($out, $status) = report();
check('passes', $status, 0);
checkTrue('and says what it checked', strpos($out, '1 template(s) checked') !== false);

scrub($tree);
finish();
