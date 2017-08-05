<?php
class Model{
	//保存连接信息
	public static  $link = NULL;

	//保存表名
	protected $table = NULL;
	//初始化表信息
	private $opt;
	//记录发送过的sql
	public static $sqls = array();

	public function __construct($table=NULL){
		$this->table = is_null($table)?C('DB_PREFIX').$this->table:C('DB_PREFIX').$table;

		//连接数据库
		$this->_connect();
		//初始化sql信息
		$this->_opt();
	}
	public function query($sql){
		self::$sqls[] = $sql;//将每次的sql语句保存
		$link = self::$link;
		$result = $link->query($sql);
		if($link->error) halt('mysql错误:'.$link->error.'<br/>SQL:'.$sql);
		$rows = array();
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		$result->free();//释放数据库查询的结果
		$this->_opt();
		return $rows;
	}
	//获取符合条件的所有数据
	public function all(){
		$sql = "SELECT".$this->opt['field']." FROM ".$this->table.$this->opt['where'].$this->opt['group'].$this->opt['having'].$this->opt['order'].$this->opt['limit'];
		return $this->query($sql);
	}
	//all的别名
	public function findAll(){
		return $this->all();
	}

	//查询指定字段的数据
	public  function field($field){
		$this->opt['field'] = " ".$field;
		return $this;
	}
	//添加查询条件where
	public function where($where){
		$this->opt['where'] = " WHERE ".$where;
		return $this;
	}

	
	public  function order($order){
		$this->opt['order'] = " ORDER BY ".$order;
		return $this;
	}

	public  function limit($limit){
		$this->opt['limit'] = " LIMIT ".$limit;
		return $this;
	}
	//获取符合条件的一条数据
	public function find(){
		$data = $this->limit(1)->all();
		$data = current($data);
		return $data;
	}
	//find的别名
	public function one(){
		return $this->find();
	}

	private function _opt(){
		$this->opt = array(
			'field'=>'*',
			'where'=>'',
			'group'=>'',
			'having'=>'',
			'order'=>'',
			'limit'=>''
			);
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