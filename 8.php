<?php 
	/*$where = '';*/
	define('ACC_KEY', true);
	require('include/init.php');

	$request = new Request();

	$url = 'http://dev.in-carmedia.com/system/gethotredio.php?token=48b4bf4df2a1eda7ba7b1f7b1668b79d';
	/*$url = 'http://dev.in-carmedia.com/system/dologin.php';
	$data = array('deviceid' => '');*/

	$data = $request->http_request_GET($url);
	$data = json_decode($data);
	var_dump($data);
 ?>