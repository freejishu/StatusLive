<?php
if(@$_GET['t']=="1" && $_GET['id'] && $_GET['key']){
    $curl = curl_init();
    //https://stats.uptimerobot.com/api/getMonitor/KQLq0Cy2M?m=780719148&_=1597850130030
    curl_setopt($curl, CURLOPT_URL, 'https://stats.uptimerobot.com/api/getMonitor/'.$_GET['key'].'?m='.$_GET['id']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    print_r($data);
    //https://stats.uptimerobot.com/api/getMonitorList/KQLq0Cy2M?page=1&_=1597848743667
    
}else if($_GET['key']){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://stats.uptimerobot.com/api/getMonitorList/'.$_GET['key'].'?page=1&_=1597848743667');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    print_r($data);
}
    