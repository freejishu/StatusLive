<?php
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://stats.uptimerobot.com/api/status-page/KQLq0Cy2M/1?sort=1');
//把 KQLq0Cy2M 改为你的 uptimerobot public page 地址后面的Key（如https://stats.uptimerobot.com/KQLq0Cy2M中的KQLq0Cy2M）
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($curl);
curl_close($curl);
echo $_GET['callback']."(";
print_r($data);
echo ")";