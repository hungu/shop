<?php
/*
file:	catedel.php
作用:	更具传入的栏目id删除该栏目
 */


	/*define('ACC_KEY', true);
	require('../include/init.php');*/

	include_once('include.php');
	//防止恶意输入||同事接收要删除的栏目id
	$cate_id = $_GET['cate_id'] + 0;
	//实例化栏目model
	$category_model = new Category_model();
	//判断该栏目下是否存在子栏目
	if($category_model->get_son($cate_id)) {
		echo '该栏目存在子栏目,删除失败!';
		exit;
	}
	//判断操作是否成功
	if($category_model->delete($cate_id)) {
		echo '删除成功';
	} else {
		echo '删除失败';
	}

?>
