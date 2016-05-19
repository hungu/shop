<?php 
	define('ACC_KEY', true);
	require('./include/init.php');
	
	/*$conn = Mysql::get_ins();
	var_dump($conn->auto_execute('test', $_GET, 'insert'));*/

	$test = new Test_model('test');
	$data = array('char1'=>'aaa', 'char2'=>'bbb');
	$a = $test->get_row();
	var_dump($a);
 ?>