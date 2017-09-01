<?php 


    include_once 'class/Mysql.class.php';
    include_once 'class/CrawlerStudent.php';
    global $dbhost, $dbuser, $dbpwd, $dbname;

    $dbhost = '127.0.0.1';
    $dbuser = 'root';
    $dbpwd  = 'root';
    $dbname = 'student';

    set_time_limit(0);

    if(gather_house()){
        echo "<script>setTimeout(function (){location.reload();}, 3000);</script>";    // 刷新页面
    }else{
        echo "<script>window.close();</script>";
    }



    /**
     * 采集student公寓信息
     * @Author   Cai
     * @DateTime 2017-03-08
     * @return   boolean     采集成功true,无采集内容false
     */
    function gather_house()
    {

        global $dbhost, $dbuser, $dbpwd, $dbname;
        $mysql   = new MySQL($dbhost, $dbuser, $dbpwd, $dbname);
        $crawler = new CrawlerStudent(); // student.com 采集类



        $arrFacility = []; // 设施数组
        $arr         = $mysql->getAll('SELECT FacilitiesID, FacilitiesName FROM house_facilities');
        $arrFacility = array_column($arr, 'FacilitiesName', 'FacilitiesID');

        // $arrContainFacility = []; // 房租包含数组
        // $arr                = $mysql->getAll('SELECT ContainID, ContainName FROM house_contain');
        // $arrContainFacility = array_column($arr, 'ContainName', 'ContainID');

        $arrSecurityFacility = []; // 安全保障数组
        $arr                 = $mysql->getAll('SELECT SecurityFacilitiesID, SecurityFacilitiesName FROM house_security_facilities');
        $arrSecurityFacility = array_column($arr, 'SecurityFacilitiesName', 'SecurityFacilitiesID');





        $schoolInfo = $mysql->getone('SELECT SchoolID, AreaID, URL FROM house_school WHERE Status = 0 ORDER BY SchoolID ASC');
        if ($schoolInfo) {
            
            // 采集前5页数据
            for ($page = 1; $page < 6; $page++) { 
                $houseLists = [];
                $houseLists = $crawler->getRoomList($schoolInfo['URL'] . '?page_number=' . $page);
                foreach ($houseLists as $index => $room) {

                    // 获取公寓详情
                    $roomUrl = $room['url'];

                    $GatherInfo = $mysql->getone('SELECT GatherID, SchoolID, ApartmentID FROM house_gather WHERE GatherUrl = \'' . $roomUrl . '\'');

                    // 未采集的开始采集
                    if (!$GatherInfo) {
                        # code...

                        $roomInfo = $crawler->getRoomInfo($roomUrl);

                        if ($roomInfo != false) {

                            $data                  = [];
                            $data['ApartmentName'] = $roomInfo['ApartmentName']; // 公寓名
                            $data['Introduce']     = $roomInfo['Introduce']; // 公寓详情
                            $data['AreaID']        = $schoolInfo['AreaID']; // 所在城市ID
                            $data['SchoolID']      = $schoolInfo['SchoolID']; // 所在学校ID
                            $data['Address']       = $roomInfo['Address']; // 公寓地址
                            $data['Price']         = isset($roomInfo['Price']) ? $roomInfo['Price'] : 0.00; // 公寓价格
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
                                    $facilityId = array_search($facility, $arrFacility);
                                    if ($facilityId == false) {
                                        $facilityId                      = $mysql->insert('house_facilities', array('FacilitiesName' => $facility));
                                        $arrFacility[$facilityId] = $facility;
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
                                        $facilityId                       = $mysql->insert('house_security_facilities', array('SecurityFacilitiesName' => $facility));
                                        $arrSecurityFacility[$facilityId] = $facility;
                                    }
                                    $arr[] = $facilityId;
                                }
                                $data['SecurityFacilities'] = implode(',', $arr); // 公寓设施字符串
                            }


                            // 插入不同的房型
                            if (isset($roomInfo['layout'])) {
                                # code...
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

                                    // 插入已经采集过的URL
                                    $mysql->insert('house_gather', ['GatherUrl' => $roomUrl, 'SchoolID' => $schoolInfo['SchoolID'], 'ApartmentID' => $roomId]);
                                    echo "Save...SchoolID:{$schoolInfo['SchoolID']} Page:{$page} Index:{$index}<br/>";

                                }
                            }

                        }

                    }else{
                        // 已采集过
                        echo "SchoolID:{$schoolInfo['SchoolID']} Page:{$page} Index:{$index} GatherID:{$GatherInfo['GatherID']}<br/>";
                    }

                }
            }

            // 前5页采集完更新学校列表状态
            $sql = 'UPDATE house_school SET Status = 1 WHERE SchoolID = ' . $schoolInfo['SchoolID'];
            $mysql->query($sql);
            return true;
        }else{
            // 学校列表全部采集完成，Status直0等待下次采集
            $sql = 'UPDATE house_school SET Status = 0';
            $mysql->query($sql);
            echo "All the school list collection ";
            return false;
        }


    }