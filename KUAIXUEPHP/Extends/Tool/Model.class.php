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
	//有结果集的方法select
	public function query($sql){
		self::$sqls[] = $sql;//将每次的sql语句保存
		$link = self::$link;
		$result = $link->query($sql);//这里的query是mysqli的方法，不是当前类的query
		if($link->error) halt('mysql错误:'.$link->error.'<br/>SQL:'.$sql);
		$rows = array();
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		$result->free();//释放数据库查询的结果
		$this->_opt();//清空查询的参数
		return $rows;
	}
	//无结果的方法实现,比如delete,insert
	public function exe($sql){

		self::$sqls[] = $sql;
		$link = self::$link;
		$bool = $link->query($sql);
		$this->_opt();
		if(is_object($bool)){
			halt('请用query方法发送查询sql');
		}

		if ($bool) {
			return $link->insert_id ? $link->insert_id : $link->affected_rows;//affected_rows 所影响的记录行数
		}else{
			halt('mysql错误:'.$link->error."<br/>SQL:".$sql);
		}
	}
	//添加方法
	public function add($data=NULL){
		if(is_null($data)) $data = $_POST;

		$fields = '';
		$values = '';
		foreach ($data as $f => $v) {
			$fields .="`".$this->_safe_str($f)."`,";
			$values .="'".$this->_safe_str($v)."',";
		}
		$fields = trim($fields,',');
		$values = trim($values,',');

		$sql = "INSERT INTO ".$this->table."(".$fields.") VALUES(".$values.")";
		return $this->exe($sql);
	}

	//修改方法
	public function update($data=NULL){
		if(empty($this->opt['where'])) halt('更新语句必须有where');

		if(is_null($data)) $data = $_POST;

		$values = '';
		foreach ($data as $f => $v) {
			$values .="`".$this->_safe_str($f)."`='".$this->_safe_str($v)."',";
		}
		$values = trim($values,',');
		$sql = "UPDATE ".$this->table." SET ".$values.$this->opt['where'];
		return $this->exe($sql);
	}
	//删除方法
	public function delete(){
		if(empty($this->opt['where'])) halt('删除语句必须有where');
		$sql = "DELETE FROM ".$this->table.$this->opt['where'];
		$this->exe($sql);

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
		if(!empty($where)){
			$this->opt['where'] = " WHERE ".$where;
		}
		
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


	//模型安全处理字符串(处理用户的数据)
	private function _safe_str($str){
		//系统开启自动转义
		if(get_magic_quotes_gpc()){//get_magic_quotes_gpc获取当前 magic_quotes_gpc 的配置选项设置
			$str = stripslashes($str);//stripslashes() 函数删除由 addslashes() 函数添加的反斜杠。
		}

		return self::$link->real_escape_string($str);//mysql_real_escape_string() 函数转义 SQL 语句中使用的字符串中的特殊字符。
	}
}