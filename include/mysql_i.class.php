<?php 
	/*
		file:mysql_i.class.php
		作用:基于mysqli类的扩展类
		date:2016-04-20
		
	*/
	//防止非法查看
	defined('ACC_KEY')||exit('Access Invalid!');
	class Mysql_i extends Db {
		private static $ins = NULL;
		private $conn = NULL;
		private $conf = NULL;

		//初始化数据库连接
		private function __construct() {
			$this->conf = Conf::get_ins();

			if($this->connect($this->conf->db_host, $this->conf->db_user, $this->conf->db_password)) {
				$this->conn->select_db($this->conf->db_name);
				$this->query('SET names ' . $this->conf->db_char);
			}
		}
		
		//自动释放数据库资源
		public function __destruct() {
			$this->conn->close();
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


		/*
			连接数据库 
			@param string $db_host	数据库地址
			@param string $db_user	数据库用户名
			$param string $db_password	数据库密码
			@return object
		*/  
		public function connect($db_host, $db_user, $db_password) {
			$this->conn = new mysqli($db_host, $db_user, $db_password);
			if(mysqli_connect_errno()) {
				Log::write('数据库连接失败: ' . mysqli_connect_error());
				return false;
			}
			return true;
		}

		/*
			执行sql语句
			@param string $sql	sql语句
			@return 资源
		*/ 
		public function query($sql) {
			//判断sql 语句合法性
			if(empty($sql) || (!is_string($sql))) {
				Log::write('SQL 语句错误:' . $sql . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			//判断是否存在非法操作
			if((stripos($sql, 'drop') !== false) || ((stripos($sql, 'delete') !== false) && (stripos($sql, 'where') === false))) {
				Log::write('非法操作:' . $sql . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			//调试模式记录执行日志
			if(DEBUG) {
				Log::write($sql);
			}
			//执行
			$rs = $this->conn->query($sql);
			if(!$rs) {
				Log::write('数据库执行错误:' . $sql . ' ; ' . $this->conn->error . ' 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
			}
			return $rs;
		}

		/*
			自动拼接执行
			@param string $table	表名
			@param array $data	数据
			$param string $act	执行的动作(insert||update)
			$param string $where 位置(update)
			@return bool
		*/  
		public function auto_execute($table, $data, $act='insert', $where='') {
			if((!is_array($data))) {
				Log::write('表名或数据错误' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			//防止非法操作
			if(empty($where) && $act == 'update') {
				Log::write('update 操作需指定where' . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
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
		
		/*
			返回查询的全部结果
			@param string $table	表名||sql语句
			@param array||string $field	字段名
			$param string $where 位置(update)
			@return array
		*/ 
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
			while($row = $rs->fetch_assoc()) {
				$list[] = $row;
			}
			$rs->close();
			return $list;
		}	
		
		
		/*
			返回查询的单条结果
			@param string $table	表名||sql语句
			@param array||string $field	字段名
			$param string $where 位置(update)
			@return array
		*/ 
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
			$row = $rs->fetch_assoc();
			$rs->close();
			return $row;
		}
		
		/*
			返回查询的全部结果
			@param string $table	表名||sql语句
			@param string $field	字段名
			$param string $where 位置(update)
			@return string
		*/ 
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
			$row = $rs->fetch_row();
			$rs->close();
			return $row[0];
		}

		/*
		删除数据操作
		@param string 
		 */
		
		public function delete($table, $where) {
			if(empty($where)) {
				Log::write('删除操作,where 不存在:' . $sql . ' ; 文件: ' .__FILE__ . ' ; 类名: ' .__CLASS__. ' ; 方法名: ' .__FUNCTION__. ' ; 行数: ' .__LINE__);
				return false;
			}
			$sql = "DELETE FROM {$table} WHERE {$where}";
			return $this->query($sql);
		}
		
		//返回影响的行数
		public function affected_rows() {
			return $this->conn->affected_rows;
		}
		
		//返回最后插入的id
		public function insert_id() {
			return $this->conn->insert_id;
		}
		
	}
 ?>