<?php


	class MySQL{
		var $linkid = null;     		//数据库连接标识
		var $result = null;     		//执行query命令的结果资源标识
		
		/*构造函数*/
		function __construct($dbhost = '127.0.0.1', $dbuser, $dbpw, $dbname, $error='false', $encoding = 'utf8', $conn = ''){
			$this -> connect($dbhost, $dbuser, $dbpw, $dbname, $error, $encoding, $conn);
		}
		
		/*数据库连接*/
		function connect($dbhost, $dbuser, $dbpw, $dbname, $error, $encoding, $conn){
			$this->error = $error;
			$func = empty($conn) ? 'mysql_pconnect' : 'mysql_connect';//创建持久连接还是非持久连接
			if(!$this->linkid = @$func($dbhost, $dbuser, $dbpw, true)){
				$this->dbshow('001');
			} else {
				if($this->dbversion() > '4.1'){
					mysql_query( "SET NAMES ".$encoding);
					if($this->dbversion() > '5.0.1'){
						mysql_query("SET sql_mode = ''",$this->linkid);
					}
				}
			}
			if($dbname){
				if(mysql_select_db($dbname, $this->linkid)===false){
					$this->dbshow("002");
				}
			}
		}
		/*选择数据库*/
		function select_db($dbname){
			return mysql_select_db($dbname, $this->linkid);
		}
		/*查询*/
		function query($sql){
			$result=mysql_query($sql, $this->linkid);
			if($result){
				$this -> result = $result;
			}else{
				$this->dbshow("003");
			}
			return $this -> result;
		}
		/*获取全部记录*/
		function getall($sql, $type=MYSQL_ASSOC){
			$rows = [];
			$this->query($sql);
			if($this -> result){
				while($row = mysql_fetch_array($this -> result,$type)){
					$rows[] = $row;
				}
			}
			return $rows;
		}
		/*获取一条记录*/
		function getone($sql, $type=MYSQL_ASSOC){
			$result = $this->query($sql,$this->linkid);
			$row = mysql_fetch_array($result, $type);
			return $row;
		}

		/*插入数据
		 * @param string $table         表名
		 * @param array  $datas  		数据
		 * @return id                   最后插入生成的ID
		 * */
		function insert($table, $data, $replace = false){
			$datas[] = $data;
	        if($this->insertAll($table, $datas, $replace)){
	        	return $this->insert_id();
	        }else{
	        	return false;
	        }
		}


		/*插入数据
		 * @param string $table         表名
		 * @param array  $datas  		数据数组
		 * @return id                   最后插入生成的ID
		 * */
		function insertAll($table, $datas, $replace = false){
			if(!is_array($datas[0])) return false;
			$fields = array_keys($datas[0]);
			array_walk($fields, array($this, 'parseKey'));
			$values  =  array();
			foreach ($datas as $data){
	            $value   =  array();
	            foreach ($data as $key=>$val){
	                $val   =  $this->parseValue($val);
	                if(is_scalar($val)) { // 过滤非标量数据
	                    $value[]   =  $val;
	                }
	            }
	            $values[] = '(' . implode(',', $value) . ')';
	        }
	        $sql = ($replace ? 'REPLACE':'INSERT') . ' INTO ' . $table . ' (' . implode(',', $fields) . ') VALUES ' . implode(',', $values);
	        return $this->query($sql);
		}
		
		/*返回结果集*/
		function fetch_array(){
			return mysql_fetch_array($this -> result);
		}
		/*返回前一次操作所影响的记录行数*/
		function affected_rows(){
			return mysql_affected_rows($this->linkid);
		}
		/*返回结果集中行的数目(仅对 SELECT 语句有效)*/
		function num_rows(){
			return mysql_num_rows($this->result);
		}
		/*返回结果集中字段的数*/
		function num_fields(){
			return mysql_num_fields($this -> result);
		}
		/*返回上一步 INSERT 操作产生的 ID*/
		function insert_id(){
			return mysql_insert_id($this->linkid);
		}
		/*释放结果内存*/
		function free_result(){
			if (is_scalar($this->result)) {
				return true;
			}
			return mysql_free_result($this->result);
		}
		/*转义 SQL 语句中使用的字符串中的特殊字符*/
		function escape_string($str){
			$str = addslashes($str);
			if($this->linkid){
				if (get_magic_quotes_gpc()==0){
					$str = mysql_real_escape_string($str, $this->linkid);
				}
			}else{
				$str = mysql_escape_string($str);
			}
			return $str;
		}
		/*返回上一个 MySQL 操作产生的文本错误信息*/
		function error(){
			return mysql_error($this->linkid);
		}
		/*返回上一个 MySQL 操作中的错误信息的数字编码*/
		function errno(){
			return mysql_errno($this->linkid);
		}
		/*关闭非持久的 MySQL 连接*/
		function close(){
			return mysql_close($this->linkid);
		}
		/*返回 MySQL 服务器的信息*/
		function dbversion(){
			return mysql_get_server_info($this->linkid);
		}
		/*定义数据库错误显示*/
		function dbshow($err){
			if($this->error=='true'){
				$info = "Errno：" . $this->errno() . " -> Error：" . $this->error();
				exit($info);
			}else{
				exit("MySQL ERROR.<br>错误码：". $err);
			}
		}
		/*定义防止注入方法(检查数据)*/
		function checksql($str){
			$tmp = preg_match('/select|insert|update|delete|union|into|load_file|outfile/', $str);//字符串比对解析，与大小写无关
			if($tmp){
				$this->dbshow("004");
			}else{
				return $str;
			}
		}
		/*	字段和表名处理添加`
		 * @access protected
		 * @param string $key
		 * @return string
		 * */
	    protected function parseKey(&$key) {
	        $key   =  trim($key);
	        if(!preg_match('/[,\'\"\*\(\)`.\s]/', $key)) {
	           $key = '`' . $key . '`';
	        }
	        return $key;
	    }
	    /*	数据value分析
		 * @access protected
		 * @param mixed $value
		 * @return string
		 * */
	    protected function parseValue($value) {
	        if(is_string($value)) {
	            $value =  '\'' . $this->escape_string($value) . '\'';
	        }elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
	            $value =  $this->escape_string($value[1]);
	        }elseif(is_array($value)) {
	            $value =  array_map(array($this, 'parseValue'), $value);
	        }elseif(is_bool($value)){
	            $value =  $value ? '1' : '0';
	        }elseif(is_null($value)){
	            $value =  'null';
	        }
	        return $value;
	    }
		//析构函数，自动关闭数据库,垃圾回收机制
		function __destruct() {
	        if (!empty($this->result)) {
	            $this->free_result();
	        }
	        //mysql_close($this->linkid);
	    }
	}


?>