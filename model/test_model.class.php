<?php 
	
	defined('ACC_KEY')||exit('Access Invalid!');

	class Test_model extends Model {
		public function __construct() {
			parent::__construct('test', 'id');
		}
		public function reg($data) {
			$a = $this->db->auto_execute($this->table, $data,'update', 'id=2');
			echo $this->db->affected_rows();
			return $a;
		}
		
		public function query() {
			return $this->db->query('select * from test');
		}
		public function get_row() {
			
			return $this->db->get_all('test',  array('char1', 'char2'));
		}
	}
 ?>