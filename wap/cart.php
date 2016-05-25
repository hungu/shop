<?php 
	
	
	
	define('ACC_KEY', true);
	require('../include/init.php');


	//判断是否登陆 || 微信端用户应直接登陆,为防止违规操作,需要进行判断
	if(!isset($_SESSION['wap_name']) || empty($_SESSION['wap_name'])) {
		$cart = new Cart();
	} else {
		$cart = new Cart_model();
	}

	//获得加入购物车商品的信息
	if(isset($_GET['goods_id']) && !empty($_GET['goods_id'])) {
		//防止恶意添加
		$last_url = $_SERVER['HTTP_REFERER'];
		$check = 'http://' . $_SERVER['SERVER_NAME'] . '/wap/good_info.php?goods_id=' . $_GET['goods_id'];
		if($last_url != $check) {
			header("Location: index.php");
		}

		$goods = new Goods_model();
		$good_info = $goods->find($_GET['goods_id']);

		//将商品添加到购物车 || flag 作用防止单页刷新,重复加入购物车
		if(!empty($_SESSION['flag']) && $_SESSION['flag'] == $_GET['goods_id']) {
			header("Location: cart.php");
		} else if ($cart->add_item($good_info['goods_id'], $good_info['goods_name'], $good_info['shop_price'], $good_info['thumb_img'], 1)) {
			$_SESSION['flag'] = $_GET['goods_id'];
			$_SESSION['msg'] = '添加成功';
		}
	}
	$_SESSION['in_cart'] = 1;
	
	//购物车所有商品
	$cart_list = $cart->get_all();
	
	include('../view/wap/cart.html');

 ?> 

