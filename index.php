<?php

	
	include_once 'lib/Mysql.class.php';
	include_once 'lib/Crawler_51room.php';

	$dbhost = '127.0.0.1';
	$dbuser = 'root';
	$dbpwd = 'root';
	$dbname = 'room';

	$mysql = new MySQL($dbhost, $dbuser, $dbpwd, $dbname);
	$crawler = new Crawler_51room();	// 51room.com 采集类

	$cityList = $crawler->getCityList();	// 城市列表

	var_dump($cityList);