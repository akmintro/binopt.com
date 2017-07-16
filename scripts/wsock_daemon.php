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
$daemon->runDaemon($baseDir.'/wsock_daemon.pid');

//  Закрываем порочные связи со стандартным вводом-выводом...

fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);

//  Перенаправляем ввод-вывод туда куда нам надо или не надо...

$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen($baseDir.'/wsock_log.txt', 'wb');
$STDERR = fopen($baseDir.'/wsock_err.txt', 'wb');

require($baseDir.'/wsock.php');

?>