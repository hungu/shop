<?php 
	/*define('ACC_KEY', true);
	require('../include/init.php');*/

	include_once('include.php');
	
	if(isset($_GET['act']) && $_GET['act'] == 'show') {
		$goods = new goods_model();
		$goodslist = $goods->get_trash();
		include(ROOT . 'view/admin/templates/goodslist.html');
	} else {
		$goods_id = $_GET['goods_id'] + 0;
		$goods = new goods_model();

		if($goods->trash($goods_id)) {
			echo '加入回收站成功!';
		} else {
			echo '加入回收站失败!';
		}
	}
 ?>