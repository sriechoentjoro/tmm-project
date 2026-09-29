#!/usr/bin/env php
<?php
/**
 * Run every harness in this directory, each in its own process.
 *
 * Separate processes are not tidiness: several harnesses point the ORM's
 * connections at their own SQLite file, and one that inherited another's
 * would pass or fail for the wrong reason.
 *
 * Usage:
 *     php tests/run.php              run everything, exit 1 if anything failed
 *     php tests/run.php region       run the harnesses whose name matches
 *     php tests/run.php --list       name them without running them
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$args = array_slice($argv, 1);
$list = in_array('--list', $args, true);
$filters = array_values(array_filter($args, function ($a) {
    return strpos($a, '--') !== 0;
}));

$files = glob($root . '/tests/*.php');
sort($files);

$harnesses = [];
foreach ($files as $file) {
    $name = basename($file, '.php');
    // run.php is this file; bootstrap.php is CakePHP's own PHPUnit bootstrap,
    // which came with the skeleton and is not a harness.
    if ($name === 'run' || $name === 'bootstrap') {
        continue;
    }
    if ($filters) {
        $wanted = false;
        foreach ($filters as $filter) {
            if (stripos($name, $filter) !== false) {
                $wanted = true;
                break;
            }
        }
        if (!$wanted) {
            continue;
        }
    }
    $harnesses[$name] = $file;
}

// The browser harnesses are JavaScript, and need node and playwright. They are
// listed and run where those exist, and named as skipped where they do not,
// rather than quietly left out of the count.
$browser = glob($root . '/tests/browser/*.js');
sort($browser);

if ($list) {
    foreach ($harnesses as $name => $file) {
        echo '  ', $name, "\n";
    }
    foreach ($browser as $file) {
        echo '  browser/', basename($file, '.js'), "\n";
    }
    exit(0);
}

if (!is_dir($root . '/vendor')) {
    exit("vendor/ is not there. Run composer install first.\n");
}

$failed = [];
$checks = 0;

foreach ($harnesses as $name => $file) {
    echo "\n", $name, "\n";
    $output = [];
    $code = 0;
    exec('php ' . escapeshellarg($file) . ' 2>&1', $output, $code);
    foreach ($output as $line) {
        echo $line, "\n";
        if (preg_match('/^\s*(\d+) checks,/', $line, $m)) {
            $checks += (int)$m[1];
        }
    }
    if ($code !== 0) {
        $failed[] = $name;
    }
}

// ------------------------------------------------------------- the browser
$node = trim((string)shell_exec('command -v node 2>/dev/null'));
$modules = trim((string)shell_exec('npm root -g 2>/dev/null'));
$havePlaywright = $modules !== '' && is_dir($modules . '/playwright');

foreach ($browser as $file) {
    $name = 'browser/' . basename($file, '.js');
    echo "\n", $name, "\n";
    if ($node === '' || !$havePlaywright) {
        echo "  skipped: needs node and a global playwright install\n";
        echo "           npm install -g playwright\n";
        continue;
    }
    $output = [];
    $code = 0;
    exec('NODE_PATH=' . escapeshellarg($modules) . ' ' . escapeshellarg($node)
        . ' ' . escapeshellarg($file) . ' 2>&1', $output, $code);
    foreach ($output as $line) {
        echo $line, "\n";
        if (preg_match('/^\s*(\d+) checks,/', $line, $m)) {
            $checks += (int)$m[1];
        }
    }
    if ($code !== 0) {
        $failed[] = $name;
    }
}

echo "\n", str_repeat('-', 72), "\n";
printf("%d harness(es), %d checks\n", count($harnesses) + count($browser), $checks);

if ($failed) {
    echo count($failed), " failed: ", implode(', ', $failed), "\n";
    exit(1);
}

echo "all good\n";
exit(0);
