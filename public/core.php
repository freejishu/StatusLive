<?php
header("Content-Type: application/x-www-form-urlencoded; charset=UTF-8");

//如果使用隐私模式，在这里填入你的API_KEY，如果使用公开模式则置空避免key被更改。
$apikey = 'ur609264-826267974xxxxxxxxxxxxxxx';

//json缓存目录，可自行配置
$file_name = 'uptime.json';

//缓存时间，单位为秒，为11秒是因为UptimeRobotAPI免费用户调用限制为6次/分钟
$cache_time = 11;


if(file_exists($file_name)){
    $str = fread(fopen($file_name, "r"), filesize($file_name)); //指定读取大小，这里把整个文件内容读取出来
    $json = json_decode($str, true);
    if(time() - $json['time'] < $cache_time){
        header("StatusLiveCache: Hit");
        exit($json['json']);
    } 
}
$link = 'https://api.uptimerobot.com/v2/getMonitors';
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
if(@$o['stat']=='ok'){
    header("StatusLiveCache: Miss.");
    //替换删除敏感数据，如有其他需要可以自行增加
    for ($i = 0; $i < count($o['monitors']); $i++) {
        $o['monitors'][$i]['url'] = "";
        $o['monitors'][$i]['http_username'] = "";
        $o['monitors'][$i]['http_password'] = "";
        $o['monitors'][$i]['port'] = "";
    }
    $json = [];
    $json['time'] = time();
    $json['json'] = json_encode($o);
    
    file_put_contents($file_name, json_encode($json));
    
    exit($json['json']);
}else{
    //请求错误不缓存
    header("StatusLiveCache: Miss, Request Fail.");
    exit(json_encode($o));
}
