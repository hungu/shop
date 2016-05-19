<?php 
	
	defined('ACC_KEY')||exit('Access Invalid!');
	
	class Black_list_model extends Model {

		public function __construct() {
			parent::__construct('black_list', 'black_id');
		}

		//检查用户是否在黑名单中
		public function check($openid) {
			return $this->select('openid', 'openid = "' . $openid . '"');
		}

		/*//添加用户到黑名单中
		public function */
	}
 ?>