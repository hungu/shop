<?php 

	include_once('include.php');

	$user = new User_model();

	if(isset($_GET['act']) && !empty($_GET['act']) && isset($_GET['user_id']) && !empty($_GET['user_id'])) {

		if(($_GET['act'] == 'is_adm' || $_GET['act'] == 'locked') && isset($_GET['set'])) {
			$set = empty($_GET['set'] + 0) ? 1 : 0;
			$data = array($_GET['act'] => $set);
			if($user->update($data, $_GET['user_id'])) {
				$_SESSION['msg'] = '设置成功';
			} else {
				$_SESSION['msg'] = '设置失败';
			}
		} else if($_GET['act'] == 'del') {
			
		} else {
			die('参数错误.');
		}
		
	}
	
	$user_list = $user->select();
	include(ROOT . 'view/admin/templates/userlist.html');

 ?>