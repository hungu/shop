<?php 
	/*define('ACC_KEY', true);
	require('../include/init.php');*/

	include_once('include.php');

	$goods = new goods_model();
	$goodslist = $goods->get_goods();


	include(ROOT . 'view/admin/templates/goodslist.html');
 ?>