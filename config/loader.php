<?php

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
    'App\Core'          => __DIR__.'/../modules/Core/',
    'App\Api'           => __DIR__.'/../modules/Api/',
));

$loader->register();
?>
