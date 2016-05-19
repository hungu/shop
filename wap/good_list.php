<?php 
	
	define('ACC_KEY', true);
	require('../include/init.php');

	$cate_id = 0;
	if(isset($_GET['cate_id']) && !empty($_GET['cate_id'])) {
		$cate_id = $_GET['cate_id'] + 0;
	}

	$tree = array();
	$cate = new Category_model();
	$tree = $cate->get_son_tree($cate_id);
	//判断是否是查找全部商品
	if($cate_id) {
		$tmp = $cate->find($cate_id);
		$cate_name = $tmp['cate_name'];
		$tree[] = $tmp;
	} else {
		$tree[]['cate_id'] = 0;
		$cate_name = '全部商品';
	}

	//获得所有顶级栏目
	$cate_list = $cate->select('*', 'parent_id = 0');

	$goods = new Goods_model();
	$goods_list = $goods->select('*', 'is_delete = 0');

	$good_list = array();
	foreach ($tree as $cate) {
		foreach ($goods_list as $good) {
			if($cate['cate_id'] == $good['cate_id']) {
				$good_list[$good['add_time']] = $good;
			}
		}
	}

	include('../view/wap/good_list.html')
 ?>