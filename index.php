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

			$citylist = [];	// 城市列表

			// $crawler = new Crawler($this->url);
			// $html = $crawler->getHtml();
			$dom = file_get_html($this->url);	// 获取dom对象

			$cityDom = $dom->find('#cityModal a');	// 获取城市a标签dom对象集合

			if ($cityDom != null) {
				foreach ($cityDom as $a) {
					$cityname = explode('<br>', $a->innertext);
					$city['name'] = trim($cityname[0]);
					$city['url'] = $a->href;
					$citylist[] = $city;
				}
			}

			$dom->clear(); 
			unset($cityDom);
			unset($dom);

			return $citylist;
		}

		// 获取城市所在的公寓分页数量
		public function getCityPage($cityUrl)
		{
			$page = 1;	// 分页数目
			$dom = file_get_html($cityUrl);	// 获取dom对象
			$pageDom = $dom->find('.pagination a');	// 获取分页a标签对象
			if ($pageDom != null) {
				$page = $dom->find('.pagination a', -1)->innertext;
				if ($page == '&gt;'){	// 不是最后一页
					$page = $dom->find('.pagination a', -2)->innertext;
				}
			}
			$dom->clear();
			unset($dom);
			return (int)$page;
		}

		// 获取页面公寓列表地址
		public function getRoomList($pageUrl)
		{
			$roomList = [];	// 城市列表

			$dom = file_get_html($pageUrl);	// 获取dom对象

			$roomDom = $dom->find('a.thumbnail');	// 获取住宿a标签dom对象集合

			if ($roomDom != null) {
				foreach ($roomDom as $a) {
					$room['url'] = $a->href;
					$roomList[] = $room;
				}
			}

			$dom->clear(); 
			unset($roomDom);
			unset($dom);

			return $roomList;
		}
	}


	$crawler = new Crawler_51room();



	// 城市列表获取
	//$citylist = $crawler->getCityList();
	//var_dump($citylist);

	// 城市分页数获取
	//var_dump($crawler->getCityPage('http://www.51room.co.uk/property/rent/us/new_york'));

	// 获取页面住宿列表
	//var_dump($crawler->getRoomList('http://www.51room.co.uk/property/rent/us/alamo/1'));
