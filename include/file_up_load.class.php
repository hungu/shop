<?php

	class File_up_load {
		private $path = '';				//上传文件保存路径
		private $allow_type = array();	//限制上传文件的类型
		private	$max_size = 0;		//限制上传文件的大小
		private $is_rand_name = false;	//是否使用系统随机重命名文件(false不随机)
		
		private $file_name = '';		//源文件名
		private	$tmp_file_name = '';	//临时文件名
		private $file_type = '';		//文件类型
		private $file_size = '';		//文件大小
		private $new_file_name = '';	//新文件名
		private $error_num = 0;			//错误号
		private $error_mess = '';		//错误信息

		private $conf = NULL;			//配置文件引用
		
		//初始化
		public function __construct() {
		//配置文件信息的取得和赋值
			$this->conf = Conf::get_ins();
			$this->path = $this->conf->file_path;
			$this->allow_type = $this->conf->file_allow_type;
			$this->max_size = $this->conf->file_max_size * 1024 * 1024;
			$this->is_rand_name = $this->conf->file_is_rand_name;
			
		}
		
		//设置配置信息
		public function __set($key, $value) {
			$key = strtolower($key);
			if(array_key_exists($key, get_class_vars(get_class($this)))) {
				$this->set_option($key, $value);
			}
		}
		
		//成员属性赋值
		private function set_option($key, $value) {
			$this->$key = $value;
		}
		
		//文件上传处理
		public function upload($field) {
			$return = true;
			//判断路径是否合法
			if(!$this->check_path()) {
				$this->error_mess = $this->get_error();
				return false;
			}
			//将上传的文件信息赋值
			$name = $_FILES[$field]['name'];
			$tmp_name = $_FILES[$field]['tmp_name'];
			$size = $_FILES[$field]['size'];
			$error = $_FILES[$field]['error'];
			//判断是单文件上传还是多文件上传
			if(is_Array($name)) {
				$errors = array();
				//循环文件
				foreach($name as $k => $v) {
					if(!$this->set_files($name[$k], $tmp_name[$k], $size[$k], $error[$k])) {
						if(!$this->check_file_size() || !$this->check_file_type()) {
							$errors[] = $this->get_error();
							$return = false;
						}
					} else {
						$errors[] = $this->get_error();
						$return = false;
					}
					//如果存在错误,初始化属性
					if(!$return) {
						$this->set_files();
					}
				}
				if($return) {
					//存放所有上传文件名
					$file_names = array();
					foreach($name as $k => $v) {
						if(!$this->set_files($name[$k], $tmp_name[$k], $size[$k], $error[$k])) {
							$this->set_new_file_name();
							if(!$this->move_file()) {
								$errors[] = $this->get_error();
								return false;
							}
							$file_names[] = $this->new_file_name;
						}
					}
					
				}
				$this->error_mess = $errors;
				return $return;
			//单文件上传
			} else {
				if($this->set_files($name, $tmp_name, $size, $error)) {
					//检查大小和类型
					if($this->check_file_size() && $this->check_file_type()) {
						//设置新文件明 
						$this->set_new_file_name();
						//移动文件
						if($this->move_file()) {
							//移动成功返回保存路径
							$path = rtrim($this->path, '/') . '/';
							$path .= $this->new_file_name;
							return $path;
						} else {
							$return = false;
						}
					} else {
						$return = false;
					}
				} else {
					$return = false;
				}
				if(!$return) {
					$this->error_mess = $this->get_error();
				}
				return $return;
			}
		}
		
		//将文件移动到指定目录
		private function move_file() {
			if(!$this->error_num) {
				$path = $this->path;
				$path .= $this->new_file_name;
				if(@move_uploaded_file($this->tmp_file_name, $path)) {
					return true;
				} else {
					$this->set_option('error_num', -3);
					return false;
				}
			} else {
				return false;
			}
		}
		
		//设置新文件名
		private function set_new_file_name() {
			//判断是否需要随机命名
			if($this->is_rand_name) {
				$this->set_option('new_file_name', $this->pro_rand_name());
			} else {
				$this->set_option('new_file_name', $this->file_name);
			}
		}
		
		//生成随机文件名
		private function pro_rand_name() {
			//生成15位随机数
			$file_name = build_order_no();
			return $file_name . '.' . $this->file_type;
		}

		//检查上传文件类型
		private function check_file_type() {
			if (in_array(strtolower($this->file_type), $this->allow_type)) {
		      return true;
		    }else {
		      $this->setOption('error_num', -1);
		      return false;
		    }
		}
		
		//检查上传文件大小
		private function check_file_size() {
			if ($this->file_size > $this->max_size) {
		      $this->setOption('error_num', -2);
		      return false;
		    }else{
		      return true;
		    }
		}

		//检查文件保存路径是否合法
		private function check_path() {
			//判断路径是否定义
			if(empty($this->path)) {
				$this->set_option('error_num', -5);
				return false;
			}
			$path = rtrim($this->path, '/') . '/' . date('ymd') . '/';
			$this->set_option('path', $path);
			//判断路径是否存在,如不存在则手动创建
			if(!file_exists($this->path) && !mkdir($this->path, 0755, true)) {
				$this->set_option('error_num', -4);
				return false;
			}
			//查看路径是否可写
			if(!is_writable($this->path)) {
				$this->set_option('error_num', -6);
				return false;
			}
			return true;
			
		}
		
		//设置上传文件信息
		private function set_files($name='', $tmp_name='', $size=0, $error=0) {
			$this->set_option('error_num', $error);
			if($error) {
				return false;
			}
			$this->set_option('file_name', $name);
			$this->set_option('tmp_file_name', $tmp_name);
			$type = explode('.', $name);
			$type = end($type);
			$this->set_option('file_type', strtolower($type));
			$this->set_option('file_size', $size);
			return true;
		}
		
		//设置错误信息
		private function get_error() {
			$str = "上传文件&lt;font color='red'>{$this->file_name}&lt;/font>时出错 : ";
			switch($this->error_num) {
				case 4: $str .= "没有文件被上传"; break;
				case 3: $str .= "文件只有部分被上传"; break;
				case 2: $str .= "上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值"; break;
				case 1: $str .= "上传的文件超过了php.ini中upload_max_filesize选项限制的值"; break;
				case -1: $str .= "未允许类型"; break;
				case -2: $str .= "文件过大,上传的文件不能超过{$this->maxsize}个字节"; break;
				case -3: $str .= "上传失败"; break;
				case -4: $str .= "建立存放上传文件目录失败，请重新指定上传目录"; break;
				case -5: $str .= "必须指定上传文件的路径"; break;
				case -6: $str .= "目标不可写";break;
				default: $str .= "未知错误";
			}
			return $str.'&lt;br>';
		}
		
		//取得错误信息
		public function get_error_msg(){
			return $this->error_mess;
		}
		
	}
?>