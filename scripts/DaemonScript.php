<?php

    //  ����� ���������� ���������� ��������
    public function signalHandler($signo) {
        switch($signo) {
            case SIGTERM:
                //  ��� ��������� ������� ���������� ������ ������������� ����...
                $this->stop = true;
                break;
            //  default:
            //  ����� �� ������� ���������� ������� �� ����� ������ ������� ���� ��� ��� �����...
        }
    }

    //  ���������� ��������� �������� ��� ���������� � �������, ��� �� ��� ��� � ��� ��� ����������...
    function isDaemonActive($pid_file) {
        if( is_file($pid_file) ) {
            $pid = file_get_contents($pid_file);
            //  ��������� �� ������� ��������...
            if(posix_kill($pid,0)) {
                //  ����� ��� �������...
                return true;
            } else {
                //  pid-���� ����, �� �������� ���...
                if(!unlink($pid_file)) {
                    //  �� ���� ���������� pid-����. ������...
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

        //  ��������� �����, ������� ����� �������� �� ��������� ��������� ��������...
        pcntl_signal(SIGTERM,[$this,'signalHandler']);

        //  �������� pid �������� � ������� ���������� ������� getmypid() � ���������� ��� � pid ����...
        file_put_contents($pid_file, getmypid());

        //  ��������� �������� ����� �� ����������� ������-�������...
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);

        //  �������������� ����-����� ���� ���� ��� ���� ��� �� ����...
        $STDIN = fopen('/dev/null', 'r');
        $STDOUT = fopen('/dev/null', 'wb');
        $STDERR = fopen('/dev/null', 'wb');
    }

    $proc_num = 0;

    // ������� �������� �������
    if( pcntl_fork() ) {
        // ������� �������� �������
        if( pcntl_fork() )
        {
            // ������� �� ������������� ��������, ������������ � �������...
            exit(0);
        }
        else
            $proc_num = 2;
    }
    else
        $proc_num = 1;

    //	������ �������� ��������� ��������...
    posix_setsid();
    //	�������� ����, � ��������� ������ ������ ������ �������� � �� ��������� �� ������� ������������...
    declare(ticks=1);

    // ������ ������
    if($proc_num == 1)
    {
        runDaemon('currency_daemon.pid');

        require('currency_script.php');
    }
    // ������ ������
    else if($proc_num == 2)
    {
        runDaemon('socket_daemon.pid');

        require('echows.php');
    }
?>