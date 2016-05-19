<?php 
	
	define('ACC_KEY', true);
	require('include/init.php');

	if(!isset($_GET['goods_id']) || empty($_GET['goods_id'])) {
		header("Location: index.php");
		exit;
	}

	$goods_id = $_GET['goods_id'] + 0;
	$goods = new goods_model();
	$g = $goods->find($goods_id);

	include(ROOT . 'view/front/shangpin.html');
	
 ?>