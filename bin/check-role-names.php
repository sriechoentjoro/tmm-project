#!/usr/bin/env php
<?php
/**
 * Find role names the code tests for that the roles table does not have.
 *
 * Nothing in this application grants access to a person. Access is worked out
 * from the roles they hold, and several decisions in the pipeline are gated by
 * the role's NAME as a string in the code: who may put a candidate forward,
 * who may promote, who may record a departure or the end of a programme.
 *
 * That makes a role name part of the code's vocabulary, and renaming one on
 * the roles screen silently breaks every check written against the old name.
 * The checks fail closed, so nothing errors: the button simply stops working
 * for that role, and the person reports it days later as "it used to work".
 *
 * This reads every role name the code tests and reports the ones the roles
 * table has no row for. It is deliberately a reporting tool, not a rule: a
 * name may legitimately be planned before the row exists.
 *
 * Usage:
 *     php bin/check-role-names.php          report problems, exit 1 if any
 *     php bin/check-role-names.php --all    also list every name resolved
 *
 * Without a database connection it still lists what the code tests, so it is
 * useful on a checkout that cannot reach MySQL - it just cannot compare.
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', $argv, true);

/**
 * The three shapes a role test takes in this codebase.
 *
 *   hasRole('tmm-training')
 *   in_array('administrator', $roles)
 *   array_intersect(['administrator', 'tmm-training'], $viewerRoles)
 *
 * Plus the class constants that list them, such as
 * const PROPOSER_ROLES = ['administrator', 'lpk-penyangga'].
 */
const PATTERNS = [
    "/hasRole\(\s*'([a-z0-9][a-z0-9-]*)'/",
    "/in_array\(\s*'([a-z0-9][a-z0-9-]*)'\s*,\s*\\\$[A-Za-z_]*[Rr]oles/",
];

/**
 * Lists of names, matched as a whole then split.
 */
const LIST_PATTERNS = [
    "/array_intersect\(\s*\[([^\]]*)\]/",
    "/const\s+[A-Z_]*ROLES\s*=\s*\[([^\]]*)\]/",
];

$found = [];
$exts = ['php', 'ctp'];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    if (!$file->isFile() || !in_array(strtolower($file->getExtension()), $exts, true)) {
        continue;
    }
    $rel = ltrim(str_replace($root, '', $file->getPathname()), '/');
    foreach (file($file->getPathname()) as $n => $line) {
        foreach (PATTERNS as $pattern) {
            if (preg_match_all($pattern, $line, $m)) {
                foreach ($m[1] as $name) {
                    $found[$name][] = $rel . ':' . ($n + 1);
                }
            }
        }
        foreach (LIST_PATTERNS as $pattern) {
            if (!preg_match_all($pattern, $line, $m)) {
                continue;
            }
            foreach ($m[1] as $list) {
                if (preg_match_all("/'([a-z0-9][a-z0-9-]*)'/", $list, $inner)) {
                    foreach ($inner[1] as $name) {
                        $found[$name][] = $rel . ':' . ($n + 1);
                    }
                }
            }
        }
    }
}

// Words that match the shape but are not role names.
$notRoles = ['post', 'get', 'put', 'patch', 'delete', 'index', 'view', 'add', 'edit'];
foreach ($notRoles as $word) {
    unset($found[$word]);
}

ksort($found);
printf("%d role name(s) tested by the code\n\n", count($found));

foreach ($found as $name => $places) {
    printf("  %-22s %d place(s)\n", $name, count($places));
    if ($showAll) {
        foreach (array_unique($places) as $place) {
            printf("      %s\n", $place);
        }
    }
}

// ---- compare against the roles table, where one can be reached ----------
$configFile = $root . '/config/app.php';
if (!is_file($configFile)) {
    echo "\nNo config/app.php - cannot compare against the roles table.\n";
    exit(0);
}

// config/app_datasources.php stops the whole script with E_USER_ERROR when no
// credentials are configured - deliberately, so a missing password fails at
// boot rather than as a confusing query error. E_USER_ERROR cannot be caught,
// so the credentials are checked here instead of loading the config and
// hoping. Without them this tool still does its first job: listing what the
// code expects.
$hasLocal = is_file($root . '/config/app_local.php');
$hasEnv = getenv('TMM_DB_USERNAME') !== false && getenv('TMM_DB_PASSWORD') !== false;
if (!$hasLocal && !$hasEnv) {
    echo "\nNo database credentials configured, so the roles table cannot be read.\n";
    echo "The names above are what the code expects; compare them by hand.\n";
    exit(0);
}

require $root . '/vendor/autoload.php';
// config/app.php reads CORE_PATH and CONFIG, which only the application's own
// path definitions provide. Requiring them is more honest than guessing at
// each constant as app.php turns out to need it.
require $root . '/config/paths.php';
try {
    \Cake\Core\Configure::config('default', new \Cake\Core\Configure\Engine\PhpConfig());
    \Cake\Core\Configure::load('app', 'default', false);
    foreach ((array)\Cake\Core\Configure::consume('Datasources') as $name => $config) {
        \Cake\Datasource\ConnectionManager::setConfig($name, $config);
    }
    $rows = \Cake\Datasource\ConnectionManager::get('cms_authentication_authorization')
        ->execute('SELECT name FROM roles')
        ->fetchAll('assoc');
} catch (\Throwable $e) {
    echo "\nCould not read the roles table (" . $e->getMessage() . ").\n";
    echo "The names above are what the code expects; compare them by hand.\n";
    exit(0);
}

/**
 * Names the code tests for that are deliberately not roles.
 *
 * A name with no row behind it normally means a typo, and the check exists to
 * find those. But a check written as `management OR director` is not a typo -
 * it is one branch naming two words for the same job, so that an installation
 * that calls the role either thing lands on the same screen. The branch that
 * finds nothing simply never fires, and nobody loses anything.
 *
 * Every entry has to say which real role covers it. Anything not listed here
 * is still reported, which is the point: one explained exception keeps the
 * check green, so the next genuine typo stands out instead of being lost in a
 * warning everybody has learned to scroll past.
 */
const SYNONYMS = [
    'director' => 'an alternative spelling of management in DashboardController; the management role exists and covers it',
];

$actual = [];
foreach ($rows as $row) {
    $actual[strtolower(trim($row['name']))] = true;
}
printf("\n%d role(s) in the roles table\n", count($actual));

$missing = [];
$synonyms = [];
foreach ($found as $name => $places) {
    if (isset($actual[$name])) {
        continue;
    }
    if (isset(SYNONYMS[$name])) {
        $synonyms[$name] = $places;
        continue;
    }
    $missing[$name] = $places;
}

if ($synonyms) {
    printf("\n%d name(s) tested as an alternative to a role that does exist:\n\n", count($synonyms));
    foreach ($synonyms as $name => $places) {
        printf("  %-22s %s\n", $name, SYNONYMS[$name]);
    }
}

if (!$missing) {
    echo "\nevery role name the code tests exists in the roles table\n";
    exit(0);
}

printf("\n%d role name(s) the code tests and the roles table does not have:\n\n", count($missing));
foreach ($missing as $name => $places) {
    printf("  %s\n", $name);
    foreach (array_slice(array_unique($places), 0, 6) as $place) {
        printf("      %s\n", $place);
    }
    if (count(array_unique($places)) > 6) {
        printf("      ... and %d more\n", count(array_unique($places)) - 6);
    }
    echo "\n";
}
echo "A check written against a name with no row fails closed: the button\n";
echo "stops working for that role and nothing reports an error.\n";
exit(1);
