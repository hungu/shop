<?php 
	
	define('ACC_KEY', true);
	require('../include/init.php');
	
	if(!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['passwd']) || empty($_POST['passwd'])) {
		include('../view/wap/login.html');
		exit;
	}

	$user = new User_model();
	if($user_id = $user->check_user($_POST['username'], md5($_POST['passwd']))) {
		//设置登陆状态的session 信息
		$_SESSION['user_id'] = $user_id;
		$_SESSION['wap_name'] = $_POST['username'];
		$_SESSION['openid'] = $user->get_openid($user_id);

		//同步购物车到数据库中
		$cart = new Cart_model();

		/*$back = $_SERVER["HTTP_REFERER"];*/
		if(isset($_SESSION['back']) && !empty($_SESSION['back'])) {
			$back = $_SESSION['back'];
			$_SESSION['back'] = NULL;
		}
		if(empty($back)) {
			$back = 'index.php';
		}
		header("Location: $back");
	} else {
		$_SESSION['msg'] = '登陆失败!';
		include('../view/wap/login.html');
		exit;
	}


 ?>