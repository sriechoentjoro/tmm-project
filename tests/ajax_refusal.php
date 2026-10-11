<?php
/**
 * What an AJAX request gets when it is not allowed through.
 *
 * The address cascade printed "-- Not allowed to look this up --" at a user
 * whose role was allowed. Three different things used to arrive at that
 * script looking alike:
 *
 *   - a role refused: a 302 to the referer, so jQuery followed it and got a
 *     whole HTML page with 200 on it, and only the JSON parse failed;
 *   - an action that threw: an HTML error page;
 *   - a session that had ended: AuthComponent's 403 with an empty body, which
 *     is the same 403 a refused role gets.
 *
 * So the script guessed, and guessing sent us to read permissions for an hour
 * over what was most likely an expired session. Each of the three now arrives
 * as itself.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\Routing\Router;

/**
 * Start a controller the way the dispatcher does and see what comes back.
 *
 * @param array $user The identity to put in the session, or [] for none.
 * @param bool $ajax Whether the page made the request itself.
 * @return \Cake\Http\Response|null What startupProcess returned.
 */
function startup(array $user = [], $ajax = true)
{
    $session = new Session();
    if ($user) {
        $session->write('Auth.User', $user);
    }
    $environment = ['REQUEST_METHOD' => 'GET'];
    if ($ajax) {
        $environment['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    }
    $request = new ServerRequest([
        'url' => '/master-kabupatens/get-by-province',
        'query' => ['master_propinsi_id' => '12'],
        'environment' => $environment,
        'session' => $session,
        'params' => ['controller' => 'MasterKabupatens',
            'action' => 'getByProvince', 'plugin' => null, 'pass' => []],
    ]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    $controller = new \App\Controller\MasterKabupatensController($request, new Response());

    return $controller->startupProcess();
}

echo "  a signed-out page asking for a list\n";
$answer = startup([], true);
checkTrue('is answered, not redirected', $answer instanceof Response);
check('with 401, which is not 403', $answer->getStatusCode(), 401);
checkTrue('as JSON', strpos($answer->getHeaderLine('Content-Type'), 'json') !== false);
$body = json_decode((string)$answer->getBody(), true);
checkTrue('carrying a sentence the page can show',
    isset($body['message']) && $body['message'] !== '');
// Compared against the catalogue rather than against English: these pages
// run in Indonesian by default, so asserting the English wording would be
// asserting that the translation is missing.
$catalogue = file_get_contents(TMM_ROOT . '/src/Locale/id/default.po');
preg_match('/msgid "Your session has ended\. Please sign in again\."\nmsgstr "([^"]*)"/',
    $catalogue, $said);
check('in the words the catalogue gives for it, not the English',
    $body['message'], isset($said[1]) ? $said[1] : '(not in the catalogue)');

echo "  the same request from somebody signed in\n";
$answer = startup(['id' => 9, 'username' => 'lpk_user',
    'role_names' => ['lpk-penyangga']], true);
check('is not refused here', $answer, null);

echo "  an ordinary page load with no session\n";
// Not every unauthenticated request is an AJAX call. A person opening a page
// should still be taken to the login screen, not handed a line of JSON.
$answer = startup([], false);
checkTrue('still goes to the login screen',
    $answer === null || strpos($answer->getHeaderLine('Location'), 'login') !== false);

echo "  the lookup itself, for the role that was accused\n";
$source = file_get_contents(TMM_ROOT . '/src/Controller/MasterKabupatensController.php');
checkTrue('is open to anybody signed in',
    strpos($source, "return (bool)\$this->Auth->user('id');") !== false);

echo "  what the script says now\n";
$js = file_get_contents(TMM_ROOT . '/webroot/js/address-cascade.js');
checkTrue('401 reads as a session that ended',
    strpos($js, "xhr.status === 401") !== false
    && strpos($js, 'Signed out') !== false);
checkTrue('403 is the only thing that reads as a refusal',
    strpos($js, "xhr.status === 403") !== false);
checkTrue('a server error reads as one', strpos($js, 'xhr.status >= 500') !== false);
checkTrue('and whatever the server said is repeated rather than guessed at',
    strpos($js, 'xhr.responseJSON && xhr.responseJSON.message') !== false);

$app = file_get_contents(TMM_ROOT . '/src/Controller/AppController.php');
checkTrue('a refused AJAX request is no longer redirected',
    strpos($app, "setConfig('unauthorizedRedirect', false)") !== false);

finish();
