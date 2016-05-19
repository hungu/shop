<?php
/*
file:	cateedit.php
作用:	初始化编辑栏目页面
 */


	/*define('ACC_KEY', true);
	require('../include/init.php');*/

	include_once('include.php');
	//判断参数知否正确
	if(!isset($_GET['cate_id'])) {
		echo '栏目不存在';
		exit;
	}
	//接收要编辑栏目的cate_id||防止恶意输入
	$cate_id = $_GET['cate_id'] + 0;
	//实例化栏目model
	$category_model = new Category_model();
	//查找欲编辑栏目id 的信息
	$cate = $category_model->find($cate_id);
	//查找所有栏目
	$cate_list = $category_model->get_son_tree();


	include(ROOT . 'view/admin/templates/cateedit.html');
?>
