<?php
$filename = "currency_data.txt";

//system config
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);		//Время выполнения скрипта безгранично
ob_implicit_flush();	//Включаем вывод без буферизации 
ignore_user_abort(true);//Выключаем зависимость от пользователя
//system config

$baseDir = dirname(__FILE__);
$pidfile = $baseDir.'/pid_file.pid';
$offfile = $baseDir.'/off_file.pid';

//srdin/stdout 
//ini_set('error_log',$baseDir.'/echowserrors.txt');
fclose(STDIN);
//fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
//$STDOUT = fopen($baseDir.'/echowsconsolelog.txt', 'ab');
//$STDERR = fopen($baseDir.'/echowsconsoleerr.txt', 'ab');
//srdin/stdout 

//log-file
$GLOBALS['file'] = $baseDir.'/echowslog.html';
consolestart();
consolemsg("echows - try to start..."); 
//log-file


//pid-file
//Если в PID файле хранится PID процесса и он активен, то не запускаем копию
if (isDaemonActive($pidfile)) {
	consolemsg("CANCEL echows - already active"); 
	consoleend();
	exit();
}
////file_put_contents($pidfile, getmypid());//СОХРАНЯЕМ PID в файле
consolemsg("OK getmypid = ".getmypid()); 
//pid-file

$timelimit = 0; // если 0, то тогда безлимитно, только на сообщение, иначе кол-во секунд
$starttime = round(microtime(true),2);

consolemsg("socket - try to start...");
$socket = stream_socket_server("tcp://127.0.0.1:8889", $errno, $errstr);

if (!$socket) {
	consolemsg("ERROR socket unavailable " .$errstr. "(" .$errno. ")");
	unlink($pidfile);
	consolemsg("pidfile ".$pidfile." ulinked");
	consoleend();
    die($errstr. "(" .$errno. ")\n");
}

$connects = array();
$currency = array();
while (true) {
	//формируем массив прослушиваемых сокетов:
	$read = $connects;
	$read []= $socket;
	$write = $except = null;

	stream_select($read, $write, $except, 1);

	if (in_array($socket, $read)) {//есть новое соединение то обязательно делаем handshake
		//принимаем новое соединение и производим рукопожатие:
		if (($connect = stream_socket_accept($socket, -1)) && $info = handshake($connect)) {
			consolemsg("new connection... connect=".$connect.", info=".$info." OK");            
			$connects[] = $connect;//добавляем его в список необходимых для обработки
			$currency[] = 1;
			onOpen($connect, $info);//вызываем пользовательский сценарий
            fwrite($connect, encode(json_encode(["data" => json_decode(file_get_contents("http://binopt.com/api/v1/currency/history?instrument=1"), true), "type" => "history"])));
		}
		unset($read[ array_search($socket, $read) ]);
	}

	foreach($read as $connect) {//обрабатываем все изменившиеся соединения
		$data = fread($connect, 100000);

		if (!$data) { //соединение было закрыто
				consolemsg("connection closed...");    
				fclose($connect);
			$id = array_search($connect, $connects);
		    unset($connects[$id]);
		    unset($currency[$id]);
		    onClose($connect);//вызываем пользовательский сценарий
				consolemsg("OK");    
		    continue;
		}

		$cur = decode($data)["payload"];
		$currency[array_search($connect, $connects)] = $cur;
        fwrite($connect, encode(json_encode(["data" => json_decode(file_get_contents("http://binopt.com/api/v1/currency/history?instrument=".$cur), true), "type" => "history"])));
	}

	echo count($connects);

    $json_data = json_decode(file_get_contents($filename), true);
    $package = array();
    foreach ($json_data as $key => $val)
    {
        if($key != "time")
        {
            $package[$key] = ["data" => $val, "type" => "current"];
        }
    }

	foreach($connects as $key => $connect) {//обрабатываем все соединения
		fwrite($connect, encode(json_encode($package[$currency[$key]])));
	}

	//Здесь же можно поставить и лимиты на выжираемую память но пока типа на вермя
	if($timelimit!=0 && ( round(microtime(true),2) - $starttime) > $timelimit) { //Если за пределами timelimit - вырубаем процесс
			consolemsg("time limit is over"); 
			consolemsg("time = ".(round(microtime(true),2) - $starttime)); 
			fclose($socket);
			consolemsg("socket - closed");	
			unlink($pidfile);
			consolemsg("pidfile ".$pidfile." unlinked");
			consoleend();
			exit();		
	}


	if(file_exists($offfile)){   //Если встретили offile то завершаем процесс
		consolemsg("off file found"); 
		consolemsg("time = ".(round(microtime(true),2) - $starttime)); 
		fclose($socket);
		consolemsg("socket - closed");	
		unlink($pidfile);
		consolemsg("pidfile ".$pidfile." unlinked");
		
		if(!unlink($offfile)) {
			consolemsg("ERROR DELETING OFF FILE".$offfile);
		//не могу уничтожить pid-файл. ошибка
		exit(-1);
	    }

		consolemsg("offfile ".$offfile." unlinked");
		consoleend();
		exit();		
	}
}

//unreachble code
fclose($socket);
consolemsg("socket - closed");	
unlink($pidfile);
consolemsg("pidfile ".$pidfile." unlinked");
consoleend();

//------------------------------------------------------------------------------------------------------------------------------------------------

function handshake($connect) { //Функция рукопожатия
    $info = array();

    $line = fgets($connect);
    $header = explode(' ', $line);
    $info['method'] = $header[0];
    $info['uri'] = $header[1];

    //считываем заголовки из соединения
    while ($line = rtrim(fgets($connect))) {
        if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
            $info[$matches[1]] = $matches[2];
        } else {
            break;
        }
    }

    $address = explode(':', stream_socket_get_name($connect, true)); //получаем адрес клиента
    $info['ip'] = $address[0];
    $info['port'] = $address[1];

    if (empty($info['Sec-WebSocket-Key'])) {
        return false;
    }

    //отправляем заголовок согласно протоколу вебсокета
    $SecWebSocketAccept = base64_encode(pack('H*', sha1($info['Sec-WebSocket-Key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "Sec-WebSocket-Accept:".$SecWebSocketAccept."\r\n\r\n";
    fwrite($connect, $upgrade);

    return $info;
}

function encode($payload, $type = 'text', $masked = false) {
    $frameHead = array();
    $payloadLength = strlen($payload);

    switch ($type) {
        case 'text':
            // first byte indicates FIN, Text-Frame (10000001):
            $frameHead[0] = 129;
            break;

        case 'close':
            // first byte indicates FIN, Close Frame(10001000):
            $frameHead[0] = 136;
            break;

        case 'ping':
            // first byte indicates FIN, Ping frame (10001001):
            $frameHead[0] = 137;
            break;

        case 'pong':
            // first byte indicates FIN, Pong frame (10001010):
            $frameHead[0] = 138;
            break;
    }

    // set mask and payload length (using 1, 3 or 9 bytes)
    if ($payloadLength > 65535) {
        $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 255 : 127;
        for ($i = 0; $i < 8; $i++) {
            $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
        }
        // most significant bit MUST be 0
        if ($frameHead[2] > 127) {
            return array('type' => '', 'payload' => '', 'error' => 'frame too large (1004)');
        }
    } elseif ($payloadLength > 125) {
        $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 254 : 126;
        $frameHead[2] = bindec($payloadLengthBin[0]);
        $frameHead[3] = bindec($payloadLengthBin[1]);
    } else {
        $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
    }

    // convert frame-head to string:
    foreach (array_keys($frameHead) as $i) {
        $frameHead[$i] = chr($frameHead[$i]);
    }
    if ($masked === true) {
        // generate a random mask:
        $mask = array();
        for ($i = 0; $i < 4; $i++) {
            $mask[$i] = chr(rand(0, 255));
        }

        $frameHead = array_merge($frameHead, $mask);
    }
    $frame = implode('', $frameHead);

    // append payload to frame:
    for ($i = 0; $i < $payloadLength; $i++) {
        $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
    }

    return $frame;
}

function decode($data){
    $unmaskedPayload = '';
    $decodedData = array();

    // estimate frame type:
    $firstByteBinary = sprintf('%08b', ord($data[0]));
    $secondByteBinary = sprintf('%08b', ord($data[1]));
    $opcode = bindec(substr($firstByteBinary, 4, 4));
    $isMasked = ($secondByteBinary[0] == '1') ? true : false;
    $payloadLength = ord($data[1]) & 127;

    // unmasked frame is received:
    if (!$isMasked) {
        return array('type' => '', 'payload' => '', 'error' => 'protocol error (1002)');
    }

    switch ($opcode) {
        // text frame:
        case 1:
            $decodedData['type'] = 'text';
            break;

        case 2:
            $decodedData['type'] = 'binary';
            break;

        // connection close frame:
        case 8:
            $decodedData['type'] = 'close';
            break;

        // ping frame:
        case 9:
            $decodedData['type'] = 'ping';
            break;

        // pong frame:
        case 10:
            $decodedData['type'] = 'pong';
            break;

        default:
            return array('type' => '', 'payload' => '', 'error' => 'unknown opcode (1003)');
    }

    if ($payloadLength === 126) {
        $mask = substr($data, 4, 4);
        $payloadOffset = 8;
        $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
    } elseif ($payloadLength === 127) {
        $mask = substr($data, 10, 4);
        $payloadOffset = 14;
        $tmp = '';
        for ($i = 0; $i < 8; $i++) {
            $tmp .= sprintf('%08b', ord($data[$i + 2]));
        }
        $dataLength = bindec($tmp) + $payloadOffset;
        unset($tmp);
    } else {
        $mask = substr($data, 2, 4);
        $payloadOffset = 6;
        $dataLength = $payloadLength + $payloadOffset;
    }

    /**
     * We have to check for large frames here. socket_recv cuts at 1024 bytes
     * so if websocket-frame is > 1024 bytes we have to wait until whole
     * data is transferd.
     */
    if (strlen($data) < $dataLength) {
        return false;
    }

    if ($isMasked) {
        for ($i = $payloadOffset; $i < $dataLength; $i++) {
            $j = $i - $payloadOffset;
            if (isset($data[$i])) {
                $unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
            }
        }
        $decodedData['payload'] = $unmaskedPayload;
    } else {
        $payloadOffset = $payloadOffset - 4;
        $decodedData['payload'] = substr($data, $payloadOffset);
    }

    return $decodedData;
}

//пользовательские сценарии:

function onOpen($connect, $info) {
	consolemsg("open OK"); 

    //fwrite($connect, encode('Привет, мы соеденены'));
}

function onClose($connect) {
    consolemsg("close OK");
}

function consolestart(){
	consolemsg("console - start");
}

function consolemsg($msg){
	$file = null;/*
	if(!file_exists($GLOBALS['file'])) {
	    $file = fopen($GLOBALS['file'],"w");
	}else
	    $file = fopen($GLOBALS['file'],"a");
	
	echo $msg."\r\n";
	fputs ($file, "[<b>".date("Y.m.d-H:i:s")."</b>]". $msg ."<br />\r\n"); 
	fclose($file); */
}

function consoleend(){
	consolemsg("console - end");
}

function isDaemonActive($pidfile) {
  if( file_exists($pidfile) ) {
    $pid = file_get_contents($pidfile);
    //получаем статус процесса
	$status = getDaemonStatus($pid);
    if($status['run']) { 
	  //демон уже запущен
	  consolemsg("daemon already running info=".$status['info']);
      return true;
    } else {
      //pid-файл есть, но процесса нет
      consolemsg("there is no process with PID = ".$pid.", last termination was abnormal...");
      consolemsg("try to unlink PID file...");
      if(!unlink($pidfile)) {
	    consolemsg("ERROR");
        //не могу уничтожить pid-файл. ошибка
        exit(-1);
      }
      consolemsg("OK");
    }
  }
  return false;
}

function getDaemonStatus($pid) {
	$result = array ('run'=>false);
	$output = null;
	exec("ps -aux -p ".$pid, $output);

	if(count($output)>1){//Если в результате выполнения больше одной строки то процесс есть! т.к. первая строка это заголовок, а вторая уже процесс
		$result['run'] = true;
		$result['info'] = $output[1];//строка с информацией о процессе
	}
	return $result;
}
