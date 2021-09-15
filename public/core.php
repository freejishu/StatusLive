<?php
header("Content-Type: application/x-www-form-urlencoded; charset=UTF-8");

//如果使用隐私模式，在这里填入你的API_KEY，如果使用公开模式则置空避免key被更改。
$apikey = 'ur609264-826267974xxxxxxxxxxxxxxx';

$link = 'https://api.uptimerobot.com/v2/getMonitors';

//请求UptimeRobot
$data = json_decode(file_get_contents("php://input"));
if($apikey != ''){
    $data->api_key = $apikey;
}
$data  = http_build_query($data);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($curl);
curl_close($curl);
$o = json_decode($output, true);

//替换删除敏感数据，如有其他需要可以自行增加
for ($i = 0; $i < count($o['monitors']); $i++) {
    $o['monitors'][$i]['url'] = "";
    $o['monitors'][$i]['http_username'] = "";
    $o['monitors'][$i]['http_password'] = "";
    $o['monitors'][$i]['port'] = "";
}

exit(json_encode($o));