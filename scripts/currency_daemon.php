<?php

// Создаем дочерний процесс
if( pcntl_fork() ) {
        exit(0);
}

//	Делаем основным процессом дочерний...
posix_setsid();
//	Включаем тики, в противном случае скрипт просто повисает и не реагирует на внешние раздражители...
declare(ticks=1);


$baseDir = dirname(__FILE__);
include $baseDir."/Daemon.php";
$daemon = new Daemon();
$daemon->runDaemon($baseDir.'/currency_daemon.pid', $baseDir.'/currency_log.txt', $baseDir.'/currency_err.txt');
require($baseDir.'/currency_script.php');

?>