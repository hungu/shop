<?php 
	/*
	file:init.php;
	作用:框架初始化
	 */
	
	//开启session
	session_start();
	
	defined('ACC_KEY')||exit('Access Invalid!');
	
	define('ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/');	//初始化当前的根目录
	define('DEBUG', true);	//是否开启调试模式

	require(ROOT . 'include/lib_base.php');		//引入基本自定义函数

	function __autoload($class) {
		if(strripos($class, 'model') !== false) {
			require(ROOT . 'model/' . strtolower($class) . '.class.php');
		} else {
			require(ROOT . 'include/' . strtolower($class) . '.class.php');
		}
	}

	//加载配置文件
	$conf = Conf::get_ins();

	//自动解密COOKIE
	$_COOKIE = _decrypt($_COOKIE, $conf->key);

	//用addslashes过滤POST, GET, COOKIE
	$_GET = _addslashes($_GET);
	$_POST = _addslashes($_POST);
	$_COOKIE = _addslashes($_COOKIE);

	//设置调试模式
	if(defined('DEBUG')) {
		ini_set('display_errors', 'On');
		error_reporting(E_ALL);
	} else {
		ini_set('display_errors', 'Off');
	}



 ?>