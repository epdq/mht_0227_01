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

	foreach ($cityList as $key => $city) {
		# code...
		$cityId = $mysql->insert('city', $city);	// 插入数据库的城市ID

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
				$data['AttrStr'] = implode(',', $roomInfo['Facility']);	// 公寓设备
				$data['Notice'] = $roomInfo['Notice'];	// 公寓预订须知
				$data['AddTime'] = time();
				$data['RoomNo'] = $roomInfo['RoomNo'];	// 51room网站公寓编号
				$data['RoomDevice'] = $roomInfo['RoomDevice'];	// 51room网站右上角公寓设备

				# code...
				$roomId = $mysql->insert('room51room', $data);	// 公寓信息插入数据库,返回公寓ID


				// 根据公寓ID插入公寓图片
				$sql = 'INSERT INTO roompic(ApartmentId, picPath) VALUES ';
				foreach ($roomInfo['ThumbSmall'] as $pic) {
					$sql = $sql . '(' . $roomId . ', \'' . $pic . '\'),';
				}
				$sql = substr($sql, 0, -1);
				$mysql->query($sql);


				die();
			}

		}


	}