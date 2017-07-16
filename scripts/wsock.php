<?php
//system config
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);              //Время выполнения скрипта безгранично
ob_implicit_flush();    //Включаем вывод без буферизации
ignore_user_abort(true);//Выключаем зависимость от пользователя
$currencyfile = $baseDir.'/currency_data.txt';

require '/var/www/ratchet/bin/WSockServer.php';
?>