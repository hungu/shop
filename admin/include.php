<?php 
	
	define('ACC_KEY', true);
	require('../include/init.php');
	if(!isset($_SESSION['adm_name']) || empty($_SESSION['adm_name'])) {
		header('Location: login.php');
		exit;
	}
 ?>