<?php
	/*
		图片处理类
	*/
	
	class Img_tool {
		//判断文件是否存在
		public static function f_exist($file) {
			if(file_exists($file)) {
				return true;
			}
			return false;
		}
		
		//判断函数是否存在
		public static function fun_exist($func) {
			if(function_exists($func)) {
				return true;
			}
			return false;
		}
		//获得图片的信息
		public static function image_info($image) {
			if(!self::f_exist($image)) {
				return false;
			}
			
			$info = getimagesize($image);
			if(!is_array($info)) {
				return false;
			}
			
			$img['width'] = $info[0];
			$img['height'] = $info[1];
			$img['ext'] = substr($info['mime'], strpos($info['mime'], '/') + 1);
			return $img;
		}
			
			//添加水印
		public static function water($dst, $water, $save = NULL, $pos = 2, $alpha = 50) {
			/*
			//验证文件是否存在
			if(!self::exist($dst) || !self::exist($water)) {
				return false;
			}
			*/
			
			//确认水印比待操作图片小
			$dinfo = self::image_info($dst);
			$winfo = self::image_info($water);
			
			if(!is_array($dinfo) || !is_array($winfo)) {
				return false;
			}
			
			if($winfo['height'] > $dinfo['height'] || $winfo['width'] > $dinfo['width']) {
				return false;
			}
			
			//载入两张图片
			$dfunc = 'imagecreatefrom' . $dinfo['ext'];
			$wfunc = 'imagecreatefrom' . $winfo['ext'];
			if(!self::fun_exist($dfunc) || !self::fun_exist($wfunc)) {
				return false;
			}
			
			//动态加载函数来创建画布
			$dim = $dfunc($dst);  // 创建待操作的画布
			$wim = $wfunc($water);  // 创建水印画布
			
			// 根据水印的位置 计算粘贴的坐标
			switch($pos) {
				case 0: // 左上角
					$posx = 0;
					$posy = 0;
					break;
				case 1: // 右上角
					$posx = $dinfo['width'] - $winfo['width'];
					$posy = 0;
					break;
				case 3: // 左下角
					$posx = 0;
					$posy = $dinfo['height'] - $winfo['height'];
					break;
				default:
					$posx = $dinfo['width'] - $winfo['width'];
					$posy = $dinfo['height'] - $winfo['height'];
			}
			
			// 加水印
			imagecopymerge ($dim,$wim, $posx , $posy , 0 , 0 , $winfo['width'] , $winfo['height'] , $alpha);
			
			$createfunc = 'image' . $dinfo['ext'];
			if(!self::fun_exist($createfunc)) {
				imagedestroy($dim);
				imagedestroy($wim);
				return false;
			}
			// 保存
			if(!$save) {
				$save = $dst;
				unlink($dst); // 删除原图
			}
			
			$createfunc($dim,$save);
			
			imagedestroy($dim);
			imagedestroy($wim);

			return true;
		}
		
		//生成缩略图
		public static function thumb($dst, $save = NULL, $width = 200, $height = 200) {
			//判断处理图片是否存在
			$dinfo = self::image_info($dst);
			if(!is_array($dinfo)) {
				return false;
			}
			
			//计算缩放比例
			$calc = min($width/$dinfo['width'], $height/$dinfo['height']);
			
			//创建原始画布
			$dfunc = 'imagecreatefrom' . $dinfo['ext'];
			if(!self::fun_exist($dfunc)) {
				return false;
			}
			$dim = $dfunc($dst);
			
			// 创建缩略画布
			$tim = imagecreatetruecolor($width, $height);
			
			// 创建白色填充缩略画布
			$white = imagecolorallocate($tim, 255, 255, 255);
			
			// 填充缩略画布
			imagefill($tim,0,0,$white);
			
			// 计算缩略图大小
			$dwidth = (int)$dinfo['width']*$calc;
			$dheight = (int)$dinfo['height']*$calc;
			//计算间距
			$paddingx = (int)($width - $dwidth) / 2;
			$paddingy = (int)($height - $dheight) / 2;
			//创建缩略图
			imagecopyresampled($tim,$dim,$paddingx,$paddingy,0,0,$dwidth,$dheight,$dinfo['width'],$dinfo['height']);
			
			$createfunc = 'image' . $dinfo['ext'];
			if(!self::fun_exist($createfunc)) {
				imagedestroy($dim);
				imagedestroy($tim);
				return false;
			}
			//确认是否需要删除原图
			if(!$save) {
				$save = $dst;
				unlink($dst);
			}
			
			//保存图片
			$createfunc($tim,$save);
			
			//销毁画布
			imagedestroy($dim);
			imagedestroy($tim);
			
			return true;
			
		}
		
		 //写验证码
    /*
        author: dabao
    */
		public static function captcha($width=50,$height=25) {
            //造画布
            $image = imagecreatetruecolor($width,$height) ;
           
            //造背影色
            $gray = imagecolorallocate($image, 200, 200, 200);
           
            //填充背景
            imagefill($image, 0, 0, $gray);
           
            //造随机字体颜色
            $color = imagecolorallocate($image, mt_rand(0, 125), mt_rand(0, 125), mt_rand(0, 125)) ;
            //造随机线条颜色
            $color1 =imagecolorallocate($image, mt_rand(100, 125), mt_rand(100, 125), mt_rand(100, 125));
            $color2 =imagecolorallocate($image, mt_rand(100, 125), mt_rand(100, 125), mt_rand(100, 125));
            $color3 =imagecolorallocate($image, mt_rand(100, 125), mt_rand(100, 125), mt_rand(100, 125));
           
            //在画布上画线
            imageline($image, mt_rand(0, 50), mt_rand(0, 25), mt_rand(0, 50), mt_rand(0, 25), $color1) ;
            imageline($image, mt_rand(0, 50), mt_rand(0, 20), mt_rand(0, 50), mt_rand(0, 20), $color2) ;
            imageline($image, mt_rand(0, 50), mt_rand(0, 20), mt_rand(0, 50), mt_rand(0, 20), $color3) ;
           
            //在画布上写字
            $text = substr(str_shuffle('ABCDEFGHIJKMNPRSTUVWXYZabcdefghijkmnprstuvwxyz23456789'), 0,4) ;
            imagestring($image, 5, 7, 5, $text, $color) ;
           
            //显示、销毁
            header('content-type: image/jpeg');
            imagejpeg($image);
            imagedestroy($image);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>