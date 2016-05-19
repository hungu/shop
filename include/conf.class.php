<?php 
	/*
	file:conf.class.php
	作用:读取config.inc.php 文件中的配置信息
	 */
	
	defined('ACC_KEY')||exit('Access Invalid!');
	
	class Conf {
		private static $ins = NULL;
		private $data = array();
		final private function __construct() {
			//将配置文件信息赋给data属性
			include(ROOT . 'include/config.inc.php');
			$this->data = $_CFG;
		}
		final private function __clone() {
		}
		public static function get_ins() {
			if(self::$ins instanceof self) {
				return self::$ins;
			} else {
				self::$ins = new self();
				return self::$ins;
			}
		}
		//动态读取配置
		public function __get($key) {
			if(array_key_exists($key, $this->data)) {
				return $this->data[$key];
			} else {
				return NULL;
			}
		}
		//动态设置配置
		public function __set($key,$value) {
			$this->data[$key] = $value;
		}
	}
 ?>