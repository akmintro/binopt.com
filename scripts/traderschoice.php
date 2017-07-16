<?php

$instruments =
[
    'AUD/JPY' => 49,
    'AUD/NZD' => 50,
    'AUD/USD' => 5,
    'EUR/AUD' => 15,
    'EUR/CAD' => 16,
    'EUR/GBP' => 6,
    'EUR/JPY' => 9,
    'EUR/USD' => 1,
    'GBP/AUD' => 53,
    'GBP/CAD' => 54,
    'GBP/CHF' => 12,
    'GBP/JPY' => 11,
    'GBP/USD' => 2,
    'NZD/CHF' => 57,
    'NZD/USD' => 8,
    'USD/CAD' => 7,
    'USD/CHF' => 4,
    'USD/JPY' => 3
];

function appendHTML(DOMNode $parent, $source) {
    $tmpDoc = new DOMDocument();
    $tmpDoc->loadHTML($source);
    foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
        $node = $parent->ownerDocument->importNode($node);
        $parent->appendChild($node);
    }
}

function curl_get_html($url)
{
    $headers = array(
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 OPR/45.0.0.255225845",
        "X-Requested-With: XMLHttpRequest",
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$htmlContent = curl_get_html("https://www.investing.com/common/ajax_func.php?action=getSentimentsTypes&pair_type=currency&page_name=active_instruments");
$htmlContent2 = curl_get_html("https://www.investing.com/common/ajax_func.php?action=getSentimentsTypes&pair_type=currency&page_name=active_instruments&page=2&moreResult=true");

$htmlContent = substr_replace($htmlContent, $htmlContent2, strpos($htmlContent, "</tbody>"), 0);

$DOM = new DOMDocument();
$DOM->loadHTML($htmlContent);

$Header = $DOM->getElementsByTagName('th');
$Detail = $DOM->getElementsByTagName('td');

//#Get header name of the table
foreach($Header as $NodeHeader)
{
    $aDataTableHeaderHTML[] = trim($NodeHeader->textContent);
}

//#Get row data/detail table without header name as key
$i = 0;
$j = 0;
foreach($Detail as $sNodeDetail)
{
    $aDataTableDetailHTML[$j][] = trim($sNodeDetail->textContent);
    $i = $i + 1;
    $j = $i % count($aDataTableHeaderHTML) == 0 ? $j + 1 : $j;
}

//#Get row data/detail table with header name as key and outer array index as row number
for($i = 0; $i < count($aDataTableDetailHTML); $i++)
{
    $instr = $instruments[$aDataTableDetailHTML[$i][1]];
    if($instr != null)
        $aTempData[$instr] = [$aDataTableHeaderHTML[2] => $aDataTableDetailHTML[$i][2], $aDataTableHeaderHTML[3] => $aDataTableDetailHTML[$i][3]];
}
$tradersData = $aTempData; unset($aTempData);
//print_r($tradersData);
?>