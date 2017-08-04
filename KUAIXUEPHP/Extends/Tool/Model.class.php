<?php
class Model{
	//保存连接信息
	public static  $link = NULL;

	public function __construct(){
		//连接数据库
		$this->_connect();
	}
	//连接数据库
	private function _connect(){
		if(is_null(self::$link)){
			$db = C('DB_DATABASE');
			if(empty($db)) halt('请先配置数据库');
			$link = new Mysqli(C('DB_HOST'), C('DB_USER'),C('DB_PASSWORD'),$db,C('DB_PORT'));
			if($link->connect_error) halt('数据库连接错误，请检查配置项');
			$link->set_charset(C('DB_CHARSET'));
			self::$link = $link;
		}
	}
}