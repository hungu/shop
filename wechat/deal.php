<?php 
	
	define('ACC_KEY', true);
	require_once('../include/init.php');

	function register_deal($openid) {
		/*//判断用户是否在黑名单中
		$black = new Black_list_model();
		if($black->check($openid)) return false;*/

		$user = new User_model();
		$passwd = rand(100000, 999999);
		if($user_id = $user->get_id('openid', $openid)) {
			//该微信号已注册 , 重置密码
			$lock = $user->get_id('openid', $openid, 'locked');
			if($lock === false || $lock == 1) {
				return '用户已被锁定,请联系管理员.';
			}
			if($user->reset_pass($user_id, md5($passwd))) {
				return $passwd;
			} else {
				return false;
			}
		} else {
			$username = $nickname = get_user_nickname($openid);
			if(empty($nickname)) {
				return false;
			}
			//防止用户名相同
			$tmp = '';
			while ($user->get_id('username', $username . $tmp)) {
				$tmp += 1;
			}
			$username .= $tmp;
			$data = array(
					'username' => $username,
					'passwd' => md5($passwd),
					'regtime' => time(),
					'openid' => $openid,
					'nickname' => $nickname
				);
			if($user->add($data)) {
				return $passwd;
			}
			return false;
		}
	}

	function get_user_nickname($openid) {
		$acc = new access_token(ROOT . 'data/other/4297f44b13955235245b2497399d7a93.xml');
		$access_token = $acc;
		$get_request = new request();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
		$nickname_array = json_decode($get_request->http_request_GET($url), true);
		return empty($nickname_array['nickname']) ? false  : $nickname_array['nickname'];
	}

	function get_name($openid) {
		$user = new User_model();
		return $user->get_id('openid', $openid, 'username');
	}

	echo register_deal('oy7R3v45-aq1Q9_CItwz54cF4_rE');
 ?>