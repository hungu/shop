<?php 
	/*
	file:lib_base.php
	作用:基本自定义函数
	 */
	
	defined('ACC_KEY')||exit('Access Invalid!');

	//循环对数组解密
	function _decrypt($arr, $key) {
		foreach ($arr as $k => $v) {
			if(is_string($v)) {
				$arr[$k] = decrypt($v, $key);
			} else if(is_array($v)) {
				$arr[$k] = _decrypt($v, $key);
			}
		}
		return $arr;
	}

	//递归对数组使用addslashes转义
	function _addslashes($arr) {
		foreach ($arr as $k => $v) {
			if(is_string($v)) {
				$arr[$k] = addslashes($v);
			} else if(is_array($v)) {
				//如果是数组,递归调用
				$arr[$k] = _addslashes($v);
			}
		}
		return $arr;
	}

	//创建唯一字符串(商品单号),每一个微秒不重复
	function build_order_no() {
		return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(0, 9);
	}

	//加密算法
	function encrypt($data, $key) {
		$char = ''; 
		$str = ''; 
	    $key = md5($key);  
	    $x = 0;  
	    $len = strlen($data);  
	    $l = strlen($key);  
	    for ($i = 0; $i < $len; $i++) {  
	        if ($x == $l)   
	        {  
	            $x = 0;  
	        }  
	        $char .= $key{$x};  
	        $x++;  
	    }  
	    for ($i = 0; $i < $len; $i++) {  
	        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);  
	    }  
	    return base64_encode($str);  
	}  

	//解密算法
	function decrypt($data, $key) { 
		$char = '';   
	    $key = md5($key);  
	    $x = 0;  
	    $data = base64_decode($data);  
	    $len = strlen($data);  
	    $l = strlen($key);  
	    for ($i = 0; $i < $len; $i++) {  
	        if ($x == $l)   
	        {  
	            $x = 0;  
	        }  
	        $char .= substr($key, $x, 1);  
	        $x++;  
	    }  
	    for ($i = 0; $i < $len; $i++) {  
	        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {  
	            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));  
	        }  
	        else {  
	            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));  
	        }  
	    }  
	    return $str;  
	} 

	//验证键值在数组中是否存在,且对应的数值不为空
	function array_yz($key, $arr) {

		if(!is_array($arr) || empty($arr)) {
			return false;
		}
		if(is_array($key)) {
			foreach ($key as $v) {
				if(!in_array($v, $arr)) {
					return false;
				}
				if(empty($arr[$v])) {
					return false;
				}
			}
		} else {
			if(!in_array($key, $arr)) {
				return false;
			}
			if(empty($arr[$key])) {
				return false;
			}
		}
		return true;
	}
 ?>