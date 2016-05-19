<?php 
	/*
	file:config.inc.php
	作用:配置文件
	 */
	
	defined('ACC_KEY')||exit('Access Invalid!');
	
	//初始化配置数组
	$_CFG = array();

	//db
	$_CFG['db_host'] = 'localhost';	//数据库地址
	$_CFG['db_user'] = 'root';			//数据库用户名
	$_CFG['db_password'] = 'password';	//数据库用户密码
	$_CFG['db_name'] = 'shop';
	$_CFG['db_char'] = 'utf8';

	//上传文件
	$_CFG['file_path'] = ROOT . 'data/img/';				//文件存放目录
	$_CFG['file_allow_type'] = array('jpg','gif','png');	//允许被上传的文件类型
	$_CFG['file_max_size'] = 1;								//允许上传文件的大小(M)
	$_CFG['file_is_rand_name'] = true;						//是否才用随机命名

	//加密(cookie)
	$_CFG['key'] = 'book_store';							//加密密钥
	$_CFG['salt'] = 'my_store';								//盐

	//设置记住登陆时间
	$_CFG['logined_timeout'] = 7;							//最大记住登陆时间(天)

	//购物车商品最大数量
	$_CFG['car_good_max'] = 200;							//单个商品最大数量

 ?>