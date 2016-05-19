<?php 
	
	define('ACC_KEY', true);
	require('../include/init.php');

	/*
	用户如果为未登陆创建订单,则使用session记录订单,跳转至登陆页面,进行登陆,完成登陆后进入订单页面
	如果用户直接访问订单页面,则直接跳转至登陆页面,登陆后跳转至主页
	 */
	/*if(empty($_SESSION['back'])) {
		$_SESSION['back'] = NULL;
	}*/

	if(!isset($_SESSION['wap_name']) || empty($_SESSION['wap_name'])) {
		if(isset($_POST['box']) || !empty($_POST['box'])) {
			//用户为未登录将订单列表保存到session中
			$_SESSION['order_tmp'] = $_POST['box'];
		}
		$_SESSION['back'] = 'order.php';			//用户未登陆时且未选中任何商品登陆后可以跳回到ordr页面
		$_SESSION['msg'] = '您尚未登陆,请先登陆';
		header("Location: login.php");
		exit;
	} else {
		//用户已登陆后将之前的订单列表中的数据同步出来
		if(isset($_SESSION['order_tmp']) || !empty($_SESSION['order_tmp'])) {
			$_SESSION['order'] = $_SESSION['order_tmp'];
			$_SESSION['order_tmp'] = NULL;
		}
		/*
		可能会存在session['order_tmp']中有数据,但用户再次提交,故已post提交为准
		 */
		//用户登陆且通过post 提交
		if(isset($_POST['box']) || !empty($_POST['box'])) {
			$_SESSION['order'] = $_POST['box'];	
		}
	}

	//用户已登录直接访问order.php 跳转至主页 || 购物车页面未选中任何商品
	if(!isset($_SESSION['order']) || empty($_SESSION['order'])) {
		if($_SESSION['in_cart']) {
			$_SESSION['msg'] = '您未选择任何商品.';
			$_SESSION['in_cart'] = NULL;
			header("Location: cart.php");
			exit;
		}
		$_SESSION['msg'] = '登陆成功';
		header("Location: index.php");
		exit;
	}

	$cart = new Cart_model();
	$cart_list = $cart_list = $cart->get_all();

	//检查库存
	/*foreach ($_SESSION['order'] as $goods_id) {
		$cart_num = $cart_list['goods_id']['num'];
		$goods_num = $cart->
	}*/

	if(!$cart->num_check($_SESSION['order'])) {
		$_SESSION['msg'] = '库存不足.';
		header("Location: cart.php");
		exit;
	}

	include('../view/wap/order.html');
 ?>