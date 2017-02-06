<?php
namespace App\Api\Controllers;

class WebsocketController extends BaseController {
    public function checkAction() {
        try {
            $pidfile = $this->config->parameters->scriptsfolder.'/pid_file.pid';
            if( !file_exists($pidfile) ) {
                $st_output = $this->perform("start");
            }
            else {
                $st_output = $this->perform("status");
            }

            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function startAction() {
        try {
            $st_output = $this->perform("start");

            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function stopAction() {
        try {
            $st_output = $this->perform("stop");

            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    public function statusAction() {
        try {
            $st_output = $this->perform("status");

            return $this->render($st_output);
        } catch (\Exception $e) {
            return $this->render(["meta" => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    private function perform($act)
    {
        $scripts = $this->config->parameters->scriptsfolder;


        $pidfile = $scripts.'/pid_file.pid';
        $offfile = $scripts.'/off_file.pid';

        if($act=='start') { //Если происходит действите старт, инициализируем игру
            exec("php -q ".$scripts."/echows.php &");

            //воткнуть паузу 0,5 для того, чтобы ws сервак мог нормально стартануть
            usleep(500000);

            return ["meta" => [
                "code" => 200,
                "message" => $this->status($pidfile)
            ]];

        } elseif($act=='stop'){ //Если действите старт не произошло и игра не инициализирована, то выходим

            $pid = $this->getstatus($pidfile);

            if($pid==-1){
                //echo "{color:\"grey\",msg:\"[<b>".date("Y.m.d-H:i:s")."</b>] ws echo server already stopped\"}";//Не работает передача - это JSON
                return ["meta" => [
                    "code" => 200,
                    "message" => $this->status($pidfile)
                ]];
            }
            //создаём offfile только зная что процесс запущен, чтобы избежать глюков при следующем запуске процесса
            file_put_contents($offfile, $pid);//СОХРАНЯЕМ PID в OFF файле

            //воткнуть паузу для того, чтобы сервак мог нормально завершить работу
            usleep(1500000);

            return ["meta" => [
                "code" => 200,
                "message" => $this->status($pidfile)
            ]];
        } elseif($act=='status'){ //Если действите старт не произошло и игра не инициализирована, то выходим
            return ["meta" => [
                "code" => 200,
                "message" => $this->status($pidfile)
            ]];
        }
    }

    private function status($pidfile) {

        if( file_exists($pidfile)  ) {
            $pid = file_get_contents($pidfile);

            //получаем статус процесса
            $output = null;
            exec("ps -aux -p ".$pid, $output);

            if(count($output)>1){//Если в результате выполнения больше одной строки то процесс есть! т.к. первая строка это заголовок, а вторая уже процесс
                return "ws echo server is running with PID =".$pid;
            } else {
                //pid-файл есть, но процесса нет
                return "ws echo server is down cause abnormal reason with PID =".$pid;
            }
        }
        return "ws echo server is off, press start";
    }

    private function getstatus($pidfile) {

        if( file_exists($pidfile) ) {
            $pid = file_get_contents($pidfile);

            //получаем статус процесса
            $output = null;
            exec("ps -aux -p ".$pid, $output);

            if(count($output)>1){//Если в результате выполнения больше одной строки то процесс есть! т.к. первая строка это заголовок, а вторая уже процесс
                return $pid;
            } else {
                //pid-файл есть, но процесса нет
                return -1;
            }
        }
        return -1;//файла и процесса нет
    }
}
?>