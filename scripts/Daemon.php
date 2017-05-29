<?php
class Daemon
{
    //  Собственно детальная проверка что происходит с демоном, жив он или мёрт и как так получилось...
    public function isDaemonActive($pid_file) {

        if( is_file($pid_file) ) {
            $pid = file_get_contents($pid_file);
            //  Проверяем на наличие процесса...
            if(posix_kill($pid,0)) {
                //  Демон уже запущен...
                return true;
            } else {
                //  pid-файл есть, но процесса нет...
                if(!unlink($pid_file)) {
                    //  Не могу уничтожить pid-файл. ошибка...
                    exit(-1);
                }
            }
        }
        return false;
    }

    public function runDaemon($pid_file)
    {
        if ($this->isDaemonActive($pid_file)) {
            echo "Daemon '".$pid_file."' is already exist!\n";
            exit(0);
        }

        //  Получаем pid процесса с помощью встроенной функции getmypid() и записываем его в pid файл...
        file_put_contents($pid_file, getmypid());
    }
}
?>