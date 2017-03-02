<?php

	
	include_once 'lib/Mysql.class.php';
	include_once 'lib/Crawler_51room.php';

	$dbhost = '127.0.0.1';
	$dbuser = 'root';
	$dbpwd = 'root';
	$dbname = 'room';



	// 测试代码段
	// 
	// 
	// 
	// 
	// $crawler = new Crawler_51room();

	// 城市列表获取
	// $citylist = $crawler->getCityList();
	// var_dump($citylist);

	// 城市分页数获取
	// var_dump($crawler->getCityPage('http://www.51room.co.uk/property/rent/us/alameda'));

	// 获取页面住宿列表
	// var_dump($crawler->getRoomList('http://www.51room.co.uk/property/rent/us/alameda/1'));
	
	// 获取公寓详细信息
	// var_dump($crawler->getRoomInfo('http://www.51room.co.uk/property/rent/us/alameda/pid/6149'));
	
	// 获取公寓地图经纬度
	// var_dump($crawler->getRoomMapInfo('http://www.51room.co.uk/property/rent/us/alameda/map?type=property&id=6149'));
	

	// die();


	// 
	// 
	// 
	// 
	// 
	set_time_limit(0);
	
	$mysql = new MySQL($dbhost, $dbuser, $dbpwd, $dbname);
	$crawler = new Crawler_51room();	// 51room.com 采集类

	$arrFacility = [];	// 设施数组
	$arr = $mysql->getAll('SELECT FacilityId, FacilityName FROM roomfacility WHERE 1;');
	$arrFacility = array_column($arr, 'FacilityName', 'FacilityId');

	$arrCity = [];	// 城市数组
	$arr = $mysql->getAll('SELECT CityId, CityName FROM city WHERE 1;');
	$arrCity = array_column($arr, 'CityName', 'CityId');

	$cityList = $crawler->getCityList();	// 城市列表

	foreach ($cityList as $key => $city) {
		# code...
		# 判断城市是否已经存在并获取城市ID
		$cityId = array_search($city['CityName'], $arrCity);	// 城市ID
		if ($cityId == false) {
			$cityId = $mysql->insert('city', $city);	// 插入数据库的城市ID
			$arrCity['CityId'] = $city['CityName'];
		}


		$cityUrl = $city['CityUrl'];	// 城市url
		$page = $crawler->getCityPage($cityUrl);	// 当前城市分页数目

		// 循环城市分页列表
		for ($i=1; $i <= $page; $i++) {
			$pageUrl = $cityUrl . '/' . $i;
			$roomList = $crawler->getRoomList($pageUrl);

			// 循环公寓列表
			foreach ($roomList as $k => $room) {
				
				// 获取公寓详情
				$roomUrl = $room['url'];
				$roomInfo = $crawler->getRoomInfo($roomUrl);

				//var_dump($roomInfo);

				//获取公寓URL
				$mapUrl = $roomInfo['MapUrl'];

				$mapInfo = $crawler->getRoomMapInfo($mapUrl);


				$data = [];
				$data['ApartmentName'] = $roomInfo['ApartmentName'];	// 公寓名
				$data['ApartmentDesc'] = $roomInfo['ApartmentDesc'];	// 公寓详情
				$data['CityId'] = $cityId;	// 所在城市ID
				$data['Addr'] = $roomInfo['Addr'];	// 公寓地址
				$data['Price'] = $roomInfo['Price'];	// 公寓价格
				$data['Longitude'] = $mapInfo['Longitude'];	// 经度
				$data['Latitude'] = $mapInfo['Latitude'];	// 纬度


				// 公寓设施处理
				$data['AttrStr'] = implode(',', $roomInfo['Facility']);	// 公寓设备
				$arr = [];
				foreach ($roomInfo['Facility'] as $key => $facility) {
					$facilityId = array_search($facility, $arrFacility);
					if ($facilityId == false) {
						$facilityId = $mysql->insert('roomfacility', array('FacilityName' => $facility, 'AddTime' => time()));
						$arrFacility[$facilityId] = $facility;
					}
					$arr[] = $facilityId;
				}
				$data['Facility'] = implode(',', $arr);	// 公寓设施字符串




				$data['BedroomNum'] = $roomInfo['BedroomNum'];	// 卧室数量
				$data['BathroomNum'] = $roomInfo['BathroomNum'];	// 卫浴数量
				$data['Notice'] = $roomInfo['Notice'];	// 公寓预订须知
				$data['AddTime'] = time();
				$data['RoomNo'] = $roomInfo['RoomNo'];	// 51room网站公寓编号
				$data['RoomDevice'] = $roomInfo['RoomDevice'];	// 51room网站右上角公寓设备

				# code...
				$roomId = $mysql->insert('room51room', $data);	// 公寓信息插入数据库,返回公寓ID


				// 根据公寓ID插入公寓图片
				$sql = 'INSERT INTO roompic(ApartmentId, picPath) VALUES ';
				$values = [];
				foreach ($roomInfo['ThumbSmall'] as $pic) {
					$values[] = '(' . $roomId . ', \'' . $pic . '\')';
				}
				$sql .= implode(',', $values);
				$mysql->query($sql);


				//die('ok');
				echo "run...\r\n";
			}

		}


	}