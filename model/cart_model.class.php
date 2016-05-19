<?php 
	
	/*
	购物车model
	 */
	
	defined('ACC_KEY')||exit('Access Invalid!');
	class Cart_model extends Model {
		private $conf = NULL;
		private $err_no = 0;
		private $err_msg = NULL;
		private $user_id = 0;				//存放用户id
		private $cart = array();			//存放session购物车的内容

		public function __construct() {
			parent::__construct('cart', 'cart_id');
			$this->user_id = $_SESSION['user_id'];
			$this->conf = Conf::get_ins();
			//如果购物车数据,$rs 为空数据 if 判断为false
			if($rs = $this->select('*', 'user_id = ' . $this->user_id)) {
				//数据库类返回的为数组
				foreach ($rs as $k => $v) {
					$id = $v['goods_id'];
					$this->cart[$id]['cart_id'] = $v['cart_id'];
					$this->cart[$id]['name'] = $v['goods_name'];
					$this->cart[$id]['price'] = $v['shop_price'];
					$this->cart[$id]['num'] = $v['goods_num'];
					$this->cart[$id]['img'] = $v['thumb_img'];
					$this->cart[$id]['is_salse'] = $v['is_on_sale'];
				}
			}
			if(!empty($_SESSION['cart'])) {
				if($this->sync()) {
					$_SESSION['cart'] = NULL;
				}
			}
		}

		/*
		自动过滤 自动验证 自动填充 根据后期功能填写
		 */
		
		//同步用户未登陆时放入购物车的商品
		public function sync() {
			//循环添加
			foreach ($_SESSION['cart'] as $k => $v) {
				$this->add_item($k, $v['name'], $v['price'], $v['img'], $v['num']);
			}
			return true;
		}

		//添加购物车
		public function add_item($goods_id, $name, $price, $thumb_img, $num =1) {
			$flage = false;
			if($num > $this->conf->car_good_max) {
				$num = $this->conf->car_good_max;
				$flage = true;
			}
			//如果商品已经存在则,只修改数量
			if($this->has_item($goods_id)) {
				//如果添加商品数量大于最大添加数量且商品已存在,则直接修改商品数量为最大商品数量
				if($flage) {
					return $this->mod_num($goods_id, $num);
				}
				return $this->inc_num($goods_id, $num);
			}
			//添加数据库
			$data = array(
					'goods_id' => $goods_id,
					'user_id' => $this->user_id,
					'goods_name' => $name,
					'shop_price' => $price,
					'thumb_img' => $thumb_img,
					'is_on_sale' => 1,
					'goods_num' => $num,
				);
			if($this->add($data)) {
				//同步到类中
				$tmp = array();
				$tmo['cart_id'] = $this->db->insert_id();
				$tmp['name'] = $name;
				$tmp['price'] = $price;
				$tmp['num'] = $num;
				$tmp['img'] = $thumb_img;
				$tmp['is_salse'] = 1;
				$this->cart[$goods_id] = $tmp;
				return true;
			}
			$this->err_no = 3;
			return false;
		}

		//判断购物车是否存在该商品
		public function has_item($id) {
			return array_key_exists($id, $this->cart);
		}

		//添加商品数量
	    public function inc_num($id, $num = 1) {
	    	if($this->has_item($id)) {
	    		$num = $this->cart[$id]['num'] + $num;
	    		if($num > $this->conf->car_good_max) {
	    			$this->err_no = 1;
					return false;
				}
				if(!$cart_id = $this->get_cart_id($id)) {
					return false;
				}
				$data = array('goods_num' => $num);
				if($this->update($data, $cart_id)) {
					$this->cart[$id]['num'] = $num;
					return true;
				}
				$this->err_no = 3;
				return false;
	    	} else {
	    		$this->err_no = 2;
	    		return false;
	    	}
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
	    	if(!$cart_id = $this->get_cart_id($id)) {
				return false;
			}
	    	$data = array('goods_num', $num);
			if($this->update($data, $cart_id)) {
				$this->cart[$id]['num'] = $num;
				return true;
			}
			$this->err_no = 3;
			return false;
	    }

	    //获得cart_id
	    public function get_cart_id($id) {
	    	if(!$cart_id = $this->db->get_one($this->table, 'cart_id', ' goods_id = ' . $id . ' AND user_id = ' . $this->user_id)) {
				$this->err_no = 3;
				return false;
			}
			return $cart_id;
	    }

	    //减少购物车的数量
	    public function dec_num($id, $num = 1) {
	    	if($this->has_item($id)) {
	    		if(!$cart_id = $this->get_cart_id($id)) {
					return false;
				}
				//如果购物车数量小于1,则删除该商品
	    		if($this->cart[$id]['num'] <= $num) {
					if($this->delete($cart_id)) {
						unset($this->cart[$id]);
						return true;
					}
					$this->err_no = 3;
					return false;
	    		} else {
	    			$num = $this->cart[$id]['num'] - $num;
	    			return $this->mod_num($id, $num);
	    		}
	    	}
	    }

	    //删除商品
	    public function del_item($id) {
	    	if(!$cart_id = $this->get_cart_id($id)) {
				return false;
			}
	    	if($this->delete($cart_id)) {
				unset($this->cart[$id]);
				return true;
			}
			$this->err_no = 3;
			return false;
	    }

	    //获得商品种类
	    public function get_cnt() {
	    	return count($this->cart);
	    }

	    //获得商品的总数
	    public function get_num() {
	    	if($this->get_cnt() === 0) {
	    		return 0;
	    	}
	    	$sum = 0;
	    	foreach ($this->cart as $v) {
	    		$sum += $v['num'];
	    	}
	    	return $sum;
	    }

	    //获得购物车商品总价值
	   	public function get_price() {
	   		if($this->get_cnt() === 0) {
	   			return 0.00;
	   		}
	   		$price = 0.00;
	   		foreach ($this->cart as $v) {
	   			$price += $v['num'] * $v['price'];
	   		}
	   		return $price;
	   	}

	   	//获得购物车里面所有的商品
	   	public function get_all() {
	   		return $this->cart;
	   	}

	   	//清空购物车
	   	public function clear() {
	   		$this->db->delete($this->table, 'user_id = ' . $this->user_id);
	   		if($this->db->affected_rows() === get_cnt()) {
	   			$this->cart = array();
	   			return true;
	   		}
	   		$this->err_no = 3;
	   		return false;
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
				case 3:
					$this->err_msg = '数据库错误';
					break;
				default:
					$this->err_msg = '未知错误.';
					break;
			}
		}

		//检查购物车商品数量是否超过库存
		public function num_check($good_ids = array()) {
			if(empty($good_ids)) {
				$good_ids = implode(' OR goods_id = ', array_keys($this->cart));
			} else {
				$good_ids = implode(' OR goods_id = ', $good_ids);
			}
			$good_list = $this->db->get_all( 'goods', 'goods_number, goods_id', 'goods_id = ' . $good_ids);
			foreach ($good_list as $v) {
				foreach ($this->cart as $k => $cart) {
					if($v['goods_id'] == $k) {
						if($v['goods_number'] < $cart['num']) {
							return false;
						}
					}
				}
			}
			return true;
		}
	}
 ?>