<?php 
	session_start();
	//生成验证码内容
	for($i = 0; $i < 4; $i ++){
		$rand.= dechex(rand(1, 15));
	}
	$_SESSION['pic'] = $rand;
	//创建画布
	$img = imagecreate(100, 30);
	//设置背景颜色
	$bg = imagecolorallocate($img, 255, 255, 255);
	//加上干扰线
	for($i = 0; $i < 3; $i ++){
		$co = imagecolorallocate($img, rand(0, 254), rand(0, 254), rand(0, 254));
		imageline($img, rand(0, 100), rand(0, 30), rand(0, 100), rand(0, 30), $co);
	}
	//加上干扰点
	for($i = 0;$i < 200; $i ++){
		$co = imagecolorallocate($img, rand(0, 254), rand(0, 254), rand(0, 254));
		imagesetpixel($img, rand(0, 100), rand(0, 30), $co);
	}
	//王图像上添加字符串
	imagestring($img, rand(3, 6), rand(3, 60), rand(3, 16), $rand, $co);
	//转换成图片
	header("Content-type: image/jpeg");
	imagejpeg($img);
	//销毁画布
	imagedestroy($img);
 ?>