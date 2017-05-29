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

$users->addGet('/{id:[0-9]+}', array(
    'action' => 'get'
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

$users->addPost('/password', array(
    'action' => 'changePassword'
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
    'action' => 'readUser'
));

$bets->addGet('/real', array(
    'action' => 'read',
    'realdemo' => 1
));

$bets->addGet('/demo', array(
    'action' => 'read',
    'realdemo' => 0
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
/*
$deposits->addPost('/admin', array(
    'action' => 'create',
    'admin' => 1
));

$deposits->addPost('/user', array(
    'action' => 'create',
    'admin' => 0
));

$deposits->addPut('/{id:[0-9]+}', array(
    'action' => 'update'
));
*/
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


// Summary group
$summary = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'summary'
));

$summary->setPrefix($versions['v1'].'/summary');

$summary->addGet('/deposits', array(
    'action' => 'readDeposits'
));

// Currency group
$currency = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'currency'
));

$currency->setPrefix($versions['v1'].'/currency');

$currency->addGet('/history', array(
    'action' => 'readHistory'
));

$currency->addGet('/last', array(
    'action' => 'readLast'
));

$currency->addPost('', array(
    'action' => 'create'
));

$currency->addDelete('', array(
    'action' => 'delete'
));

// Login group
$login = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'login'
));

$login->setPrefix($versions['v1']);

$login->addGet('/users/isauth', array(
    'action' => 'isauthUser'
));

$login->addPost('/users/auth', array(
    'action' => 'authUser'
));

$login->addPost('/users/unauth', array(
    'action' => 'unauthUser'
));

$login->addGet('/operators/isauth', array(
    'action' => 'isauthOper'
));

$login->addPost('/operators/auth', array(
    'action' => 'authOper'
));

$login->addPost('/operators/unauth', array(
    'action' => 'unauthOper'
));

$login->addDelete('/login', array(
    'action' => 'delete'
));

// Registration group
$registration = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'registration'
));

$registration->setPrefix($versions['v1']);

$registration->addPost('/users/register', array(
    'action' => 'registerUser'
));
$registration->addGet('/users/activate', array(
    'action' => 'activateUser'
));

// Settings group
$settings = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'settings'
));

$settings->setPrefix($versions['v1'].'/settings');

$settings->addGet('', array(
    'action' => 'read'
));

$settings->addPut('', array(
    'action' => 'update'
));

// Sessions group
/*
$session = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'api',
    'controller' => 'session'
));

$session->setPrefix($versions['v1'].'/session');

$session->addGet('', array(
    'action' => 'read'
));

$session->addPost('', array(
    'action' => 'create'
));

$session->addPost('/login', array(
    'action' => 'login'
));

$session->addPut('', array(
    'action' => 'update'
));

$session->addDelete('', array(
    'action' => 'delete'
));
*/

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
$router->mount($summary);
$router->mount($currency);
$router->mount($login);
$router->mount($registration);
$router->mount($settings);

?>