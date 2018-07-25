<?php
if(@$_GET['t']=="1" && $_GET['id'] && $_GET['key']){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://stats.uptimerobot.com/api/monitor-page/'.$_GET['key'].'/'.$_GET['id']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    //echo $_GET['callback']."(";
    print_r($data);
    //echo ")";
    
}else if($_GET['key']){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://stats.uptimerobot.com/api/status-page/'.$_GET['key'].'/1?sort=1');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    //echo $_GET['callback']."(";
    print_r($data);
    //echo ")";
}
    