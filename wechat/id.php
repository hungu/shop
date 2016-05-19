<?php 
	
	define('ACC_KEY', true);
	require_once('../include/init.php');

	if(isset($_GET['code']) && !empty($_GET['code'])) {
		$xml = simplexml_load_file(ROOT . 'data/other/4297f44b13955235245b2497399d7a93.xml');
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$xml->appid.
					'&secret='.$xml->appsecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
		$get_request = new request();
		$access_token_array = json_decode($get_request->http_request_GET($url), true);
		$openid = $access_token_array['openid'];
		$user = new User_model();
		if($user_id = $user->get_id('openid', $openid)) {			//判断用户是否已注册
			$user_info = $user->find($user_id);
			if($user_info['locked']) {								//锁定账户不允许登陆
				echo '该账户已被锁定';
				exit;
			}
			$_SESSION['user_id'] = $user_info['user_id'];
			$_SESSION['openid'] = $user_info['openid'];
			/*
			判断是否为管理员
			管理员帐号不可作为普通账号登陆
			 */
			if($user_info['is_admin']) {
				$_SESSION['adm_wap_name'] = $user_info['username'];
				header("Location: ../wap/adm_index.php");
				exit;
			}
			$_SESSION['wap_name'] = $user_info['username'];				//移动到if上面,管理员可登陆后也可作为普通用户
			header("Location: ../wap/index.php");
			exit;
		} else {
			include('deal.php');
			if(register_deal($openid)) {
				//未注册的用户不考虑存在是管理员的可能
				$user = new User_model();
				$user_id = $user->get_id('openid', $openid);
				$user_info = $user->find($user_id);
				$_SESSION['user_id'] = $user_info['user_id'];
				$_SESSION['openid'] = $user_info['openid'];
				$_SESSION['wap_name'] = $user_info['username'];
				header("Location: ../wap/index.php");
				exit;
			}
		}
	}
 ?>