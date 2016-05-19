<?php 
	
	define('ACC_KEY', true);
	require('../include/init.php');

	$goods = new Goods_model();
	//获取前20的新品
	$goods_list = $goods->select('*', 'is_delete = 0 AND is_new = 1 ORDER BY add_time DESC LIMIT 0, 20');
	$cate = new Category_model();
	//获取所有顶级栏目
	$cate_list = $cate->select('*', 'parent_id = 0');

	include('../view/wap/index.html')
 ?>