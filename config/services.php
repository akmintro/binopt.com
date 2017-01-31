<?php
use \Phalcon\Logger\Adapter\File as Logger;

$di['session'] = function () use ($config) {
    $session = new \Phalcon\Session\Adapter\Redis(array(
        'uniqueId' => $config->session->unique_id,
        'path' => $config->session->path,
        'name' => $config->session->name
    ));

    $session->start();

    return $session;
};

$di['security'] = function () {
    $security = new \Phalcon\Security();
    $security->setWorkFactor(10);

    return $security;
};
/*
$di['redis'] = function () use ($config) {
    $redis = new \Redis();
    $redis->connect(
        $config->redis->host,
        $config->redis->port
    );

    return $redis;
};
*/
$di['url'] = function () use ($config, $di) {
    $url = new \Phalcon\Mvc\Url();

    return $url;
};

$di['voltService'] = function($view, $di) use ($config) {
    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

    if (!is_dir($config->view->cache->dir)) {
        mkdir($config->view->cache->dir);
    }

    $volt->setOptions(array(
        "compiledPath" => $config->view->cache->dir,
        "compiledExtension" => ".compiled",
        "compileAlways" => true
    ));

    return $volt;
};

$di['logger'] = function () {
    $file = __DIR__."/../logs/".date("Y-m-d").".log";
    $logger = new Logger($file, array('mode' => 'w+'));

    return $logger;
};

$di['cache'] = function () use ($di, $config) {
    $frontend = new \Phalcon\Cache\Frontend\Igbinary(array(
        'lifetime' => 3600 * 24
    ));

    $cache = new \Phalcon\Cache\Backend\Redis($frontend, array(
        'redis' => $di['redis'],
        'prefix' => $config->application->name.':'
    ));

    return $cache;
};

$di['db'] = function () use ($config) {

    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname,
        "options" => array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
            \PDO::ATTR_CASE => \PDO::CASE_LOWER,
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            \PDO::ATTR_PERSISTENT => true
        )
    ));
};

$di['mongo'] = function() {
    $mongo = new MongoClient();
    return $mongo->selectDB("bitpress");
};

$di['collectionManager'] = function(){
    return new Phalcon\Mvc\Collection\Manager();
};

$di['modelsCache'] = $di['cache'];

// Core managers

$di['core_operator_manager'] = function() {
    return new App\Core\Managers\OperatorManager();
};

$di['core_user_manager'] = function() {
    return new App\Core\Managers\UserManager();
};

$di['core_country_manager'] = function() {
    return new App\Core\Managers\CountryManager();
};

$di['core_robotcodetype_manager'] = function() {
    return new App\Core\Managers\RobotcodetypeManager();
};

$di['core_robotcode_manager'] = function() {
    return new App\Core\Managers\RobotcodeManager();
};

$di['core_account_manager'] = function() {
    return new App\Core\Managers\AccountManager();
};

$di['core_promo_manager'] = function() {
    return new App\Core\Managers\PromoManager();
};

$di['core_instrument_manager'] = function() {
    return new App\Core\Managers\InstrumentManager();
};


$di['core_bet_manager'] = function() {
    return new App\Core\Managers\BetManager();
};

$di['core_invest_manager'] = function() {
    return new App\Core\Managers\InvestManager();
};

$di['core_deposit_manager'] = function() {
    return new App\Core\Managers\DepositManager();
};

$di['core_withdrawal_manager'] = function() {
    return new App\Core\Managers\WithdrawalManager();
};

$di['core_summary_manager'] = function() {
    return new App\Core\Managers\SummaryManager();
};
?>