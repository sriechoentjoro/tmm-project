<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 * Cache: Routes are cached to improve performance, check the RoutingMiddleware
 * constructor in your `src/Application.php` file to change this behavior.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    // Register scoped middleware for in scopes.
    $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httpOnly' => true,
    ]));

    /*
     * Apply a middleware to the current route scope.
     * Requires middleware to be registered through `Application::routes()` with `registerMiddleware()`
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *
     * ```
     * $routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);
     * $routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);
     * ```
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    
    // Home Page
    $routes->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);

    // AJAX filter routes for related tables on view pages
    // Example: /personnels/filterRelated?model=Companies&foreign_key=personnel_id&foreign_value=5
    $routes->connect(
        '/:controller/filterRelated',
        ['action' => 'filterRelated']
    );
    
    // Candidate management routes
    $routes->connect(
        '/candidates/add-interview/:id',
        ['controller' => 'Candidates', 'action' => 'addInterview'],
        ['pass' => ['id'], 'id' => '[0-9]+']
    );
    $routes->connect(
        '/candidates/add-mcu/:id',
        ['controller' => 'Candidates', 'action' => 'addMcu'],
        ['pass' => ['id'], 'id' => '[0-9]+']
    );
    $routes->connect(
        '/candidates/upload-document/:id',
        ['controller' => 'Candidates', 'action' => 'uploadDocument'],
        ['pass' => ['id'], 'id' => '[0-9]+']
    );
    $routes->connect(
        '/candidates/dashboard',
        ['controller' => 'Candidates', 'action' => 'dashboard']
    );
    $routes->connect(
        '/candidates/change-status/:id/:statusId',
        ['controller' => 'Candidates', 'action' => 'changeStatus'],
        ['pass' => ['id', 'statusId'], 'id' => '[0-9]+', 'statusId' => '[0-9]+']
    );
    
    $routes->fallbacks(DashedRoute::class);
});

// AJAX routes without CSRF protection
Router::scope('/', function (RouteBuilder $routes) {
    // Address cascading AJAX endpoints
    $routes->connect(
        '/master-kabupatens/get-by-province',
        ['controller' => 'MasterKabupatens', 'action' => 'getByProvince']
    );
    $routes->connect(
        '/master-kecamatans/get-by-kabupaten',
        ['controller' => 'MasterKecamatans', 'action' => 'getByKabupaten']
    );
    $routes->connect(
        '/master-kelurahans/get-by-kecamatan',
        ['controller' => 'MasterKelurahans', 'action' => 'getByKecamatan']
    );
});

/*
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * Router::scope('/api', function (RouteBuilder $routes) {
 *     // No $routes->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */

