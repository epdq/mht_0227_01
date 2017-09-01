<?php


	require_once "class/simple_html_dom.php";

	date_default_timezone_set('Asia/Shanghai');

	class CrawlerStudent
	{
		private $base_url = 'https://cn.student.com';
		//城市采集默认地址
		private $url = 'https://cn.student.com/us';

		function __construct(){

		}

		// 获取student城市列表
		public function getCityList($url = true)
		{

			$citylist = [];	// 城市列表

			// $crawler = new Crawler($this->url);
			// $html = $crawler->getHtml();
			$dom = file_get_html($this->url);	// 获取dom对象

			$cityDom = $dom->find('.browse__cities a');	// 获取城市a标签dom对象集合

			if ($cityDom != null) {
				foreach ($cityDom as $a) {
					$cityname = explode('<br>', $a->innertext);
					$city['AreaCnName'] = trim($cityname[0]);
					$pattern = '#/us/(.*?)$#';
					if(preg_match($pattern, $a->href, $arrAreaName)){
						$city['AreaEngName'] = ucwords(str_replace('-', ' ', $arrAreaName[1]));	// 城市英文名
						$city['FirstLetter'] = substr($city['AreaEngName'], 0, 1);	// 城市首字母
					}
					if ($url) {
						$city['url'] = $this->base_url . $a->href;
					}
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
			if ($dom) {
				
				$schoolDom = $dom->find('.browse__universities a');	// 获取城市a标签dom对象集合

				if ($schoolDom != null) {
					foreach ($schoolDom as $a) {
						$schoolName = explode('<br>', $a->innertext);
						$school['SchoolCnName'] = trim($schoolName[0]);
						$school['URL'] = $this->base_url . $a->href;
						$pattern = '#/u/(.*?)$#';
						if(preg_match($pattern, $a->href, $arrSchoolName)){
							$school['SchoolEngName'] = ucwords(str_replace('-', ' ', $arrSchoolName[1]));	// 城市英文名
							//$school['FirstLetter'] = substr($school['SchoolEngName'], 0, 1);	// 学校首字母
						}
						$schoolList[] = $school;
					}
				}

				$dom->clear(); 
				unset($schoolDom);

			}

			unset($dom);

			return $schoolList;
		}

		// 获取学校信息信息
		public function getSchoolInfo($schoolURL='')
		{
			$dom = file_get_html($schoolURL);	// 获取dom对象
			if ($dom) {
				# code...
				$cityDom = $dom->find('.breadcrumb__text', 1);	// 获取城市span标签对象
				if ($cityDom != null) {
					$schoolInfo['AreaCnName'] = str_replace(' / ', '', $cityDom->innertext);
				}
				$aDom = $dom->find('.breadcrumb__container a', 0);	
				if ($aDom != null) {
					$schoolInfo['AreaEngName'] = ucwords(str_replace(['/us/', '-'], ['', ' '],  $aDom->href));
					$schoolInfo['FirstLetter'] = substr($schoolInfo['AreaEngName'], 0, '1');
				}
				$pDom = $dom->find('.brief-introduction__count', 0);
				if ($pDom) {
					# code...
					$schoolInfo['ApartmentNum'] = intval($pDom->plaintext);
				}
				if (isset($dom)) {
					$dom->clear();
				}
			}

			unset($dom);
			return $schoolInfo;
		}

		// 获取城市所在的公寓分页数量
		public function getCityPage($cityUrl)
		{
			$page = 1;	// 分页数目
			$dom = file_get_html($cityUrl);	// 获取dom对象
			if ($dom) {
				# code...
				$pageDom = $dom->find('.pagination__item a');	// 获取分页a标签对象
				if ($pageDom != null) {
					$page = $dom->find('.pagination__item a', -1)->innertext;
				}
				$dom->clear();
			}
			unset($dom);
			return (int)$page;
		}

		// 获取页面公寓列表
		public function getRoomList($pageUrl)
		{
			$roomList = [];	// 城市列表

			$dom = file_get_html($pageUrl);	// 获取dom对象
			if ($dom) {
				# code...
				$roomDom = $dom->find('a.property-image__container');	// 获取住宿a标签dom对象集合

				if ($roomDom != null) {
					foreach ($roomDom as $a) {
						$room['url'] = $this->base_url . $a->href;
						$roomList[] = $room;
					}
				}

				$dom->clear(); 
			}

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

				$room['ApartmentName'] = trim($dom->find('h1', 0)->innertext);	// 公寓名称
				$room['Introduce'] = $dom->find('.about__summary-text', 0)->innertext;	// 公寓介绍
				$room['Address'] = $dom->find('.about__feature-text', 0)->plaintext;	// 公寓地址
				$priceDom = $dom->find('.room-matrix__categories-price', 0);
				if ($priceDom) {
					$price = $priceDom->plaintext;
					$price = str_replace(['$', ',', ' '], '', $price);
					$room['Price'] = (double)$price;	// 公寓价格
				}

				$minLeaseDom = $dom->find('.room-matrix__listing-tendancy', 0);	// 最短租期
				if ($minLeaseDom) {
					$room['MinLease'] = (int)str_replace(['最短租期', '个月', '起租', 'r', '\n'], '', $minLeaseDom->plaintext);
				}
				$imgSrc = $dom->find('.hero-banner__image', 0)->src;	// 主图地址
				$imgSrc = 'http:' . $imgSrc;
				//$imgNmae = date('Ymdhis') . rand(1000, 9999) . '.jpg';	// 保存到本地图片名称
				//copy($imgSrc, 'images/' . $imgNmae);
				$room['Images'] = [];
				$room['Images'][] = $imgSrc;	// 本地图片地址

				$imgDom = $dom->find('.gallery__item-image');
				if ($imgDom) {
					# code...
					foreach ($imgDom as $key => $img) {
						if ($img->src == '') {
							$room['Images'][] = 'http:' . $img->getAttribute('data-src');
						}else{
							$room['Images'][] = 'http:' . $img->src;
						}
						
					}
				}
				// 公寓设施
				$facilityDom = $dom->find('.accordion__content', 0);
				if ($facilityDom) {
					$facility = $facilityDom->plaintext;
					$pattern = '#\s*(\S+)\s*#';
					if(preg_match_all($pattern, $facility, $arrFacility)){
							$room['Facilites'] = $arrFacility[1];
					}
				}

				// 房租包含
				$facilityDom = $dom->find('.accordion__content', 1);
				if ($facilityDom) {
					$facility = $facilityDom->plaintext;
					$pattern = '#\s*(\S+)\s*#';
					if(preg_match_all($pattern, $facility, $arrFacility)){
							$room['ContainFacilities'] = $arrFacility[1];
					}
				}


				// 安全保障
				$facilityDom = $dom->find('.accordion__content', 2);
				if ($facilityDom) {
					$facility = $facilityDom->plaintext;
					$pattern = '#\s*(\S+)\s*#';
					if(preg_match_all($pattern, $facility, $arrFacility)){
							$room['SecurityFacilities'] = $arrFacility[1];
					}
				}
				
				// 房型
				// room-matrix__type
				$layoutDom = $dom->find('h3.room-matrix__type');
				if ($layoutDom) {
					foreach ($layoutDom as $key => $value) {
						$room['layout'][] = $value->plaintext;
					}
				}


				// 地图数据
				$map = $dom->find('#map', 0);
				if ($map) {
					$mapInfo = json_decode(htmlspecialchars_decode($map->getAttribute('data-map')), true);
					$room['Longitude'] = $mapInfo['property_data']['longitude'];
					$room['Latitude'] = $mapInfo['property_data']['latitude'];
				}



				$roomInfo = $room;
			}

			if (!is_scalar($dom)) {
				$dom->clear();
			}
			unset($dom);

			return $roomInfo;
		}
	}


	 // $crawler = new CrawlerStudent();

	// 城市列表获取
	 // $citylist = $crawler->getCityList();
	 // var_dump($citylist);


	// 学校列表获取
	// $schoolList = $crawler->getSchoolList();
	// var_dump($schoolList);
	 // die();

	// 学校所在城市
	// $area = $crawler->getSchoolInfo('https://cn.student.com/us/tempe/u/itt-technical-institute-tempe-campus');
	// var_dump($area);
	// die();

	// 城市分页数获取
	//var_dump($crawler->getCityPage('https://cn.student.com/us/los-angeles'));

	// 获取页面住宿列表
	//var_dump($crawler->getRoomList('https://cn.student.com/us/los-angeles?page_number=1'));
	
	// 获取公寓详细信息
	 // var_dump($crawler->getRoomInfo('https://cn.student.com/us/new-york-city/p/532-east-83rd-street'));
	 // die();
