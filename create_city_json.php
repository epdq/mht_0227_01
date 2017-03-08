<?php 


include_once 'lib/Mysql.class.php';
global $dbhost, $dbuser, $dbpwd, $dbname;
$dbhost = '127.0.0.1';
$dbuser = 'root';
$dbpwd  = 'root';
$dbname = 'student';

create_city_json();
echo "ok";

/**
 * 读取数据库城市列表并生成json格式数据
 * @Author   Cai
 * @DateTime 2017-03-08
 * @return   [type]     [description]
 */
function create_city_json()
{
	# code...
	global $dbhost, $dbuser, $dbpwd, $dbname;
	$mysql   = new MySQL($dbhost, $dbuser, $dbpwd, $dbname);
	$arrLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

	foreach ($arrLetters as $key => $letter) {
		# code...
		$arrJson = [];
		$sql = 'SELECT AreaCnName FROM house_area WHERE FirstLetter = \'' . $letter . '\'';
		$arrCityLists = $mysql->getall($sql);
		if (empty($arrCityLists)) {
			file_put_contents('Cityes/' . $letter . '.json', 'null');
		}else{
			foreach ($arrCityLists as $k => $city) {
				# code...
				$arrJson[]['name'] = $city['AreaCnName'];
			}
			file_put_contents('Cityes/' . $letter . '.json', (json_encode($arrJson)));
		}

	}
}

 ?>