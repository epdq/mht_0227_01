<?php



	require_once "lib/simple_html_dom.php";

	date_default_timezone_set('Asia/Shanghai');

	class Crawler_51room
	{
		//城市采集默认地址
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

		// 获取房间详细信息
		public function getRoomInfo($RoomUrl)
		{
			$roomInfo = [];	// 公寓信息

			$dom = file_get_html($RoomUrl);	// 获取dom对象


			if ($dom != false) {

				$room['ApartmentName'] = $dom->find('h3', 0)->innertext;	// 公寓名称
				$room['ApartmentDesc'] = str_replace(' ', '', $dom->find('.mt10', 0)->innertext);	// 公寓介绍
				$room['Addr'] = trim($dom->find('.panel-body', 1)->plaintext);	// 公寓地址
				$price = $dom->find('.panel-price span', 0)->plaintext;
				$room['Price'] = round(str_replace([' ', ','], '', $price), 2);	// 公寓价格

				// 主图保存
				$imgSrc = $dom->find('.thumbmain', 0)->src;	// 主图地址
				do{
					$imgNmae = date('Ymdhis') . rand(1000, 9999) . '.jpg';	// 保存到本地图片名称
					$copyRet = copy($imgSrc, 'images/' . $imgNmae);
				}while ($copyRet == false);

				$room['ThumbMain'] = $imgNmae;	// 本地图片地址

				// 小图保存
				$thumbSmallDom = $dom->find('.thumbsmall');
				foreach ($thumbSmallDom as $key => $value) {
					$imgSrc = $value->src;
					do{
						$imgNmae = date('Ymdhis') . rand(1000, 9999) . '.jpg';
						$copyRet = copy($imgSrc, 'images/' . $imgNmae);
					}while ($copyRet == false);
					$room['ThumbSmall'][] = $imgNmae;
				}

				// 公寓设施
				$facility = $dom->find('.mt10 .col-xs-3');
				foreach ($facility as $key => $value) {
					$room['Facility'][] = trim($value->plaintext);
				}
				
				$room['Notice'] = str_replace(' ', '', $dom->find('.mt10', 2)->innertext);	// 预订须知

				$room['RoomNo'] = $dom->find('.panel-price div', 1)->plaintext;
				$room['RoomDevice'] = $dom->find('.panel-price div', 3)->plaintext;

				$room['MapUrl'] = $dom->find('#min-map a', 0)->href;

				$roomInfo[] = $room;
			}

			$dom->clear();
			unset($dom);

			return $roomInfo;
		}

		public function getRoomMapInfo($mapUrl)
		{
			$mapInfo = [];	// 公寓信息
			$dom = file_get_html($mapUrl);	// 获取dom对象
			$html = $dom;
			$pattern = '/add_map\((.*?),(.*?),/';
			if(preg_match($pattern, $html, $map) > 0){
				$mapInfo['Longitude'] = $map[1];
				$mapInfo['Latitude'] = $map[2];
			}

			$dom->clear();
			unset($dom);
			return $mapInfo;
		}
	}


	//$crawler = new Crawler_51room();

	// 城市列表获取
	//$citylist = $crawler->getCityList();
	//var_dump($citylist);

	// 城市分页数获取
	//var_dump($crawler->getCityPage('http://www.51room.co.uk/property/rent/us/new_york'));

	// 获取页面住宿列表
	//var_dump($crawler->getRoomList('http://www.51room.co.uk/property/rent/us/alamo/1'));
	
	// 获取公寓详细信息
	//var_dump($crawler->getRoomInfo('http://www.51room.co.uk/property/rent/us/addison/pid/5219'));
	//
	// 获取公寓地图经纬度
	//var_dump($crawler->getRoomMapInfo('http://www.51room.co.uk/property/rent/us/addison/map?type=property&id=5219'));
