<?php 

	
	defined('ACC_KEY')||exit('Access Invalid!');
	class Goods_model extends Model {
		public function __construct() {
			parent::__construct('goods', 'goods_id');
		}
		//自动过滤字段
		protected $fields =  array(
				'goods_id', 'goods_sn', 'cate_id', 'brand_id', 
				'goods_name', 'shop_price', 'market_price', 
				'goods_number', 'click_count', 'goods_weight', 
				'goods_brief', 'goods_tesc', 'thumb_img', 
				'goods_img', 'ori_img', 'is_on_sale', 'is_delete', 
				'is_best', 'is_new', 'is_hot', 'add_time', 'last_update'
				);
		//自动填充字段和规则
		protected $_auto = array(
			array('is_hot', 'value', 0),
			array('is_new', 'value', 0),
			array('is_best', 'value', 0),
			array('add_time', 'function', 'time')
			);

		//自动验证字段
		protected $_valid = array(
			array('goods_name', 1, '必须有商品名', 'require', ''),
			array('cate_id', 1, '栏目id必须是整型值', 'number', ''),
			array('is_new', 0, '参数参数错误', 'in', '0,1'),
			array('goods_brief', 2, '商品简介须在5到10个汉字之间', 'length', '10,20')
			);

		//把商品放入回收站(is_delete设置唯1)
		public function trash($goods_id) {
			return $this->update(array('is_delete' => 1), $goods_id);
		}

		//查找未加入回收站所有商品
		public function get_goods() {
			return $this->select('*', 'is_delete = 0');
		}

		//查找所有加入回收站的商品
		public function get_trash() {
			return $this->select('*', 'is_delete = 1');
		}
		
	}
 ?>