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
 * Leave without running, because this machine cannot run it.
 *
 * Not a pass and not a failure. A harness that skipped in silence would make
 * the total say more was checked than was, and one that threw would bury the
 * reason in a stack trace - which is what happened on the server, where PHP has
 * no pdo_sqlite: six harnesses dumped forty lines of trace each and the run
 * looked broken rather than partly unavailable.
 *
 * Exit code 2, which tests/run.php counts as skipped.
 *
 * @param string $why What is missing, in words somebody can act on.
 * @return void
 */
function skip($why)
{
    printf("  skipped: %s\n", $why);
    exit(2);
}

/**
 * Whether this PHP can talk to SQLite at all.
 *
 * The harnesses stand the ORM up on a temporary file rather than touch a real
 * database, so a PHP without the driver cannot run them. On Debian and Ubuntu
 * it is one package.
 *
 * @return string|null Null when it works, or what to do about it.
 */
function sqliteUnavailable()
{
    if (!extension_loaded('pdo_sqlite')
        || !in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
        return 'needs the pdo_sqlite extension, which this PHP does not have. '
            . 'On Debian or Ubuntu: apt install php' . PHP_MAJOR_VERSION . '.'
            . PHP_MINOR_VERSION . '-sqlite3';
    }

    return null;
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
    // Checked here rather than in each harness: every one that needs a database
    // comes through this function, and none of them should have to remember.
    $missing = sqliteUnavailable();
    if ($missing !== null) {
        skip($missing);
    }

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
 * Render a template or an element the way a request would, and collect every
 * warning the application's own code emits while doing it.
 *
 * The point is the error handler. A template that reads an array key that is
 * not there, or calls a method on null, renders anyway: PHP emits a notice,
 * the page comes out with a gap in it, and on a production server with debug
 * off nobody sees either. So a render that produces a warning from a file
 * under src/ or config/ is a failure here, and the warning is quoted.
 *
 * Warnings from vendor/ are ignored. This application runs CakePHP 3.9 on
 * whatever PHP the machine has, and a newer PHP deprecates things inside the
 * framework that are none of the template's business.
 *
 * @param string $what Template name, or element name when $isElement.
 * @param array $vars View variables.
 * @param array $options controller, templatePath, isElement, url, params.
 * @return array [html, problems] - problems is a list of strings.
 */
function renderView($what, array $vars, array $options = [])
{
    $options += [
        'controller' => 'Pages',
        'templatePath' => null,
        'isElement' => false,
        'url' => '/',
        'params' => [],
    ];

    Cake\Core\Configure::write('App.base', false);
    Cake\Core\Configure::write('App.dir', 'src');
    Cake\Core\Configure::write('App.webroot', 'webroot');
    Cake\Core\Configure::write('App.wwwRoot', TMM_ROOT . '/webroot');
    Cake\Core\Configure::write('App.fullBaseUrl', 'http://localhost');
    Cake\Core\Configure::write('App.imageBaseUrl', 'img/');
    Cake\Core\Configure::write('App.cssBaseUrl', 'css/');
    Cake\Core\Configure::write('App.jsBaseUrl', 'js/');
    Cake\Core\Configure::write('debug', true);

    $request = new Cake\Http\ServerRequest([
        'url' => $options['url'],
        'webroot' => '/',
        'query' => isset($options['query']) ? $options['query'] : [],
        'params' => $options['params'] + ['plugin' => null,
            'controller' => $options['controller'], 'action' => 'index',
            'pass' => [], '_ext' => null],
    ]);

    Cake\Routing\Router::reload();
    Cake\Routing\Router::setRequestInfo($request);
    Cake\Routing\Router::prefix('admin', function ($routes) {
        $routes->connect('/:controller/:action/*', [],
            ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    });
    Cake\Routing\Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);

    $viewOptions = ['name' => $options['controller']];
    if ($options['templatePath'] !== null) {
        $viewOptions['templatePath'] = $options['templatePath'];
    }
    $view = new Cake\View\View($request, null, null, $viewOptions);
    $view->set($vars);

    $problems = [];
    set_error_handler(function ($no, $message, $file, $line) use (&$problems) {
        if (strpos($file, '/vendor/') === false) {
            $problems[] = $message . '  (' . str_replace(TMM_ROOT . '/', '', $file)
                . ':' . $line . ')';
        }

        return true;
    });
    try {
        $html = $options['isElement']
            ? $view->element($what, $vars)
            : $view->render($what, false);
    } catch (\Throwable $e) {
        $problems[] = get_class($e) . ': ' . $e->getMessage();
        $html = '';
    }
    restore_error_handler();

    return [$html, array_values(array_unique($problems))];
}

/**
 * Render something and fail the check when the application warned.
 *
 * @param string $label What is being rendered, for the check line.
 * @param string $what Template or element name.
 * @param array $vars View variables.
 * @param array $options As renderView().
 * @return string The html, or '' when it did not render.
 */
function renderClean($label, $what, array $vars, array $options = [])
{
    list($html, $problems) = renderView($what, $vars, $options);
    if ($problems) {
        $GLOBALS['tmm_checks']++;
        $GLOBALS['tmm_failures']++;
        printf("  %-62s FAIL\n", $label);
        foreach ($problems as $problem) {
            echo '      ', $problem, "\n";
        }

        return '';
    }
    check($label . sprintf(' (%d bytes)', strlen($html)), $problems, []);

    return $html;
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
