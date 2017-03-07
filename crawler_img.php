<?php

	if ($argv[1] != 'start') {
		die('end');
	}
	
	include_once 'lib/Mysql.class.php';

	$dbhost = '127.0.0.1';
	$dbuser = 'root';
	$dbpwd = 'root';
	$dbname = 'student';

	set_time_limit(0);
	if (PHP_VERSION > '5.1') {
	    date_default_timezone_set('Asia/Shanghai');
	}

	$mysql = new MySQL($dbhost, $dbuser, $dbpwd, $dbname);


	do {
		$images = $mysql->getall('SELECT ImagesID, SourceUrl FROM house_image WHERE Status = 0 LIMIT 100');
		foreach ($images as $key => $img) {
			# code...
			$imageUrl = 'Uploads/House/' . date('YmdHis') . rand(1000, 9999) . '.jpg';
			
			if(copy($img['SourceUrl'], $imageUrl)){
				$sql = 'UPDATE house_image SET Status = 1, ImageUrl = \'/' . $imageUrl . '\' WHERE ImagesID = ' . $img['ImagesID'];
				$mysql->query($sql);
			}
		}
		echo "runing...\r\n" . rand(1000, 9999);
	} while (!empty($images));

	echo "end";