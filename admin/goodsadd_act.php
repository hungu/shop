<?php 
/*
file:	goodadd.php
作用:	添加商品处理实现
 */

	/*define('ACC_KEY', true);
	require('../include/init.php');*/

	include_once('include.php');

	$file = $_FILE;

	$data = array();
	$goods = new goods_model();
	$_POST['goods_weight'] *= $_POST['weight_unit'];
	//自动过滤
	$data = $goods->_facade($_POST);
	//自动填充
	$data = $goods->_auto_fill($data);
	//自动添加货号
	if(empty($data['goods_sn'])) {
		$data['goods_sn'] = build_order_no();
	}
	//自动验证
	if(!$goods->_validate($data)) {
		echo $goods->error['_validate'];
		exit;
	}
	$up = new File_up_load();
	//文件上传
	$path = $up->upload('goods_img');
	if(!$path) {
		echo $up->get_error_msg();
		exit;
	}
	//获取上传图片的信息,判断图片大小是否符合要求
	$info = Img_tool::image_info($path);
	if($info['width'] < 600 || $info['height'] < 800) {
		unlink($path);
		die('图片分辨率错误:上传图片分辨率应大于 600*800');
	}
	//原始图片保存路径
	$data['ori_img'] = ltrim($path, $conf->file_path);
	//缩略图保存路径
	$thumb_img = substr($data['ori_img'], 0, 7) . 't' . substr($data['ori_img'], -19);
	//商品图路径
	$goods_img = substr($data['ori_img'], 0, 7) . 'g' . substr($data['ori_img'], -19);
	//保存缩略图和商品图
	if(Img_tool::thumb($path, $conf->file_path . $thumb_img, 160, 220) && Img_tool::thumb($path, $conf->file_path . $goods_img, 300, 400) && Img_tool::thumb($path, NULL, 600, 800)) {
		$data['thumb_img'] = $thumb_img;
		$data['goods_img'] = $goods_img;
	} else {
		die('文件错误');
	}
	
	//添加商品
	if($goods->add($data)) {
		echo '商品发布成功!';
	} else {
		echo '商品发布失败!';
	}
	
 ?>