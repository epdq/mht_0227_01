<?php

	include_once 'class/Mysql.class.php';
    global $dbhost, $dbuser, $dbpwd, $dbname;
	// $dbhost = '127.0.0.1';
	// $dbuser = 'root';
	// $dbpwd = 'root';
	// $dbname = 'student';

	$dbhost = '121.40.209.129';
	$dbuser = '57ustest';
	$dbpwd = 'test57us';
	$dbname = 'db_www_57us';
	
	set_time_limit(0);



    if(GatherIMG()){
        echo "<script>setTimeout(function (){location.reload();}, 3000);</script>";    // 刷新页面
    }else{
        echo "<script>window.close();</script>";
    }



    /**
     * 采集图片到本地
     * @Author   Cai
     * @DateTime 2017-03-08
     * @return   boolean     采集成功true,无采集内容false
     */
	function GatherIMG()
	{
	    global $dbhost, $dbuser, $dbpwd, $dbname;
		if (PHP_VERSION > '5.1') {
		    date_default_timezone_set('Asia/Shanghai');
		}

		$mysql = new MySQL($dbhost, $dbuser, $dbpwd, $dbname);

		$images = $mysql->getall('SELECT ImagesID, SourceUrl FROM house_image WHERE Status = 0 LIMIT 1');
		$Year = date('Y');
		@mkdir('up/' . $Year);
		$MonthDay = date('md');
		@mkdir('up/'.$Year.'/'. $MonthDay);
		$Path = rand(100, 500);
		@mkdir('up/'.$Year.'/' . $MonthDay . '/' . $Path);
		if (!empty($images)) {
			foreach ($images as $key => $img) {

				$imageUrl = 'up/' . $Year . '/' . $MonthDay . '/' . $Path . '/' . date('YmdHis') . rand(1000, 9999) . '.jpg';
				
				if(copy($img['SourceUrl'], $imageUrl)){
					$sql = 'UPDATE house_image SET Status = 1, ImageUrl = \'/' . $imageUrl . '\' WHERE ImagesID = ' . $img['ImagesID'];
					$mysql->query($sql);
				}
			}
			return true;
		}else{
			return false;
		}

	}
