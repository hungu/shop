<?php 
	
	define('ACC_KEY', true);
	require('include/init.php');

	$goods_model = new Goods_model();
	$new_list = $goods_model->select('*', 'is_new = 1 AND is_delete = 0 ORDER BY add_time DESC LIMIT 0, 5');
	include(ROOT . 'view/front/index.html');
	
 ?>