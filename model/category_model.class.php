<?php 
	/*
	file:category_model.class.php
	作用:栏目model
	 */

	defined('ACC_KEY')||exit('Access Invalid!');
	class Category_model extends Model{
		public function __construct() {
			parent::__construct('category', 'cate_id');
		}
		
		//获得指定栏目的子孙树
		public function get_son_tree($id = 0, $lev = 0) {
			$arr = $this->select();
			$tree = array();
			foreach($arr as $v) {
				if($v['parent_id'] == $id) {
					$v['lev'] = $lev;
					$tree[] = $v;
					$tree = array_merge($tree,$this->get_son_tree($v['cate_id'], $lev+1));
				}
			}
			return $tree;
		}

		//获得指定栏目的家谱树(找爹)
		public function get_father_tree($id = 0) {
			$arr = $this->select();
			$tree = array();
			while($id > 0) {
				foreach ($arr as $v) {
					if($v['cate_id'] == $id) {
						$tree[] = $v;
						$id = $v['parent_id'];
						break;
					}
				}
			}
			return $tree;
		}

		//查找子栏目
		public function get_son($cate_id) {
			return $this->db->get_all($this->table, 'cate_id, cate_name, parent_id', 'parent_id = ' . $cate_id);
		}
		
	}
 ?>

