<?php
// $data = $_POST;
// $res = array();

$res['code'] = 1;
$res['msg'] = 'OK';



// if($data['user'] == 'admin'){
//  	$res['code'] = 0;
//  	$res['msg'] = "不能为admin";
// }

// sleep(3);
$res['msg'] = date("YmdHis");

echo json_encode($res);

?>