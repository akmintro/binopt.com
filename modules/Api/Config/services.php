<?php

use App\Api\Managers\JWTTokenParser;
use App\Api\Managers\SessionManager;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Cache\Frontend\None as FrontendNone;


$di['dispatcher'] = function () use ($di) {
    $eventsManager = $di->getShared('eventsManager');
/*
    $apiListener = new \App\Core\Listeners\ApiListener();
    $eventsManager->attach('dispatch', $apiListener);*/

    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    $dispatcher->setDefaultNamespace("App\Api\Controllers");

    return $dispatcher;
};

$di['url']->setBaseUri(''.$config->application->baseUri.'');

$di['view'] = function () {
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir(__DIR__ . '/../Views/Default/');
    $view->registerEngines(array(
        ".volt" => 'voltService'
    ));

    return $view;
};

$di['tokenParser'] = function () {
    return new JWTTokenParser('borch_z_pampuchkami');
};

$di['rest_session'] = function () {
    $redis = new Redis(
        new FrontendNone(["lifetime" => 36000])
    );
//    $redis = new Redis();
    return new SessionManager(86400, $redis);
};

?>