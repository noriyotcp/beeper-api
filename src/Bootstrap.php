<?php

namespace BeeperApi;

use BeeperApi\Exceptions\ApiException;
use HTTPQuest\HTTPQuest;

require __DIR__ . '/../vendor/autoload.php';

/**
 * REQUEST PARSING
 */
$httpquest = new HTTPQuest();
$httpquest->decode($_POST, $_FILES);

/**
 * DEPENDENCY INJECTOR
 */
$injector = include('Dependencies.php');

$request = $injector->make('Http\Request');
$response = $injector->make('Http\Response');

/**
 * ROUTE DISPATCHER
 */
$routeDefinitionCallback = function (\FastRoute\RouteCollector $r) {
    $routes = include('Routes.php');
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
};

$dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPath());

switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent([
            'message' => 'Resource not found',
            'code'    => 404
        ]);
        $response->setStatusCode(404);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent([
            'message' => 'Method not allowed',
            'code'    => 405
        ]);
        $response->setStatusCode(405);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $className = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];
        $parsedVars = [];
        //add ":" so that auryn DI will know that we are passing param, not a class name
        foreach ($vars as $key => $val)
            $parsedVars[":" . $key] = $val;

        $class = $injector->make($className);
        try {
            $injector->execute([$class, $method], $parsedVars);
        } catch (ApiException $e) {
            $response->setContent([
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'errors'  => $e->getErrors()
            ]);
            $response->setStatusCode($e->getCode());
        }
        break;
}

/**
 * RESPONSE
 */
$response->setHeader('Content-Type', 'application/json');
foreach ($response->getHeaders() as $header) {
    header($header);
}
echo $response->getContent();