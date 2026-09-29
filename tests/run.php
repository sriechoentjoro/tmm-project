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

// A pass on a PHP the application does not support is weaker than a pass on the
// server, and not in a way anybody would guess. Two harnesses passed on 8.4 and
// failed on 7.4: iterator_to_array() refuses an array on 7.4 and accepts one on
// 8, so a fixture that stood in for a query was fine here and a TypeError
// there. Saying the version out loud is the cheapest guard against reading a
// green run as more than it is.
if (PHP_VERSION_ID >= 80000) {
    printf("PHP %s. This application supports 5.6 to 7.4, so a pass here is not
"
        . "a pass on the server - run it there too before believing it.
",
        PHP_VERSION);
}

$failed = [];
$skipped = [];
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
    // 2 is a harness saying this machine cannot run it - a missing extension,
    // not a fault in the application. Counted apart from both.
    if ($code === 2) {
        $skipped[] = $name;
        continue;
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
        $skipped[] = $name;
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
    if ($code === 2) {
        $skipped[] = $name;
        continue;
    }
    if ($code !== 0) {
        $failed[] = $name;
    }
}

echo "\n", str_repeat('-', 72), "\n";
$total = count($harnesses) + count($browser);
printf("%d harness(es), %d ran, %d checks\n", $total, $total - count($skipped), $checks);

if ($skipped) {
    echo count($skipped), ' skipped, this machine cannot run them: ',
        implode(', ', $skipped), "\n";
}

if ($failed) {
    echo count($failed), " failed: ", implode(', ', $failed), "\n";
    exit(1);
}

// A skip is not a pass, so the wording does not claim one.
echo $skipped ? "nothing failed\n" : "all good\n";
exit(0);
