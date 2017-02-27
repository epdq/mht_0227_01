<?php
	class Mysql{
		public function init($server, $user, $pwd, $db)
		{
			$con = mysql_connect($server, $user, $pwd);
			if (!$con) {
				die('Connect MySQL error:' . mysql_error());
			}

			if (!mysql_select_db($db, $con)) {
				die('Can\'t select db:' . mysql_error());
			}
		}
	}