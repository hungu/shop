<?php 
	
	defined('ACC_KEY')||exit('Access Invalid!');
	class User_model extends Model {
		public function __construct() {
			parent::__construct('user', 'user_id');
		}

		//检查是否免登陆
		/*
		@param 	string 	$date 	明文cookie
		@return bool
		 */
		public function check_save_logined($data) {
			list($user_id, $logined_token, $logined_timeout) = explode(':', $data);
			if(!$row = $this->find($user_id, 'logined_token, logined_timeout, username')) {
				return false;
			}
			if($logined_token == $row['logined_token'] && $logined_timeout == $row['logined_timeout']) {
				if($logined_timeout >= time()) {
					return $user_id . ':' . $row['username'];
				}
				return false;
			}
			return false;
		}

		//设置免登陆
		/*
		@param 	int 	$user_id	用户id
		@param 	int 	$timeout 	用户免登陆时间(天)
		return 	string 	免登陆cookie值(明文)
		 */
		public function save_logined($user_id, $timeout = 7) {
			$logined_token = md5(uniqid(rand(), TRUE));
			$logined_timeout = time() + (86400 * $timeout);
			$data = array(
					'logined_token' => $logined_token,
					'logined_timeout' => $logined_timeout,
				);
			if(!$this->update($data, $user_id)) {
				return false;
			}
			return $user_id . ':' . $logined_token . ':' . $logined_timeout;
		}

		//验证登陆
		public function check_user($username, $passwd, $is_adm = '') {
			$is_adm = '';
			if($is_adm) $is_adm = ' AND is_adm = 1 ';
			return $this->db->get_one($this->table, 'user_id', 'username = "' . $username . '" AND passwd = "' . $passwd . '"' . $is_adm . ' AND locked = 0');
		}

		//获得用户的openid
		public function get_openid($user_id) {
			return $this->db->get_one($this->table, 'openid', 'user_id = ' . $user_id);
		}

		//重置用户密码
		public function reset_pass($user_id, $passwd) {
			$data = array('passwd' => $passwd);
			return $this->update($data, $user_id);
		}

		//根据唯一key 进行查找, 默认查找user_id
		public function get_id($k, $v, $field = 'user_id') {
			return $this->db->get_one($this->table, $field, $k . ' = "' . $v . '"');
		}

		/*//根据唯一key,判断用户是否锁定
		public function is_lock($k, ) {
			$lock = $this->db->get_one($this->table, 'locked', )
		}*/
	}
 ?>