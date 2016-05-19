<?php 

	/*
		file:mysql_i.class.php
		作用:基于mysql扩展类
		date:2016-04-20
		
	*/
	defined('ACC_KEY')||exit('Access Invalid!');
	
	class Mysql extends Db {
		private static $ins = NULL;
		private $conn = NULL;
		private $conf = NULL;

		//初始化数据库连接
		private function __construct() {
			$this->conf = Conf::get_ins();

			if($this->connect($this->conf->db_host, $this->conf->db_user, $this->conf->db_password)) {
				mysql_select_db($this->conf->db_name);
				$this->query('SET names ' . $this->conf->db_char);
			}
		}
		
		//自动释放数据库资源
		public function __destruct() {
			mysql_close($this->conn);
		}

		//单例
		public static function get_ins() {
			if(self::$ins instanceof self) {
				return self::$ins;
			} else {
				self::$ins = new self();
				return self::$ins;
			}
		}


		//连接数据库
		public function connect($db_host, $db_user, $db_password) {
			$this->conn = mysql_connect($db_host, $db_user, $db_password);
			if(!$this->conn) {
				Log::write('数据库连接失败' );
				return false;
			}
			return true;
		}

		//执行sql语句
		public function query($sql) {
			//判断sql 语句合法性
			if(is_null($sql) || (!is_string($sql))) {
				Log::write('SQL 语句错误:' . $sql . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			//判断是否存在非法操作
			if((strpos($sql, 'delete') !== false ) || (strpos($sql, 'drop') !== false) ) {
				Log::write('非法操作:' . $sql . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			//调试模式记录执行日志
			if(DEBUG) {
				Log::write($sql);
			}
			//执行
			$rs = mysql_query($sql, $this->conn);
			if(!$rs) {
				Log::write(mysql_error($this->conn));
			}
			return $rs;
		}

		//自动拼接执行
		public function auto_execute($table, $data, $act='insert', $where='') {
			if((!is_array($data)) || (!is_string($table))) {
				Log::write('表名或数据错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			if(!empty($where)) {
				$where = " WHERE {$where}";
			}
			$act = strtolower($act);
			if($act == 'update') {
				$sql = 'UPDATE ' . $table . ' SET ';
				foreach($data as $k => $v) {
					$sql .= $k . "='" . $v . "',";
				}
				$sql = rtrim($sql, ',');
				$sql .= $where;
				return $this->query($sql);
			} else if($act == 'insert') {
				$sql = 'INSERT INTO ' . $table . ' ( ' . implode(',', array_keys($data)) . ' ) ';
				$sql .= "VALUES ( '";
				$sql .= implode("','", array_values($data));
				$sql .= "' )";
				return $this->query($sql);
			}
			Log::write('操作动作参数错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
			return false;
		}
		
		//返回全部结果
		public function get_all($table = NULL, $field = '*', $where = NULL) {
			//获取参数个数
			if(!is_string($table)) {
				Log::write('表名参数错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			$param_num = count(func_get_args());
			if($param_num == 1) {
				$sql = $table;
			} else {
				if(is_array($field)) {
					$field = implode(',', array_values($field));
				} else if(is_string($field)) {
				} else {
					Log::write('字段参数错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
					return false;
				}
				if(!is_string($where)) {
					$where = '';
				} else {
					$where = 'WHERE ' . $where;
				}
				$sql = "SELECT {$field} FROM {$table} {$where}";
			}
			$rs = $this->query($sql);
			if(!$rs) {
				return false;
			}
			$list = array();
			while($row = mysql_fetch_assoc($rs)) {
				$list[] = $row;
			}
			return $list;
		}	
		
		
		//返回单条结果
		public function get_row($table = NULL, $field = '*', $where = NULL) {
			if(!is_string($table)) {
				Log::write('表名参数错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			$param_num = count(func_get_args());
			if($param_num == 1) {
				$sql = $table;
			} else {
				if(is_array($field)) {
					$field = implode(',', array_values($field));
				} else if(is_string($field)) {
				} else {
					Log::write('字段参数错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
					return false;
				}
				if(!is_string($where)) {
					$where = '';
				} else {
					$where = 'WHERE ' . $where;
				}
				$sql = "SELECT {$field} FROM {$table} {$where} LIMIT 1";
			}
			$rs = $this->query($sql);
			if(!$rs) {
				return false;
			}
			return $row = mysql_fetch_assoc($rs);
		}
		
		//返回单个数据
		public function get_one($table = NULL, $field = NULL, $where = NULL) {
			if(!is_string($table)) {
				Log::write('表名参数错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			$param_num = count(func_get_args());
			if($param_num == 1) {
				$sql = $table;
			} else {
				if(!is_string($field)) {
					Log::write('字段参数错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
					return false;
				}
				if(!is_string($where)) {
					$where = '';
				} else {
					$where = 'WHERE ' . $where;
				}
				$sql = "SELECT {$field} FROM {$table} {$where} LIMIT 1";
			}
			$rs = $this->query($sql);
			if(!$rs) {
				return false;
			}
			$row = mysql_fetch_row($rs);
			return $row[0];
		}
		
		//返回影响的行数
		public function affected_rows() {
			return mysql_affected_rows($this->conn);
		}
		
		//返回最后插入的id
		public function insert_id() {
			return mysql_insert_id($this->conn);
		}
		
		
	}
 ?>