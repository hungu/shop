<?php 
	/*
	file:	cart.calss.php
	作用:	购物车类
	 */
	
	/*
	将对象存入session 中发现未知原因的异常
	*/


	class Cart {
		private static $ins = NULL;
		private $conf = NULL;
		private $items = array();
		private $err_no = 0;
		private $err_msg = NULL;

		final private function __construct() {
			$this->conf = Conf::get_ins();
		}

		final private function __clone() {

		}

		//获得实例
		private static function get_ins() {
			if(!(self::$ins instanceof self)) {
	            self::$ins = new self();
	        }
	        return self::$ins;
		}

		//把购物车的单例队形放到session里
		public static function get_cart() {
			if(!isset($_SEEEION['cart']) || !($_SESSION['cart'] instanceof self)) {
				$_SESSION['cart'] = self::get_ins();
			}
			return $_SESSION['cart'];
		}

		//添加商品
		public function add_item($id, $name, $price, $num = 1) {
			if($num > $this->conf->car_good_max) {
				$num = $this->conf->car_good_max;
			}
			//如果商品已经存在则,只添加数量
			if($this->has_item($id)) {
				return $this->inc_num($id, $num);
			}
			$info = array();
			$info['name'] = $name;
			$info['price'] = $price;
			$info['num'] = $num;
			//添加
			$this->items[$id] = $info;
		}

		//判断商品是否存在于购物车
		public function has_item($id) {
	        return array_key_exists($id,$this->items);
	    }

	    //添加商品数量
	    public function inc_num($id, $num = 1) {
	    	if($this->has_item($id)) {
	    		$num = $this->items[$id]['num'] + $num;
	    		if($num > $this->conf->car_good_max) {
	    			$this->err_no = 1;
					return false;
				}
				$this->items[$id]['num'] = $num;
				return true;
	    	} else {
	    		$this->err_no = 2;
	    		return false;
	    	}
	    }

	    //减少商品数量
	    public function dec_num($id, $num = 1) {
	    	if($this->has_item($id)) {
	    		$this->items[$id]['num'] -= $num; 
	    	} else {
	    		$this->err_no = 2;
	    		return false;
	    	}

	    	if($this->items[$id]['num'] < 1) {
	    		$this->del_item($id);
	    	}
	    }

	    //删除商品
	    public function del_item($id) {
	    	unset($this->items[$id]);
	    }

	    //直接修改购物车商品的数量
	    public function mod_num($id, $num = 1) {
	    	if($num > $this->conf->car_good_max) {
	    		$this->err_no = 1;
				return false;
			}
	    	if(!$this->has_item($id)) {
	    		$this->err_no = 2;
	    		return false;
	    	}
	    	$this->items[$id]['num'];
	    	return true;
	    }

	    //获得商品种类
	    public function get_cnt() {
	    	return count($this->items);
	    }

	    //获得购物车商品的总数
	    public function get_num() {
	    	if($this->get_cnt() === 0) {
	    		return 0;
	    	}
	    	$sum = 0;
	    	foreach ($this->items as $item) {
	    		$sum += $item['num'];
	    	}
	    	return $sum;
	    }

	    //获得购物车商品总价值
	    public function get_price() {
	    	if($this->get_cnt() ===0) {
	    		return 0;
	    	}
	    	$price = 0.00;
	    	foreach ($this->items as $item) {
	    		$price += $item['num'] * $item['price'];
	    	}
	    	return $price;
	    }

	    //获得购物车里面的所有商品
	    public function get_all() {
	    	return $this->items;
	    }

	    //清空购物车
	    
	    public function clear() {
	    	$this->items = array();
	    }

		//获得错误信息
		public function get_err() {
			switch ($this->err_no) {
				case 1:
					$this->err_msg = '商品数量不能超过' . $this->conf->car_good_max;
					break;
				case 2:
					$this->err_msg = '商品不存在.';
					break;
				default:
					$this->err_msg = '未知错误.';
					break;
			}
		}

	}
	
 ?>