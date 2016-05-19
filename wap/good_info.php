<?php 
	

	if(!isset($_GET['goods_id']) || empty($_GET['goods_id'])) {
		header('Location: index.php');
		exit;
	}

	define('ACC_KEY', true);
	require('../include/init.php');

	$goods = new Goods_model();
	if(!$good_info = $goods->find($_GET['goods_id'])) {
		header("Location: index.php");
	}

	/*var_dump($good_info);
	exit;*/


	include('../view/wap/good_info.html')
 ?>