<?php

/*
file:	cateadd_act.php
作用:	处理cateadd.php表单页面数据,调用model,将数据入库
*/


	/*define('ACC_KEY', true);
	require('../include/init.php');
*/

	include_once('include.php');
	
	$data = array();
	//判断提交的数据是否完整
	if(empty($_POST['cate_name']) || empty($_POST['cate_intro']) || !isset($_POST['parent_id'])) {
		exit('数据不完整..');
	}

	$data['cate_name'] = $_POST['cate_name'];
	$data['parent_id'] = $_POST['parent_id'];
	$data['cate_intro'] = $_POST['cate_intro'];
	//实例化 model
	$category_model = new Category_model();
	if($category_model->add($data)) {
		echo '栏目添加成功!';
		exit;
	} else {
		echo '栏目添加失败!';
		exit;
	}

	?>