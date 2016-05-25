<?php
//简单一点的
//
/*define('ACC_KEY', true);
include('include/lib_base.php');

class test {
	public function a() {
		$data = 'a';
		$key = 'yes';
		echo $data = encrypt($data, $key);
		echo '<br />';
		echo decrypt($data, $key);
	}
 }

 $test = new test();
 $test->a();*/

/* $timeout = 7;
 echo time();
 echo '<br />';
 $logined_timeout = time() + 86400 * $timeout;
 echo $logined_timeout;
*/
/* echo (1462720948 - 1462116148) / 7;*/

	/*$data = array(
			$a = 'a';
			$b = 'b';
			$c = 'c';
		);*/
	/*$data = 'a:b:c';
	$data = explode(':', $data);
	var_dump($data);*/

	/*$data = 'a:b:c';
	list($a, $b, $c) = explode(':', $data);
	echo $a;
	echo '<br />';
	echo $b;
	echo '<br />';
	echo $c;*/
	/*define('ACC_KEY', true);
	require('include/init.php');

	setcookie('a', encrypt('falwjfehauowfh', $conf->key));
*/

	/*header('Location: 3.php');*/
/*?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<a href="login.php?check=1&back=3.php">fffff</a>
</body>
</html>*/

	/*define('ACC_KEY', true);
	require('include/init.php');

	$cart = new Cart();
	if(isset($_SESSION['cart'])) {
		echo 'yes';
	} else {
		if($cart = new Cart()) {
			echo 'yes';
		} else {
			'no';
		}
	}
	echo '<br />';
	$cart->add_item(2, '西装', 222.99);
	var_dump($_SESSION['cart']);
	echo $cart->get_cnt();
	echo '<br />';
	echo $cart->get_num();
	echo '<br />';
	var_dump($cart->get_price());
*/

	/*$back = $_SERVER['HTTP_REFERER'];*/
	/*echo dirname($back);*/
	/*echo $back;*/
	/*echo substr($back, -8);*/



?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<form action="3.php" method="post">
		<input type="hidden" name='id[]' value='1'>
		<input type="hidden" name='id[]' value='2'>
		<input type="hidden" name='id[]' value='3'>
		<input type="hidden" name='id[]' value='4'>
		<input type="submit">
	</form>	
</body>
</html>