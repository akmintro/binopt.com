<?php

    //  Метод занимается обработкой сигналов
    public function signalHandler($signo) {
        switch($signo) {
            case SIGTERM:
                //  При получении сигнала завершения работы устанавливаем флаг...
                $this->stop = true;
                break;
            //  default:
            //  Таким же образом записываем реакцию на любые другие сигналы если нам это нужно...
        }
    }

    //  Собственно детальная проверка что происходит с демоном, жив он или мёрт и как так получилось...
    function isDaemonActive($pid_file) {
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

    function runDaemon($pid_file)
    {
        if (isDaemonActive($pid_file)) {
            echo "Daemon '".$pid_file."' is already exist!\n";
            exit(0);
        }

        //  Назначаем метод, который будет отвечать за обработку системных сигналов...
        pcntl_signal(SIGTERM,[$this,'signalHandler']);

        //  Получаем pid процесса с помощью встроенной функции getmypid() и записываем его в pid файл...
        file_put_contents($pid_file, getmypid());

        //  Закрываем порочные связи со стандартным вводом-выводом...
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);

        //  Перенаправляем ввод-вывод туда куда нам надо или не надо...
        $STDIN = fopen('/dev/null', 'r');
        $STDOUT = fopen('/dev/null', 'wb');
        $STDERR = fopen('/dev/null', 'wb');
    }

    $proc_num = 0;

    // Создаем дочерний процесс
    if( pcntl_fork() ) {
        // Создаем дочерний процесс
        if( pcntl_fork() )
        {
            // Выходим из родительского процесса, привязанного к консоли...
            exit(0);
        }
        else
            $proc_num = 2;
    }
    else
        $proc_num = 1;

    //	Делаем основным процессом дочерний...
    posix_setsid();
    //	Включаем тики, в противном случае скрипт просто повисает и не реагирует на внешние раздражители...
    declare(ticks=1);

    // первый скрипт
    if($proc_num == 1)
    {
        runDaemon('currency_daemon.pid');

        require('currency_script.php');
    }
    // второй скрипт
    else if($proc_num == 2)
    {
        runDaemon('socket_daemon.pid');

        require('echows.php');
    }
?>