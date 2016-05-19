<?php 
	/*
	file:log.class.php
	作用:记录信息到日志
	 */
	defined('ACC_KEY')||exit('Access Invalid!');
	
	class Log {
		const LOGFILE = ROOT . 'data/log/curr.log';//日志文件的名称
		//记录日志
		public static function write($cont) {
			$cont = date('y-m-d H:i:s') . '  ' . $cont . "\n";
			self::is_bak();
			$fh = fopen(self::LOGFILE, 'ab');
			fwrite($fh, $cont);
			fclose($fh);
		}

		//备份日志
		private static function bak() {
			$bak = dirname(self::LOGFILE) . '/' . date('ymd_His_') . 'log.bak';
			return rename(self::LOGFILE, $bak);
		}

		//返回日志文件路径
		private static function is_bak() {
			//判断文件是否存在,不存在则手动创建
			if(!file_exists(self::LOGFILE)) {
				touch(self::LOGFILE);
			}
			//清除文件缓存
			clearstatcache();
			//判断文件是否达到备份要求(超过一兆备份)
			$size = filesize(self::LOGFILE);
			if($size <= 1024*1024) {
				return;
			}
			//执行备份文件
			if(!self::bak()) {
				return;
			} else {
				touch(self::LOGFILE);
				return;
			}

		}
	}
 ?>