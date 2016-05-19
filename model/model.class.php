<?php 
	/*
	file:model.class.php
	作用:Model类
	 */
	
	defined('ACC_KEY')||exit('Access Invalid!');
	
	class Model {
		protected $table = NULL;
		protected $db = NULL;
		protected $pk = NULL;	//表中的主键

		public $error = array();		//记录错误信息
		protected $fields = array();	//表中的所有字段
		protected $_auto = array();		//自动填充字段和规则

		protected $_valid = array();	//验证规则

		public function __construct($table, $pk) {
			if(!empty($table)) {
				$this->table = $table;
			}
			if(!empty($pk)) {
				$this->pk = $pk;
			}
			$this->db = Mysql_i::get_ins();
		}
		
		//添加
		/*
		@param	array	$data	需插入的数据
		@return bool
		*/
		public function add($data) {
			return $this->db->auto_execute($this->table,$data);
		}
		
		//查找
		/*
		@param	array||string	$field	需要查询的字段
		@param	string			$where	查血字段的位置
		@return array
		*/
		public function select($field = '*', $where = NULL) {
			return $this->db->get_all($this->table, $field, $where);
		}
		
		//更新
		/*
		@param	array	$array	需要更改的数据
		@param	string	$id		需要更改的id(不可为空)
		@return int
		 */
		public function update($data, $id) {
			if(empty($id)) {
				return false;
			}
			if($this->db->auto_execute($this->table, $data, 'update', $this->pk . ' = ' .$id)) {
				return $this->db->affected_rows();
			} else {
				return false;
			}
		}
		
		//根据主键查询一行
		/*
		@param	string	$id		需要更改的id(不可为空)
		@param	string	$field	需要查找的字段	
		@return array
		 */
		public function find($id = NULL, $field = '*') {
			if(empty($id)) {
				return false;
			}
			return $this->db->get_row($this->table, $field, $this->pk . ' = ' . $id);
		}

		//根据主键删除
		/*
		@param 	string 	$id 	需要删除行的id(不可为空)
		@return int
		 */
		public function delete($id) {
			if(empty($id)) {
				return false;
			}
			if($this->db->delete($this->table, $this->pk . ' = ' . $id)) {
				return $this->db->affected_rows();
			} else {
				return false;
			}
		}

		//自动获取字段值
		public function get_field() {
			$sql = 'SHOW FIELDS FROM ' . $this->table;
			$rs = $this->db->query($sql);
			if(!$rs) {
				return false;
			}
			while($row = $rs->fetch_assoc()) {
				$this->fields[] = $row['Field'];
			}
			return true;
		}

		/*
		自动过滤
		@param 	array 	$arr 	待处理的数组
		@return array
		 */
		public function _facade($arr = array()) {
			//判断字段数组是否被赋值
			if(empty($this->fields)) {
				$this->get_field();
			}
			$data = array();
			foreach ($arr as $k => $v) {
				//判断字段是否是表的字段
				if(in_array($k, $this->fields)) {
					$data[$k] = $v;
				}
			}
			return $data;
		}

		//自动填充
		/*
		@param 	array 	$data 	等待处理数组
		@return array
		 */
		/*
		array(
			array('字段名', '类型(value/function), '值'),
		)
		 */
		public function _auto_fill($data) {
			if(empty($this->_auto)) {
				return $data;
			}
			foreach ($this->_auto as $v) {
				if(!array_key_exists($v[0], $data)) {
					switch ($v[1]) {
						case 'value':
							$data[$v[0]] = $v[2];
							break;
						case 'function':
							$data[$v[0]] = call_user_func($v[2]);
							break;
						default:
							break;
					}
				}
			}
			return $data;
		}

		//自动验证
		/*array(
			array('字段名', '检查规则', '错误信息', '检验规则', '参数(可无)'),
		)*/
		/*
		@param 	array 	$data 	需要验证的数据
		@return bool
		 */
		/*
		0:	如果存在则检验
		1:	必检字段
		2:	如果存在且非空则检验
		 */
		public function _validate($data) {
			if(empty($this->_valid)) {
				return true;
			}
			foreach ($this->_valid as $v) {
				switch ($v[1]) {
					case 1:
						if(!isset($data[$v[0]]) || !$this->check($data[$v[0]], $v[3], $v[4])) {
							$this->error[__FUNCTION__] = $v[2];
							return false;
						}
						break;
					case 0:
						if(isset($data[$v[0]])) {
							if(!$this->check($data[$v[0]], $v[3], $v[4])) {
								$this->error[__FUNCTION__] = $v[2];
								return false;
							}
						}
						break;
					case 2:
						if(isset($data[$v[0]]) && !empty($data[$v[0]])) {
							if(!$this->check($data[$v[0]], $v[3], $v[4])) {
								$this->error[__FUNCTION__] = $v[2];
								return false;
							}
						}
						break;
					default:
						return false;
						break;
				}
			}
			return true;
		}

		//检查验证字段
		/*
		@param 	mix 	$value 		需检验的数据
		@param 	string 	$rule 		检验规则
		@param 	mix 	$param 		
		return 	bool
		 */
		/*
		require:	检查是否非空
		number:		检查是否是数字
		in:			检查是否在给定集合中
		between:	检查是否在给定区间中
		length:		检查长度是否在给定区间中
		 */
		protected function check($value, $rule, $param = '') {
			/*echo $rule;
			echo '<br />';*/
			switch ($rule) {
				case 'require':
					return !empty($value);
					break;
				case 'number':
					return is_numeric($value);
					break;
				case 'in':
					$tmp = explode(',', $param);
					return in_array($value, $tmp);
					break;
				case 'between':
					list($min, $max) = explode(',', $param);
					return $value >= $min && $value <= $max;
					break;
				case 'length':
					list($min, $max) = explode(',', $param);
					return strlen($value) >= $min && strlen($value) <= $max;
					break;
				default:
					return false;
					break;
			}
			return true;
		}
	}
 ?>