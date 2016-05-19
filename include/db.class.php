<?php  
	/*
	file:db.class.php
	作用:数据库类
	 */
	
	defined('ACC_KEY')||exit('Access Invalid!');
	
	abstract class Db {
		/*
		连接服务器
		parms $db_host 服务器地址
		parms $db_user 用户名
		parms $db_password 密码
		return bool
		 */
		public abstract function connect($db_host, $db_user, $db_password);
		/*
		发送查询
		parms $sql 发送的sql语句
		return mixed
		 */
		public abstract function query($sql);

		/*
		查询多行数据
		parms $sql select型语句
		return array/bool
		 */
		public abstract function get_all($sql);

		/*
		查询单行数据
		parms $sql select型语句
		return array/bool
		 */
		public abstract function get_row($sql);

		/*
		查询单个数据
		parms $sql select型语句
		return array/bool
		 */
		public abstract function get_one($sql);

		/*
		自动执行insert/update语句
		parms $table 表名
		parms $data 数据
		parms $act 动作insert/update
		parms $where update位置
		return array/bool
		 */
		public abstract function auto_execute($table, $data, $act='insert', $where='');

	}
?>