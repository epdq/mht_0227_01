<?php


	//require_once "lib/Crawler.class.php";
	require_once "lib/simple_html_dom.php";

	class Crawler_51room
	{
		//采集地址
		private $url = 'http://www.51room.co.uk/property/rent/us/new_york';

		function __construct(){

		}

		// 获取51room城市列表
		public function getCityList()
		{

			// $crawler = new Crawler($this->url);
			// $html = $crawler->getHtml();
			$dom = file_get_html($this->url);	// 获取dom对象

			$cityDom = $dom->find('#cityModal a');

			$citylist = [];
			foreach ($cityDom as $a) {
				$cityname = explode('<br>', $a->innertext);
				$city['name'] = trim($cityname[0]);
				$city['url'] = $a->href;
				$citylist[] = $city;
			}

			$dom->clear(); 
			unset($cityDom);
			unset($dom);

			return $citylist;
		}

		// 获取城市所在的公寓数量
		public function getCityRoomNum($cityUrl)
		{
			# code...
		}
	}


	$apartment = new Crawler_51room();
	$citylist = $apartment->getCityList();
	var_dump($citylist);