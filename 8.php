<?php 
	/*$where = '';*/
	define('ACC_KEY', true);
	require('include/init.php');

	$request = new Request();

	/*$url = 'http://dev.in-carmedia.com/system/doack.php?token=11663c21bd2f8e784bf5d82a40114b79&act=in';*/
	$url = 'http://dev.in-carmedia.com/system/dologin.php';
	$data = array('deviceid' => '');

	$data = $request->http_request_GET($url);
	$data = json_decode($data);
	var_dump($data);
 ?>