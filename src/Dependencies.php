<?php

$injector = new \Auryn\Injector;

$injector->alias('Http\Request', 'Http\HttpRequest');
$injector->share('Http\HttpRequest');
$injector->define('Http\HttpRequest', [
    ':get' => $_GET,
    ':post' => $_POST,
    ':cookies' => $_COOKIE,
    ':files' => $_FILES,
    ':server' => $_SERVER,
    ':inputStream' => file_get_contents('php://input')
]);

$injector->alias('Http\Response', 'BeeperApi\HttpResponse');
$injector->share('BeeperApi\HttpResponse');

$injector->alias('BeeperApi\Repositories\Users\UserRepository', 'BeeperApi\Repositories\Users\MicroUser');
$injector->share('BeeperApi\Repositories\Users\MicroUser');

$injector->alias('BeeperApi\Repositories\Beeps\BeepRepository', 'BeeperApi\Repositories\Beeps\MicroBeep');
$injector->share('BeeperApi\Repositories\Beeps\MicroBeep');


return $injector;