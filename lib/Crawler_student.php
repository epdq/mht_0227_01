<?php


	require_once "lib/simple_html_dom.php";

	date_default_timezone_set('Asia/Shanghai');

	class Crawler_student
	{
		private $base_url = 'https://cn.student.com';
		//城市采集默认地址
		private $url = 'https://cn.student.com/us';

		function __construct(){

		}

		// 获取student城市列表
		public function getCityList()
		{

			$citylist = [];	// 城市列表

			// $crawler = new Crawler($this->url);
			// $html = $crawler->getHtml();
			$dom = file_get_html($this->url);	// 获取dom对象

			$cityDom = $dom->find('.browse__cities a');	// 获取城市a标签dom对象集合

			if ($cityDom != null) {
				foreach ($cityDom as $a) {
					$cityname = explode('<br>', $a->innertext);
					$city['name'] = trim($cityname[0]);
					$city['url'] = $this->base_url . $a->href;
					$citylist[] = $city;
				}
			}

			$dom->clear(); 
			unset($cityDom);
			unset($dom);

			return $citylist;
		}


		// 获取student大学列表
		public function getSchoolList()
		{

			$schoolList = [];	// 城市列表

			// $crawler = new Crawler($this->url);
			// $html = $crawler->getHtml();
			$dom = file_get_html($this->url);	// 获取dom对象

			$schoolDom = $dom->find('.browse__universities a');	// 获取城市a标签dom对象集合

			if ($schoolDom != null) {
				foreach ($schoolDom as $a) {
					$schoolName = explode('<br>', $a->innertext);
					$school['name'] = trim($schoolName[0]);
					$school['url'] = $this->base_url . $a->href;
					$schoolList[] = $school;
				}
			}

			$dom->clear(); 
			unset($schoolDom);
			unset($dom);

			return $schoolList;
		}

		// 获取城市所在的公寓分页数量
		public function getCityPage($cityUrl)
		{
			$page = 1;	// 分页数目
			$dom = file_get_html($cityUrl);	// 获取dom对象
			$pageDom = $dom->find('.pagination__item a');	// 获取分页a标签对象
			if ($pageDom != null) {
				$page = $dom->find('.pagination__item a', -1)->innertext;
			}
			$dom->clear();
			unset($dom);
			return (int)$page;
		}

		// 获取页面公寓列表
		public function getRoomList($pageUrl)
		{
			$roomList = [];	// 城市列表

			$dom = file_get_html($pageUrl);	// 获取dom对象

			$roomDom = $dom->find('a.property-image__container');	// 获取住宿a标签dom对象集合

			if ($roomDom != null) {
				foreach ($roomDom as $a) {
					$room['url'] = $this->base_url . $a->href;
					$roomList[] = $room;
				}
			}

			$dom->clear(); 
			unset($roomDom);
			unset($dom);

			return $roomList;
		}

		// 获取房间详细信息
		public function getRoomInfo($RoomUrl)
		{
			$roomInfo = [];	// 公寓信息

			$dom = file_get_html($RoomUrl);	// 获取dom对象


			if ($dom != false) {

				$room['ApartmentName'] = $dom->find('h1', 0)->innertext;	// 公寓名称
				$room['ApartmentDesc'] = $dom->find('.about__summary-text', 0)->innertext;	// 公寓介绍
				$room['Addr'] = $dom->find('.about__feature-text', 0)->plaintext;	// 公寓地址
				$price = $dom->find('.room-matrix__categories-price', 0)->plaintext;
				$room['Price'] = $price;	// 公寓价格
				$imgSrc = $dom->find('.hero-banner__image', 0)->src;	// 主图地址
				$imgSrc = 'http:' . $imgSrc;
				$imgNmae = date('Ymdhis') . rand(1000, 9999) . '.jpg';	// 保存到本地图片名称
				copy($imgSrc, 'images/' . $imgNmae);
				$room['Pic'] = $imgNmae;	// 本地图片地址

				// 公寓设施
				$facility = $dom->find('.accordion__content div');
				foreach ($facility as $key => $value) {
					$room['facility'][] = trim($value->plaintext);
				}
				
				// 地图数据
				$map = $dom->find('#map', 0);
				$room['map'] = $map->getAttribute('data-map');

				$roomInfo = $room;
			}

			$dom->clear();
			unset($dom);

			return $roomInfo;
		}
	}


	$crawler = new Crawler_student();

	// 城市列表获取
	// $citylist = $crawler->getCityList();
	// var_dump($citylist);


	// 学校列表获取
	// $schoolList = $crawler->getSchoolList();
	// var_dump($schoolList);

	// 城市分页数获取
	//var_dump($crawler->getCityPage('https://cn.student.com/us/los-angeles'));

	// 获取页面住宿列表
	//var_dump($crawler->getRoomList('https://cn.student.com/us/los-angeles?page_number=1'));
	
	// 获取公寓详细信息
	var_dump($crawler->getRoomInfo('https://cn.student.com/us/tucson/p/the-seasons-tucson'));
