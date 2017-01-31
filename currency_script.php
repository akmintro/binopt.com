<?php
function get_rand($max, $last)
{
    $array = array();

    $part1 = 1/($last+1);
    $part2 = 1/($max-$last+1);

    $randval = ($max+2)*rand(0,1000)/2000;

    for($i=0; $i<$last; ++$i)
    {
        if($i==0)
            $array[$i] = $part1;
        else
            $array[$i] = $array[$i-1] + $part1;

        if($randval>$array[$i])
            $randval-=$array[$i];
        else
            return $i;
    }

    for($i=$last; $i<=$max; ++$i)
    {
        if($i==$last)
            $array[$i] = 1;
        else
            $array[$i] = $array[$i-1] - $part2;

        if($randval>$array[$i])
            $randval-=$array[$i];
        else
            return $i;
    }
}

function get_last($cur_val, $real_val)
{
    $full = 20;
    $half = $full/2;

    $length = strlen(substr(strrchr($real_val, "."), 1)) + 1;
    $power = $half ** $length;
    $last_shift = abs($cur_val - $real_val) * $power;

    $max = max($last_shift, $full);
    if($cur_val < $real_val)
        $start_val = min($cur_val, $real_val-$half/$power);
    else
        $start_val = max($real_val-$half/$power, min($real_val, $cur_val-$full/$power));

    $rand = get_rand($max, ($cur_val-$start_val)*$power);
    $last = $start_val+$rand/$power;

    //echo "\nreal: ".$real_val." - cur: ".$cur_val." - start: ".$start_val."\t - rand: ".$rand."\t - last: ".$last;
    return $last;
}

function save_history($history)
{
    $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => 'http://binopt.com/api/v1/currency',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($history)
    ));
    $response = curl_exec($myCurl);
    curl_close($myCurl);

    //echo "Ответ на Ваш запрос: ".$response;
}

$filename = "currency_data.txt";

$start = microtime(true);
set_time_limit(60);

for ($i = 0; $i < 59; ++$i) {
    //echo $i."\n";

    if(($i%6)==0) {
        $real_data = json_decode(file_get_contents('http://tsw.ru.forexprostools.com/api.php?action=refresher&pairs=1,2,3,4,5,6,7,8,9,11,12,15,16,49,50,53,54,57&timeframe=60'), true);
        if(file_exists($filename))
            $current_data = json_decode(file_get_contents($filename), true);
        else
            $current_data = null;

        foreach ($real_data as $key => $value)
        {
            if($key != "time") {
                $name = $value['summaryName'];
                $real = floatval(str_replace(',', '.', $value['summaryLast']));
                $current = ($current_data == null || !isset($current_data[$key])) ? $real : $current_data[$key]['last'];
                $value = array("name" => $name, "real" => $real, "current" => $current);
            }
            $data[$key] = $value;
        }
    }

    foreach ($data as $key => $value)
    {
        if($key != "time") {
            $last = get_last($value['current'], $value['real']);
            $value['current'] = $last;
            $new_value = array("name" => $value['name'], "last" => $last);
        }
        else
            $new_value = $value = gmdate("Y-m-d H:i:s");

        $data[$key] = $value;
        $result[$key] = $new_value;
    }
    //var_dump($data);
    file_put_contents($filename, json_encode($result));

    if(($i%10)==0)
        save_history($result);

    time_sleep_until($start + $i + 1);
}


?>
