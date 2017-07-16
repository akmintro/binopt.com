<?php

use App\Api\Managers\JWTTokenManager;
use App\Api\Managers\SessionManager;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Cache\Frontend\None as FrontendNone;

use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;

$di['dispatcher'] = function () use ($di) {

    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setDefaultNamespace("App\Api\Controllers");

    $eventsManager = $di->getShared('eventsManager');
    $apiListener = new \App\Api\Listeners\ApiListener();
    $eventsManager->attach('dispatch', $apiListener);

    $dispatcher->setEventsManager($eventsManager);

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
    return new JWTTokenManager('borch_z_pampuchkami');
};
/*
$di['rest_session'] = function () {
    $redis = new Redis(
        new FrontendNone(["lifetime" => 36000])
    );
//    $redis = new Redis();
    return new SessionManager(86400, $redis);
};
*/
$di['acl'] = function() {

    $acl = new AclList();
    $acl->setDefaultAction(Acl::DENY);
    // Register roles
    $roles = [
        'guests' => new Role(
            'guest',
            'Anyone browsing the site who is not signed in is considered to be a "Guest".'
        ),
        'users'  => new Role(
            'user',
            'Member privileges, granted after sign in.'
        ),
        'operators'  => new Role(
            'operator',
            'Operator privileges, granted after sign in.'
        ),
        'admins'  => new Role(
            'admin',
            'Admin privileges, granted after sign in.'
        ),
        'server'  => new Role(
            'server',
            'Server privileges, granted after sign in.'
        )

    ];
    foreach ($roles as $role) {
        $acl->addRole($role);
    }


    //Server area resources
    $serverResources = array(
        'currency'   => array('create', 'delete'),
        'bets'       => array('read', 'update'),
        'login'      => array('delete'),
        'settings'   => array('read', 'update')
    );
    foreach ($serverResources as $resource => $actions) {
        $acl->addResource(new Resource($resource), $actions);
    }
    //Admin area resources
    $adminResources = array(
        'users'      => array('read', 'get', 'update', 'create', 'delete', 'getNew'),
        'operators'  => array('read', 'update', 'create', 'delete', 'getTemplate', 'updateTemplate', 'createTemplate'),
        'bets'       => array('read'),
        'deposits'   => array('read'),
        'withdrawals'=> array('read'),
        'summary'    => array('readDeposits'),
        'robotcodes' => array('read', 'update', 'create', 'delete'),
        'robotcodetypes' => array('read'),
        'login'      => array('unauthOper', 'isauthOper'),
        'promos'     => array('read', 'create', 'delete', 'update'),
        'settings'   => array('read', 'update')
    );
    foreach ($adminResources as $resource => $actions) {
        $acl->addResource(new Resource($resource), $actions);
    }
    //Operator area resources
    $operResources = array(
        'users'      => array('read', 'get', 'create', 'update', 'getNew'),
        'operators'  => array('getTemplate', 'updateTemplate', 'createTemplate'),
        'bets'       => array('read'),
        'deposits'   => array('read'),
        'withdrawals'=> array('read'),
        'summary'    => array('readDeposits'),
        'login'      => array('unauthOper', 'isauthOper'),
        'promos'     => array('read'),
        'robotcodes' => array('read'),
        'robotcodetypes' => array('read')
    );
    foreach ($operResources as $resource => $actions) {
        $acl->addResource(new Resource($resource), $actions);
    }
    //User area resources
    $userResources = array(
        'bets'    => array('create', 'readUser'),
        'login'   => array('unauthUser', 'isauthUser'),
        'users'   => array('changePassword')
    );
    foreach ($userResources as $resource => $actions) {
        $acl->addResource(new Resource($resource), $actions);
    }
    //Public area resources
    $publicResources = array(
        'registration' => array('registerUser', 'activateUser'),
        'login'      => array('authUser', 'authOper', 'uloginUser'),
        'currency'   => array('readHistory', 'readLast'),
        'instruments'=> array('read'),
        'countries'  => array('read')
    );
    foreach ($publicResources as $resource => $actions) {
        $acl->addResource(new Resource($resource), $actions);
    }


    //Grant access to server areas
    foreach ($serverResources as $resource => $actions) {
        foreach ($actions as $action){
            $acl->allow('server', $resource, $action);
        }
    }
    //Grant access to private area to role Admin
    foreach ($adminResources as $resource => $actions) {
        foreach ($actions as $action){
            $acl->allow('admin', $resource, $action);
        }
    }
    //Grant access to private area to role Users
    foreach ($userResources as $resource => $actions) {
        foreach ($actions as $action){
            $acl->allow('user', $resource, $action);
        }
    }
    //Grant access to private area to role Operators
    foreach ($operResources as $resource => $actions) {
        foreach ($actions as $action){
            $acl->allow('operator', $resource, $action);
        }
    }
    //Grant access to public areas to both users and guests
    foreach ($roles as $role) {
        foreach ($publicResources as $resource => $actions) {
            foreach ($actions as $action){
                $acl->allow($role->getName(), $resource, $action);
            }
        }
    }

    return $acl;
}

?>