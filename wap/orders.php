<?php 
	
	define('ACC_KEY', true);
	require('../include/init.php');

	//判断用户是否登陆
	if(!isset($_SESSION['wap_name']) || empty($_SESSION['wap_name'])) {
		$_SESSION['back'] = 'orders.php';
		header('login.php');
	}

	//提交订单处理
	$key = array('adder', '$goods_id', '$pay_type');
	if(array_yz($key, $_POST)) {
		$cart = new Cart_model();
		/*
			锁定商品表,检查购买商品数量是否超过库存
			超过则回到购物车
			未超过减少商品数量,将购买商品添加到order_goods表中
			成功后解锁
			生成订单编号等订单信息,同步到订单表中,将订单状态设置为为付款

			进入付款页面

		 */
	}


	include('../view/wap/orders.html');
 ?>