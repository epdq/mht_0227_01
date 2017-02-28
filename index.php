<?php

	include_once "lib/Mysql.class.php";
	include_once "lib/Crawler.class.php";
	include_once "lib/simple_html_dom.php";

	$crawler = new Crawler();
	$html = $crawler->getHtml("http://www.baidu.com");

	$dom = str_get_html($html);

	$arrDom = $dom->find('a');

	foreach ($arrDom as $key => $value) {
		echo $value->href . '<br/>';
	}

	$dom->clear(); 
	unset($dom);
