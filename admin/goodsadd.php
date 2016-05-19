<?php 
/*
file:	goodadd.php
作用:	添加商品页面
 */

	/*define('ACC_KEY', true);
	require('../include/init.php');*/

	include_once('include.php');
	$category_model = new Category_model();
	//获得所有栏目
	$cate_list = $category_model->select();
	//获得栏目子孙书
	$cate_list = $category_model->get_son_tree(0);

	include(ROOT . 'view/admin/templates/goodsadd.html');
 ?>