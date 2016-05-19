<?php 
	
	define('ACC_KEY', true);
	require('../include/init.php');

	if(!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['passwd']) || empty($_POST['passwd'])) {
		include(ROOT . 'view/admin/templates/login.html');
		exit;
	}	
	
	$user = new User_model();

	if($user_id = $user->check_user($_POST['username'], md5($_POST['passwd']), 1)) {
		$data = array('lastlogin' => time());
		$user->update($data, $user_id);
		$_SESSION['user_id'] = $user_id;
		$_SESSION['adm_name'] = $_POST['username'];
		$_SESSION['openid'] = $user->get_openid($user_id);
		header("Location: index.php");
	} else {
		$_SESSION['msg'] = '登陆失败!';
		include(ROOT . 'view/admin/templates/login.html');
		exit;
	}
 ?>