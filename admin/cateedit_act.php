<?php
/****
file:catdit_act.php
作用:接受cateeidt.php表单发送来的数据,并调用model,把数据入库
****/



	/*define('ACC_KEY', true);
	require('../include/init.php');*/

	include_once('include.php');
	$data = array();
	//判断数据是否完整
	if(empty($_POST['cate_name']) || empty($_POST['cate_intro']) || !isset($_POST['parent_id'])) {
		exit('数据不完整..');
	}
	
	$data['cate_name'] = $_POST['cate_name'];
	$data['parent_id'] = $_POST['parent_id'] + 0;
	$data['cate_intro'] = $_POST['cate_intro'];
	$data['cate_id'] = $_POST['cate_id'] + 0;

	//实例化栏目model
	$category_model = new Category_model();

	//查看新父栏目的家谱树中是否包含要修改的栏目
	$tree = $category_model->get_father_tree($data['parent_id']);
	foreach ($tree as $v) {
		if($v['cate_id'] == $data['cate_id']) {
			echo '父栏目选取错误!';
			exit;
		}
	}
	
	//将数据写入数据库中
	if($category_model->update($data, $data['cate_id'])) {
		echo '更新成功';
	} else {
		echo '更新失败';
	}

