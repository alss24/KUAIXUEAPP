<?php 
//错误提示信息
function halt($error,$level='ERROR',$type=3,$dest=NULL){
	if(is_array($error)){
		Log::write($error['message'],$level,$type,$dest);
	}else{
		Log::write($error,$level,$type,$dest);
	}

	$e = array();
	if(DEBUG){
		//开启调试模式
		if(!is_array($error)){
			$trace = debug_backtrace();
			$e['message'] = $error;
			$e['file'] = $trace[0]['file'];
			$e['line'] = $trace[0]['line'];
			$e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
			$e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';

			ob_start();//开启缓冲区
			debug_print_backtrace();//打印错误信息
			$e['trace'] = htmlspecialchars(ob_get_clean());//实体化从缓冲区获取的信息
		}else{
			$e = $error;
		}
	}else{
		if($url = C('ERROR_URL')){
			go($url);
		}else{
			$e['message'] = C('ERROR_MSG');
		}
	}

	include DATA_PATH.'/Tpl/halt.html';
	die;
}

//打印用户自定义的常量
function print_const(){
	$const = get_default_constants(true);//true表示返回带键值的数组
	p($const['user']);

}






//打印函数
function p($arr){
	if(is_bool($arr)){
		var_dump($arr);
	}else if(is_null($arr)){
		var_dump($arr);
	}else{
		echo "<pre style='padding:10px;border-radius:5px;background:#f5f5f5;border:1px solid #ccc;font-size:15px;'>".print_r($arr,true)."</pre>";
	}
}



//跳转函数
function go($url,$time=0,$msg=''){
	/*headers_sent() 函数检查 HTTP 标头是否已被发送以及在哪里被发送。
	如果报头已发送，则返回 true，否则返回 false。*/
	if(!headers_sent()){
		$time==0?header("Location:".$url):header("refresh:{$time};url={$url}");
		die($msg);
	}else{
		echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if($time) die($msg);
	}
}
/*加载配置项
1.首先加载系统默认配置项C($sysConfig) 之后加载用户自定义配置项C($userConfig),用户自定义的配置项优先级高于系统默认配置项
2.读取配置项
C('CODE_LEN')
3.临时动态改变配置项
C('CODE_LEN',10)
4.读取所有配置项
C()
*/
function C($var = NULL,$value = NULL){
	static $config = array();
	//加载配置项
	if(is_array($var)){
		$config = array_merge($config,array_change_key_case($var,CASE_UPPER));
		return;
	}

	if(is_string($var)){
		$var = strtoupper($var);
		//两个参数传递
		if(!is_null($value)){
			//临时动态改变配置项
			$config[$var] = $value;
			return;
		}
		//读取配置项
		return isset($config[$var])?$config[$var]:NULL;
	}
	//读取所有配置项
	if(is_null($var)&&is_null($value)){
		return $config;
	}
}


//数据库操作中的M函数
function M($table){
	$obj = new Model($table);
	return $obj;
}//系统日志类
class Log{
	//$msg 是字符串格式的错误信息
	public static function write($msg, $level='ERROR',$type=3,$dest=NULL){
		if(!C('SAVE_LOG')) return;
		if(is_null($dest)){
			$dest = LOG_PATH.'/'.date('Y_m_d').".log";
		}

		if(is_dir(LOG_PATH)){
			error_log("[TIME]:".date("Y-m-d H:i:s")."{$level}:{$msg}\r\n",$type,$dest);//type=3表示以文本的形式保存
		}
	}
}class Controller{

	//用于存放模版赋值的键值对
	private $var = array();

	//构造函数
	public function __construct(){

		if(method_exists($this, '__init')){
			$this->__init();
		}
		if(method_exists($this, '__auto')){
			$this->__auto();
		}
	}
	//成功提示方法
	protected function success($msg,$url=NULL,$time=3){
		$url = $url?"window.location.href='".$url."'":"window.history.back(-1)";
		include APP_TPL_PATH."/success.html";
		die;
	}

	//模版赋值
	protected function assign($var = NULL, $value=NULL){
		$this->var[$var] = $value;
	}

	//模版显示
	protected function display($tpl=NULL){
		if(is_null($tpl)){
			$path = APP_TPL_PATH.'/'.CONTROLLER.'/'.ACTION.'.html';
		}else{
			$suffix = strrchr($tpl,'.');// strrchr 搜索 第二个字符 在字符串中的位置，并返回从该位置到字符串结尾的所有字符
			$tpl = empty($suffix)?$tpl.'.html':$tpl;//如果没有扩展名则默认是html
			$path = APP_TPL_PATH.'/'.CONTROLLER.'/'.$tpl;

		}
		if(!is_file($path)) halt($path.'模版文件不存在');
		extract($this->var);
		include $path;
	}

}final class Application{
	public static function run(){
		
		self::_init();
		//非致命错误处理
		set_error_handler(array(__CLASS__,'error'));//set_error_handler() 函数设置用户定义的错误处理函数。
		//致命错误的处理
		register_shutdown_function(array(__CLASS__,'fatal_error'));
		self::_set_url();
		//载入用户自定义的库文件
		self::_user_import();
		//自动载入
		spl_autoload_register(array(__CLASS__,'_autoload'));
		//创建默认控制器
		self::_create_demo();

		self::_app_run();
	}
	/*如果运行中出现致命错误会调用fatal_error,如果运行完了没有致命错误，也会调用fatal_error,不过此时error_get_last中没有信息*/
	public static function fatal_error(){
		if($e = error_get_last()){//返回最后发生的错误：以数组的形式
			//p($e);die;Array
				/*(
				    [type] => 4
				    [message] => syntax error, unexpected '}'
				    [file] => D:\phpStudy\WWW\KUAIXUEAPP\Index\Controller\IndexController.class.php
				    [line] => 8
				)*/

				self::error($e['type'],$e['message'],$e['file'],$e['line']);
		}
	}
	public static function error($errno,$error,$file,$line){
		switch ($errno) {
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				$msg = $error.$file."第{$line}行";
				halt($msg);
				break; 
			case E_STRICT:
			case E_USER_WARNING:
			case E_USER_NOTICE:

			
			default:
				if(DEBUG){
					include DATA_PATH.'/Tpl/notice.html';
				}
				break;
		}
	}
	//初始化框架
	private static function _init(){
		//加载配置项
		C( include CONFIG_PATH.'/config.php' );


		//公共配置项
		$commonPath = COMMON_CONFIG_PATH.'/config.php';
		$commonConfig = <<<str
<?php
return array(
	//配置项=>配置值
);
?>
str;
	
		//查看是否已经有自定义的公共配置项文件了，如果没有则为之创建一个默认的
		is_file($commonPath)||file_put_contents($commonPath, $commonConfig);

		//加载公告的配置项
		C( include $commonPath );





		//用户配置项
		$userPath = APP_CONFIG_PATH.'/config.php';
		$userConfig = <<<str
<?php
return array(
	//配置项=>配置值
);
?>
str;
	
		//查看用户是否已经有自定义的配置项文件了，如果没有则为之创建一个默认的
		is_file($userPath)||file_put_contents($userPath, $userConfig);

		//加载用户的配置项
		C( include $userPath );

		//设置默认时区
		date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
		//是否开启session
		C('SESSION_AUTO_START') && session_start();

	}
	//设置外部路径
	private static function _set_url(){
		$path = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$path = str_replace('\\', '/', $path);
		define('__APP__',$path);//http://localhost/KUAIXUEAPP/index.php
		define('__ROOT__',dirname($path));
		
		define('__TPL__',__ROOT__.'/'.APP_NAME.'/Tpl');
		define('__PUBLIC__',__TPL__.'/Public');
	}
	//自动载入
	private static function _autoload($className){
		
		switch (true) {
			//判断是否是控制器,例如：IndexController
			case strlen($className)>10 && substr($className,-10)=='Controller':
				$path = APP_CONTROLLER_PATH.'/'.$className.'.class.php';

				if(!is_file($path)) {
					//用户可以自定义一个EmptyController文件，防止用户胡乱输入控制器名称
					$emptyPath = APP_CONTROLLER_PATH.'/EmptyController.class.php';
					if(is_file($emptyPath)){
						include $emptyPath;
						return;
					}else{
						halt($path.'控制器未找到');
					}
					
				}
				include $path;
				break;
			
			default:
				//工具类
				$path = TOOL_PATH.'/'.$className.".class.php";
				if(!is_file($path)) halt($path.'类未找到');
				include $path;
				break;
		}
		
	}
	//创建默认控制器
	private static function _create_demo(){
		$path = APP_CONTROLLER_PATH . '/IndexController.class.php';
		$str = <<<str
<?php
class IndexController extends Controller{
	public function index(){
		echo "ok";
	}
}
?>
str;
		is_file($path) || file_put_contents($path, $str);
	}
	//实例化应用控制器
	private static function _app_run(){
	
		$c = isset($_GET[C('VAR_CONTROLLER')])?$_GET[C('VAR_CONTROLLER')]:"Index";
		$a = isset($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:"index";
		define("CONTROLLER",$c);
		define("ACTION",$a);

		$c .= 'Controller';
		if(class_exists($c)){//先查看一下用户地址栏中要访问的控制器是否存在
			$obj = new $c();
			//判断要访问的方法是否存在
			if(!method_exists($obj,$a)){
				if(method_exists($obj,'__empty')){
					//用户自定义__empty方法
					$obj->__empty();
				}else{
					//用户没有自定义__empty方法
					halt($c.'控制器中'.$a.'方法不存在');
				}
			}else{
				$obj->$a();
			}
			
		}else{
			$obj = new EmptyController();
			$obj->index();
		}
		 
	}

	
	//载入用户自定义的库文件
	private static function _user_import(){
		$fileArr = C('AUTO_LOAD_FILE');
		if(is_array($fileArr)&& !empty($fileArr)){
			foreach ($fileArr as $v) {
				require_once COMMON_LIB_PATH.'/'.$v;
			}
		}
	}
	
	
	
}
