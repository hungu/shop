<?php
/****
file:	catelist.php
作用:	获得栏目列表,初始化模板页面
****/



	/*define('ACC_KEY', true);
	require('../include/init.php');*/

	include_once('include.php');
	//实例化栏目model
	$category_model = new Category_model();
	//获得栏目的子孙树
	$cate_list = $category_model->get_son_tree();
	//引入栏目列表的模版
	include(ROOT . 'view/admin/templates/catelist.html');
?>
