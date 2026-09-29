<?php
/**
 * The little that every harness in this directory needs.
 *
 * Deliberately not PHPUnit. These tests reach into controllers with
 * reflection, swap the ORM onto SQLite, and read the source of the
 * application as text; a test runner that wants a bootstrap and a config file
 * would be more machinery than the tests are. What they need is a way to say
 * "this should equal that", a count at the end, and an exit code.
 *
 * Each file runs on its own - php tests/region_lists.php - and tests/run.php
 * runs them all in separate processes, which they need: several of them
 * reconfigure ConnectionManager, and that is not something to share.
 *
 * Written for PHP 7.4, the version the server runs. Nothing here uses syntax
 * from 8.0 or later.
 */

/** Stated once so a test can read it without repeating the path. */
define('TMM_ROOT', dirname(dirname(__DIR__)));

require TMM_ROOT . '/vendor/autoload.php';
require TMM_ROOT . '/config/paths.php';

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

Configure::write('App', [
    'namespace' => 'App',
    'encoding' => 'UTF-8',
    'defaultLocale' => 'en_US',
    'paths' => [
        'plugins' => [TMM_ROOT . '/plugins/'],
        'templates' => [TMM_ROOT . '/src/Template/'],
        'locales' => [TMM_ROOT . '/src/Locale/'],
    ],
]);
Cache::setConfig('_cake_model_', ['className' => 'Array']);
Cache::setConfig('_cake_core_', ['className' => 'Array']);

$GLOBALS['tmm_failures'] = 0;
$GLOBALS['tmm_checks'] = 0;

/**
 * Compare two values exactly.
 *
 * @param string $label What is being claimed, in words a reader can check.
 * @param mixed $got What the code did.
 * @param mixed $want What it should have done.
 * @return void
 */
function check($label, $got, $want)
{
    $GLOBALS['tmm_checks']++;
    if ($got === $want) {
        printf("  %-62s ok\n", $label);

        return;
    }
    $GLOBALS['tmm_failures']++;
    printf("  %-62s FAIL\n      got  %s\n      want %s\n", $label,
        tmm_show($got), tmm_show($want));
}

/**
 * Assert something is true, where saying what it equals would read worse.
 *
 * @param string $label What is being claimed.
 * @param bool $ok The claim.
 * @return void
 */
function checkTrue($label, $ok)
{
    check($label, (bool)$ok, true);
}

/**
 * A value, short enough to read in a failure line.
 *
 * @param mixed $value The value.
 * @return string
 */
function tmm_show($value)
{
    $text = is_string($value) ? '"' . $value . '"' : var_export($value, true);
    $text = preg_replace('/\s+/', ' ', $text);

    return strlen($text) > 300 ? substr($text, 0, 297) . '...' : $text;
}

/**
 * Point the named connections at one SQLite file and forget every table.
 *
 * The connections this application uses are real and separate - fifteen of
 * them - and a test that needs two of them needs them to answer. One file
 * behind several names does that, and keeps the test's own setup to the
 * tables it actually cares about.
 *
 * @param array $names Connection names to redirect.
 * @param string $file Where to put the database.
 * @return \Cake\Database\Connection The first one named.
 */
function sqliteConnections(array $names, $file)
{
    @unlink($file);
    Cache::clear(false, '_cake_model_');
    foreach ($names as $name) {
        ConnectionManager::drop($name);
        ConnectionManager::setConfig($name, [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Sqlite',
            'database' => $file,
        ]);
    }
    TableRegistry::getTableLocator()->clear();

    return ConnectionManager::get($names[0]);
}

/**
 * Reach a protected method without building a controller.
 *
 * Constructing one pulls in components, a request and a database; the rules
 * under test here need none of those.
 *
 * @param string $class Fully qualified class name.
 * @param string $method Method name.
 * @return array [instance, ReflectionMethod]
 */
function reachInto($class, $method)
{
    $reflection = new ReflectionClass($class);
    $instance = $reflection->newInstanceWithoutConstructor();
    $handle = $reflection->getMethod($method);
    $handle->setAccessible(true);

    return [$instance, $handle];
}

/**
 * Give a controller instance a request, which is a protected property.
 *
 * @param object $controller The instance.
 * @param string $queryString What the browser sent, as a query string.
 * @param array $params Routing parameters.
 * @return void
 */
function giveRequest($controller, $queryString, array $params = [])
{
    parse_str($queryString, $query);
    $property = new ReflectionProperty('Cake\Controller\Controller', 'request');
    $property->setAccessible(true);
    $property->setValue($controller, new Cake\Http\ServerRequest([
        'url' => '/', 'webroot' => '/', 'query' => $query,
        'params' => $params + ['plugin' => null, 'controller' => 'Candidates',
            'action' => 'index', 'pass' => [], '_ext' => null],
    ]));
}

/**
 * Print the count and leave with an exit code the runner can read.
 *
 * @param string|null $tidy A file to remove on the way out.
 * @return void
 */
function finish($tidy = null)
{
    if ($tidy !== null) {
        @unlink($tidy);
    }
    printf("\n  %d checks, %s\n", $GLOBALS['tmm_checks'],
        $GLOBALS['tmm_failures'] ? $GLOBALS['tmm_failures'] . ' FAILED' : 'all good');
    exit($GLOBALS['tmm_failures'] ? 1 : 0);
}
