<?php
$versions = [
    'v1' => '/api/v1',
    'v2' => '/api/v2'
];
$router->removeExtraSlashes(true);


// Operators group
$operators = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'operators'
));

$operators->setPrefix($versions['v1'].'/operators');

$operators->addGet('', array(
    'action' => 'read'
));

$operators->addPost('', array(
    'action' => 'create'
));

$operators->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$operators->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Users group
$users = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'users'
));

$users->setPrefix($versions['v1'].'/users');

$users->addGet('', array(
    'action' => 'read'
));

$users->addPost('', array(
    'action' => 'create'
));

$users->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$users->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Countries group
$countries = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'countries'
));

$countries->setPrefix($versions['v1'].'/countries');

$countries->addGet('', array(
    'action' => 'read'
));

$countries->addPost('', array(
    'action' => 'create'
));

$countries->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$countries->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Robot code types group
$robotcodetypes = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'robotcodetypes'
));

$robotcodetypes->setPrefix($versions['v1'].'/robotcodetypes');

$robotcodetypes->addGet('', array(
    'action' => 'read'
));

$robotcodetypes->addPost('', array(
    'action' => 'create'
));

$robotcodetypes->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$robotcodetypes->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Robot codes group
$robotcodes = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'robotcodes'
));

$robotcodes->setPrefix($versions['v1'].'/robotcodes');

$robotcodes->addGet('', array(
    'action' => 'read'
));

$robotcodes->addPost('', array(
    'action' => 'create'
));

$robotcodes->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$robotcodes->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Accounts group
$accounts = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'accounts'
));

$accounts->setPrefix($versions['v1'].'/accounts');

$accounts->addGet('', array(
    'action' => 'read'
));

$accounts->addPost('', array(
    'action' => 'create'
));

$accounts->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$accounts->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Promos group
$promos = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'promos'
));

$promos->setPrefix($versions['v1'].'/promos');

$promos->addGet('', array(
    'action' => 'read'
));

$promos->addPost('', array(
    'action' => 'create'
));

$promos->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$promos->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Instruments group
$instruments = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'instruments'
));

$instruments->setPrefix($versions['v1'].'/instruments');

$instruments->addGet('', array(
    'action' => 'read'
));

$instruments->addPost('', array(
    'action' => 'create'
));

$instruments->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$instruments->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Bets group
$bets = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'bets'
));

$bets->setPrefix($versions['v1'].'/bets');

$bets->addGet('', array(
    'action' => 'read'
));

$bets->addPost('', array(
    'action' => 'create'
));

$bets->addPut('', array(
    'action' => 'update'
));

// Invests group
$invests = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'invests'
));

$invests->setPrefix($versions['v1'].'/invests');

$invests->addGet('', array(
    'action' => 'read'
));

$invests->addPost('', array(
    'action' => 'create'
));

$invests->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$invests->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Deposits group
$deposits = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'deposits'
));

$deposits->setPrefix($versions['v1'].'/deposits');

$deposits->addGet('', array(
    'action' => 'read'
));

$deposits->addPost('', array(
    'action' => 'create'
));

$deposits->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$deposits->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

// Withdrawals group
$withdrawals = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'withdrawals'
));

$withdrawals->setPrefix($versions['v1'].'/withdrawals');

$withdrawals->addGet('', array(
    'action' => 'read'
));

$withdrawals->addPost('', array(
    'action' => 'create'
));

$withdrawals->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));

$withdrawals->addDelete('/{id:[0-9]+}', array(
    'action' => 'delete'
));

$router->mount($operators);
$router->mount($users);
$router->mount($countries);
$router->mount($robotcodetypes);
$router->mount($robotcodes);
$router->mount($accounts);
$router->mount($promos);
$router->mount($instruments);
$router->mount($bets);
$router->mount($invests);
$router->mount($deposits);
$router->mount($withdrawals);
?>