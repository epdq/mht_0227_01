<?php

if ($argv[1] != 'start') {
    die('end');
}

include_once 'lib/Mysql.class.php';
include_once 'lib/Crawler_student.php';

$dbhost = '127.0.0.1';
$dbuser = 'root';
$dbpwd  = 'root';
$dbname = 'student';

set_time_limit(0);

$mysql   = new MySQL($dbhost, $dbuser, $dbpwd, $dbname);
$crawler = new Crawler_student(); // student.com 采集类

// =======================================
// 采集插入城市列表
// $cityList = $crawler->getCityList(false);    // 学校列表
// $mysql->insertAll('house_area', $cityList);
// echo "城市采集完成";
// exit();
// =========================================

// =========================================
//  采集插入学校列表

// $arrCity = [];    // 城市数组
// $arr = $mysql->getAll('SELECT AreaID, AreaCnName FROM house_area;');
// $arrCity = array_column($arr, 'AreaCnName', 'AreaID');

// $schoolList = $crawler->getSchoolList();    // 学校列表

// foreach ($schoolList as $key => $school) {
//     # code...
//     $arrSchool = [];
//     $city = $crawler->getSchoolArea($school['URL']);
//     $school['AreaID'] = (int)array_search($city['AreaCnName'], $arrCity);
//     //var_dump($school);
//     $mysql->insert('house_school', $school);
//     echo "insert..." . rand(1000, 9999) . "\r\n";
// }

// echo "学校采集完成";
// exit();
// =======================================

// 读取上次采集位置
$breakpoint = file_get_contents('breakpoint.txt');
if (!$breakpoint) {
    $breakpoint = [
        "schoolIndex" => -1,
        "pageIndex"   => -1,
        "listIndex"   => -1,
    ];
} else {
    $breakpoint = json_decode($breakpoint, true);
}

// 采集公寓信息

$arrFacility = []; // 设施数组
$arr         = $mysql->getAll('SELECT FacilitiesID, FacilitiesName FROM house_facilities');
$arrFacility = array_column($arr, 'FacilitiesName', 'FacilitiesID');

$arrContainFacility = []; // 房租包含数组
$arr                = $mysql->getAll('SELECT ContainID, ContainName FROM house_contain');
$arrContainFacility = array_column($arr, 'ContainName', 'ContainID');

$arrSecurityFacility = []; // 安全保障数组
$arr                 = $mysql->getAll('SELECT SecurityFacilitiesID, TagName FROM house_security_facilities');
$arrSecurityFacility = array_column($arr, 'TagName', 'SecurityFacilitiesID');

$arrCity = []; // 城市数组
$arr     = $mysql->getAll('SELECT AreaID, AreaCnName FROM house_area;');
$arrCity = array_column($arr, 'AreaCnName', 'AreaID');

$arrSchool = []; // 学校数组
$arr       = $mysql->getAll('SELECT SchoolID, SchoolCnName FROM house_school;');
$arrSchool = array_column($arr, 'SchoolCnName', 'SchoolID');

$schoolList = $crawler->getSchoolList(); // 学校列表

foreach ($schoolList as $schoolIndex => $school) {
    # code...

    // 已经读取城市跳过
    if ((int) $schoolIndex <= (int) $breakpoint['schoolIndex']) {
        continue;
    }

    $schoolUrl = $school['URL']; // 城市url

    $areaInfo = $crawler->getSchoolArea($schoolUrl); // 城市名称

    $areaId = array_search($areaInfo['AreaCnName'], $arrCity); // 城市ID
    if ($areaId == false) {
        $areaId           = $mysql->insert('house_area', $areaInfo); // 插入数据库的城市ID
        $arrCity[$areaId] = $areaInfo['AreaCnName'];
        file_put_contents('area.log', $areaInfo['AreaCnName'] . '\r\n', FILE_APPEND);
    }
    $school['AreaID'] = $areaId;

    # 判断学校是否已经存在并获取学校ID
    $schoolId = array_search($school['SchoolCnName'], $arrSchool); // 城市ID

    if ($schoolId == false) {
        $schoolId             = $mysql->insert('house_school', $school); // 插入数据库的学校ID
        $arrSchool[$schoolId] = $school['SchoolCnName'];

    }

    $page = $crawler->getCityPage($schoolUrl); // 当前城市分页数目

    // 循环城市分页列表
    for ($pageIndex = 1; $pageIndex <= $page; $pageIndex++) {

        // 已经读取页数跳过
        if ((int) $pageIndex <= (int) $breakpoint['pageIndex']) {
            continue;
        }

        $pageUrl  = $schoolUrl . '?page_number=' . $pageIndex;
        $roomList = $crawler->getRoomList($pageUrl);

        // 循环公寓列表
        foreach ($roomList as $listIndex => $room) {

            // 已经读取列表跳过
            if ((int) $listIndex <= (int) $breakpoint['listIndex']) {
                continue;
            }

            // 获取公寓详情
            $roomUrl  = $room['url'];
            $roomInfo = $crawler->getRoomInfo($roomUrl);

            if ($roomInfo != false) {

                $data                  = [];
                $data['ApartmentName'] = $roomInfo['ApartmentName']; // 公寓名
                $data['Introduce']     = $roomInfo['Introduce']; // 公寓详情
                $data['AreaID']        = $areaId; // 所在城市ID
                $data['SchoolID']      = $schoolId; // 所在学校ID
                $data['Address']       = $roomInfo['Address']; // 公寓地址
                $data['Price']         = $roomInfo['Price']; // 公寓价格
                $data['MinLease']      = isset($roomInfo['MinLease']) ? $roomInfo['MinLease'] : 0;
                if (isset($roomInfo['MinLease'])) {
                    $data['Longitude'] = $roomInfo['Longitude']; // 经度
                    $data['Latitude']  = $roomInfo['Latitude']; // 纬度
                }

                // 公寓设施处理
                if (isset($roomInfo['Facilites'])) {
                    $data['AttrStr'] = implode(',', $roomInfo['Facilites']); // 公寓设备
                    $arr             = [];
                    foreach ($roomInfo['Facilites'] as $key => $facility) {
                        $facilityId = array_search($facility, $arrFacility);
                        if ($facilityId == false) {
                            $facilityId               = $mysql->insert('house_facilities', array('FacilitiesName' => $facility));
                            $arrFacility[$facilityId] = $facility;
                        }
                        $arr[] = $facilityId;
                    }
                    $data['Facilities'] = implode(',', $arr); // 公寓设施字符串
                }

                // 房租包含
                if (isset($roomInfo['ContainFacilities'])) {
                    $data['ContainStr'] = implode(',', $roomInfo['ContainFacilities']); // 公寓设备
                    $arr                = [];
                    foreach ($roomInfo['ContainFacilities'] as $key => $facility) {
                        $facilityId = array_search($facility, $arrContainFacility);
                        if ($facilityId == false) {
                            $facilityId                      = $mysql->insert('house_contain', array('ContainName' => $facility));
                            $arrContainFacility[$facilityId] = $facility;
                        }
                        $arr[] = $facilityId;
                    }
                    $data['ContainFacilities'] = implode(',', $arr); // 公寓设施字符串
                }

                // 安全保障
                if (isset($roomInfo['SecurityFacilities'])) {
                    $data['SecurityStr'] = implode(',', $roomInfo['SecurityFacilities']); // 公寓设备
                    $arr                 = [];
                    foreach ($roomInfo['SecurityFacilities'] as $key => $facility) {
                        $facilityId = array_search($facility, $arrSecurityFacility);
                        if ($facilityId == false) {
                            $facilityId                       = $mysql->insert('house_security_facilities', array('TagName' => $facility));
                            $arrSecurityFacility[$facilityId] = $facility;
                        }
                        $arr[] = $facilityId;
                    }
                    $data['SecurityFacilities'] = implode(',', $arr); // 公寓设施字符串
                }

                foreach ($roomInfo['layout'] as $k => $layout) {
                    # code...
                    $data['AddTime'] = time();
                    $data['layout']  = $layout;
                    # code...
                    $roomId = $mysql->insert('house_apartment', $data); // 公寓信息插入数据库,返回公寓ID

                    // 根据公寓ID插入公寓图片
                    $sql    = 'INSERT INTO house_image(ApartmentId, SourceUrl) VALUES ';
                    $values = [];
                    foreach ($roomInfo['Images'] as $img) {
                        $values[] = '(' . $roomId . ', \'' . $img . '\')';
                    }
                    $sql .= implode(',', $values);
                    $mysql->query($sql);

                }

            }

            // 记录已读取列表序号
            $breakpoint['listIndex'] = $listIndex;
            file_put_contents('breakpoint.txt', json_encode($breakpoint));

            echo "running...$roomUrl\r\n";
            //die();
        }

        // 记录已读取页数序号
        $breakpoint['listIndex'] = -1;
        $breakpoint['pageIndex'] = $pageIndex;
        file_put_contents('breakpoint.txt', json_encode($breakpoint));

    }

    // 记录已读取城市序号
    $breakpoint['pageIndex']   = -1;
    $breakpoint['schoolIndex'] = $schoolIndex;
    file_put_contents('breakpoint.txt', json_encode($breakpoint));

}

echo "end";
