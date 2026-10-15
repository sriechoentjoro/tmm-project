<?php
/**
 * What a form shows when it refuses what you typed.
 *
 * CakePHP renders a refused field as
 *
 *     <div class="input text error">
 *       <input class="form-control form-error" ...>
 *       <div class="error-message">Why it was refused</div>
 *     </div>
 *
 * and nothing the layout loaded had a rule for any of those three classes.
 * base.css and style.css do; the layout links neither - one is commented out,
 * the other is not linked at all. pretty-form.css styles
 * .cand-form .error-message, and three templates use .cand-form.
 *
 * So on nearly every form the sentence saying which field was wrong and why
 * rendered as plain black body text, the same size and weight as the hint
 * above it, with nothing marking the field. The red flash at the top of the
 * page said "could not be saved. Please, try again", and the part that
 * answered "what do I change?" was a thousand pixels down looking like prose.
 *
 * Also checked here: that no form invites more characters than its column can
 * hold. Candidates/add asked for a name of up to 256 into a varchar(100) and
 * a code of up to 256 into a varchar(10), and said so on the label.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_form_errors.sqlite';
$conn = sqliteConnections(['cms_lpk_candidates', 'default'], $db);
$conn->execute('CREATE TABLE candidates (id INTEGER PRIMARY KEY,
    candidate_code VARCHAR(10), name VARCHAR(100), identity_number VARCHAR(50),
    telephone_emergency VARCHAR(256), email VARCHAR(100), address VARCHAR(256),
    post_code VARCHAR(6), is_holding_passport BOOLEAN, is_training_pass BOOLEAN,
    is_apprenticeship_pass BOOLEAN, created DATETIME, modified DATETIME)');

$candidates = TableRegistry::getTableLocator()->get('Candidates');
$candidate = $candidates->newEntity();
$candidate->name = str_repeat('a', 200);
$candidate->candidate_code = 'XXXXXXXXXXXXX';
$candidate->setError('name', ['maxLength' => 'The name is at most 100 characters']);
$candidate->setError('candidate_code', ['maxLength' => 'The code is at most 10 characters']);

$vars = ['candidate' => $candidate] + array_fill_keys([
    'apprenticeOrders', 'vocationalTrainingInstitutions', 'acceptanceOrganizations',
    'masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterPropinsis',
    'masterKabupatens', 'masterKecamatans', 'masterKelurahans', 'masterBloodTypes',
    'masterCandidateInterviewResults', 'masterRejectedReasons',
], []);

echo "  a refused field on the form\n";
$html = renderClean('the add form renders with errors on it', 'add', $vars, [
    'controller' => 'Candidates', 'templatePath' => 'Candidates',
    'url' => '/candidates/add',
    'params' => ['controller' => 'Candidates', 'action' => 'add'],
]);
checkTrue('says which field and why', strpos($html, 'The code is at most 10 characters') !== false);
checkTrue('for every refused field, not just the first',
    strpos($html, 'The name is at most 100 characters') !== false);
checkTrue('in markup something can style', strpos($html, 'class="error-message"') !== false);
checkTrue('and marks the field itself', strpos($html, 'form-error') !== false);

echo "  the stylesheet that makes it look like a refusal\n";
$css = file_get_contents(TMM_ROOT . '/webroot/css/form-errors.css');
checkTrue('the message has a rule of its own',
    preg_match('/^\.error-message\s*\{/m', $css) === 1);
checkTrue('and a colour', strpos($css, '#dc3545') !== false);
checkTrue('the refused field is bordered', strpos($css, '.form-control.form-error') !== false);

$layout = file_get_contents(TMM_ROOT . '/src/Template/Layout/elegant.ctp');
checkTrue('and the layout loads it on every page',
    strpos($layout, '/css/form-errors.css') !== false);

echo "  how assets are versioned\n";
// '?v=' . time() changed on every request, so nothing could ever be served
// from cache and everything was re-downloaded on every page load - while the
// files linked with no version at all were the only cacheable ones, and went
// on being served from the visitor's cache after they were fixed.
// The assignment, not the mention: the comment above it quotes the old line
// to say what went wrong, and matching that would be matching the
// explanation rather than the code.
check('no asset carries a version that changes every request',
    strpos($layout, "\$cacheBust = '?v=' . time()"), false);
checkTrue('each carries its own file\'s modification time',
    strpos($layout, 'filemtime($file)') !== false);
$links = preg_match_all('/<link rel="stylesheet" href="<\?= \$staticAssetsUrl \?>/', $layout);
$versioned = preg_match_all('/\$assetVersion\(/', $layout);
checkTrue('and every stylesheet and script the layout links is versioned',
    $versioned >= $links && $links > 0);

echo "  what the forms say they will accept\n";
// Read from the dumps of the real databases, not from a list written here.
$widths = [];
foreach (glob(TMM_ROOT . '/database_exports/*/*.sql') as $dump) {
    $sql = file_get_contents($dump);
    if (preg_match_all('/CREATE TABLE\s+`(\w+)`\s*\((.*?)\n\)\s*ENGINE/s', $sql, $tables, PREG_SET_ORDER)) {
        foreach ($tables as $one) {
            foreach (explode("\n", $one[2]) as $line) {
                if (preg_match('/^\s*`(\w+)`\s+(varchar|char)\((\d+)\)/i', $line, $column)) {
                    $widths[strtolower($one[1])][strtolower($column[1])] = (int)$column[3];
                }
            }
        }
    }
}
checkTrue('the dumps still describe the candidates table',
    isset($widths['candidates']['name']));
check('and name is the width the form now offers', $widths['candidates']['name'], 100);

$over = [];
foreach (glob(TMM_ROOT . '/src/Template/*/*.ctp') as $path) {
    $folder = basename(dirname($path));
    $table = strtolower(preg_replace('/(?<!^)(?=[A-Z])/', '_', $folder));
    if (!isset($widths[$table])) {
        continue;
    }
    $markup = file_get_contents($path);
    if (!preg_match_all("/Form->control\(\s*'(\w+)'\s*,\s*\[(.*?)\n\s*\]\)/s", $markup, $found, PREG_SET_ORDER)) {
        continue;
    }
    foreach ($found as $one) {
        if (!preg_match("/'maxlength'\s*=>\s*(\d+)/", $one[2], $m)) {
            continue;
        }
        $column = strtolower($one[1]);
        if (isset($widths[$table][$column]) && (int)$m[1] > $widths[$table][$column]) {
            $over[] = basename(dirname($path)) . '/' . basename($path) . ' ' . $column;
        }
    }
}
check('no form offers more characters than its column holds',
    implode(', ', $over), '');

finish($db);
