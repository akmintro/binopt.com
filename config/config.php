<?php
return new \Phalcon\Config(array(
    'application' => array(
        'name' => 'Bin Opt'
    ),

    'root_dir' => __DIR__.'/../',
/*
    'redis' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
    ),
*/
    'session' => array(
        'unique_id' => 'binopt',
        'name' => 'binopt',
        'path' => 'tcp://127.0.0.1:6379?weight=1'
    ),

    'view' => array(
        'cache' => array(
            'dir' => __DIR__.'/../cache/volt/'
        )
    ),

    'database' => array(
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'binoptuser',
        'password' => 'ufhhbgjnnthlcX2',
        'dbname' => 'binopt',
    ),
/*
    'apiKeys' => array(
        '6y825Oei113X3vbz78Ck7Fh7k3xF68Uc0lki41GKs2Z73032T4z8m1I81648JcrY'
    ),
*/
    'parameters' => array(
        'gmailusername' => 'anishenkokirill@gmail.com',
        'gmailpassword' => 'fybotyr0',
        'winpercent' => 0.86,
        'currencydata' => __DIR__ . '/../scripts/currency_data.txt',
        'scriptsfolder' => __DIR__ . '/../scripts',
        'adminwallet' => "walletwallet",
        'servertoken' => __DIR__ .'/../scripts/servertoken.txt'
    )
));
?>

