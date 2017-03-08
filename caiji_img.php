<?php

	include_once 'class/Mysql.class.php';

	$dbhost = '127.0.0.1';
	$dbuser = 'root';
	$dbpwd = 'root';
	$dbname = 'student';

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
		if (PHP_VERSION > '5.1') {
		    date_default_timezone_set('Asia/Shanghai');
		}

		$mysql = new MySQL($dbhost, $dbuser, $dbpwd, $dbname);

		$images = $mysql->getall('SELECT ImagesID, SourceUrl FROM house_image WHERE Status = 0 LIMIT 100');

		if (!empty($images)) {
			foreach ($images as $key => $img) {
				$imageUrl = 'Uploads/House/' . date('YmdHis') . rand(1000, 9999) . '.jpg';
				
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
