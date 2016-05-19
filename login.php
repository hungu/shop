<?php 
	
	define('ACC_KEY', true);
	require('include/init.php');

	//清空cookie
	function del_cookie() {
		foreach ($_COOKIE as $k => $v) {
			setcookie($k, '', time() -1, '/');
		}
	}

	//判断是否存在登陆操作
	if(isset($_POST['submit']) && $_POST['act'] = 'act_login') {

		/*
		添加验证码验证.............
		 */
		$user = new User_model();
		//自动过滤
		$data = $user->_facade($_POST);
		//自动填充
		$data = $user->_auto_fill($data);
		//自动验证
		if(!$user->_validate($data)) {
			echo $user->error['_validate'];
			exit;
		}
		//密码加密验证
		$data['passwd'] = md5($data['passwd']);
		//验证
		if($user_id = $user->check_user($data['username'], $data['passwd'])) {
			if(!empty($_POST['logined'])) {
				//防止用户恶意增加记住登陆时间
				if($_POST['logined'] > $conf->logined_timeout) {
					$logined_timeout = $conf->logined_timeout;
				} else {
					$logined_timeout = $_POST['logined'];
				}
				//设置cookie 保持记录时间
				if($logined_token = $user->save_logined($user_id, $logined_timeout)) {
					setcookie('l_t', encrypt($logined_token, $conf->key), time() + (86400 * $logined_timeout), '/');
				}
			}
			//使用cookie记录用户名
			setcookie('u_n', encrypt($data['username'], $conf->key), time() + (86400 * 365), '/');

			//设置登陆状态的session 信息
			$_SESSION['user_id'] = $user_id;
			$_SESSION['username'] = $data['username'];
			$_SESSION['openid'] = $this->get_openid($user_id);
			//跳转页面
			$back = empty($_POST['back']) ? 'http://shop.zuixinan.top' : $_POST['back'];
			header("Location: {$back}");
			exit;
		} else {
			echo "<script type='text/javascript'>
			 		alert('登陆失败!');
			 	</script>";
		}
	}

	//获取上一页面的地址
	$back = empty($_GET['back']) ? 'http://shop.zuixinan.top' : $_GET['back'];

	//检查记录登陆状态
	if(!empty($_GET['check'])) {
		if(!empty($_COOKIE['l_t'])) {
			$user = new User_model();
			if($user_info = $user->check_save_logined($_COOKIE['l_t'])) {
				list($user_id, $username) = explode(':', $user_info);
				//设置登陆状态的session 信息
				$_SESSION['user_id'] = $user_id;
				$_SESSION['username'] = $username;
				header("Location: {$back}");
			}
		}
	}

	//退出登陆
	if(empty($_GET['out'])) {
		$username = $_SESSION['username'];
		session_unset();
		session_destroy();
		del_cookie();
		setcookie('u_n', encrypt($username), $conf->key), time() + (86400 * 365), '/');
	}

	//引入模版页面
	include(ROOT . 'view/front/denglu.html');

 ?>
