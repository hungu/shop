<?php 
	/*
	file:	cart.calss.php
	作用:	购物车类
	 */
	
	/*
	将对象存入session 中发现未知原因的异常
	*/

	/*
	购物车流程仿照京东商城
	添加购物车是商品数量可设置用户可填写和不可填写(为1)
	*/


	class Cart {
		private $conf = NULL;
		private $err_no = 0;
		private $err_msg = NULL;

		public function __construct() {
			$this->conf = Conf::get_ins();
			if(!isset($_SESSION['cart'])) {
				$_SESSION['cart'] = array();
			}
		}

		//添加商品
		public function add_item($id, $name, $price, $thumb_img, $num = 1) {
			$flage = false;
			if($num > $this->conf->car_good_max) {
				$num = $this->conf->car_good_max;
				$flage = true;
			}
			//如果商品已经存在则,只修改数量
			if($this->has_item($id)) {
				//如果添加商品数量大于最大添加数量且商品已存在,则直接修改商品数量为最大商品数量
				if($flage) {
					return $this->mod_num($id, $num);
				}
				return $this->inc_num($id, $num);
			}
			$info = array();
			$info['name'] = $name;				//商品名称
			$info['price'] = $price;			//商品价格
			$info['num'] = $num;				//商品数量
			$info['img'] = $thumb_img;			//商品缩略图
			//添加
			$_SESSION['cart'][$id] = $info;
			return true;
		}

		//判断商品是否存在于购物车
		public function has_item($id) {
	        return array_key_exists($id,$_SESSION['cart']);
	    }

	    //添加商品数量
	    public function inc_num($id, $num = 1) {
	    	if($this->has_item($id)) {
	    		$num = $_SESSION['cart'][$id]['num'] + $num;
	    		if($num > $this->conf->car_good_max) {
	    			$this->err_no = 1;
					return false;
				}
				$_SESSION['cart'][$id]['num'] = $num;
				return true;
	    	} else {
	    		$this->err_no = 2;
	    		return false;
	    	}
	    }

	    //减少商品数量
	    public function dec_num($id, $num = 1) {
	    	if($this->has_item($id)) {
	    		$_SESSION['cart'][$id]['num'] -= $num; 
	    	} else {
	    		$this->err_no = 2;
	    		return false;
	    	}

	    	if($_SESSION['cart'][$id]['num'] < 1) {
	    		$this->del_item($id);
	    	}
	    }

	    //删除商品
	    public function del_item($id) {
	    	unset($_SESSION['cart'][$id]);
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
	    	$_SESSION['cart'][$id]['num'] = $num;
	    	return true;
	    }

	    //获得商品种类
	    public function get_cnt() {
	    	return count($_SESSION['cart']);
	    }

	    //获得购物车商品的总数
	    public function get_num() {
	    	if($this->get_cnt() === 0) {
	    		return 0;
	    	}
	    	$sum = 0;
	    	foreach ($_SESSION['cart'] as $item) {
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
	    	foreach ($_SESSION['cart'] as $item) {
	    		$price += $item['num'] * $item['price'];
	    	}
	    	return $price;
	    }

	    //获得购物车里面的所有商品
	    public function get_all() {
	    	return $_SESSION['cart'];
	    }

	    //清空购物车
	    
	    public function clear() {
	    	$_SESSION['cart'] = array();
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