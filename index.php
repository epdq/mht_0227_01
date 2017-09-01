<?php

    $data = '';
    $url = 'http://www.ip.cn/';

    $queryServer = curl_init();
    $ip = rand(1,222).'.'.rand(1,222).'.'.rand(1,222).'.'.rand(1,222);
    curl_setopt($queryServer, CURLOPT_HTTPHEADER, array(
        'X-FORWARDED-FOR:' . $ip,
        'CLIENT-IP:' . $ip
    )); // 构造IP
    curl_setopt($queryServer, CURLOPT_REFERER, $url);
    curl_setopt($queryServer, CURLOPT_URL, $url);
    curl_setopt($queryServer, CURLOPT_HEADER, 0);
    curl_setopt($queryServer, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($queryServer, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($queryServer, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.80 Safari/537.36");
    curl_setopt($queryServer, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($queryServer, CURLOPT_TIMEOUT, 300);
    if ($data) {
        curl_setopt($queryServer, CURLOPT_POST, true);
        curl_setopt($queryServer, CURLOPT_POSTFIELDS, $data);
    }
    $contents = curl_exec($queryServer);

    echo ($contents);