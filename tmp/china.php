<?php 
	$im = imagecreatetruecolor(500, 25);
	$gray = imagecolorallocate($im, 200, 200, 200);
	$blue = imagecolorallocate($im, 0, 0, 255);

	imagefill($im, 0, 0, $gray);
	
	$code = '而文';

	imagettftext($im, 10, 0, 2, 20, $blue, './data/fonts/STXIHEI.TTF', $code);

	header("Content-type: image/jpeg");
	imagejpeg($im);
	imagedestroy($im);
 ?>